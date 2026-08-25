<?php
/**
 * Post card used in archives.
 *
 * @package ANSClothes
 */

?>
<article <?php post_class( 'ans-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="ans-post-card__media" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'ansclothes-card' ); ?>
		</a>
	<?php endif; ?>

	<span class="ans-date"><?php echo esc_html( get_the_date() ); ?></span>

	<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

	<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
</article>
