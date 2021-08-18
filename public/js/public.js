(function($) {
  'use strict';
  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  const Cookie = {
    read: function(name) {
      var nameEQ = name + '=';
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
    },
    create: function(name, value, days) {
      let expires = '';
      if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toGMTString();
      } else {
        expires = '';
      }
      document.cookie = name + '=' + value + expires + '; path=/';
    },
    erase: function(name) {
      Cookie.create(name, '', -1);
    },
  };
  const OrderForCustomer = {
    init: function() {
      if ($('#search_customer').length) {
        this.searchCustomer();
      }
      if ($('#search_product').length) {
        this.searchProduct()
        this.getProducts();
      }
      if ($('#$gofc_customer_billing').length) {
        this.setupGofcCustomerBilling()
      }
      if ($('.gfc_add_customer').length) {
        this.addNewCustomer();
      }
      if ($('.gofc_exit_place_order_for_customer').length) {
        this.exitPlaceOrderForCustomer();
      }
      this.giveWarningWhenLeavingTheCheckout();
      this.exitFromOrderForCustomerIfNotOnValidPage();
    },
    searchCustomer: function() {
      $('#search_customer').on('keyup', function() {
        let to_search = $(this);
        let customers = $('.gofc-customers-list-item');
        for (let i = 0; i < customers.length; i++) {
          const customer = $(customers[i]);
          if (new RegExp(to_search.val(), 'i').test(customer.data('customer_name'))) {
            customer.attr('style', '');
          } else {
            customer.attr('style', 'display:none !important;');
          }
        }
      });
    },
    searchProduct: function() {
      let timeout = null;
      let self = this;
      $('#search_product').on('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          let to_search = $('#search_product').val();
          self.getProducts(to_search);
        }, 800)
      });
    },
    getProducts: function(to_search = '') {
      var data = {
        action: 'gofc_search_product',
        search: to_search
      };
      let products_list = $('.gofc-products-list');
      products_list.html('<div class="loading-spinner"><div class="loading-animation"><div></div></div></div>'); // TODO: Use skeleton loaders
      $.get(my_gofc_object.ajax_url, data, function(response) {
        products_list.html('');
        if (response == 0) {
          alert('Network Error. Try Again.')
          return;
        }
        response = JSON.parse(response);
        response.forEach(element => {
          let new_product = '<li class="list-group-item gofc-products-list-item"> \
          <img class="gofc-products-list-item-thumbnail" src="' + element["thumbnail_url"] + '"/> \
          <span class="gofc-products-list-item-name"> \
            ' + element["name"] + ' \
            <span class="gofc-products-list-item-price">$' + element["price"] + '</span> \
          </span>';
          new_product += '<a href="' + (element['is_in_stock'] ? element["add_to_cart_url"] : 'javascript:void(0)') + '" value="' + element["id"] + '" data-product_id="' + element['id'] + '" data-product_sku="' + element['sku'] + '" aria-label="Add ' + element["name"] + ' to your cart"class="' + (element['is_in_stock'] ? 'ajax_add_to_cart add_to_cart_button' : '') + ' v-btn v-btn-outline-primary gofc-products-list-item-add-to-cart-btn" ' + (element['is_in_stock'] ? '' : 'disabled') + '>' + (element['is_in_stock'] ? '<span class="added_to_cart_label">Added to Cart</span><span class="adding_to_cart_label">Adding to Cart</span><span class="add_to_cart_label">Add to Cart</span>' : 'Out Of Stock') + '</a>';
          new_product += '</li>';
          products_list.append(new_product);
        });
      });
    },
    setupGofcCustomerBilling: function() {
      const $gofc_customer_billing = $('#gofc_customer_billing');
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
      if (billing_email.length) {
        billing_email.val($gofc_customer_billing.data('email'));
      }
      if (first_name.length) {
        first_name.val($gofc_customer_billing.data('firstname'));
      }
      if (last_name.length) {
        last_name.val($gofc_customer_billing.data('lastname'));
      }
      if (billing_address_1.length) {
        billing_address_1.val($gofc_customer_billing.data('address1'));
      }
      if (billing_address_2.length) {
        billing_address_2.val($gofc_customer_billing.data('address2'));
      }
      if (billing_company.length) {
        billing_company.val($gofc_customer_billing.data('company'));
      }
      if (billing_country.length) {
        billing_country.val($gofc_customer_billing.data('country')).change();
      }
      if (billing_postcode.length) {
        billing_postcode.val($gofc_customer_billing.data('postcode'));
      }
      if (billing_state.length) {
        billing_state.val($gofc_customer_billing.data('state')).change();
      }
      if (billing_city.length) {
        billing_city.val($gofc_customer_billing.data('city'));
      }
      if (billing_phone.length) {
        billing_phone.val($gofc_customer_billing.data('phone'));
      }
    },
    addNewCustomer: function() {
      let self = this;
      $('.gfc_add_customer').on('click', function(e) {
        e.preventDefault();
        e = $(this);
        let email = null;
        if (e.data('input_element')) {
          email = $(e.data('input_element')).val();
          if (email.trim() == '') {
            alert('Email is required.');
          } else if(!self.validateEmail(email.trim())) {
            alert('Please enter a valid email.');
          } else {
            $('#addNewCustomerModal').modal('hide');
          }
        }
        if (e.data('customer_email')) {
          email = e.data('customer_email');
        }
        if (!email || !self.validateEmail(email.trim())) {
          return;
        }
        Cookie.create(my_gofc_object.cookie_name, email, 1);
        $('#gofc_customer_section').slideUp();
        $('#gofc_products_section').slideDown();
        $('#alert-placing-for-customer #alert_customer_email').html(email);
      });
    },
    validateEmail: function(email){
      const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    },
    exitPlaceOrderForCustomer: function() {
      $('.gofc_exit_place_order_for_customer').on('click', function(e) {
        Cookie.erase(my_gofc_object.cookie_name);
        $.get(my_gofc_object.ajax_url, {
          action: 'gofc_reset_cart'
        });
        $('#gofc_customer_section').slideDown();
        $('#gofc_products_section').slideUp();
        $('#gofc_products_section .ajax_add_to_cart.added').html('Add to Cart');
        $('#gofc_products_section .ajax_add_to_cart').removeClass('added');
      });
    },
    exitFromOrderForCustomerIfNotOnValidPage: function() {
      let valid_pages = ['/my-account/brand-partner-customers/', '/checkout/', '/cart/'];
      if (!valid_pages.includes(window.location.pathname) && Cookie.read(my_gofc_object.cookie_name) != null) {
        $.get(my_gofc_object.ajax_url, {
          action: 'gofc_reset_cart'
        });
        Cookie.erase(my_gofc_object.cookie_name);
        $('.GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER_DELETE').toast('show');
      }
    },
    giveWarningWhenLeavingTheCheckout: function() {
      if (window.location.pathname == '/checkout/' && Cookie.read(my_gofc_object.cookie_name)) {
        window.onbeforeunload = function() {
          return 'You have attempted to leave this page. And if you leave it you will be exites from place order for customer mode. Are you sure you want to exit this page?';
        };
      }
    }
  }
  $(window).load(function() {
    OrderForCustomer.init();
  });
})(jQuery);
