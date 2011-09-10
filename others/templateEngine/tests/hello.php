<?php
/**
 *A simple example of variable usage
 * */
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'../Xtreme.php');
$xtreme=new Xtreme();
$xtreme->setCompileDirectory('tmp/');
$xtreme->setTemplateDirectories('templates/');
$xtreme->assign('key','Hello world!');
$xtreme->output('hello',false,true);
?>