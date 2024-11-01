<?php 
$pluginurl = plugins_url();
$previews = <<<IMGS
 
<ul class="wpmp-gallery clearfix">
<li class='wpmp-main-image'><a href='{$pluginurl}/wp-marketplace/previews/{$images[0]}' rel="product-images" title="<b>{$post->post_title} Image {$c} of {$t}</b>" ><img src='{$pluginurl}/wp-marketplace/libs/timthumb.php?w=340&h=200&zc=1&src=previews/{$images[0]}'/></a></li>
IMGS;
 $t = count($images);
foreach($images as $image){
    ++$c;
$previews .= <<<IMGS
<li><a href='{$pluginurl}/wp-marketplace/previews/{$image}' rel="product-images" title="<b>{$post->post_title} Image {$c} of {$t}</b>" ><img src='{$pluginurl}/wp-marketplace/libs/timthumb.php?w=77&h=50&zc=1&src=previews/{$image}'/></a></li>\n
IMGS;
}
$previews .= <<<IMGS
</ul>
 
<script language="JavaScript">
<!--
  jQuery(function(){
      
      jQuery("a[rel='product-images']").colorbox();       
      
  });
//-->
</script>
IMGS;
 