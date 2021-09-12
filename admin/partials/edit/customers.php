
<style>
#gofc-customers-listing {
  margin-bottom: 1rem;
}
#load-more-customers-button-wrap {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
<?php
$customers_data = $this->helpers->get_customers($affiliate_user_id);
?>
<div id="edit-affiliate-customers">
  <h3>Customers</h3>
  <?php
  if (!empty($customers_data['customers'])) { ?>
    <div id="gofc-customers-listing">
      <?php foreach ($customers_data['customers'] as $key => $customer) { ?>
        <div class="gofc-customers-list_item">
          <div class="v-row">
            <div class="gofc-customers-list-item-name v-col-lg-6">
              <strong style="text-transform: capitalize;">
                <?php echo isset($customer['user']) ? $customer['user']->display_name : $customer['email']; ?>
              </strong>
              <br>
              <span><?php echo $customer['email']; ?></span>
            </div>
            <div class="gofc-customers-list-item-last-order-date v-col-lg-6">
              Last Order Date
              <div>
                <?php echo $customer['last_order_date']; ?>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <div id="load-more-customers-button-wrap">
      <button type="button" id="load-more-customers" class="button" offset="<?php echo $customers_data['orders_found']; ?>" affiliate-user-id="<?php echo $affiliate_user_id; ?>">Load More Customers</button>
    </div>
    <?php
  } else { ?>
    <p>You do not have any <?php // $this->core_settings->affiliate_term; ?> referred customers, yet.</p>
  <?php } ?>
</div>
