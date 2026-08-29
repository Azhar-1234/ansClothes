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
		</div>

		<nav class="ans-footer__col" aria-label="<?php esc_attr_e( 'Information', 'ansclothes' ); ?>">
			<h2><?php esc_html_e( 'Information', 'ansclothes' ); ?></h2>
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => '',
					)
				);
				?>
			<?php else : ?>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/#about' ) ); ?>"><?php esc_html_e( 'About Us', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/#contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Blog', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( ansclothes_shop_url() ); ?>"><?php esc_html_e( 'Outlets', 'ansclothes' ); ?></a></li>
				</ul>
			<?php endif; ?>
		</nav>

		<nav class="ans-footer__col" aria-label="<?php esc_attr_e( 'Policies', 'ansclothes' ); ?>">
			<h2><?php esc_html_e( 'Policies', 'ansclothes' ); ?></h2>
			<?php if ( has_nav_menu( 'footer_policies' ) ) : ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer_policies',
						'container'      => false,
						'menu_class'     => '',
					)
				);
				?>
			<?php else : ?>
				<ul>
					<li><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/refund-return-policy/' ) ); ?>"><?php esc_html_e( 'Refund & Return Policy', 'ansclothes' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/community-guidelines/' ) ); ?>"><?php esc_html_e( 'Community Guidelines', 'ansclothes' ); ?></a></li>
				</ul>
			<?php endif; ?>
		</nav>

		<div class="ans-footer__col">
			<h2><?php esc_html_e( 'Visit Us', 'ansclothes' ); ?></h2>
			<?php if ( ansclothes_option( 'footer_address_1' ) ) : ?>
				<p><?php echo esc_html( ansclothes_option( 'footer_address_1' ) ); ?></p>
			<?php endif; ?>
			<?php if ( ansclothes_option( 'footer_address_2' ) ) : ?>
				<p><?php echo esc_html( ansclothes_option( 'footer_address_2' ) ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
		<div class="ans-footer-widgets">
			<?php dynamic_sidebar( 'footer-1' ); ?>
		</div>
	<?php endif; ?>

	<div class="ans-footer__bottom">
		<span class="ans-footer__copy"><?php echo esc_html( ansclothes_option( 'footer_copy' ) ); ?></span>
		<?php if ( ansclothes_option( 'footer_facebook_url' ) || ansclothes_option( 'footer_instagram_url' ) ) : ?>
			<div class="ans-footer__social">
				<?php if ( ansclothes_option( 'footer_facebook_url' ) ) : ?>
					<a href="<?php echo esc_url( ansclothes_option( 'footer_facebook_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook', 'ansclothes' ); ?></a>
				<?php endif; ?>
				<?php if ( ansclothes_option( 'footer_instagram_url' ) ) : ?>
					<a href="<?php echo esc_url( ansclothes_option( 'footer_instagram_url' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Instagram', 'ansclothes' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
