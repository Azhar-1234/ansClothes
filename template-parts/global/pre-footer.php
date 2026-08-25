<?php
/**
 * Newsletter before the footer.
 *
 * @package ANSClothes
 */
?>

<section class="ans-newsletter" aria-label="<?php esc_attr_e( 'Newsletter', 'ansclothes' ); ?>">
	<h2><?php esc_html_e( 'Join the ANS Circle', 'ansclothes' ); ?></h2>
	<p><?php esc_html_e( 'New drops, styling notes, and member-only offers - no spam.', 'ansclothes' ); ?></p>

	<form class="ans-newsletter__form" action="#" method="post">
		<label class="screen-reader-text" for="ans-newsletter-email"><?php esc_html_e( 'Email address', 'ansclothes' ); ?></label>
		<input id="ans-newsletter-email" type="email" name="email" placeholder="<?php esc_attr_e( 'Your email address', 'ansclothes' ); ?>" required>
		<button type="submit"><?php esc_html_e( 'Subscribe', 'ansclothes' ); ?></button>
	</form>
</section>
