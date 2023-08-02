<?php

namespace Theme\Inc\Modules\Package;

use WP_Query;
use WP_REST_Response;

class Package
{
	const POST_TYPE = 'package';
	private array $posts = [];
	private array $taxonomies;
	private array $taxonomies_terms = [];
	private array $defaultSettings;

	public function __construct()
	{
		$this->taxonomies = [
			'package_region' => [
				'label' => __('Région'),
				'slug' => 'region',
			],
			'package_type' => [
				'label' => __('Aventure'),
				'slug' => 'aventure',
			],
			'package_group' => [
				'label' => __('Type'),
				'slug' => 'type',
			],
		];

		add_action('init', [$this, 'init'], 0);

		$this->add_actions();
		$this->add_filters();
		$this->add_shortcodes();
	}

	private function add_actions(): void
	{
//		add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
	}

	private function add_filters(): void
	{
		add_filter('gform_pre_validation', [$this, 'populate_choices']);
		add_filter('gform_pre_submission_filter', [$this, 'populate_choices']);
		add_filter('gform_admin_pre_render', [$this, 'populate_choices']);
	}

	private function add_shortcodes(): void
	{
		add_shortcode('package_form', [$this, 'post_form_shortcode']);
		add_shortcode('packages', [$this, 'packages_shortcode']);
	}

	public function init(): void
	{
		$this->defaultSettings = $this->setDefaultSettings();
		$this->posts = $this->set_posts();
		$this->taxonomies_terms = $this->set_taxonomies_terms();
	}

	public function setDefaultSettings(): array
	{
		$img_placeholder = get_option('theme_utilities_img_placeholder');
		$img_placeholder = $img_placeholder ? wp_get_attachment_image_url($img_placeholder, 'large') : false;

		return [
			'default_video' => get_option('theme_utilities_package_video_placeholder'),
			'default_image' => $img_placeholder,
		];
	}

	public function getPosts(): array
	{
		return $this->posts;
	}

	public function getTaxonomiesTerms(): array
	{
		return $this->taxonomies_terms;
	}

	public function enqueue_scripts(): void
	{
		wp_enqueue_script('package-front-script', get_template_directory_uri() . '/assets/js/package-front.js', ['jquery'], time(), true);


		wp_localize_script('package-front-script', 'postData', [
			'packages' => json_encode($this->posts),
			'taxonomies' => json_encode($this->taxonomies_terms),
			'defaultSettings' => json_encode($this->defaultSettings),
			'admin_url' => admin_url('admin-ajax.php'),
			'isFront' => is_front_page(),
			'home_url' => home_url(),
			'selected' => $this->get_default_selected()
		]);
	}

	public function admin_enqueue_scripts(): void
	{
		wp_enqueue_media();
		wp_enqueue_script('package-admin-script', get_template_directory_uri() . '/assets/modules/package-admin.js', array('jquery'), '1.0', true);
	}

	public function set_posts(): array
	{
		$args = [
			'post_type' => self::POST_TYPE,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		];

		$posts_query = new WP_Query($args);
		$posts = [];

		if ($posts_query->have_posts()) {
			while ($posts_query->have_posts()) {
				$posts_query->the_post();
				$post_id = get_the_ID();

				$image = get_the_post_thumbnail_url($post_id, 'large');

				if (!$image || $image == '') {
					$image = $this->defaultSettings['default_image'];
				}

				$posts[$post_id] = [
					'title' => get_the_title(),
					'content' => get_the_content() ?: '',
					'permalink' => get_the_permalink(),
					'featured_image' => $image,
					'video' => get_post_meta($post_id, 'vimeo_video_id', true) ?: '',
					'id' => $post_id,
					'price' => get_field('prix', $post_id) ?: '',
					'isSelected' => false,
					'leaflet' => get_field('leaflet_map') ?: '',
					'taxonomies' => $this->get_post_taxonomies_terms($post_id),
					'duration' => get_field('duree', $post_id) ?: '',
					'length' => get_field('temps_de_vol', $post_id) ?: '',
					'slides' => []
				];
			}

			wp_reset_postdata();
		}
		return $posts;
	}

	public function set_taxonomies_terms(): array
	{
		$taxonomies = get_object_taxonomies(self::POST_TYPE);

		$taxonomiesTerms = [];

		foreach ($taxonomies as $taxonomy) {
			$taxonomy_object = get_taxonomy($taxonomy);

			$taxonomiesTerms[$taxonomy] = [
				'name' => $taxonomy_object->labels->name,
				'terms' => [],
			];

			foreach (get_terms([
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
				'parent' => 0
			]) as $term) {
				$taxonomiesTerms[$taxonomy]['terms'][$term->term_id] = [
					'id' => $term->term_id,
					'title' => $term->name,
					'permalink' => $term->slug,
					'content' => $term->description ?: '',
					'featured_image' => $this->get_taxonomies_terms_featured_image($term) ?? '',
					'video' => get_field('taxonomy_featured_video', $term) ?? '',
					'duration' => get_field('duree', $term) ?: '',
					'length' => get_field('temps_de_vol', $term) ?: '',
					'price' => get_field('prix', $term) ?: '',
					'slides' => []
				];
			}
		}


		return $taxonomiesTerms;
	}

