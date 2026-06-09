<?php
/**
 * Allow SVG uploads in WordPress Media Library without a plugin.
 * Restricts upload capabilities to Administrators for security.
 */

// 1. Add SVG and SVGZ to the allowed upload MIME types
add_filter('upload_mimes', function ($mimes) {
    // Only allow administrators to upload SVGs to mitigate security risks
    if (!current_user_can('administrator')) {
        return $mimes;
    }

    $mimes['svg'] = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';

    return $mimes;
});

// 2. Fix the file type check and extension mismatch in WordPress 4.7+
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
    if (!current_user_can('administrator')) {
        return $data;
    }

    $filetype = wp_check_filetype($filename, $mimes);
    $ext = $filetype['ext'];
    $type = $filetype['type'];

    if (in_array($ext, ['svg', 'svgz'], true)) {
        $data['ext'] = $ext;
        $data['type'] = $type;
        $data['proper_filename'] = $data['proper_filename'] ? $data['proper_filename'] : $filename;
    }

    return $data;
}, 10, 4);

// 3. Fix the Media Library preview rendering constraints
add_action('admin_head', function () {
    echo '<style type="text/css">
        .attachment-266x266, .thumbnail img { 
            width: 100% !important; 
            height: auto !important; 
        }
    </style>';
});
