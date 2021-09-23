
<div id="gofc_products_section" style="display: <?php echo (isset($_COOKIE[$this->cookie_name]) ? 'block' : 'none'); ?> ;">
  <div class="alert alert-info" role="alert" id="alert-placing-for-customer">
    <i class="fa fa-info-circle mr-1" aria-hidden="true"></i>
    You're placing an order for <span id="alert_customer_email"><?php echo isset($_COOKIE[$this->cookie_name]) ? $_COOKIE[$this->cookie_name] : ''; ?></span>. You can use your <?php echo $this->core_settings->affiliate_term; ?> customer coupon: <strong><?php echo $this->primary_affiliate_coupon_code; ?></strong>, but not your personal <?php echo $this->core_settings->affiliate_term; ?> coupon.
  </div>
  <button class="v-btn v-btn-outline-primary gofc_exit_place_order_for_customer mb-2">Exit 'Place Order For Customer' Mode</button>
  <div id="gofc-customer-search" class="card gofc-customer-search">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-sm-6">
          <div class="form-group md-form mt-2 mb-0">
            <label for="search_product">Filter by Product Name</label>
            <input type="text" name="search_product" id="search_product" class="form-control"/>
          </div>
        </div>
        <div class="col-sm-6 col-md-3 offset-md-3 d-flex align-items-center justify-content-sm-end">
          <select class="form-control" id="products-sorting-order" style="display: block !important;">
            <option value="title_a_z" selected>Sort By Title A - Z</option>
            <option value="title_z_a">Sort By Title Z - A</option>
            <option value="latest">Sort By Latest</option>
            <option value="price_low_high">Sort By Price Low - High</option>
            <option value="price_high_low">Sort By Price High - Low</option>
          </select>
        </div>
      </div>
      <div class="gofc-products-list">
        <!-- Products listed here via js -->
      </div>
    </div>
  </div>
</div>