	public function get_taxonomies_terms_featured_image($term)
	{
		$featured_image = get_field('taxonomy_featured_image', $term);


		if ($featured_image == '') {
			$featured_image = $this->defaultSettings['default_image'];
		}

		return $featured_image;
	}

	public function get_post_taxonomies_terms($post_id): array
	{
		$taxonomies_terms = [];

		foreach ($this->taxonomies as $key => $taxonomy) {
			$taxonomies_terms[$key] = wp_get_post_terms($post_id, $key, ['fields' => 'ids']);
		}

		return $taxonomies_terms;
	}

	public function get_posts_choices(): array
	{
		$choices = [];

		foreach ($this->posts as $post) {
			$choices[] = ['text' => $post['title'], 'value' => $post['id'], 'price' => $post['price'], 'isSelected' => false];
		}

		return $choices;
	}

	private function get_taxonomy_choices($taxonomy): array
	{
		$terms = $this->taxonomies_terms[$taxonomy]['terms'];

		$choices = [];

		foreach ($terms as $term) {
			$choices[] = ['text' => $term['text'], 'value' => $term['id'], 'isSelected' => false];
		}

		return $choices;
	}

	public function get_default_selected()
	{
		$queriedObject = get_queried_object();
		$package_region_term = get_query_var('package_region_term');
		$package_group = get_query_var('package_group');
		$values = [];

		if (is_tax()) {
			$key = $queriedObject->taxonomy;
			$values[$key] = $queriedObject->term_id;
		} elseif (is_single() && 'package' == get_post_type()) {
			$values['destination'] = $queriedObject->ID . "|" . $this->posts[$queriedObject->ID]['price'];
			foreach ($this->posts[$queriedObject->ID]['taxonomies'] as $key => $taxonomy) {
				if (!empty($taxonomy)) {
					$values[$key] = $taxonomy[0];
				}
			}
		}

		if ($package_region_term) {
			$selected_region = array_filter($this->taxonomies_terms['package_region']['terms'], function ($item) use ($package_region_term) {
				return $item['permalink'] === $package_region_term;
			});

			$values['package_region'] = array_values($selected_region)[0]['id'];
		}

		if ($package_group) {
			$selected_group = array_filter($this->taxonomies_terms['package_group']['terms'], function ($item) use ($package_group) {
				return $item['permalink'] === $package_group;
			});
			$values['package_group'] = array_values($selected_group)[0]['id'];
		}

//		} elseif (is_front_page()) {
//
////			$randomPostId = rand(0, count($this->posts) - 1);
////			$randomPost = $this->posts[$randomPostId];
////
////			$values['destination'] = $randomPost['id'];
////
////			foreach ($randomPost['taxonomies'] as $key => $taxonomy) {
////
////				if (!empty($taxonomy)) {
////					$values[$key] = $taxonomy[0];
////				}
////			}
//
//		}

		return $values;
	}

	public function populate_choices($form)
	{
		foreach ($form['fields'] as &$field) {
			if ($field->cssClass == 'package-regions') {
				$field->choices = $this->get_taxonomy_choices('package_region');
			}
			if ($field->cssClass == 'package-types') {
				$field->choices = $this->get_taxonomy_choices('package_type');
			}
			if ($field->cssClass == 'package-groups') {
				$field->choices = $this->get_taxonomy_choices('package_group');
			}
			if ($field->cssClass == 'packages') {
				$field->choices = $this->get_posts_choices();
			}
		}

		return $form;
	}

	public function post_form_shortcode($args): bool|string
	{
		$args = shortcode_atts(
			array(
				'form_id' => null,
				'template' => null
			),
			$args,
			'package_form'
		);

		$post = [
			'duration' => '',
			'length' => '',
			'price' => '',
			'excerpt' => '',
		];

		if (is_single() && get_post_type() === 'package') {
			$post = $this->posts[get_the_ID()];
		} else if (is_tax()) {

			$queriedObj = get_queried_object();
			$queriedObjTaxonomy = $queriedObj->taxonomy;
			$queriedObjTermId = $queriedObj->term_id;
			$post = $this->taxonomies_terms[$queriedObjTaxonomy]['terms'][$queriedObjTermId];
		}

		extract($args);
		ob_start();
		include __DIR__ . '/package-form-template.php';
		return ob_get_clean();
	}

	public function packages_shortcode($args): bool|string
	{
		$args = shortcode_atts(
			array(),
			$args,
			'packages'
		);

		extract($args);

		ob_start();
		include __DIR__ . '/packages-loop.php';
		return ob_get_clean();
	}
}

new Package();
