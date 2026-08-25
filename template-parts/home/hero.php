<?php
/**
 * Homepage hero.
 *
 * @package ANSClothes
 */

$ansclothes_slides = array();

$slide1_img = ansclothes_option( 'slider_1_image' );
$slide1_url = ansclothes_option( 'slider_1_url' );
if ( $slide1_img ) {
    $ansclothes_slides[] = array(
        'image' => $slide1_img,
        'url'   => $slide1_url ? $slide1_url : ansclothes_shop_url(),
        'alt'   => __( 'Slide 1', 'ansclothes' ),
    );
} else {
    $ansclothes_slides[] = array(
        'image' => 'https://arjobd.com/uploads/Banner/web-coverpng.webp',
        'url'   => ansclothes_shop_url(),
        'alt'   => __( 'ANSClothes cover banner', 'ansclothes' ),
    );
}

$slide2_img = ansclothes_option( 'slider_2_image' );
$slide2_url = ansclothes_option( 'slider_2_url' );
if ( $slide2_img ) {
    $ansclothes_slides[] = array(
        'image' => $slide2_img,
        'url'   => $slide2_url ? $slide2_url : ansclothes_shop_url(),
        'alt'   => __( 'Slide 2', 'ansclothes' ),
    );
} else {
    $ansclothes_slides[] = array(
        'image' => 'https://arjobd.com/uploads/Banner/cover333.webp',
        'url'   => ansclothes_shop_url(),
        'alt'   => __( 'ANSClothes collection banner', 'ansclothes' ),
    );
}
?>

<section class="ans-hero" aria-label="<?php esc_attr_e( 'Featured collections', 'ansclothes' ); ?>">
	<div class="ans-hero__slides">
		<?php foreach ( $ansclothes_slides as $ansclothes_index => $ansclothes_slide ) : ?>
			<a class="ans-hero__slide<?php echo 0 === $ansclothes_index ? ' is-active' : ''; ?>" href="<?php echo esc_url( $ansclothes_slide['url'] ); ?>">
				<img src="<?php echo esc_url( $ansclothes_slide['image'] ); ?>" alt="<?php echo esc_attr( $ansclothes_slide['alt'] ); ?>">
			</a>
		<?php endforeach; ?>
	</div>

	<div class="ans-hero__dots" aria-hidden="true">
		<?php foreach ( $ansclothes_slides as $ansclothes_index => $ansclothes_slide ) : ?>
			<span class="<?php echo 0 === $ansclothes_index ? 'is-active' : ''; ?>"></span>
		<?php endforeach; ?>
	</div>
</section>
