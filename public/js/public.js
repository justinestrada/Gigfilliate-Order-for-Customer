(function($) {
'use strict';

const OrderForCustomer = {
  init: function() {
    if ($('body.woocommerce-account .woocommerce-MyAccount-content.account.brand-partner-customers').length) {
      this.onSearchCustomers()
      this.onSearchProducts()
      this.onGetProducts()
      this.onAddNewCustomer()
      this.onBtnClickPlaceOrder()
      this.onExitPlaceOrderForCustomer()
    }
    if ($('#gofc_customer_billing').length) {
      this.setupCustomerBilling()
    }
    this.giveWarningWhenLeavingTheCheckout()
    this.exitFromOrderForCustomerIfNotOnValidPage()
  },
  onSearchCustomers: function() {
    $('#search_customer').on('keyup', function() {
      let to_search = $(this)
      let customers = $('.gofc-customers-list-item')
      for (let i = 0; i < customers.length; i++) {
        const customer = $(customers[i])
        if (new RegExp(to_search.val(), 'i').test(customer.data('customer_name'))) {
          customer.attr('style', '')
        } else {
          customer.attr('style', 'display: none !important;')
        }
      }
    })
  },
  onSearchProducts: function() {
    let timeout = null
    const self = this
    $('#search_product').on('keyup', function() {
      clearTimeout(timeout)
      timeout = setTimeout(function() {
        self.onGetProducts()
      }, 750)
    });
  },
  onGetProducts: function() {
    let $products_list = $('.gofc-products-list');
    $products_list.html('<div class="loading-spinner"><div class="loading-animation"><div></div></div></div>'); // TODO: Use skeleton loaders
    this.getProducts().then( function(res) {
      $products_list.html('')
      if (!res || res === 0) {
        alert('Network Error. Try Again.')
        return
      }
      res = JSON.parse(res);
      if (res.success) {
        if (res.products.length) {
          res.products.forEach( element => {
            let new_product = '<div class="gofc-products-list-item">\
                <div class="v-row">\
                  <div class="v-col-md-3 v-col-lg-2">\
                    <div class="gofc-products-list-item_thumbnail-wrap">\
                      <img class="gofc-products-list-item_thumbnail" src="' + element['thumbnail_url'] + '" alt="' + element['name'] + '"/>\
                    </div>\
                  </div>\
                  <div class="v-col-md-5 v-col-lg-6 d-flex align-items-center">\
                    <span class="gofc-products-list-item-name">\
                      ' + element["name"] + '\
                      <span class="gofc-products-list-item-price">$' + element['price'] + '</span>\
                    </span>\
                  </div>\
                  <div class="v-col-4 d-flex align-items-center justify-content-sm-end">\
                    <a href="' + (element['is_in_stock'] ? element['add_to_cart_url'] : 'javascript:void(0)') + '" value="' + element['id'] + '" data-product_id="' + element['id'] + '" data-product_sku="' + element['sku'] + '" aria-label="Add ' + element["name"] + ' to your cart"class="' + (element['is_in_stock'] ? 'ajax_add_to_cart add_to_cart_button' : '') + ' v-btn v-btn-primary gofc-products-list-item-add-to-cart-btn" ' + (element['is_in_stock'] ? '' : 'disabled') + '>\
                      ' + (element['is_in_stock'] ? '<span class="added_to_cart_label">Added to Cart</span><span class="adding_to_cart_label">Adding to Cart</span><span class="add_to_cart_label">Add to Cart</span>' : 'Out Of Stock') + '\
                    </a>\
                  </div>\
                </div>\
              </div>'
            $products_list.append(new_product)
          })
        } else {
          $products_list.html('<p>No products found.</p>')
        }
      } else {
        console.error(res)
      }
    }).catch(function(err) {
      console.error(err)
    })
  },
  getProducts: function() {
    return new Promise( (resolve, reject) => {
      $.ajax({
        url: GOFC.ajax_url,
        data: {
          action: 'gofc_get_products',
          search: $('#search_product').val()
        },
        type: 'POST',
        config: { headers: {'Content-Type': 'multipart/form-data' }},
      }).done(function(res) {
        // const json_res = JSON.parse(res)
        resolve(res)
      }).fail(function(err) {
        reject(err)
      })
    })
  },
  setupCustomerBilling: function() {
    const $gofc_customer_billing = $('#gofc_customer_billing')
    const $billing_email = $('[name="billing_email"]')
    const $first_name = $('[name="first_name"]')
    const $last_name = $('[name="last_name"]')
    const $billing_address_1 = $('[name="billing_address_1"]')
    const $billing_address_2 = $('[name="billing_address_2"]')
    const $billing_company = $('[name="billing_company"]')
    const $billing_country = $('[name="billing_country"]')
    const $billing_postcode = $('[name="billing_postcode"]')
    const $billing_state = $('[name="billing_state"]')
    const $billing_city = $('[name="billing_city"]')
    const $billing_phone = $('[name="billing_phone"]')
    if ($billing_email.length) {
      $billing_email.val($gofc_customer_billing.data('email'))
    }
    if ($first_name.length) {
      $first_name.val($gofc_customer_billing.data('firstname'))
    }
    if ($last_name.length) {
      $last_name.val($gofc_customer_billing.data('lastname'))
    }
    if ($billing_address_1.length) {
      $billing_address_1.val($gofc_customer_billing.data('address1'))
    }
    if ($billing_address_2.length) {
      $billing_address_2.val($gofc_customer_billing.data('address2'))
    }
    if ($billing_company.length) {
      $billing_company.val($gofc_customer_billing.data('company'))
    }
    if ($billing_country.length) {
      $billing_country.val($gofc_customer_billing.data('country')).change()
    }
    if ($billing_postcode.length) {
      $billing_postcode.val($gofc_customer_billing.data('postcode'))
    }
    if ($billing_state.length) {
      $billing_state.val($gofc_customer_billing.data('state')).change()
    }
    if ($billing_city.length) {
      $billing_city.val($gofc_customer_billing.data('city'))
    }
    if ($billing_phone.length) {
      $billing_phone.val($gofc_customer_billing.data('phone'))
    }
  },
  onAddNewCustomer: function() {
    const self = this
    $('#addNewCustomerModal').on('shown.bs.modal', function (e) {
      setTimeout(function() {  
        $('#new-gofc-customer').focus()
      }, 500)
    })
    $('label[for="new-gofc-customer"]').on('click', function() {
      $(this).addClass('active')
      $('#new-gofc-customer').focus()
    })
    $('#gofc-add-customer-form').on('submit', function(e) {
      e.preventDefault()
      const $input = $('#new-gofc-customer')
      let email = null;
      email = $input.val()
      let is_invalid = false
      let invalid_feedback = ''
      if (email.trim() === '') {
        is_invalid = true
        invalid_feedback = 'An email address is required.'
      } else if (!Utilities.isEmailValid(email.trim())) {
        is_invalid = true
        invalid_feedback = 'Please enter a valid email.'
      }
      if (is_invalid) {
        $input.parent().addClass('is-invalid')    
        $input.next().text(invalid_feedback).show()      
        $input.focus()
        return
      } else {
        $('#gofc-add-customer-form').find('[type="submit"]').prop('disabled', true)
        $('#gofc-add-customer-form').find('.v-skeleton-block').show()
        $('#gofc-add-customer-form').find('.form-group').hide()
        self.checkEmailExist(email).then( function(res) {
          res = JSON.parse(res)
          if (res.exists) {
            is_invalid = true
            $input.parent().addClass('is-invalid')    
            $input.next().text('This customer email already exists.').show()      
            $input.focus()
          } else {
            // else email doesnt exist can create new customer
            $('#addNewCustomerModal').modal('hide')
            // can create new customer enter 'Place Order for Customer' mode
            self.startPlaceOrderForCustomer(email)
          }
          $('#gofc-add-customer-form').find('[type="submit"]').prop('disabled', false).removeAttr('disabled')
          $('#gofc-add-customer-form').find('.v-skeleton-block').hide()
          $('#gofc-add-customer-form').find('.form-group').show()
        }).catch(function(err) {
          console.error(err)
        })
      }
    });
  },
  onBtnClickPlaceOrder: function() {
    $('.gofc-btn-place-order').on('click', function() {
      const email = $(this).attr('customer-email')
      OrderForCustomer.startPlaceOrderForCustomer(email)
    })
  },
  checkEmailExist: function(email) {
    return new Promise( (resolve, reject) => {
      $.ajax({
        url: GOFC.ajax_url,
        data: {
          action: 'gofc_check_email_exists',
          email: email
        },
        type: 'POST',
        config: { headers: {'Content-Type': 'multipart/form-data' }},
      }).done(function(res) {
        resolve(res)
      }).fail(function(err) {
        reject(err)
      })
    })
  },
  startPlaceOrderForCustomer: function( email ) {
    Cookie.create(GOFC.cookie_name, email, 1)
    $('#gofc_customer_section').slideUp()
    $('#gofc_products_section').slideDown()
    $('#alert-placing-for-customer #alert_customer_email').html(email)
  },
  onExitPlaceOrderForCustomer: function() {
    $('.gofc_exit_place_order_for_customer').on('click', function(e) {
      Cookie.erase(GOFC.cookie_name);
      $.get(GOFC.ajax_url, {
        action: 'gofc_reset_cart'
      });
      $('#gofc_customer_section').slideDown();
      $('#gofc_products_section').slideUp();
      $('#gofc_products_section .ajax_add_to_cart.added').html('Add to Cart');
      $('#gofc_products_section .ajax_add_to_cart').removeClass('added');
    });
  },
  exitFromOrderForCustomerIfNotOnValidPage: function() {
    if (!this.isCurrentURLValid() && Cookie.read(GOFC.cookie_name) !== null) {
      $.get(GOFC.ajax_url, {
        action: 'gofc_reset_cart'
      });
      Cookie.erase(GOFC.cookie_name);
      $('.GIGFILLIATE_PLACING_ORDER_FOR_CUSTOMER_DELETE').toast('show')
    }
  },
  isCurrentURLValid: function() {
    const location_href = window.location.href
    // TODO: Checkout /{affiliate-term}-customers
    const valid_pages = ['/account/brand-partner-customers/', '/my-account/brand-partner-customers/', '/checkout/', '/cart/'];
    let is_valid = false
    valid_pages.forEach(function(valid_page) {
      if (location_href.includes(valid_page)) {
        is_valid = true
      }
    })
    return is_valid
  },
  giveWarningWhenLeavingTheCheckout: function() {
    if (window.location.pathname == '/checkout/' && Cookie.read(GOFC.cookie_name)) {
      window.onbeforeunload = function() {
        return "You are attempting to leave this page. When you leave you exit 'Place Order for Customer' mode. Are you sure you want to exit this page?"
      }
    }
  }
}

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
}

const Utilities = {
  isEmailValid: function(email) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    return re.test(String(email).toLowerCase())
  },  
}

$(document).ready(function() {
  OrderForCustomer.init()
})
})(jQuery);
