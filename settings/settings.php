 <style>
.wrap *{
    font-family: Tahoma;
    letter-spacing: 1px;
}

input[type=text],textarea{
    width:500px;
    padding:5px;
}

input{
   padding: 7px; 
}
.cats li{   
    width:20%;
    float: left;
}
</style>
 <?php
    $settings = maybe_unserialize(get_option('_wpmp_settings'));
    
?>
<!--[if IE]>
<style>
ul#navigation { 
border-bottom: 1px solid #999999;
}
</style>
<![endif]-->
<div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
<h2>Marketplace Settings <img style="display: none;" id="wdms_loading" src="images/loading.gif" /></h2>

<header>
    <h1>Settings</h1>
</header>

<nav> 
    <ul>
        <li class="selected"><a href="#tab1">Basic Settings</a></li>
        <li><a href="#tab2">Payment Options</a></li>         
        
    </ul>
</nav>
 
<div class="updated" style="padding: 5px;display: none;" id="message"></div>
<form method="post" id="wpmp_settings_form">
<section class="tab" id="tab1">
<input type="hidden" name="action" value="wpmp_save_settings">
<div id="settings">    <br>
<b>Exclude selected categgories from marketplace:</b> <br>
<br>

<ul class="cats">
<?php $categories = get_categories( array('hide_empty' => 0) );
foreach($categories as $category){
  $ckd = @in_array($category->slug, $settings['cats'])?'checked=checked':'';
  echo "<li><label for='{$category->term_id}'><input $ckd type='checkbox' id='{$category->term_id}' name='_wpmp_settings[cats][]' value='{$category->slug}'> {$category->name}</label></li>";
}
?>
</ul>
</div> 
<div><br>
<div style="clear: both;"><br/></div>
 
<b>Product Page Thumb Size</b><br/>
Width: <input type="text" style="width: 60px" size="5" name="_wpmp_settings[vw]" value="<?php echo $settings['vw']; ?>">px  &nbsp;&nbsp;&nbsp;Height: <input type="text" size="5" name="_wpmp_settings[vh]" style="width: 60px" value="<?php echo $settings['vh']; ?>">px


</div>

</section>

<section class="tab" id="tab2">
 

<table>
<tr><td>Paypal Mode:</td><td><select name="_wpmp_settings[paypal_mode]"><option value="live">Live</option><option value="sandbox" <?php echo $settings[paypal_mode]=='sandbox'?'selected=selected':''; ?>>SandBox</option></select></td></tr>
<tr><td>Paypal Email:</td><td><input type="text" name="_wpmp_settings[paypal_email]" value="<?php echo $settings['paypal_email']; ?>" /></td></tr>
<tr><td>Cancel Url:</td><td><input type="text" name="_wpmp_settings[cancel_url]" value="<?php echo $settings['cancel_url']; ?>" /></td></tr>
<tr><td>Return Url:</td><td><input type="text" name="_wpmp_settings[return_url]" value="<?php echo $settings['return_url']; ?>" /></td></tr>
<tr><td>Currency:</td><td><input type="text" name="_wpmp_settings[currency]" value="<?php echo $settings['currency']; ?>" /></td></tr>
</table>



</section>
 <br>
<br>


<input type="reset" value="Reset" class="button-secondary" style="padding: 8px 20px;">
<input type="submit" value="Save Settings" class="button-primary" style="padding: 8px 20px;">   
<img style="display: none;" id="wdms_saving" src="images/loading.gif" />
</form>
<br>
 
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
    
    jQuery('#wpmp_settings_form').submit(function(){
       
       jQuery(this).ajaxSubmit({
        url:ajaxurl,
        beforeSubmit: function(formData, jqForm, options){
          jQuery('#wdms_saving').fadeIn();  
        },   
        success: function(responseText, statusText, xhr, $form){
          jQuery('#message').html("<p>"+responseText+"</p>").slideDown();
          //setTimeout("jQuery('#message').slideUp()",4000);
          jQuery('#wdms_saving').fadeOut();  
          jQuery('#wdms_loading').fadeOut();  
        }   
       });
        
       return false; 
    });
    
   
});
 
</script>
