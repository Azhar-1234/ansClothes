<?php
/**
 * Storefront homepage.
 *
 * @package ANSClothes
 */

get_header();

get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/home/circle-categories' );
get_template_part( 'template-parts/home/category-products' );

get_footer();
