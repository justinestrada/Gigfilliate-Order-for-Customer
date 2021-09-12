<?php

class Gigfilliate_Order_For_Customer_Helpers {

  public function __construct() { }

  public function get_customers( $affiliate_user_id = false, $current_user = false, $limit = 20, $offset = false ) {
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
    $affiliate_id = get_user_meta($affiliate_user_id, 'v_affiliate_id', true);
    $args = [
      'post_type' => 'shop_order',
      'post_status' => array( 'wc-completed', 'wc-processing' ),
      'orderby' => 'date',
      'order' => 'DESC',
      'meta_query' => [
        'relation' => 'AND',
        [
          'key' => 'v_order_affiliate_id',
          'value' => $affiliate_id,
          'compare' => '='
        ],
        [
          'key' => 'v_order_affiliate_volume_type',
          'value' => 'PERSONAL',
          'compare' => '!='
        ]
      ]
    ];
    if ($limit) {
      $args['limit'] = $limit;
    }
    if ($offset) {
      $args['offset'] = $offset;
    }
    // echo '<pre>';
    // var_dump($args);
    // echo '</pre>';
    // $orders = wc_get_orders($args); // not working correctly with meta_query
    $orders = get_posts($args);
    if (empty($orders)) {
      return $res;
    }
    $res['orders_found'] = count($orders);
    foreach ($orders as $key => $order) {
      $wc_order = new WC_Order( $order->ID );
      $customer_email = $wc_order->get_billing_email();
      // if customer already exists skip
      if (isset($res['customers'][$customer_email])) {
        // TODO: Increment the orders already placed count
        continue;
      }
      $new_customer = [
        'email' => $customer_email
      ];
      $order_user = $wc_order->get_user();
      if ($order_user !== false && $order_user !== null) {
        $customer_email = $order_user->user_email;
        /*
         * Skip if customer is current user
         * Dont think this is necessary now since we check if user is not PERSONAL volume
        if ($current_user->user_email === $order_user->user_email) {
          continue;
        }
        */
        // Skip if past customer is now an active affiliate
        if (vitalibis_is_active_affiliate((int)$order_user->ID)) {
          continue;
        }
        $new_customer['user'] = $order_user;
      }
      // $new_customer['last_order'] = $order;
      // $new_customer['order_affiliate_id'] = get_post_meta($order->ID, 'v_order_affiliate_id', true);
      $new_customer['last_order_date'] = $wc_order->get_date_created()->date('F j, Y, g:i a');
      $res['customers'][$customer_email] = $new_customer;
    }
    return $res;
  }
}
