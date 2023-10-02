<?php

$status = '';
$active_key = get_option('activeKey');
$user_id = get_option('userId');

include_once 'qr-header.php';

?>

<div class ="home-screen">
  <div class="banner">
      <img src="<?php echo plugin_dir_url( __DIR__ ).'assets/img/banner.jpg'; ?>"  alt=""  />
  </div>

  <div class="text-wraper">
      <h1 class="head-title">Tự động tạo QR code <br>cho mỗi giao dịch của khách hàng</h1>
      <?php
      if($active_key == 'wp_error'){
          echo '<p>Có lẽ kích hoạt plugin có vấn đề. Bạn vui long ngưng kích hoạt và kích hoạt lại plugin.</p>';
      }
      else if ($active_key == 'account_exists'){
          echo '<p>Dường như bạn đã đăng ký tài khoản với email và domain này.</p>';
          echo '<p>Vui lòng truy cập vào email để lấy khóa kích hoạt và nhập vào ô bên dưới.</p>' ;
        ?>
          <form method="post" action="" class="update-key-form">
            <input  class="form-control" type="text" name="key_data" placeholder="Nhập khóa ở đây" />
            <input class="form-submit" type="submit" name="submit_form_update_key" value="Chỉnh sửa khóa kích hoạt" />
          </form>
      <?php    
      }
      else {
          echo '<p>Khóa kích hoạt : '. $active_key .'</p>';
      }
      ?>
      <a href="<?php echo home_url().'/wp-admin/admin.php?page=wc-settings&tab=checkout&section=other_payment'; ?>">Tiến hành cài đặt thanh toán</a>
  </div>
  
</div>

<style>

.home-screen {
  height: 100%;
  min-height: 100vh;
  position: relative;
  color: white;
  top: 0;
  left: 0;
  bottom:0;
  right:0;
}

.text-wraper {
  position: fixed;
  top: 45%;
  left: 30%;
  transform: translate(-50%, -50%);
}

.text-wraper > p {
  color: #1d2327;
  font-size: 17px;
  line-height: 1.3;
}

.text-wraper > a {
  border-radius: 30px;
  padding: 16px 30px;
  margin : 0.67em 0;
  border-width: 2px;
  background: #4285f4;
  border-color: #4285f4;
  color: #fff;
  position: relative;
  top: 0;
  -o-transition: .3s all ease;
  transition: .3s all ease;
  box-shadow: 0 4px 20px -5px rgba(66, 133, 244, 0.4);
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 17px;
}

.text-wraper .update-key-form {
    display: contents;
    margin-top: 0em;
}

.text-wraper .form-control {
    border: 2px solid #e9ecef;
    font-size: 16px;
    height: 45px;
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

.text-wraper .form-submit {
    border-radius: 30px;
    padding: 14px 30px;
    margin: 16px 10px 16px 0px;
    border-width: 2px;
    background: #4285f4;
    border-color: #4285f4;
    color: #fff;
    position: relative;
    top: 0;
    -o-transition: .3s all ease;
    transition: .3s all ease;
    text-align: center;
    font-size: 17px;
    border-style: none;
}


.text-wraper .head-title {
  color: #1d2327;
  font-weight: 700;
  font-size: 40px;
  line-height: 1.3;
}

.banner img{
    max-height: 100%;
    width: 100%;
    height: auto;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}


</style> 


    

