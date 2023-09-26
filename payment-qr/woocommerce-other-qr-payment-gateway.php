<?php
/* @wordpress-plugin
 * Plugin Name:       Thanh toán hóa đơn bằng mã QR code
 * Plugin URI:        
 * Description:       Tự động xác nhận thanh toán quét mã QR Code.
 * Version:           1.1.0
 * WC requires at least: 3.0
 * WC tested up to: 7.9
 * Author:            XOKE
 * Author URI:        
 * Text Domain:       woocommerce-other-qr-payment-gateway
 * Domain Path: /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

include_once 'http/httpclient.php';
include_once 'config.php';

add_action('admin_menu', 'plugin_add_admin_menu');

function plugin_add_admin_menu() {
    add_menu_page(
        'QR Code Plugin', // Tiêu đề của trang
        'QR Code', // Tên trên menu
        'manage_options', // Quyền cần để truy cập
        'my-custom-api-plugin', // Slug của trang
        'plugin_settings_page', // Callback hiển thị trang quản lý
        'dashicons-text', // Icon
        99 // Vị trí trên menu
    );
}

// Callback để hiển thị trang quản lý admin
function plugin_settings_page() {
    require 'templates/qr-home.php';
}

// Đăng ký hàm chạy khi plugin được tắt
register_activation_hook(__FILE__, 'register_api');

function register_api() {

	$admin_user = get_userdata(1);
	$password = generateRandomPassword(8);
	$site_url = home_url();

	$api_url = config::$qr_url.'/auth/register';

	$params =  array(
		'email' => $admin_user->user_email,	
		'password' => $password,	
		'webDomain' => $site_url,	
		'name' => $admin_user->display_name,
		'address' => "string",
		'phone' => "000000",
		'dateOfBirth' => $admin_user->user_registered
	  );
	  
	$make_call = callAPI('POST', $api_url, json_encode($params));
	$response = json_decode($make_call);

    if (is_wp_error($response)) {
		add_option('activeKey', 'wp_error');
    } else {

		if($response->statusCode == 409)
		{
			add_option('activeKey', 'account_exists');
		}
		else if($response->statusCode == 400){
			add_option('activeKey', 'validate_data');
		}
		else if($response->statusCode == 200){
			add_option('userId', $response->data->id);
			add_option('activeKey', $response->data->activeKey);
		}
    }
}

// Đăng ký hàm chạy khi plugin được tắt
register_deactivation_hook(__FILE__, 'my_custom_api_plugin_deactivate');

function my_custom_api_plugin_deactivate() {

	$active_key = get_option('activeKey');

	$api_url = config::$qr_url.'/auth/check-active-key';

	$params =  array(
		'username' => $admin_user->user_email,	
		'activeKey' => $active_key
	  );

	$make_call = callAPI('POST', $api_url, json_encode($params));
	$response = json_decode($make_call);

	if (is_wp_error($response)) {
		delete_option('userId'); 
		delete_option('activeKey'); 
    } else {

		if($response->statusCode == 404 || $response->statusCode == 400)
		{
			delete_option('userId'); 
			delete_option('activeKey'); 
		}
    }
}

$active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

if(custom_payment_is_woocommerce_active()){
	
	add_filter('woocommerce_payment_gateways', 'add_other_payment_gateway');
	function add_other_payment_gateway( $gateways ){
		$gateways[] = 'WC_Other_Payment_Gateway';
		return $gateways;
	}

	add_action('plugins_loaded', 'init_other_payment_gateway');
	function init_other_payment_gateway(){
		require 'class-woocommerce-other-qr-payment-gateway.php';
	}

	add_action( 'plugins_loaded', 'other_payment_load_plugin_textdomain' );
	function other_payment_load_plugin_textdomain() {
	  load_plugin_textdomain( 'woocommerce-other-qr-payment-gateway', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	add_action('wp_enqueue_scripts', 'tutsplus_enqueue_custom_js');

	function tutsplus_enqueue_custom_js() {
		wp_enqueue_script('custom', plugin_dir_url( __FILE__ ) .'assets/js/custom.js', array('jquery'), false, true);

		wp_localize_script('custom', 'my_email_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
	}
		
	add_action('woocommerce_thankyou', 'custom_thankyou_message');
	
	function custom_thankyou_message($order_id) {

		require 'templates/qr-payment.php';

	}

	add_action('wp_ajax_send_email', 'my_email_send_email');
	add_action('wp_ajax_nopriv_send_email', 'my_email_send_email');

	function my_email_send_email() 
	{
		$order_id = $_POST['order_id'];
	
		$order = wc_get_order($order_id); 

		$admin_email = get_option('admin_email'); 

		$subject   = 'Đơn hàng '.$order->id.' đã thanh toán '.$order->get_total() . $order->currency;
		
		$api_url = get_rest_url( null, '/custom-order-api/v1/update-status' );

		$body = 'Bạn đã nhận được một đơn đặt hàng thanh toán bằng QR Code :<br>';
		$body .= 'ID Hóa đơn: ' . $order->id . '<br>';
		$body .= 'Tên khách hàng: ' . $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() . '<br>';
		$body .= 'Email: ' . $order->get_billing_email() . '<br>';
		
		$body .= '<br>';
		
		$body .= 'Danh sách sản phẩm :<br>';
		foreach ($order->get_items() as $item_id => $item) {
			$product = $item->get_product();
			$body .= 'Tên sản phẩm: ' . $product->get_name() . '<br>';
			$body .= 'Số lượng: ' . $item->get_quantity() . '<br>';
			$body .= 'Giá: ' . $item->get_subtotal() . $order->currency.'<br>';
		}

		$body .= '<br>';
		$body .= '<p>Xác nhận khách hàng đã thanh toán</p>';

		$url_update_status = $api_url.'?order_id='.$order->id; 

		$body .= '<a type="button"
					href="'.$url_update_status.'">
					Chuyển trạng thái hóa đơn
				</a>
			';
	
		$headers = array('Content-Type: text/html; charset=UTF-8');
	
		// Gửi email
		$result = wp_mail($admin_email, $subject, $body, $headers);
	
		if ($result) {
			echo 'Email sent with order details successfully.';
		} else {
			echo 'Email sending with order details failed.';
		}
	
		wp_die();
	}

	// Đăng ký action
	add_action('init', 'custom_form_handler');
	
	function custom_form_handler() {

		if (isset($_POST['submit_form_update_key'])) {
	
			$key = sanitize_text_field($_POST['key_data']);
	
			update_option('activeKey', $key );
	
			exit();
		}
	}
	


	// Define the API endpoint

	function custom_order_api_update_status() {
		$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;

		if ( $order_id) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$order->update_status( 'completed' );
				return 'Success';
			} else {
				return 'Invalid Order ID';
			}
		} else {
			return 'Missing Parameters';
		}
	}

	function get_order_payment() {
		$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;

		if ($order_id) {
			$order = wc_get_order( $order_id );
			if ( $order ) {

				$data = array(
					'data' => array(
						'order_id' =>  $order_id ,	
						'total' => intval($order->total)
					),
					'status_code' => 200
				  );
				return $data;
			} else {
				$data = array(
					'data' => [],
					'status_code' => 400
				  );
				return $data;
			}
		} else {
			$data = array(
				'data' => [],
				'status_code' => 400
			  );
			return $data;
		}
	}


	// Register the API endpoint
	add_action( 'rest_api_init', function () {
		register_rest_route( 'custom-order-api/v1', '/update-status', array(
			'methods' => 'GET',
			'callback' => 'custom_order_api_update_status',
		) );
	} );

	add_action( 'rest_api_init', function () {
		register_rest_route( 'custom-order-api/v1', '/get-order-payment', array(
			'methods' => 'GET',
			'callback' => 'get_order_payment',
		) );
	} );
}


function generateRandomPassword($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $password .= $characters[$index];
    }
    
    return $password;
}


/**
 * @return bool
 */
function custom_payment_is_woocommerce_active()
{
	$active_plugins = (array) get_option('active_plugins', array());

	if (is_multisite()) {
		$active_plugins = array_merge($active_plugins, get_site_option('active_sitewide_plugins', array()));
	}

	return in_array('woocommerce/woocommerce.php', $active_plugins) || array_key_exists('woocommerce/woocommerce.php', $active_plugins);
}

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
