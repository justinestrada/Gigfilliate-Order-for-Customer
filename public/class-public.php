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
        <p>TODO:</p>
        <h3>Get Customers</h3>
        <ul>
          <li>Get current user affiliate id</li>
          <li>Get Orders where v_order_affiliate_id = affiliate_id</li>
          <li>Get Customer Details from Orders</li>
        </ul>
        <h3>List Customers</h3>
        <ul>
          <li>Filter customers</li>
          <li>
            Customers listing
            <br>
            <ul>
              <li>Full Name, Email, Button [Place Order]</li>
            </ul>
          </li>
          <li>Add Customer Button</li>
        </ul>
        <h3>Place Order For Customer</h3>
        <ul>
          <li>On Click Place Order (Do you think we should use usermeta or a cookie)</li>
          <!-- <li>Update current logged in user meta, ex<br>
            <pre>
            update_user_meta($current_user_id, 'v_placing_order_for_customer', $customer_user_id);
            </pre>
          </li> -->
          <li>
            Create a Cookie 'GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER' store customer_user_id
          </li>
          <li>
            Show a notice and show an exit [Exit 'Place Order For Customer' Mode] button
            <br>
            "You're placing an order for {CUSTOMER_NAME}. You can use your {Affiliate_Term} customer coupon, but not your personal {Affiliate_Term} coupon."
          </li>
          <li>
            [Exit 'Place Order For Customer' Mode]
            <br>
            <ul>
              <li>On click - delete cookie 'GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER'</li>
            </ul>
            <!-- <ul>
              <li>On click - set the user meta 'v_placing_order_for_customer' to null</li>
            </ul> -->
          </li>
          <li>
            List all products (We'll need admin settings for this to exclude products)
            <br>
            <ul>
              <li>Product Name, Price & Add To Cart (No subscription options)</li>
            </ul>
          </li>
          <li>In the Right Sidebar Cart there should also be a big notice saying "You're placing an order for {CUSTOMER_NAME}."...</li>
        </ul>
        <p>Okay that's a lot of instructions for now.</p>
      </div>
    </div>
    <?php
  }
}




  
