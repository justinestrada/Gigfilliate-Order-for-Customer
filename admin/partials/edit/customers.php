
<div>
  <h3>Customers</h3>
  <div>
    <?php
    $customers = $this->helpers->get_customers($affiliate_user_id);
    ?>
    <?php
    if (!empty($customers)) {
      foreach ($customers as $key => $customer) {
        // TODO: replace data-customer_name with data-customer_email
        ?>
        <div class="gofc-customers-list_item">
          <div class=" v-row">
            <div class="gofc-customers-list-item-name v-col-lg-6">
              <strong style="text-transform: capitalize;">
                <?php echo isset($customer['user']) ? $customer['user']->display_name : $customer['email']; ?>
              </strong>
              <br>
              <span><?php echo $customer['email']; ?></span>
            </div>
            <div class="gofc-customers-list-item-last-order-date v-col-lg-6">
              Last Order Date
              <div><?php echo $customer['last_order']->get_date_created()->date('F j, Y, g:i:a'); ?></div>
            </div>
          </div>
        </div>
      <?php }
    } else { ?>
      <p>You do not have any <?php // $this->core_settings->affiliate_term; ?> referred customers, yet.</p>
    <?php } ?>
  </div>
</div>
