<?php
function wpmp_show_cart($content){
    if(!is_page('cart')) return $content;
    global $wpdb;
    $cart_data = wpmp_get_cart_data();
    foreach($cart_data as $pid=>$cdt){
        extract($cdt);
        if($pid){
        $cart_items[$pid] = $wpdb->get_row("select f.id,f.title,p.price from {$wpdb->prefix}ahm_files f,{$wpdb->prefix}ahm_premium_packages p where f.id=p.pid and f.id='$pid'",ARRAY_A);
        $cart_items[$pid]['quantity'] =  $quantity;
        $cart_items[$pid]['license'] =  $license;
        }
    }
    include("cart.php");
    return $cart;
}

function wpmp_add_to_cart($pid=0, $license=''){  
    $pid = $_REQUEST['wpmp_add_to_cart']?$_REQUEST['wpmp_add_to_cart']:$pid;
    $license = $license?$license:$_REQUEST['license'];
    if($pid<=0) return;
    $cart_data = wpmp_get_cart_data();
    $q = $_REQUEST['quantity']?$_REQUEST['quantity']:1;
    @extract(get_post_meta($pid,'wpmp_list_opts',true));
    $price = $price[$license];
    $cart_data[$pid] = array('quantity'=>$q,'license'=>$license,'price'=>$price);
    wpmp_update_cart_data($cart_data);
    /*if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        die("added");
    } else {
      if(get_option("wpmp_after_addtocart_redirect")=="cartpage")  
      header("location: ".home_url("?pagename=cart"));
      else
      header("location: ".$_SERVER['HTTP_REFERER']);
      die();
    }*/
}

function wpmp_remove_cart_item(){
    if($_REQUEST['wpmp_remove_cart_item']<=0) return;    
    $cart_data = wpmp_get_cart_data();
    unset($cart_data[$_REQUEST['wpmp_remove_cart_item']]);    
    wpmp_update_cart_data($cart_data);
    $ret['cart_subtotal'] = wpmp_get_cart_subtotal();
    $ret['cart_discount'] = wpmp_get_cart_discount();
    $ret['cart_total'] = wpmp_get_cart_total();
    die(json_encode($ret));
}

function wpmp_update_cart(){
    if($_REQUEST['wpmp_update_cart']<=0) return;
    wpmp_update_cart_data($_POST['cart_items']);
    $ret['cart_subtotal'] = wpmp_get_cart_subtotal();
    $ret['cart_discount'] = wpmp_get_cart_discount();
    $ret['cart_total'] = wpmp_get_cart_total();
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    die(json_encode($ret));
    }
}

function wpmp_get_cart_data(){
    global $current_user;
    if(is_user_logged_in()){    
    get_currentuserinfo();
    $cart_id = $current_user->ID."_cart";       
    } else {
    $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
    }
    $cart_data = maybe_unserialize(get_option($cart_id));
    return $cart_data?$cart_data:array();
}

function wpmp_update_cart_data($cart_data){
    global $current_user;
    if(is_user_logged_in()){    
    get_currentuserinfo();
    $cart_id = $current_user->ID."_cart";       
    } else {
    $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
    }
    $cart_data = update_option($cart_id, $cart_data);
    return $cart_data;
}

function wpmp_get_cart_items(){
    global $current_user, $wpdb;    
    $cart_data = wpmp_get_cart_data();    
    return unserialize($cart_items);
}

function wpmp_get_cart_subtotal(){
    $cart_items = wpmp_get_cart_items();
    $total = 0;
    if(is_array($cart_items)){
    foreach($cart_items as $item)    {
        $total +=  ($item['price']*$item['quantity']);
    }}
     
    return number_format($total,2);
}



function wpmp_get_cart_discount(){
    global $current_user;
    get_currentuserinfo();
    $role = $current_user->caps[count($current_user->caps)-1];
    $role = $role?$role:'guest';
    $subtotal = wpmp_get_cart_subtotal();
    $cart_items = wpmp_get_cart_items();
    foreach($cart_items as $pid=>$cin){
       $opt = get_post_meta($pid,'wpmp_list_opts',true); 
       $discount += ($cin['price']*$cid['quantity']*$opt['discount'][$role])/100;
    }
     
    return number_format($discount,2);
}
function wpmp_get_cart_total(){
    return number_format(wpmp_get_cart_subtotal()-wpmp_get_cart_discount(),2);
}

function wpmp_empty_cart(){
    global $current_user;
    if(is_user_logged_in()){    
    get_currentuserinfo();
    $cart_id = $current_user->ID."_cart";       
    } else {
    $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
    }
    delete_option($cart_id);
}

