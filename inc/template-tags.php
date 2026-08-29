<?php
/**
 * Template helpers and theme defaults.
 *
 * Every homepage string lives here as a default so the theme looks complete
 * the moment it is activated, before anything is entered in the Customizer.
 *
 * @package ANSClothes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * All Customizer defaults, in one place.
 *
 * @return array
 */
function ansclothes_defaults() {
	$assets = get_template_directory_uri() . '/assets';

	$slider_defaults = array();
	for ( $i = 1; $i <= ANSCLOTHES_SLIDER_SLOTS; $i++ ) {
		$slider_defaults[ "slider_{$i}_position" ] = 'center';
	}

	return $slider_defaults + array(
		'currency'             => '৳',

		'announcement_enable'  => true,
		'announcement_text'    => __( 'Free delivery on your first order — www.ansclothes.com', 'ansclothes' ),
		'announcement_url'     => '',

		/* Quick cash-on-delivery checkout modal. Rates are options rather than
		   hard-coded because the store has no WooCommerce shipping zones set
		   up for them to be read from. */
		'cod_enable'           => true,
		'cod_title'            => __( 'Cash on Delivery', 'ansclothes' ),
		'cod_intro'            => __( 'Fill Up The Form To Confirm Your Order.', 'ansclothes' ),
		'cod_button_label'     => __( 'Complete Order', 'ansclothes' ),
		'cod_ship1_label'      => __( 'Inside Dhaka', 'ansclothes' ),
		'cod_ship1_cost'       => '70',
		'cod_ship2_label'      => __( 'Outside Dhaka', 'ansclothes' ),
		'cod_ship2_cost'       => '130',

		'checkout_heading'     => __( 'Please fill up this form to confirm your order', 'ansclothes' ),
		/* Left empty so the stylesheet's own colours apply until the shop
		   deliberately overrides them in the Customizer. */
		'announcement_bg'      => '',
		'announcement_color'   => '',

		'bag_text'             => __( 'Bag (0)', 'ansclothes' ),
		'bag_url'              => '#bag',

		'hero_eyebrow'         => __( 'The First Drop — 2026', 'ansclothes' ),
		'hero_title'           => __( 'Everyday tees,<br>made <em class="ans-serif">properly</em>.', 'ansclothes' ),
		'hero_text'            => __( 'Heavyweight cotton. Clean cuts. No noise. ANSClothes starts with one thing done right — the t-shirt.', 'ansclothes' ),
		'hero_btn1_text'       => __( 'Shop T-Shirts', 'ansclothes' ),
		'hero_btn1_url'        => '#shop',
		'hero_btn2_text'       => __( 'Coming Soon', 'ansclothes' ),
		'hero_btn2_url'        => '#coming',
		'hero_image'           => $assets . '/tee-navy.jpg',
		'hero_card_title'      => __( 'The Navy Essential', 'ansclothes' ),
		'hero_card_meta'       => __( '৳ 850 — Oversized fit', 'ansclothes' ),

		'marquee_items'        => "Premium Quality\nHeavyweight Cotton\nOversized Fit\nMade in Bangladesh",

		'circle_categories_enable' => true,
		'circle_categories_count'  => 4,
		'circle_categories_slugs'  => '',

		'category_products_enable' => true,
		'category_products_slugs'  => '',
		'category_products_count'  => 8,

		'category_desc_enable'     => true,

		/* Single product buy buttons. A contact button with an empty number is
		   hidden on the front end — there is nothing to link to. */
		'product_addtocart_label'  => __( 'Add to Cart', 'ansclothes' ),

		'product_buynow_enable'    => true,
		'product_buynow_label'     => __( 'Buy Now', 'ansclothes' ),

		'product_whatsapp_enable'  => true,
		'product_whatsapp_label'   => __( 'Order on WhatsApp', 'ansclothes' ),
		'product_whatsapp_number'  => '',
		'product_whatsapp_message' => __( 'Hello! I would like to order: {product} ({price}) — {url}', 'ansclothes' ),

		'product_call_enable'      => true,
		'product_call_label'       => __( 'Call for Order', 'ansclothes' ),
		'product_call_number'      => '',

		'footer_copy'          => __( '© 2026 ANSClothes — www.ansclothes.com', 'ansclothes' ),
		'footer_address_1'     => __( 'Banani: Level 3, House 45, Road 11, Block C', 'ansclothes' ),
		'footer_address_2'     => __( 'Mirpur 1: Level 4, Rupayan Latifa Shamsuddin Square', 'ansclothes' ),
		'footer_facebook_url'  => '',
		'footer_instagram_url' => '',
	);
}

