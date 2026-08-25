<?php
/**
 * Slide-in cart drawer.
 *
 * Replaces the header cart link's navigation with an off-canvas panel that
 * lists the current cart and lets the customer adjust quantities or remove
 * lines without leaving the page. The link keeps its href so it still works
 * as a plain link to the cart page when JavaScript is unavailable.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the drawer can run (WooCommerce loaded and cart available).
 *
 * @return bool
 */
function ansclothes_cart_drawer_available() {
	return function_exists( 'WC' ) && WC()->cart;
}

/**
 * Markup for the drawer's scrollable line-item area and totals.
 *
 * Kept separate from the shell so it can be re-rendered on its own for both
 * WooCommerce's add-to-cart fragments and this theme's own AJAX updates.
 *
 * @return string
 */
function ansclothes_cart_drawer_inner() {
	if ( ! ansclothes_cart_drawer_available() ) {
		return '';
	}

	$cart  = WC()->cart;
	$items = $cart->get_cart();

	ob_start();
	?>
	<div class="ans-cart-drawer__inner">
		<?php if ( empty( $items ) ) : ?>

			<div class="ans-cart-drawer__empty">
				<p><?php esc_html_e( 'Your cart is empty.', 'ansclothes' ); ?></p>
				<a class="ans-cart-drawer__continue" href="<?php echo esc_url( ansclothes_shop_url() ); ?>">
					<?php esc_html_e( 'Continue shopping', 'ansclothes' ); ?>
				</a>
			</div>

		<?php else : ?>

			<ul class="ans-cart-drawer__items">
				<?php
				foreach ( $items as $cart_item_key => $cart_item ) :
					$product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

					if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
						continue;
					}

					$product_link = $product->is_visible() ? $product->get_permalink( $cart_item ) : '';
					$thumbnail    = $product->get_image( 'woocommerce_thumbnail' );

					/*
					 * WooCommerce folds the chosen variant into the product name
					 * ("Tee - L"). Split them back out so the name stays on one
					 * line and the variant reads as its own detail underneath.
					 */
					if ( $product->is_type( 'variation' ) ) {
						$product_name = get_the_title( $product->get_parent_id() );
						$item_meta    = wc_get_formatted_variation( $product, true, false );
					} else {
						$product_name = $product->get_name();
						$item_meta    = wc_get_formatted_cart_item_data( $cart_item, true );
					}

					$product_name = apply_filters( 'woocommerce_cart_item_name', $product_name, $cart_item, $cart_item_key );
					$line_price   = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cart_item, $cart_item_key );
					?>
					<li class="ans-cart-line" data-key="<?php echo esc_attr( $cart_item_key ); ?>">
						<div class="ans-cart-line__media">
							<?php if ( $product_link ) : ?>
								<a href="<?php echo esc_url( $product_link ); ?>"><?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<?php else : ?>
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput ?>
							<?php endif; ?>
						</div>

						<div class="ans-cart-line__body">
							<h3 class="ans-cart-line__name">
								<?php if ( $product_link ) : ?>
									<a href="<?php echo esc_url( $product_link ); ?>"><?php echo wp_kses_post( $product_name ); ?></a>
								<?php else : ?>
									<?php echo wp_kses_post( $product_name ); ?>
								<?php endif; ?>
							</h3>

							<?php if ( $item_meta ) : ?>
								<div class="ans-cart-line__meta"><?php echo wp_kses_post( $item_meta ); ?></div>
							<?php endif; ?>

							<div class="ans-cart-line__price"><?php echo wp_kses_post( $line_price ); ?></div>

							<div class="ans-cart-line__controls">
								<div class="ans-cart-qty">
									<button type="button" class="ans-cart-qty__btn" data-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'ansclothes' ); ?>">&minus;</button>
									<input
										type="number"
										class="ans-cart-qty__input"
										value="<?php echo esc_attr( $cart_item['quantity'] ); ?>"
										min="0"
										step="1"
										aria-label="<?php esc_attr_e( 'Quantity', 'ansclothes' ); ?>">
									<button type="button" class="ans-cart-qty__btn" data-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'ansclothes' ); ?>">+</button>
								</div>

								<button type="button" class="ans-cart-line__remove">
									<?php esc_html_e( 'Remove', 'ansclothes' ); ?>
								</button>
							</div>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>

		<?php endif; ?>
	</div>

	<div class="ans-cart-drawer__footer<?php echo empty( $items ) ? ' is-hidden' : ''; ?>">
		<div class="ans-cart-drawer__total">
			<span><?php esc_html_e( 'Estimated total', 'ansclothes' ); ?></span>
			<strong><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></strong>
		</div>
		<p class="ans-cart-drawer__note"><?php esc_html_e( 'Taxes and shipping calculated at checkout.', 'ansclothes' ); ?></p>
		<a class="ans-cart-drawer__checkout" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
			<span class="ans-cart-drawer__checkout-icon" aria-hidden="true"></span>
			<?php esc_html_e( 'Order Now', 'ansclothes' ); ?>
		</a>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * The drawer shell, printed once per page.
 */