function wpmp_checkout(){
    //if(!$_GET['wpmp_checkout']) return;
    $order = new Order();
    $total = wpmp_get_cart_total();
    if($total<=0) {header("location: ".home_url("/?pagename=cart")); die();}
    $paypal_account = get_option('_wpmp_paypal_account');
    $payment = new Payment();
    $payment->InitiateProcessor('PayPal');
    $payment->Processor->InvoiceNo = uniqid();
    $payment->Processor->OrderTitle = 'WPDM Pro Order# '.$payment->Processor->InvoiceNo;    
    $payment->Processor->Amount = $total;
    $payment->Processor->Currency = get_option('_wpmp_currency',true);
    $cart_data = wpmp_get_cart_data();
    $items = serialize(array_keys($cart_data));
    global $current_user;
    if(is_user_logged_in()){    
    get_currentuserinfo();
    }    
    $order->NewOrder($payment->Processor->InvoiceNo, $payment->Processor->OrderTitle, $items, $payment->Processor->Amount,$current_user->ID,'Processing','Processing',serialize($cart_data));    
    include("checkout.php");    
    wpmp_empty_cart();
    die();
}

function wpmp_addtocart_js(){
    if(get_option('wpmp_ajaxed_addtocart',0)==0) return;
?>
<script language="JavaScript">
<!--
  jQuery(function(){
       jQuery('.wpdm-pp-add-to-cart-link').click(function(){
            if(this.href!=''){
                var lbl;
                var obj = jQuery(this);
                lbl = jQuery(this).html();
                jQuery(this).html('<img src="<?php echo plugins_url();?>/wpdm-premium-packages/images/wait.gif"/> adding...');
                jQuery.post(this.href,function(){
                   obj.html('added').unbind('click').click(function(){ return false; });
                })
            
            }
       return false;     
       });
       
       jQuery('.wpdm-pp-add-to-cart-form').submit(function(){
           
           var form = jQuery(this);
           var fid = this.id;
           form.ajaxSubmit({
               'beforeSubmit':function(){                   
                  jQuery('#submit_'+fid).val('adding...').attr('disabled','disabled');
               },
               'success':function(res){
                   jQuery('#submit_'+fid).val('added').attr('disabled','disabled');
               }
           });
            
       return false;     
       });
  });
//-->
</script>
<?php    
}


function wpmp_buynow($content){    
    global $wpdb, $post, $wp_query;    
    $settings = maybe_unserialize(get_option('_wpmp_settings'));
    if($wp_query->query_vars['wp-marketplace']==''||$_GET['buy']=='')
    return $content;    
    @extract(get_post_meta($post->ID,"wpmp_list_opts",true));
    wpmp_add_to_cart($post->ID, $_GET['buy']);    
    $paypal_account = $settings['paypal_email'];
    $payment = new Payment();
    $payment->InitiateProcessor('PayPal');
    $payment->Processor->OrderTitle = $post->post_title;
    $payment->Processor->InvoiceNo = uniqid();
    $payment->Processor->Amount = number_format($price[$_GET['buy']],2);
    $payment->Processor->Currency = $settings['currency'];
    $order = new Order();
    $cart_data = wpmp_get_cart_data();
    $order->NewOrder($payment->Processor->InvoiceNo, $payment->Processor->OrderTitle, serialize(array_keys($cart_data)), $payment->Processor->Amount,$current_user->ID,'Processing','Processing',serialize($cart_data));    
    include(WP_PLUGIN_DIR."/wp-marketplace/tpls/checkout.php");
    wpmp_empty_cart();
    return '';
}

function update_os(){
    global $wpdb;
    $wpdb->update("{$wpdb->prefix}ahm_orders",array('order_status'=>$_POST['status']),array('order_id'=>$_POST['order_id']));
    die('Order status updated');
}

function update_ps(){
    global $wpdb;
    $wpdb->update("{$wpdb->prefix}ahm_orders",array('payment_status'=>$_POST['status']),array('order_id'=>$_POST['order_id']));
    die('Payment status updated');
}


function ajaxinit(){
if($_POST['action']=='wpmp_ajax_call'){
    
    if(function_exists($_POST['execute']))
        call_user_func($_POST['execute'],$_POST);
        else
        echo "function not defined!";
        
    die();
}
}

