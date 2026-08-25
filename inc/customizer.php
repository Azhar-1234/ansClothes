<?php
/**
 * Customizer: every homepage section is editable from Appearance → Customize.
 *
 * @package ANSClothes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkbox sanitizer.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function ansclothes_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize a heading that may contain a little inline markup.
 *
 * @param string $value Raw value.
 * @return string
 */
/**
 * Hex colour that also accepts an empty value.
 *
 * sanitize_hex_color() returns null for '', which would store nothing and
 * make "clear the colour" indistinguishable from an invalid entry, so the
 * empty case is handled explicitly.
 *
 * @param string $value Raw value.
 * @return string
 */
/**
 * Non-negative price, stored as a plain decimal string.
 *
 * @param string $value Raw value.
 * @return string
 */
function ansclothes_sanitize_price( $value ) {
	$value = (float) $value;

	return (string) max( 0, $value );
}

function ansclothes_sanitize_optional_hex_color( $value ) {
	if ( '' === trim( (string) $value ) ) {
		return '';
	}

	$color = sanitize_hex_color( $value );

	return $color ? $color : '';
}

function ansclothes_sanitize_html( $value ) {
	return wp_kses( $value, ansclothes_heading_tags() );
}

/**
 * The full Customizer field map.
 *
 * @return array
 */