/**
 * Read a theme mod, falling back to the shared default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function ansclothes_option( $key ) {
	$defaults = ansclothes_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';

	return get_theme_mod( $key, $default );
}

/**
 * The store currency symbol.
 *
 * @return string
 */
function ansclothes_currency() {
	return ansclothes_option( 'currency' );
}

/**
 * Whether WooCommerce is available.
 *
 * @return bool
 */
function ansclothes_has_woocommerce() {
	return class_exists( 'WooCommerce' );
}

/**
 * Storefront URL, preferring the WooCommerce shop when available.
 *
 * @return string
 */
function ansclothes_shop_url() {
	if ( ansclothes_has_woocommerce() && function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'shop' );
	}

	return get_post_type_archive_link( 'ans_product' );
}

/**
 * Cart URL, preferring the WooCommerce cart when available.
 *
 * @return string
 */
function ansclothes_cart_url() {
	if ( ansclothes_has_woocommerce() && function_exists( 'wc_get_cart_url' ) ) {
		return wc_get_cart_url();
	}

	return ansclothes_option( 'bag_url' );
}

/**
 * Customer account URL, preferring WooCommerce My Account.
 *
 * @return string
 */
function ansclothes_account_url() {
	if ( ansclothes_has_woocommerce() && function_exists( 'wc_get_page_permalink' ) ) {
		return wc_get_page_permalink( 'myaccount' );
	}

	return wp_login_url();
}

/**
 * Header bag label, including WooCommerce cart count when available.
 *
 * @return string
 */
function ansclothes_bag_label() {
	if ( ansclothes_has_woocommerce() && function_exists( 'WC' ) && WC()->cart ) {
		return sprintf(
			/* translators: %d: cart item count. */
			__( 'Bag (%d)', 'ansclothes' ),
			WC()->cart->get_cart_contents_count()
		);
	}

	return ansclothes_option( 'bag_text' );
}

/**
 * Product category links for the header navigation.
 *
 * @return array
 */
function ansclothes_product_category_links() {
	$taxonomy = ansclothes_has_woocommerce() ? 'product_cat' : 'ans_product_cat';
	$terms    = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 8,
			'orderby'    => 'menu_order',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return array(
			array(
				'name' => __( 'Shop', 'ansclothes' ),
				'url'  => ansclothes_shop_url(),
			),
		);
	}

	$links = array();

	foreach ( $terms as $term ) {
		$term_link = get_term_link( $term );

		if ( is_wp_error( $term_link ) ) {
			continue;
		}

		$links[] = array(
			'name'    => $term->name,
			'url'     => $term_link,
			'term_id' => $term->term_id,
		);
	}

	return $links;
}

/**
 * Split a newline separated setting into a clean array.
 *
 * @param string $key Setting key.
 * @return array
 */
function ansclothes_lines( $key ) {
	$raw   = (string) ansclothes_option( $key );
	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$lines = array_filter( array_map( 'trim', (array) $lines ), 'strlen' );

	return array_values( $lines );
}

/**
 * Allowed inline tags for headings.
 *
 * @return array
 */
function ansclothes_heading_tags() {
	return array(
		'em'     => array( 'class' => array(), 'style' => array() ),
		'i'      => array( 'class' => array() ),
		'strong' => array( 'class' => array() ),
		'br'     => array(),
		'span'   => array( 'class' => array() ),
		'a'      => array( 'href' => array(), 'title' => array() ),
	);
}

/**
 * Echo a heading setting with inline markup preserved.
 *
 * @param string $key Setting key.
 */
