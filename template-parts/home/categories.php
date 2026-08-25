<?php
/**
 * Shop by category.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'categories_enable' ) ) {
	return;
}

$ansclothes_terms = get_terms(
	array(
		'taxonomy'   => 'ans_product_cat',
		'hide_empty' => false,
		'number'     => 8,
	)
);

$ansclothes_has_terms = ! is_wp_error( $ansclothes_terms ) && ! empty( $ansclothes_terms );
$ansclothes_count     = $ansclothes_has_terms ? count( $ansclothes_terms ) : count( ansclothes_demo_categories() );
?>

<section id="categories" class="ans-section ans-wrap ans-reveal">
	<div class="ans-section-head">
		<h2><?php ansclothes_heading( 'categories_heading' ); ?></h2>
		<span class="ans-meta">
			<?php
			printf(
				/* translators: %s: number of collections. */
				esc_html__( '%s Collections', 'ansclothes' ),
				esc_html( zeroise( $ansclothes_count, 2 ) )
			);
			?>
		</span>
	</div>

	<div class="ans-grid-4">
		<?php
		if ( $ansclothes_has_terms ) {
			foreach ( $ansclothes_terms as $ansclothes_term ) {
				$ansclothes_label = get_term_meta( $ansclothes_term->term_id, 'ans_count_label', true );
				$ansclothes_img   = (int) get_term_meta( $ansclothes_term->term_id, 'ans_term_image', true );

				if ( ! $ansclothes_label ) {
					$ansclothes_label = sprintf(
						/* translators: %s: number of items. */
						_n( '%s Item', '%s Items', $ansclothes_term->count, 'ansclothes' ),
						zeroise( $ansclothes_term->count, 2 )
					);
				}

				ansclothes_category_card(
					array(
						'name'  => $ansclothes_term->name,
						'count' => $ansclothes_label,
						'image' => $ansclothes_img ? wp_get_attachment_image_url( $ansclothes_img, 'ansclothes-card' ) : '',
						'url'   => get_term_link( $ansclothes_term ),
					)
				);
			}
		} else {
			foreach ( ansclothes_demo_categories() as $ansclothes_cat ) {
				ansclothes_category_card( $ansclothes_cat );
			}
		}
		?>
	</div>
</section>
