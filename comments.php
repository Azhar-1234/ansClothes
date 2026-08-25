<?php
/**
 * Comments.
 *
 * @package ANSClothes
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="ans-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="ans-meta">
			<?php
			printf(
				/* translators: %s: comment count. */
				esc_html( _n( '%s Comment', '%s Comments', get_comments_number(), 'ansclothes' ) ),
				esc_html( number_format_i18n( get_comments_number() ) )
			);
			?>
		</h2>

		<ol>
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 44,
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( 'Prev', 'ansclothes' ),
				'next_text' => esc_html__( 'Next', 'ansclothes' ),
				'class'     => 'ans-pagination',
			)
		);
		?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'        => esc_html__( 'Leave a comment', 'ansclothes' ),
			'class_submit'       => 'ans-btn',
			'comment_notes_before' => '',
		)
	);
	?>
</div>
