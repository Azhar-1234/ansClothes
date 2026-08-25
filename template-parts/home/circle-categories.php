<?php
/**
 * Circular categories section.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'circle_categories_enable' ) ) {
    return;
}

$count = absint( ansclothes_option( 'circle_categories_count' ) );
if ( ! $count ) {
    $count = 4;
}

$slugs_setting = ansclothes_option( 'circle_categories_slugs' );
$slugs_array   = array();
if ( ! empty( $slugs_setting ) ) {
    $slugs_array = array_filter( array_map( 'trim', explode( ',', $slugs_setting ) ) );
}

$exclude_setting = ansclothes_option( 'circle_categories_exclude' );
$exclude_slugs   = array();
if ( ! empty( $exclude_setting ) ) {
    $exclude_slugs = array_filter( array_map( 'trim', explode( ',', $exclude_setting ) ) );
}

$args = array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'number'     => $count,
);

if ( ! empty( $slugs_array ) ) {
    $args['slug'] = $slugs_array;
} else {
    $args['parent'] = 0;
}

if ( ! empty( $exclude_slugs ) ) {
    $exclude_ids = array();
    foreach ( $exclude_slugs as $eslug ) {
        $term_obj = get_term_by( 'slug', $eslug, 'product_cat' );
        if ( $term_obj ) {
            $exclude_ids[] = $term_obj->term_id;
        }
    }
    if ( ! empty( $exclude_ids ) ) {
        $args['exclude'] = $exclude_ids;
    }
}

$terms = get_terms( $args );

if ( empty( $terms ) || is_wp_error( $terms ) ) {
    return;
}
?>

<style>
.ans-circle-categories-section {
    padding: 60px 20px;
}
.ans-circle-categories-list {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 60px;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto;
}
.ans-circle-category-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #1a1a1a;
    transition: transform 0.3s ease;
}
.ans-circle-category-item:hover {
    transform: translateY(-5px);
}
.ans-circle-category-img {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.ans-circle-category-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.ans-circle-category-title {
    font-size: 18px;
    font-weight: 600;
    margin: 0;
    text-align: center;
    letter-spacing: 0.5px;
}
@media (max-width: 768px) {
    .ans-circle-categories-list {
        gap: 30px;
    }
    .ans-circle-category-img {
        width: 100px;
        height: 100px;
        margin-bottom: 12px;
    }
    .ans-circle-category-title {
        font-size: 15px;
    }
}
</style>

<section class="ans-circle-categories-section">
    <div class="ans-circle-categories-list">
        <?php foreach ( $terms as $term ) : 
            $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
            $image_url    = '';
            if ( $thumbnail_id ) {
                $image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
            } else {
                if ( function_exists( 'wc_placeholder_img_src' ) ) {
                    $image_url = wc_placeholder_img_src();
                } else {
                    $image_url = get_template_directory_uri() . '/assets/images/placeholder.png'; // Fallback
                }
            }
        ?>
        <a href="<?php echo esc_url( get_term_link( $term ) ); ?>" class="ans-circle-category-item">
            <div class="ans-circle-category-img">
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $term->name ); ?>">
            </div>
            <h3 class="ans-circle-category-title"><?php echo esc_html( $term->name ); ?></h3>
        </a>
        <?php endforeach; ?>
    </div>
</section>
