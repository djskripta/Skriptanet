<?php

/* 
 * @company SkriptaNet
 * @author Nikita Nikitin
 */

?>

<?php
$ci = &get_instance();
$ci->load->helper('form');
?>
<h2>Admin Login</h2>
<form name="form-main" id="form-main" method="post" action="">
    <div class="row">
	<div class="span4">
	    <label for="username">USR</label>
	    <input name="username" type="text" />
	    <br />
	    <label for="password">PWD</label>
	    <input name="password" type="password" />
	    <button type="submit" class="btn btn-default">Log In</button>
	</div>
	<div class="span8">
	    Not Registered? Sign Up.
	</div>
    </div>
</form>