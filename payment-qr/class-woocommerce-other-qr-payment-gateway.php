<?php

require 'http/httpclient.php';

class WC_Other_Payment_Gateway extends WC_Payment_Gateway{

    private $order_status;

	public function __construct(){
		$this->id = 'other_payment';
		$this->method_title = __('Thanh toán bằng QR Code','woocommerce-other-payment-gateway');
		$this->title = __('Thanh toán bằng QR Code','woocommerce-other-payment-gateway');
		$this->has_fields = true;
		$this->init_form_fields();	
		$this->init_settings();
		$this->enabled = $this->get_option('enabled');
		$this->title = $this->get_option('title');
		$this->description = __('Khách hàng chọn thanh toán trả trước bằng QR Code','woocommerce-other-payment-gateway');
		$this->hide_text_box = $this->get_option('hide_text_box');
		$this->text_box_required = $this->get_option('text_box_required');
		$this->order_status = $this->get_option('order_status');


		add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));

	}


	public function init_form_fields(){
				$this->form_fields = array(
					'enabled' => array(
					'title' 		=> __( 'Bật/Tắt', 'woocommerce-other-payment-gateway' ),
					'type' 			=> 'checkbox',
					'label' 		=> __( 'Bật thanh toán bằng QR', 'woocommerce-other-payment-gateway' ),
					'default' 		=> 'yes'
					),
					'bank_name_and_code' => array(
						'title' => __( 'Ngân hàng', 'woocommerce-other-payment-gateway' ),
						'label' => 'Chọn ngân hàng',
						'type' => 'select',
						'options' => array(
							'ICB-Vietinbank' => 'Vietinbank',
							'VCB-Vietcombank' => 'Vietcombank',
							'BIDV-BIDV' => 'BIDV',
							'VBA-Agribank' => 'Agribank',
							'OCB-OCB' => 'OCB',
							'MB-MBBank' => 'MBBank',
							'TCB-Techcombank' => 'Techcombank',
							'ACB-ACB' => 'ACB',
							'VPB-TPBank' => 'TPBank',
							'SCB-SCB' => 'SCB',
							'VIB-VIB' => 'VIB',
							'SHB-SHB' => 'SHB',
							'EIB-Eximbank' => 'Eximbank'
						),
						'default' => 'ICB-Vietinbank'
					),
					'bank_account_name' => array(
						'title' => __( 'Tên chủ tài khoản', 'woocommerce-other-payment-gateway' ),
						'type' => 'text',
						'description' 	=> __( 'Tên chủ tài khoản', 'woocommerce-other-payment-gateway' ),
						'default'		=> __( 'Nguyễn Văn A', 'woocommerce-other-payment-gateway' ),
						'desc_tip'		=> true,
					),
					'bank_account_number' => array(
						'title' => __( 'Số tài khoản', 'woocommerce-other-payment-gateway' ),
						'type' => 'text',
						'description' 	=> __( 'Số tài khoản', 'woocommerce-other-payment-gateway' ),
						'default'		=> __( '106812051995', 'woocommerce-other-payment-gateway' ),
						'desc_tip'		=> true,
					),
					'order_status' => array(
						'title' => __( 'Trạng thái sau khi thanh toán', 'woocommerce-other-payment-gateway' ),
						'type' => 'select',
						'options' => wc_get_order_statuses(),
						'default' => 'wc-pending',
						'description' 	=> __( 'Trạng thái của hóa đơn sau khi đặt hàng', 'woocommerce-other-payment-gateway' ),
					)
			 );
	}

	public function process_payment( $order_id ) {
		global $woocommerce;
		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		$order->update_status($this->order_status, __( 'Awaiting payment', 'woocommerce-other-payment-gateway' ));

		// Reduce stock levels
		wc_reduce_stock_levels( $order_id );

		if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
			$order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']));
		}

		// Remove cart
		$woocommerce->cart->empty_cart();

		$chosen_payment_method = $order->get_payment_method();

		// if ( $chosen_payment_method == 'other_payment') {

		// 	do_action('send_mail_payment_confirmed_action',$order);
		
		// } 

		// Return thankyou redirect
		return array(
			'result' => 'success',
			'redirect' => $this->get_return_url( $order )
		);
	}
}
