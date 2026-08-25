<?php
/**
 * Quick cash-on-delivery checkout modal.
 *
 * "Order Now" opens a single-step form (name / phone / address / shipping
 * choice) and creates the WooCommerce order directly, rather than sending the
 * customer through the multi-field checkout page.
 *
 * The order is built programmatically instead of going through the checkout
 * gateway flow, so it works whether or not the COD gateway is switched on in
 * WooCommerce settings. Shipping rates come from the Customizer because the
 * store has no shipping zones configured to read them from.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the quick COD checkout should be used.
 *
 * @return bool
 */
function ansclothes_cod_enabled() {
	return function_exists( 'WC' ) && WC()->cart && ansclothes_option( 'cod_enable' );
}

/**
 * The configured shipping choices.
 *
 * @return array List of [ key, label, cost ].
 */
function ansclothes_cod_shipping_options() {
	$options = array();

	foreach ( array( 'ship1', 'ship2' ) as $index => $slug ) {
		$label = trim( (string) ansclothes_option( 'cod_' . $slug . '_label' ) );

		if ( '' === $label ) {
			continue;
		}

		$options[] = array(
			'key'   => $slug,
			'label' => $label,
			'cost'  => (float) ansclothes_option( 'cod_' . $slug . '_cost' ),
		);
	}

	return apply_filters( 'ansclothes_cod_shipping_options', $options );
}

/**
 * Look up one shipping option by key, falling back to the first.
 *
 * @param string $key Option key.
 * @return array|null
 */
function ansclothes_cod_get_shipping_option( $key ) {
	$options = ansclothes_cod_shipping_options();

	foreach ( $options as $option ) {
		if ( $option['key'] === $key ) {
			return $option;
		}
	}

	return $options ? $options[0] : null;
}

/**
 * The cart lines and totals shown inside the modal.
 *
 * Re-rendered on its own whenever the quantity or shipping choice changes.
 *
 * @param string $shipping_key Selected shipping option key.
 * @return string
 */
function ansclothes_cod_summary( $shipping_key = '' ) {
	if ( ! ansclothes_cod_enabled() ) {
		return '';
	}

	$shipping = ansclothes_cod_get_shipping_option( $shipping_key );
	$ship_cost = $shipping ? $shipping['cost'] : 0;
	$subtotal  = (float) WC()->cart->get_subtotal();
	$total     = $subtotal + $ship_cost;

	ob_start();
	?>
	<div class="ans-cod__summary" data-total="<?php echo esc_attr( $total ); ?>">
		<ul class="ans-cod__items">
			<?php
			foreach ( WC()->cart->get_cart() as $key => $cart_item ) :
				$product = $cart_item['data'];

				if ( ! $product || ! $product->exists() ) {
					continue;
				}

				if ( $product->is_type( 'variation' ) ) {
					$name = get_the_title( $product->get_parent_id() );
					$meta = wc_get_formatted_variation( $product, true, false );
				} else {
					$name = $product->get_name();
					$meta = wc_get_formatted_cart_item_data( $cart_item, true );
				}
				?>
				<li class="ans-cod-item" data-key="<?php echo esc_attr( $key ); ?>">
					<div class="ans-cod-item__media">
						<?php echo $product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<span class="ans-cod-item__count"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
					</div>

					<div class="ans-cod-item__body">
						<h4 class="ans-cod-item__name"><?php echo esc_html( $name ); ?></h4>
						<?php if ( $meta ) : ?>
							<div class="ans-cod-item__meta"><?php echo wp_kses_post( $meta ); ?></div>
						<?php endif; ?>
						<div class="ans-cod-item__price"><?php echo wp_kses_post( wc_price( (float) $product->get_price() ) ); ?></div>

						<div class="ans-cod-qty">
							<button type="button" class="ans-cod-qty__btn" data-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'ansclothes' ); ?>">&minus;</button>
							<span class="ans-cod-qty__value"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
							<button type="button" class="ans-cod-qty__btn" data-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'ansclothes' ); ?>">+</button>
						</div>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>

		<div class="ans-cod__totals">
			<div class="ans-cod__row">
				<span><?php esc_html_e( 'Subtotal', 'ansclothes' ); ?></span>
				<span><?php echo wp_kses_post( wc_price( $subtotal ) ); ?></span>
			</div>
			<div class="ans-cod__row">
				<span><?php esc_html_e( 'Shipping', 'ansclothes' ); ?></span>
				<span><?php echo wp_kses_post( wc_price( $ship_cost ) ); ?></span>
			</div>
			<div class="ans-cod__row ans-cod__row--total">
				<span><?php esc_html_e( 'Total', 'ansclothes' ); ?></span>
				<span><?php echo wp_kses_post( wc_price( $total ) ); ?></span>
			</div>
		</div>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Label for the submit button, including the current total.
 *
 * @param float $total Order total.
 * @return string
 */
