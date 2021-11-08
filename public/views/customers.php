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
              <div class="v-col-lg-3">
                Customer Info
              </div>
              <div class="v-col-lg-2">
                Last Order Date
              </div>
              <div class="v-col-lg-1 gwp-text-center">
                Total Orders
              </div>
              <div class="v-col-lg-2 gwp-text-center">
                Average Order Value
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
                  <div class="v-col-lg-3 text-center text-lg-left mb-3 mb-lg-0">
                    <div>
                      <strong class="gofc-customer_full-name">
                        <?php echo $customer['full_name']; ?>
                      </strong>
                      <br>
                      <span class="gofc-customer_email"><?php echo $customer['email']; ?></span><br>
                      <span class="text-black-50"><?php echo $customer['city'] .', '.$customer['state']; ?></span>
                    </div>
                  </div>
                  <div class="v-col-lg-2 gwp-text-center">
                    <div>
                      <span class="d-lg-none mr-1">Last Order Date:</span>
                      <div class="gofc-customer_value">
                        <?php echo $customer['last_order_date']; ?>
                      </div>
                    </div>
                  </div>
                  <div class="v-col-lg-1 gwp-text-center">
                    <div>
                      <span class="d-lg-none mr-1">Total Orders:</span>
                      <div class="gofc-customer_value gofc-customer_total-orders-value">
                        <?php echo $customer['orders_count']; ?>
                        <div class="v-skeleton-block">
                          <div class="v-line"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="v-col-lg-2 gwp-text-center">
                    <div>
                      <span class="d-lg-none mr-1">Average Order Value:</span>
                      <div class="gofc-customer_value gofc-customer_aov-value">
                        $<?php echo $customer['aov']; ?>
                        <div class="v-skeleton-block">
                          <div class="v-line"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="gofc-customer_total-spend v-col-lg-2 gwp-text-center mb-3 mb-lg-0">
                    <div>
                      <span class="d-lg-none mr-1">Total Spend:</span>
                      <span class="gofc-customer_value total-spend_value">
                        $<?php echo $customer['total_spend']; ?>
                        <div class="v-skeleton-block">
                          <div class="v-line"></div>
                        </div>
                      </span>
                    </div>
                  </div>
                  <div class="v-col-lg-2 gwp-text-lg-right d-flex justify-content-center justify-content-lg-end align-items-center">
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
