<?php
/*

AUTORE: Jstar

*/
define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$xgp_root = './../';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);
include('AdminFunctions/Autorization.' . $phpEx);



//ADD

if ($_GET['tipo'] == $lang['universe_settings_add'])
	{
  	$universe_name = $_GET['universe_name'];
		$maxID=0;
	  $result = doquery("SELECT * FROM `{{table}}` ORDER BY `id` DESC", 'universe');
	  $row = mysql_fetch_array($result);
	
	  while ($iduniverse=$row["id"]) {
      if($iduniverse > $maxID)
        $maxID= $iduniverse;
      $row = mysql_fetch_array($result);
	 }
  
		
		doquery ("INSERT `{{table}}` SET 
    `universe_name` = '". mysql_real_escape_string($universe_name) ."' , 
    `id` ='". ($maxID+1)."'"
    , 'universe');
    

	}

	// DELETE 
	if ($_GET['action'] == "Cancella")
	{
	  $iduniverse = $_GET['iduniverse'];
	  
		doquery("DELETE FROM `{{table}}` WHERE `id` = '". intval($iduniverse) ."'", 'universe');
    doquery("UPDATE `{{table}}` SET `id`= `id`-1 WHERE `id`> '". intval($iduniverse) ."'",'universe' );
	}
	
	//SAVE THE MODIFICATION
	if ($_GET['universe_to_mod'] != "Nessuno"){
        $universe_to_mod=intval($_GET['universe_to_mod']);
        $new_universe_name=mysql_real_escape_string($_GET[$universe_to_mod]);
        
        doquery ("UPDATE `{{table}}` SET `universe_name` = '". mysql_real_escape_string($new_universe_name) ."' WHERE `id` = '". $universe_to_mod ."'", 'universe');       
  } 

	 $parse=$lang; 
	 $result = doquery("SELECT * FROM `{{table}}` ORDER BY `id` DESC", 'universe');
	 $row = mysql_fetch_array($result);
	
   $parse['universe_list'] .= "<script type='text/javascript'>\n";
   $parse['universe_list'] .="function showhidesimple(targetElementId){\n";
   $parse['universe_list'] .="var targetElement = document.getElementById(targetElementId);\n";
   $parse['universe_list'] .="if(targetElement.style.display!='none')\n";
   $parse['universe_list'] .=" targetElement.style.display='none';\n";
   $parse['universe_list'] .="else\n";
   $parse['universe_list'] .= "targetElement.style.display='block';\n"; 
   $parse['universe_list'] .= "}\n</script>";
	
  while ($iduniverse=$row["id"])
	{
    $parse['universe_name']= $row["universe_name"];
    $parse['iduniverse'] = $row["id"];   
		$parse['universe_list'] .= parsetemplate(gettemplate('adm/UniverseSettingsRows'), $parse);
    $row = mysql_fetch_array($result);
	}
  	 
	display(parsetemplate(gettemplate('adm/UniverseSettings'), $parse), false, '', true, false);
	
?>