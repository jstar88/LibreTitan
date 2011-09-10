<?php
/**
 * examples of cache usage
 * */
include(dirname(__FILE__).DIRECTORY_SEPARATOR.'../Xtreme.php');
$xtreme=new Xtreme();
$xtreme->setCompileDirectory('tmp/');
$xtreme->setTemplateDirectories('templates/');
$xtreme->assign('key','I have been compiled in compressed php code!');
$html=$xtreme->output('fasthello');
$html.= "<br>";

//using hardisk for storing compressed and compiled php
$xtreme2=new Xtreme();
$xtreme2->setCompileDirectory('tmp/');
$xtreme2->setTemplateDirectories('templates/');
$xtreme2->assign('key',"I'm more fast, i don't need to be compiled");
$html.=$xtreme2->output('fasthello');
$html.= "<br>";

//using hardisk and internal cache
$xtreme3=new Xtreme();
$xtreme3->setCompileDirectory('tmp/');
$xtreme3->setTemplateDirectories('templates/');
for($i=1;$i<=10;i++){
   $xtreme3->assign('key',"wow i'm super fast! number $i");
   $html.=$xtreme3->output('fasthello',true);
   $html.= "<br>";
}
echo $html;
?>
