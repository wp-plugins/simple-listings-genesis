<?php

/**
 * Simple Listing Post Type
 *
 * @package    Simple_Listing_Post_Type
 * @author     Robin Cornett <hello@robincornett.com>
 * @copyright  2014 Robin Cornett
 *
 */

class Simple_Listing_Post_Type_Registrations {

	public $post_type = 'listing';

	public $taxonomies = 'status';

	public function init() {
		add_action( 'init', array( $this, 'register' ) );
		add_filter( 'cmb_meta_boxes', array( $this, 'set_metaboxes' ) );
		if ( basename( get_template_directory() ) == 'genesis' ) {
			add_filter( 'archive_template', array( $this, 'load_archive_template' ) );
			add_filter( 'single_template', array( $this, 'load_single_template' ) );
		}
		add_filter( 'post_class', array( $this, 'set_post_class' ) );
	}

	/**
	 * Initiate registrations of listing post types and taxonomies.
	 */
	public function register() {
		$this->register_post_type_listing();
		$this->register_taxonomy_status();
	}

	/**
	 * Register the Listing type.
	 */
	protected function register_post_type_listing() {
		$labels = array(
			'name'                => __( 'Listings', 'simple-listings-genesis' ),
			'singular_name'       => __( 'Listing', 'simple-listings-genesis' ),
			'menu_name'           => __( 'Listings', 'simple-listings-genesis' ),
			'parent_item_colon'   => __( 'Parent Listing:', 'simple-listings-genesis' ),
			'all_items'           => __( 'All Listings', 'simple-listings-genesis' ),
			'view_item'           => __( 'View Listing', 'simple-listings-genesis' ),
			'add_new_item'        => __( 'Add New Listing', 'simple-listings-genesis' ),
			'add_new'             => __( 'New Listing', 'simple-listings-genesis' ),
			'edit_item'           => __( 'Edit Listing', 'simple-listings-genesis' ),
			'update_item'         => __( 'Update Listing', 'simple-listings-genesis' ),
			'search_items'        => __( 'Search Listings', 'simple-listings-genesis' ),
			'not_found'           => __( 'No Listings found', 'simple-listings-genesis' ),
			'not_found_in_trash'  => __( 'No Listings found in Trash', 'simple-listings-genesis' ),
		);

		$rewrite = array(
			'slug'                => 'listings',
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);

		$args = array(
			'label'               => __( 'Listing', 'simple-listings-genesis' ),
			'description'         => __( 'Listing information', 'simple-listings-genesis' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'genesis-cpt-archives-settings' ),
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-location-alt',
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'query_var'           => 'listing',
			'rewrite'             => $rewrite,
			'capability_type'     => 'page',
		);

		$args = apply_filters( 'listingposttype_args', $args );

		register_post_type( $this->post_type, $args );
	} // ends Listing registration

	/**
	 * register listing status taxonomy
	 * @return taxonomy
	 *
	 * @since  1.0.0
	 *
	 */
	protected function register_taxonomy_status() {
		$labels = array(
			'name'              => __( 'Listing Status', 'simple-listings-genesis' ),
			'singular_name'     => __( 'Listing Status', 'simple-listings-genesis' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => 'status' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'listingposttype_status_args', $args );

		register_taxonomy( $this->taxonomies, $this->post_type, $args );

	}

	/**
	 * Define the metabox and field configurations.
	 *
	 * @since 1.0.0
	 *
	 * @param array $meta_boxes Existing meta boxes.
	 *
	 * @return array            Amended meta boxes.
	 *
	*/
	public function set_metaboxes( $meta_boxes ) {

		// Start with an underscore to hide fields from custom fields list
		$prefix = '_cmb_';

		$meta_boxes[] = array(
			'id'         => 'listing_metabox',
			'title'      => __( 'Listing Details', 'simple-listings-genesis' ),
			'pages'      => array( 'listing' ), // Post type
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true, // Show field names on the left
			'fields'     => array(
				array(
					'name' => __( 'MLS Link: ', 'simple-listings-genesis' ),
					'desc' => __( 'Enter the full URL of your listing. This can be on a separate MLS site or on your own site.', 'simple-listings-genesis' ),
					'id'   => $prefix . 'mls-link',
					'type' => 'text',
				),
				array(
					'name' => __( 'Location', 'simple-listings-genesis' ),
					'desc' => __( 'City, State location information. eg, Chattanooga, Tennessee', 'simple-listings-genesis' ),
					'id'   => $prefix . 'listing-location',
					'type' => 'text',
				),
				array(
					'name' => __( 'Transaction Value', 'simple-listings-genesis' ),
					'desc' => __( 'The sale price or property value.', 'simple-listings-genesis' ),
					'id'   => $prefix . 'listing-price',
					'type' => 'text',
				),
			),
		);

		return $meta_boxes;
	}

	/**
	 * load Listing archive template
	 * @param  template $archive_template requires Genesis
	 *
	 * @since  1.2.0
	 */
	public function load_archive_template( $archive_template ) {
		if ( is_post_type_archive( 'listing' ) || is_tax( 'status' ) ) {
			$archive_template = SIMPLELISTING_PATH . '/views/archive-listing.php';
		}

		return $archive_template;

	}

	/**
	 * load single Listing template
	 * @param  template $single_template requires Genesis
	 * @since 1.2.0
	 */
	public function load_single_template( $single_template ) {
		if ( is_singular( 'listing' ) ) {
			$single_template = SIMPLELISTING_PATH . '/views/single-listing.php';
		}

		return $single_template;

	}

	/**
	 * set post class for all listings
	 * @param post_class $classes post class based on taxonomy
	 *
	 * @since  1.1.0
	 */
	public function set_post_class( $classes ) {
		global $post;
		$terms = wp_get_object_terms( $post->ID, 'status' );
		foreach ( $terms as $term ) {
			$classes[] = $term->slug;
		}
		return $classes;
	}
}