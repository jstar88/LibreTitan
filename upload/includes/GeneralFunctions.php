<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2009 By lucky from xgproyect.net      	 #
# *																			 #
# *																			 #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.									 #
# *																			 #
# *  This program is distributed in the hope that it will be useful,		 #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of			 #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the			 #
# *  GNU General Public License for more details.							 #
# *																			 #
##############################################################################


function deep ( $value )
{
	$value = is_array($value) ? array_map('deep', $value) :  addslashes(trim( nl2br( strip_tags($value ) ) ));
	return $value;
}

function  addslashes_deep ( $value )
{
	$value = is_array($value) ?  array_map('addslashes_deep', $value) : addslashes($value);
	return $value;
}

function unset_vars( $prefix )
{
	$vars = array_keys($GLOBALS);
	for( $n = 0, $i = 0;  $i < count($vars);  $i ++ )
		if( strpos($vars[$i], $prefix) === 0 )
		{
			unset($GLOBALS[$vars[$i]]);
			$n ++;
		}

	return  $n;
}

function update_config( $config_name, $config_value )
{
	global $game_config;
	doquery("UPDATE {{table}} SET `config_value` = '".$config_value."' WHERE `config_name` = '".$config_name."';",'config');
}

function is_email($email)
{
	return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
}

function message ($mes, $dest = "", $time = "3", $topnav = false, $menu = true)
{
	$parse['mes']   = $mes;

	$page .= parsetemplate(gettemplate('message_body'), $parse);

	if (!defined('IN_ADMIN'))
	{
		display ($page, $topnav, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"$time;URL=$dest\">" : ""), false, $menu);
	}
	else
	{
		display ($page, $topnav, (($dest != "") ? "<meta http-equiv=\"refresh\" content=\"$time;URL=$dest\">" : ""), true, false);
	}

}

function display ($page, $topnav = true, $metatags = '', $AdminPage = false, $menu = true)
{
	global $link, $game_config, $debug, $user, $planetrow, $xgp_root, $phpEx;

	if (!$AdminPage)
		$DisplayPage  = StdUserHeader($metatags);
	else
		$DisplayPage  = AdminUserHeader($metatags);

	if ($topnav)
	{
		include_once($xgp_root . 'includes/functions/ShowTopNavigationBar.' . $phpEx);
		$DisplayPage .= ShowTopNavigationBar( $user, $planetrow );
	}

	if ($menu && !$AdminPage)
	{
		include_once($xgp_root . 'includes/functions/ShowLeftMenu.' . $phpEx);
		$DisplayPage .= ShowLeftMenu ($user);
	}

	$DisplayPage .= "\n<center>\n". $page ."\n</center>\n";

	if(!defined('LOGIN') && $_GET['page'] != 'galaxy')
		$DisplayPage .= parsetemplate(gettemplate('footer'), $parse);

	if ($link)
	{
		mysql_close($link);
	}

	if ( $user['authlevel'] == 3 && $game_config['debug'] == 1 && !$AdminPage )
	{
		// Convertir a objeto dom
		$DisplayPage = str_get_html($DisplayPage);

		// Modificar div#content
		$content = $DisplayPage->find("div#content", 0);

		// Contenido debug
		$content->innertext .= $debug->echo_log();
	}

	echo $DisplayPage;

	if ( $user['authlevel'] == 3 && $game_config['debug'] == 1 && $AdminPage)
	{

		echo "<center>";
		echo $debug->echo_log();
		echo "</center>";
	}

	die();
}

function StdUserHeader ($metatags = '')
{
	global $dpath, $game_config;

	$parse['-title-'] 	.= $game_config['game_name'];
	$parse['-favi-']	.= "<link rel=\"shortcut icon\" href=\"./favicon.ico\">\n";

	if(!defined('LOGIN'))
	{
		$parse['-style-']  	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/css/default.css\">\n";
		$parse['-style-']  	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/css/formate.css\">\n";
		$parse['-style-'] 	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"". $dpath ."formate.css\" />\n";
	}
	else
	{
		$parse['-style-']  	.= "<link rel=\"stylesheet\" type=\"text/css\" href=\"styles/css/styles.css\">\n";
	}

	$parse['-meta-']	.= ($metatags) ? $metatags : "";
	$parse['-meta-']	.= "<script type=\"text/javascript\" src=\"scripts/overlib.js\"></script>\n";

	return parsetemplate(gettemplate('simple_header'), $parse);
}