function ansclothes_customizer_fields() {
	return array(
		'ansclothes_announcement' => array(
			'title'  => __( 'Announcement Bar', 'ansclothes' ),
			'fields' => array(
				'announcement_enable' => array(
					'label' => __( 'Show the announcement bar', 'ansclothes' ),
					'type'  => 'checkbox',
				),
				'announcement_text'   => array(
					'label'       => __( 'Announcement text', 'ansclothes' ),
					'type'        => 'html',
					'description' => __( 'Basic inline tags are allowed: <strong>, <em>, <span>, <br>, <a>. Leave empty to hide the bar.', 'ansclothes' ),
				),
				'announcement_url'    => array(
					'label'       => __( 'Link URL (optional)', 'ansclothes' ),
					'type'        => 'url',
					'description' => __( 'Makes the whole message clickable, e.g. a link to a sale page. Leave empty for plain text.', 'ansclothes' ),
				),
				'announcement_bg'     => array(
					'label'       => __( 'Background colour', 'ansclothes' ),
					'type'        => 'color',
					'description' => __( 'Leave empty to use the theme default.', 'ansclothes' ),
				),
				'announcement_color'  => array(
					'label' => __( 'Text colour', 'ansclothes' ),
					'type'  => 'color',
				),
			),
		),
		'ansclothes_cod'          => array(
			'title'  => __( 'Checkout — Cash on Delivery', 'ansclothes' ),
			'fields' => array(
				'cod_enable'       => array(
					'label'       => __( 'Use the quick cash-on-delivery popup', 'ansclothes' ),
					'type'        => 'checkbox',
					'description' => __( '"Order Now" opens a short form instead of the full checkout page. Turn off to use the normal WooCommerce checkout.', 'ansclothes' ),
				),
				'cod_title'        => array( 'label' => __( 'Popup title', 'ansclothes' ), 'type' => 'text' ),
				'cod_intro'        => array( 'label' => __( 'Intro line', 'ansclothes' ), 'type' => 'text' ),
				'cod_button_label' => array(
					'label'       => __( 'Submit button text', 'ansclothes' ),
					'type'        => 'text',
					'description' => __( 'The order total is appended automatically.', 'ansclothes' ),
				),
				'cod_ship1_label'  => array( 'label' => __( 'Shipping option 1 — label', 'ansclothes' ), 'type' => 'text', 'description' => __( 'Leave empty to hide this option.', 'ansclothes' ) ),
				'cod_ship1_cost'   => array( 'label' => __( 'Shipping option 1 — cost', 'ansclothes' ), 'type' => 'price' ),
				'cod_ship2_label'  => array( 'label' => __( 'Shipping option 2 — label', 'ansclothes' ), 'type' => 'text', 'description' => __( 'Leave empty to hide this option.', 'ansclothes' ) ),
				'cod_ship2_cost'   => array( 'label' => __( 'Shipping option 2 — cost', 'ansclothes' ), 'type' => 'price' ),
			),
		),
		'ansclothes_slider'      => array(
			'title'  => __( 'Homepage — Slider', 'ansclothes' ),
			'fields' => array(
				'slider_1_image' => array( 'label' => __( 'Slide 1 Image', 'ansclothes' ), 'type' => 'image' ),
				'slider_1_url'   => array( 'label' => __( 'Slide 1 Link', 'ansclothes' ), 'type' => 'url' ),
				'slider_2_image' => array( 'label' => __( 'Slide 2 Image', 'ansclothes' ), 'type' => 'image' ),
				'slider_2_url'   => array( 'label' => __( 'Slide 2 Link', 'ansclothes' ), 'type' => 'url' ),
			),
		),
		'ansclothes_shop'      => array(
			'title'  => __( 'Homepage — Circle Categories', 'ansclothes' ),
			'fields' => array(
				'circle_categories_enable'  => array( 'label' => __( 'Show circle categories section', 'ansclothes' ), 'type' => 'checkbox' ),
				'circle_categories_count'   => array( 'label' => __( 'Number of categories to show', 'ansclothes' ), 'type' => 'number' ),
				'circle_categories_slugs'   => array( 'label' => __( 'Specific category slugs to SHOW (comma-separated, leave empty for all)', 'ansclothes' ), 'type' => 'text' ),
				'circle_categories_exclude' => array( 'label' => __( 'Specific category slugs to EXCLUDE (comma-separated)', 'ansclothes' ), 'type' => 'text' ),
			),
		),
		'ansclothes_product_buttons' => array(
			'title'  => __( 'Single Product — Buy Buttons', 'ansclothes' ),
			'fields' => array(
				'product_addtocart_label'  => array( 'label' => __( '"Add to Cart" button text', 'ansclothes' ), 'type' => 'text' ),

				'product_buynow_enable'    => array( 'label' => __( 'Show "Buy Now" button', 'ansclothes' ), 'type' => 'checkbox', 'description' => __( 'Adds the item to the cart and goes straight to checkout.', 'ansclothes' ) ),
				'product_buynow_label'     => array( 'label' => __( '"Buy Now" button text', 'ansclothes' ), 'type' => 'text' ),

				'product_whatsapp_enable'  => array( 'label' => __( 'Show "Order on WhatsApp" button', 'ansclothes' ), 'type' => 'checkbox' ),
				'product_whatsapp_label'   => array( 'label' => __( 'WhatsApp button text', 'ansclothes' ), 'type' => 'text' ),
				'product_whatsapp_number'  => array( 'label' => __( 'WhatsApp number', 'ansclothes' ), 'type' => 'text', 'description' => __( 'With country code, e.g. 8801XXXXXXXXX. Leave empty to hide the button.', 'ansclothes' ) ),
				'product_whatsapp_message' => array( 'label' => __( 'Pre-filled WhatsApp message', 'ansclothes' ), 'type' => 'textarea', 'description' => __( 'Placeholders: {product}, {price}, {url}.', 'ansclothes' ) ),

				'product_call_enable'      => array( 'label' => __( 'Show "Call for Order" button', 'ansclothes' ), 'type' => 'checkbox' ),
				'product_call_label'       => array( 'label' => __( 'Call button text', 'ansclothes' ), 'type' => 'text' ),
				'product_call_number'      => array( 'label' => __( 'Phone number to call', 'ansclothes' ), 'type' => 'text', 'description' => __( 'E.g. +8801XXXXXXXXX. Leave empty to hide the button.', 'ansclothes' ) ),
			),
		),
		'ansclothes_category_products' => array(
			'title'  => __( 'Homepage — Category Products', 'ansclothes' ),
			'fields' => array(
				'category_products_enable' => array( 'label' => __( 'Show category products section', 'ansclothes' ), 'type' => 'checkbox' ),
				'category_products_slugs'  => array( 'label' => __( 'Specific category slugs to show (comma-separated)', 'ansclothes' ), 'type' => 'text' ),
				'category_products_count'  => array( 'label' => __( 'Number of products per category', 'ansclothes' ), 'type' => 'number' ),
			),
		),

	);
}

