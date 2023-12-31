<?php

require_once(dirname(__DIR__). '/http/httpclient.php');
require_once(dirname(__DIR__). '/config.php');

// Lấy thông tin đơn hàng
$order = wc_get_order($order_id);

// Lấy phương thức thanh toán của đơn hàng
$payment_method = $order->get_payment_method();

$user_id = get_option('userId');

$uri = config::$qr_url;

// Lấy thông tin cài đặt của phương thức thanh toán
$payment_gateways = WC_Payment_Gateways::instance();
$payment_gateway = $payment_gateways->payment_gateways()[$payment_method];
$bank_setting = $payment_gateway->settings['bank_name_and_code'];
$bank_code = explode("-", $bank_setting)[0];
$bank_namme = explode("-", $bank_setting)[1];
$bank_account_number = $payment_gateway->settings['bank_account_number'];
$bank_account_name = $payment_gateway->settings['bank_account_name'];

$total = intval($order->total);
$transfer_content = $user_id.'z'.$order->id ;

$qr_code_resonnse = payment_fields($user_id,$order,$bank_code,$bank_account_number,$uri);

$update_status_url = get_rest_url( null, '/custom-order-api/v1/update-status' );

function payment_fields($user_id,$order,$bank_code,$bank_account_number,$uri){
    

    $site_url = home_url();

    $url = $uri.'/qrcode/generate-bank-qrcode';

    $data_array =  array(
        'email' => get_option('admin_email'),	
        'activeKey' => get_option('activeKey'),
        'webDomain' => $site_url,		
        'bankCode' => $bank_code,
        'bankAccount' => $bank_account_number,
        'amount' => intval($order->total),
        'message' => $user_id.'z'.$order->id 
    );
    
    $make_call = callAPI('POST', $url, json_encode($data_array));
    
    if (is_wp_error($make_call)) {
        return $make_call;
    } else {
        $response = json_decode($make_call);	

        return $response;
    }
}

?>
<?php
    if($payment_method == 'other_payment'){
        ?>
        <section class="woocommerce-customer-details">
            <section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">

                <div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2 ">
                    <h2 class="woocommerce-column__title">Thông tin thanh toán</h2>
                    <address> 
                        <div class="qr-payment"> 
                            <div class="payment-info">
                                <p class="">Ngân hàng : <?php echo $bank_namme ?></p>
                                <p class="">Chủ tài khoản : <?php echo $bank_account_name; ?> </p>
                                <p class="">Số tài khoản : <?php echo $bank_account_number; ?></p>
                                <p class="">Số tiền : <?php echo intval($order->total); ?> đ</p>
                                <p class="">Nội dung : <?php echo $user_id.'z'.$order->id ; ?></p>
                            </div>   
                            <div class="payment-qr-code">
                                <?php    
                                if($qr_code_resonnse->statusCode == 200)
                                {
                                    ?>                        
                                        <img src="<?php echo $qr_code_resonnse->data->base64Image; ?>" />
                                    <?php
                                }
                                else {
                                    ?>
                                    <p class="woocommerce_other_payment_bank_code"><?php echo json_encode($qr_code_resonnse); ?></p>
                                    <?php
                                }
                                ?>
                            </div>   
                        </div>
                        <p class="payment-note">Chú ý : Quý khách hãy kiểm tra lại thông tin thật rõ ràng trước khi thanh toán. </p>
                        <button id="accept-payment" class="button alt accept-payment" disabled>Đang chờ thanh toán</button>
                    </address> 
                </div><!-- /.col-2 -->

            </section><!-- /.col2-set -->
        </section>
        <style>
            .qr-payment  {
                display: flex;
                justify-content: space-between;
            }
            .qr-payment .payment-info p {
                font-size: 18px;
            }
            .payment-note {
                font-size: 18px !important;
            }

            .qr-payment .payment-qr-code {
                margin-bottom: 15px;
            }
            .qr-payment .payment-qr-code img {

            }

           .accept-payment
           {
                width: 100% ;
                text-align: center ;
           }     
        </style>    

        <script>
            
            (function($){

                var url = '<?php echo $uri ?>';
                console.log(url);
                var order_id = '<?php echo $order_id ?>';
                console.log(order_id);
                var user_id = '<?php echo $user_id ?>';
                console.log(user_id);
                var total = '<?php echo $total ?>';
                console.log(total);
                var bank_account_number = '<?php echo $bank_account_number ?>';
                console.log(bank_account_number);
                var transfer_content = '<?php echo $transfer_content ?>';
                console.log(transfer_content);
                var update_status_url = '<?php echo $update_status_url ?>';
                console.log(update_status_url);

                // Dữ liệu bạn muốn gửi dưới dạng đối tượng JSON
                var requestData = {
                    userId : user_id,	
                    accountNumber : bank_account_number,	
                    paymentAmount : total,	
                    transferContent : transfer_content    
                };

                setInterval(function () {

                    $.ajax({
                        url: url + "/transaction/get-transaction-info",
                        type: "POST",
                        dataType: "json",
                        data: JSON.stringify(requestData), 
                        contentType: "application/json", 
                        success: function (data) {
                            document.getElementById("accept-payment").textContent = "Thanh toán của bạn đã được xác nhận"

                            $.ajax({
                                url: update_status_url + "?order_id="+order_id,
                                type: "GET",
                                success: function (data) {                      
                                }
                            });
                        },
                        error: function (error) {
                            
                        }
                    });
                }
                , 1000);    
            })(jQuery);
        </script>
<?php
}
?>

