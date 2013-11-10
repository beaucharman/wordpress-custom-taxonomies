# WordPress Custom Taxonomies

> A PHP class to help register and maintain WordPress custom taxonomies. It also comes with some rad built-in properties and methods that can be used in templates to maintain clean code and modular development.

For more information about registering taxonomies, including a full list of options, visit the [WordPress Codex](http://codex.wordpress.org/Function_Reference/register_taxonomy).

This class works well with the [WordPress Custom Post Type class](https://github.com/beaucharman/wordpress-custom-post-type). They were made for each other <3.

### Declaring New Taxonomies

Include `custom-taxonomy.php` in your `functions.php` file.

Declare the various argument arrays to setup the new taxonomy as needed (`name` and `post_type` are required):

```php
// required
$args['name'] = '';

// The post type/s that the taxonomy is connected to.
// String or array
$args['post_type'] = '';

// optional
$args['labels'] = array(
  'label_singular'        => '',
  'label_plural'          => '',
  'menu_label'            => ''
 );

 $args['options'] = array(
  'public'                => true,
  'show_ui'               => true,
  'show_in_nav_menus'     => true,
  'show_tagcloud'         => true,
  'hierarchical'          => false,
  'update_count_callback' => null,
  'query_var'             => true,
  'rewrite'               => true,
  'capabilities'          => array(),
  'sort'                  => null
 );

$args['help'] = '';
```

Then create a variable (for future reference, but is not required) from an instance of the Bamboo_Custom_Taxonomy class:

```php
$Taxonomy = new Bamboo_Custom_Taxonomy($args);
```

You can even quick declare a Taxonomy by just passing the name and associated post type two strings. So for a Taxonomy of of *genre* for the post type *movie*, just pass:

```PHP
$Genre = new Bamboo_Custom_Taxonomy('genre', 'movie');
```

### Usage

The custom taxonomy class creates a handfull of useful properties and methods that can be accessed through the taxonomy's instance variable and can be used in template and admin files.

#### Properties

**`$Taxonomy->name`**

The taxonomy slug.

**`$Taxonomy->lables`**

An array of the singular, plural and menu lables.

#### Methods

**`$Taxonomy->get()`**

Get all the terms assigned to this taxonomy. Accepts an array of arguments, and a boolean value to retrieve just a single value (true, useful to use along side `'include' => array( $single_id )` ) or an array of results (false).

For example:

```php
$taxonomies = $Taxonomy->get();
```

**Note:** A declaration of `global $Taxonomy;` might be required on some template files.

See the [Get Terms => Parameters](http://codex.wordpress.org/Function_Reference/get_terms#Parameters) codex reference for the list of possible arguments, and the [Get Terms => Return Values](http://codex.wordpress.org/Function_Reference/get_terms#Return_Values) codex reference for the list of return values.
