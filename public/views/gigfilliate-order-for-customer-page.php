<?php
$orders = wc_get_orders([
  'orderby'   => 'date',
  'order'     => 'DESC',
  'meta_query' => [
    [
      'key' => 'v_order_affiliate_id',
      'value' => (int)get_user_meta(get_current_user_id(), 'v_affiliate_id', true),
    ]
  ]
]);
$customers = [];
foreach ($orders as $key => $order) {
  if ($order->get_user() != null) {
    $customers[$order->get_user()->ID] = ["user" => $order->get_user(), "last_order" => $order];
  }
}
if (!isset($_COOKIE[$this->cookie_name])) {
?>
  <h3>Select Customer:</h3>
  <button type="button" class="v-btn v-btn-outline-primary" data-toggle="modal" data-target="#addNewCustomerModal">
    Add New Customer
  </button>
  <div class="card gofc-customer-search w-100">
    <div class="card-body">
      <div class="card-header">
        <div class="row">
          <div class="col-sm-6">
            <h4>Filter By Name:</h4>
          </div>
          <div class="col-sm-6">
            <input type="text" name="search_customer" id="search_customer" class="form-control">
          </div>
        </div>
      </div>
      <ul class="list-group list-group-flush gofc-customers-list">
        <?php
        foreach ($customers as $key => $customer) {
          if (!$customer["user"]) {
            break;
          }
        ?>
          <li class="list-group-item gofc-customers-list-item" data-customer_name="<?php echo $customer["user"]->display_name; ?>">
            <span class="gofc-customers-list-item-name"><b><?php echo $customer["user"]->display_name . '</b><br><span class="secondary-text">' . $customer["user"]->user_email . '</span>'; ?></span>
            <span class="gofc-customers-list-item-last-order-date"><b>Last Order Date</b> <br> <span class="secondary-text"><?php echo $customer["last_order"]->get_date_created()->date("m/d/y"); ?></span></span>
            <form class="gofc-customers-list-item-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
              <input name='action' type="hidden" value='customer_order_form_submit'>
              <input name='customer' type="hidden" value='<?php echo $customer["user"]->user_email; ?>'>
              <button type="submit" class="v-btn v-btn-outline-primary gofc-customers-list-item-place-order-btn">Place Order</button>
            </form>
          </li>
        <?php
        }
        ?>
      </ul>
    </div>
  </div>

  <div class="modal fade" id="addNewCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addNewCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form class="modal-content" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="addNewCustomerModalLabel">Add New Customer</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input name='action' type="hidden" value='customer_order_form_submit'>
          <div class="form-group">
            <label for="exampleInputEmail1">Email address</label>
            <input type="email" name="customer" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" required>
            <small id="emailHelp" class="form-text text-muted">Please enter the email of the customer.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="v-btn v-btn-outline-primary">Add</button>
        </div>
      </form>
    </div>
  </div>
<?php
  return;
}


$customer = get_user_by("email", $_COOKIE[$this->cookie_name]);
?>
<div class="alert alert-info" role="alert">
  You're placing an order for <?php echo ($customer != null ? ($customer->first_name . ' ' . $customer->last_name) : $_COOKIE[$this->cookie_name]); ?>. You can use your <?php echo $this->core_settings->affiliate_term; ?> customer coupon, but not your personal <?php echo $this->core_settings->affiliate_term; ?> coupon
</div>
<form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
  <input type="hidden" name="exit_customer" value="true">
  <input name='action' type="hidden" value='exit_customer_form_submit'>
  <button type="submit" class="v-btn v-btn-primary">Exit 'Place Order For Customer' Mode</button>
</form>
<br>
<h3>Select Product:</h3>
<div class="card gofc-customer-search">
  <div class="card-body">
    <div class="card-header">
      <div class="row">
        <div class="col-sm-6">
          <h4>Filter By Name:</h4>
        </div>
        <div class="col-sm-6">
          <input type="text" name="search_product" id="search_product" class="form-control">
        </div>
      </div>
    </div>
    <ul class="list-group list-group-flush gofc-products-list">

    </ul>
  </div>
</div>