<?php
/**
 * Lookbook mosaic.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'lookbook_enable' ) ) {
	return;
}

$ansclothes_looks = array(
	array( 'image' => ansclothes_option( 'look1_image' ), 'label' => ansclothes_option( 'look1_label' ) ),
	array( 'image' => ansclothes_option( 'look2_image' ), 'label' => ansclothes_option( 'look2_label' ) ),
	array( 'image' => ansclothes_option( 'look3_image' ), 'label' => ansclothes_option( 'look3_label' ) ),
);
?>

<section id="lookbook" class="ans-lookbook ans-section ans-reveal">
	<div class="ans-wrap">
		<div class="ans-section-head">
			<h2><?php ansclothes_heading( 'lookbook_heading' ); ?></h2>
			<span class="ans-meta"><?php echo esc_html( ansclothes_option( 'lookbook_meta' ) ); ?></span>
		</div>

		<div class="ans-lookbook__grid">
			<?php foreach ( $ansclothes_looks as $ansclothes_look ) : ?>
				<div class="ans-look">
					<?php if ( $ansclothes_look['image'] ) : ?>
						<img src="<?php echo esc_url( $ansclothes_look['image'] ); ?>" alt="<?php echo esc_attr( $ansclothes_look['label'] ); ?>" loading="lazy">
					<?php else : ?>
						<span class="ans-card__placeholder"><?php echo esc_html( $ansclothes_look['label'] ); ?></span>
					<?php endif; ?>

					<?php if ( $ansclothes_look['label'] ) : ?>
						<span class="ans-look__tag"><?php echo esc_html( $ansclothes_look['label'] ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>

			<div class="ans-lookbook__note">
				<span class="ans-eyebrow"><?php echo esc_html( ansclothes_option( 'lookbook_note_eyebrow' ) ); ?></span>
				<p><?php echo esc_html( ansclothes_option( 'lookbook_note_text' ) ); ?></p>

				<?php if ( ansclothes_option( 'lookbook_link_text' ) ) : ?>
					<a class="ans-link-underline" href="<?php echo esc_url( ansclothes_option( 'lookbook_link_url' ) ); ?>"><?php echo esc_html( ansclothes_option( 'lookbook_link_text' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