function ansclothes_cod_button_label( $total ) {
	return sprintf(
		'%s - %s',
		ansclothes_option( 'cod_button_label' ),
		wp_strip_all_tags( wc_price( $total ) )
	);
}

/**
 * The modal itself, printed once per page.
 */
function ansclothes_cod_modal() {
	if ( ! ansclothes_cod_enabled() ) {
		return;
	}

	$options  = ansclothes_cod_shipping_options();
	$selected = $options ? $options[0] : null;
	$total    = (float) WC()->cart->get_subtotal() + ( $selected ? $selected['cost'] : 0 );
	?>
	<div class="ans-cod" id="ans-cod-modal" aria-hidden="true">
		<div class="ans-cod__overlay" data-cod-close></div>

		<div class="ans-cod__dialog" role="dialog" aria-modal="true" aria-labelledby="ans-cod-title">
			<header class="ans-cod__head">
				<h2 id="ans-cod-title">
					<span class="ans-cod__head-icon" aria-hidden="true"></span>
					<?php echo esc_html( ansclothes_option( 'cod_title' ) ); ?>
				</h2>
				<button type="button" class="ans-cod__close" data-cod-close aria-label="<?php esc_attr_e( 'Close', 'ansclothes' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</header>

			<form class="ans-cod__form" novalidate>
				<p class="ans-cod__intro"><?php echo esc_html( ansclothes_option( 'cod_intro' ) ); ?></p>

				<div class="ans-cod__error" role="alert" aria-live="polite"></div>

				<div class="ans-cod__field">
					<label for="ans-cod-name"><?php esc_html_e( 'Full Name', 'ansclothes' ); ?> <abbr title="<?php esc_attr_e( 'required', 'ansclothes' ); ?>">*</abbr></label>
					<span class="ans-cod__input ans-cod__input--user">
						<input type="text" id="ans-cod-name" name="name" autocomplete="name" placeholder="<?php esc_attr_e( 'Full name', 'ansclothes' ); ?>" required>
					</span>
				</div>

				<div class="ans-cod__field">
					<label for="ans-cod-phone"><?php esc_html_e( 'Phone number', 'ansclothes' ); ?> <abbr title="<?php esc_attr_e( 'required', 'ansclothes' ); ?>">*</abbr></label>
					<span class="ans-cod__input ans-cod__input--phone">
						<input type="tel" id="ans-cod-phone" name="phone" autocomplete="tel" placeholder="<?php esc_attr_e( 'Phone', 'ansclothes' ); ?>" required>
					</span>
				</div>

				<div class="ans-cod__field">
					<label for="ans-cod-address"><?php esc_html_e( 'Address', 'ansclothes' ); ?> <abbr title="<?php esc_attr_e( 'required', 'ansclothes' ); ?>">*</abbr></label>
					<span class="ans-cod__input ans-cod__input--pin">
						<input type="text" id="ans-cod-address" name="address" autocomplete="street-address" placeholder="<?php esc_attr_e( 'Address', 'ansclothes' ); ?>" required>
					</span>
				</div>

				<div class="ans-cod__cart"><?php echo ansclothes_cod_summary(); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>

				<?php if ( $options ) : ?>
					<div class="ans-cod__shipping">
						<span class="ans-cod__legend"><?php esc_html_e( 'Shipping method', 'ansclothes' ); ?></span>

						<?php foreach ( $options as $index => $option ) : ?>
							<label class="ans-cod-ship<?php echo 0 === $index ? ' is-selected' : ''; ?>">
								<input
									type="radio"
									name="shipping"
									value="<?php echo esc_attr( $option['key'] ); ?>"
									<?php checked( 0, $index ); ?>>
								<span class="ans-cod-ship__dot" aria-hidden="true"></span>
								<span class="ans-cod-ship__label"><?php echo esc_html( $option['label'] ); ?></span>
								<span class="ans-cod-ship__cost"><?php echo wp_kses_post( wc_price( $option['cost'] ) ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="ans-cod__field">
					<label for="ans-cod-note"><?php esc_html_e( 'Order note', 'ansclothes' ); ?></label>
					<span class="ans-cod__input ans-cod__input--note">
						<input type="text" id="ans-cod-note" name="note" placeholder="<?php esc_attr_e( 'Order note', 'ansclothes' ); ?>">
					</span>
				</div>

				<button type="submit" class="ans-cod__submit">
					<?php echo esc_html( ansclothes_cod_button_label( $total ) ); ?>
				</button>
			</form>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'ansclothes_cod_modal' );

/**
 * Keep the modal's cart summary in step with WooCommerce's own AJAX refresh.
 *
 * @param array $fragments Fragments keyed by selector.
 * @return array
 */
function ansclothes_cod_fragments( $fragments ) {
	if ( ansclothes_cod_enabled() ) {
		$fragments['.ans-cod__cart'] = '<div class="ans-cod__cart">' . ansclothes_cod_summary() . '</div>';
	}

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'ansclothes_cod_fragments' );

/**
 * AJAX: re-render the summary for a shipping choice or quantity change.
 */
function ansclothes_cod_refresh() {
	check_ajax_referer( 'ans-cart-drawer', 'nonce' );

	if ( ! ansclothes_cod_enabled() ) {
		wp_send_json_error( array( 'message' => __( 'Checkout unavailable.', 'ansclothes' ) ), 400 );
	}

	$shipping_key = isset( $_POST['shipping'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping'] ) ) : '';

	// An optional quantity change arrives with the same request so the
	// summary only has to be rebuilt once.
	$key      = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : null;

	if ( '' !== $key && null !== $quantity && WC()->cart->get_cart_item( $key ) ) {
		if ( $quantity <= 0 ) {
			WC()->cart->remove_cart_item( $key );
		} else {
			WC()->cart->set_quantity( $key, $quantity, true );
		}
	}

	WC()->cart->calculate_totals();

	$shipping = ansclothes_cod_get_shipping_option( $shipping_key );
	$total    = (float) WC()->cart->get_subtotal() + ( $shipping ? $shipping['cost'] : 0 );

	wp_send_json_success(
		array(
			'summary'     => ansclothes_cod_summary( $shipping_key ),
			'buttonLabel' => ansclothes_cod_button_label( $total ),
			'count'       => WC()->cart->get_cart_contents_count(),
			'isEmpty'     => WC()->cart->is_empty(),
		)
	);
}
add_action( 'wp_ajax_ansclothes_cod_refresh', 'ansclothes_cod_refresh' );
add_action( 'wp_ajax_nopriv_ansclothes_cod_refresh', 'ansclothes_cod_refresh' );

/**
 * AJAX: validate the form and create the order.
 */
function ansclothes_cod_place_order() {
	check_ajax_referer( 'ans-cart-drawer', 'nonce' );

	if ( ! ansclothes_cod_enabled() ) {
		wp_send_json_error( array( 'message' => __( 'Checkout unavailable.', 'ansclothes' ) ), 400 );
	}

	if ( WC()->cart->is_empty() ) {
		wp_send_json_error( array( 'message' => __( 'Your cart is empty.', 'ansclothes' ) ), 400 );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$address = isset( $_POST['address'] ) ? sanitize_textarea_field( wp_unslash( $_POST['address'] ) ) : '';
	$note    = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
	$ship_id = isset( $_POST['shipping'] ) ? sanitize_text_field( wp_unslash( $_POST['shipping'] ) ) : '';

	$errors = array();

	if ( '' === $name ) {
		$errors[] = __( 'Please enter your full name.', 'ansclothes' );
	}

	// Digits only, so formatting characters do not pass a length check.
	if ( strlen( preg_replace( '/\D/', '', $phone ) ) < 6 ) {
		$errors[] = __( 'Please enter a valid phone number.', 'ansclothes' );
	}

	if ( '' === $address ) {
		$errors[] = __( 'Please enter your delivery address.', 'ansclothes' );
	}

	if ( $errors ) {
		wp_send_json_error( array( 'message' => implode( ' ', $errors ) ), 422 );
	}

	$shipping = ansclothes_cod_get_shipping_option( $ship_id );

	try {
		$order = wc_create_order( array( 'customer_id' => get_current_user_id() ) );

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$order->add_product( $cart_item['data'], $cart_item['quantity'] );
		}

		$fields = array(
			'first_name' => $name,
			'phone'      => $phone,
			'address_1'  => $address,
			'country'    => WC()->countries->get_base_country(),
		);

		$order->set_address( $fields, 'billing' );
		$order->set_address( $fields, 'shipping' );

		if ( $shipping ) {
			$shipping_item = new WC_Order_Item_Shipping();
			$shipping_item->set_method_title( $shipping['label'] );
			$shipping_item->set_total( $shipping['cost'] );
			$order->add_item( $shipping_item );
		}

		if ( '' !== $note ) {
			$order->set_customer_note( $note );
		}

		$order->set_payment_method( 'cod' );
		$order->set_payment_method_title( ansclothes_option( 'cod_title' ) );
		$order->calculate_totals();

		/* Stock is reduced by WooCommerce's own hook on this status change,
		   so it is not reduced here as well. */
		$order->update_status( 'processing', __( 'Order placed via quick cash-on-delivery checkout.', 'ansclothes' ) );

		WC()->cart->empty_cart();

		wp_send_json_success(
			array(
				'redirect' => $order->get_checkout_order_received_url(),
				'orderId'  => $order->get_id(),
			)
		);
	} catch ( Exception $e ) {
		wp_send_json_error( array( 'message' => __( 'Sorry, we could not place your order. Please try again.', 'ansclothes' ) ), 500 );
	}
}
add_action( 'wp_ajax_ansclothes_cod_place_order', 'ansclothes_cod_place_order' );
add_action( 'wp_ajax_nopriv_ansclothes_cod_place_order', 'ansclothes_cod_place_order' );
