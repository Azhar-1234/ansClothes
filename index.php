<?php
/**
 * Fallback template — blog index and anything without a more specific template.
 *
 * @package ANSClothes
 */

get_header();
?>

<div class="ans-page-header">
	<h1>
		<?php
		if ( is_home() && ! is_front_page() ) {
			single_post_title();
		} else {
			esc_html_e( 'Journal', 'ansclothes' );
		}
		?>
	</h1>
</div>

<div class="ans-archive">
	<?php if ( have_posts() ) : ?>
		<div class="ans-grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
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
