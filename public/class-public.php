<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://partners.vitalibis.com/login
 * @since      0.0.1
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/public
 * @author     Gigfilliate <justin@justinestrada.com>
 */
class Gigfilliate_Order_For_Customer_Public {

	private $plugin_name;
	private $version;
  public $settings;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
    $this->settings = json_decode(get_option('vitalibis_settings'));
    $this->new_account_page();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );
	}

  public function new_account_page() {
    // $affiliate_term_slug = str_replace(' ', '-', strtolower($this->settings->affiliate_term)); // TODO: the page url should use the affiliate_term_slug
		// Register new endpoint to use for My Account page
		// Any change here resave Permalinks or it will give 404 error
		add_action( 'init', function () {
			add_rewrite_endpoint( 'brand-partner-customers', EP_ROOT | EP_PAGES );
		});
    // Add new query var
    add_filter( 'query_vars', function ( $vars ) {
      $vars[] = 'brand-partner-customers';
      return $vars;
    }, 0 );
    // Insert the new endpoint into the My Account menu
    add_filter( 'woocommerce_account_menu_items', function ( $menu_links ) {
      $new_menu_links = array(
        'brand-partner-customers' => $this->settings->affiliate_term . ' Customers'
      );
      $menu_links = array_slice( $menu_links, 0, 1, true ) 
        + $new_menu_links 
        + array_slice( $menu_links, 1, NULL, true );
      return $menu_links;
    });
    // Add content to the new endpoint
    add_action( 'woocommerce_account_brand-partner-customers_endpoint', function () {
      $this->new_account_page_content();
    });
  }

  public function new_account_page_content() { ?>
    <div style="margin-bottom: 1rem;">
      <h1><?php echo $this->settings->affiliate_term ?> Customers</h1>
      <div>
      </div>
    </div>
    <?php
  }
}




  
