<?php
/**
 * WooCommerce wrapper — redesigned archive layout with sidebar filters.
 *
 * Renders the standard WooCommerce shop/category/product pages inside the
 * ANSClothes archive shell (hero header + filter sidebar + product grid).
 *
 * @package ANSClothes
 */

get_header();

$is_archive = is_shop() || is_product_category() || is_product_tag();
?>

<?php if ( $is_archive ) : ?>

	<?php
	/* ── Page header ── */
	$archive_title = '';
	if ( is_shop() ) {
		$archive_title = get_the_title( wc_get_page_id( 'shop' ) );
	} elseif ( is_product_category() ) {
		$archive_title = single_term_title( '', false );
	} elseif ( is_product_tag() ) {
		$archive_title = single_term_title( '', false );
	}
	?>

	<div class="ans-archive-header">
		<h1 class="ans-archive-title"><?php echo esc_html( $archive_title ); ?></h1>
		<span class="ans-archive-divider" aria-hidden="true">
			<span class="ans-archive-divider__line"></span>
			<svg class="ans-archive-divider__icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<rect x="7" y="7" width="10" height="10" fill="currentColor" transform="rotate(45 12 12)"/>
			</svg>
			<span class="ans-archive-divider__line"></span>
		</span>
		<?php
		if ( is_product_category() ) {
			$term_desc = term_description();
			if ( $term_desc ) {
				echo '<div class="ans-archive-desc">' . wp_kses_post( $term_desc ) . '</div>';
			}
		}
		?>
	</div>

	<div class="ans-archive-shell">

		<!-- Sidebar / Filters -->
		<aside class="ans-filter-sidebar" id="ans-filter-sidebar">
			<h2 class="ans-filter-sidebar__title"><?php esc_html_e( 'Filters', 'ansclothes' ); ?></h2>

			<?php if ( class_exists( 'WooCommerce' ) ) : ?>

				<?php
				/* Price filter widget area — rendered via a hook-based shortcode wrapper */
				$min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
				$max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

				$price_max = (int) apply_filters( 'woocommerce_price_filter_widget_max_amount',
					(float) WC()->query->get_main_query()->get( 'wc_query' ) !== false
						? wp_cache_get( 'wc_max_price_filter' )
						: 0
				);
				?>

				<!-- Price Filter -->
				<?php
				ob_start();
				the_widget( 'WC_Widget_Price_Filter', array( 'title' => '' ) );
				$ans_price_widget_html = trim( ob_get_clean() );

				// WC_Widget_Price_Filter renders nothing when every product in the
				// current archive shares the same price (no range to filter). Rather
				// than hide the section — which reads as "broken" — show a plain
				// price line so the panel stays consistent across every category.
				$ans_price_fallback = '';
				if ( '' === $ans_price_widget_html ) {
					$ans_prices = array();
					foreach ( ansclothes_current_archive_product_ids() as $ans_pid ) {
						$ans_product_for_price = wc_get_product( $ans_pid );
						if ( $ans_product_for_price ) {
							$ans_prices[] = (float) $ans_product_for_price->get_price();
						}
					}
					if ( $ans_prices ) {
						$ans_min_price = min( $ans_prices );
						$ans_max_price = max( $ans_prices );
						$ans_price_fallback = ( $ans_min_price === $ans_max_price )
							? sprintf(
								/* translators: %s: price */
								esc_html__( 'All items are %s', 'ansclothes' ),
								wc_price( $ans_min_price )
							)
							: sprintf(
								/* translators: 1: min price, 2: max price */
								esc_html__( 'Price: %1$s — %2$s', 'ansclothes' ),
								wc_price( $ans_min_price ),
								wc_price( $ans_max_price )
							);
					}
				}
				?>
				<div class="ans-filter-group">
					<button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-price">
						<?php esc_html_e( 'Price', 'ansclothes' ); ?>
						<svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
					<div class="ans-filter-group__body" id="filter-price">
						<?php if ( '' !== $ans_price_widget_html ) : ?>
							<?php echo $ans_price_widget_html; // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php elseif ( '' !== $ans_price_fallback ) : ?>
							<p class="ans-price-fallback"><?php echo wp_kses_post( $ans_price_fallback ); ?></p>
						<?php else : ?>
							<p class="ans-price-fallback"><?php esc_html_e( 'No products to filter by price.', 'ansclothes' ); ?></p>
						<?php endif; ?>
					</div>
				</div>

				<!-- Category Filter -->
				<div class="ans-filter-group">
					<button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-cats">
						<?php esc_html_e( 'Categories', 'ansclothes' ); ?>
						<svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
					<div class="ans-filter-group__body" id="filter-cats">
						<?php
						the_widget(
							'WC_Widget_Product_Categories',
							array(
								'title'              => '',
								'show_children_only' => 0,
								'hide_empty'         => 0,
								'show_count'         => 0,
								'orderby'            => 'name',
								'dropdown'           => 0,
							)
						);
						?>
					</div>
				</div>

				<!-- Attribute / Size Filter -->
				<?php $ans_available_sizes = ansclothes_get_available_sizes(); ?>
				<?php if ( $ans_available_sizes ) : ?>
				<div class="ans-filter-group">
					<button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-size">
						<?php esc_html_e( 'Size', 'ansclothes' ); ?>
						<svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
					<div class="ans-filter-group__body" id="filter-size">
						<ul class="ans-size-filter-list">
							<?php foreach ( $ans_available_sizes as $ans_size ) : ?>
								<li>
									<a href="<?php echo esc_url( ansclothes_size_filter_url( $ans_size ) ); ?>"
									   class="<?php echo ansclothes_is_size_selected( $ans_size ) ? 'is-selected' : ''; ?>">
										<?php echo esc_html( $ans_size ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
				<?php endif; ?>

				<!-- Availability Filter -->
				<div class="ans-filter-group">
					<button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-avail">
						<?php esc_html_e( 'Availability', 'ansclothes' ); ?>
						<svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</button>
					<div class="ans-filter-group__body" id="filter-avail">
						<ul class="ans-availability-list">
							<li>
								<label class="ans-checkbox">
									<input type="checkbox" name="instock" value="1"<?php checked( isset( $_GET['instock'] ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>>
									<span class="ans-checkbox__box"></span>
									<?php esc_html_e( 'In stock', 'ansclothes' ); ?>
								</label>
							</li>
							<li>
								<label class="ans-checkbox">
									<input type="checkbox" name="outofstock" value="1"<?php checked( isset( $_GET['outofstock'] ) ); // phpcs:ignore WordPress.Security.NonceVerification ?>>
									<span class="ans-checkbox__box"></span>
									<?php esc_html_e( 'Out of stock', 'ansclothes' ); ?>
								</label>
							</li>
						</ul>
					</div>
				</div>

			<?php endif; // WooCommerce ?>

		</aside>

		<!-- Product Grid -->
		<div class="ans-archive-main">

			<!-- Mobile filter button -->
			<button class="ans-filter-mobile-toggle" aria-expanded="false" aria-controls="ans-filter-sidebar">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M1 3h14M4 8h8M7 13h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
				<?php esc_html_e( 'Show Filters', 'ansclothes' ); ?>
			</button>

			<!-- Toolbar -->
			<div class="ans-archive-toolbar">
				<span class="ans-archive-count">
					<?php
					global $wp_query;
					$total = $wp_query->found_posts;
					printf(
						/* translators: %d: number of products */
						esc_html( _n( '%d item', '%d items', $total, 'ansclothes' ) ),
						esc_html( $total )
					);
					?>
				</span>
				<div class="ans-archive-toolbar__right">
					<?php if ( function_exists( 'woocommerce_catalog_ordering' ) ) : ?>
						<?php woocommerce_catalog_ordering(); ?>
					<?php endif; ?>
					<div class="ans-layout-toggle" role="group" aria-label="<?php esc_attr_e( 'Layout', 'ansclothes' ); ?>">
						<button class="ans-layout-btn is-active" data-cols="3" aria-label="<?php esc_attr_e( '3 columns', 'ansclothes' ); ?>" title="<?php esc_attr_e( '3 columns', 'ansclothes' ); ?>">
							<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="1" y="1" width="5" height="18" rx="1" fill="currentColor"/><rect x="7.5" y="1" width="5" height="18" rx="1" fill="currentColor"/><rect x="14" y="1" width="5" height="18" rx="1" fill="currentColor"/></svg>
						</button>
						<button class="ans-layout-btn" data-cols="4" aria-label="<?php esc_attr_e( '4 columns', 'ansclothes' ); ?>" title="<?php esc_attr_e( '4 columns', 'ansclothes' ); ?>">
							<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><rect x="0" y="0" width="4" height="20" rx="1" fill="currentColor"/><rect x="5.3" y="0" width="4" height="20" rx="1" fill="currentColor"/><rect x="10.6" y="0" width="4" height="20" rx="1" fill="currentColor"/><rect x="16" y="0" width="4" height="20" rx="1" fill="currentColor"/></svg>
						</button>
					</div>
				</div>
			</div>

			<!-- Products -->
			<div class="ans-archive-products" id="ans-products-grid" data-cols="3">
				<?php if ( have_posts() ) : ?>

				<ul class="products">
					<?php
					while ( have_posts() ) {
						the_post();
						wc_get_template_part( 'content', 'product' );
					}
					?>
				</ul>

					<?php do_action( 'woocommerce_after_shop_loop' ); ?>

				<?php else : ?>
					<?php do_action( 'woocommerce_no_products_found' ); ?>
				<?php endif; ?>
			</div>

		</div><!-- .ans-archive-main -->

	</div><!-- .ans-archive-shell -->

<?php else : ?>

	<!-- Single product / cart / checkout / account pages -->
	<div class="ans-woocommerce ans-wrap">
		<?php woocommerce_content(); ?>
	</div>

<?php endif; ?>

<?php
get_footer();
