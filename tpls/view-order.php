<?php
    
    global $wpdb;
    $order->items = unserialize($order->items);
     
?>
<div class="wrap">
    <div class="icon32" id="icon-file-manager"><br></div>
<h2>View Order <img id="lng" style="display: none;" src="images/loading.gif" /></h2>
<div id="msg" style="padding: 5px 10px;display: none;" class="message updated">Message</div>
<div style="float: right;margin-top:-30px">
<h2 style="font-size: 12pt">
Order ID: <?php echo $order->order_id;  ?><br/>
Order Total: $<?php echo number_format($order->total,2);  ?>
</h2>
</div>
<h2 style="font-size: 12pt">Order Items</h2>
<table width="100%" cellspacing="0" class="widefat fixed">
<thead>
<tr><th align="left">Item Name</th><th align="left">Unit Price</th><th align="left">Quanntity</th><th align="left">Subtotal</th></tr>
</thead>
<?php 
$cart_data = unserialize($order->cart_data); 
foreach($order->items as $fid){    
    $ditem = $wpdb->get_row("select f.*,p.price from {$wpdb->prefix}ahm_files as f,{$wpdb->prefix}ahm_premium_packages as p where f.id='{$fid}' and f.id=p.pid");    
    
    $quantity = $cart_data[$fid];
     echo "<tr><td>{$ditem->title}</td><td>$".number_format($ditem->price,2)."</td><td>{$quantity}</td><td>$".number_format($ditem->price*$quantity,2)."</td></tr>";
     
    ?>
    
    <?php
}
?>
</table>
<br />
<b>Order Status: 
                <select id="osv" name="order_status">                                    
                <option <?php if($order->order_status=='Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                <option <?php if($order->order_status=='Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                <option <?php if($order->order_status=='Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                <option <?php if($order->order_status=='Canceled') echo 'selected="selected"'; ?> value="Canceled">Canceled</option>
                </select>
</b>   <input type="button" id="update_os" class="button button-secondary" value="Update">
&nbsp;
<b>Payment Status: 
                <select id="psv" name="payment_status">                                    
                <option <?php if($order->payment_status=='Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                <option <?php if($order->payment_status=='Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                <option <?php if($order->payment_status=='Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                <option <?php if($order->payment_status=='Canceled') echo 'selected="selected"'; ?> value="Canceled">Canceled</option>
                </select>
</b>   <input id="update_ps" type="button" class="button button-secondary" value="Update">

</div>
<script language="JavaScript">
<!--
  jQuery(function(){
     
      jQuery('#update_os').click(function(){
          jQuery('#lng').fadeIn();
          jQuery.post(ajaxurl,{action:'wpdm_pp_ajax_call',execute:'update_os',order_id:'<?php echo $_GET[id]; ?>',status:jQuery('#osv').val()},function(res){
              jQuery('#msg').html(res).fadeIn();
              jQuery('#lng').fadeOut();
          });
      });
      
      jQuery('#update_ps').click(function(){
          jQuery('#lng').fadeIn();
          jQuery.post(ajaxurl,{action:'wpdm_pp_ajax_call',execute:'update_ps',order_id:'<?php echo $_GET[id]; ?>',status:jQuery('#psv').val()},function(res){
              jQuery('#msg').html(res).fadeIn();
              jQuery('#lng').fadeOut();
          });
      });
      
      
  });
//-->
</script>