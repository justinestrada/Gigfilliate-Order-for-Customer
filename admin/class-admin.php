<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://partners.vitalibis.com/login
 * @since      0.0.1
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/admin
 * @author     Gigfilliate <justin@justinestrada.com>
 */
class Gigfilliate_Order_For_Customer_Admin
{

  private $plugin_name;
  private $version;
  private $helpers;
  public $site_url;

  /**
   * Initialize the class and set its properties.
   *
   * @since    0.0.1
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct($plugin_name, $version, $helpers) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->helpers = $helpers;
    $this->site_url = get_site_url();
    add_action('admin_menu', [$this, 'admin_menu'], 20);
    add_action('vitalibis_settings_dashboard_row', [$this, 'vitalibis_settings_dashboard_row'], 10, 2);
    add_action('on_save_vitalibis_settings_dashboard_row', [$this, 'on_save_vitalibis_settings_dashboard_row'], 10, 2);
    add_filter('vitalibis_notification_template_tags', [$this, 'vitalibis_notification_template_tags'], 20, 2);
    add_action('gigfilliate_edit_affiliate_tabs', [$this, 'edit_affiliate_tabs'], 10, 2);
    add_action('gigfilliate_edit_affiliate_tab_content', [$this, 'edit_affiliate_tab_content'], 10, 2);
    add_action('wp_ajax_gofc_get_customers', [$this, 'ajax_get_customers']);
  }

  public function admin_menu() {
    add_submenu_page('vitalibis', 'Order For Customer', 'Order For Customer', 'manage_options', '/admin.php?page=vitalibis&tab=settings&status_view=dashboard');
  }

  public function vitalibis_settings_dashboard_row($settings) {
    ?>
    <tr>
      <th><label for="excluded_product_ids_from_order_for_customer">Excluded Products From Order For Customer</label></th>
      <td>
        <input type="text" id="excluded_product_ids_from_order_for_customer" name="excluded_product_ids_from_order_for_customer" class="large-text" value="<?php echo (isset($_POST['excluded_product_ids_from_order_for_customer'])) ? $_POST['excluded_product_ids_from_order_for_customer'] : (isset($settings->dashboard->excluded_product_ids_from_order_for_customer) ? $settings->dashboard->excluded_product_ids_from_order_for_customer : ''); ?>" />
        <p class="description">You can add ids of products here seprated by (,) . If you dont want them to be visible in orders for customer section.</p>
      </td>
    </tr>
    <?php
  }

  public function on_save_vitalibis_settings_dashboard_row($settings) {
    $settings->dashboard->excluded_product_ids_from_order_for_customer = isset($_POST['excluded_product_ids_from_order_for_customer']) ? $_POST['excluded_product_ids_from_order_for_customer'] : '';
  }

  public function vitalibis_notification_template_tags($template_tags, $notification) {
    if ($notification->slug == "new-customer-by-bp") {
      $template_tags['{site_name}'] = get_bloginfo('name'); // default value
      $template_tags['{site_url}'] = get_site_url(); // default value
      $template_tags['{affiliate_first_name}'] = 'Jane'; // test value
      $template_tags['{affiliate_last_name}'] = 'Doe'; // test value
      $template_tags['{affiliate_email}'] = 'janedoe@test.com'; // test value
      $template_tags['{new_user_email}'] = 'johndoe@test.com'; // test value
      $template_tags['{password_change_url}'] = get_site_url().'/wp-login.php?action=lostpassword'; // default value
    }
    return $template_tags;
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    0.0.1
   */
  public function enqueue_styles() {
    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    0.0.1
   */
  public function enqueue_scripts() {
    wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, false);
  }

  public function edit_affiliate_tabs() {
    $affiliate_tab = isset($_GET['affiliate_tab']) ? $_GET['affiliate_tab']: 'details'; ?>
    <a href="<?php echo $this->site_url; ?>/wp-admin/admin.php?page=vitalibis&tab=affiliates&affiliate_id=<?php echo $_GET['affiliate_id']; ?>&action=edit&affiliate_tab=customers" class="nav-tab <?php echo ($affiliate_tab === 'customers') ? 'nav-tab-active' : ''; ?>">Customers</a>
    <?php
  }

  public function edit_affiliate_tab_content() {
    $affiliate_id = (int)$_GET['affiliate_id'];
    $affiliate_user_id = vitalibis_get_user_id_by_affiliate_id( $affiliate_id );
    $affiliate_tab = isset($_GET['affiliate_tab']) ? $_GET['affiliate_tab']: 'details';
    if ($affiliate_tab === 'customers') { ?>
      <div id="postbox-container-2" class="postbox-container">
        <div id="normal-sortables" class="meta-box-sortables ui-sortable">
          <div class="postbox">
            <div class="inside" style="overflow: auto;">
              <?php
              require_once( plugin_dir_path( __FILE__ ) . 'partials/edit/customers.php' ); ?> 
            </div> 
          </div>
        </div>
      </div>
      <?php
    }
  }

  public function ajax_get_customers() {
    $res = array( 'success' => false );
    if (!isset($_POST['action']) || $_POST['action'] !== 'gofc_get_customers') {
      exit(json_encode($res));
    }
    if (!isset($_POST['affiliate_user_id'])) {
      $res['msg'] = 'Affiliate ID is required.';
      exit(json_encode($res));
    }
    $affiliate_user_id = $_POST['affiliate_user_id'];
    $offset = (isset($_POST['offset'])) ? $_POST['offset'] : false;
    $limit = (isset($_POST['limit'])) ? $_POST['limit'] : 10;
    $res['customers_data'] = $this->helpers->get_customers( $affiliate_user_id, false, $limit, $offset );
    // $res['success'] = !empty($res['customers_data']['customers']) ? true : false;
    $res['success'] = true;
    exit(json_encode($res));
  }
}
