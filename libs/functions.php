<?php

function  agent_contact_info(){
     global $post;
     @extract(get_post_meta($post->ID,'wpmp_list_opts',true));
     @extract($agent);
      
     ?>
     <img src="<?php echo $picture?$picture:plugins_url().'/wp-marketplace/images/agent-image.png'; ?>" style="float: left;padding-right:15px;" /><br/>
     <b><?php echo $name; ?></b><br/>
     <?php echo $website?$website.'</br>':''; ?>     
     <?php echo $email?$email.'</br>':''; ?>     
     <?php echo $phone?$phone.'</br>':''; ?>     
     <?php
}

function wpmp_video(){
    global $post;
    $meta = get_post_meta($post->ID,'wpmp_list_opts',true);  
    $settings = maybe_unserialize(get_option("_wpmp_settings"));
    if($meta['video_id']){ ?>
    <h3>Video Tour</h3>
    <hr noshade="noshade" size="1" />
    <iframe width="<?php echo $settings['vw']?$settings['vw']:320; ?>" height="<?php echo $settings['vh']?$settings['vh']:240; ?>" src="http://www.youtube.com/embed/<?php echo $meta['video_id']; ?>" frameborder="0" allowfullscreen></iframe>
    <?php }
}    
function wpmp_listing_details(){
    global $post;
    $meta = get_post_meta($post->ID,'wpmp_list_opts',true);  
    if($meta['property_for']=='rent'){ ?>
    <h3>Rental Details</h3>
    <hr noshade="noshade" size="1" />
    <div class="meta meta_b" style="padding: 0px;margin: 0px;width: 100%;">
    <ul style="margin-left: 0px;width: 100%;">
    <?php if($meta['rent']!='') echo "<li><span>Rent:</span>\$".number_format($meta[rent],2,'.',',')."/{$meta[rent_period]}</li>\n"; ?>    
    <?php if($meta['damage_deposit']!='') echo "<li><span>Damage Deposit:</span>{$meta[damage_deposit]}</li>\n"; ?>    
    <?php if($meta['minimal_lease_term']!='') echo "<li><span>Minimum lease term:</span>{$meta[minimal_lease_term]} {$meta[rent_period]}(s)</li>\n"; ?>    
    <?php if($meta['smoking']!='') echo "<li><span>Smoking allowed:</span>".($meta[smoking]?'Yes':'No')."</li>\n"; ?>    
    <?php if($meta['pets']!='') echo "<li><span>Pets allowed:</span>".($meta[pets]?'Yes':'No')."</li>\n"; ?>    
    </ul>
    </div>    
    <div style="clear: both;"><br/></div>                    
    <b>Other Rental Details</b><br/>
    <p><?php echo  $meta['other_rental_details']; ?></p>
    <?php }
    
    if($meta['property_for']=='sale'){ ?>
    <h3>Sales Details</h3>
    <hr noshade="noshade" size="1" />
    <div class="meta meta_b" style="padding: 0px;margin: 0px;width: 100%;">
    <ul style="margin-left: 0px;width: 100%;">
    <?php if($meta['price']!='') echo "<li><span>Price:</span>\$".number_format($meta[price],2,'.',',')."</li>\n"; ?>    
    <?php if($meta['sale_terms']!='') echo "<li><span>Sale Terms:</span>{$meta[sale_terms]}</li>\n"; ?>    
    </ul>
    </div>    
    <div style="clear: both;"><br/></div>                    
    <b>Other Sales Details</b><br/>
    <p><?php echo  $meta['other_sales_details']; ?></p>
    <?php }
    
    
    if($meta['property_for']=='rent2own'){ ?>
    <h3>Rent to Own Details</h3>
    <hr noshade="noshade" size="1" />
    <div class="meta meta_b" style="padding: 0px;margin: 0px;width: 100%;">
    <ul style="margin-left: 0px;width: 100%;">
    <?php if($meta['rent2own_price']!='') echo "<li><span>Sale Price Today:</span>\$".number_format($meta['rent2own_price'],2,'.',',')."</li>\n"; ?>    
    <?php if($meta['minimum_initial_deposit']!='') echo "<li><span>Initial Deposit:</span>$".number_format($meta['minimum_initial_deposit'],2,'.',',')."</li>\n"; ?>    
    <?php if($meta['rent2own_terms']!='') echo "<li><span>Terms:</span>".$meta['rent2own_terms']."</li>\n"; ?>        
    </ul>
    </div>    
    <div style="clear: both;"><br/></div>                    
    <b>Other Rent to Own Details</b><br/>
    <p><?php echo  $meta['other_rent2own_details']; ?></p>
    <?php }
    
    
    
}

?>