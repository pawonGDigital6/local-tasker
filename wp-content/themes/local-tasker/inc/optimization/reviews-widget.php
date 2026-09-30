<?php
/**
 * Hold the Trustindex review widget back until it is actually approached.
 *
 * The widget renders inside the acf-block/google-review block, about halfway
 * down the homepage, and its own container is emitted with
 * `opacity:0;height:0` until loader.js initialises it - so nothing it
 * downloads is visible when the page first paints. Despite that it fetches
 * 134 KB, a fifth of the page, between 347ms and 528ms: squarely inside the
 * window the LCP hero image is competing for bandwidth in. Measured on
 * mobile, the hero had 302 KB in flight alongside it and a Load Delay of
 * 1950ms, which is most of a 4.9s LCP.
 *
 * Trustindex opts itself out of every mechanism that would normally defer
 * this. All 110 of its <img> tags carry class="skip-lazy" and none carry
 * loading="lazy", and its stylesheet is tagged data-no-defer/data-no-optimize,
 * so neither WP Rocket nor the browser holds any of it back unprompted.
 *
 * So two changes, both scoped to this one block's own markup:
 *
 *   1. Its images are made natively lazy, so the browser skips them until
 *      the widget nears the viewport.
 *   2. loader.js is parked until the block approaches the viewport.
 *
 * The widget still renders exactly as before once reached - only later, which
 * is where it already was visually. Nothing outside this block is touched.
 *
 * @package local-tasker
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block whose output carries the review widget.
 */
const LT_REVIEWS_BLOCK = 'acf-block/google-review';

/**
 * Script handle registered by the Trustindex plugin.
 */
const LT_REVIEWS_SCRIPT = 'trustindex-loader-js';

/**
 * Script type used to park the loader.
 *
 * Must be something no browser recognises as executable - an unknown type is
 * neither run nor fetched - while leaving the src attribute in place.
 */
const LT_REVIEWS_PARKED_TYPE = 'text/lt-lazy';

/**
 * Make every <img> in a fragment natively lazy.
 *
 * Trustindex adds `skip-lazy`, which is the opt-out marker WP Rocket and
 * several other optimisers honour, so it has to come off for anything to
 * defer these at all. `loading="lazy"` is then what actually holds the
 * request back, and it is the browser doing it rather than a script, so it
 * still works with JavaScript delayed or disabled.
 *
 * Kept as a plain string transform so it can be exercised directly against
 * captured markup in tests without booting WordPress.
 *
 * @param string $html Block markup.
 * @return string
 */
function lt_reviews_widget_lazy_images( $html ) {
	if ( '' === $html || false === strpos( $html, '<img' ) ) {
		return $html;
	}

	return preg_replace_callback(
		'#<img\b[^>]*>#i',
		static function ( $matches ) {
			$tag = $matches[0];

			// Already lazy - leave it exactly as it is.
			if ( preg_match( '#\bloading\s*=\s*["\']?lazy#i', $tag ) ) {
				return $tag;
			}

			// Drop the opt-out marker, keeping the rest of the class list.
			$tag = preg_replace_callback(
				'#\bclass\s*=\s*(["\'])(.*?)\1#i',
				static function ( $class_match ) {
					$classes = preg_replace( '#\bskip-lazy\b#i', '', $class_match[2] );
					$classes = trim( preg_replace( '#\s+#', ' ', $classes ) );

					// An empty class attribute is valid but pointless; drop it.
					return '' === $classes ? '' : sprintf( 'class=%s%s%s', $class_match[1], $classes, $class_match[1] );
				},
				$tag
			);

			return preg_replace( '#\s*/?>$#', ' loading="lazy" />', $tag, 1 );
		},
		$html
	);
}

/**
 * Apply the image treatment to the review block only.
 *
 * @param string $content Rendered block HTML.
 * @param array  $block   Parsed block.
 * @return string
 */
function lt_reviews_widget_filter_block( $content, $block ) {
	if ( is_admin() || empty( $block['blockName'] ) || LT_REVIEWS_BLOCK !== $block['blockName'] ) {
		return $content;
	}

	return lt_reviews_widget_lazy_images( $content );
}
add_filter( 'render_block', 'lt_reviews_widget_filter_block', 10, 2 );

