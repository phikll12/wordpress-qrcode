<?php

$status = '';
$active_key = get_option('activeKey');
$user_id = get_option('userId');

if($active_key == 'wp_error'){
    $status = 'Có lẽ kích hoạt plugin có vấn đề. Bạn vui long ngưng kích hoạt và kích hoạt lại plugin.';
}
else if ($active_key == 'account_exists'){
    $status = 'Dường như bạn đã đăng ký tài khoản với email và domain này. <br> ';
    $status .= 'Vui lòng truy cập vào email để lấy key kích hoạt và nhập vào ô bên dưới.' ;
}
else {
    $status = $active_key;
}

include_once 'qr-header.php';

?>


<!-- Header Section Start -->
<header id="home" class="hero-area">    
  <div class="overlay">
    <span></span>
  </div>
  <div class="header-content">      
    <div class="row space-100">
      <div class="col-lg-12 col-md-12 col-xs-12">
        <div class="contents">
          <h2 class="head-title">Tự động tạo QR code <br>cho mỗi giao dịch của khách hàng</h2>
          <?php
          if($active_key == 'wp_error'){
              echo '<p>Có lẽ kích hoạt plugin có vấn đề. Bạn vui long ngưng kích hoạt và kích hoạt lại plugin.</p>';
          }
          else if ($active_key == 'account_exists'){
              echo '<p>Dường như bạn đã đăng ký tài khoản với email và domain này.</p>';
              echo '<p>Vui lòng truy cập vào email để lấy key kích hoạt và nhập vào ô bên dưới..</p>' ;
            ?>
              <form method="post" action="">
                <input type="text" name="key_data" placeholder="Nhập key ở đây" />
                <input type="submit" name="submit_form_update_key" value="Gửi" />
              </form>
          <?php    
          }
          else {
              echo '<p> KEY của bạn : '. $active_key .'</p>';
          }

          ?>
          
          <a href="<?php echo home_url().'/wp-admin/admin.php?page=wc-settings&tab=checkout&section=other_payment'; ?>"><p>Tiến hành cài đặt thanh toán</p> </a>
        </div>
      </div>
    </div> 
  </div>            
</header>




    

