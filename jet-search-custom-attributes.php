<?php
/**
 * Plugin Name: JetSearch – Search by Custom Attributes
 * Description: Enables JetSearch to search by custom WooCommerce product attributes.
 * Version: 1.0
 * Author: Crocoblock
 * Author URI: https://crocoblock.com/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: jet-search-custom-attributes
 *
 * @package jet-search-custom-attributes
 * @author  Crocoblock
 * @license GPL-2.0+
 * @copyright 2025, Crocoblock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', array( 'Jet_Search_Custom_Attributes', 'init' ) );

class Jet_Search_Custom_Attributes {

	/**
	 * Initialize the plugin only if Jet_Search is available.
	 *
	 * @return void
	 */
	public static function init() {
		if ( ! class_exists( 'Jet_Search' ) ) {
			return;
		}

		$instance = new self();
		$instance->register_hooks();
	}

	/**
	 * Register all necessary filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'jet_search/extend_taxonomies_with_custom_attributes', array( $this, 'add_custom_attribute_taxonomies' ) );
		add_filter( 'jet_search/custom_attribute_search_ids', array( $this, 'search_custom_attributes' ), 10, 4 );
	}

	/**
	 * Add custom attributes defined via filter to the JetSearch taxonomy list.
	 *
	 * @param array $taxonomies
	 * @return array
	 */
	public function add_custom_attribute_taxonomies( $taxonomies ) {
		$custom = apply_filters( 'jet_search/custom_attributes_list', [] );

		foreach ( $custom as $key => $label ) {
			$taxonomies[ 'attribute_' . $key ] = $label;
		}

		return $taxonomies;
	}

	/**
	 * Search in custom attributes stored in _product_attributes post meta.
	 *
	 * @param array  $ids         Array of current matched post IDs.
	 * @param string $search      Search string.
	 * @param array  $taxonomies  Selected "taxonomies" (attribute keys like attribute_quality).
	 * @param array  $settings    JetSearch widget settings.
	 *
	 * @return array Modified post ID list with matches found in custom attributes.
	 */
	public function search_custom_attributes( $ids, $search, $taxonomies, $settings ) {
		global $wpdb;

		if ( empty( $search ) || empty( $taxonomies ) ) {
			return $ids;
		}

		$search = strtolower( $search );
		$post_ids = [];

		$raw_meta = $wpdb->get_results( "
			SELECT post_id, meta_value
			FROM {$wpdb->postmeta}
			WHERE meta_key = '_product_attributes'
		", ARRAY_A );

		if ( empty( $raw_meta ) ) {
			return $ids;
		}

		foreach ( $raw_meta as $row ) {
			$attributes = maybe_unserialize( $row['meta_value'] );

			if ( ! is_array( $attributes ) ) {
				continue;
			}

			foreach ( $taxonomies as $key ) {
				$attr_name = str_replace( 'attribute_', '', $key );

				if ( isset( $attributes[ $attr_name ] ) ) {
					$value = $attributes[ $attr_name ]['value'] ?? '';
					$value_normalized = strtolower( trim( $value ) );

					if ( stripos( $value_normalized, $search ) !== false ) {
						$post_ids[] = $row['post_id'];
						break;
					}
				}
			}
		}

		if ( ! empty( $post_ids ) ) {
			return array_merge( $ids, $post_ids );
		}

		return $ids;
	}
}