function ansclothes_cart_drawer() {
	if ( ! ansclothes_cart_drawer_available() ) {
		return;
	}
	?>
	<div class="ans-cart-drawer" id="ans-cart-drawer" aria-hidden="true">
		<div class="ans-cart-drawer__overlay" data-cart-close></div>

		<aside class="ans-cart-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Cart', 'ansclothes' ); ?>">
			<header class="ans-cart-drawer__head">
				<div class="ans-cart-drawer__heading">
					<span class="ans-cart-drawer__eyebrow">
						<?php esc_html_e( 'Curated selection', 'ansclothes' ); ?>
						<span class="ans-cart-drawer__badge"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
					</span>
					<h2><?php esc_html_e( 'Cart', 'ansclothes' ); ?></h2>
				</div>

				<button type="button" class="ans-cart-drawer__close" data-cart-close aria-label="<?php esc_attr_e( 'Close cart', 'ansclothes' ); ?>">
					<span aria-hidden="true">&times;</span>
				</button>
			</header>

			<div class="ans-cart-drawer__body">
				<?php echo ansclothes_cart_drawer_inner(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</div>

			<div class="ans-cart-drawer__loading" aria-hidden="true"></div>
		</aside>
	</div>
	<?php
}
add_action( 'wp_footer', 'ansclothes_cart_drawer' );

/**
 * Keep the drawer contents, the header count and the drawer badge in sync
 * with WooCommerce's own AJAX add-to-cart refresh.
 *
 * @param array $fragments Fragments keyed by CSS selector.
 * @return array
 */
function ansclothes_cart_drawer_fragments( $fragments ) {
	if ( ! ansclothes_cart_drawer_available() ) {
		return $fragments;
	}

	$count = WC()->cart->get_cart_contents_count();

	$fragments['.ans-cart-drawer__body']  = '<div class="ans-cart-drawer__body">' . ansclothes_cart_drawer_inner() . '</div>';
	$fragments['.ans-cart-count']         = '<span class="ans-cart-count">' . esc_html( $count ) . '</span>';
	$fragments['.ans-cart-drawer__badge'] = '<span class="ans-cart-drawer__badge">' . esc_html( $count ) . '</span>';

	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'ansclothes_cart_drawer_fragments' );

/**
 * AJAX: set a line's quantity, or remove it when the quantity reaches zero.
 */
function ansclothes_cart_drawer_update() {
	check_ajax_referer( 'ans-cart-drawer', 'nonce' );

	if ( ! ansclothes_cart_drawer_available() ) {
		wp_send_json_error( array( 'message' => __( 'Cart unavailable.', 'ansclothes' ) ), 400 );
	}

	$key      = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
	$quantity = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : null;

	if ( '' === $key || ! WC()->cart->get_cart_item( $key ) ) {
		wp_send_json_error( array( 'message' => __( 'That item is no longer in your cart.', 'ansclothes' ) ), 404 );
	}

	if ( null === $quantity || $quantity <= 0 ) {
		WC()->cart->remove_cart_item( $key );
	} else {
		WC()->cart->set_quantity( $key, $quantity, true );
	}

	WC()->cart->calculate_totals();

	$count = WC()->cart->get_cart_contents_count();

	wp_send_json_success(
		array(
			'contents' => ansclothes_cart_drawer_inner(),
			'count'    => $count,
		)
	);
}
add_action( 'wp_ajax_ansclothes_cart_update', 'ansclothes_cart_drawer_update' );
add_action( 'wp_ajax_nopriv_ansclothes_cart_update', 'ansclothes_cart_drawer_update' );
