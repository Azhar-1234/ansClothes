<?php
/**
 * "The Fit" editorial band.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'editorial_enable' ) ) {
	return;
}

$ansclothes_image = ansclothes_option( 'editorial_image' );

$ansclothes_stats = array(
	array( 'value' => ansclothes_option( 'editorial_stat1_value' ), 'label' => ansclothes_option( 'editorial_stat1_label' ) ),
	array( 'value' => ansclothes_option( 'editorial_stat2_value' ), 'label' => ansclothes_option( 'editorial_stat2_label' ) ),
	array( 'value' => ansclothes_option( 'editorial_stat3_value' ), 'label' => ansclothes_option( 'editorial_stat3_label' ) ),
);
?>

<section id="editorial" class="ans-editorial">
	<div class="ans-editorial__media">
		<?php if ( $ansclothes_image ) : ?>
			<img src="<?php echo esc_url( $ansclothes_image ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( ansclothes_option( 'editorial_heading' ) ) ); ?>" loading="lazy">
		<?php endif; ?>
	</div>

	<div class="ans-editorial__text">
		<span class="ans-eyebrow"><?php echo esc_html( ansclothes_option( 'editorial_eyebrow' ) ); ?></span>
		<h2><?php ansclothes_heading( 'editorial_heading' ); ?></h2>
		<p><?php echo esc_html( ansclothes_option( 'editorial_text' ) ); ?></p>

		<div class="ans-stats">
			<?php foreach ( $ansclothes_stats as $ansclothes_stat ) : ?>
				<?php if ( $ansclothes_stat['value'] ) : ?>
					<div class="ans-stat">
						<strong><?php echo esc_html( $ansclothes_stat['value'] ); ?></strong>
						<span><?php echo esc_html( $ansclothes_stat['label'] ); ?></span>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>

		<?php if ( ansclothes_option( 'editorial_btn_text' ) ) : ?>
			<a class="ans-btn ans-btn--light" style="align-self:flex-start" href="<?php echo esc_url( ansclothes_option( 'editorial_btn_url' ) ); ?>"><?php echo esc_html( ansclothes_option( 'editorial_btn_text' ) ); ?></a>
		<?php endif; ?>
	</div>
</section>
