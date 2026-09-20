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