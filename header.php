<?php
/**
 * Header.
 *
 * @package ANSClothes
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'ansclothes' ); ?></a>

<?php if ( ansclothes_option( 'announcement_enable' ) && ansclothes_option( 'announcement_text' ) ) : ?>
	<div class="ans-announcement"<?php echo ansclothes_announcement_style(); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<?php ansclothes_announcement_content(); ?>
	</div>
<?php endif; ?>

<header class="ans-header">
	<div class="ans-header__bar">
		<div class="ans-branding">
			<?php
			ansclothes_logo();

			if ( ! has_custom_logo() && ! get_theme_mod( 'ansclothes_hide_title' ) ) :
				?>
				<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
				<?php
			endif;
			?>
		</div>

		<nav id="ans-primary-nav" class="ans-nav" aria-label="<?php esc_attr_e( 'Site navigation', 'ansclothes' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_class'     => '',
						'container'      => false,
					)
				);
			} else {
				?>
				<ul>
					<?php foreach ( ansclothes_product_category_links() as $ansclothes_link ) : ?>
						<li><a href="<?php echo esc_url( $ansclothes_link['url'] ); ?>"><?php echo esc_html( $ansclothes_link['name'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php
			}
			?>
		</nav>

		<div class="ans-header__actions">
			<button class="ans-icon-btn ans-search-toggle" type="button" aria-label="<?php esc_attr_e( 'Search products', 'ansclothes' ); ?>" aria-expanded="false" aria-controls="ans-header-search">
				<span class="ans-icon ans-icon--search" aria-hidden="true"></span>
			</button>

			<a class="ans-icon-btn" href="<?php echo esc_url( ansclothes_account_url() ); ?>" aria-label="<?php esc_attr_e( 'My account', 'ansclothes' ); ?>">
				<span class="ans-icon ans-icon--user" aria-hidden="true"></span>
			</a>

			<a class="ans-icon-btn ans-cart-btn" href="<?php echo esc_url( ansclothes_cart_url() ); ?>" aria-label="<?php echo esc_attr( ansclothes_bag_label() ); ?>">
				<span class="ans-icon ans-icon--bag" aria-hidden="true"></span>
				<span class="ans-cart-count"><?php echo esc_html( preg_replace( '/\D+/', '', ansclothes_bag_label() ) ); ?></span>
			</a>

			<button class="ans-nav-toggle" aria-expanded="false" aria-controls="ans-primary-nav">
				<?php esc_html_e( 'Menu', 'ansclothes' ); ?>
			</button>
		</div>
	</div>

	<form id="ans-header-search" class="ans-header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="ans-header-search-field"><?php esc_html_e( 'Search products', 'ansclothes' ); ?></label>
		<input type="search" id="ans-header-search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products', 'ansclothes' ); ?>">
		<?php if ( ansclothes_has_woocommerce() ) : ?>
			<input type="hidden" name="post_type" value="product">
		<?php endif; ?>
	</form>
</header>

<main id="content">
