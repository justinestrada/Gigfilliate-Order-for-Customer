<link rel="stylesheet" href="<?php echo  WP_PLUGIN_DIR.'/gigfilliate-order-for-customer/public/css/select2.min.css';?>">
<script src="<?php echo  WP_PLUGIN_DIR.'/gigfilliate-order-for-customer/public/js/select2.full.min.js';?>"></script>
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
  $customers[$order->get_user()->ID] = $order->get_user();
}
if(!isset($_COOKIE[$cookie_name])) {
?>
<h3>Select Customer:</h3>
<form action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
  <input name='action' type="hidden" value='customer_order_form_submit'>
  <select class="select-customer" name="customer">
    <?php
    foreach ($customers as $key => $customer) {
      echo '<option value="'.$key.'">'.$customer->display_name.' ('.$customer->user_email.')</option>';
    }
    ?>
  </select>
  <br><br>
  <button type="submit" class="v-btn v-btn-outline-primary">Place Order</button>
</form>
<?php
}else{
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
<?php
}
?>

<script>
jQuery(document).ready(function() {
  jQuery('.select-customer').select2({
    width:"400px"
  });
});
</script>