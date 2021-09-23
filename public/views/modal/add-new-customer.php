
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
        <form id="gofc-add-customer-form" action="POST">
          <div class="v-skeleton-block" style="display: none; height: 40px; margin-bottom: 1rem;">
            <div class="v-line" style="height: 40px; margin: 0;"></div>
          </div>
          <div class="form-group md-form mt-2">
            <label for="new-gofc-customer">New Email Address</label>
            <input type="email" id="new-gofc-customer" name="new_gofc_customer" class="form-control" aria-describedby="emailHelp" required/>
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>
          <input name="action" type="hidden" value="customer_order_form_submit"/>
          <button type="submit" class="v-btn v-btn-primary"><i class="fa fa-plus mr-1" aria-hidden="true"></i>Add New Customer</button>
        </form>
      </div>
    </div>
  </div>
</div>
