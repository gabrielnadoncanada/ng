<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

const IS_VITE_DEVELOPMENT = true;
define('TEMPLATE_DIR', get_stylesheet_directory_uri());
const SCRIPT_DIR = TEMPLATE_DIR . '/assets/js';
define('VITE_SERVER', 'http://localhost:3000');
define('VITE_ENTRY_POINT', '/main.js');
//add_filter('query_vars', 'custom_query_vars');
//add_filter('term_link', 'remove_taxonomy_slug_from_archive', 10, 3);
//add_action('init', 'custom_rewrite_rules', 11);


//function custom_query_vars($vars)
//{
//	$vars[] = 'package_region_term';
//	return $vars;
//}
//
//function remove_taxonomy_slug_from_archive($link, $term, $taxonomy)
//{
//	$custom_taxonomies = array('package_region', 'package_type', 'package_group');
//	if (in_array($taxonomy, $custom_taxonomies)) {
//		return home_url('/') . $term->slug;
//	}
//	return $link;
//}
//
//function custom_rewrite_rules()
//{
//	global $wp_rewrite;
//	$region_terms = get_terms(array(
//		'taxonomy' => 'package_region',
//		'hide_empty' => false,
//	));
//
//	if ($region_terms && !is_wp_error($region_terms)) {
//		foreach ($region_terms as $term) {
//			$slug = $term->slug;
//			add_rewrite_rule("^$slug/survols/([^/]+)/?$", 'index.php?package_group=$matches[1]&package_region_term=' . $slug, 'top');
//			add_rewrite_rule("^$slug/([^/]+)/?$", 'index.php?package_type=$matches[1]&package_region_term=' . $slug, 'top');
//			add_rewrite_rule("^$slug/?$", 'index.php?package_region=' . $slug, 'top');
//		}
//	}
//}

function ng_setup()
{
	add_theme_support('editor-styles');


	add_editor_style('dist/assets/main.css');

	register_nav_menus(
		array(
			'header-menu' => __('Header Menu'),
			'footer-menu' => __('Footer Menu')
		)
	);
}

add_action('after_setup_theme', 'ng_setup');
add_action('wp_enqueue_scripts', 'ng_enqueue_assets');

function ng_enqueue_assets()
{
	wp_enqueue_script('magnetic', get_stylesheet_directory_uri() . '/assets/js/magnetic.js', array('jquery'), time(), true);
	wp_enqueue_script('alpine', get_stylesheet_directory_uri() . '/assets/js/alpine.min.js', array(), time(), true);
	wp_enqueue_script('gsap', get_stylesheet_directory_uri() . '/assets/js/gsap.min.js', array(), time(), true);
	wp_enqueue_script('scripts', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array('jquery', 'underscore'), time(), false);
	wp_enqueue_script('swiper', get_stylesheet_directory_uri() . '/assets/js/swiper-bundle.js', array(), null, true);
	wp_enqueue_style('swiper', get_stylesheet_directory_uri() . '/assets/css/swiper-bundle.css', array(), null);


	if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true) {
		function vite_head_module_hook()
		{
			echo '<script type="module" crossorigin src="' . VITE_SERVER . VITE_ENTRY_POINT . '"></script>';
		}

		add_action('wp_head', 'vite_head_module_hook');


	} else {
		wp_enqueue_style('tailpress', get_stylesheet_directory_uri() . '/dist/assets/main.css', array(), time());
		wp_enqueue_script('main', get_stylesheet_directory_uri() . '/dist/assets/main.js', array(), time(), true);
	}
}

function get_last_post_of_current_post_type()
{
	$post_type = get_post_type(); // Gets the post type of the current post

	$args = array(
		'post_type' => $post_type, // Use the post type retrieved
		'posts_per_page' => 3, // Only get the last post
		'order' => 'DESC', // Get posts in descending order (latest first)
		'orderby' => 'date' // Order by date
	);

	$wp_query = new WP_Query($args); // Query the posts

	?>

	<div class="my-20"><h2 class="text-center my-12">Articles récents</h2>
	<div class="grid grid-cols-1 gap-x-4 gap-y-8 xs:grid-cols-2 lg:grid-cols-3 sm:gap-x-6 xl:gap-x-8 max-w-[1536px]  w-full mx-auto">
	<?php
	if ($wp_query->have_posts()) : while ($wp_query->have_posts()) : $wp_query->the_post();
		// Get template part
		get_template_part('template-parts/content', get_post_format());
	endwhile; endif;
	?>
	</div>

	</div>
	<?php

	the_post_navigation(
		array(
			'prev_text' => '<span class="nav-subtitle">' . esc_html__('←', 'hc') . '</span> <span class="nav-title">%title</span>',
			'next_text' => '<span class="nav-subtitle">' . esc_html__('→', 'hc') . '</span> <span class="nav-title">%title</span>',
		)
	);

	wp_reset_postdata(); // Reset post data after custom query
}

require get_template_directory() . '/package.php';


add_shortcode('leaflet_map', function(){
	ob_start();
	get_template_part('template-parts/leaflet-map');
	return ob_get_clean();
});


function get_default_image_placeholder() {
	return wp_get_attachment_url(get_option('theme_utilities_img_placeholder')) ?? get_stylesheet_directory_uri() . '/assets/img/placeholder.jpg';
}