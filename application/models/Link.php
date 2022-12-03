<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Link extends CI_Model {

function hits($link,$request,$token='',$type = 1)
    {
        $Base_API = 'http://localhost/Parking_Adda/api/';
        $query = http_build_query($request);
        if ($type == 0) {
            $custom_type = 'GET';
            $url = $Base_API . $link . "?" . $query;
        } else {
            $custom_type = 'POST';
            $url = $Base_API . $link;
        }
      
        // $data = json_encode($data);
        $header = array("Authorization:".token_get());
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_type);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response1 = curl_exec($ch);
        curl_close($ch);
        return $response1;
    }
} 

