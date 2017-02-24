<?php

if (!class_exists('Shopme_Woo_Config')) {

	class Shopme_Woo_Config extends Shopme_Custom_Content_Types_and_Taxonomies {

		function __construct() {

			add_action('shopme_pre_import_hook', array($this, 'woocommerce_import_start'));

		}

		function woocommerce_import_start() {
			global $wpdb;

			$file = self::$view_path['XML_PATH'] . 'default.xml';

			$parser      = new WXR_Parser();
			$import_data = $parser->parse( $file );

			if ( isset( $import_data['posts'] ) ) {
				$posts = $import_data['posts'];

				if ( $posts && sizeof( $posts ) > 0 ) foreach ( $posts as $post ) {

					if ( $post['post_type'] == 'product' ) {

						if ( $post['terms'] && sizeof( $post['terms'] ) > 0 ) {

							foreach ( $post['terms'] as $term ) {

								$domain = $term['domain'];

								if ( strstr( $domain, 'pa_' ) ) {

									// Make sure it exists!
									if ( ! taxonomy_exists( $domain ) ) {

										$nicename = strtolower(sanitize_title(str_replace('pa_', '', $domain)));

										$exists_in_db = $wpdb->get_var("SELECT attribute_id FROM ".$wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '".$nicename."';");

										if (!$exists_in_db) :

											// Create the taxonomy
											$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", array( 'attribute_name' => $nicename, 'attribute_type' => 'select' ), array( '%s', '%s' ) );

										endif;

										// Register the taxonomy now so that the import works!
										register_taxonomy( $domain,
											array('product'),
											array(
												'hierarchical' => true,
												'labels' => array(
													'name' => $nicename,
													'singular_name' => $nicename,
													'search_items' =>  esc_html__( 'Search', 'shopme_app_textdomain') . ' ' . $nicename,
													'all_items' => esc_html__( 'All', 'shopme_app_textdomain') . ' ' . $nicename,
													'parent_item' => esc_html__( 'Parent', 'shopme_app_textdomain') . ' ' . $nicename,
													'parent_item_colon' => esc_html__( 'Parent', 'shopme_app_textdomain') . ' ' . $nicename . ':',
													'edit_item' => esc_html__( 'Edit', 'shopme_app_textdomain') . ' ' . $nicename,
													'update_item' => esc_html__( 'Update', 'shopme_app_textdomain') . ' ' . $nicename,
													'add_new_item' => esc_html__( 'Add New', 'shopme_app_textdomain') . ' ' . $nicename,
													'new_item_name' => esc_html__( 'New', 'shopme_app_textdomain') . ' ' . $nicename
												),
												'show_ui' => false,
												'query_var' => true,
												'rewrite' => array( 'slug' => strtolower(sanitize_title($nicename)), 'with_front' => false, 'hierarchical' => true ),
											)
										);

									}
								}
							}

						}
					}
				}
			}

		}

	}

}