function AdminUserHeader ($metatags = '')
{
	global $game_config;

	if (!defined('IN_ADMIN'))
		$parse['-title-'] 	.= 	"XG Proyect - Install";
	else
		$parse['-title-'] 	.= 	$game_config['game_name'] . " - Admin CP";

	$parse['-favi-']	.= 	"<link rel=\"shortcut icon\" href=\"./../favicon.ico\">\n";
	$parse['-style-']	.=	"<link rel=\"stylesheet\" type=\"text/css\" href=\"./../styles/css/admin.css\">\n";
	$parse['-meta-']	.= 	"<script type=\"text/javascript\" src=\"./../scripts/overlib.js\"></script>\n";
	$parse['-meta-'] 	.= ($metatags) ? $metatags : "";
	return parsetemplate(gettemplate('adm/simple_header'), $parse);
}

function CalculateMaxPlanetFields (&$planet)
{
	global $resource;
	return $planet["field_max"] + ($planet[ $resource[33] ] * FIELDS_BY_TERRAFORMER);
}

function GetTargetDistance ($OrigUniverse, $DestUniverse,$OrigGalaxy, $DestGalaxy, $OrigSystem, $DestSystem, $OrigPlanet, $DestPlanet)
{
//start variables
 $distance = 0;

	if (($OrigUniverse - $DestUniverse) != 0)
		$distance = abs($OrigUniverse - $DestUniverse) * 10000;
  if (($OrigGalaxy - $DestGalaxy) != 0)
		$distance += abs($OrigGalaxy - $DestGalaxy) * 20000;
	elseif (($OrigSystem - $DestSystem) != 0)
		$distance += abs($OrigSystem - $DestSystem) * 5 * 19 + 2700;
	elseif (($OrigPlanet - $DestPlanet) != 0)
		$distance += abs($OrigPlanet - $DestPlanet) * 5 + 1000;
	else
		$distance += 5;

	return $distance;
}  

function GetMissionDuration ($GameSpeed, $MaxFleetSpeed, $Distance, $SpeedFactor)
{
	$Duration = 0;
	$Duration = round(((35000 / $GameSpeed * sqrt($Distance * 10 / $MaxFleetSpeed) + 10) / $SpeedFactor));
	return $Duration;
}

function GetGameSpeedFactor ()
{
	global $game_config;
	return $game_config['fleet_speed'] / 2500;
}

function GetFleetMaxSpeed ($FleetArray, $Fleet, $Player)
{
	global $reslist, $pricelist;

	if ($Fleet != 0)
		$FleetArray[$Fleet] =  1;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship == 202)
		{
			if ($Player['impulse_motor_tech'] >= 5)
				$speedalls[$Ship] = $pricelist[$Ship]['speed2'] + (($pricelist[$Ship]['speed'] * $Player['impulse_motor_tech']) * 0.2);
			else
				$speedalls[$Ship] = $pricelist[$Ship]['speed']  + (($pricelist[$Ship]['speed'] * $Player['combustion_tech']) * 0.1);
		}
		if ($Ship == 203 or $Ship == 204 or $Ship == 209 or $Ship == 210)
			$speedalls[$Ship] = $pricelist[$Ship]['speed'] + (($pricelist[$Ship]['speed'] * $Player['combustion_tech']) * 0.1);

		if ($Ship == 205 or $Ship == 206 or $Ship == 208)
			$speedalls[$Ship] = $pricelist[$Ship]['speed'] + (($pricelist[$Ship]['speed'] * $Player['impulse_motor_tech']) * 0.2);

		if ($Ship == 211)
		{
			if ($Player['hyperspace_motor_tech'] >= 8)
				$speedalls[$Ship] = $pricelist[$Ship]['speed2'] + (($pricelist[$Ship]['speed'] * $Player['hyperspace_motor_tech']) * 0.3);
			else
				$speedalls[$Ship] = $pricelist[$Ship]['speed']  + (($pricelist[$Ship]['speed'] * $Player['impulse_motor_tech']) * 0.2);
		}

		if ($Ship == 207 or $Ship == 213 or $Ship == 214 or $Ship == 215 or $Ship == 216)
			$speedalls[$Ship] = $pricelist[$Ship]['speed'] + (($pricelist[$Ship]['speed'] * $Player['hyperspace_motor_tech']) * 0.3);
	}

	if ($Fleet != 0)
	{
		$ShipSpeed = $speedalls[$Ship];
		$speedalls = $ShipSpeed;
	}

	return $speedalls;
}