function ansclothes_heading( $key ) {
	echo wp_kses( ansclothes_option( $key ), ansclothes_heading_tags() );
}

/**
 * Inline colour overrides for the announcement bar.
 *
 * Emitted as custom properties so the stylesheet keeps ownership of the
 * layout and only the chosen colours are overridden. Returns an empty
 * string when the shop has not set either colour.
 *
 * @return string A ready-to-print style attribute, or ''.
 */
function ansclothes_announcement_style() {
	$vars = array();
	$bg   = ansclothes_option( 'announcement_bg' );
	$fg   = ansclothes_option( 'announcement_color' );

	if ( $bg ) {
		$vars[] = '--ans-announcement-bg:' . $bg;
	}

	if ( $fg ) {
		$vars[] = '--ans-announcement-color:' . $fg;
	}

	if ( ! $vars ) {
		return '';
	}

	return ' style="' . esc_attr( implode( ';', $vars ) ) . '"';
}

/**
 * Announcement bar inner content.
 *
 * Shared by the header template and the Customizer partial so the live
 * preview renders exactly what the front end does.
 */
function ansclothes_announcement_content() {
	$url = ansclothes_option( 'announcement_url' );

	if ( $url ) {
		printf(
			'<a class="ans-announcement__link" href="%s">%s</a>',
			esc_url( $url ),
			wp_kses( ansclothes_option( 'announcement_text' ), ansclothes_heading_tags() )
		);
		return;
	}

	ansclothes_heading( 'announcement_text' );
}

/**
 * Formatted price for a product.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function ansclothes_price( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$price   = get_post_meta( $post_id, '_ans_price', true );

	if ( '' === $price ) {
		return '';
	}

	return ansclothes_currency() . ' ' . $price;
}

/**
 * Build product card data for the current post, WooCommerce or theme product.
 *
 * @param int|null $post_id Post ID.
 * @return array
 */
function ansclothes_get_product_card_args( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$args    = array(
		'id'          => $post_id,
		'name'        => get_the_title( $post_id ),
		'price'       => get_post_meta( $post_id, '_ans_price', true ),
		'badge'       => get_post_meta( $post_id, '_ans_badge', true ),
		'image'       => get_the_post_thumbnail_url( $post_id, 'ansclothes-card' ),
		'url'         => get_permalink( $post_id ),
		'add_to_cart' => '',
	);

	if ( 'product' === get_post_type( $post_id ) && ansclothes_has_woocommerce() && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( $post_id );

		if ( $product ) {
			$args['price_html'] = $product->get_price_html();
			$args['badge']      = $product->is_on_sale() ? __( 'Sale', 'ansclothes' ) : '';
			$args['add_to_cart'] = $product->add_to_cart_url();

			if ( ! $args['image'] && function_exists( 'wc_placeholder_img_src' ) ) {
				$args['image'] = wc_placeholder_img_src( 'woocommerce_thumbnail' );
			}
		}
	}

	return $args;
}

/**
 * Category image URL, supporting WooCommerce and theme collection images.
 *
 * @param int    $term_id Term ID.
 * @param string $size    Image size.
 * @return string
 */
function ansclothes_category_image_url( $term_id, $size = 'thumbnail' ) {
	$image_id = get_term_meta( $term_id, 'thumbnail_id', true );

	if ( ! $image_id ) {
		$image_id = get_term_meta( $term_id, 'ans_term_image', true );
	}

	if ( $image_id ) {
		return wp_get_attachment_image_url( (int) $image_id, $size );
	}

	return get_template_directory_uri() . '/assets/tee-navy.jpg';
}

/**
 * Render homepage product card with hover actions.
 *
 * @param array $args Product card data.
 */
