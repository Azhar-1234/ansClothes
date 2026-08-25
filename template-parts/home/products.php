<?php
/**
 * New arrivals grid.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'products_enable' ) ) {
	return;
}

$ansclothes_limit = max( 1, (int) ansclothes_option( 'products_count' ) );
$ansclothes_post_type = ansclothes_has_woocommerce() ? 'product' : 'ans_product';

$ansclothes_query = new WP_Query(
	array(
		'post_type'           => $ansclothes_post_type,
		'post_status'         => 'publish',
		'posts_per_page'      => $ansclothes_limit,
		'orderby'             => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

$ansclothes_has_products = $ansclothes_query->have_posts();

$ansclothes_total = $ansclothes_has_products
	? (int) wp_count_posts( $ansclothes_post_type )->publish
	: count( ansclothes_demo_products() );
?>

<section id="shop" class="ans-section ans-wrap ans-reveal">
	<div class="ans-section-head">
		<h2><?php ansclothes_heading( 'products_heading' ); ?></h2>
		<span class="ans-meta">
			<?php
			printf(
				/* translators: %s: number of styles. */
				esc_html__( '%s Styles', 'ansclothes' ),
				esc_html( zeroise( $ansclothes_total, 2 ) )
			);
			?>
		</span>
	</div>

	<div class="ans-grid-4">
		<?php
		if ( $ansclothes_has_products ) {
			while ( $ansclothes_query->have_posts() ) {
				$ansclothes_query->the_post();

				ansclothes_product_card( ansclothes_get_product_card_args() );
			}

			wp_reset_postdata();
		} else {
			foreach ( array_slice( ansclothes_demo_products(), 0, $ansclothes_limit ) as $ansclothes_product ) {
				ansclothes_product_card( $ansclothes_product );
			}
		}
		?>
	</div>

	<?php if ( $ansclothes_has_products && $ansclothes_total > $ansclothes_limit ) : ?>
		<p style="margin-top:48px;text-align:center">
			<a class="ans-btn ans-btn--ghost" href="<?php echo esc_url( ansclothes_shop_url() ); ?>"><?php esc_html_e( 'View all products', 'ansclothes' ); ?></a>
		</p>
	<?php endif; ?>
</section>
