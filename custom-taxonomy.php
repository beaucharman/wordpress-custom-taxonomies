<?php
/**
 * Bamboo - WordPress Custom Taxonomy
 * ========================================================================
 * custom-taxonomy.php
 * @version   2.0 | June 30th 2013
 * @author    Beau Charman | @beaucharman | http://www.beaucharman.me
 * @link      https://github.com/beaucharman/wordpress-custom-taxonomies
 * @license   MIT license
 *
 * Properties
 *  $Taxonomy->name   {string}
 *  $Taxonomy->lables {array}
 *
 * Methods
 *  $Taxonomy->get()
 *
 * To declare a custom taxonomy, simply create a new instance of the
 * Bamboo_Custom_Taxonomy class.
 *
 * Configuration guide:
 * https://github.com/beaucharman/wordpress-custom-taxonomies
 *
 * For more information about registering Taxonomies:
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 *
 * #Protip
 * If referencing a custom taxonomy and you receive an 'invalid taxonomy'
 * error (mostly likely outside of template files), run your code *after* the
 * init function with an action hook.
 */



/* ========================================================================
   Custom Taxonomy class
   ======================================================================== */



class Bamboo_Custom_Taxonomy
{

	public $name;
	public $post_type;
	public $labels;
	public $options;
	public $help;


	/**
	 * Constructor
	 * ========================================================================
	 * @param  {array}    $args
	 * @param  {string}   $post_type
	 * @return {instance} taxonomy
	 */
	public function __construct($args, $post_type = null)
	{
		/**
		 * Set class values
		 */
		if (! is_array($args))
		{
			$name = $args;
			$args = array();
		}
		else
		{
			$name = $args['name'];
			$post_type = $args['post_type'];
		}

		$args = array_merge(
			array(
				'name'    => $this->uglify_words($name),
				'post_type' => $post_type,
				'labels'  => array(),
				'options' => array(),
				'help'    => null
			),
			$args
		);

		$this->name = $args['name'];
		$this->post_type = $args['post_type'];
		$this->labels = $args['labels'];
		$this->options = $args['options'];
		$this->help = $args['help'];

		/**
		 * Create the labels where needed
		 */

		/* Taxonomy singluar label */
		if (! isset($this->labels['singular']))
		{
			$this->labels['singular'] = $this->prettify_words($this->name);
		}

		/* Taxonomy plural label */
		if (! isset($this->labels['plural']))
		{
			$this->labels['plural'] = $this->plurify_words($this->labels['singular']);
		}

		/* Taxonomy menu label */
		if (! isset($this->labels['menu']))
		{
			$this->labels['menu'] = $this->labels['plural'];
		}

		/**
		 * If the taxonomy doesn't already exist, create it!
		 */
		if (! taxonomy_exists($this->name))
		{
			add_action('init', array(&$this, 'register_custom_taxonomy'), 0);

			if ($this->help)
			{
				add_action('contextual_help',
				array(&$this, 'add_custom_contextual_help'), 10, 3);
			}
		}
	}



	/**
	 * Register Custom Taxonomy
	 * ========================================================================
	 * @param  {null}
	 * @return {object} taxonomy
	 */
	public function register_custom_taxonomy()
	{
		/**
		 * Set up the taxonomy labels
		 */
		$labels = array(
			'name'              => __($this->labels['plural'], $this->labels['plural'] . ' general name'),
			'singular_name'     => __($this->labels['singular'], $this->labels['singular'] . ' singular name'),
			'menu_name'         => __($this->labels['menu']),
			'search_items'      => __('Search ' . $this->labels['plural']),
			'all_items'         => __('All ' . $this->labels['plural']),
			'parent_item'       => __('Parent ' . $this->labels['singular']),
			'parent_item_colon' => __('Parent '. $this->labels['singular'] . ':'),
			'edit_item'         => __('Edit ' . $this->labels['singular']),
			'update_item'       => __('Update ' . $this->labels['singular']),
			'add_new_item'      => __('Add New ' . $this->labels['singular']),
			'new_item_name'     => __('New ' . $this->labels['singular']),
		);

		/**
		 * Configure the taxonomy options
		 */
		$options = array_merge(
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'rewrite'           => array('slug' => $this->get_slug()),
				'show_admin_column' => true
			),
			$this->options
		);

		/**
		 * Register the new taxonomy
		 */
		register_taxonomy($this->name, $this->post_type, $options);
	}



	/**
	 * Add Custom Contextual Help
	 * ========================================================================
	 * @param $contextual_help
	 * @param $screen_id
	 * @param $screen
	 */
	public function add_custom_contextual_help($contextual_help, $screen_id, $screen)
	{
		$context = 'edit-' . $this->name;

		if ($context == $screen->id)
		{
			$contextual_help = $this->help;
		}

		return $contextual_help;
	}



	/**
	 * Get
	 * ========================================================================
	 * @param  {array}   $user_args
	 * @param  {boolean} $single
	 * @return {object}  term data
	 */
	public function get($user_args = array(), $single = false)
	{
		$args = array_merge(
			array(
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false
			), $user_args
		);

		$items = get_terms($this->name, $args);

		if ($single)
		{
			return $items[0];
		}

		return $items;
	}



	/**
	 * Get Slug
	 * ========================================================================
	 * @param  {string} $name
	 * @return {string}
	 */
	public function get_slug($name = null)
	{
		if (! $name)
		{
			$name = $this->name;
		}

		return strtolower(str_replace(' ', '-', str_replace('_', '-', $name)));
	}



	/**
	 * Prettify Words
	 * ========================================================================
	 * @param  {string} $words
	 * @return {string}
	 *
	 * Creates a pretty version of a string, like
	 * a pug version of a dog.
	 */
	public function prettify_words($words)
	{
		return ucwords(str_replace('_', ' ', $words));
	}



	/**
	 * Uglify Words
	 * ========================================================================
	 * @param  {string} $word
	 * @return {string}
	 *
	 * creates a url firendly version of the given string.
	 */
	public function uglify_words($words)
	{
		return strToLower(str_replace(' ', '_', $words));
	}



	/**
	 * Plurify Words
	 * ========================================================================
	 * @param  {string} $words
	 * @return {string}
	 *
	 * Plurifies most common words. Not currently working
	 * proper nouns, or more complex words, for example
	 * knife -> knives, leaf -> leaves.
	 */
	public function plurify_words($words)
	{
		if (strToLower(substr($words, -1)) == 'y')
		{
			return substr_replace($words, 'ies', -1);
		}

		if (strToLower(substr($words, -1)) == 's')
		{
			return $words . 'es';
		}

		return $words . 's';
	}
}
