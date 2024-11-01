<?php
function wpmp_product_files(){             
    $m = get_post_meta($_GET['post'],"wpmp_list_opts",true);
    @extract($m); 
    
    $adpdir = WP_PLUGIN_DIR.'/wp-marketplace/product-files/';     
?>
<style type="">
#pfUploader {background: transparent url('<?php echo plugins_url(); ?>/wp-marketplace/images/browse.png') left top no-repeat; }
#pfUploader:hover {background-position: left bottom; }
.highlight{
    border:1px solid #F79D2B;
    background:#FDE8CE;
    width: 40px;
    height: 40px;
    float:left;padding:5px;
}
.pf{
    float:left;padding:5px;
}
.dfile{
    padding:5px 10px;
    border:1px solid #FFCCBF;
    margin-bottom: 3px;
}
.cfile{
    padding:5px 10px;
    border:1px solid #ccc;
    margin-bottom: 3px;
}
.dfile img,.cfile img{
    cursor: pointer;
}
</style>
  
<div id="currentfiles">

<?php

 

//if( empty($files)  ) $files = array();

//foreach($files as $value):
if($file!=''){
$value = $file;
$filename = end( explode('/',$value)  );
$filename = preg_replace("/wpmp\-([0-9]+)\-/","",$filename);
if(strlen($filename)>20)
$filename = substr($filename,0,10).'...'.substr($filename,strlen($filename)-13);
?>
<div class="cfile">
<input type="hidden" value="<?php echo $value; ?>" name="wpmp_list[files][]">
<nobr>
<b><img align="left" rel="del" src="../wp-content/plugins/download-manager/images/remove.png">&nbsp;<?php echo  $filename; ?></b>
</nobr>
<div style="clear: both;"></div>
</div>


<?php
}
//endforeach;

?>


<?php if($files):  ?>
<script type="text/javascript">


jQuery('img[rel=del], img[rel=undo]').click(function(){

     if(jQuery(this).attr('rel')=='del')
     {
     
     jQuery(this).parents('div.cfile').removeClass('cfile').addClass('dfile').find('input').attr('name','del[]');
     jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/download-manager/images/add.png').attr('title','Undo Delete');
     
     } else {
     
     
            jQuery(this).parents('div.dfile').removeClass('dfile').addClass('cfile').find('input').attr('name','files[]');
            jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/download-manager/images/remove.png').attr('title','Delete File');

     
     
     }



});



</script>


<?php endif; ?>



</div>
<br clear="all" />
<input type="file" id="pf" name="pf">
   
<div class="clear"></div>
 
 
<script type="text/javascript">
      
      jQuery(document).ready(function() {         
        jQuery('#pf').uploadify({
    
          'uploader'  : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/uploadify.swf',   
          /*'script'    : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/uploadify.php',  */
          'script'    : '<?php echo home_url('/wp-admin/');?>admin.php?task=wpmp_upload_product_files',                      
          'cancelImg' : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/cancel.png',  
          'folder'    : '<?php echo str_replace($_SERVER['DOCUMENT_ROOT'],'',UPLOAD_DIR); ?>',
          'multi'  : false,
          'wmode': 'transparent',
          'hideButton': true,
          'auto'      : true,
          'width': 150,
          'height': 30,
          'onComplete': function(event, ID, fileObj, response, data) {                            
                            var nm = fileObj.name;
                            if(fileObj.name.length>20) nm = fileObj.name.substring(0,7)+'...'+fileObj.name.substring(fileObj.name.length-10);
                            jQuery('#currentfiles').html("<div id='"+ID+"' style='display:none' class='cfile'><input type='hidden' id='in_"+ID+"' name='wpmp_list[file]' value='"+response+"' /><nobr><b><img id='del_"+ID+"' src='<?php echo plugins_url(); ?>/wp-marketplace/images/remove.png' rel='del' align=left />&nbsp;"+nm+"</b></nobr><div style='clear:both'></div></div>");
                            jQuery('#'+ID).fadeIn();
                            jQuery('#del_'+ID).click(function(){
                                if(jQuery(this).attr('rel')=='del'){
                                jQuery('#'+ID).removeClass('cfile').addClass('dfile');
                                jQuery('#in_'+ID).attr('name','del[]');
                                jQuery(this).attr('rel','undo').attr('src','<?php echo plugins_url(); ?>/wp-marketplace/images/add.png').attr('title','Undo Delete');
                                } else if(jQuery(this).attr('rel')=='undo'){
                                jQuery('#'+ID).removeClass('dfile').addClass('cfile');
                                jQuery('#in_'+ID).attr('name','files[]');
                                jQuery(this).attr('rel','del').attr('src','<?php echo plugins_url(); ?>/wp-marketplace/images/remove.png').attr('title','Delete File');
                                }
                                
                                
                            });
                            
                         }    
        });
                        
        jQuery('.del_adp').click(function(){
                                if(confirm('Are you sure?')){
                                    jQuery.post(ajaxurl,{action:'wpmp_delete_product_file',file:jQuery('#in_'+jQuery(this).attr('rel')).val()})
                                    jQuery('#'+jQuery(this).attr('rel')).fadeOut().remove();
                                }
                                
                            });
   
      });
  
      </script>
<?php    
} 


function wpmp_upload_product_files(){
     $adpdir = WP_PLUGIN_DIR.'/wp-marketplace/product-files/';     
     if(is_uploaded_file($_FILES['Filedata']['tmp_name'])&&$_GET['task']=='wpmp_upload_product_files'){
        $tempFile = $_FILES['Filedata']['tmp_name'];    
        $targetFile =  $adpdir ."wpmp-". time().'-'.wpmp_format_name($_FILES['Filedata']['name']);
        move_uploaded_file($tempFile, $targetFile);
        echo basename($targetFile);        
        die();
     }
     
}

function wpmp_delete_product_file(){
    @unlink(WP_PLUGIN_DIR.'/wp-marketplace/product-files/'.$_POST['file']);
    die();
}

 

function wpmp_meta_box_product_upload($meta_boxes){
    $meta_boxes['wp-marketplace-files'] = array('title'=>'Upload Product File','callback'=>'wpmp_product_files','position'=>'side','priority'=>'core');
    return $meta_boxes;
}

if(is_admin())  {
    wp_enqueue_script('swfobject',plugins_url().'/wp-marketplace/uploadify/swfobject.js');
    wp_enqueue_script('uploadify',plugins_url().'/wp-marketplace/uploadify/jquery.uploadify.v2.1.4.min.js');
    wp_enqueue_style('uploadify',plugins_url().'/wp-marketplace/uploadify/uploadify.css');
    
    add_action("init","wpmp_upload_product_files");
    add_action("wp_ajax_wpmp_delete_product","wpmp_delete_product");
    add_filter("wpmp_meta_box","wpmp_meta_box_product_upload");

}

 
