<?php
$ci = &get_instance();
?>
<style type="text/css">
    .row{
	border-bottom:2px solid #999;
	padding-bottom:16px;
	margin-bottom:16px;
    }
    
    .row:first-of-type{
	margin-top:16px;
	padding-top:16px;
    }
    
    [class*="col-"]{
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
    }
    
    .row.attributes{
	margin:0px; 
	padding:0px; 
	border:0px none !important;
    }
</style>

<?php
foreach($parsed_fields as $label => $value){
    ?>
    <div class="row table-row">
	<div class="col-xs-2">
	    <b><?=$label?></b>
	</div>
	<div class="col-xs-10"><?=$value?></div>
    </div>
    <?php
}
?>