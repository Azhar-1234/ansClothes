<?php
/**
 * Three brand promises.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'promises_enable' ) ) {
	return;
}

$ansclothes_promises = array();

for ( $i = 1; $i <= 3; $i++ ) {
	$title = ansclothes_option( 'promise' . $i . '_title' );

	if ( $title ) {
		$ansclothes_promises[] = array(
			'title' => $title,
			'text'  => ansclothes_option( 'promise' . $i . '_text' ),
		);
	}
}

if ( ! $ansclothes_promises ) {
	return;
}
?>

<section class="ans-promises ans-reveal">
	<?php foreach ( $ansclothes_promises as $ansclothes_promise ) : ?>
		<div class="ans-promise">
			<strong><?php echo esc_html( $ansclothes_promise['title'] ); ?></strong>
			<span><?php echo esc_html( $ansclothes_promise['text'] ); ?></span>
		</div>
	<?php endforeach; ?>
</section>
