<?php
/**
 * Homepage hero.
 *
 * @package ANSClothes
 */

$ansclothes_slides = array();

/* Slots 1 & 2 fall back to the original design's placeholder banners so the
   hero never looks empty before the shop uploads its own images; slots 3+
   are purely optional extra slides and are simply skipped when unset. */
$ansclothes_fallback_slides = array(
    1 => 'https://arjobd.com/uploads/Banner/web-coverpng.webp',
    2 => 'https://arjobd.com/uploads/Banner/cover333.webp',
);

for ( $ansclothes_i = 1; $ansclothes_i <= ANSCLOTHES_SLIDER_SLOTS; $ansclothes_i++ ) {
    $slide_img = ansclothes_option( "slider_{$ansclothes_i}_image" );

    if ( ! $slide_img && ! isset( $ansclothes_fallback_slides[ $ansclothes_i ] ) ) {
        continue;
    }

    $slide_url = ansclothes_option( "slider_{$ansclothes_i}_url" );

    $ansclothes_slides[] = array(
        'image'    => $slide_img ? $slide_img : $ansclothes_fallback_slides[ $ansclothes_i ],
        'url'      => $slide_url ? $slide_url : ansclothes_shop_url(),
        /* translators: %d: slide number. */
        'alt'      => sprintf( __( 'Slide %d', 'ansclothes' ), $ansclothes_i ),
        'position' => ansclothes_option( "slider_{$ansclothes_i}_position" ),
    );
}
?>

<section class="ans-hero" aria-label="<?php esc_attr_e( 'Featured collections', 'ansclothes' ); ?>">
	<div class="ans-hero__slides">
		<?php foreach ( $ansclothes_slides as $ansclothes_index => $ansclothes_slide ) : ?>
			<a class="ans-hero__slide<?php echo 0 === $ansclothes_index ? ' is-active' : ''; ?>" href="<?php echo esc_url( $ansclothes_slide['url'] ); ?>">
				<img src="<?php echo esc_url( $ansclothes_slide['image'] ); ?>" alt="<?php echo esc_attr( $ansclothes_slide['alt'] ); ?>" style="object-position: <?php echo esc_attr( $ansclothes_slide['position'] ); ?>;">
			</a>
		<?php endforeach; ?>
	</div>

	<div class="ans-hero__dots" aria-hidden="true">
		<?php foreach ( $ansclothes_slides as $ansclothes_index => $ansclothes_slide ) : ?>
			<span class="<?php echo 0 === $ansclothes_index ? 'is-active' : ''; ?>"></span>
		<?php endforeach; ?>
	</div>
</section>
