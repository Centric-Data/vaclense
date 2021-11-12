<?php

/**
 * Vacancy-Lense
 *
 * @package     Vacancy-Lense
 * @author      Centric Data
 * @copyright   2021 Centric Data
 * @license     GPL-2.0-or-later
 *
*/
/*
Plugin Name: Vacancy-Lense
Plugin URI:  https://github.com/Centric-Data/vaclense
Description: This is a vacancy listing plugin, when activated allows shows a list of vacancies posted
Author: Centric Data
Version: 1.0.0
Author URI: https://github.com/Centric-Data
Text Domain: vaclense
*/
/*
Vacancy-Lense is free software: you can redistribute it and/or modify it under the terms of GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version.

Vacancy-Lense Form is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Contact-Lense Form.
*/

/* Exit if directly accessed */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define variable for path to this plugin file.
define( 'VL_LOCATION', dirname( __FILE__ ) );
define( 'VL_LOCATION_URL' , plugins_url( '', __FILE__ ) );
define( 'VL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 *
 */
class VacancyLense
{

  public function __construct()
  {
    // Add assets (js, css)
    add_action( 'wp_enqueue_scripts', array( $this, 'vl_load_assets' ) );

    // Register Custom Boxes
    add_action( 'add_meta_boxes', array( $this, 'vl_custom_meta_boxes' ) );

    // Add shortcode
    add_shortcode( 'vacancy-lense', array( $this, 'vl_load_shortcode' ) );

    // Register REST Route
    add_filter( 'rest_route_for_post', array( $this, 'vl_rest_route_cpt' ), 10, 2 );

    // Register a CPT
    add_action( 'init', array( $this, 'vl_vacancy_cpt' ) );

    // CPT Custom Columns
    add_filter( 'manage_centric_vacancy_posts_columns', array( $this, 'vl_vacancy_columns' ) );

    // Save Meta Box Data
    add_action( 'save_post', array( $this, 'vl_save_meta_box' ) );

    // Fetch Meta Data
    add_action( 'manage_centric_vacancy_posts_custom_column', array( $this, 'vl_custom_column_data' ), 10, 2 );
  }

  // Enqueue scripts
  public function vl_load_assets(){
    wp_enqueue_style( 'vaclense-css', VL_PLUGIN_URL . 'css/vaclense.css', [], time(), 'all' );
    wp_enqueue_script( 'vaclense-js', VL_PLUGIN_URL . 'js/vaclense.js', ['jquery'], time(), 1 );
  }

  // Create meta Boxes
  public function vl_custom_meta_boxes(){
    add_meta_box( 'vacancy_fields', __( 'Position Details', 'vaclense' ), array( $this, 'vl_render_details' ), 'centric_vacancy', 'advanced', 'high' );
  }

  // Render Meta-boxes html
  public function vl_render_details( $post ){
    include( VL_LOCATION . '/inc/box_forms.php' );
  }

  // Render shortcode
  public function vl_load_shortcode(){
    include( VL_LOCATION . '/inc/shortcodehtml.php' );
  }

  // Register a Route
  public function vl_rest_route_cpt( $route, $post ){
    if( $post->post_type === 'centric_vacancy' ){
      $route = '/wp/v2/vacancies/' . $post->ID;
    }
    return $route;
  }

  // Create a CPT
  public function vl_vacancy_cpt(){
    $labels = array(
      'name'            =>  _x( 'Vacancies', 'Post type general name', 'vaclense' ),
      'singular'        =>  _x( 'Vacancy', 'Post type singular', 'vaclense' ),
      'menu_name'       =>  _x( 'Vacancies', 'Admin Menu Text', 'vaclense' ),
      'name_admin_bar'  =>  _x( 'Vacancy', 'Add New on Toolbar', 'vaclense' ),
      'add_new'         =>  __( 'Add New', 'vaclense' ),
      'add_new_item'    =>  __( 'Add New Vacancy', 'vaclense' ),
      'new_item'        =>  __( 'New Vacancy' ),
      'edit_item'       =>  __( 'Edit Vacancy', 'vaclense' ),
      'view_item'       =>  __( 'View Vacancy', 'vaclense' ),
      'all_items'       =>  __( 'All Vacancies', 'vaclense' ),
    );
    $args       = array(
      'labels'              =>  $labels,
      'public'              =>  true,
      'has_archive'         =>  'centric_vacancy',
      'rewrite'             =>  array(
        'slug'              =>  'centric_vacancy/vacancies',
        'with_front'        =>  FALSE
      ),
      'hierarchical'        =>  false,
      'show_in_rest'        =>  true,
      'taxonomies' => array('post_tag'),
      'rest_base'           =>  'vacancies',
      'rest_controller_class' =>  'WP_REST_Posts_Controller',
      'supports'              =>  array( 'title', 'editor' ),
      'capability_type'       =>  'post',
      'menu_icon'             =>  'dashicons-groups'
    );
    register_post_type( 'centric_vacancy', $args );
  }

  // Register taxonomies
  public function vl_tag_taxonomies(){

  }

  // Custom Vacancy CPT Columns
  public function vl_vacancy_columns( $columns ){
    $newColumns = array();
      $newColumns['title']        = 'Position';
      $newColumns['details']      = 'Job Description';
      $newColumns['organisation'] = 'Organisation';
      $newColumns['tags']         = 'Tags';
      $newColumns['date']         = 'Date';

      return $newColumns;
  }

  // Save data from boxes
  public function vl_save_meta_box( $post_id ){
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if( $parent_id = wp_is_post_revision( $post_id ) ){
      $post_id = $parent_id;
    }
    $fields = [
      'vacancy_org',
      'vacancy_pay'
    ];
    foreach( $fields as $field ){
      if( array_key_exists( $field, $_POST ) ){
        update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
      }
    }
  }

  // Fetch and populate vacancy data
  public function vl_custom_column_data( $column, $post_id ){
    switch ( $column ) {
      case 'details':
        echo get_the_excerpt();
        break;
      case 'organisation':
        $org = get_post_meta( get_the_ID(), 'vacancy_org', true );
        echo $org;
        break;
      default:
        echo "Data hidden";
        break;
    }
  }

}

new VacancyLense;



?>
