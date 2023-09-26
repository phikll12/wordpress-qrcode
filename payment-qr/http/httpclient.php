<?php

if (!function_exists('callAPI'))   {
    function callAPI($method, $url, $data){
        $curl = curl_init();
        switch ($method){
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){
            die("Connection Failure".curl_error($curl));
        }
        curl_close($curl);
        return $result;
        }
    
  }

  if (!function_exists('get_banks'))   {
    function get_banks()
    {
        $api_url = 'https://dothanhtung.name.vn/bank/get-list';
        $make_call = callAPI('GET', $api_url, '');

        $response  = json_encode($make_call);

        if (is_wp_error($response)) {
           return '';
        } else {
                return $response;
            }
        return '';	
    }

  }
?>