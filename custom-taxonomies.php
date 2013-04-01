<?php
/*

  lt3-theme Custom Taxonomies

------------------------------------------------
  custom-taxonomies.php
  @version 2.0 | April 1st 2013
  @package lt3
  @author  Beau Charman | @beaucharman | http://beaucharman.me
  @link    https://github.com/beaucharman/lt3
  @licence GNU http://www.gnu.org/licenses/lgpl.txt

  For more information about registering Taxonomies:
  http://codex.wordpress.org/Function_Reference/register_taxonomy

  You can also turn the custom post types declarations into a plugin. for more information: http://codex.wordpress.org/Writing_a_Plugin

  To declare a taxonomy, simply add a taxonomy array to the $lt3_custom_taxonomies array variable, with required values of:
  array(
    'slug_singuar'          => '',
    'slug_plural'           => '',
    'name_singular'         => '',
    'name_plural'           => '',
    // and optional values of:
    'public'                => true,
    'show_in_nav_menus'     => true,
    'show_ui'               => true,
    'show_tagcloud'         => true,
    'hierarchical'          => true,
    'update_count_callback' => NULL,
    'query_var'             => true,
    'rewrite'               => '',
    'capabilities'          => array(),
    'sort'                  => NULL,
    'post_type'             => array('')
  )
------------------------------------------------ */

/*

  Declare Taxonomies

------------------------------------------------ */
$lt3_custom_taxonomies = array();

/*

  Register Taxonomies

------------------------------------------------ */
add_action('init', 'lt3_register_taxonomies', 0);
function lt3_register_taxonomies()
{
  global $lt3_custom_taxonomies;
  foreach($lt3_custom_taxonomies as $ct)
  {
    $labels = array(
      'name'                  => __($ct['name_plural'], $ct['name_plural'] . ' general name'),
      'singular_name'         => __($ct['name_singular'], $ct['name_singular'] . ' singular name'),
      'search_items'          => __('Search ' . $ct['name_plural']),
      'all_items'             => __('All ' . $ct['name_plural']),
      'parent_item'           => __('Parent ' . $ct['name_singular']),
      'parent_item_colon'     => __('Parent '. $ct['name_singular'] .':'),
      'edit_item'             => __('Edit '. $ct['name_singular']),
      'update_item'           => __('Update ' . $ct['name_singular']),
      'add_new_item'          => __('Add New ' . $ct['name_singular']),
      'new_item_name'         => __('New ' . $ct['name_singular']),
      'menu_name'             => __($ct['name_plural'])
    );
    register_taxonomy($ct['slug_singular'], $ct['post_type'], array(
      'labels'                => $labels,
      'public'                => ($ct['public'])                ? $ct['public'] : true,
      'show_in_nav_menus'     => ($ct['show_in_nav_menus'])     ? $ct['show_in_nav_menus'] : true,
      'show_ui'               => ($ct['show_ui'])               ? $ct['show_ui'] : true,
      'show_tagcloud'         => ($ct['show_tagcloud'])         ? $ct['show_tagcloud'] : true,
      'show_admin_column'     => ($ct['show_admin_column'])     ? $ct['show_admin_column'] : false,
      'hierarchical'          => ($ct['hierarchical'])          ? $ct['hierarchical'] : false,
      'update_count_callback' => ($ct['update_count_callback']) ? $ct['update_count_callback'] : null,
      'query_var'             => ($ct['query_var'])             ? $ct['query_var'] : $ct['slug_plural'],
      'rewrite'               => ($ct['rewrite'])               ? $ct['rewrite'] : true,
      'capabilities'          => ($ct['capabilities'])          ? $ct['capabilities'] : array(),
      'sort'                  => ($ct['sort'])                  ? $ct['sort'] : null
    ));
  }
}

/*

  Create Taxonomy drop downs for all post types

------------------------------------------------ */
add_action('restrict_manage_posts', 'lt3_todo_restrict_manage_posts');
function lt3_todo_restrict_manage_posts()
{
  global $typenow;
  $args=array('public' => true, '_builtin' => false);
  $post_types = get_post_types($args);
  if (in_array($typenow, $post_types))
  {
    $filters = get_object_taxonomies($typenow);
    foreach ($filters as $tax_slug)
    {
      $tax_obj = get_taxonomy($tax_slug);
      wp_dropdown_categories(array(
        'show_option_all' => __('Show All '.$tax_obj->label),
        'taxonomy' => $tax_slug,
        'name' => $tax_obj->name,
        'orderby' => 'term_order',
        'selected' => $_GET[$tax_obj->query_var],
        'hierarchical' => $tax_obj->hierarchical,
        'show_count' => false,
        'hide_empty' => true
      ));
    }
  }
}

add_filter('parse_query','lt3_todo_convert_restrict');
function lt3_todo_convert_restrict($query)
{
  global $pagenow,  $typenow;
  if ($pagenow=='edit.php')
  {
    $filters = get_object_taxonomies($typenow);
    foreach ($filters as $tax_slug)
    {
      $var = &$query->query_vars[$tax_slug];
      if (isset($var))
      {
        $term = get_term_by('id',$var,$tax_slug);
        $var = $term->slug;
      }
    }
  }
}