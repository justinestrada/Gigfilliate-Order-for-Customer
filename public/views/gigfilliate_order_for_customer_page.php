<?php
$cookie_name = "GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER";
$orders = wc_get_orders( array(
  'orderby'   => 'date',
  'order'     => 'DESC',
  'meta_query' => array(
      array(
          'key' => 'v_order_affiliate_id',
          'value' => (int)get_user_meta(get_current_user_id(), 'v_affiliate_id', true),
      )
  )
));
$customers = [];
foreach ($orders as $key => $order) {
  if ($order->get_user() != null) {
    $customers[$order->get_user()->ID] = ["user"=>$order->get_user(),"last_order"=>$order];
  }
}
if(!isset($_COOKIE[$cookie_name])) {
?>

<h3>Select Customer:</h3>
<div class="card gofc-customer-search">
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
          if(!$customer["user"]){
            break;
          }
      ?>
      <li class="list-group-item gofc-customers-list-item" data-customer_name="<?php echo $customer["user"]->display_name;?>">
        <span class="gofc-customers-list-item-name"><b><?php echo $customer["user"]->display_name.'</b><br><span class="secondary-text">'.$customer["user"]->user_email.'</span>';?></span>
        <span class="gofc-customers-list-item-last-order-date"><b>Last Order Date</b> <br> <span class="secondary-text"><?php echo $customer["last_order"]->get_date_created()->date("m/d/y");?></span></span>
        <form class="gofc-customers-list-item-form" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
          <input name='action' type="hidden" value='customer_order_form_submit'>
          <input name='customer' type="hidden" value='<?php echo $key;?>'>
          <button type="submit" class="v-btn v-btn-outline-primary gofc-customers-list-item-place-order-btn">Place Order</button>
        </form>
      </li>
      <?php
        }
      ?>
    </ul>
  </div>
</div>
<?php
return;
}


$customer = get_user_by("ID", $_COOKIE[$cookie_name]);
$vitalibis_settings = get_option('vitalibis_settings');
$vitalibis_settings = json_decode($vitalibis_settings);
?>
<div class="alert alert-info" role="alert">
You're placing an order for <?php echo $customer->first_name.' '.$customer->last_name;?>. You can use your <?php echo $vitalibis_settings->affiliate_term;?> customer coupon, but not your personal <?php echo $vitalibis_settings->affiliate_term;?> coupon
</div>
<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
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