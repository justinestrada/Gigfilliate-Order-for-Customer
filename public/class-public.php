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
  public $cookie_name = "GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER";


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
    add_action('woocommerce_before_cart_contents', [$this,'customer_notice']);
    add_action('cfw_after_customer_info_tab_login',[$this,'customer_notice'],10, 3);
    add_action('cfw_checkout_after_login',[$this,'customer_notice'],10, 3);
    add_action("wp_footer",array($this,"toast"));
    add_action("xoo_wsc_cart_after_head",[$this,'customer_notice'],10, 3);
    add_action('woocommerce_checkout_update_order_meta',[$this,'woocommerce_checkout_update_order_meta']);
    add_action("woocommerce_admin_order_data_after_billing_address",[$this,'woocommerce_admin_order_data_after_billing_address'],10,1);
    add_action("woocommerce_checkout_process",[$this,'woocommerce_checkout_process']);
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
      setcookie($this->cookie_name, $_POST['customer'], (time() + (86400 * 30)), "/");
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  public function exit_customer_form_submit(){
    if (isset($_POST['exit_customer'])) {
      setcookie($this->cookie_name,null, time() - 3600,"/");
    }
    header('Location: ' . $_SERVER['HTTP_REFERER']);
  }
  public function customer_notice(){
    if(isset($_COOKIE[$this->cookie_name])) {
      $customer = get_user_by("email", $_COOKIE[$this->cookie_name]);
      ?>
      <div class="alert alert-info" role="alert">
      You're placing an order for <?php echo ($customer != null?($customer->first_name.' '.$customer->last_name):$_COOKIE[$this->cookie_name]);?>.
      </div>
       <input type="hidden" name="new_billing_email" value="<?php echo ($customer != null?$customer->user_email:$_COOKIE[$this->cookie_name]);?>">
      <?php
    }
  }

  public function gofc_search_product(){
    $settings = json_decode(get_option('vitalibis_settings'));
    $args = array(
      'post_type' => 'product',
      'posts_per_page' => 15,
      'order' => 'ASC',
      's'=>$_GET["search"]
    );
    if(isset($settings->dashboard->excluded_product_ids_from_order_for_customer) && $settings->dashboard->excluded_product_ids_from_order_for_customer != null){
      $args['post__not_in'] = explode(",",$settings->dashboard->excluded_product_ids_from_order_for_customer);
    }
    
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
    exit( json_encode($to_return) );
  }
  public function toast(){
    if(!isset($_COOKIE[$this->cookie_name])) {
      return;
    }
    $user = get_user_by("email",$_COOKIE[$this->cookie_name]);
    if($user != null){
      $customer = new WC_Customer($user->ID);
      ?>
      <span id="gofc_customer_billing" 
      data-email="<?php echo $customer->get_billing_email();?>"
      data-firstName="<?php echo $customer->get_billing_first_name();?>"
      data-lastName="<?php echo $customer->get_billing_last_name();?>"
      data-company="<?php echo $customer->get_billing_company();?>"
      data-address1="<?php echo $customer->get_billing_address_1();?>"
      data-address2 ="<?php echo $customer->get_billing_address_2();?>"
      data-city="<?php echo $customer->get_billing_city();?>"
      data-state="<?php echo $customer->get_billing_state();?>"
      data-postcode="<?php echo $customer->get_billing_postcode();?>"
      data-country="<?php echo $customer->get_billing_country();?>"
      data-phone="<?php echo $customer->get_billing_phone();?>"
      ></span>
      <?php
    }else{
      ?>
      <span id="gofc_customer_billing" 
      data-email="<?php echo $_COOKIE[$this->cookie_name];?>"
      ></span>
      <?php
    }
      ?>

  <div class="toast ml-auto GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER_DELETE" data-autohide="false">
    <div class="toast-header">
      <strong class="mr-auto text-primary">Exited</strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
    </div>
    <div class="toast-body">
      You have been exited from the Order for Customer mood.
    </div>
  </div>
    <?php
  }
  public function woocommerce_checkout_process() {
    if(isset($_POST['new_billing_email']) && $_POST['new_billing_email'] != null){
      if(!email_exists($_POST['new_billing_email'])){
        $arr = explode("/", $_POST['new_billing_email'], 2);
        $login_name = $arr[0];
        wp_create_user($login_name, md5(time()."_temp"), $_POST['new_billing_email']);
        wp_mail($_POST['new_billing_email'], "New Order And Account", "BP Ordered for you and we created your account please set your password by going on this link. https://radicalskincare.com/wp-login.php?action=lostpassword");
      }
    }
}
  public function woocommerce_checkout_update_order_meta($order_id){
    if ( isset($_POST['new_billing_email']) ) {
      update_post_meta( $order_id, 'ordered_by', wp_get_current_user()->user_email); 
      update_post_meta( $order_id, '_customer_user', esc_attr(get_current_user_id()));
    }
  }

  public function woocommerce_admin_order_data_after_billing_address($order) {
    $ordered_by = get_post_meta( $order->get_id(), 'ordered_by', true ); 
    if ($ordered_by) {
      ?>
      <p>
        <strong>Ordered By</strong><br>
        <?php echo $ordered_by; ?>
      </p>
      <?php
    }
  }
}



