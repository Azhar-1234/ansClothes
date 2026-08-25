<?php
/**
 * ANSClothes theme functions and definitions.
 *
 * @package ANSClothes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ANSCLOTHES_VERSION', '1.4.0' );

/**
 * Theme setup.
 */
function ansclothes_setup() {
	load_theme_textdomain( 'ansclothes', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 68,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'ansclothes' ),
			'footer'  => __( 'Footer Menu', 'ansclothes' ),
		)
	);

	add_image_size( 'ansclothes-card', 800, 1000, true );
	add_image_size( 'ansclothes-hero', 1400, 1600, true );
}
add_action( 'after_setup_theme', 'ansclothes_setup' );

/**
 * Content width.
 */
function ansclothes_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'ansclothes_content_width', 760 );
}
add_action( 'after_setup_theme', 'ansclothes_content_width', 0 );

/**
 * Enqueue styles and scripts.
 */
function ansclothes_scripts() {
	wp_enqueue_style(
		'ansclothes-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;500;600;700;800;900&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	);

	wp_enqueue_style(
		'ansclothes-style',
		get_stylesheet_uri(),
		array( 'ansclothes-fonts' ),
		ANSCLOTHES_VERSION
	);

	wp_enqueue_script(
		'ansclothes-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array( 'jquery' ),
		ANSCLOTHES_VERSION,
		true
	);

	// Checkout runs its own distraction-free layout (see header.php/footer.php)
	// with its own type system, so its fonts are only loaded on that page.
	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
		wp_enqueue_style(
			'ansclothes-checkout-fonts',
			'https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700&family=Inter:wght@400;500;600&display=swap',
			array(),
			null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		);
	}

	// Powers the slide-in cart drawer: WooCommerce's fragments script refreshes
	// it after an AJAX add-to-cart, and the localized data backs its own
	// quantity/remove requests.
	if ( function_exists( 'WC' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );

		wp_localize_script(
			'ansclothes-main',
			'ansCart',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ans-cart-drawer' ),
				'cartUrl' => wc_get_cart_url(),
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'ansclothes_scripts' );

/**
 * Preconnect to the Google Fonts hosts.
 *
 * @param array  $urls          URLs to print.
 * @param string $relation_type Relation type.
 * @return array
 */
function ansclothes_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && ( wp_style_is( 'ansclothes-fonts', 'queue' ) || wp_style_is( 'ansclothes-checkout-fonts', 'queue' ) ) ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
		$urls[] = array( 'href' => 'https://fonts.googleapis.com' );
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'ansclothes_resource_hints', 10, 2 );

/**
 * Register widget areas.
 */
function ansclothes_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'ansclothes' ),
			'id'            => 'footer-1',
			'description'   => __( 'Shown above the footer bar, in three columns.', 'ansclothes' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'ansclothes_widgets_init' );

/**
 * Body classes.
 *
 * @param array $classes Body classes.
 * @return array
 */
function ansclothes_body_classes( $classes ) {
	if ( ! is_active_sidebar( 'footer-1' ) ) {
		$classes[] = 'no-footer-widgets';
	}

	if ( is_front_page() ) {
		$classes[] = 'ans-front';
	}

	return $classes;
}
add_filter( 'body_class', 'ansclothes_body_classes' );

/**
 * Excerpt length / more.
 *
 * @param int $length Words.
 * @return int
 */
function ansclothes_excerpt_length( $length ) {
	return 22;
}
add_filter( 'excerpt_length', 'ansclothes_excerpt_length' );

/**
 * Excerpt ellipsis.
 *
 * @return string
 */
function ansclothes_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'ansclothes_excerpt_more' );

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/meta-boxes.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/product-filters.php';
require get_template_directory() . '/inc/single-product.php';
require get_template_directory() . '/inc/size-charts.php';
require get_template_directory() . '/inc/cart-drawer.php';
require get_template_directory() . '/inc/cod-checkout.php';
require get_template_directory() . '/inc/checkout.php';
