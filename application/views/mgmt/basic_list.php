<?php
$ci = &get_instance();
?>
<style type="text/css">
    
</style>

<h2><?=$entity_type?> Management</h2>
<ul>
    <li><a href="<?=$ci->controller_path.'/create'?>">Create <?=$entity_type?></a></li>
</ul>
<h3>Existing <?=$entity_type?>s</h3>
<ul>
<?php
foreach($results as $result){
    ?>
    <li><a href="<?=$ci->controller_path?>/edit/<?=$result[$primary_key]?>"><?=$result['email']?></a></li>
    <?php
}
?>
</ul>