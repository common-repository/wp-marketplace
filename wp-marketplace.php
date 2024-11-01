<?php 
/**
 * @package Marketplace Plugin for Wordpress
 * @author Shaon
 * @version 1.1.1
 */
/*
Plugin Name:  WP Marketplace
Plugin URI: http://wpmarketplaceplugin.com/
Description: Marketplace Plugin for Wordpress
Author: Shaon
Version: 1.1.1
Author URI: http://wpmarketplaceplugin.com/
*/

 
include("libs/functions.php");
include("libs/class.plugin.php");
include("libs/class.order.php");
include("libs/class.payment.php");
include("libs/cart.php");

$wpmp_plugin = new ahm_plugin('wp-marketplace');

function wpmp_post_types(){   
    register_post_type("wp-marketplace",array(
            
            'labels' => array(
                'name' => __('Marketplace'),
                'singular_name' => __('Product'),
                'add_new' => __('Add Product'),
                'add_new_item' => __('Add New Product'),
                'edit_item' => __('Edit Product'), 
                'new_item' => __('New Product'),
                'view_item' => __('View Product'),
                'search_items' => __('Search Product'),
                'not_found' =>  __('No product found'),
                'not_found_in_trash' => __('No product found in Trash'), 
                'parent_item_colon' => ''
            ),
            'public' => true,
            'publicly_queryable' => true,
            'has_archive' => true,
            'show_ui' => true, 
            'query_var' => true,
            'rewrite' => array('slug'=>'product','with_front'=>true),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_icon' =>plugins_url().'/wp-marketplace/images/wpmp.png',            
            'supports' => array('title','editor','author','excerpt','thumbnail','type') ,
            'taxonomies' => array('type')
             
        )
    );     
    
    
    
}

