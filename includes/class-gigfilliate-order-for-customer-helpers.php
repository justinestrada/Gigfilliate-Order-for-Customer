<?php

class Gigfilliate_Order_For_Customer_Helpers {

  public function __construct() { }

  public function get_customers( $affiliate_user_id = false, $current_user = false ) {
    if (!$affiliate_user_id) {
      return [];
    }
    if (!$current_user) {
      $current_user = get_userdata(get_current_user_id());
    }
    $orders = wc_get_orders([
      'orderby'   => 'date',
      'order'     => 'DESC',
      'meta_query' => [
        [
          'key' => 'v_order_affiliate_id',
          'value' => (int)get_user_meta($affiliate_user_id, 'v_affiliate_id', true),
        ]
      ]
    ]);
    $customers = [];
    if (empty($orders)) {
      return $customers;
    }
    foreach ($orders as $key => $order) {
      $order_user = $order->get_user();
      $customer_email = $order->get_billing_email();
      $new_customer = [
        'email' => $customer_email
      ];
      if ($order_user !== false && $order_user !== null) {
        $customer_email = $order_user->user_email;
        // var_dump($order_user, $billing_email);
        // wp_die();
        // Skip if customer is current user
        if ($current_user->user_email === $order_user->user_email) {
          continue;
        }
        // Skip if past customer is now an active affiliate
        if (vitalibis_is_active_affiliate((int)$order_user->ID)) {
          continue;
        }
        $new_customer['user'] = $order_user;
      }
      $new_customer['last_order'] = $order;
      $customers[$customer_email] = $new_customer;
    }
    return $customers;
  }
}
