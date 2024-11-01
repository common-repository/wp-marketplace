<?php
function wppre_additional_preview_images(){             
    @extract(get_post_meta($_GET['post'],"wpmp_list_opts",true)); 
    $adpdir = WP_PLUGIN_DIR.'/wp-marketplace/previews/';     
?>
<style type="">
#apvUploader {background: transparent url('<?php echo plugins_url(); ?>/wp-marketplace/images/browse.png') left top no-repeat; }
#apvUploader:hover {background-position: left bottom; }
.highlight{
    border:1px solid #F79D2B;
    background:#FDE8CE;
    width: 40px;
    height: 40px;
    float:left;padding:5px;
}
.adp{
    float:left;padding:5px;
}
</style>
  
<ul id="adpcon">
<?php
    
    if(is_array($images)){
        foreach($images as $mpv){
            if(file_exists($adpdir.$mpv)){
            ?>
             <li id='<?php echo ++$mmv; ?>' class='adp'>
             <input type='hidden'  id='in_<?php echo $mmv; ?>' name='wpmp_list[images][]' value='<?php echo $mpv; ?>' />
             <img style='position:absolute;z-index:9999;cursor:pointer;' id='del_<?php echo $mmv; ?>' rel="<?php echo $mmv; ?>" src='<?php echo plugins_url(); ?>/wp-marketplace/images/remove.png' class="del_adp" align=left />
             <img src='<?php echo plugins_url(); ?>/wp-marketplace/libs/timthumb.php?w=50&h=50&zc=1&src=previews/<?php echo $mpv; ?>'/>
             <div style='clear:both'></div>
             </li>
            <?php
        }}
    }
?>
</ul><br clear="all" />
<input type="file" id="apv" name="apv">

<div class="clear"></div>
 
 
<script type="text/javascript">
      
      jQuery(document).ready(function() {
        jQuery('#adpcon').sortable({placeholder:'highlight'});
        jQuery('#apv').uploadify({
    
          'uploader'  : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/uploadify.swf',   
          /*'script'    : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/uploadify.php',  */
          'script'    : '<?php echo home_url('/wp-admin/');?>admin.php?task=wpmp_upload_previews',                      
          'cancelImg' : '<?php echo plugins_url(); ?>/wp-marketplace/uploadify/cancel.png',  
          'folder'    : '<?php echo str_replace($_SERVER['DOCUMENT_ROOT'],'',UPLOAD_DIR); ?>',
          'multi'  : true,
          'wmode': 'transparent',
          'hideButton': true,
          'auto'      : true,
          'width': 150,
          'height': 30,
          'onComplete': function(event, ID, fileObj, response, data) {                            
                            if(fileObj.name.length>20) nm = fileObj.name.substring(0,7)+'...'+fileObj.name.substring(fileObj.name.length-10);
                            jQuery('#adpcon').append("<div id='"+ID+"' style='display:none;float:left;padding:5px;' class='adp'><input type='hidden' id='in_"+ID+"' name='wpmp_list[images][]' value='"+response+"' /><nobr><b><img style='position:absolute;z-index:9999;cursor:pointer;' id='del_"+ID+"' src='<?php echo plugins_url(); ?>/wp-marketplace/images/remove.png' rel='del' align=left /><img src='<?php echo plugins_url(); ?>/wp-marketplace/libs/timthumb.php?w=50&h=50&zc=1&src=previews/"+response+"'/></b></nobr><div style='clear:both'></div></div>");
                            jQuery('#'+ID).fadeIn();
                            jQuery('#del_'+ID).click(function(){
                                if(confirm('Are you sure?')){
                                    jQuery.post(ajaxurl,{action:'wpmp_delete_preview',file:jQuery('#in_'+ID).val()})
                                    jQuery('#'+ID).fadeOut().remove();
                                }
                                
                            });
                            
                         }   
        });
                        
        jQuery('.del_adp').click(function(){
                                if(confirm('Are you sure?')){
                                    jQuery.post(ajaxurl,{action:'wpmp_delete_preview',file:jQuery('#in_'+jQuery(this).attr('rel')).val()})
                                    jQuery('#'+jQuery(this).attr('rel')).fadeOut().remove();
                                }
                                
                            });
   
      });
  
      </script>
<?php    
} 

function wpmp_format_name($text){
            $allowed = "/[^a-z0-9\\.\\-\\_]/i";      
            $text = preg_replace($allowed,"-",$text);
            $text = preg_replace("/([\\-]+)/i","-",$text);
            return $text;
}   

function wpmp_upload_previews(){
     $adpdir = WP_PLUGIN_DIR.'/wp-marketplace/previews/';     
     if(is_uploaded_file($_FILES['Filedata']['tmp_name'])&&$_GET['task']=='wpmp_upload_previews'){
        $tempFile = $_FILES['Filedata']['tmp_name'];    
        $targetFile =  $adpdir ."wpdm-adp-". time().'-'.wpmp_format_name($_FILES['Filedata']['name']);
        move_uploaded_file($tempFile, $targetFile);
        echo basename($targetFile);        
        die();
     }
     
}

function wpmp_delete_preview(){
    @unlink(WP_PLUGIN_DIR.'/wp-marketplace/previews/'.$_POST['file']);
    die();
}

 
 
function wpmp_get_thumbs($id){
    global $post;
    @extract(get_post_meta($id,"wpmp_list_opts",true));      
    $img = '';     
     
    if($images){
    $t = count($images);
    foreach($images as $p){
        ++$k;
        echo "<a class='colorbox' rel='colorbox' title='{$post->post_title} &#187; Image {$k} of $t' href='".plugins_url()."/wp-marketplace/previews/{$p}' id='more_previews_a_{$k}' class='more_previews_a' ><img id='more_previews_{$k}' class='more_previews' src='".plugins_url().'/wp-marketplace/libs/timthumb.php?w='.get_option('_wpmp_athumb_w',80).'&h='.get_option('_wpmp_athumb_h',60).'&zc=1&src=wp-content/plugins/wp-marketplace/previews/'.$p."'/></a>";
    }}
    
   
}

function wpmp_meta_box_images($meta_boxes){
    $meta_boxes['wpmp-images'] = array('title'=>'Images','callback'=>'wppre_additional_preview_images','position'=>'side','priority'=>'low');
    return $meta_boxes;
}

if(is_admin())  {
    wp_enqueue_script('swfobject',plugins_url().'/wp-marketplace/uploadify/swfobject.js');
    wp_enqueue_script('uploadify',plugins_url().'/wp-marketplace/uploadify/jquery.uploadify.v2.1.4.min.js');
    wp_enqueue_style('uploadify',plugins_url().'/wp-marketplace/uploadify/uploadify.css');
    
    add_action("init","wpmp_upload_previews");
    add_action("wp_ajax_wpmp_delete_preview","wpmp_delete_preview");
    add_filter("wpmp_meta_box","wpmp_meta_box_images");
}


 