function ProceedToPaymentGateway(){    
    global $wpdb,$current_user;
    get_currentuserinfo();
    $order = new Order();
    /*if($current_user->ID==''){
        $loginurl = wp_login_url( $_SERVER['REQUEST_URI'].'&do=proceed&payment='.$_REQUEST['payment_method']);
        $html = <<<REG
        Please <a href='{$loginurl}'>login</a> or enter <br/>email:<input typ= to proceed.</a>
REG;
        die($html);
    } */
    $package = $wpdb->get_row("select * from {$wpdb->prefix}ahm_files where id='$_REQUEST[wpmp_page]'",ARRAY_A);
    $premium = $wpdb->get_row("select * from {$wpdb->prefix}ahm_premium_packages where pid='$package[id]'",ARRAY_A);
    $paypal_account = get_option('_wpmp_paypal_account');
    $payment = new Payment();
    $payment->InitiateProcessor('PayPal');
    $payment->Processor->InvoiceNo = uniqid();
    $payment->Processor->OrderTitle = 'WPDM Pro Order# '.$payment->Processor->InvoiceNo;        
    $payment->Processor->Amount = number_format($premium[price],2);
    $payment->Processor->Currency = get_option('_wpmp_currency',true);
    $items = serialize(array($package['id']));
    $order->NewOrder($payment->Processor->InvoiceNo, $payment->Processor->OrderTitle, $items, $payment->Processor->Amount,$current_user->ID,'Processing','Processing','');
    echo $payment->Processor->ShowPaymentForm(1);      
}
function PayNow(){    
    global $wpdb,$current_user;
    get_currentuserinfo();
    $order = new Order();
    $corder = $order->GetOrder($_REQUEST['order_id']);    
    $paypal_account = get_option('_wpmp_paypal_account');
    $payment = new Payment();
    $payment->InitiateProcessor('PayPal');
    $payment->Processor->OrderTitle = $corder->title;
    $payment->Processor->InvoiceNo = $corder->order_id.'_'.time();
    $payment->Processor->Amount = number_format($corder->total,2);
    $payment->Processor->Currency = get_option('_wpmp_currency',true);
    $items = serialize(array($package['id']));
    echo $payment->Processor->ShowPaymentForm(1);      
}

 
function ProcessOrder(){                                                                       
    global $current_user;
    get_currentuserinfo();
    $order = new Order();    
    if(preg_match("@\/payment\/([^\/]+)\/([^\/]+)@is",$_SERVER['REQUEST_URI'],$process)){
        $gateway = $process[1];
        $page = $process[2];        
        $_POST['invoice'] = array_shift(explode("_",$_POST['invoice']));
        $odata = $order->GetOrder($_POST['invoice']);        
        $current_user = get_userdata($odata->uid);
        $uname = $current_user->display_name;
        $uid = $current_user->ID;
        $email = $current_user->user_email;
                
        $myorders = get_option('_wpmp_users_orders',true);
        if($page=='notify'){
        if(!$uid) {
        $uname = str_replace(array("@",'.'),'',$_POST['payer_email']);   
        $password = $_POST['invoice'];
        $email = $_POST['payer_email'];
        $uid = wp_create_user($uname,$password,$_POST['payer_email']);
        $logininfo = "
         Username: $uname<br/>
         Passworf: $password<br/>
        ";
        }    
            
        
        $order->Update(array('order_status'=>$_POST['payment_status'],'payment_status'=>$_POST['payment_status'],'uid'=>$uid), $_POST['invoice']);        
        
        $sitename = get_option('blogname');
        $message = <<<MAIL
                    Hello {$uname},<br/>
                    Thanks for your business with us.<br/>                    
                    Please <a href="{$myorders}">click here</a> to view your purchased items.<br/>
                    {$myorders} <br/>
                    {$logininfo}                    
                    <br/><br/>
                    Regards,<br/>
                    Admin<br/>
                    <b>{$sitename}</b>
                    
MAIL;
        $headers = 'From: '.get_option('blogname').' <'.get_option('admin_email').'>' . "\r\n\\";
        wp_mail( $email, "You order on ".get_option('blogname'), $message, $headers, $attachments );        
        die("OK");
        }
       /* echo $page;
        print_r($_POST);
        echo $myorders;die();  */
        if($page=='return'&&$_POST['payment_status']=='Completed'){
            if(!$current_user->ID){
            $uname = str_replace(array("@",'.'),'',$_POST['payer_email']);   
            $password = $_POST['invoice'];
            $creds = array();
            $creds['user_login'] = $uname;
            $creds['user_password'] = $password;
            $creds['remember'] = true;
            $user = wp_signon( $creds, false );        
            }            
            die("<script>location.href='$myorders';</script>");
        } 
        //wp_email()
        die();
    }
}

 