function register_marketplace_product_taxonomies() 
{
  // Add new taxonomy, make it hierarchical (like categories)
  $labels = array(
    'name' => _x( 'Product Types', 'taxonomy general name' ),
    'singular_name' => _x( 'Type', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Types' ),
    'all_items' => __( 'All Types' ),
    'parent_item' => __( 'Parent Type' ),
    'parent_item_colon' => __( 'Parent Type:' ),
    'edit_item' => __( 'Edit Type' ), 
    'update_item' => __( 'Update Type' ),
    'add_new_item' => __( 'Add New Type' ),
    'new_item_name' => __( 'New Type Name' ),
    'menu_name' => __( 'Product Types' ),
  );     

  register_taxonomy('type',array('wp-marketplace'), array(
    'hierarchical' => true,
    'labels' => $labels,
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array( 'slug' => 'product-category' ),
  ));

   
}

function wpmp_install(){
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');    
    $sql[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_mp_orders` (
              `order_id` varchar(100) NOT NULL,
              `title` varchar(255) NOT NULL,
              `date` int(11) NOT NULL,
              `items` text NOT NULL,
              `cart_data` text NOT NULL,
              `total` double NOT NULL,
              `order_status` enum('Pending','Processing','Completed','Canceled') NOT NULL,
              `payment_status` enum('Pending','Processing','Completed','Canceled') NOT NULL,
              `uid` int(11) NOT NULL,
              PRIMARY KEY (`order_id`)
            ) ENGINE=MyISAM";
    
    $sql[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_mp_order_items` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `oid` varchar(255) NOT NULL,
          `pid` int(11) NOT NULL,
          `quantity` int(11) NOT NULL,
          `license` varchar(255) NOT NULL,
          `price` double NOT NULL,
          `status` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM";
    
    $sql[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}_mp_payment_methods` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(100) NOT NULL,
          `description` text NOT NULL,
          `class_name` varchar(80) NOT NULL,
          `enabled` int(11) NOT NULL,
          `default` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM";    
    $sql[] = "INSERT INTO `{$wpdb->prefix}_mp_payment_methods` (`id`, `title`, `description`, `class_name`, `enabled`, `default`) VALUES(1, 'PayPal', 'PayPal', 'paypal', 1, 1)";    
    foreach($sql as $qry){
        $wpdb->query($qry);
    }
    update_option('wpmp_access_level','level_10');      
    wpmp_post_types();
    flush_rewrite_rules();
}

function wpmp_the_content($content){
    global $post;            
    //if(!is_single()) return $content;
    if($post->post_type!='wp-marketplace') return $content;     
    @extract(get_post_meta($post->ID,"wpmp_list_opts",true)); 
    include("tpls/product/default.php");
    return $content;
}



function wpmp_meta_box_pricing($post){     
    @extract(get_post_meta($post->ID,"wpmp_list_opts",true)); 
    ?>
    <div class="postbox" style="width: 48%;float: right;">
    <h3>Role Based Discount</h3>
     <table width="100%" style="margin: 10px;">
     <tr><th align="left">Role</th><th align="left">Discount (%)</th></tr>
     <tr><td width="250px">Guest (guest) </td><td><input type="text" size="8" name="wpmp_list[discount][guest]" value="<?php echo $discount['guest']; ?>"></td></tr>     
         <?php
    global $wp_roles;
    $roles = array_reverse($wp_roles->role_names);
    foreach( $roles as $role => $name ) { 
    
    
    
    if(  $currentAccess ) $sel = (in_array($role,$currentAccess))?'checked':'';
    
    
    
    ?>
    <tr><td><?php echo $name; ?> (<?php echo $role; ?>) </td><td><input type="text" size="8" name="wpmp_list[discount][<?php echo $role; ?>]" value="<?php echo $discount[$role]; ?>"></td></tr>     
    
    <?php } ?>     
    </table>
    </div>
    <div class="postbox" style="width: 48%;float: left;">
    <h3>License Based Pricing</h3>
     <table width="100%" style="margin: 10px;">
     <tr><th align="left">License Type</th><th align="left">Price($)</th></tr>
     <tr><td width="250px">Single User/Domain </td><td><input type="text" size="8" name="wpmp_list[price][single_user]" value="<?php echo $price['single_user']; ?>"></td></tr>     
     <tr><td width="250px">Multi User/Domain </td><td><input type="text" size="8" name="wpmp_list[price][multi_user]" value="<?php echo $price['multi_user']; ?>"></td></tr>     
     <tr><td width="250px">Unlimited User/Domain </td><td><input type="text" size="8" name="wpmp_list[price][unlimited_user]" value="<?php echo $price['unlimited_user']; ?>"></td></tr>     
     <tr><td width="250px">Developer </td><td><input type="text" size="8" name="wpmp_list[price][dev_user]" value="<?php echo $price['dev_user']; ?>"></td></tr>     
    </table>
    </div>
     <div style="clear: both;"></div>
     <?php
}

function wpmp_meta_box_icon(){
    global $post;
     @extract(get_post_meta($post->ID,"wpmp_list_opts",true)); 
    ?>
     
     <?php    
        $path = "wp-content/plugins/wp-marketplace/images/icons/";
        $scan = scandir( '../'.$path );
        $k = 0;
        foreach( $scan as $v )
        {
        if( $v=='.' or $v=='..' or is_dir('../'.$path.$v) ) continue;

        $fileinfo[$k]['file'] = 'wp-marketplace/images/icons/'.$v;
        $fileinfo[$k]['name'] = $v;
        $k++;
        }


        if( !empty($fileinfo) )
        {
                  
         include dirname(__FILE__).'/libs/icon.php';

        } else {

        ?>
        <div class="updated" style="padding: 5px;">
            upload your icons on '/wp-content/plugins/wp-marketplace/images/icons/' using ftp</div>

        <?php } ?>
     
    <?php
}

function wpmp_meta_box_demo($post){     
    @extract(get_post_meta($post->ID,"wpmp_list_opts",true));     
    ?>
     <table width="95%">
     <tr><td width="200px">Demo Site URL: </td><td><input type="text" style="width: 100%" name="wpmp_list[demo_site]" value="<?php echo $demo_site; ?>"></td></tr>
     <tr><td>Demo Admin URL: </td><td><input type="text" style="width: 100%" name="wpmp_list[demo_admin]" value="<?php echo $demo_admin; ?>"></td></tr>
     <tr><td>Username: </td><td><input type="text" style="width: 100%" name="wpmp_list[demo_username]" value="<?php echo $demo_username; ?>"></td></tr>
     <tr><td>Password: </td><td><input type="text" style="width: 100%" name="wpmp_list[demo_password]" value="<?php echo $demo_password; ?>"></td></tr>     
     </table>
 
     <?php
}
function wpmp_meta_box_video($post){     
    @extract(get_post_meta($post->ID,"wpmp_list_opts",true)); 
    ?>
     <table width="100%">
     <tr><td valign="top">YouTube Video ID:&nbsp;<input type="text" size="15" name="wpmp_list[video_id]" id="vid" value="<?php echo $video_id; ?>"  onchange="jQuery('#video-preview').html('<iframe width=250 height=180 src=\'http://www.youtube.com/embed/'+jQuery('#vid').val()+'\' frameborder=0 allowfullscreen></iframe>');">
     <br/>
     <div id="video-preview" align="center">
     </div>
     </td>
     </tr>     
     </table>
     
     
     <?php
}

 

register_taxonomy_for_object_type('category', 'wp-marketplace');

/*function wpmp_menu(){
    add_menu_page("WP Real Estate","WP Real Estate",get_option('wpmp_access_level'),'wp-marketplace','wpmp_manage_listing');
    add_submenu_page( 'wp-marketplace', 'WP Real Estate', 'WP Real Estate', get_option('wpmp_access_level'), 'wp-marketplace', 'wpmp_manage_listing');        
    
}
 */
function wpmp_meta_boxes(){                                
    $meta_boxes = array(
                            'wpmp-info'=>array('title'=>'Pricing & Discounts','callback'=>'wpmp_meta_box_pricing','position'=>'normal','pririty'=>'low'),
                            'wpmp-demo'=>array('title'=>'Demo Info','callback'=>'wpmp_meta_box_demo','position'=>'normal','pririty'=>'low'),
                            //'wpmp-video'=>array('title'=>'Video','callback'=>'wpmp_meta_box_video','position'=>'side','pririty'=>'low'),
                            'wpmp-icons'=>array('title'=>'Icon','callback'=>'wpmp_meta_box_icon','position'=>'side','pririty'=>'core')                            
                       );
    $meta_boxes = apply_filters("wpmp_meta_box", $meta_boxes);
    foreach($meta_boxes as $id=>$meta_box){
        extract($meta_box);
        add_meta_box($id, $title, $callback,'wp-marketplace', $position, $priority);
    }    
}

function wpmp_save_meta_data($postid, $post){               
         if($_POST['wpmp_list']){
         update_post_meta($postid,"wpmp_list_opts",$_POST['wpmp_list']);  
         foreach($_POST['wpmp_list'] as $k=>$v){
                update_post_meta($postid,$k,$v);
             }
         }
}

function wpmp_settings(){
       include("settings/settings.php");
    
}

function wpmp_orders(){       
    
    $order = new Order();    
    $l = 15;
    
    $p = $_GET['paged']?$_GET['paged']:1;
    $s = ($p-1)*$l;
    if($_GET['task']=='vieworder'){
    $order = $order->getOrder($_GET['id']);
    include('tpls/view-order.php');        
    }
    else {                   
    if($_REQUEST['oid'])    
    $qry[] = "order_id='$_REQUEST[oid]'" ;   
    if($_REQUEST['ost'])    
    $qry[] = "order_status='$_REQUEST[ost]'" ;   
    if($_REQUEST['pst'])
    $qry[] = "payment_status='$_REQUEST[pst]'";    
    if($_REQUEST['sdate']!=''||$_REQUEST['edate']!=''){
        $_REQUEST['edate'] = $_REQUEST['edate']?$_REQUEST['edate']:$_REQUEST['sdate'];
        $_REQUEST['sdate'] = $_REQUEST['sdate']?$_REQUEST['sdate']:$_REQUEST['edate'];
        $sdate = strtotime("$_REQUEST[sdate] 00:00:00");
        $edate = strtotime("$_REQUEST[edate] 23:59:59");
        $qry[] = "(`date` >=$sdate and `date` <=$edate)";
    }
    
    if($qry)
    $qry = "where ".implode(" and ", $qry);
   
    $t = $order->totalOrders($qry); 
    $orders = $order->GetAllOrders($qry,$s, $l);
    include('tpls/orders.php');    
    }
}

function wpmp_myorders($content){
    global $current_user, $_ohtml;
    get_currentuserinfo();
    $order = new Order();         
    $myorders = $order->GetOrders($current_user->ID);
    $_ohtml = '';        
    include('tpls/my-orders.php');
    $content = str_replace('[my-orders]',$_ohtml, $content);
    return $content;
    
}


function wpmp_set_post_type( $query ) {
 
         /*$cat = $query->query_vars['category_name'];
         $set = get_option('_wpmp_settings');
         if ( @in_array($cat, $set['cats']))  */     
        
         if(!is_admin()){
         if(!is_page())
         $query->set( 'post_type', array('post','wp-marketplace'));         
         else
         $query->set( 'post_type', array('post','wp-marketplace','page'));         
         }
         return $query;
         
     } 
     
function wpmp_tabs($attrs,$content){
    
        $tabs = explode("|",$attrs['tabs']);
        $html = "<div class='wpmp-tab-container'><ul class='tabs'>";
        foreach($tabs as $tab){
            ++$tn;
            $html .= "<li><a href='#tab{$tn}'>{$tab}</a></li>\n";
        }
        $html .= "</ul>";
        $html .= '<div class="tab_container">';
        $tab_cons = explode("######",$content);
        foreach($tab_cons as $con){
         ++$tc ;  
        $html .= '<div id="tab'.$tc.'" class="tab_content">'.$con.'</div>';
        }
        $html .= '</div></div>';
        return $html;
    
}
     

function wpmp_menu(){     
    add_submenu_page( 'edit.php?post_type=wp-marketplace', 'Orders &lsaquo; Marketplace', 'Orders', 'administrator', 'orders', 'wpmp_orders');    
    add_submenu_page( 'edit.php?post_type=wp-marketplace', 'Settings &lsaquo; Marketplace', 'Settings', 'administrator', 'settings', 'wpmp_settings');    
}

function wpmp_save_settings(){
    update_option('_wpmp_settings',$_POST['_wpmp_settings']);    
    die('Settings Saved Successfully');
}

function wpmp_download(){    
    if(!$_GET['wpmpfile']||!$_GET['oid']) return;
    global $wpdb, $current_user;
    get_currentuserinfo();
    $order = new Order();
    $odata = $order->GetOrder($_GET['oid']);
    $items = unserialize($odata->items);    
    if(@in_array($_GET['wpmpfile'],$items)&&$_GET['oid']!=''&&is_user_logged_in()&&$current_user->ID==$odata->uid){        
        @extract(get_post_meta($_GET['wpmpfile'],"wpmp_list_opts",true));   
        $fname = dirname(__FILE__).'/product-files/'.$file;
      
        include("libs/process.php");
    }
}



add_action("admin_menu","wpmp_menu");
add_action( 'init', 'wpmp_post_types', 0 );
add_action( 'init', 'register_marketplace_product_taxonomies', 0 );
add_action( 'init', 'wpmp_download', 0 );
add_action( 'the_content', 'wpmp_buynow',999999);
add_filter('the_content','wpmp_myorders'); 
//add_filter( 'pre_get_posts', 'wpmp_set_post_type' );
add_shortcode("wpmp-tabs","wpmp_tabs");

wp_enqueue_script('jquery');


if(is_admin()){    
    add_action("admin_menu","wpmp_menu");            
    add_action( 'admin_init', 'wpmp_meta_boxes', 0 );
    add_action( 'save_post', 'wpmp_save_meta_data',10,2);
    add_action( 'wp_ajax_wpmp_save_settings', 'wpmp_save_settings');    
 
}
else{
    add_filter("the_content","wpmp_the_content");
}

$wpmp_plugin->load_styles();
$wpmp_plugin->load_scripts();

 
register_activation_hook(__FILE__,'wpmp_install');

$wpmp_plugin->load_modules();  
