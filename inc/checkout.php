<?php
/**
 * Checkout page — reduced to the fields a cash-on-delivery shop actually
 * needs: name, mobile, district, area/thana and address, plus the optional
 * coupon form.
 *
 * The page uses the classic [woocommerce_checkout] shortcode rather than the
 * checkout block, because the block's fields cannot be removed without its
 * validation still requiring them.
 *
 * District is required so WooCommerce has a location to match against the
 * shipping zones configured in the dashboard (WooCommerce → Settings →
 * Shipping) — without it the cart has no address to calculate a rate from,
 * so the shipping row silently disappears and the order ships free.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Reduce the checkout to three billing fields.
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function ansclothes_checkout_fields( $fields ) {
	$country = WC()->countries->get_base_country();

	$fields['billing'] = array(
		'billing_first_name' => array(
			'label'       => __( 'Full name', 'ansclothes' ),
			'placeholder' => __( 'Full name', 'ansclothes' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
			'autocomplete' => 'name',
			'priority'    => 10,
		),
		'billing_phone'      => array(
			'label'       => __( 'Mobile number', 'ansclothes' ),
			'placeholder' => __( 'Mobile number', 'ansclothes' ),
			'required'    => true,
			'type'        => 'tel',
			'class'       => array( 'form-row-wide' ),
			'autocomplete' => 'tel',
			'priority'    => 20,
		),
		/* Drives shipping-zone matching (see the file docblock) — kept as
		   WooCommerce's own 'state' field type so it renders the district
		   dropdown that ships with WooCommerce for country BD. */
		'billing_state'      => array(
			'type'        => 'state',
			'label'       => __( 'District', 'ansclothes' ),
			'required'    => true,
			'class'       => array( 'form-row-first' ),
			'validate'    => array( 'state' ),
			'priority'    => 30,
		),
		'billing_address_2'  => array(
			'label'       => __( 'Area / Thana', 'ansclothes' ),
			'placeholder' => __( 'e.g. Banani', 'ansclothes' ),
			'required'    => false,
			'class'       => array( 'form-row-last' ),
			'priority'    => 35,
		),
		'billing_address_1'  => array(
			'type'        => 'textarea',
			'label'       => __( 'Details address', 'ansclothes' ),
			'placeholder' => __( 'Full address with house, road and area', 'ansclothes' ),
			'required'    => true,
			'class'       => array( 'form-row-wide' ),
			'autocomplete' => 'street-address',
			'priority'    => 40,
		),
		/* Kept as a hidden field: WooCommerce still needs a country for
		   shipping and tax lookups even though it is not asked for. */
		'billing_country'    => array(
			'type'     => 'hidden',
			'default'  => $country,
			'required' => false,
			'class'    => array( 'ans-hidden-field' ),
		),
	);

	// Everything ships to the billing address, so this group is not needed.
	$fields['shipping'] = array();

	unset( $fields['order']['order_comments'] );

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'ansclothes_checkout_fields', 20 );

/**
 * Default the (hidden) country to the store's own country.
 *
 * @return string
 */
function ansclothes_checkout_default_country() {
	return WC()->countries->get_base_country();
}
add_filter( 'default_checkout_billing_country', 'ansclothes_checkout_default_country' );

/**
 * Nothing is collected for shipping, so never show that address block.
 *
 * @return bool
 */
function ansclothes_checkout_no_shipping_address() {
	return false;
}
add_filter( 'woocommerce_cart_needs_shipping_address', 'ansclothes_checkout_no_shipping_address' );

/**
 * Drop the "create an account" and "ship to a different address" extras.
 */
add_filter( 'woocommerce_enable_signup_and_login_from_checkout', '__return_false' );
add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );

/**
 * Email is not collected, so make sure WooCommerce does not insist on it.
 *
 * @param array $fields Billing fields.
 * @return array
 */