function GetShipConsumption ( $Ship, $Player )
{
	global $pricelist;

	if ($Player['impulse_motor_tech'] >= 5)
		$Consumption  = $pricelist[$Ship]['consumption2'];
	else
		$Consumption  = $pricelist[$Ship]['consumption'];

	return $Consumption;
}

function GetFleetConsumption ($FleetArray, $SpeedFactor, $MissionDuration, $MissionDistance, $FleetMaxSpeed, $Player)
{
	$consumption = 0;
	$basicConsumption = 0;

	foreach ($FleetArray as $Ship => $Count)
	{
		if ($Ship > 0)
		{
			$ShipSpeed         = GetFleetMaxSpeed ( "", $Ship, $Player );
			$ShipConsumption   = GetShipConsumption ( $Ship, $Player );
			$spd               = 35000 / ($MissionDuration * $SpeedFactor - 10) * sqrt( $MissionDistance * 10 / $ShipSpeed );
			$basicConsumption  = $ShipConsumption * $Count;
			$consumption      += $basicConsumption * $MissionDistance / 35000 * (($spd / 10) + 1) * (($spd / 10) + 1);
		}
	}

	$consumption = round($consumption) + 1;

	return $consumption;
}

function pretty_time ($seconds)
{
	$day = floor($seconds / (24 * 3600));
	$hs = floor($seconds / 3600 % 24);
	$ms = floor($seconds / 60 % 60);
	$sr = floor($seconds / 1 % 60);

	if ($hs < 10) { $hh = "0" . $hs; } else { $hh = $hs; }
	if ($ms < 10) { $mm = "0" . $ms; } else { $mm = $ms; }
	if ($sr < 10) { $ss = "0" . $sr; } else { $ss = $sr; }

	$time = '';
	if ($day != 0) { $time .= $day . 'd '; }
	if ($hs  != 0) { $time .= $hh . 'h ';  } else { $time .= '00h '; }
	if ($ms  != 0) { $time .= $mm . 'm ';  } else { $time .= '00m '; }
	$time .= $ss . 's';

	return $time;
}

function pretty_time_hour ($seconds)
{
	$min = floor($seconds / 60 % 60);
	$time = '';
	if ($min != 0) { $time .= $min . 'min '; }
	return $time;
}

function ShowBuildTime($time)
{
	global $lang;
	return "<br>". $lang['fgf_time'] . pretty_time($time);
}

function parsetemplate ($template, $array)
{
	return preg_replace('#\{([a-z0-9\-_]*?)\}#Ssie', '( ( isset($array[\'\1\']) ) ? $array[\'\1\'] : \'\' );', $template);
}

function gettemplate ($templatename)
{
	global $xgp_root;
	return @file_get_contents($xgp_root . TEMPLATE_DIR . '/' . $templatename . ".tpl");
}

function includeLang ($filename, $ext = '.mo')
{
	global $xgp_root, $lang;

	include ($xgp_root . "language/". DEFAULT_LANG ."/". $filename.$ext);
}

function GetStartAdressLink ( $FleetRow, $FleetType )
{  //start mod
	$Link  = "<a href=\"game.php?page=galaxy&mode=3&universe=".$FleetRow['fleet_start_universe']."&galaxy=".$FleetRow['fleet_start_galaxy']."&system=".$FleetRow['fleet_start_system']."\" ". $FleetType ." >";
	$Link .= "[".$FleetRow['fleet_start_universe'].":".$FleetRow['fleet_start_galaxy'].":".$FleetRow['fleet_start_system'].":".$FleetRow['fleet_start_planet']."]</a>";
 //end mod
	return $Link;
}

