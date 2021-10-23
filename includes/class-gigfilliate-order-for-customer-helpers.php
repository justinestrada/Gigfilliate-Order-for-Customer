<?php

class Gigfilliate_Order_For_Customer_Helpers {

  public function __construct() {
  }

  public function get_customers($affiliate_user_id = false, $current_user = false, $limit = 20, $offset = false, $order_by = 'az') {
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
        ],
        'order_clause' => [
          'key' => '_billing_first_name',
        ]
      ],
    ];
    if ($limit) {
      $args['limit'] = $limit;
    }
    if ($offset) {
      $args['offset'] = $offset;
    }
    if ($order_by == 'az') {
      $args['order'] = 'ASC';
      $args['meta_query']['order_clause']['key'] = '_billing_first_name';
    }
    if ($order_by == 'za') {
      $args['order'] = 'DESC';
      $args['meta_query']['order_clause']['key'] = '_billing_first_name';
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
    foreach ($orders as $order) {
      $wc_order = new WC_Order($order->ID);
      $customer_email = $wc_order->get_billing_email();
      $customer_fname = $wc_order->get_billing_first_name();
      $customer_lname = $wc_order->get_billing_last_name();
      $customer_state = $wc_order->get_billing_state();
      $customer_city = $wc_order->get_billing_city();
      // if customer already exists skip
      if (isset($res['customers'][$customer_email])) {
        $res['customers'][$customer_email]['orders_count']++;
        $res['customers'][$customer_email]['total_spend'] += (float)$wc_order->get_total();
        $res['customers'][$customer_email]['aov'] = (float)number_format((float)($res['customers'][$customer_email]['total_spend'] / $res['customers'][$customer_email]['orders_count']), 2, '.', '');
        continue;
      }
      $new_customer = [
        'email' => $customer_email
      ];
      $order_user = get_user_by('email', $customer_email);
      if ($order_user !== false && $order_user !== null) {
        // Skip if past customer is now an active affiliate
        if (vitalibis_is_active_affiliate((int)$order_user->ID)) {
          continue;
        }
      }
      $new_customer['full_name'] = $customer_fname . ' ' . $customer_lname;
      $new_customer['state'] = $customer_state;
      $new_customer['city'] = $customer_city;
      // $new_customer['last_order'] = $order; // unused
      // $new_customer['order_affiliate_id'] = get_post_meta($order->ID, 'v_order_affiliate_id', true); // unused
      $new_customer['orders_count'] = 1;
      $new_customer['total_spend'] = (float)$wc_order->get_total();
      $new_customer['aov'] = (float)number_format((float)($new_customer['total_spend'] / $new_customer['orders_count']), 2, '.', '');
      $new_customer['last_order_date'] = $wc_order->get_date_created()->date('F j, Y, g:i a');
      $res['customers'][$customer_email] = $new_customer;
    }
    return $res;
  }
}
