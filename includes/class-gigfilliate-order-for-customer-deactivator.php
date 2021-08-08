<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://partners.vitalibis.com/login
 * @since      0.0.1
 *
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      0.0.1
 * @package    Gigfilliate_Order_For_Customer
 * @subpackage Gigfilliate_Order_For_Customer/includes
 * @author     Gigfilliate <justin@justinestrada.com>
 */
class Gigfilliate_Order_For_Customer_Deactivator {

  /**
   * Short Description. (use period)
   *
   * Long Description.
   *
   * @since    0.0.1
   */
  public static function deactivate()
  {
    Gigfilliate_Order_For_Customer_Deactivator::notification_settings();
  }

  public static function notification_settings()
  {
    $notification_settings = json_decode(get_option('vitalibis_notification_settings'));
    foreach ($notification_settings->notifications as $key => $notification) {
      if ($notification_settings->notifications[$key]->slug == "new-customer-by-bp") {
        unset($notification_settings->notifications[$key]);
      }
    }
    update_option("vitalibis_notification_settings", json_encode($notification_settings));
  }
}
