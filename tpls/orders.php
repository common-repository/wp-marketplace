<?php
    global $wpdb;
?>
<div class="wrap">
    <div class="icon32" id="icon-file-manager"><br></div>
<h2>Orders   </h2>

<div style="float: right;">
<h2 style="font-size: 12pt">Total Sales: <?php $total = $wpdb->get_var("select sum(total) as tamount from {$wpdb->prefix}mp_orders where payment_status='Completed'"); echo '$'.number_format($total,2); ?></h2><br />
</div>


           
<form method="get" action="" id="posts-filter">
 <input type="hidden" name="page" value="file-manager/pp-orders">
<div class="tablenav">

<div class="alignleft actions">
   

 <select class="select-action" name="ost">
<option value="">Order status:</option>
<option value="Pending" <?php echo $_REQUEST['ost']=='Pending'?'selected=selected':''; ?>>Pending</option>
<option value="Processing" <?php echo $_REQUEST['ost']=='Processing'?'selected=selected':''; ?>>Processing</option>
<option value="Completed" <?php echo $_REQUEST['ost']=='Completed'?'selected=selected':''; ?>>Completed</option>
<option value="Canceled" <?php echo $_REQUEST['ost']=='Canceled'?'selected=selected':''; ?>>Canceled</option>
</select>
      
<select class="select-action" name="pst">
<option value="">Payment status:</option>
<option value="Pending" <?php echo $_REQUEST['pst']=='Pending'?'selected=selected':''; ?>>Pending</option>
<option value="Processing" <?php echo $_REQUEST['pst']=='Processing'?'selected=selected':''; ?>>Processing</option>
<option value="Completed" <?php echo $_REQUEST['pst']=='Completed'?'selected=selected':''; ?>>Completed</option>
<option value="Canceled" <?php echo $_REQUEST['pst']=='Canceled'?'selected=selected':''; ?>>Canceled</option>
</select>
Date<span class="info infoicon" title="(yyyy-mm-dd)">(?)</span> : 
from <input type="text" name="sdate" value="<?php echo $_REQUEST[sdate]; ?>"> 
to <input type="text" name="edate" value="<?php echo $_REQUEST[edate]; ?>">

Order ID: <input type="text" name="oid" value="<?php echo $_REQUEST[oid]; ?>">

<input type="submit" class="button-secondary action" id="doaction" name="doaction" value="Apply">

| <b><?php echo $t; ?> order(s) found</b>
</div>

<br class="clear">
</div>

<div class="clear"></div>

<table cellspacing="0" class="widefat fixed">
    <thead>
    <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
 
    <th style="" class="manage-column column-media" id="media" scope="col">Order ID</th>
    <th style="" class="manage-column column-author" id="author" scope="col">Total Amount</th>
    <th style="" class="manage-column column-author" id="author" scope="col">Customer Name</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Order Status</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Payment Status</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Order Date</th>
    </tr>
    </thead>

    <tfoot>
    <tr>
    <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
 
    <th style="" class="manage-column column-media" id="media" scope="col">Order ID</th>
    <th style="" class="manage-column column-author" id="author" scope="col">Total Amount</th>
    <th style="" class="manage-column column-author" id="author" scope="col">Customer Name</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Order Status</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Payment Status</th>
    <th style="" class="manage-column column-parent" id="parent" scope="col">Order Date</th>
    </tr>
    </tfoot>

    <tbody class="list:post" id="the-list">
    <?php     
    foreach($orders as $order) { 
          $user_info = get_userdata($order->uid);         
        ?>
    <tr valign="top" class="alternate author-self status-inherit" id="post-8">

                <th class="check-column" scope="row"><input type="checkbox" value="8" name="id[]"></th>
                
                <td class="media column-media">
                    <strong><a title="Edit" href="edit.php?post_type=marketplace&page=orders&task=vieworder&id=<?php echo $order->order_id; ?>"><?php echo $order->order_id; ?></a></strong>
                </td>
                <td class="author column-author"><?php echo $order->total; ?> USD</td>
                <td class="author column-author"><?php echo $user_info->user_login; ?></td>
                <td class="parent column-parent"><?php echo $order->order_status; ?></td>
                <td class="parent column-parent"><?php echo $order->payment_status; ?></td>
                <td class="parent column-parent"><?php echo date("D, F d, Y",$order->date); ?></td>
     
     </tr>
     <?php } ?>
    </tbody>
</table>
                    
<?php
 

$page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($t/$l),
    'current' => $p
));


?>

<div id="ajax-response"></div>

<div class="tablenav">

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
    number_format_i18n( ( $_GET['paged'] - 1 ) * $l + 1 ),
    number_format_i18n( min( $_GET['paged'] * $l, $t ) ),
    number_format_i18n( $t ),
    $page_links
); echo $page_links_text; ?></div>
<?php } ?>

<div class="alignleft actions">
  
<input type="submit" class="button-secondary action" id="doaction2" name="doaction2" value="Apply">

</div>

<br class="clear">
</div>
    <div style="display: none;" class="find-box" id="find-posts">
        <div class="find-box-head" id="find-posts-head">Find Posts or Pages</div>
        <div class="find-box-inside">
            <div class="find-box-search">
                
                <input type="hidden" value="" id="affected" name="affected">
                <input type="hidden" value="3a4edcbda3" name="_ajax_nonce" id="_ajax_nonce">                <label for="find-posts-input" class="screen-reader-text">Search</label>
                <input type="text" value="" name="ps" id="find-posts-input">
                <input type="button" class="button" value="Search" onclick="findPosts.send();"><br>

                <input type="radio" value="posts" checked="checked" id="find-posts-posts" name="find-posts-what">
                <label for="find-posts-posts">Posts</label>
                <input type="radio" value="pages" id="find-posts-pages" name="find-posts-what">
                <label for="find-posts-pages">Pages</label>
            </div>
            <div id="find-posts-response"></div>
        </div>
        <div class="find-box-buttons">
            <input type="button" value="Close" onclick="findPosts.close();" class="button alignleft">
            <input type="submit" value="Select" class="button-primary alignright" id="find-posts-submit">
        </div>
    </div>
</form>
<br class="clear">

</div>