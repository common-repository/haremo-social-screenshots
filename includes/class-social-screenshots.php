<?php

class Social_Screenshots {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {

		$this->plugin_name = 'social-screenshots';
		$this->version = '0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-social-screenshots-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-social-screenshots-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-social-screenshots-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-social-screenshots-admin.php';
		$this->loader = new Social_Screenshots_Loader();
	}

	private function set_locale() {
		$plugin = new Social_Screenshots_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin = new Social_Screenshots_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'post_submitbox_misc_actions', $plugin, 'add_to_publishbox');
		$this->loader->add_action( 'parse_request', $plugin, 'parse_request');
		$this->loader->add_action( 'save_post', $plugin, 'save_post');
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin, 'enqueue_scripts' );
	}

	private function define_public_hooks() {
		$plugin = new Social_Screenshots_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_head', $plugin, 'buffer_start', 0);
		$this->loader->add_action( 'wp_head', $plugin, 'buffer_end', 999);
		$this->loader->add_action( 'wp_head', $plugin, 'add_opengraph_image', 1000);
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
