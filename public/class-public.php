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
    add_action('admin_post_customer_order_form_submit',[$this,'customer_order_form_submit']);
    add_action('admin_post_exit_customer_form_submit',[$this,'exit_customer_form_submit']);
    add_action('wp_ajax_gofc_search_product',[$this,'gofc_search_product']);
    add_action('woocommerce_before_calculate_totals', [$this,'customer_notice']);
    add_action('cfw_after_customer_info_tab_login','customer_notice',10, 3);
    add_action('cfw_checkout_after_login','customer_notice',10, 3);
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
    wp_localize_script( $this->plugin_name, 'my_gofc_object',
    array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
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

  public function new_account_page_content() { 
    ?>
    <div style="margin-bottom: 1rem;">
      <h1><?php echo $this->settings->affiliate_term ?> Customers</h1>
      <?php
      ob_start();
        require_once WP_PLUGIN_DIR."/gigfilliate-order-for-customer/public/views/gigfilliate_order_for_customer_page.php";
      echo ob_get_clean();
      ?>
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
  public function customer_order_form_submit(){
    if (isset($_POST['customer']) && $_POST['customer'] != null) {
      setcookie("GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER", $_POST['customer'], time() + (86400 * 30), "/");
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  public function exit_customer_form_submit(){
    if (isset($_POST['exit_customer'])) {
      setcookie("GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER",null, time() - 3600,"/");
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  public function customer_notice(){
    if(isset($_COOKIE["GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER"])) {
      $customer = get_user_by("ID", $_COOKIE["GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER"]);
      ?>
      <div class="alert alert-info" role="alert">
      You're placing an order for <?php echo $customer->first_name.' '.$customer->last_name;?>.
      </div>
      <?php
    }
  }

  public function gofc_search_product(){
    $args = array(
      'post_type' => 'product',
      'posts_per_page' => 15,
      'order' => 'ASC',
      's'=>$_GET["search"]
    );
    $to_return = [];
    foreach ((new WP_Query( $args ))->posts as $post) {
      $product = wc_get_product($post->ID);
      $to_return[] = [
        "id"=>$post->ID,
        "thumbnail_url"=>wp_get_attachment_url($product->get_image_id()),
        "name"=>$product->get_name(),
        "price"=>$product->get_regular_price(),
        "sku"=>$product->get_sku(),
        "add_to_cart_url"=>$product->add_to_cart_url(),
        "is_in_stock"=>$product->is_in_stock()
      ];
    }
    echo json_encode($to_return);
    wp_die();
  }
}



