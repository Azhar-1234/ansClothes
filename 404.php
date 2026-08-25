<?php
/**
 * 404.
 *
 * @package ANSClothes
 */

get_header();
?>

<section class="ans-404">
	<h1>404</h1>
	<p class="ans-meta"><?php esc_html_e( 'This page slipped out of stock.', 'ansclothes' ); ?></p>
	<p style="margin-top:32px">
		<a class="ans-btn" href="<?php echo esc_url( ansclothes_shop_url() ); ?>"><?php esc_html_e( 'Back to the shop', 'ansclothes' ); ?></a>
	</p>
</section>

<?php
get_footer();
