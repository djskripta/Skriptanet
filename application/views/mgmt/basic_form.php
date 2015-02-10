<?php
$ci = &get_instance();
$ci->load->helper('form');

$create_or_edit = isset($entity) ? 'Edit' : 'Create';
?>

<style type="text/css">
    [class*="col-"] > label{
	background-color:#e0e0e0;
	width:100%;
	height:100%;
	padding:6px;
	margin:0px;
	box-sizing:content-box;
	-moz-box-sizing:content-box;
	-webkit-box-sizing:content-box;
	border:1px solid #000;
	border-bottom-width:0px;
    }
    
    form .row:last-of-type [class*="col-"] > label{
	border-bottom-width:1px;
    }
    
    form#form-main label{
	height:100%;
    }
    
    .row.table-row{
	display:table;
	width:100%;
	margin:0 auto;
    }
    
    .row.table-row > [class*="col-"]{
	float:left;
	display:table-cell;
	vertical-align:top;
    }    
</style>

<h2><?=$create_or_edit?> <?=$entity_type?></h2>
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
	    <div class="col-xs-5">
	    <?php
	}
	
	switch($field->field_type){
	    case 'text':
		?>
		<input name="<?=$field->name?>" id="<?=$field_id?>" type="text" value="<?=$value?>" /><br />
		<?php
		break;
	    case 'password':
		?>
		<input name="<?=$field->name?>" id="<?=$field_id?>" type="password" /><br />
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
	<div class="col-xs-5" style="text-align:right;">
	    <button type="submit" class="btn btn-default">Go</button>
	</div>
    </div>
</form>