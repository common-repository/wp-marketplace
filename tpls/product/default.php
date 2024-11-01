<?php
    //Show Images
    include("views/images.php");
    include("views/prices.php");
    include("views/content.php");
    include("views/demo.php");


$content = <<<CONT
<div>
<div class='clear'></div>
{$previews}{$prices}{$demo} 
{$content}
</div>
CONT;
?>