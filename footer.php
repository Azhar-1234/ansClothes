<?php
/**
 * Footer.
 *
 * @package ANSClothes
 */

?>
</main><!-- #content -->

<?php get_template_part( 'template-parts/global/pre-footer' ); ?>

<footer class="ans-footer">
	<div class="ans-footer__main">
		<div class="ans-footer__brand">
			<?php ansclothes_logo(); ?>
			<p><?php esc_html_e( "A brand dedicated to redefining men's fashion in Bangladesh.", 'ansclothes' ); ?></p>
			<strong><?php esc_html_e( 'Hotline 24/7: +880 9611 900372', 'ansclothes' ); ?></strong>
		</div>

		<nav class="ans-footer__col" aria-label="<?php esc_attr_e( 'Information', 'ansclothes' ); ?>">
			<h2><?php esc_html_e( 'Information', 'ansclothes' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php esc_html_e( 'About Us', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Blog', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( ansclothes_shop_url() ); ?>"><?php esc_html_e( 'Outlets', 'ansclothes' ); ?></a></li>
			</ul>
		</nav>

		<nav class="ans-footer__col" aria-label="<?php esc_attr_e( 'Policies', 'ansclothes' ); ?>">
			<h2><?php esc_html_e( 'Policies', 'ansclothes' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/refund-return-policy/' ) ); ?>"><?php esc_html_e( 'Refund & Return Policy', 'ansclothes' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Community Guidelines', 'ansclothes' ); ?></a></li>
			</ul>
		</nav>

		<div class="ans-footer__col">
			<h2><?php esc_html_e( 'Visit Us', 'ansclothes' ); ?></h2>
			<p><?php esc_html_e( 'Banani: Level 3, House 45, Road 11, Block C', 'ansclothes' ); ?></p>
			<p><?php esc_html_e( 'Mirpur 1: Level 4, Rupayan Latifa Shamsuddin Square', 'ansclothes' ); ?></p>
		</div>
	</div>

	<div class="ans-footer__bottom">
		<span class="ans-footer__copy"><?php echo esc_html( ansclothes_option( 'footer_copy' ) ); ?></span>
		<div class="ans-footer__social">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Facebook', 'ansclothes' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Instagram', 'ansclothes' ); ?></a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
