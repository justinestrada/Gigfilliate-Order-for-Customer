<?php

/**
 * Fired during plugin activation
 *
 * @link       https://partners.vitalibis.com/login
 * @since      0.0.1
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/includes
 * @author     Gigfilliate <justin@justinestrada.com>
 */
class Gigfilliate_Order_For_Customer_Activator {
  
  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    0.0.1
   */
  public static function activate()
  {
    Gigfilliate_Order_For_Customer_Activator::notification_settings();
  }

  public static function notification_settings()
  {
    $notification_settings = json_decode(get_option('vitalibis_notification_settings'));
    $new_customer_by_bp = null;
    foreach ($notification_settings->notifications as $notification) {
      if ($notification->slug == "new-customer-by-bp") {
        $new_customer_by_bp = $notification;
      }
    }
    if ($new_customer_by_bp == null) {
      $notification_settings->notifications[] = [
        "enabled" => 1,
        "slug" => "new-customer-by-bp",
        "name" => "New Order And Account",
        "description" => "Notify customer when BP create account for them.",
        "subject" => "Your account is created on our site because BP placed order for you.",
        "message" => "{affiliate_first_name} {affiliate_last_name} creted an account for you on our website <a href='{site_url}'>{site_name}</a> with the email <b>{new_user_email}</b> and ordered for you. <br> Please set your password by going on password reset url. <br> <a href='{password_change_url}'>{password_change_url}</a>",
        "template_tags" => "
        {site_name} - Your site name.<br>
        {site_url} - Your site url.<br>
        {affiliate_first_name} - The first name of the affiliate.<br>
        {affiliate_last_name} - The last name of the affiliate.<br>
        {affiliate_email} - The email address of the affiliate.<br>
        {new_user_email} - Email of the newly created user.<br>
        {password_change_url} - URL where user can change its password.
        "
      ];
    }
    update_option("vitalibis_notification_settings", json_encode($notification_settings));
  }
}
