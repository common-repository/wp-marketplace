<?php

$price_title['single_user'] = 'Single User';
$price_title['multi_user'] = 'Multi User';
$price_title['unlimited_user'] = 'Unlimited User';
$price_title['dev_user'] = 'Developement';

$prices = <<<PRICE
 
<div class='prices'>
<b>Prices:</b> <br/>
<ul>
PRICE;

foreach($price as $k=>$p){
    if($p){
    $p = number_format($p,2);
    $link = home_url("/?wp-marketplace={$post->post_name}&buy=$k");
    $prices .= "<li><span>{$price_title[$k]}</span>\$$p <a href='{$link}'>Buy</a></li>";
    }
}


$prices .= <<<PRICE
</ul>
<div class="clear"></div>
</div>
PRICE;

