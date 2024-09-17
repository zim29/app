<?php
/**
 * Astra Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra Child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );
}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/**
 * Add google tag manager code in head tag.
 */
function custom_add_gtm_code() {
?>
<!--  Add your GTM Code below this line -->
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-NXVCL2C');</script>
<!-- End Google Tag Manager -->
<?php
}
add_action( 'wp_head', 'custom_add_gtm_code' );

/**
 * Add google tag manager noscript just after opening body tag.
 */
// Add Google Tag code which is supposed to be placed after opening body tag.
add_action( 'wp_body_open', 'wpdoc_add_custom_body_open_code' );
 
function wpdoc_add_custom_body_open_code() {
    echo '<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NXVCL2C"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->';
	 echo '<script async type="text/javascript" src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=WkiHTE"></script>';
}
/*
function wpdoc_add_custom_body_open_code() {
    echo '<script async type="text/javascript" src="https://static.klaviyo.com/onsite/js/klaviyo.js?company_id=WkiHTE"></script>';
}
add_action( 'wp_body_open', 'wpdoc_add_custom_body_open_code' );*/