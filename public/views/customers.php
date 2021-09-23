
<style>
#gof-customers-list_headers,
#gofc-customers-list,
#gof-customer-list_skeleton {
  margin-bottom: 1rem;
}
#gof-customer-list_skeleton .v-skeleton-block,
#gof-customer-list_skeleton .v-skeleton-block .v-line {
  height: 24px;
}
</style>
<section id="gofc_customer_section" style="display: <?php echo (!isset($_COOKIE[$this->cookie_name]) ? 'block' : 'none'); ?>;">
  <div id="gofc-customer-search" class="v-card gofc-customer-search">
    <div class="v-card-body">
      <div class="v-row mb-3">
        <div class="v-col-md-6 mb-3 mb-md-0">
          <div class="form-group md-form mt-2 mb-0">
            <label for="search_customer">Filter by Customer Name</label>
            <input type="text" name="search_customer" id="search_customer" class="form-control">
          </div>
        </div>
        <div class="v-col-md-6 gwp-text-right">
          <button type="button" class="v-btn v-btn-outline-primary mt-2" data-toggle="modal" data-target="#addNewCustomerModal">
            <i class="fa fa-plus mr-1" aria-hidden="true"></i>Add New Customer
          </button>
        </div>
      </div>
      <div>
        <?php
        if (!empty($this->my_customers)) { ?>
          <div id="gof-customers-list_headers">
            <div class="v-row align-items-center">
              <div class="v-col-lg-4">
                User Info
              </div>
              <div class="v-col-lg-4">
                Order Info
              </div>
              <div class="v-col-lg-2 gwp-text-center">
                Total Spend
              </div>
              <!-- TODO: The Customers sort should only load after all the customers are loaded in, then we just sort with JavaScript on the Frontend -->
              <?php /*
              <div id="gofc-customer-sort-col" class="v-col-lg-2" style="display: none;">
                <select class="form-control d-block" id="customer-sort">
                  <!-- <option value="mo">Sort by most orders</option> -->
                  <option value="az" selected>Sort A - Z</option>
                  <option value="za">Sort Z - A</option>
                </select>
              </div>
              */ ?>
            </div>
          </div>
          <div id="gofc-customers-list" offset="<?php echo $this->my_customers['orders_found']; ?>" affiliate-user-id="<?php echo $this->current_user_id; ?>">
            <?php foreach ($this->my_customers['customers'] as $key => $customer) { ?>
              <div class="gofc-customer" customer_email="<?php echo $customer['email']; ?>" customer_full-name="<?php echo $customer['full_name']; ?>" orders_count="<?php echo $customer['orders_count']; ?>">
                <div class="v-row">
                  <div class="v-col-lg-4">
                    <div>
                      <strong class="gofc-customer_full-name">
                        <?php echo $customer['full_name']; ?>
                      </strong>
                      <br>
                      <span><?php echo $customer['email']; ?></span><br>
                      <span class="text-black-50"><?php echo $customer['city'] .', '.$customer['state']; ?></span>
                    </div>
                  </div>
                  <div class="v-col-lg-4">
                    <div>
                      <span class="text-black-50">Last Order At:</span> <strong><?php echo $customer['last_order_date']; ?></strong><br>
                      <span class="text-black-50">Total Orders:</span> <strong><?php echo $customer['orders_count']; ?></strong><br>
                      <span class="text-black-50">Average Order Value:</span> <strong>$<?php echo $customer['aov']; ?></strong><br>
                    </div>
                  </div>
                  <div class="gofc-customer_total-spend v-col-lg-2 gwp-text-center">
                    $<?php echo $customer['total_spend']; ?>
                  </div>
                  <div class="v-col-lg-2 gwp-text-lg-right d-flex justify-content-end align-items-center">
                    <button type="button" class="gofc-btn-place-order v-btn v-btn-primary" customer-email="<?php echo $customer['email']; ?>">Place Order</button>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
          <div id="gof-customer-list_skeleton">
            <div class="v-skeleton-block">
              <div class="v-line"></div>
            </div>
            <div class="v-skeleton-block" style="width: 75%;">
              <div class="v-line"></div>
            </div>
            <div class="v-skeleton-block" style="width: 25%;">
              <div class="v-line"></div>
            </div>
          </div>
        <?php } else { ?>
          <p>You do not have any <?php $this->core_settings->affiliate_term; ?> referred customers, yet.</p>
        <?php } ?>
      </div>
      <div id="gofc-no-results-found" style="display: none;">
        <p>No customers found.</p>
      </div>
    </div>
  </div>
</section>
