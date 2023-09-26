(function($){
	$('#accept-payment').click(function() {

		document.getElementById("accept-payment").disabled = true;

		// Lấy URL của trang hiện tại
		var currentUrl = window.location.href;

		// Tách ID đơn hàng từ URL bằng cách sử dụng regex
		var orderIdMatch = currentUrl.match(/order-received\/(\d+)/);

		if (orderIdMatch) {
			var orderId = orderIdMatch[1]; // Lấy ID đơn hàng từ match[1]
			console.log("ID đơn hàng: " + orderId);
		} else {
			console.log("Không tìm thấy ID đơn hàng trong URL.");
		}
		
		$.ajax({
            type: 'POST',
            url: my_email_ajax.ajax_url,
            data: {
                action: 'send_email',
                order_id: orderId
            },
            success: function(response) {

				document.getElementById("accept-payment").textContent = 'Thanh toán của bạn đang được xác nhận';

            }
        });
	});
})(jQuery);

