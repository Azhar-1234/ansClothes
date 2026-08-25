<?php
/**
 * Custom sidebar Size filter for the shop/category archive.
 *
 * Product "size" data isn't a global WooCommerce attribute on this site —
 * it's stored as a local product attribute, and inconsistently named
 * ("size" on some products, "length" on others). WC_Widget_Layered_Nav only
 * works with global (taxonomy) attributes, so it can't be used here. These
 * helpers read both local attribute keys directly and filter in PHP.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Local attribute keys that represent a product's size.
 *
 * @return string[]
 */
function ansclothes_size_attribute_keys() {
	return array( 'size', 'length' );
}

/**
 * Get a product's size values (from whichever local attribute holds them).
 *
 * @param WC_Product $product Product.
 * @return string[] Upper-cased, trimmed size tokens.
 */
function ansclothes_get_product_sizes( $product ) {
	foreach ( ansclothes_size_attribute_keys() as $key ) {
		$value = $product->get_attribute( $key );
		if ( $value ) {
			return array_filter( array_map(
				static function ( $s ) {
					return strtoupper( trim( $s ) );
				},
				preg_split( '/\s*[,|]\s*/', $value )
			) );
		}
	}
	return array();
}

/**
 * Product IDs for the current archive context (category/tag/shop), ignoring
 * pagination and the size filter itself — used both to build the facet list
 * and to apply the size filter.
 *
 * @return int[]
 */
function ansclothes_current_archive_product_ids() {
	$args = array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	);

	if ( is_product_category() || is_product_tag() ) {
		$queried = get_queried_object();
		if ( $queried instanceof WP_Term ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => $queried->taxonomy,
					'field'    => 'term_id',
					'terms'    => $queried->term_id,
				),
			);
		}
	}

	return get_posts( $args );
}

/**
 * Distinct, sensibly-ordered size values available in the current archive.
 *
 * @return string[]
 */
function ansclothes_get_available_sizes() {
	$order = array( 'XS', 'S', 'M', 'L', 'XL', 'XXL', '2XL', '3XL', '4XL' );
	$sizes = array();

	foreach ( ansclothes_current_archive_product_ids() as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product ) {
			continue;
		}
		foreach ( ansclothes_get_product_sizes( $product ) as $size ) {
			$sizes[ $size ] = true;
		}
	}

	$sizes = array_keys( $sizes );
	usort(
		$sizes,
		static function ( $a, $b ) use ( $order ) {
			$ia = array_search( $a, $order, true );
			$ib = array_search( $b, $order, true );
			$ia = false === $ia ? 999 : $ia;
			$ib = false === $ib ? 999 : $ib;
			return $ia <=> $ib;
		}
	);

	return $sizes;
}

/**
 * Filter the main archive query down to products whose size matches the
 * `filter_size` query var.
 *
 * @param WP_Query $query Main query.
 */
function ansclothes_apply_size_filter( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! ( $query->is_post_type_archive( 'product' ) || $query->is_tax( 'product_cat' ) || $query->is_tax( 'product_tag' ) ) ) {
		return;
	}

	if ( empty( $_GET['filter_size'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	$selected = array_filter( array_map(
		static function ( $s ) {
			return strtoupper( trim( $s ) );
		},
		explode( ',', wc_clean( wp_unslash( $_GET['filter_size'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification
	) );

	if ( ! $selected ) {
		return;
	}

	$matched = array();

	foreach ( ansclothes_current_archive_product_ids() as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product ) {
			continue;
		}
		if ( array_intersect( $selected, ansclothes_get_product_sizes( $product ) ) ) {
			$matched[] = $id;
		}
	}

	$query->set( 'post__in', $matched ? $matched : array( 0 ) );
}
add_action( 'pre_get_posts', 'ansclothes_apply_size_filter' );

/**
 * Build the URL for toggling a size on/off in the current query string.
 *
 * @param string $size Size token.
 * @return string
 */
function ansclothes_size_filter_url( $size ) {
	$selected = array();
	if ( ! empty( $_GET['filter_size'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$selected = array_filter( array_map( 'trim', explode( ',', wc_clean( wp_unslash( $_GET['filter_size'] ) ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	$key = array_search( strtoupper( $size ), array_map( 'strtoupper', $selected ), true );

	if ( false !== $key ) {
		unset( $selected[ $key ] );
	} else {
		$selected[] = $size;
	}

	if ( ! $selected ) {
		return remove_query_arg( 'filter_size' );
	}

	return add_query_arg( 'filter_size', implode( ',', $selected ) );
}

/**
 * Whether a size is currently selected in the query string.
 *
 * @param string $size Size token.
 * @return bool
 */
function ansclothes_is_size_selected( $size ) {
	if ( empty( $_GET['filter_size'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}
	$selected = array_map( 'strtoupper', array_map( 'trim', explode( ',', wc_clean( wp_unslash( $_GET['filter_size'] ) ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	return in_array( strtoupper( $size ), $selected, true );
}
