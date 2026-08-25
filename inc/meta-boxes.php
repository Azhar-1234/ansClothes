<?php
/**
 * Product meta box and collection term fields.
 *
 * @package ANSClothes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the product details meta box.
 */
function ansclothes_add_meta_boxes() {
	add_meta_box(
		'ansclothes_product_details',
		__( 'Product Details', 'ansclothes' ),
		'ansclothes_product_meta_box',
		'ans_product',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'ansclothes_add_meta_boxes' );

/**
 * Render the product details meta box.
 *
 * @param WP_Post $post Current post.
 */
function ansclothes_product_meta_box( $post ) {
	wp_nonce_field( 'ansclothes_save_product', 'ansclothes_product_nonce' );

	$price = get_post_meta( $post->ID, '_ans_price', true );
	$badge = get_post_meta( $post->ID, '_ans_badge', true );
	$sizes = get_post_meta( $post->ID, '_ans_sizes', true );
	?>
	<p>
		<label for="ans_price"><strong><?php esc_html_e( 'Price', 'ansclothes' ); ?></strong></label><br>
		<input type="text" id="ans_price" name="ans_price" value="<?php echo esc_attr( $price ); ?>" class="widefat" placeholder="850">
		<span class="description"><?php esc_html_e( 'Number only — the currency symbol is set in the Customizer.', 'ansclothes' ); ?></span>
	</p>
	<p>
		<label for="ans_badge"><strong><?php esc_html_e( 'Badge', 'ansclothes' ); ?></strong></label><br>
		<input type="text" id="ans_badge" name="ans_badge" value="<?php echo esc_attr( $badge ); ?>" class="widefat" placeholder="<?php esc_attr_e( 'New / Featured / Pre-Order', 'ansclothes' ); ?>">
	</p>
	<p>
		<label for="ans_sizes"><strong><?php esc_html_e( 'Sizes', 'ansclothes' ); ?></strong></label><br>
		<input type="text" id="ans_sizes" name="ans_sizes" value="<?php echo esc_attr( $sizes ); ?>" class="widefat" placeholder="S, M, L, XL, XXL">
		<span class="description"><?php esc_html_e( 'Comma separated.', 'ansclothes' ); ?></span>
	</p>
	<?php
}

/**
 * Save product meta.
 *
 * @param int $post_id Post ID.
 */
function ansclothes_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['ansclothes_product_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ansclothes_product_nonce'] ) ), 'ansclothes_save_product' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'ans_price' => '_ans_price',
		'ans_badge' => '_ans_badge',
		'ans_sizes' => '_ans_sizes',
	);

	foreach ( $fields as $field => $meta_key ) {
		if ( isset( $_POST[ $field ] ) ) {
			$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );

			if ( '' === $value ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
	}
}
add_action( 'save_post_ans_product', 'ansclothes_save_product_meta' );

/**
 * Extra fields on the "add collection" screen.
 */
function ansclothes_taxonomy_add_fields() {
	wp_nonce_field( 'ansclothes_save_term', 'ansclothes_term_nonce' );
	?>
	<div class="form-field">
		<label for="ans_count_label"><?php esc_html_e( 'Count Label', 'ansclothes' ); ?></label>
		<input type="text" id="ans_count_label" name="ans_count_label" value="" placeholder="<?php esc_attr_e( '12 Items / Coming Soon', 'ansclothes' ); ?>">
		<p><?php esc_html_e( 'Shown on the homepage category card. Leave empty to use the real product count.', 'ansclothes' ); ?></p>
	</div>
	<div class="form-field">
		<label for="ans_term_image"><?php esc_html_e( 'Card Image', 'ansclothes' ); ?></label>
		<input type="hidden" id="ans_term_image" name="ans_term_image" value="">
		<button type="button" class="button ans-media-pick" data-target="ans_term_image"><?php esc_html_e( 'Select image', 'ansclothes' ); ?></button>
		<button type="button" class="button-link ans-media-clear" data-target="ans_term_image"><?php esc_html_e( 'Remove', 'ansclothes' ); ?></button>
		<div class="ans-media-preview" data-preview-for="ans_term_image"></div>
	</div>
	<?php
}
add_action( 'ans_product_cat_add_form_fields', 'ansclothes_taxonomy_add_fields' );

