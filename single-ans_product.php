<?php
/**
 * Single product.
 *
 * @package ANSClothes
 */

get_header();

while ( have_posts() ) :
	the_post();

	$ansclothes_price = ansclothes_price();
	$ansclothes_badge = get_post_meta( get_the_ID(), '_ans_badge', true );
	$ansclothes_sizes = array_filter( array_map( 'trim', explode( ',', (string) get_post_meta( get_the_ID(), '_ans_sizes', true ) ) ) );
	$ansclothes_terms = get_the_terms( get_the_ID(), 'ans_product_cat' );
	?>

	<article <?php post_class( 'ans-single-product' ); ?>>
		<div class="ans-single-product__media">
			<?php if ( $ansclothes_badge ) : ?>
				<span class="ans-badge"><?php echo esc_html( $ansclothes_badge ); ?></span>
			<?php endif; ?>

			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'ansclothes-hero' );
			} else {
				echo '<span class="ans-card__placeholder">' . esc_html( get_the_title() ) . '</span>';
			}
			?>
		</div>

		<div class="ans-single-product__info">
			<?php if ( $ansclothes_terms && ! is_wp_error( $ansclothes_terms ) ) : ?>
				<span class="ans-eyebrow">
					<a href="<?php echo esc_url( get_term_link( $ansclothes_terms[0] ) ); ?>"><?php echo esc_html( $ansclothes_terms[0]->name ); ?></a>
				</span>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<?php if ( $ansclothes_price ) : ?>
				<span class="ans-single-product__price"><?php echo esc_html( $ansclothes_price ); ?></span>
			<?php endif; ?>

			<?php if ( has_excerpt() ) : ?>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<?php if ( $ansclothes_sizes ) : ?>
				<div>
					<span class="ans-eyebrow" style="display:block;margin-bottom:10px"><?php esc_html_e( 'Available sizes', 'ansclothes' ); ?></span>
					<div class="ans-sizes">
						<?php foreach ( $ansclothes_sizes as $ansclothes_size ) : ?>
							<span><?php echo esc_html( $ansclothes_size ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<a class="ans-btn" href="<?php echo esc_url( ansclothes_cart_url() ); ?>" style="align-self:flex-start">
				<?php esc_html_e( 'Order now', 'ansclothes' ); ?>
			</a>
		</div>
	</article>

	<?php if ( trim( get_the_content() ) ) : ?>
		<div class="ans-content"><?php the_content(); ?></div>
	<?php endif; ?>

	<?php
	// Related products from the same collection.
	if ( $ansclothes_terms && ! is_wp_error( $ansclothes_terms ) ) :
		$ansclothes_related = new WP_Query(
			array(
				'post_type'      => 'ans_product',
				'posts_per_page' => 4,
				'post__not_in'   => array( get_the_ID() ),
				'no_found_rows'  => true,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'ans_product_cat',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $ansclothes_terms, 'term_id' ),
					),
				),
			)
		);

		if ( $ansclothes_related->have_posts() ) :
			?>
			<section class="ans-section ans-wrap">
				<div class="ans-section-head">
					<h2><?php esc_html_e( 'You might also like', 'ansclothes' ); ?></h2>
				</div>
				<div class="ans-grid-4">
					<?php
					while ( $ansclothes_related->have_posts() ) :
						$ansclothes_related->the_post();
						get_template_part( 'template-parts/content', 'ans_product' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
			<?php
		endif;
	endif;

endwhile;

get_footer();