function ansclothes_home_product_card( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'id'          => 0,
			'name'        => '',
			'price'       => '',
			'price_html'  => '',
			'image'       => '',
			'badge'       => '',
			'url'         => '',
			'add_to_cart' => '',
		)
	);
	?>
	<article class="ans-home-product">
		<div class="ans-home-product__media">
			<?php if ( $args['badge'] ) : ?>
				<span class="ans-badge"><?php echo esc_html( $args['badge'] ); ?></span>
			<?php endif; ?>

			<?php if ( $args['image'] ) : ?>
				<img src="<?php echo esc_url( $args['image'] ); ?>" alt="<?php echo esc_attr( $args['name'] ); ?>" loading="lazy">
			<?php else : ?>
				<span class="ans-card__placeholder"><?php echo esc_html( $args['name'] ); ?></span>
			<?php endif; ?>

			<div class="ans-home-product__actions">
				<?php if ( $args['url'] ) : ?>
					<a href="<?php echo esc_url( $args['url'] ); ?>"><?php esc_html_e( 'View Details', 'ansclothes' ); ?></a>
				<?php endif; ?>

				<?php if ( $args['add_to_cart'] ) : ?>
					<a href="<?php echo esc_url( $args['add_to_cart'] ); ?>" data-quantity="1" data-product_id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Add to Cart', 'ansclothes' ); ?></a>
				<?php endif; ?>
			</div>
		</div>

		<h3><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['name'] ); ?></a></h3>

		<?php if ( '' !== $args['price_html'] ) : ?>
			<span class="ans-home-product__price"><?php echo wp_kses_post( $args['price_html'] ); ?></span>
		<?php elseif ( '' !== $args['price'] ) : ?>
			<span class="ans-home-product__price"><?php echo esc_html( ansclothes_currency() . ' ' . $args['price'] ); ?></span>
		<?php endif; ?>
	</article>
	<?php
}

/**
 * Demo products used when no product has been published yet.
 *
 * @return array
 */
function ansclothes_demo_products() {
	$assets = get_template_directory_uri() . '/assets';

	return array(
		array( 'name' => __( 'The Navy Essential Tee', 'ansclothes' ), 'price' => '850', 'image' => $assets . '/tee-navy.jpg', 'badge' => __( 'Featured', 'ansclothes' ) ),
		array( 'name' => __( 'The Black Essential Tee', 'ansclothes' ), 'price' => '850', 'image' => '', 'badge' => '' ),
		array( 'name' => __( 'The White Essential Tee', 'ansclothes' ), 'price' => '850', 'image' => '', 'badge' => '' ),
		array( 'name' => __( 'The Charcoal Essential Tee', 'ansclothes' ), 'price' => '890', 'image' => '', 'badge' => __( 'New', 'ansclothes' ) ),
		array( 'name' => __( 'Drop Shoulder — Onyx', 'ansclothes' ), 'price' => '950', 'image' => '', 'badge' => __( 'New', 'ansclothes' ) ),
		array( 'name' => __( 'Drop Shoulder — Cloud', 'ansclothes' ), 'price' => '950', 'image' => '', 'badge' => '' ),
		array( 'name' => __( 'Classic Polo — Black', 'ansclothes' ), 'price' => '1,150', 'image' => '', 'badge' => __( 'New', 'ansclothes' ) ),
		array( 'name' => __( 'Classic Polo — Ivory', 'ansclothes' ), 'price' => '1,150', 'image' => '', 'badge' => '' ),
		array( 'name' => __( 'Relaxed Pant — Black', 'ansclothes' ), 'price' => '1,450', 'image' => '', 'badge' => __( 'Pre-Order', 'ansclothes' ) ),
		array( 'name' => __( 'Relaxed Pant — Stone', 'ansclothes' ), 'price' => '1,450', 'image' => '', 'badge' => '' ),
		array( 'name' => __( 'Oxford Shirt — White', 'ansclothes' ), 'price' => '1,650', 'image' => '', 'badge' => __( 'Pre-Order', 'ansclothes' ) ),
		array( 'name' => __( 'Oxford Shirt — Black', 'ansclothes' ), 'price' => '1,650', 'image' => '', 'badge' => '' ),
	);
}

/**
 * Demo collections used when no collection term exists yet.
 *
 * @return array
 */
