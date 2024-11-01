<?php //print_r( $fileinfo );  ?>
<style type="text/css">
.wdmiconfile{    
    -webkit-border-radius: 6px;
-moz-border-radius: 6px;
border-radius: 6px;
}
</style>


 
<?php 
$img = array('jpg','gif','jpeg','png');
foreach($fileinfo as $index=>$value): $ext = strtolower(end(explode(".",$value['file']))); if(in_array($ext,$img)): ?>

<label>
<img class="wdmiconfile" id="<?php echo md5($value['file']) ?>" src="<?php  echo plugins_url().'/'.$value['file'] ?>" alt="<?php echo $value['name'] ?>" style="padding:5px; margin:1px; float:left; border:#fff 2px solid " />

<input rel="wdmiconfile" style="display:none" <?php if($icon==$value['file']) echo ' checked="checked" ' ?> type="radio"  name="wpmp_list[icon]"  class="checkbox"  value="<?php echo $value['file'] ?>"></label>

<?php endif; endforeach; ?>

<script type="text/javascript">
//border:#CCCCCC 2px solid


jQuery('#<?php echo md5($icon) ?>').css('border','#008000 2px solid').css('background','#F2FFF2');

jQuery('img.wdmiconfile').click(function(){

jQuery('img.wdmiconfile').css('border','#fff 2px solid').css('background','transparent');
jQuery(this).css('border','#008000 2px solid').css('background','#F2FFF2');



});

</script>

 <div style="clear: both;"></div>