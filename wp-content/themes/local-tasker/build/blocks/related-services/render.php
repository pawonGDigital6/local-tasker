<?php
/**
 * ACF Block template.
 *
 * @param array $block The block settings and attributes.
 */

// Support custom "anchor" values.
$anchor = '';
if (!empty($block['anchor'])) {
	$anchor = 'id="' . esc_attr($block['anchor']) . '" ';
}

// Create class attribute allowing for custom "className" and "align" values.
$class_name = 'acf-block related-services py-5';

if (!empty($block['className'])) {
	$class_name .= ' ' . $block['className'];
}

if (!empty($block['align'])) {
	$class_name .= ' align' . $block['align'];
}

$class_name .= generate_block_settings_classnames()['class_name'];

// Block Style.
$style_attr = '';
$block_style = generate_block_settings_classnames()['block_style'];

if ($block_style !== '') {
	$style_attr = 'style="' . $block_style . '"';
}

?>
<?php
// 1. Get the current post ID to exclude it from the related query.
$current_post_id = get_the_ID();

// 2. Setup query arguments.
$related_args = array(
	'post_type' => 'service',
	'posts_per_page' => 2,
	'post__not_in' => array($current_post_id),
	// Query Optimizations:
	'no_found_rows' => true,  // Bypasses pagination counting (highly recommended for related posts).
	'update_post_term_cache' => false, // Disables taxonomy caching if you aren't displaying terms.
);

$related_services = new WP_Query($related_args);

// 3. Check if there are related services to display.
if ($related_services->have_posts()):
	?>
	<section <?php echo esc_attr($anchor); ?> class="<?php echo esc_attr($class_name); ?>" <?php echo $style_attr; ?>>
		<div class="container">
			<div class="rows grid sm:grid-cols-2 sm:gap-8 gap-5 sm:py-10 border-t border-[#E6E6E6]">
				<?php
				while ($related_services->have_posts()):
					$related_services->the_post();
					?>
					<div class="gird-item">
						<?php get_template_part('template-parts/components/related', 'service'); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php
	// 5. Reset global post data.
	wp_reset_postdata();
endif;
?>