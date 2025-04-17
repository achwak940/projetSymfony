(function() {
	'use strict';

	var tinyslider = function() {
		var el = document.querySelectorAll('.testimonial-slider');

		if (el.length > 0) {
			var slider = tns({
				container: '.testimonial-slider',
				items: 1,
				axis: "horizontal",
				controlsContainer: "#testimonial-nav",
				swipeAngle: false,
				speed: 700,
				nav: true,
				controls: true,
				autoplay: true,
				autoplayHoverPause: true,
				autoplayTimeout: 3500,
				autoplayButtonOutput: false
			});
		}
	};
	tinyslider();

	


	var sitePlusMinus = function() {

		var value,
    		quantity = document.getElementsByClassName('quantity-container');

		function createBindings(quantityContainer) {
	      var quantityAmount = quantityContainer.getElementsByClassName('quantity-amount')[0];
	      var increase = quantityContainer.getElementsByClassName('increase')[0];
	      var decrease = quantityContainer.getElementsByClassName('decrease')[0];
	      increase.addEventListener('click', function (e) { increaseValue(e, quantityAmount); });
	      decrease.addEventListener('click', function (e) { decreaseValue(e, quantityAmount); });
	    }

	    function init() {
	        for (var i = 0; i < quantity.length; i++ ) {
						createBindings(quantity[i]);
	        }
	    };

	    function increaseValue(event, quantityAmount) {
	        value = parseInt(quantityAmount.value, 10);

	        console.log(quantityAmount, quantityAmount.value);

	        value = isNaN(value) ? 0 : value;
	        value++;
	        quantityAmount.value = value;
	    }

	    function decreaseValue(event, quantityAmount) {
	        value = parseInt(quantityAmount.value, 10);

	        value = isNaN(value) ? 0 : value;
	        if (value > 0) value--;

	        quantityAmount.value = value;
	    }
	    
	    init();
		
	};
	sitePlusMinus();

	document.addEventListener("DOMContentLoaded", function () {
		function updateTotal() {
		  let totalCart = 0;
	 
		  document.querySelectorAll("tr").forEach(function (row) {
			 const priceCell = row.querySelector(".product-price");
			 const quantityInput = row.querySelector(".quantity-amount");
			 const totalCell = row.querySelector(".product-total");
	 
			 if (priceCell && quantityInput && totalCell) {
				const price = parseFloat(priceCell.dataset.price);
				const quantity = parseInt(quantityInput.value);
				const total = price * quantity;
	 
				totalCell.textContent = total.toFixed(2) + " dt";
				totalCart += total;
			 }
		  });
	 
		  const subtotal = document.querySelector("#cart-subtotal");
		  const total = document.querySelector("#cart-total");
		  if (subtotal && total) {
			 subtotal.textContent = totalCart.toFixed(2) + " dt";
			 total.textContent = totalCart.toFixed(2) + " dt";
		  }
		}
	 
		document.querySelectorAll(".increase").forEach(function (btn) {
		  btn.addEventListener("click", function () {
			 const input = btn.closest(".quantity-container").querySelector(".quantity-amount");
			 input.value = parseInt(input.value) + 1;
			 updateTotal();
		  });
		});
	 
		document.querySelectorAll(".decrease").forEach(function (btn) {
		  btn.addEventListener("click", function () {
			 const input = btn.closest(".quantity-container").querySelector(".quantity-amount");
			 let val = parseInt(input.value);
			 if (val > 1) {
				input.value = val - 1;
				updateTotal();
			 }
		  });
		});
	 
		// Mettre à jour les totaux au chargement
		updateTotal();
	 });
	
})()