/**
 * Park loader.js instead of letting it download with everything else.
 *
 * The obvious move - strip the src so the preload scanner ignores it - does
 * not survive contact with the plugin. Trustindex prints an inline fallback
 * that runs regardless:
 *
 *     if (window.TrustindexWidget ||
 *         document.querySelector('script[src*="…/loader.js"]')) { return; }
 *     // …otherwise build a fresh <script> and inject it
 *
 * With the src removed that querySelector finds nothing, the fallback
 * concludes the loader is missing and injects its own copy, so the script
 * downloads anyway and the deferral achieves nothing. Verified locally:
 * loader.js still arrived, with initiatorType "script".
 *
 * So the src stays exactly where it is - the fallback finds it and stands
 * down - and the type is changed instead. A script with a type the browser
 * does not recognise is neither executed nor fetched, while remaining a
 * perfectly ordinary match for `script[src*="loader.js"]`. Working with the
 * fallback rather than against it.
 *
 * @param string $tag    Script markup.
 * @param string $handle Script handle.
 * @return string
 */
function lt_reviews_widget_defer_loader( $tag, $handle ) {
	if ( is_admin() || LT_REVIEWS_SCRIPT !== $handle ) {
		return $tag;
	}

	// Only rewrite the shape we expect; if the plugin changes its markup we
	// would rather leave the widget working than half-rewrite its tag.
	if ( ! preg_match( '#\ssrc=(["\'])(.*?)\1#i', $tag ) ) {
		return $tag;
	}

	// Replace an existing type, or add one right after <script.
	if ( preg_match( '#\stype\s*=\s*(["\']).*?\1#i', $tag ) ) {
		$tag = preg_replace( '#\stype\s*=\s*(["\']).*?\1#i', ' type="' . LT_REVIEWS_PARKED_TYPE . '"', $tag, 1 );
	} else {
		$tag = preg_replace( '#<script\b#i', '<script type="' . LT_REVIEWS_PARKED_TYPE . '"', $tag, 1 );
	}

	return $tag;
}
add_filter( 'script_loader_tag', 'lt_reviews_widget_defer_loader', 10, 2 );

/**
 * Print the loader that restores the parked script.
 *
 * Observes the block's <section>, not the widget container: Trustindex
 * collapses its own container to `height:0` until it initialises, and a
 * zero-height element never intersects, so observing it would park the
 * script forever.
 *
 * If the block is not on this page the script is restored immediately, so a
 * widget placed through some other route still behaves normally.
 */
function lt_reviews_widget_bootstrap() {
	if ( is_admin() ) {
		return;
	}
	?>
<script id="lt-reviews-lazy">
(function () {
	var PARKED = 'script[type="<?php echo esc_js( LT_REVIEWS_PARKED_TYPE ); ?>"]';
	var parked = document.querySelectorAll(PARKED);
	if (!parked.length) {
		return;
	}

	var loaded = false;
	function load() {
		if (loaded) {
			return;
		}
		loaded = true;
		document.querySelectorAll(PARKED).forEach(function (old) {
			// Copying the node and dropping the type is what makes it run:
			// a script element that is already in the DOM will not execute
			// just because its type changes.
			var s = document.createElement('script');
			for (var i = 0; i < old.attributes.length; i++) {
				var a = old.attributes[i];
				if (a.name !== 'type') {
					s.setAttribute(a.name, a.value);
				}
			}
			s.async = true;
			old.parentNode.replaceChild(s, old);
		});
	}

	var anchor = document.querySelector('.lt-google-review');
	if (!anchor || !('IntersectionObserver' in window)) {
		// Nothing to hang the observer on, or a browser without support:
		// behave exactly as before rather than never loading the widget.
		load();
		return;
	}

	var io = new IntersectionObserver(function (entries) {
		if (entries.some(function (e) { return e.isIntersecting; })) {
			io.disconnect();
			load();
		}
	}, { rootMargin: '400px 0px' });

	io.observe(anchor);
})();
</script>
	<?php
}
add_action( 'wp_footer', 'lt_reviews_widget_bootstrap', 20 );
