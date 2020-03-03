$(document).ready(function() {
	$('.checkout-leftmobil-title').click(function () {
		$('.checkout-left-mobil-none').slideToggle();
	});
    // Language
	$('#form-language .language-select').on('click', function(e) {
		e.preventDefault();
        
		$('#form-language input[name=\'code\']').val($(this).attr('name'));

		$('#form-language').submit();
	});

	$('.click-filter2').on('click', function() {
		if ($('.naw_mobil_menu').hasClass('active')) {
			// $('.click-filter2').removeClass('open-menu');
			$('body').removeClass('o_active');
			$('.naw_mobil_menu').removeClass('active');
		} 
		else {
			$(this).addClass('open-menu');
			$('body').addClass('o_active');
			$('.naw_mobil_menu').addClass('active');
		}
	});

	$('#form-currency .currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).attr('name'));

		$('#form-currency').submit();
	});

	function openModal(product_id,prev_product_id,next_product_id,key,array_length,type_product)
	{
		$.ajax({
			url: 'index.php?route=ajax/product',
			type: 'get',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('#modalContent').hide();
				$('#loader').show();
				$('#pragucModal').modal('show');
				data = json['success'];
				window.history.pushState('page2', 'Title', '/index.php?route=product/product&product_id=' + product_id);
					
				$('.call-you-madal').find('.produc-modal-info-top').find('.p-title-modal').html(data['name']);					
				$('.call-you-madal').find('.produc-modal-info-top').find('.p-cost-modal').find('.discount-price').html(data['tax']);	

				if(key == 0)
				{
					$('.call-you-madal').find('.prevButton').hide();
				}
				else{
					$('.call-you-madal').find('.prevButton').show();
					var prev_product_id = $('#' + type_product + (key-1)).find('.product_id').val();
					var prev_prev_product_id = $('#' + type_product + (key-1)).find('.prev_product_id').val();
					var prev_next_product_id = $('#' + type_product + (key-1)).find('.next_product_id').val();
					var prev_key = $('#' + type_product + (key-1)).find('.key').val();
					var prev_array_length = $('#' + type_product + (key-1)).find('.array_length').val();
					var prev_type_product = $('#' + type_product + (key-1)).find('.type_product').val();
					$('.call-you-madal').find('.prevButton').unbind("click");
					$('.call-you-madal').find('.prevButton').click(function(){ openModal(prev_product_id,prev_prev_product_id,prev_next_product_id,prev_key,prev_array_length,prev_type_product); });
				}
				
				
				if(key == array_length)
				{
					$('.call-you-madal').find('.nextButton').hide();
				}
				else{
					$('.call-you-madal').find('.nextButton').show();
					var next_loop_key = parseInt(key) + 1;
					var next_product_id = $('#' + type_product + next_loop_key).find('.product_id').val();
					var next_prev_product_id = $('#' + type_product + next_loop_key).find('.prev_product_id').val();
					var next_next_product_id = $('#' + type_product + next_loop_key).find('.next_product_id').val();
					var next_key = $('#' + type_product + next_loop_key).find('.key').val();
					var next_array_length = $('#' + type_product + next_loop_key).find('.array_length').val();
					var next_type_product = $('#' + type_product + next_loop_key).find('.type_product').val();
					$('.call-you-madal').find('.nextButton').unbind("click");
					$('.call-you-madal').find('.nextButton').click(function(){ 
						openModal(next_product_id,next_prev_product_id,next_next_product_id,next_key,next_array_length,next_type_product); 
					});
				}
					


				if(data['special'])
					$('.call-you-madal').find('.produc-modal-info-top').find('.p-cost-modal').find('.real-price').html(data['price']);				
				else
					$('.call-you-madal').find('.produc-modal-info-top').find('.p-cost-modal').find('.real-price').html("");
				
				$resImage = `<div class="carousel-inner">
					<div class="carousel-item active">
						<img class="d-block w-100" src="${data['image']}">
					</div>`;
				for($i = 0; $i < data['images'].length; $i++) {
					$resImage += `<div class="carousel-item">
						<img class="d-block w-100" src="` + data['images'][$i] + `">
					</div>`;
				}
				
				$resImage += `</div>`;
				
				if(data['images'].length > 0)
					$resImage += `<a class="carousel-control-prev" href="#carousel2_indicator" role="button" data-slide="prev">  </a>
					<a class="carousel-control-next" href="#carousel2_indicator" role="button" data-slide="next">  </a>`;

				$('.call-you-madal').find('.carousel-fade').html($resImage);

				// begin wishlist
				$('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').unbind("click");
			
				$('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').on("click",function(){ wishlistModal.add(data['product_id']) });

				//begin offer
				$('.call-you-madal').find('.produc-modal-info').find('a').attr("href","/index.php?route=common/suggest&product_id=" + data['product_id']);


				$('.btn-favorite').on('click', function(){
					$(this).toggleClass('btn-favorite--is-active');
				});

				if(data['wishlist']){
					if(!$('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').hasClass('btn-favorite--is-active')){
						$('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').addClass('btn-favorite--is-active');
					}
				}
				else {
					if($('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').hasClass('btn-favorite--is-active')){
						$('.call-you-madal').find('.produc-modal-info-footer').find('.btn-favorite').removeClass('btn-favorite--is-active');
					}        
				}
				// end wishlist

				// basket begin
				$('.call-you-madal').find('.produc-modal-info-footer').find('.modal-cart-icon').unbind("click");
				$('.call-you-madal').find('.produc-modal-info-footer').find('.modal-cart-icon').on("click",function(){ cart.add(data['product_id']) });
		
				// basket end


				// attributes begin


				var filter_groups = data['filter_groups'];
				if(filter_groups.length > 0){
					$resFilters = '';
					for($i = 0; $i < filter_groups.length; $i++) {
						filterGroup = filter_groups[$i];
						$resFilters += `<div class="titleNone titleNone11">`+ filterGroup['name'] + ': ';
						for($j = 0; $j < filterGroup['filter'].length; $j++){
							$resFilters += filterGroup['filter'][$j]['name'];
							if(filterGroup['filter'].length > 1 && $j != filterGroup['filter'].length-1){
								$resFilters += ',';	
							}
						}

						$resFilters += `</div>`;
					}
					$('.call-you-madal').find('.produc-modal-info').find('.collapse2').html($resFilters);
					// $(".titleNone-block11").hide();
					// $(".titleNone11").click(function () {
					// 	$(this).next(".titleNone-block11").slideToggle(200);
					// 	$(this).siblings().next(".titleNone-block11").slideUp(200);
					// });
				}
				else{
					$resFilters = `<span class="modal-type">No Attributes</span>`;
					$('.call-you-madal').find('.produc-modal-info').find('.collapse2').html($resFilters);
				}
				// attributes end
				$('#collapseOneModal').addClass('show');
				// $('#collapseOneModal').removeClass('show');
				$('#headingOneq').find('button').removeClass('a-is-active');
				// Modal show
				setTimeout(function(){
					$('#loader').hide();
					$('#modalContent').show();
				},2000);

				
				
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		}); 
	}

	$(document).on('click','.modal-product',function(){
		var product_id = $(this).find('.product_id').val();
		var prev_product_id = $(this).find('.prev_product_id').val();
		var next_product_id = $(this).find('.next_product_id').val();
		var key = $(this).find('.key').val();
		var array_length = $(this).find('.array_length').val();
		var type_product = $(this).find('.type_product').val();
		if(product_id){
			openModal(product_id,prev_product_id,next_product_id,key,array_length,type_product)
		}
		else {
			alert("Error");
		}
	
	}); 


	$('#pragucModal').on('hidden.bs.modal', function () {
		window.history.pushState('page2', 'Title', '');
	})

	/* Search */
	$('#sinput input[name=\'search\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=product/search';

		var value = $('header #sinput input[name=\'search\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#sinput input[name=\'search\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			var url = $('base').attr('href') + 'index.php?route=product/search';
			var value = $('header #sinput input[name=\'search\']').val();

			if (value) {
				url += '&search=' + encodeURIComponent(value);
			}
			location = url;
		}
	});
	
});

