<?php
/**
 * Product archive (the shop) — also used for collection archives.
 *
 * @package ANSClothes
 */

get_header();

$ansclothes_is_tax = is_tax( 'ans_product_cat' );
?>

<div class="ans-page-header">
	<span class="ans-eyebrow"><?php esc_html_e( 'The Shop', 'ansclothes' ); ?></span>
	<h1>
		<?php
		if ( $ansclothes_is_tax ) {
			single_term_title();
		} else {
			post_type_archive_title();
		}
		?>
	</h1>

	<?php
	if ( $ansclothes_is_tax && ansclothes_option( 'category_desc_enable' ) && term_description() ) {
		echo '<div class="ans-term-desc">' . wp_kses_post( term_description() ) . '</div>';
	}
	?>
</div>

<div class="ans-archive">
	<?php if ( have_posts() ) : ?>
		<div class="ans-grid-4">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'ans_product' );
			endwhile;
			?>
		</div>

		<?php ansclothes_pagination(); ?>
	<?php else : ?>
		<div class="ans-grid-4">
			<?php
			foreach ( ansclothes_demo_products() as $ansclothes_product ) {
				ansclothes_product_card( $ansclothes_product );
			}
			?>
		</div>
		<p class="ans-meta" style="margin-top:32px">
			<?php esc_html_e( 'Sample products — publish products in the dashboard to replace them.', 'ansclothes' ); ?>
		</p>
	<?php endif; ?>
</div>

<?php
get_footer();
