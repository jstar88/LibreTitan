<?php
/**
 *A simple example of array usage
 * */
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'../Xtreme.php');
$xtreme=new Xtreme();

$array={1=>'apple',2=>'pear'};
$array2={3=>'strawberry',4=>'plum'};
$xtreme->setCompileDirectory('tmp/');
$xtreme->setTemplateDirectories('templates/');
$xtreme->assign($array);
$xtreme->assign('key',$array2);
$html=$xtreme->output('fruits');
echo $html;
?>
