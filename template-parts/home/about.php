<?php
/**
 * About the brand + coming soon chips.
 *
 * @package ANSClothes
 */

if ( ! ansclothes_option( 'about_enable' ) ) {
	return;
}

$ansclothes_coming = ansclothes_option( 'coming_enable' ) ? ansclothes_lines( 'coming_items' ) : array();
?>

<section id="about" class="ans-about">
	<?php if ( ansclothes_option( 'about_eyebrow' ) ) : ?>
		<span class="ans-eyebrow"><?php echo esc_html( ansclothes_option( 'about_eyebrow' ) ); ?></span>
	<?php endif; ?>

	<blockquote>
		<p><?php echo esc_html( ansclothes_option( 'about_quote' ) ); ?></p>
	</blockquote>

	<?php if ( $ansclothes_coming ) : ?>
		<div id="coming" class="ans-coming">
			<?php foreach ( $ansclothes_coming as $ansclothes_item ) : ?>
				<span>
					<?php
					printf(
						/* translators: %s: product category name. */
						esc_html__( '%s — Coming Soon', 'ansclothes' ),
						esc_html( $ansclothes_item )
					);
					?>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>
