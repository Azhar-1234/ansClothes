<?php
/**
 * Search results.
 *
 * @package ANSClothes
 */

get_header();
?>

<div class="ans-page-header">
	<span class="ans-eyebrow"><?php esc_html_e( 'Search', 'ansclothes' ); ?></span>
	<h1>
		<?php
		printf(
			/* translators: %s: search query. */
			esc_html__( 'Results for “%s”', 'ansclothes' ),
			esc_html( get_search_query() )
		);
		?>
	</h1>
	<?php get_search_form(); ?>
</div>

<div class="ans-archive">
	<?php if ( have_posts() ) : ?>
		<div class="ans-grid-3">
			<?php
			while ( have_posts() ) :
				the_post();

				if ( 'ans_product' === get_post_type() ) {
					get_template_part( 'template-parts/content', 'ans_product' );
				} else {
					get_template_part( 'template-parts/content' );
				}
			endwhile;
			?>
		</div>

		<?php ansclothes_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>

<?php
get_footer();