/**
 * Extra fields on the "edit collection" screen.
 *
 * @param WP_Term $term Current term.
 */
function ansclothes_taxonomy_edit_fields( $term ) {
	wp_nonce_field( 'ansclothes_save_term', 'ansclothes_term_nonce' );

	$label    = get_term_meta( $term->term_id, 'ans_count_label', true );
	$image_id = (int) get_term_meta( $term->term_id, 'ans_term_image', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="ans_count_label"><?php esc_html_e( 'Count Label', 'ansclothes' ); ?></label></th>
		<td>
			<input type="text" id="ans_count_label" name="ans_count_label" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( '12 Items / Coming Soon', 'ansclothes' ); ?>">
			<p class="description"><?php esc_html_e( 'Shown on the homepage category card. Leave empty to use the real product count.', 'ansclothes' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="ans_term_image"><?php esc_html_e( 'Card Image', 'ansclothes' ); ?></label></th>
		<td>
			<input type="hidden" id="ans_term_image" name="ans_term_image" value="<?php echo esc_attr( $image_id ); ?>">
			<button type="button" class="button ans-media-pick" data-target="ans_term_image"><?php esc_html_e( 'Select image', 'ansclothes' ); ?></button>
			<button type="button" class="button-link ans-media-clear" data-target="ans_term_image"><?php esc_html_e( 'Remove', 'ansclothes' ); ?></button>
			<div class="ans-media-preview" data-preview-for="ans_term_image">
				<?php
				if ( $image_id ) {
					echo wp_get_attachment_image( $image_id, 'medium', false, array( 'style' => 'max-width:180px;height:auto;margin-top:10px;' ) );
				}
				?>
			</div>
		</td>
	</tr>
	<?php
}
add_action( 'ans_product_cat_edit_form_fields', 'ansclothes_taxonomy_edit_fields' );

/**
 * Save the collection term fields.
 *
 * @param int $term_id Term ID.
 */
function ansclothes_save_term_fields( $term_id ) {
	if ( ! isset( $_POST['ansclothes_term_nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ansclothes_term_nonce'] ) ), 'ansclothes_save_term' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	if ( isset( $_POST['ans_count_label'] ) ) {
		update_term_meta( $term_id, 'ans_count_label', sanitize_text_field( wp_unslash( $_POST['ans_count_label'] ) ) );
	}

	if ( isset( $_POST['ans_term_image'] ) ) {
		update_term_meta( $term_id, 'ans_term_image', absint( wp_unslash( $_POST['ans_term_image'] ) ) );
	}
}
add_action( 'created_ans_product_cat', 'ansclothes_save_term_fields' );
add_action( 'edited_ans_product_cat', 'ansclothes_save_term_fields' );

/**
 * Media picker script for the collection screens.
 *
 * @param string $hook Current admin page.
 */
function ansclothes_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}

	$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'ans_product_cat' !== $taxonomy ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'ansclothes-admin',
		get_template_directory_uri() . '/assets/js/admin.js',
		array( 'jquery' ),
		ANSCLOTHES_VERSION,
		true
	);
}
add_action( 'admin_enqueue_scripts', 'ansclothes_admin_assets' );

/**
 * Price column on the products list table.
 *
 * @param array $columns Columns.
 * @return array
 */
function ansclothes_product_columns( $columns ) {
	$columns['ans_price'] = __( 'Price', 'ansclothes' );

	return $columns;
}
add_filter( 'manage_ans_product_posts_columns', 'ansclothes_product_columns' );

/**
 * Render the price column.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function ansclothes_product_column_content( $column, $post_id ) {
	if ( 'ans_price' === $column ) {
		$price = get_post_meta( $post_id, '_ans_price', true );
		echo $price ? esc_html( ansclothes_currency() . ' ' . $price ) : '&mdash;';
	}
}
add_action( 'manage_ans_product_posts_custom_column', 'ansclothes_product_column_content', 10, 2 );
