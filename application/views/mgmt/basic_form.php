<?php
$ci = &get_instance();
$ci->load->helper('form');

if(!function_exists('form_error')){
    function form_error($field){
	return isset($errors[$field]) ? '<div class="error">'.$errors[$field].'</div>' : '';
    }
}
?>
<h1>Create <?=ucfirst(str_replace('_model','',$model))?></h1>
<div id="container">

    <form name="form-main" id="form-main" method="post" action="">    
	<?php
	$skip_fields = array('id','created','updated');

	foreach($schema as $field){
	    if(in_array($field->name, $skip_fields)) continue;
	    switch($field->field_type){
		case 'text':
		    print $field->label.' <input name="'.$field->name.'" type="text" /><br />';
		    break;
		case 'password':
		    print $field->label.' <input name="'.$field->name.'" type="password" /><br />';
		    break;
		default:
		    print $field->label.'..['.$field->field_type.']';
		    break;
	    }
	    //print $field->label.' <input name="'.$field->name.'" type="text" /><br />';
	    //print isset($errors[$field->name]) ? '<div class="error">'.$errors[$field->name][0].'</div>' : '';
	}

	print '<input type="submit" value="Go" />';
	?>
    </form>
</div>