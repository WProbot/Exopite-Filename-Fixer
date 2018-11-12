<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.joeszalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Filename_Fixer
 * @subpackage Exopite_Filename_Fixer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Exopite_Filename_Fixer
 * @subpackage Exopite_Filename_Fixer/includes
 * @author     Joe Szalai <joe@joeszalai.org>
 */
class Exopite_Filename_Fixer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Exopite_Filename_Fixer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'exopite-filename-fixer';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Exopite_Filename_Fixer_Loader. Orchestrates the hooks of the plugin.
	 * - Exopite_Filename_Fixer_i18n. Defines internationalization functionality.
	 * - Exopite_Filename_Fixer_Admin. Defines all hooks for the admin area.
	 * - Exopite_Filename_Fixer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-filename-fixer-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-filename-fixer-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-exopite-filename-fixer-admin.php';

		$this->loader = new Exopite_Filename_Fixer_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Exopite_Filename_Fixer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Exopite_Filename_Fixer_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Exopite_Filename_Fixer_Admin( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Maybe later add options?
		 */
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		/**
		 * START FOR DEMONSTRATION PURPOSES ONLY
		 */

		/**
		 * Sanitize file name.
		 *
		 * This will work only not for Images.
		 * With this _wp_attachment_metadata on upload will not be generated by WordPress.
		 */
		// $this->loader->add_action( 'add_attachment', $plugin_admin, 'manage_attachment' );

		/**
		 * Sanitize filename on upload. This will not check if filename is unique.
		 *
		 * @link https://developer.wordpress.org/reference/hooks/wp_unique_filename/
		 * @link https://core.trac.wordpress.org/browser/tags/4.9.8/src/wp-includes/functions.php#L0
		 */
		// $this->loader->add_filter( 'wp_unique_filename', $plugin_admin, 'wp_unique_filename', 10, 4 );

		/**
		 * END FOR DEMONSTRATION PURPOSES ONLY
		 */

		/**
		 * Sanitize filename.
		 *
		 * WordPress build in sanitize_file_name will not take care umlauts.
		 * This generate sometime some issues with urls and filenames.
		 */
		$this->loader->add_filter( 'sanitize_file_name', $plugin_admin, 'sanitize_file_name', 10, 2 );

		/**
		 * Automatically Set the WordPress Image Title, Alt-Text & Other Meta
		 *
		 * @link https://brutalbusiness.com/automatically-set-the-wordpress-image-title-alt-text-other-meta/
		 */
		$this->loader->add_action( 'add_attachment', $plugin_admin, 'auto_image_details' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Exopite_Filename_Fixer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}