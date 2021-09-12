<?php

class Gigfilliate_Order_For_Customer_Helpers {

  public function __construct() { }

  public function get_customers( $affiliate_user_id = false, $current_user = false, $limit = 10, $offset = false ) {
    $res = [
      'orders_found' => 0,
      'customers' => [],
    ];
    if (!$affiliate_user_id) {
      return [];
    }
    if (!$current_user) {
      $current_user = get_userdata(get_current_user_id());
    }
    $args = [
      'orderby'   => 'date',
      'order'     => 'DESC',
      'meta_query' => [
        [
          'key' => 'v_order_affiliate_id',
          'value' => (int)get_user_meta($affiliate_user_id, 'v_affiliate_id', true),
        ]
      ]
    ];
    if ($limit) {
      $args['limit'] = $limit;
    }
    if ($offset) {
      $args['offset'] = $offset;
    }
    $orders = wc_get_orders($args);
    if (empty($orders)) {
      return $res;
    }
    $res['orders_found'] = count($orders);
    foreach ($orders as $key => $order) {
      $customer_email = $order->get_billing_email();
      // if customer already exists skip
      if (isset($res['customers'][$customer_email])) {
        // TODO: Increment the orders already placed count
        continue;
      }
      $new_customer = [
        'email' => $customer_email
      ];
      $order_user = $order->get_user();
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
      $new_customer['last_order_date'] = $order->get_date_created()->date('F j, Y, g:i:a');
      $res['customers'][$customer_email] = $new_customer;
    }
    return $res;
  }
}
