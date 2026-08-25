<?php
/**
 * Size Charts — a "Size Chart" post type (Products → Size Charts) with rows
 * of Size / Chest / Length, assignable to specific products and/or product
 * categories. The single product page shows a "Size Chart Guide" button
 * next to the size selector when a chart matches; clicking it opens the
 * matching chart in a modal.
 *
 * @package ANSClothes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Size Chart post type, nested under Products.
 */
function ansclothes_register_size_chart_type() {
	register_post_type(
		'ans_size_chart',
		array(
			'labels'       => array(
				'name'               => __( 'Size Charts', 'ansclothes' ),
				'singular_name'      => __( 'Size Chart', 'ansclothes' ),
				'add_new_item'       => __( 'Add Size Chart', 'ansclothes' ),
				'edit_item'          => __( 'Edit Size Chart', 'ansclothes' ),
				'all_items'          => __( 'Size Charts', 'ansclothes' ),
				'search_items'       => __( 'Search Size Charts', 'ansclothes' ),
				'not_found'          => __( 'No size charts found.', 'ansclothes' ),
				'not_found_in_trash' => __( 'No size charts found in Trash.', 'ansclothes' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=product',
			'supports'     => array( 'title' ),
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'ansclothes_register_size_chart_type' );

/**
 * Meta boxes: chart rows, and what the chart applies to.
 */
function ansclothes_size_chart_meta_boxes() {
	add_meta_box(
		'ansclothes_size_chart_rows',
		__( 'Measurements', 'ansclothes' ),
		'ansclothes_size_chart_rows_box',
		'ans_size_chart',
		'normal',
		'high'
	);

	add_meta_box(
		'ansclothes_size_chart_scope',
		__( 'Apply To', 'ansclothes' ),
		'ansclothes_size_chart_scope_box',
		'ans_size_chart',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'ansclothes_size_chart_meta_boxes' );

/**
 * Render the measurement rows table (Size / Chest / Length).
 *
 * @param WP_Post $post Current post.
 */
function ansclothes_size_chart_rows_box( $post ) {
	wp_nonce_field( 'ansclothes_save_size_chart', 'ansclothes_size_chart_nonce' );

	$rows = get_post_meta( $post->ID, '_ans_size_chart_rows', true );

	if ( ! is_array( $rows ) || empty( $rows ) ) {
		$rows = array( array( 'size' => '', 'chest' => '', 'length' => '' ) );
	}
	?>
	<table class="widefat ans-size-chart-rows" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Size', 'ansclothes' ); ?></th>
				<th><?php esc_html_e( 'Chest (in)', 'ansclothes' ); ?></th>
				<th><?php esc_html_e( 'Length (in)', 'ansclothes' ); ?></th>
				<th style="width:40px;"></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr>
					<td><input type="text" name="ans_size_chart_size[]" value="<?php echo esc_attr( $row['size'] ?? '' ); ?>" class="widefat" placeholder="M"></td>
					<td><input type="text" name="ans_size_chart_chest[]" value="<?php echo esc_attr( $row['chest'] ?? '' ); ?>" class="widefat" placeholder="38"></td>
					<td><input type="text" name="ans_size_chart_length[]" value="<?php echo esc_attr( $row['length'] ?? '' ); ?>" class="widefat" placeholder="27"></td>
					<td><button type="button" class="button-link ans-size-chart-remove-row" aria-label="<?php esc_attr_e( 'Remove row', 'ansclothes' ); ?>">&times;</button></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<p><button type="button" class="button ans-size-chart-add-row"><?php esc_html_e( '+ Add row', 'ansclothes' ); ?></button></p>
	<template id="ans-size-chart-row-template">
		<tr>
			<td><input type="text" name="ans_size_chart_size[]" value="" class="widefat" placeholder="M"></td>
			<td><input type="text" name="ans_size_chart_chest[]" value="" class="widefat" placeholder="38"></td>
			<td><input type="text" name="ans_size_chart_length[]" value="" class="widefat" placeholder="27"></td>
			<td><button type="button" class="button-link ans-size-chart-remove-row" aria-label="<?php esc_attr_e( 'Remove row', 'ansclothes' ); ?>">&times;</button></td>
		</tr>
	</template>
	<?php
}

/**
 * Render the category / product assignment box.
 *
 * @param WP_Post $post Current post.
 */
function ansclothes_size_chart_scope_box( $post ) {
	$selected_categories = array_map( 'absint', get_post_meta( $post->ID, '_ans_size_chart_category', false ) );
	$selected_products    = array_map( 'absint', get_post_meta( $post->ID, '_ans_size_chart_product', false ) );

	$categories = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		)
	);
	?>
	<p class="description"><?php esc_html_e( 'A product-specific chart below always wins over a category match.', 'ansclothes' ); ?></p>

	<p><strong><?php esc_html_e( 'Categories', 'ansclothes' ); ?></strong></p>
	<ul class="ans-size-chart-categories" style="max-height:180px;overflow:auto;margin:0 0 16px;">
		<?php if ( empty( $categories ) || is_wp_error( $categories ) ) : ?>
			<li><?php esc_html_e( 'No product categories yet.', 'ansclothes' ); ?></li>
		<?php else : ?>
			<?php foreach ( $categories as $category ) : ?>
				<li>
					<label>
						<input type="checkbox" name="ans_size_chart_categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( in_array( (int) $category->term_id, $selected_categories, true ) ); ?>>
						<?php echo esc_html( $category->name ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>

	<p><strong><?php esc_html_e( 'Specific products', 'ansclothes' ); ?></strong></p>
	<select class="wc-product-search" multiple="multiple" style="width:100%;" name="ans_size_chart_products[]" data-placeholder="<?php esc_attr_e( 'Search for products…', 'ansclothes' ); ?>" data-action="woocommerce_json_search_products_and_variations">
		<?php foreach ( $selected_products as $product_id ) : ?>
			<?php $product = wc_get_product( $product_id ); ?>
			<?php if ( $product ) : ?>
				<option value="<?php echo esc_attr( $product_id ); ?>" selected="selected"><?php echo esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ); ?></option>
			<?php endif; ?>
		<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Save the chart rows and assignment.
 *
 * @param int $post_id Post ID.
 */
function ansclothes_save_size_chart( $post_id ) {
	if ( ! isset( $_POST['ansclothes_size_chart_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ansclothes_size_chart_nonce'] ) ), 'ansclothes_save_size_chart' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$sizes   = isset( $_POST['ans_size_chart_size'] ) ? (array) wp_unslash( $_POST['ans_size_chart_size'] ) : array();
	$chests  = isset( $_POST['ans_size_chart_chest'] ) ? (array) wp_unslash( $_POST['ans_size_chart_chest'] ) : array();
	$lengths = isset( $_POST['ans_size_chart_length'] ) ? (array) wp_unslash( $_POST['ans_size_chart_length'] ) : array();

	$rows = array();

	foreach ( $sizes as $i => $size ) {
		$size   = sanitize_text_field( $size );
		$chest  = isset( $chests[ $i ] ) ? sanitize_text_field( $chests[ $i ] ) : '';
		$length = isset( $lengths[ $i ] ) ? sanitize_text_field( $lengths[ $i ] ) : '';

		if ( '' === $size && '' === $chest && '' === $length ) {
			continue;
		}

		$rows[] = array(
			'size'   => $size,
			'chest'  => $chest,
			'length' => $length,
		);
	}

	update_post_meta( $post_id, '_ans_size_chart_rows', $rows );

	delete_post_meta( $post_id, '_ans_size_chart_category' );
	$categories = isset( $_POST['ans_size_chart_categories'] ) ? array_map( 'absint', (array) $_POST['ans_size_chart_categories'] ) : array();
	foreach ( array_unique( $categories ) as $term_id ) {
		add_post_meta( $post_id, '_ans_size_chart_category', $term_id, false );
	}

	delete_post_meta( $post_id, '_ans_size_chart_product' );
	$products = isset( $_POST['ans_size_chart_products'] ) ? array_map( 'absint', (array) $_POST['ans_size_chart_products'] ) : array();
	foreach ( array_unique( $products ) as $product_id ) {
		add_post_meta( $post_id, '_ans_size_chart_product', $product_id, false );
	}
}
add_action( 'save_post_ans_size_chart', 'ansclothes_save_size_chart' );

/**
 * Admin assets: the row-repeater script, and WooCommerce's product-search
 * select2 for the "specific products" field.
 *
 * @param string $hook Current admin page.
 */
function ansclothes_size_chart_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( ! $screen || 'ans_size_chart' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_script( 'wc-enhanced-select' );
	wp_enqueue_style( 'select2' );

	wp_enqueue_script(
		'ansclothes-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		ANSCLOTHES_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ansclothes_size_chart_admin_assets' );

/**
 * Find the size chart that applies to a product — a chart assigned directly
 * to the product wins; otherwise the first chart assigned to one of its
 * categories.
 *
 * @param int $product_id Product ID.
 * @return array|null Chart rows (each an array with size/chest/length), or
 *                     null if nothing matches.
 */
function ansclothes_get_size_chart_for_product( $product_id ) {
	$chart_id = 0;

	$direct = get_posts(
		array(
			'post_type'      => 'ans_size_chart',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => '_ans_size_chart_product',
					'value' => $product_id,
				),
			),
		)
	);

	if ( ! empty( $direct ) ) {
		$chart_id = (int) $direct[0];
	} else {
		$category_ids = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		if ( ! empty( $category_ids ) && ! is_wp_error( $category_ids ) ) {
			$by_category = get_posts(
				array(
					'post_type'      => 'ans_size_chart',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
						array(
							'key'     => '_ans_size_chart_category',
							'value'   => $category_ids,
							'compare' => 'IN',
						),
					),
				)
			);

			if ( ! empty( $by_category ) ) {
				$chart_id = (int) $by_category[0];
			}
		}
	}

	if ( ! $chart_id ) {
		return null;
	}

	$rows = get_post_meta( $chart_id, '_ans_size_chart_rows', true );

	return ! empty( $rows ) ? $rows : null;
}

/**
 * "Size Chart Guide" trigger + modal on the single product page.
 *
 * Rendered as a normal, visible button placed right before the variations
 * form; assets/js/main.js moves it into the size selector's label row when
 * that row exists (variable products), so it reads as "Select Size —
 * Size Chart Guide" on one line. Products with no size attribute keep the
 * button in its fallback spot instead of losing it silently.
 */
function ansclothes_size_chart_button() {
	global $product;

	if ( ! $product ) {
		return;
	}

	$rows = ansclothes_get_size_chart_for_product( $product->get_id() );

	if ( ! $rows ) {
		return;
	}
	?>
	<button type="button" class="ans-size-chart-trigger" id="ans-size-chart-trigger" aria-haspopup="dialog">
		<?php esc_html_e( 'Size Chart Guide', 'ansclothes' ); ?>
	</button>

	<div class="ans-size-chart-modal" id="ans-size-chart-modal" aria-hidden="true">
		<div class="ans-size-chart-modal__overlay" data-size-chart-close></div>
		<div class="ans-size-chart-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Size chart', 'ansclothes' ); ?>">
			<button type="button" class="ans-size-chart-modal__close" data-size-chart-close aria-label="<?php esc_attr_e( 'Close', 'ansclothes' ); ?>">&times;</button>
			<h3><?php esc_html_e( 'Size Chart', 'ansclothes' ); ?></h3>
			<table class="ans-size-chart-modal__table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Size', 'ansclothes' ); ?></th>
						<th><?php esc_html_e( 'Chest (in)', 'ansclothes' ); ?></th>
						<th><?php esc_html_e( 'Length (in)', 'ansclothes' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $row ) : ?>
						<tr>
							<td><?php echo esc_html( $row['size'] ?? '' ); ?></td>
							<td><?php echo esc_html( $row['chest'] ?? '' ); ?></td>
							<td><?php echo esc_html( $row['length'] ?? '' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
add_action( 'woocommerce_before_add_to_cart_form', 'ansclothes_size_chart_button' );
