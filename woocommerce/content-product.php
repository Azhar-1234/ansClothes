<?php
/**
 * Product card for shop/category archive — ANSClothes redesign.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$product_id   = $product->get_id();
$product_link = get_permalink( $product_id );
$product_name = $product->get_name();
$price_html   = $product->get_price_html();
$is_on_sale   = $product->is_on_sale();

/* ── Image ── */
$image_id  = $product->get_image_id();
$image_url = '';
if ( $image_id ) {
	$image_url = wp_get_attachment_image_url( $image_id, 'ansclothes-card' );
	if ( ! $image_url ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'large' );
	}
	if ( ! $image_url ) {
		$image_url = wp_get_attachment_image_url( $image_id, 'full' );
	}
}
if ( ! $image_url ) {
	$image_url = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '';
}

/* ── Sizes ──
 * Shared helper so cards match the sidebar Size filter: size is stored
 * under "size" on some products and "length" on others, and this only
 * checked "size", so half the catalogue rendered no size pills at all.
 */
$sizes = function_exists( 'ansclothes_get_product_sizes' )
	? ansclothes_get_product_sizes( $product )
	: array();
?>
<li class="ans-pcard">

	<div class="ans-pcard__media">
		<a href="<?php echo esc_url( $product_link ); ?>" aria-label="<?php echo esc_attr( $product_name ); ?>">
			<?php if ( $image_url ) : ?>
				<img
					src="<?php echo esc_url( $image_url ); ?>"
					alt="<?php echo esc_attr( $product_name ); ?>"
					loading="lazy"
					class="ans-pcard__img">
			<?php else : ?>
				<span class="ans-card__placeholder"><?php echo esc_html( $product_name ); ?></span>
			<?php endif; ?>
		</a>

		<?php if ( $is_on_sale ) : ?>
			<span class="ans-pcard__badge" aria-label="<?php esc_attr_e( 'On sale', 'ansclothes' ); ?>">
				<?php esc_html_e( 'Sale', 'ansclothes' ); ?>
			</span>
		<?php endif; ?>
	</div>

	<div class="ans-pcard__info">
		<h3 class="ans-pcard__name">
			<a href="<?php echo esc_url( $product_link ); ?>"><?php echo esc_html( $product_name ); ?></a>
		</h3>

		<?php if ( $price_html ) : ?>
			<div class="ans-pcard__price"><?php echo wp_kses_post( $price_html ); ?></div>
		<?php endif; ?>

		<?php if ( $sizes ) : ?>
			<div class="ans-pcard__sizes">
				<?php foreach ( $sizes as $size ) : ?>
					<span class="ans-pcard__size"><?php echo esc_html( strtoupper( $size ) ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="ans-pcard__actions">
			<?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>
				<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				   data-quantity="1"
				   data-product_id="<?php echo esc_attr( $product_id ); ?>"
				   data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				   class="ans-pcard__atc<?php echo $product->is_type( 'simple' ) ? ' ajax_add_to_cart add_to_cart_button' : ''; ?>">
					<?php esc_html_e( 'Add to Cart', 'ansclothes' ); ?>
				</a>
			<?php elseif ( ! $product->is_in_stock() ) : ?>
				<span class="ans-pcard__atc ans-pcard__atc--oos" aria-disabled="true">
					<?php esc_html_e( 'Out of stock', 'ansclothes' ); ?>
				</span>
			<?php else : ?>
				<a href="<?php echo esc_url( $product_link ); ?>" class="ans-pcard__atc">
					<?php esc_html_e( 'View Product', 'ansclothes' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>

</li>
