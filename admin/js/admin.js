
(function( $ ) {
'use strict';

const EditAffiliateCustomers = {
  onLoad: function() {
    if (!$('#edit-affiliate-customers').length) {
      return
    }
    this.onClickLoadMore()
    setTimeout(function() {
      EditAffiliateCustomers.onLoadMore()
    }, 500)
  },
  onClickLoadMore: function() {
    $('#load-more-customers').on('click', function() {
      EditAffiliateCustomers.onLoadMore()
    })
  },
  onLoadMore: function() {
    const $load_more_customers = $('#load-more-customers')
    $load_more_customers.text('Loading...').prop('disabled', true)
    const affiliate_user_id = parseInt($load_more_customers.attr('affiliate-user-id'))
    const offset = parseInt($load_more_customers.attr('offset'))
    EditAffiliateCustomers.loadMoreCustomers(affiliate_user_id, offset).then( function(res) {
      res = JSON.parse(res)
      if (res.success) {
        // console.log(res, res.customers_data.customers.length)
        const customers_obj = res.customers_data.customers
        if (Object.keys(customers_obj).length) {
          let new_customers = ''
          Object.keys(customers_obj).forEach( key => {
            const customer = customers_obj[key]
            new_customers += '<div class="gofc-customers-list_item">\
              <div class="v-row">\
                <div class="gofc-customers-list-item-name v-col-lg-6">\
                  <strong style="text-transform: capitalize;">';
                    new_customers += (customer.hasOwnProperty('user')) ? customer.user.data.display_name : customer.email
                  new_customers += '</strong>\
                  <br>\
                  <span>' + customer.email + '</span>\
                </div>\
                <div class="gofc-customers-list-item-last-order-date v-col-lg-6">\
                  Last Order Date\
                  <div>' + customer.last_order_date + '</div>\
                </div>\
              </div>\
            </div>'
          })
          $('#gofc-customers-listing').append(new_customers)
          $load_more_customers.text('Load More Customers').prop('disabled', false).removeAttr('disabled')
        } else {
          $load_more_customers.text('Loaded All Customers').prop('disabled', true)
        }
        $('#load-more-customers').attr('offset', (offset + res.customers_data.orders_found))
      } else {
        console.error(res)
      }
    }).catch(function(err) {
      console.error(err)
    })
  },
  loadMoreCustomers: function(affiliate_user_id, offset) {
    return new Promise( (resolve, reject) => {
      $.ajax({
        url: Vitalibis_WP.admin_ajax,
        data: {
          action: 'gofc_get_customers',
          affiliate_user_id: affiliate_user_id,
          // limit: 20,
          offset: offset,
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
}

$(document).ready(function() {
  EditAffiliateCustomers.onLoad()
})

})( jQuery );