var wishlistModal = {
	'add': function(product_id) {

		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {

				if (json['redirect']) {
					location = json['redirect'];
				}
				if(json['status'] == "add")
				{
					$.toast().reset('all');
					$.toast({
						heading: json['success'],
						text: json['wishlist_added'],
						position: 'top-right',
						stack: false,
						icon: 'info'
					})
				}
				else
				{
					$.toast().reset('all');
					$.toast({
						heading: json['success'],
						text: json['wishlist_removed'],
						position: 'top-right',
						stack: false,
						icon: 'info'
					})	
				}


				setTimeout(function () {
					$('#wishlist-total #total-quantity').html(json['total']);
					$('#total-quantity-mobile').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'account/wishlist') {
					setTimeout(function(){
						location = 'index.php?route=account/wishlist';
					},3000);
				} else {
					$('#wishlist-total > .dropdown_body .reload-section').load('index.php?route=common/wish/info .reload-section');	
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});

	   
	},
	'remove': function(product_id) {

		$.ajax({
			url: 'index.php?route=account/wishlist/remove',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {	
									
				if (json['redirect']) {
					location = json['redirect'];
				}
				$.toast().reset('all');
				$.toast({
					heading: json['success'],
					text: json['wishlist_removed'],
					position: 'top-right',
					stack: false,
					icon: 'info'
				})
				setTimeout(function () {
					$('#wishlist-total #total-quantity').html(json['total']);
					$('#total-quantity-mobile').html(json['total']);
				}, 100);

				if (getURLVar('route') == 'account/wishlist') {
					setTimeout(function(){
						location = 'index.php?route=account/wishlist';
					},3000);
					
				} else {
					$('#wishlist-total > .dropdown_body .reload-section').load('index.php?route=common/wish/info .reload-section');	
				}
				},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}


var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
		
			success: function(json) {

				$.toast().reset('all');
				$.toast({
					heading: json['success'],
					position: 'top-right',
					stack: false,
					icon: 'info'
				})
				setTimeout(function () {
					$('#cart').find('#cart-total').text(json['total']);
					$('#cart-total-mobile').text(json['total']);	
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					setTimeout(function(){
						location = 'index.php?route=checkout/cart';
					},3000);

				} else {
					$('#cart > .dropdown_body .reload-section').load('index.php?route=common/cart/info .reload-section');
				}

			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity, product_id) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1) + '&product_id=' + product_id,
			dataType: 'json',
			success: function(json) {

			setTimeout(function () {
				$('#cart').find('#cart-total').text(json['total']);
				$('#cart-total-mobile').text(json['total']);
				$('.amount').find('#amount').text(json['total']);
				$('.payment').find('.font18').text(json['totals']);
				$('.cart-praduc-right').find('#cost-prag' + product_id).text(json['totalPrice']);
			}, 100);


			$('#cart > .dropdown_body .reload-section').load('index.php?route=common/cart/info .reload-section');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',

			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				$.toast().reset('all');
				$.toast({
					heading: json['success'],
					position: 'top-right',
					stack: false,
					icon: 'info'
				})
				setTimeout(function () {
					$('#cart').find('#cart-total').text(json['total']);	
					$('#cart-total-mobile').text(json['total']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					setTimeout(function(){
						location = 'index.php?route=checkout/cart';
					},3000);
				} else {
					$('#cart > .dropdown_body .reload-section').load('index.php?route=common/cart/info .reload-section');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}	
}

function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}


$('.btn-number-cart').click(function(e){
	e.preventDefault();
	
	fieldName = $(this).attr('data-field');
	type      = $(this).attr('data-type');
	var input = $("input[name='"+fieldName+"']");
	var currentVal = parseInt(input.val());
	var cart_id = parseInt($('input[name=quantityCartId'+ fieldName +']').val());
	var product_id = parseInt($('input[name=quantityProductId'+ fieldName +']').val());

	if (!isNaN(currentVal)) {
		if(type == 'minus') {
			
			if(currentVal > input.attr('min')) {
				input.val(currentVal - 1).change();
			}
			cart.update(cart_id,currentVal - 1,product_id); 
  
		} else if(type == 'plus') {
  
			if(currentVal < input.attr('max')) {
				input.val(currentVal + 1).change();
			}
			cart.update(cart_id,currentVal + 1,product_id);
		}


	} else {
		input.val(0);
	}
  });

$("#callBackForm").submit(function(e) {
	e.preventDefault();
	
	var form = $(this);
	var url = $(this).attr('action');

	$.ajax({
		type: "POST",
		url:  url,
		data: form.serialize(),
		success: function(data) {
			
			if(data.error)
			{

				$.toast().reset('all');
				$.toast({
					heading: data['error_text'],
					text: data.error,
					position: 'top-center',
					stack: false,
					icon: 'error'
				})
			}
			else
			{
				$.toast().reset('all');
				$.toast({
					heading: data,
					position: 'top-center',
					stack: false,
					icon: 'info'
				})
				$("#callBackForm").trigger("reset");
				$("#exampleModalCenter").modal('hide');
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$("#questionForm").submit(function(e) {
	e.preventDefault();
	
	var form = $(this);
	var url = $(this).attr('action');

	$.ajax({
		type: "POST",
		url:  url,
		data: form.serialize(),
		success: function(data) {
			if(data.error)
			{
				$.toast().reset('all');
				$.toast({
					heading: data['error_text'],
					text: data.error,
					position: 'top-center',
					stack: false,
					icon: 'error'
				})
			}
			else
			{
				$.toast().reset('all');
				$.toast({
					heading: data,
					position: 'top-center',
					stack: false,
					icon: 'info'
				})
				$("#questionForm").trigger("reset");
				$("#exampleModalCentersms").modal('hide');
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$("#suggestForm").submit(function(e) {
	e.preventDefault();

	var form = $(this);
	var url = $(this).attr('action');

	$.ajax({
		type: "POST",
		url:  url,
		data: form.serialize(),
		success: function(data) {
			if(data.error)
			{
				$.toast().reset('all');
				$.toast({
					heading: data['error_text'],
					text: data.error,
					position: 'top-center',
					stack: false,
					icon: 'error'
				})
			}
			else
			{
				$.toast().reset('all');
				$.toast({
					heading: data,
					position: 'top-center',
					stack: false,
					icon: 'info'
				})
				$("#suggestForm").trigger("reset");
				setTimeout(function(){
					window.location.href = "/";
				},2000);
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('.offered_price').click(function(e){
	e.preventDefault();
	var url = '/index.php?route=api/order/offer';
	$.ajax({
		type: "GET",
		url:  url,
		success: function(data) {
			if(data['error'])
			{
				$.toast().reset('all');
				$.toast({
					heading: data['error_text'],
					text: data['error_message'],
					position: 'top-center',
					stack: false,
					icon: 'error'
				})
			}
			else{
				window.location.href = "/index.php?route=common/suggest&product_id=" + data['id'];
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});


