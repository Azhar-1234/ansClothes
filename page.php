<?php
/**
 * Static page.
 *
 * @package ANSClothes
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<article <?php post_class(); ?>>
		<?php // "Order received" and "Pay for order" also route through is_checkout() — only the checkout form itself drops its title. ?>
		<?php if ( ! is_checkout() || is_wc_endpoint_url() ) : ?>
			<div class="ans-page-header">
				<h1><?php the_title(); ?></h1>
			</div>
		<?php endif; ?>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="ans-wrap" style="margin-top:40px">
				<?php the_post_thumbnail( 'full' ); ?>
			</div>
		<?php endif; ?>

		<div class="ans-content">
			<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="ans-meta">' . esc_html__( 'Pages:', 'ansclothes' ) . ' ',
					'after'  => '</div>',
				)
			);
			?>
		</div>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}

endwhile;

get_footer();
