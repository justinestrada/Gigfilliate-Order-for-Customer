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
   $(function() {
  if ($('#search_customer')) {
    $('#search_customer').on("keyup",function(){
      let to_search = $(this);
      let customers = $('.gofc-customers-list-item');
      for (let i = 0; i < customers.length; i++) {
        const customer = $(customers[i]);
        console.log(new RegExp(to_search.val(), 'i').test(customer.data('customer_name')));
        if(new RegExp(to_search.val(), 'i').test(customer.data('customer_name'))){
          customer.attr("style","");
        }else{
          customer.attr("style","display:none !important;");
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
    console.log(products_list);
		products_list.html('<div class="loading-spinner"><div class="loading-animation"><div></div></div></div>');
		$.get(my_gofc_object.ajax_url, data, function(response) {
			products_list.html("");
			if (response == 0) {
				alert("Network Error. Try Again.")
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
        console.log(products_list);
			});
		});
	}
  if($('#search_product')){
    let timeout = null;
    $('#search_product').on("keyup", function() {
      clearTimeout(timeout);
      timeout = setTimeout(function() {
        let to_search = $('#search_product').val();
        getProducts(to_search);
      }, 800)
    });
    getProducts();
  }
});
})(jQuery);