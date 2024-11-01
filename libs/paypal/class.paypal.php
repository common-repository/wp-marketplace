<?php
if(!class_exists('PayPal')){
class PayPal extends CommonVers{
    var $TestMode;
    
    var $GatewayUrl = "https://www.paypal.com/cgi-bin";
    var $GatewayUrl_TestMode = "https://www.sandbox.paypal.com/cgi-bin";
    var $Business;
    var $ReturnUrl;
    var $NotifyUrl;
    var $CancelUrl;    
    var $Custom;
    
    function PayPal($TestMode = 0){
        $this->TestMode = $TestMode;                
        if($TestMode==1)
        $this->GatewayUrl = $this->GatewayUrl_TestMode;
        $this->LoadSettings();
    }
    
    function LoadSettings(){    
        $settings = maybe_unserialize(get_option('_wpmp_settings'));
        $this->ReturnUrl = $settings['return_url'];
        $this->NotifyUrl = home_url('/paypal/notify/');
        $this->CancelUrl = $settings['cancel_url'];
        $this->Business =  $settings['paypal_email'];
        $this->TestMode =  $settings['paypal_mode'];
        
        if($this->TestMode)            
        $this->GatewayUrl = $this->GatewayUrl_TestMode;
        
    }
    
    function ShowPaymentForm($AutoSubmit = 0){
        
        if($AutoSubmit==1) $hide = "display:none;'";
        $paypal = plugins_url().'/wpdm-premium-packages/images/paypal.png';
        $Form = " 
                    <form method='post' name='_wpdm_bnf_{$this->InvoiceNo}' id='_wpdm_bnf_{$this->InvoiceNo}' action='{$this->GatewayUrl}'>

                    <input type='hidden' name='business' value='{$this->Business}' />

                    <input type='hidden' name='cmd' value='_xclick' />
                    <!-- the next three need to be created -->
                    <input type='hidden' name='return' value='{$this->ReturnUrl}' />
                    <input type='hidden' name='cancel_return' value='{$this->CancelUrl}' />
                    <input type='hidden' name='notify_url' value='{$this->NotifyUrl}' />
                    <input type='hidden' name='rm' value='2' />
                    <input type='hidden' name='currency_code' value='{$this->Currency}' />
                    <input type='hidden' name='lc' value='US' />
                    <input type='hidden' name='bn' value='toolkit-php' />

                    <input type='hidden' name='cbt' value='Continue' />
                    
                    <!-- Payment Page Information -->
                    <input type='hidden' name='no_shipping' value='' />
                    <input type='hidden' name='no_note' value='1' />
                    <input type='hidden' name='cn' value='Comments' />
                    <input type='hidden' name='cs' value='' />
                    
                    <!-- Product Information -->
                    <input type='hidden' name='item_name' value='{$this->OrderTitle}' />
                    <input type='hidden' name='amount' value='{$this->Amount}' />

                    <input type='hidden' name='quantity' value='1' />
                    <input type='hidden' name='item_number' value='{$this->InvoiceNo}' />
                    <input type='hidden' name='email' value='{$this->ClientEmail}' />
                    <input type='hidden' name='custom' value='{$this->Custom}' />
                    
                    <!-- Shipping and Misc Information -->
                     
                    <input type='hidden' name='invoice' value='{$this->InvoiceNo}' />

                    <noscript><p>Your browser doesn't support Javscript, click the button below to process the transaction.</p>
                    <a style=\"{$hide}\" href=\"#\" onclick=\"jQuery('#_wpdm_bnf').submit();return false;\">Buy Now&nbsp;<img align=right alt=\"paypal\" src=\"$paypal\" /></a>                    </noscript>
                    </form>
         
        
        ";
        
        if($AutoSubmit==1)
        $Form .= "<center><b>Processing....</b></center><script language=javascript>setTimeout('document._wpdm_bnf_{$this->InvoiceNo}.submit()',2000);</script>";
        
        return $Form;
        
        
    }
    
    
    function VerifyPayment() {

          // parse the paypal URL
          $url_parsed=parse_url($this->GatewayUrl);        

          // generate the post string from the _POST vars aswell as load the
          // _POST vars into an arry so we can play with them from the calling
          // script.
          //print_r($_POST);
          
          $this->InvoiceNo = $_POST['invoice'];
          
          $post_string = '';    
          foreach ($_POST as $field=>$value) { 
             $this->ipn_data["$field"] = $value;
             $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
          }
          $post_string.="cmd=_notify-validate"; // append ipn command

          // open the connection to paypal
          $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30); 
          if(!$fp) {
              
             // could not open the connection.  If loggin is on, the error message
             // will be in the log.
             $this->last_error = "fsockopen error no. $errnum: $errstr";
             $this->log_ipn_results(false);       
             return false;
             
          } else { 
     
             // Post the data back to paypal
             fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
             fputs($fp, "Host: $url_parsed[host]\r\n"); 
             fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
             fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
             fputs($fp, "Connection: close\r\n\r\n"); 
             fputs($fp, $post_string . "\r\n\r\n"); 

             // loop through the response from the server and append to variable
             while(!feof($fp)) { 
                $this->ipn_response .= fgets($fp, 1024); 
             } 

             fclose($fp); // close connection

          }
                              
          if (eregi("VERIFIED",$this->ipn_response)) {
      
             // Valid IPN transaction.             
             return true;       
             
          } else {
      
             // Invalid IPN transaction.  Check the log for details.
             $this->VerificationError = 'IPN Validation Failed.';             
             return false;
         
      }
      
   }
    
    
}
}
?>