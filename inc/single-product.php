<?php
/**
 * Single product page customizations — Tabaya-style layout.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Small italic tagline above the product title, pulled from the product's
 * short description. Moved here (before the title) instead of its default
 * position (after price), and the default excerpt hook is removed below so
 * it doesn't render twice.
 */
function ansclothes_single_product_tagline() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$short_description = $product->get_short_description();

	if ( '' === trim( wp_strip_all_tags( $short_description ) ) ) {
		return;
	}

	echo '<p class="ans-product-tagline">' . wp_kses_post( $short_description ) . '</p>';
}
add_action( 'woocommerce_single_product_summary', 'ansclothes_single_product_tagline', 4 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

/**
 * Label for the Add to Cart button, so all four buy buttons are worded from
 * one place in the Customizer.
 *
 * @return string
 */
function ansclothes_add_to_cart_label() {
	$label = trim( (string) ansclothes_option( 'product_addtocart_label' ) );

	return '' !== $label ? $label : __( 'Add to Cart', 'ansclothes' );
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'ansclothes_add_to_cart_label' );

/**
 * "Buy Now" — a second submit button in the same add-to-cart form that
 * adds the product to the cart and sends the customer straight to checkout.
 */
function ansclothes_buy_now_button() {
	global $product;

	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return;
	}

	if ( ! ansclothes_option( 'product_buynow_enable' ) ) {
		return;
	}

	$label = trim( (string) ansclothes_option( 'product_buynow_label' ) );
	$label = '' !== $label ? $label : __( 'Buy Now', 'ansclothes' );
	?>
	<button type="submit" name="ans_order_now" value="1" class="single_add_to_cart_button ans-buy-btn ans-buy-btn--now">
		<span class="ans-buy-btn__icon ans-buy-btn__icon--bolt" aria-hidden="true"></span>
		<span class="ans-buy-btn__label"><?php echo esc_html( $label ); ?></span>
	</button>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_button', 'ansclothes_buy_now_button' );

/**
 * Send the customer to checkout instead of the cart when they used the
 * "Buy Now" button.
 *
 * @param string $url Default redirect URL.
 * @return string
 */
function ansclothes_order_now_redirect( $url ) {
	if ( empty( $_POST['ans_order_now'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return $url;
	}

	/* With the quick COD popup enabled, come back to the product page with a
	   flag so the form opens over it — WooCommerce has already validated the
	   variation and put the item in the cart by this point. */
	if ( function_exists( 'ansclothes_cod_enabled' ) && ansclothes_cod_enabled() ) {
		$back = wp_get_referer();

		return add_query_arg( 'ans_cod', '1', $back ? $back : $url );
	}

	return wc_get_checkout_url();
}
add_filter( 'woocommerce_add_to_cart_redirect', 'ansclothes_order_now_redirect' );

/**
 * Strip a phone number down to what `tel:` / the WhatsApp API accept.
 *
 * @param string $number Raw number as typed in the Customizer.
 * @param bool   $keep_plus Whether to keep a leading "+" (tel: allows it,
 *                          wa.me does not).
 * @return string
 */
function ansclothes_clean_phone( $number, $keep_plus = false ) {
	$digits = preg_replace( '/[^0-9]/', '', (string) $number );

	if ( '' === $digits ) {
		return '';
	}

	return $keep_plus ? '+' . $digits : $digits;
}

/**
 * The message pre-filled into WhatsApp for the product being viewed.
 *
 * @return string
 */
function ansclothes_whatsapp_message() {
	global $product;

	$template = trim( (string) ansclothes_option( 'product_whatsapp_message' ) );

	if ( '' === $template ) {
		$template = __( 'Hello! I would like to order: {product} ({price}) — {url}', 'ansclothes' );
	}

	$name  = $product ? $product->get_name() : get_the_title();
	$price = $product ? wp_strip_all_tags( $product->get_price_html() ) : '';
	$url   = get_permalink();

	return strtr(
		$template,
		array(
			'{product}' => $name,
			'{price}'   => $price,
			'{url}'     => $url,
		)
	);
}

/**
 * WhatsApp + Call buttons.
 *
 * These sit in the same 2×2 button grid as Add to Cart / Buy Now when the
 * product has an add-to-cart form. Products with no form (out of stock,
 * external) get them in a standalone grid instead — see the fallback hook
 * below — so the contact routes never disappear.
 *
 * @param bool $standalone Whether the buttons render outside the cart form.
 * @return void
 */
function ansclothes_contact_buttons( $standalone = false ) {
	static $rendered = false;

	if ( $rendered ) {
		return;
	}

	$whatsapp_number = ansclothes_option( 'product_whatsapp_enable' )
		? ansclothes_clean_phone( ansclothes_option( 'product_whatsapp_number' ) )
		: '';

	$call_number = ansclothes_option( 'product_call_enable' )
		? ansclothes_clean_phone( ansclothes_option( 'product_call_number' ), true )
		: '';

	if ( '' === $whatsapp_number && '' === $call_number ) {
		return;
	}

	$rendered = true;

	$whatsapp_label = trim( (string) ansclothes_option( 'product_whatsapp_label' ) );
	$whatsapp_label = '' !== $whatsapp_label ? $whatsapp_label : __( 'Order on WhatsApp', 'ansclothes' );

	$call_label = trim( (string) ansclothes_option( 'product_call_label' ) );
	$call_label = '' !== $call_label ? $call_label : __( 'Call for Order', 'ansclothes' );

	if ( $standalone ) {
		echo '<div class="ans-buy-actions">';
	}

	if ( $whatsapp_number ) :
		$whatsapp_url = 'https://wa.me/' . $whatsapp_number . '?text=' . rawurlencode( ansclothes_whatsapp_message() );
		?>
		<a class="ans-buy-btn ans-buy-btn--whatsapp" href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener noreferrer">
			<span class="ans-buy-btn__icon ans-buy-btn__icon--whatsapp" aria-hidden="true"></span>
			<span class="ans-buy-btn__label"><?php echo esc_html( $whatsapp_label ); ?></span>
		</a>
		<?php
	endif;

	if ( $call_number ) :
		?>
		<a class="ans-buy-btn ans-buy-btn--call" href="tel:<?php echo esc_attr( $call_number ); ?>">
			<span class="ans-buy-btn__icon ans-buy-btn__icon--phone" aria-hidden="true"></span>
			<span class="ans-buy-btn__label"><?php echo esc_html( $call_label ); ?></span>
		</a>
		<?php
	endif;

	if ( $standalone ) {
		echo '</div>';
	}
}
add_action( 'woocommerce_after_add_to_cart_button', 'ansclothes_contact_buttons', 20 );

/**
 * Fallback for products without an add-to-cart form (out of stock, external,
 * unpurchasable). The static guard inside ansclothes_contact_buttons() means
 * this is a no-op whenever the in-form hook already ran.
 */
function ansclothes_contact_buttons_standalone() {
	ansclothes_contact_buttons( true );
}
add_action( 'woocommerce_single_product_summary', 'ansclothes_contact_buttons_standalone', 31 );

/**
 * Reassurance row under the buy buttons.
 *
 * These state shop policy, so they are filterable rather than hard-coded —
 * pass an empty array to `ansclothes_product_trust_points` to hide the row,
 * or adjust the wording if the policy changes.
 *
 * @return void
 */
function ansclothes_single_product_trust_points() {
	$points = apply_filters(
		'ansclothes_product_trust_points',
		array(
			'cod'      => __( 'Cash on delivery available', 'ansclothes' ),
			'shipping' => __( 'Fast shipping across Bangladesh', 'ansclothes' ),
			'returns'  => __( 'Easy returns', 'ansclothes' ),
		)
	);

	if ( empty( $points ) ) {
		return;
	}
	?>
	<ul class="ans-trust-points">
		<?php foreach ( $points as $ans_key => $ans_text ) : ?>
			<li class="ans-trust-points__item ans-trust-points__item--<?php echo esc_attr( $ans_key ); ?>">
				<span class="ans-trust-points__icon" aria-hidden="true"></span>
				<?php echo esc_html( $ans_text ); ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
}
add_action( 'woocommerce_after_add_to_cart_form', 'ansclothes_single_product_trust_points' );

/**
 * Friendlier heading for the related products section.
 *
 * @return string
 */
function ansclothes_related_products_heading() {
	return __( 'You may also like', 'ansclothes' );
}
add_filter( 'woocommerce_product_related_products_heading', 'ansclothes_related_products_heading' );

/**
 * Move the product data tabs inside the summary column.
 *
 * By default they render after the summary, which in a two-column grid means
 * they start a new row and therefore sit below the much taller gallery,
 * leaving a large gap under the buy buttons. Rendering them within the
 * summary keeps them directly under the buttons instead.
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 60 );

/**
 * Drop WooCommerce's "Sale!" flash from the single product page. The saving
 * is already conveyed by the struck-through price next to the sale price.
 * (The archive product cards keep their own SALE badge.)
 */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );

/**
 * The description accordion panel already has "Description" as its header
 * (from our tabs.php override), so drop WooCommerce's own duplicate <h2>
 * inside the panel content.
 */
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );

/**
 * Same for the "Additional information" tab.
 */
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

/**
 * Display-level relabel for the size attribute.
 *
 * Size is stored inconsistently across the catalogue — some products name
 * the local attribute "size", others "length" — but both hold size values
 * (M / L / XL). Until the product data is normalised, present them both as
 * "Size" so the front end doesn't show a stray "Length" heading.
 *
 * @param string $label Attribute label.
 * @return string
 */
function ansclothes_relabel_size_attribute( $label ) {
	if ( in_array( strtolower( $label ), array( 'length', 'size' ), true ) ) {
		return __( 'Size', 'ansclothes' );
	}
	return $label;
}
add_filter( 'woocommerce_attribute_label', 'ansclothes_relabel_size_attribute' );
