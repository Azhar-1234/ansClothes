<?php
/**
 * Empty state.
 *
 * @package ANSClothes
 */

?>
<div class="ans-empty">
	<p>
		<?php
		if ( is_search() ) {
			printf(
				/* translators: %s: search query. */
				esc_html__( 'No results for "%s" — try different keywords.', 'ansclothes' ),
				esc_html( get_search_query() )
			);
		} else {
			esc_html_e( 'Nothing here yet — check back soon.', 'ansclothes' );
		}
		?>
	</p>
	<?php get_search_form(); ?>
</div>
