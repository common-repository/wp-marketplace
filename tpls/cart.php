<?php

$cart = "<form method='post' action=''><input type='hidden' name='wpdm_update_cart' value='1' /><table class='wpdm_cart'><tr class='cart_header'><th style='width:20px !important'></th><th>Title</th><th>Unit Price</th><th>Quantiry</th><th class='amt'>Total</th></tr>";
if(is_array($cart_items)){
foreach($cart_items as $item){        
    $cart .= "<tr id='cart_item_{$item[id]}'><td><a class='wpdm_cart_delete_item' href='#' onclick='return wpdm_pp_remove_cart_item($item[id])'>[x]</a></td><td class='cart_item_title'>$item[title]</td><td class='cart_item_unit_price'>".number_format($item[price],2)."</td><td class='cart_item_quantity'><input type='text' name='cart_items[$item[id]]' value='$item[quantity]' size=3 /></td><td class='cart_item_subtotal amt'>".number_format($item['price']*$item['quantity'],2)."</td></tr>";
    
}}
$cart .= "

<tr><td colspan=4 align=right>Subtotal:</td><td class='amt' id='wpdm_cart_subtotal'>".wpdm_get_cart_subtotal()."</td></tr>
<tr><td colspan=4 align=right>Discount:</td><td class='amt' id='wpdm_cart_discount'>".wpdm_get_cart_discount()."</td></tr>
<tr><td colspan=4 align=right>Total:</td><td class='amt' id='wpdm_cart_total'>".wpdm_get_cart_total()."</td></tr>
<tr><td colspan=2><input class='button' type='button' onclick='location.href=\"".get_option('wpdm_continue_shopping_url')."\"' value='Continue Shopping' /></td><td colspan=3 align=right><input class='button' type='submit' value='Update Cart' /> <input class='button' type='button' value='CehckOut' onclick='location.href=\"".home_url("/?wpdm_checkout=1")."\"' /></td></tr>
</table>

</form>

<script language='JavaScript'>
<!--
    function  wpdm_pp_remove_cart_item(id){
           if(!confirm('are you sure?')) return false;
           jQuery('#cart_item_'+id+' *').css('color','#ccc');
           jQuery.post('".home_url('?wpdm_remove_cart_item=')."'+id,function(res){ jQuery('#cart_item_'+id).fadeOut().remove(); jQuery('#cart_total').html(res.cart_total); });
           return false;
    }  
//-->
</script>

";

if(count($cart_items)==0) $cart = "No item in cart.<br/><a href='".get_option('wpdm_continue_shopping_url')."'>Continue shopping</a>";
