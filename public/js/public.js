(function ($) {
  'use strict';
  $(function () {
    if ($('#search_customer').length) {
      $('#search_customer').on("keyup", function () {
        let to_search = $(this);
        let customers = $('.gofc-customers-list-item');
        for (let i = 0; i < customers.length; i++) {
          const customer = $(customers[i]);
          if (new RegExp(to_search.val(), 'i').test(customer.data('customer_name'))) {
            customer.attr("style", "");
          } else {
            customer.attr("style", "display:none !important;");
          }
        }
      });
    }

    function getProducts(to_search = "") {
      var data = {
        action: 'gofc_search_product',
        search: to_search
      };
      let products_list = $('.gofc-products-list');
      products_list.html('<div class="loading-spinner"><div class="loading-animation"><div></div></div></div>'); // TODO: Use skeleton loaders
      $.get(my_gofc_object.ajax_url, data, function (response) {
        products_list.html("");
        if (response == 0) {
          alert("Network Error. Try Again.")
          return;
        }
        response = JSON.parse(response);
        response.forEach(element => {
          let new_product = '\
          <li class="list-group-item gofc-products-list-item"> \
            <img class="gofc-products-list-item-thumbnail" src="' + element["thumbnail_url"] + '"/> \
            <span class="gofc-products-list-item-name"> \
              ' + element["name"] + ' \
              <span class="gofc-products-list-item-price">$' + element["price"] + '</span> \
            </span>';
          new_product += '<a href="' + (element['is_in_stock'] ? element['add_to_cart_url'] : 'javascript:void(0)') + '" value="' + element['id'] + '" data-product_id="' + element['id'] + '" data-product_sku="' + element['sku'] + '" aria-label="Add ' + element['name'] + ' to your cart"class="' + (element['is_in_stock'] ? 'ajax_add_to_cart add_to_cart_button' : '') + ' v-btn v-btn-outline-primary gofc-products-list-item-add-to-cart-btn" ' + (element['is_in_stock'] ? '' : 'disabled') + '>' + (element['is_in_stock'] ? '<span class="added_to_cart_label">Added to Cart</span><span class="adding_to_cart_label">Adding to Cart</span><span class="add_to_cart_label">Add to Cart</span>' : 'Out Of Stock') + '</a>';
          new_product += '</li>';
          products_list.append(new_product);
        });
      });
    }
    if ($('#search_product').length) {
      let timeout = null;
      $('#search_product').on('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
          let to_search = $('#search_product').val();
          getProducts(to_search);
        }, 800)
      });
      getProducts();
    }
    const $gofc_customer_billing = $("#gofc_customer_billing");
    if ($gofc_customer_billing.length) {
      const billing_email = $('[name="billing_email"]');
      const first_name = $('[name="first_name"]');
      const last_name = $('[name="last_name"]');
      const billing_address_1 = $('[name="billing_address_1"]');
      const billing_address_2 = $('[name="billing_address_2"]');
      const billing_company = $('[name="billing_company"]');
      const billing_country = $('[name="billing_country"]');
      const billing_postcode = $('[name="billing_postcode"]');
      const billing_state = $('[name="billing_state"]');
      const billing_city = $('[name="billing_city"]');
      const billing_phone = $('[name="billing_phone"]');
      if (billing_email.length){
        billing_email.val($gofc_customer_billing.data('email'));
      }
      if (first_name.length){
        first_name.val($gofc_customer_billing.data('firstname'));
      }
      if (last_name.length){
        last_name.val($gofc_customer_billing.data('lastname'));
      }
      if (billing_address_1.length){
        billing_address_1.val($gofc_customer_billing.data('address1'));
      }
      if (billing_address_2.length){
        billing_address_2.val($gofc_customer_billing.data('address2'));
      }
      if (billing_company.length){
        billing_company.val($gofc_customer_billing.data('company'));
      }
      if (billing_country.length){
        billing_country.val($gofc_customer_billing.data('country')).change();
      }
      if (billing_postcode.length){
        billing_postcode.val($gofc_customer_billing.data('postcode'));
      }
      if (billing_state.length){
        billing_state.val($gofc_customer_billing.data('state')).change();
      }
      if (billing_city.length){
        billing_city.val($gofc_customer_billing.data('city'));
      }
      if (billing_phone.length){
        billing_phone.val($gofc_customer_billing.data('phone'));
      }
    }

    const Cookie = {
      read: function(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
          var c = ca[i];
          while (c.charAt(0)==' ') c = c.substring(1,c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
      },
      create: function(name, value, days) {
        let expires = '';
        if (days) {
          var date = new Date();
          date.setTime(date.getTime()+(days*24*60*60*1000));
          expires = '; expires='+date.toGMTString();
        } else {
          expires = '';
        }
        document.cookie = name + '=' + value + expires + '; path=/';
      },
      erase: function(name) {
        Cookie.create(name, '', -1);
      },
    };

    let valid_pages = ['/my-account/brand-partner-customers/','/checkout/','/cart/'];
    if(!valid_pages.includes(window.location.pathname) && Cookie.read(my_gofc_object.cookie_name) != null){
      Cookie.erase(my_gofc_object.cookie_name);
      jQuery('.GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER_DELETE').toast('show');
    }

    $(".gfc_add_customer").on("click",function(e){
      e.preventDefault();
      e = $(this);
      let email = null;
      if(e.data("input_element")){
        email = $(e.data("input_element")).val();
        if(email.trim() == ""){
          alert("Email is required.");
        }else{
          $('#addNewCustomerModal').modal('hide');
        }
      }
      if(e.data("customer_email")){
        email = e.data("customer_email");
      }
      if(!email){
        return;
      }
      
      Cookie.create(my_gofc_object.cookie_name,email,1);
      $("#gofc_customer_section").slideUp();
      $("#gofc_products_section").slideDown();
      let alert_placing_for_customer = $("#alert-placing-for-customer").html();
      alert_placing_for_customer = alert_placing_for_customer.replace("{customer_email}", email);
      $("#alert-placing-for-customer").html(alert_placing_for_customer);
    });
    $(".gofc_exit_place_order_for_customer").on("click",function(e){
      Cookie.erase(my_gofc_object.cookie_name);
      $("#gofc_customer_section").slideDown();
      $("#gofc_products_section").slideUp();
    });
  });
})(jQuery);
