<?php

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <title>SkriptaNet</title>
	<!--jQuery-->
	<script src="/moneytree/js/libs/jquery/jquery.min.js"></script>
	<script src="/moneytree/js/libs/jquery/jquery-migrate.min.js"></script>
	<!--Bootstrap-->
	<link rel="stylesheet" type="text/css" href="/moneytree/libs/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/moneytree/libs/bootstrap/css/bootstrap-theme.min.css">
	<!--link rel="stylesheet" type="text/css" href="/moneytree/libs/bootstrap/css/bootstrap-responsive.css"-->
	<script src="/moneytree/libs/bootstrap/js/bootstrap.min.js"></script>
	<!--Custom-->
	<link rel="stylesheet" type="text/css" href="/moneytree/css/style.css">
    </head>
    <body>
	<header>
	    <div class="nav">
		<a href="#" class="glyphicon glyphicon-list" style="float:left;"></a>
		<div style="float:left; height:100%;">
		    <h1><?=$title?></h1>
		</div>
		<div class="btn-group pull-right" role="group">
		    <!--Nav Links-->
		    <?php
		    if(!empty($nav_links)){
			foreach($nav_links as $link => $glyphicon){
			    ?>
			    <a class="btn" href="<?=$link?>">
				<span class="glyphicon glyphicon-<?=$glyphicon?>"></span>
			    </a>
			    <?php
			}
		    }
		    ?>
		    
		    <div class="btn-group" role="group">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="float:right;">
			    <span class="glyphicon glyphicon-option-vertical"></span>
			</a>
			<ul class="dropdown-menu dropdown-menu-right">
			    <li class="text-right"><a href="#"><?=$username?></a></li>
			    <li class="divider"></li>
			    <li class="text-right"><a href="#">Account Settings</a></li>
			    <li class="text-right"><a href="#">Log Out</a></li>
			</ul>
		    </div>
		</div>
	    </div>
	</header>
	<div id="container" class="container">
	    
	    <div class="wrapper">
		<?=$content?>
	    </div>
	    
	</div>
	<footer>
	    <div class="nav">
		Footer
	    </div>
	</footer>
    </body>
</html>