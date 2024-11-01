<?php  
global $wpdb;
$orderurl = home_url('/my-orders/');
$loginurl = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));
if ( !is_user_logged_in() ) {

$_ohtml =<<<SIGNIN
      
<center>
Please <a href="{$loginurl}" class="simplemodal-login"><b>Log In or Register</b></a> to access this page
</center>

SIGNIN;

     
} else { 
if($_GET['id']==''&&$_GET['item']=='')    {
$_ohtml = <<<ROW
    
<table class="wpmp-my-orders" width="100%" cellspacing="0">
<tr>
    <th>Title</th>
    <th>Date</th>
    <th style="width: 180px;">Payment Status</th>
    
</tr>

ROW;


foreach($myorders as $order){ 
    $date = date("Y-m-d h:i a",$order->date);
    $items = unserialize($order->items);        
    
    $_ohtml .= <<<ROW
                    <tr class="order">
                        <td><a href='{$orderurl}?id={$order->order_id}'>{$order->title}</a></td>
                        <td>{$date}</td>
                        <td>{$order->order_status}</td>
                        
                    </tr>                    
ROW;

}
$homeurl = home_url('/');
$_ohtml .=<<<END
</table>
<script language="JavaScript">
<!--
  function getkey(file, order_id){
      jQuery('#lic_'+file+'_'+order_id).html('Please Wait...');
      jQuery.post('{$homeurl}',{action:'wpdm_pp_ajax_call',execute:'getlicensekey',fileid:file,orderid:order_id},function(res){
           jQuery('#lic_'+file+'_'+order_id).html("<input type=text style='width:150px;border:0px' readonly=readonly onclick='this.select()' value='"+res+"' />");
      });
  }
//-->
</script>

END;
}
 
if($_GET['id']!=''&&$_GET['item']==''){
$order = $order->GetOrder($_GET['id']);
$cart_data = unserialize($order->cart_data);
$items = array_keys($cart_data);
$_ohtml = <<<OTH
    
<table class="wpmp-my-orders" width="100%" cellspacing="0">
<caption><b>{$order->title} &#187; Order Details </b><br>
&nbsp;</caption>
<tr>
    <th>Item Name</th>
    <th>License</th>     
    <th>Download</th>
</tr>

OTH;


foreach($items as $itemid){
    $item = get_post($itemid);
    $dk = md5($item->files);    
    $download_link = home_url("/?wpmpfile={$itemid}&oid={$order->order_id}");    
    //$licenseurl = home_url("/?task=getlicensekey&file={$itemid}&oid={$order->order_id}");     
    $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$item->post_title}</td>
                        <td>{$cart_data[$itemid][license]}</td>                       
ITEM;
if($order->payment_status=='Completed'){
$_ohtml .= <<<ITEM
                                           
                        <td><a href="{$download_link}">Download</a></td>                        
                    </tr>
ITEM;
}else{
$_ohtml .= <<<ITEM
                        <td>&mdash;</td>                        
                    </tr>
ITEM;
}
}
if($order->payment_status!='Completed'){
    $purl = home_url('/?pay_now='.$order->order_id);
    $_ohtml .= <<<PAY
    <tr class="items"><td colspan="4">Please complete your payment to view download links. <div id="proceed_{$order->order_id}" style="float:right">    
         <select name="payment_method" id="pgdd_{$order->order_id}" style="padding: 0px; margin: 0px;"><option value="PayPal">PayPal IPN</option>
</select> <a onclick="return proceed2payment_{$order->order_id}(this)" href="#"><b>Pay Now</b></a>        
         <script>
         function proceed2payment_{$order->order_id}(ob){
            jQuery(ob).html('Processing...');
            jQuery('#pgdd_{$order->order_id}').attr('disabled','disabled');
            
            jQuery.post('{$purl}',{action:'wpdm_pp_ajax_call',execute:'PayNow',order_id:'{$order->order_id}',payment_method:jQuery('#pgdd_{$order->order_id}').val()},function(res){
                jQuery('#proceed_{$order->order_id}').html(res);
                });
                
                return false;
         }
         </script>
     
    </div></td></tr>
PAY;
}    

$homeurl = home_url('/');
$_ohtml .=<<<EOT
</table>
<script language="JavaScript">
<!--
  function getkey(file, order_id){
      jQuery('#lic_'+file+'_'+order_id).html('Please Wait...');
      jQuery.post('{$homeurl}',{action:'wpdm_pp_ajax_call',execute:'getlicensekey',fileid:file,orderid:order_id},function(res){
           jQuery('#lic_'+file+'_'+order_id).html("<input type=text style='width:150px;border:0px' readonly=readonly onclick='this.select()' value='"+res+"' />");
      });
  }
//-->
</script>

EOT;

}

if($_GET['id']!=''&&$_GET['item']!=''){ 
    $oid = mysql_escape_string($_GET['id']);   
    $order = $order->GetOrder($_GET['id']);
    $cart_data = unserialize($order->cart_data);
    $items = unserialize($order->items);
    $item = (int)$_GET['item'];
    $mxd = $cart_data[$item]?$cart_data[$item]:1;    
    $itemd = $wpdb->get_row("select * from {$wpdb->prefix}ahm_files where id='$item'");
    $lic = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where oid='$oid' and pid='$item'");
    $domain = is_array(unserialize($lic->domain))?unserialize($lic->domain):array($lic->domain);     
    if(count($domain)==1&&$domain[0]=='') $domain = array();
    
      
    $ahtml = <<<OTH
<form method="post" action="">   
<table class="wpmp-my-orders" width="100%" cellspacing="0">
<caption><b><a href='$orderurl?id={$order->order_id}'>{$order->title}</a> &#187; {$itemd->title} </b><br>
License number can be used with {$mxd} domain(s)<br>
<small><i>contact site admin within 3 days after add dmain if you need to change</i></small>
</caption>
<tr>
    <th>Domain Name</th>    
    <th>Status</th>
</tr>

OTH;
foreach($domain as $d){
$ahtml .= <<<OTD
    
 
<tr>
    <td>{$d}</td>    
    <td>active</td>
</tr>
OTD;
}
if(count($domain)<$mxd){
$ahtml .= <<<OTD
    
 
<tr>
    <td><input style='text-align:left' type=text name='domain' /></td>    
    <td><input class='button' type='submit' value='Add Domain' /></td>
</tr>
OTD;
}    
$ahtml .= <<<OTF
    
</table>
</form>
OTF;
    
echo $ahtml;
}
}
?>