/**
 * Register panels, sections, settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ansclothes_customize_register( $wp_customize ) {
	$defaults = ansclothes_defaults();

	$wp_customize->add_panel(
		'ansclothes_panel',
		array(
			'title'       => __( 'ANSClothes Theme', 'ansclothes' ),
			'description' => __( 'Content and visibility for the storefront homepage.', 'ansclothes' ),
			'priority'    => 20,
		)
	);

	$priority = 10;

	foreach ( ansclothes_customizer_fields() as $section_id => $section ) {
		$wp_customize->add_section(
			$section_id,
			array(
				'title'    => $section['title'],
				'panel'    => 'ansclothes_panel',
				'priority' => $priority,
			)
		);

		$priority += 10;

		foreach ( $section['fields'] as $key => $field ) {
			$type = isset( $field['type'] ) ? $field['type'] : 'text';

			switch ( $type ) {
				case 'checkbox':
					$sanitize = 'ansclothes_sanitize_checkbox';
					break;
				case 'html':
					$sanitize = 'ansclothes_sanitize_html';
					break;
				case 'url':
				case 'image':
					$sanitize = 'esc_url_raw';
					break;
				case 'number':
					$sanitize = 'absint';
					break;
				case 'price':
					$sanitize = 'ansclothes_sanitize_price';
					break;
				case 'color':
					// Allows an empty value so "unset" falls back to the stylesheet.
					$sanitize = 'ansclothes_sanitize_optional_hex_color';
					break;
				case 'textarea':
					$sanitize = 'sanitize_textarea_field';
					break;
				default:
					$sanitize = 'sanitize_text_field';
			}

			$wp_customize->add_setting(
				$key,
				array(
					'default'           => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '',
					'sanitize_callback' => $sanitize,
					'transport'         => 'refresh',
				)
			);

			$control_args = array(
				'label'       => $field['label'],
				'section'     => $section_id,
				'description' => isset( $field['description'] ) ? $field['description'] : '',
			);

			if ( 'image' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Image_Control( $wp_customize, $key, $control_args )
				);
				continue;
			}

			if ( 'color' === $type ) {
				$wp_customize->add_control(
					new WP_Customize_Color_Control( $wp_customize, $key, $control_args )
				);
				continue;
			}

			$control_args['type'] = ( 'html' === $type ) ? 'textarea' : $type;

			if ( 'number' === $type ) {
				$control_args['input_attrs'] = array( 'min' => 1, 'max' => 24, 'step' => 1 );
			}

			if ( 'price' === $type ) {
				$control_args['type']        = 'number';
				$control_args['input_attrs'] = array( 'min' => 0, 'step' => '0.01' );
			}

			$wp_customize->add_control( $key, $control_args );
		}
	}

	// Live preview for the simple text bits.
	foreach ( array( 'announcement_text', 'hero_eyebrow', 'hero_title', 'hero_text', 'footer_copy' ) as $key ) {
		$setting = $wp_customize->get_setting( $key );

		if ( $setting ) {
			$setting->transport = 'postMessage';
		}
	}

	$wp_customize->selective_refresh->add_partial(
		'announcement_text',
		array(
			'selector'        => '.ans-announcement',
			'render_callback' => 'ansclothes_announcement_content',
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'hero_title',
		array(
			'selector'        => '.ans-hero__title',
			'render_callback' => function () {
				echo wp_kses( ansclothes_option( 'hero_title' ), ansclothes_heading_tags() );
			},
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'hero_text',
		array(
			'selector'        => '.ans-hero__lead',
			'render_callback' => function () {
				echo esc_html( ansclothes_option( 'hero_text' ) );
			},
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'hero_eyebrow',
		array(
			'selector'        => '.ans-hero__text .ans-eyebrow',
			'render_callback' => function () {
				echo esc_html( ansclothes_option( 'hero_eyebrow' ) );
			},
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'footer_copy',
		array(
			'selector'        => '.ans-footer__copy',
			'render_callback' => function () {
				echo esc_html( ansclothes_option( 'footer_copy' ) );
			},
		)
	);
}
add_action( 'customize_register', 'ansclothes_customize_register' );

/**
 * Helpful pointer at the top of the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function ansclothes_customize_hints( $wp_customize ) {
	$section = $wp_customize->get_section( 'ansclothes_shop' );

	if ( $section ) {
		$section->description = sprintf(
			/* translators: %s: link to the products screen. */
			__( 'Cards are pulled from <a href="%s">Products</a>. Until you publish products, sample items from the design are shown.', 'ansclothes' ),
			esc_url( admin_url( 'edit.php?post_type=ans_product' ) )
		);
	}
}
add_action( 'customize_register', 'ansclothes_customize_hints', 20 );
