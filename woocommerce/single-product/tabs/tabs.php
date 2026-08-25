<?php
/**
 * Single Product tabs — rendered as an accordion instead of a tab strip,
 * reusing the same collapsible pattern (and JS) as the archive filter panel.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty( $product_tabs ) ) {
	return;
}

$ans_first = true;
?>
<div class="ans-product-tabs">
	<?php foreach ( $product_tabs as $ans_key => $ans_tab ) : ?>
		<div class="ans-filter-group">
			<button class="ans-filter-group__toggle" aria-expanded="<?php echo $ans_first ? 'true' : 'false'; ?>" aria-controls="tab-<?php echo esc_attr( $ans_key ); ?>">
				<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $ans_key . '_tab_title', $ans_tab['title'], $ans_key ) ); ?>
				<svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="ans-filter-group__body" id="tab-<?php echo esc_attr( $ans_key ); ?>">
				<?php
				if ( isset( $ans_tab['callback'] ) ) {
					call_user_func( $ans_tab['callback'], $ans_key, $ans_tab );
				}
				?>
			</div>
		</div>
		<?php $ans_first = false; ?>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>
</div>
