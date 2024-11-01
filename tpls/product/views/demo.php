<?php
 

$demo = <<<DEMO
<div class='prices' style='margin-top:15px'>
<b>Demo Info:</b> <br/>
<ul>
DEMO;

if($demo_site!='')
$demo .="<li>Front-end <a href='{$demo_site}'>Enter</a></li>";

if($demo_admin!=''){
$demo .= "<li>Admin <a href='{$demo_admin}'>Enter</a></li>";
$demo .= "<li><span>Username:</span>{$demo_username}</li>";
$demo .= "<li><span>Password:</span>{$demo_password}</li>";
}


$demo .= <<<PRICE
</ul>
</div>
PRICE;

