<?php
/**
 * Search form.
 *
 * @package ANSClothes
 */

?>
<form role="search" method="get" class="ans-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="ans-search-field"><?php esc_html_e( 'Search for:', 'ansclothes' ); ?></label>
	<input type="search" id="ans-search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search products…', 'ansclothes' ); ?>">
	<button type="submit" class="ans-btn"><?php esc_html_e( 'Search', 'ansclothes' ); ?></button>
</form>
