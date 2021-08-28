<?php
$orders = wc_get_orders([
  'orderby'   => 'date',
  'order'     => 'DESC',
  'meta_query' => [
    [
      'key' => 'v_order_affiliate_id',
      'value' => (int)get_user_meta($this->current_user_id, 'v_affiliate_id', true),
    ]
  ]
]);
$customers = [];
foreach ($orders as $key => $order) {
  if ($order->get_user() != null) {
    $customers[$order->get_user()->ID] = [
      'user' => $order->get_user(),
      'last_order'  => $order
    ];
  }
}
?>
<section id="gofc_customer_section" style="display: <?php echo (!isset($_COOKIE[$this->cookie_name]) ? 'block' : 'none'); ?>;">
  <div class="v-card gofc-customer-search w-100">
    <div class="v-card-body">
      <div class="v-row mb-3">
        <div class="v-col-md-6 mb-3 mb-md-0">
          <div class="form-group md-form mt-2 mb-0">
            <label for="search_customer">Filter by Customer Name</label>
            <input type="text" name="search_customer" id="search_customer" class="form-control">
          </div>
        </div>
        <div class="v-col-md-6 v-text-right">
          <button type="button" class="v-btn v-btn-outline-primary mt-2" data-toggle="modal" data-target="#addNewCustomerModal">
            Add New Customer
          </button>
        </div>
      </div>
      <div class="gofc-customers-list">
        <?php
        foreach ($customers as $key => $customer) {
          if (!$customer['user']) {
            break;
          }
          ?>
          <div class="gofc-customers-list_item" data-customer_name="<?php echo $customer['user']->display_name; ?>">
            <div class=" v-row">
              <div class="gofc-customers-list-item-name v-col-lg-4">
                <strong style="text-transform: capitalize;"><?php echo $customer['user']->display_name; ?></strong>
                <br>
                <span class="secondary-text"><?php echo $customer['user']->user_email; ?></span>
              </div>
              <div class="gofc-customers-list-item-last-order-date v-col-lg-4">
                Last Order Date
                <div class="secondary-text"><?php echo $customer['last_order']->get_date_created()->date('F j, Y, g:i:a'); ?></div>
              </div>
              <div class="gofc-customers-list-item-form v-col-lg-4 gofc-text-lg-right">
                <button type="button" class="v-btn v-btn-outline-primary gofc-customers-list-item-place-order-btn gfc_add_customer" data-customer_email="<?php echo $customer['user']->user_email; ?>">Place Order</button>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="addNewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addNewCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addNewCustomerModalLabel">Add New Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input name="action" type="hidden" value="customer_order_form_submit"/>
        <div class="form-group md-form">
          <label for="new_gofc_customer">New Email address</label>
          <input type="email" name="new_gofc_customer" class="form-control" id="new_gofc_customer" aria-describedby="emailHelp" required/>
          <!-- <small id="emailHelp" class="form-text text-muted">Please enter the email of the customer.</small> -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="v-btn v-btn-primary gfc_add_customer" data-input_element="#new_gofc_customer">Add New Customer</button>
      </div>
    </div>
  </div>
</div>

<div id="gofc_products_section" style="display: <?php echo (isset($_COOKIE[$this->cookie_name]) ? 'block' : 'none'); ?> ;">
  <div class="alert alert-info" role="alert" id="alert-placing-for-customer">
    <i class="fa fa-info-circle" aria-hidden="true"></i>
    You're placing an order for <span id="alert_customer_email"><?php echo isset($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : ''; ?></span>. You can use your <?php echo $this->core_settings->affiliate_term; ?> customer coupon, but not your personal <?php echo $this->core_settings->affiliate_term; ?> coupon.
  </div>
  <div class="card gofc-customer-search">
    <div class="card-body">
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group md-form mt-2 mb-0">
            <label for="search_product">Filter by Product Name</label>
            <input type="text" name="search_product" id="search_product" class="form-control"/>
          </div>
        </div>
        <div class="col-sm-6 d-flex align-items-center justify-content-sm-end">
          <button class="v-btn v-btn-outline-primary gofc_exit_place_order_for_customer">Exit 'Place Order For Customer' Mode</button>
        </div>
      </div>
      <div class="gofc-products-list">
        <!-- Products listed here via js -->
      </div>
    </div>
  </div>
</div>
