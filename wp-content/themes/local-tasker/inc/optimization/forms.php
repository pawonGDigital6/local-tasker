<?php
function localtasker_form_mark_rendered()
{
	$GLOBALS['localtasker_form_rendered'] = true;
}
add_action('wpcf7_contact_form', 'localtasker_form_mark_rendered');

function localtasker_form_dequeue_unused_scripts()
{
	if (!empty($GLOBALS['localtasker_form_rendered'])) {
		return;
	}

	foreach (array('wpcf7-recaptcha', 'google-recaptcha', 'contact-form-7', 'swv') as $handle) {
		wp_dequeue_script($handle);
	}
}
add_action('wp_footer', 'localtasker_form_dequeue_unused_scripts', 1);

/**
 * Load form stylesheets without blocking the first render.
 *
 * The quote popup sits in the footer of every page, so Contact Form 7 and the
 * country phone field load their CSS site-wide. None of it is needed until the
 * popup is opened, but as normal stylesheets they each cost a render-blocking
 * round trip (~470 ms each on mobile). media="print" lets the browser fetch
 * them at low priority and apply them as soon as they arrive.
 */
function localtasker_form_async_styles($tag, $handle)
{
	$deferred = array(
		'contact-form-7',
		'nbcpf-intlTelInput-style',
		'nbcpf-countryFlag-style',
	);

	if (is_admin() || !in_array($handle, $deferred, true) || false !== strpos($tag, 'media="print"')) {
		return $tag;
	}

	$async = str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $tag);
	$async = str_replace('media="all"', 'media="print" onload="this.media=\'all\'"', $async);

	// Keep the styles working when JavaScript is unavailable.
	return $async . '<noscript>' . $tag . '</noscript>' . "\n";
}
add_filter('style_loader_tag', 'localtasker_form_async_styles', 10, 2);
/**
 * Let WP Rocket delay Google reCAPTCHA.
 *
 * WP Rocket ships a built-in exclusion for reCAPTCHA, because delaying the API
 * on its own breaks Contact Form 7: the CF7 module calls grecaptcha.ready()
 * inside a DOMContentLoaded handler, so if the API is not there yet it throws
 * and the spam token is never attached to the form.
 *
 * Removing the exclusion lets Rocket delay BOTH the API and the CF7 module as
 * one group. Rocket loads them in order on the first user interaction and
 * re-dispatches DOMContentLoaded for the delayed scripts, so CF7 initialises
 * normally. Nobody can submit a form without interacting first, so the token is
 * always in place by the time the form is sent.
 *
 * reCAPTCHA is the single largest script on the site (~690 KB, ~1.1s of CPU)
 * and it is not needed until someone actually starts using a form.
 */
add_filter('rocket_delay_js_exclusions', 'localtasker_allow_recaptcha_delay');

function localtasker_allow_recaptcha_delay($excluded)
{
	if (!is_array($excluded)) {
		return $excluded;
	}

	return array_values(
		array_filter(
			$excluded,
			function ($pattern) {
				return false === stripos((string) $pattern, 'recaptcha');
			}
		)
	);
}
