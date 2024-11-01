 
<style>
.box{
    -webkit-border-radius: 7px;
-moz-border-radius: 7px;
border-radius: 7px;
border:1px solid #008000;
background: #F0FFEA url("<?php echo plugins_url();?>/wpdm-premium-packages/images/processing.gif") center 10px no-repeat;
padding:10px;
padding-top: 35px;
text-align: center;
font-family: tahoma;
font-size:9pt;
letter-spacing: 1px;
}
</style>
 
<table width="100%" height="100%"><tr><td width="100%" height="100%" align="center" valign="middle">
<table align="center"><tr><td class="box" align="center">Please wait while Proceeding to payment<br/>
<?php echo $payment->Processor->ShowPaymentForm(1);          ?>
<br/>

</td></tr></table>
</td></tr></table>
 