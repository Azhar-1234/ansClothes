<?php
/**
 * Homepage category-wise products.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'category_products_enable' ) ) {
    return;
}

$slugs_setting = ansclothes_option( 'category_products_slugs' );
$slugs_array   = array();
if ( ! empty( $slugs_setting ) ) {
    $slugs_array = array_filter( array_map( 'trim', explode( ',', $slugs_setting ) ) );
}

$count = absint( ansclothes_option( 'category_products_count' ) );
if ( ! $count ) {
    $count = 8;
}

$ansclothes_taxonomy = ansclothes_has_woocommerce() ? 'product_cat' : 'ans_product_cat';
$args = array(
    'taxonomy'   => $ansclothes_taxonomy,
    'hide_empty' => true,
    'number'     => 6,
    'orderby'    => 'menu_order',
    'order'      => 'ASC',
);

if ( ! empty( $slugs_array ) ) {
    $args['slug']   = $slugs_array;
    $args['number'] = 0; // unlimited since we specified slugs
}

$ansclothes_terms = get_terms( $args );

if ( is_wp_error( $ansclothes_terms ) || empty( $ansclothes_terms ) ) {
    return;
}

// Re-order terms to match the user's input order if slugs were provided
if ( ! empty( $slugs_array ) ) {
    $ordered_terms = array();
    foreach ( $slugs_array as $slug ) {
        foreach ( $ansclothes_terms as $term ) {
            if ( $term->slug === $slug ) {
                $ordered_terms[] = $term;
                break;
            }
        }
    }
    $ansclothes_terms = $ordered_terms;
}
?>

<style>
.ans-category-products {
    padding: 20px 0 60px;
}
.ans-category-product-container {
    padding: 0 4%; /* Halka gap left and right */
    max-width: 100%;
}
.ans-home-product__media {
    position: relative;
    aspect-ratio: 3 / 4; /* Makes images uniform size */
    overflow: hidden;
    background: #f9f9f9;
}
.ans-home-product__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.ans-home-product__media::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.15);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}
.ans-home-product__media:hover::after {
    opacity: 1;
}
.ans-home-product__actions {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -40%);
    width: 85%;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 0;
    background: transparent;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
    z-index: 10;
}
.ans-home-product__media:hover .ans-home-product__actions {
    transform: translate(-50%, -50%);
    opacity: 1;
    visibility: visible;
}
.ans-home-product__actions a {
    display: block;
    width: 100%;
    text-align: center;
    padding: 12px 20px;
    background: #ffffff;
    color: #1a1a1a !important;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px; /* Pill shape */
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.ans-home-product__actions a:hover {
    background: #1a1a1a;
    color: #ffffff !important;
}
</style>

<section class="ans-category-products">
    <?php foreach ( $ansclothes_terms as $ansclothes_term ) : ?>
        <?php
        $ansclothes_query_args = array(
            'post_type'           => ansclothes_has_woocommerce() ? 'product' : 'ans_product',
            'post_status'         => 'publish',
            'posts_per_page'      => $count,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'tax_query'           => array(
                array(
                    'taxonomy' => $ansclothes_taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $ansclothes_term->term_id,
                ),
            ),
        );

        $ansclothes_products = new WP_Query( $ansclothes_query_args );

        if ( ! $ansclothes_products->have_posts() ) {
            continue;
        }
        ?>

        <div class="ans-category-product-group ans-category-product-container" style="margin-bottom: 60px;">
            <div class="ans-category-product-group__head" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid #eae7e1;">
                <h2 style="font-size: 26px; font-weight: 500; margin: 0; color: #1a1a1a;"><?php echo esc_html( $ansclothes_term->name ); ?></h2>
                <a href="<?php echo esc_url( get_term_link( $ansclothes_term ) ); ?>" style="font-size: 13px; text-transform: uppercase; font-weight: 600; color: #d25a40; text-decoration: none; letter-spacing: 0.5px;"><?php esc_html_e( 'View All &rarr;', 'ansclothes' ); ?></a>
            </div>

            <div class="ans-grid-4">
                <?php
                while ( $ansclothes_products->have_posts() ) :
                    $ansclothes_products->the_post();
                    ansclothes_home_product_card( ansclothes_get_product_card_args() );
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
