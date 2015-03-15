<?php
$ci = &get_instance();
?>
<style type="text/css">
    .row{
	padding-bottom:16px;
	margin-bottom:16px;
    }
    
    .row:first-of-type{
	border-top:1px dotted #000;
	margin-top:16px;
	padding-top:16px;
    }
    
    [class*="col-"]{
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
    }
    
    @media (max-width: 400px){
	.row.table-row > [class*="col-"]{
	    width:100%;
	}
	
	.row.table-row > [class*="col-"]:not(:first-of-type){
	    display:none;
	}
    }
</style>

<script type="text/javascript">
    jQuery(document).ready(function(){
	jQuery("div.table-row[data-href], div.table-row[data-href] :not(a)").click(function(e){
	    //e.preventDefault();
	    //e.stopPropagation();
	    window.location = jQuery("div.table-row[data-href]:hover").attr('data-href');
	});
    });
</script>

<?php
foreach($results as $result){
    $view_link = "{$ci->controller_path}/view/{$result[$primary_key]}";
    $edit_link = "{$ci->controller_path}/edit/{$result[$primary_key]}";
    ?>
    <div class="row table-row" data-href="<?=$view_link?>">
	<div class="col-xs-8">
	    <h4><?=$result[$display_key]?></h4>
	</div>
	<div class="col-xs-4 text-right">
	    <a class="btn btn-default glyphicon glyphicon-edit" href="<?=$edit_link?>" alt="Edit"></a>
	    <a class="btn btn-default glyphicon glyphicon-eye-open" href="<?=$view_link?>" alt="View"></a>
	</div>
    </div>
    <?php
}
?>