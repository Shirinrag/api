<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pushnotification_model extends CI_Model {
	
	function place_order_confirmation($user_id='',$order_id='',$amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>1,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}","{amount}"); 
            $dynamic_value = array($order_id,$amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    function bonus_amount($user_id='', $amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>2,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{amount}"); 
            $dynamic_value = array($amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    function money_added_to_wallet($user_id='', $amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>3,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{amount}"); 
            $dynamic_value = array($amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }

    function extended_booking($user_id='',$order_id='',$amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>4,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}","{amount}"); 
            $dynamic_value = array($order_id,$amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }

    function booking_cancel($user_id='',$order_id='',$amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>5,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}","{amount}"); 
            $dynamic_value = array($order_id,$amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    function verify_booking($user_id='',$order_id=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>6,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}"); 
            $dynamic_value = array($order_id);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }

    function extended_booking_by_verifier($user_id='',$order_id='',$amount=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>7,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}","{amount}"); 
            $dynamic_value = array($order_id,$amount);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    function notification_before_half_hours($user_id='',$order_id='')
    {
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>8,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}"); 
            $dynamic_value = array($order_id);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    function verifier_notify_booking($user_id='',$order_id=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>9,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}"); 
            $dynamic_value = array($order_id);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    public function otp_notification($user_id='',$otp='')
    {
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>10,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{otp}"); 
            $dynamic_value = array($otp);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }

     public function booking_accepted($user_id='',$order_id=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>11,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}"); 
            $dynamic_value = array($order_id);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
    public function booking_rejected($user_id='',$order_id=''){
        if (!empty($user_id)) {
            $notification_template_info = $this->model->selectwhereData('tbl_push_notification_messages',array('id'=>12,'del_status'=>'1'),array('title','message','image_path'));
            $dynamic_data = array("{order_id}"); 
            $dynamic_value = array($order_id);
            $notification_data_post['title']=$notification_template_info['title']; 
            $notification_data_post['message']=str_replace($dynamic_data,$dynamic_value,$notification_template_info['message']);
            $this->pushnotifications->android($user_id,$notification_data_post);
            return true;
        } else {
            return true;
        }
    }
}
