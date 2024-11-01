<?php
class Order{
    
    function Order(){
        
    }
    
    function NewOrder($id, $title, $items, $total, $userid, $order_status = 'processing', $payment_status = 'processing',$cart_data=''){
        global $wpdb, $current_user;        
        get_currentuserinfo();
         
        $wpdb->insert("{$wpdb->prefix}mp_orders",array('order_id'=>$id, 'title'=>$title,'date'=>time(), 'items'=> $items,'total'=> $total, 'order_status'=>$order_status, 'payment_status'=> $payment_status, 'uid'=> $userid,'cart_data'=>$cart_data,'uid'=>$current_user->ID));                
        $cart_data = unserialize($cart_data);
        foreach($cart_data as $pid=>$cdt){
            extract($cdt);             
            $wpdb->insert("{$wpdb->prefix}mp_order_items",array('oid'=>$id,'pid'=>$pid,'quantity'=>$quantity,'license'=>$license,'price'=>$price));             
        }
    }
    
    function Update($data, $id){
        global $wpdb;
        $wpdb->update("{$wpdb->prefix}mp_orders",$data,array('order_id'=>$id));         
        return $this;
    }
    
    function GetOrder($id) {
        global $wpdb;
        $id = addslashes($id);
        return $wpdb->get_row("select * from {$wpdb->prefix}mp_orders where order_id='$id'");
    }

    function GetOrders($id) {
        global $wpdb;
        return $wpdb->get_results("select * from {$wpdb->prefix}mp_orders where uid='$id'");
    }
    
    function GetAllOrders($qry="",$s=0, $l=20) {
        global $wpdb;
        return $wpdb->get_results("select * from {$wpdb->prefix}mp_orders $qry order by `date` desc limit $s,$l");
    }
    
    function totalOrders($qry=''){
        global $wpdb;
        return $wpdb->get_var("select count(*) from {$wpdb->prefix}mp_orders $qry");
    }
    
    function Delete($id){
        global $wpdb;
        return $wpdb->query("delete from {$wpdb->prefix}mp_orders where order_id='$id'");
    }
    
    
}