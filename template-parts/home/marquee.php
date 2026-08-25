<?php
/**
 * Scrolling marquee band.
 *
 * @package ANSClothes
 */

$ansclothes_items = ansclothes_lines( 'marquee_items' );

if ( ! $ansclothes_items ) {
	return;
}

// Duplicated so the -50% keyframe loops seamlessly.
$ansclothes_items = array_merge( $ansclothes_items, $ansclothes_items );
?>

<div class="ans-marquee" aria-hidden="true">
	<div class="ans-marquee__track">
		<?php foreach ( $ansclothes_items as $ansclothes_item ) : ?>
			<span class="ans-marquee__item"><?php echo esc_html( $ansclothes_item ); ?></span>
		<?php endforeach; ?>
	</div>
</div>
