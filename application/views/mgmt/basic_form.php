<?php
$ci = &get_instance();
$ci->load->helper('form');

$create_or_edit = isset($entity) ? 'Edit' : 'Create';
?>

<style type="text/css">
    form *{
	-webkit-box-sizing:content-box;
	-moz-box-sizing:content-box;
	box-sizing:content-box;
    }
    
    form{
	padding:6px;
	border:1px solid #000;
    }
    
    [class*="col-"] > label{
	background-color:#e0e0e0;
	width:100%;
	height:100%;
	padding:6px;
	margin:0px;
	border:1px solid #000;
	border-bottom-width:0px;
    }
    
    form .row:last-of-type [class*="col-"] > label{
	border-bottom-width:1px;
    }
    
    [class*="col-"] input, [class*="col-"] textarea{
	width:100%;
    }
    
    form#form-main label{
	height:100%;
    }
    
    .row.table-row{
	width:100%;
	margin:0 auto;
    }
    
    [class*="col-"]{
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
    }
    
    .row.table-row > [class*="col-"]:last-of-type > *{
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
    }
    
    .row.table-row > [class*="col-"]{
	float:none;
	display:table-cell;
	vertical-align:top;
	padding-left:0px;
    }

    @media (min-width: 400px){
	.row.table-row > [class*="col-"]:not(:first-of-type){
	    padding-left:10px;
	}

	.row.table-row > [class*="col-"]:last-of-type{
	    padding-right:0px;
	}
    }
    
    @media (max-width: 400px){
    
	[class*="col-"] *{
	    -webkit-box-sizing:border-box;
	    -moz-box-sizing:border-box;
	    box-sizing:border-box;
	}
	
	.row.table-row > [class*="col-"]{
	    float:left;
	    padding-left:0px;
	}
    }
    
    @media (min-width: 561px){
	.row.table-row{
	    display:table;
	}
    }
</style>

<form name="form-main" id="form-main" method="post" action="">
    <?php
    $skip_fields = array('id','created','updated');

    foreach($ci->schema as $field){
	if(in_array($field->name, $skip_fields)) continue;
	if(isset($entity) && !$field->editable) continue;

	$value = isset($entity) ? $entity[$field->name] : '';
	$value = isset($_POST[$field->name]) ? $_POST[$field->name] : $value;

	if($field->field_type != 'hidden'){
	    $field_id = "form-main_{$field->name}";
	    ?>
	    <div class="row table-row">
	    <div class="col-xs-3">
		<label for="<?=$field_id?>">
		    <?=$field->label?>
		</label>
	    </div>
	    <div class="col-xs-9">
	    <?php
	}
	
	switch($field->field_type){
	    case 'text':
		?>
		<input name="<?=$field->name?>" id="<?=$field_id?>" type="text" value="<?=$value?>" />
		<?php
		break;
	    case 'password':
		?>
		<input name="<?=$field->name?>" id="<?=$field_id?>" type="password" />
		<?php
		break;
	    case 'textarea':
		?>
		<textarea name="<?=$field->name?>" id="<?=$field_id?>"><?=$value?></textarea>
		<?php
		break;
	    case 'custom':
		//
	    default:
		print '..['.$field->field_type.'] = ['.$value.']';
		break;
	}
	print form_error($field->name,'<div class="error">','</div>');
	if($field->field_type != 'hidden'){
	    ?>
	    </div>
	    </div>
	    <?php
	}
    }
    ?>
    <div class="row table-row">
	<div class="col-xs-3">
	    <label>
		&nbsp;
	    </label>
	</div>
	<div class="col-xs-9" style="text-align:right;">
	    <button type="submit" class="btn btn-primary">Go</button>
	</div>
    </div>
</form>