function GetTargetAdressLink ( $FleetRow, $FleetType )
{
//start mod
	$Link  = "<a href=\"game.php?page=galaxy&mode=3&universe=".$FleetRow['universe']."&galaxy=".$FleetRow['fleet_end_galaxy']."&system=".$FleetRow['fleet_end_system']."\" ". $FleetType ." >";
	$Link .= "[".$FleetRow['fleet_end_universe'].":".$FleetRow['fleet_end_galaxy'].":".$FleetRow['fleet_end_system'].":".$FleetRow['fleet_end_planet']."]</a>";
	//end mod
  return $Link;
}

function BuildPlanetAdressLink ( $CurrentPlanet )
{
//start mod
	$Link  = "<a href=\"game.php?page=galaxy&mode=3&universe=".$CurrentPlanet['universe']."&galaxy=".$CurrentPlanet['galaxy']."&system=".$CurrentPlanet['system']."\">";
	$Link .= "[".$CurrentPlanet['universe'].":".$CurrentPlanet['galaxy'].":".$CurrentPlanet['system'].":".$CurrentPlanet['planet']."]</a>";
//end mod	
  return $Link;
}

function doquery($query, $table, $fetch = false)
{
	global $link, $debug, $xgp_root;

	require($xgp_root.'config.php');

	if(!$link)
	{
		$link = mysql_connect($dbsettings["server"], $dbsettings["user"], $dbsettings["pass"]) or $debug->error(mysql_error()."<br />$query","SQL Error");
		mysql_select_db($dbsettings["name"]) or $debug->error(mysql_error()."<br />$query","SQL Error");
		echo mysql_error();
	}

	$sql 		= str_replace("{{table}}", $dbsettings["prefix"].$table, $query);
	$sqlquery 	= mysql_query($sql) or $debug->error(mysql_error()."<br />$sql<br />","SQL Error");

	unset($dbsettings);

	global $numqueries,$debug;
	$numqueries++;

	$debug->add("<tr><th>Query $numqueries: </th><th>$query</th><th>$table</th><th>$fetch</th></tr>");

	if($fetch)
		return mysql_fetch_array($sqlquery);
	else
		return $sqlquery;
}

function colorNumber($n, $s = '')
{
	if ($n > 0)
		if ($s != '')
			$s = colorGreen($s);
		else
			$s = colorGreen($n);
	elseif ($n < 0)
		if ($s != '')
			$s = colorRed($s);
		else
			$s = colorRed($n);
	else
		if ($s != '')
			$s = $s;
		else
			$s = $n;

	return $s;
}

function colorRed($n)
{
	return '<font color="#ff0000">' . $n . '</font>';
}

function colorGreen($n)
{
	return '<font color="#00ff00">' . $n . '</font>';
}

function pretty_number($n, $floor = true)
{
	if ($floor)
		$n = floor($n);

	return number_format($n, 0, ",", ".");
}

function shortly_number($number)
{
	// MAS DEL TRILLON
	if ($number >= 1000000000000000000000000)
		return pretty_number(($number/1000000000000000000))."&nbsp;<font color=lime>T+</font>";

	// TRILLON
	elseif ($number >= 1000000000000000000 && $number < 1000000000000000000000000)
		return pretty_number(($number/1000000000000000000))."&nbsp;<font color=lime>T</font>";

	// BILLON
	elseif ($number >= 1000000000000 && $number < 1000000000000000000)
		return pretty_number(($number/1000000000000))."&nbsp;<font color=lime>B</font>";

	// MILLON
	elseif ($number >= 1000000 && $number < 1000000000000)
		return pretty_number(($number/1000000))."&nbsp;<font color=lime>M</font>";

	// MIL
	elseif ($number >= 1000 && $number < 1000000)
		return pretty_number(($number/1000))."&nbsp;<font color=lime>K</font>";

	// NUMERO SIN DEFINIR
	else
		return pretty_number($number);
}

function floattostring($Numeric, $Pro = 0, $Output = false)
{
	return ($Output) ? str_replace(",",".", sprintf("%.".$Pro."f", $Numeric)) : sprintf("%.".$Pro."f", $Numeric);
}

