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
 $parse=$lang;
 
if ($_GET['tipo'] == $lang['universe_war_add'])
	{
		$id_universe_in_war1 =  intval($_GET['select_id_universe_in_war1'])+1;
		$id_universe_in_war2 =  intval($_GET['select_id_universe_in_war2'])+1;
		$war_start =  mysql_real_escape_string($_GET['war_start']);
		$war_end =  mysql_real_escape_string($_GET['war_end']);
		$distance =  mysql_real_escape_string($_GET['distance']);
		
		doquery ("INSERT `{{table}}` SET 
    `id_universe_in_war1` = '". $id_universe_in_war1 ."' ,
    `id_universe_in_war2` = '". $id_universe_in_war2 ."' ,
    `war_start` = '". $war_start ."' ,
    `war_end` = '". $war_end ."' ,
    `distance` ='".$distance ."'",
     'universewar');
	}
	

	// Delete 
	if ($_GET['action'] == "Cancella")
	{
	  $idwar = intval($_GET['id']);
		doquery("DELETE FROM `{{table}}` WHERE `id` = '". intval($idwar) ."'", 'universewar');
	}
	


	 
	 $result = doquery("SELECT * FROM `{{table}}` ORDER BY `id` DESC", 'universewar');
	 $row = mysql_fetch_array($result);
	
	$parse['dpath']=$xgp_root;
	while ($idwar=$row["id"])
	{
    $parse['id_universe_in_war1']= $row["id_universe_in_war1"];
    $parse['id_universe_in_war2'] = $row["id_universe_in_war2"];
    $parse['war_start']= $row["war_start"];
    $parse['war_end']= $row["war_end"];
    $parse['distance'] = $row["distance"];
    $parse['id'] = $row["id"];
		$parse['war_list'] .= parsetemplate(gettemplate('adm/UniverseWarRows'), $parse);

		$row = mysql_fetch_array($result);
		
	}
	$result = doquery("SELECT * FROM `{{table}}` ORDER BY `id` DESC", 'universe');
	$row = mysql_fetch_array($result);
	$indice=mysql_num_rows($result);
	
	
	while ($idwar=$row["id"])
	{
	$indice--;
	$universe_name = $row["universe_name"];
	$parse['select_id_universe_in_war1'] .= "<option value =".$indice.">".$universe_name."</option>";
  $row = mysql_fetch_array($result);
  }
  $parse['select_id_universe_in_war2'] =  $parse['select_id_universe_in_war1'];
	 
	display(parsetemplate(gettemplate('adm/UniverseWar'), $parse), false, '', true, false);
	
?>