function ansclothes_checkout_no_email_required( $fields ) {
	unset( $fields['billing_email'] );

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'ansclothes_checkout_no_email_required', 20 );

/**
 * No order-notes field is collected, so drop the whole "Additional
 * information" block rather than leaving an empty heading behind.
 */
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

/**
 * Instructional heading above the form.
 */
function ansclothes_checkout_heading() {
	$heading = trim( (string) ansclothes_option( 'checkout_heading' ) );

	if ( '' === $heading ) {
		return;
	}

	echo '<p class="ans-checkout__heading">' . esc_html( $heading ) . '</p>';
}
add_action( 'woocommerce_before_checkout_form', 'ansclothes_checkout_heading', 5 );

/**
 * Wording for the coupon prompt, so it reads as optional.
 *
 * @param string $message Default message.
 * @return string
 */
function ansclothes_checkout_coupon_message( $message ) {
	/* The anchor must keep the `showcoupon` class — WooCommerce's own script
	   binds the reveal toggle to it, so returning plain text leaves the
	   coupon form permanently hidden with no way to open it. */
	return sprintf(
		'%1$s <a href="#" class="showcoupon">%2$s</a>',
		esc_html__( 'Have a coupon?', 'ansclothes' ),
		esc_html__( 'Click here to enter your code (optional)', 'ansclothes' )
	);
}
add_filter( 'woocommerce_checkout_coupon_message', 'ansclothes_checkout_coupon_message' );

/**
 * Payment method section under billing details, matching the design's
 * left-column placement — the real gateway list (with the actual radio
 * input the order submits with) stays inside #order_review next to the
 * Place Order button, where WooCommerce's checkout AJAX always redraws it
 * as one block; this is a read-only preview of the same gateway(s), hidden
 * from view here via CSS on the real list so it isn't shown twice.
 *
 * With a single always-selected gateway (COD) this preview is equivalent to
 * the real control. If a second gateway is ever added, this preview would
 * need to become an interactive, synced copy — a plain duplicate would let
 * customers see the choice without being able to make it.
 */
function ansclothes_checkout_payment_preview() {
	if ( ! function_exists( 'WC' ) || ! WC()->payment_gateways() ) {
		return;
	}

	$gateways = WC()->payment_gateways()->get_available_payment_gateways();

	if ( empty( $gateways ) ) {
		return;
	}
	?>
	<div class="ans-co-payment-preview">
		<h3 class="ans-co-payment-preview__title"><?php esc_html_e( 'Payment method', 'ansclothes' ); ?></h3>
		<ul class="ans-co-payment-preview__list">
			<?php foreach ( $gateways as $gateway ) : ?>
				<li class="ans-co-payment-preview__item">
					<span class="ans-co-payment-preview__dot" aria-hidden="true"></span>
					<span>
						<strong><?php echo wp_kses_post( $gateway->get_title() ); ?></strong>
						<?php if ( $gateway->get_description() ) : ?>
							<span class="ans-co-payment-preview__desc"><?php echo wp_kses_post( $gateway->get_description() ); ?></span>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
/* Hooked after #order_review (not right after billing) so it's the last
   direct child of form.checkout in source order — with the two-column
   layout done via CSS floats, an earlier position here was pinning the
   order-summary floats down to this element's own vertical offset instead
   of letting them rise to the top of the left column. */
add_action( 'woocommerce_checkout_after_order_review', 'ansclothes_checkout_payment_preview' );

/**
 * Reassurance note under the Place Order button — the only gateway is COD,
 * so it's worth a reminder to have the exact amount ready.
 */
function ansclothes_checkout_cod_note() {
	?>
	<p class="ans-co-cod-note">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 8v5l3 2"></path></svg>
		<?php esc_html_e( 'Cash on delivery selected — please keep the exact amount ready.', 'ansclothes' ); ?>
	</p>
	<?php
}
add_action( 'woocommerce_review_order_after_submit', 'ansclothes_checkout_cod_note' );