function ansclothes_demo_categories() {
	$assets = get_template_directory_uri() . '/assets';

	return array(
		array( 'name' => __( 'T-Shirts', 'ansclothes' ), 'count' => __( '12 Items', 'ansclothes' ), 'image' => $assets . '/tee-navy.jpg' ),
		array( 'name' => __( 'Drop Shoulder', 'ansclothes' ), 'count' => __( '08 Items', 'ansclothes' ), 'image' => '' ),
		array( 'name' => __( 'Polo', 'ansclothes' ), 'count' => __( '06 Items', 'ansclothes' ), 'image' => '' ),
		array( 'name' => __( 'Pants', 'ansclothes' ), 'count' => __( 'Coming Soon', 'ansclothes' ), 'image' => '' ),
	);
}

/**
 * Render a product card from arbitrary data.
 *
 * @param array $args name, price, image, badge, url, alt.
 */
function ansclothes_product_card( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'name'  => '',
			'price' => '',
			'image' => '',
			'badge' => '',
			'url'   => '',
			'alt'   => '',
			'price_html' => '',
		)
	);

	$tag   = $args['url'] ? 'a' : 'div';
	$attrs = $args['url'] ? ' href="' . esc_url( $args['url'] ) . '"' : '';
	?>
	<<?php echo esc_html( $tag ) . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="ans-card ans-product">
		<div class="ans-card__media">
			<?php if ( $args['badge'] ) : ?>
				<span class="ans-badge"><?php echo esc_html( $args['badge'] ); ?></span>
			<?php endif; ?>
			<?php if ( $args['image'] ) : ?>
				<img src="<?php echo esc_url( $args['image'] ); ?>" alt="<?php echo esc_attr( $args['alt'] ? $args['alt'] : $args['name'] ); ?>" loading="lazy">
			<?php else : ?>
				<span class="ans-card__placeholder"><?php echo esc_html( $args['name'] ); ?></span>
			<?php endif; ?>
		</div>
		<div class="ans-product__row">
			<span class="ans-product__name"><?php echo esc_html( $args['name'] ); ?></span>
			<?php if ( '' !== $args['price_html'] ) : ?>
				<span class="ans-product__price"><?php echo wp_kses_post( $args['price_html'] ); ?></span>
			<?php elseif ( '' !== $args['price'] ) : ?>
				<span class="ans-product__price"><?php echo esc_html( ansclothes_currency() . ' ' . $args['price'] ); ?></span>
			<?php endif; ?>
		</div>
	</<?php echo esc_html( $tag ); ?>>
	<?php
}

/**
 * Render a category card from arbitrary data.
 *
 * @param array $args name, count, image, url.
 */
function ansclothes_category_card( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'name'  => '',
			'count' => '',
			'image' => '',
			'url'   => '',
		)
	);

	$tag   = $args['url'] ? 'a' : 'div';
	$attrs = $args['url'] ? ' href="' . esc_url( $args['url'] ) . '"' : '';
	?>
	<<?php echo esc_html( $tag ) . $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="ans-card ans-cat">
		<div class="ans-card__media">
			<?php if ( $args['image'] ) : ?>
				<img src="<?php echo esc_url( $args['image'] ); ?>" alt="<?php echo esc_attr( $args['name'] ); ?>" loading="lazy">
			<?php else : ?>
				<span class="ans-card__placeholder"><?php echo esc_html( $args['name'] ); ?></span>
			<?php endif; ?>
			<span class="ans-cat__label">
				<strong><?php echo esc_html( $args['name'] ); ?></strong>
				<span><?php echo esc_html( $args['count'] ); ?></span>
			</span>
		</div>
	</<?php echo esc_html( $tag ); ?>>
	<?php
}

/**
 * Site logo: custom logo if set, otherwise the bundled brand mark.
 *
 * @param string $class Extra class for the fallback image.
 */
function ansclothes_logo( $class = '' ) {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$logo = get_template_directory_uri() . '/assets/logo.png';
	?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="<?php echo esc_attr( $class ); ?>">
		<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
	</a>
	<?php
}

/**
 * Pagination wrapper.
 */
function ansclothes_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => __( 'Prev', 'ansclothes' ),
			'next_text' => __( 'Next', 'ansclothes' ),
			'class'     => 'ans-pagination',
		)
	);
}