function isUniverseInWar($universe1,$universe2){
 
 global $war_config;
  updateWars();
 if($universe1==$universe2)
   return 0;
 else{
  foreach ($war_config as $war) {
     if(        
      ($war['id_universe_in_war1']==$universe1&& $war['id_universe_in_war2']==$universe2 && $war['on_time_to_war']== 1 )||
      ($war['id_universe_in_war1']==$universe2&& $war['id_universe_in_war2']==$universe1 && $war['on_time_to_war']== 1 )      
      )
       return 1;
 }
 return 0;
}
}

function universesInWarWith($universe){
  global $war_config;
  
  $universes_in_war_with_this_universe=array();
  $universes_in_war_with_this_universe[]=$universe;
  updateWars();
  
  foreach ($war_config as $war){
   
    if($war['id_universe_in_war1']==$universe && $war['on_time_to_war']== 1){
       $universes_in_war_with_this_universe[]= $war['id_universe_in_war2'];
    }
    else if ($war['id_universe_in_war2']==$universe && $war['on_time_to_war']== 1){
       $universes_in_war_with_this_universe[]= $war['id_universe_in_war1'];
    }
    
    
  }
  sort($universes_in_war_with_this_universe);
  return $universes_in_war_with_this_universe;
}

function isUniverseInWarGeneric($universe){
   
    return count(universesInWarWith($universe))>1;
}

function updateWars()
{
  global $war_config;
  
  foreach ($war_config as &$war){
    	if (  time() >= ( $war['war_start'] ) && 
            time() <= ( $war['war_end'  ] )  
         ){
                    
	  	  $war['on_time_to_war']= 1; 
        }
	  	else{
	  	  $war['on_time_to_war']= 0; 
      }
	}
}
function getTimeFromDatabaseDate($dbTime){

    $time= getDatabaseDate($dbTime);
    return mktime($time[0], $time[1], $time[2], $time[3], $time[4], $time[5]);
    
}
function getDatabaseDate($dbTime){

    $arrstring = explode("-", $dbTime);
    
    $anno=$arrstring[0];
    $mese=$arrstring[1];
    $arrstring = explode(" ", $arrstring[2]);
    $giorno=$arrstring[0];
    $arrstring = explode(":", $arrstring[1]);
    $ore=$arrstring[0];
    $minuti=$arrstring[1];
    $secondi=$arrstring[2];
    return array($ore, $minuti, $secondi, $mese, $giorno, $anno);
    
}
function warWithThis($universe){
  global $war_config;
  
  $war_with_this_universe=array();
  updateWars();
  foreach ($war_config as $war){
    if($war['id_universe_in_war1']==$universe ||
       $war['id_universe_in_war2']==$universe )
    {
      if($war['on_time_to_war']==1)   
        $war_with_this_universe[]= $war;        
    }
  
 }
 return $war_with_this_universe;
}
function willFightWithThis($universe){
  global $war_config;
   
  $war_with_this_universe=array();
  
  foreach ($war_config as $war){
     
    if($war['id_universe_in_war1']==$universe ||
       $war['id_universe_in_war2']==$universe )
    {  
      if(time() <=  $war['war_end']){ 
          
        $war_with_this_universe[]= $war;
      }    
    }
 }
 return $war_with_this_universe;
}
//start mod- optimized by think
 function getRealLanguage($IDuser1,$IDuser2,$extension=".mo"){
      global $lang,$xgp_root,$lang_memo;
            
            if(isset($lang_memo[$IDuser2]) AND $lang_memo[$IDuser2] != "") 
            {
                $OwnerLanguageName = $lang_memo[$IDuser2];
                if($OwnerLanguageName == $lang['lang'])
                  return NULL;                 
                require_once($xgp_root . 'language/'.$OwnerLanguageName."/SYSTEM".$extension);
                return $langSystem;
            }
            if($IDuser1 != $IDuser2)
            {
                $QrySelect  = "SELECT lang FROM {{table}} ";
                $QrySelect .= "WHERE ";
                $QrySelect .= "`id` = '".$IDuser2."';";
                $result= doquery( $QrySelect, 'users',true);
                $lang_memo[$IDuser2] = $result['lang'];
                if($result['lang'] == $lang['lang'])
                  return NULL;
                require_once($xgp_root . 'language/'.$lang_memo[$IDuser2]."/SYSTEM".$extension); 
                return $langSystem ;
            }
        return  NULL;
} 
function isShip($i){
   return $i <= 216 && $i >= 202;
} 

?>