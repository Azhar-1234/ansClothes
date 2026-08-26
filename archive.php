<?php
/**
 * Category, tag, date and author archives.
 *
 * @package ANSClothes
 */

get_header();

$is_product_archive = is_post_type_archive( 'ans_product' )
    || is_tax( 'ans_product_cat' )
    || is_tax( 'ans_product_tag' );
?>

<?php if ( $is_product_archive ) : ?>

    <?php
    $archive_title = '';
    if ( is_post_type_archive( 'ans_product' ) ) {
        $archive_title = post_type_archive_title( 'ans_product', false );
    } elseif ( is_tax( 'ans_product_cat' ) || is_tax( 'ans_product_tag' ) ) {
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
        if ( is_tax( 'ans_product_cat' ) && ansclothes_option( 'category_desc_enable' ) ) {
            $term_desc = term_description();
            if ( $term_desc ) {
                echo '<div class="ans-archive-desc">' . wp_kses_post( $term_desc ) . '</div>';
            }
        }
        ?>
    </div>

    <div class="ans-archive-shell">
        <aside class="ans-filter-sidebar" id="ans-filter-sidebar">
            <h2 class="ans-filter-sidebar__title"><?php esc_html_e( 'Filters', 'ansclothes' ); ?></h2>

            <?php if ( class_exists( 'WooCommerce' ) ) : ?>

                <?php
                $min_price = isset( $_GET['min_price'] ) ? wc_clean( wp_unslash( $_GET['min_price'] ) ) : '';
                $max_price = isset( $_GET['max_price'] ) ? wc_clean( wp_unslash( $_GET['max_price'] ) ) : '';
                $price_max = (int) apply_filters( 'woocommerce_price_filter_widget_max_amount',
                    (float) WC()->query->get_main_query()->get( 'wc_query' ) !== false
                        ? wp_cache_get( 'wc_max_price_filter' )
                        : 0
                );
                ?>

                <div class="ans-filter-group">
                    <button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-price">
                        <?php esc_html_e( 'Price', 'ansclothes' ); ?>
                        <svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="ans-filter-group__body" id="filter-price">
                        <?php the_widget( 'WC_Widget_Price_Filter', array( 'title' => '' ) ); ?>
                    </div>
                </div>

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

                <?php
                $size_attr = wc_get_attribute_taxonomies();
                $has_pa_size = false;
                if ( $size_attr ) {
                    foreach ( $size_attr as $attr ) {
                        if ( in_array( $attr->attribute_name, array( 'size', 'pa_size', 'Size' ), true ) || stripos( $attr->attribute_name, 'size' ) !== false ) {
                            $has_pa_size = true;
                            break;
                        }
                    }
                }
                if ( $has_pa_size ) :
                ?>
                <div class="ans-filter-group">
                    <button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-size">
                        <?php esc_html_e( 'Size', 'ansclothes' ); ?>
                        <svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="ans-filter-group__body" id="filter-size">
                        <?php
                        the_widget(
                            'WC_Widget_Layered_Nav',
                            array(
                                'title'            => '',
                                'attribute'        => 'pa_size',
                                'query_type'       => 'or',
                                'display_type'     => 'list',
                                'show_counts'      => 0,
                            )
                        );
                        ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="ans-filter-group">
                    <button class="ans-filter-group__toggle" aria-expanded="true" aria-controls="filter-avail">
                        <?php esc_html_e( 'Availability', 'ansclothes' ); ?>
                        <svg class="ans-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="ans-filter-group__body" id="filter-avail">
                        <ul class="ans-availability-list">
                            <li>
                                <label class="ans-checkbox">
                                    <input type="checkbox" name="instock" value="1"<?php checked( isset( $_GET['instock'] ) ); ?>>
                                    <span class="ans-checkbox__box"></span>
                                    <?php esc_html_e( 'In stock', 'ansclothes' ); ?>
                                </label>
                            </li>
                            <li>
                                <label class="ans-checkbox">
                                    <input type="checkbox" name="outofstock" value="1"<?php checked( isset( $_GET['outofstock'] ) ); ?>>
                                    <span class="ans-checkbox__box"></span>
                                    <?php esc_html_e( 'Out of stock', 'ansclothes' ); ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

            <?php endif; ?>
        </aside>

        <div class="ans-archive-main">
            <button class="ans-filter-mobile-toggle" aria-expanded="false" aria-controls="ans-filter-sidebar">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M1 3h14M4 8h8M7 13h2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <?php esc_html_e( 'Show Filters', 'ansclothes' ); ?>
            </button>

            <div class="ans-archive-toolbar">
                <span class="ans-archive-count">
                    <?php
                    global $wp_query;
                    $total = $wp_query->found_posts;
                    printf(
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

            <div class="ans-archive-products" id="ans-products-grid" data-cols="3">
                <?php if ( have_posts() ) : ?>
                    <ul class="products">
                        <?php
                        while ( have_posts() ) {
                            the_post();
                            if ( get_post_type() === 'ans_product' ) {
                                $card = ansclothes_get_product_card_args();
                                $badge = $card['badge'] ? '<span class="ans-pcard__badge">' . esc_html( $card['badge'] ) . '</span>' : '';
                                $image = $card['image'] ? '<img src="' . esc_url( $card['image'] ) . '" alt="' . esc_attr( $card['name'] ) . '" loading="lazy" class="ans-pcard__img">' : '<span class="ans-card__placeholder">' . esc_html( $card['name'] ) . '</span>';
                                $price = '';
                                if ( '' !== $card['price_html'] ) {
                                    $price = '<div class="ans-pcard__price">' . wp_kses_post( $card['price_html'] ) . '</div>';
                                } elseif ( '' !== $card['price'] ) {
                                    $price = '<div class="ans-pcard__price">' . esc_html( ansclothes_currency() . ' ' . $card['price'] ) . '</div>';
                                }
                                ?>
                                <li class="ans-pcard">
                                    <div class="ans-pcard__media">
                                        <a href="<?php echo esc_url( $card['url'] ); ?>" aria-label="<?php echo esc_attr( $card['name'] ); ?>">
                                            <?php echo $image; ?>
                                        </a>
                                        <?php echo $badge; ?>
                                    </div>
                                    <div class="ans-pcard__info">
                                        <h3 class="ans-pcard__name">
                                            <a href="<?php echo esc_url( $card['url'] ); ?>"><?php echo esc_html( $card['name'] ); ?></a>
                                        </h3>
                                        <?php echo $price; ?>
                                    </div>
                                </li>
                                <?php
                            } else {
                                get_template_part( 'template-parts/content', get_post_type() );
                            }
                        }
                        ?>
                    </ul>
                    <?php ansclothes_pagination(); ?>
                <?php else : ?>
                    <?php get_template_part( 'template-parts/content', 'none' ); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php else : ?>

    <div class="ans-page-header">
        <?php
        the_archive_title( '<h1>', '</h1>' );
        the_archive_description( '<div class="ans-meta">', '</div>' );
        ?>
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

<?php endif; ?>

<?php
get_footer();
