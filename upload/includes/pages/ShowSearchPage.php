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

if(!defined('INSIDE')){ die(header("location:../../"));}

function ShowSearchPage()
{
	global $dpath, $lang;

	$parse 	= $lang;
	$type 	= $_POST['type'];

	$searchtext = mysql_escape_string($_POST["searchtext"]);
   if ( $_POST )
	{
	switch($type)
	{
		case "playername":
			$table 	= gettemplate('search/search_user_table');
			$row 	= gettemplate('search/search_user_row');
			$search = doquery("SELECT * FROM {{table}} WHERE username LIKE '%{$searchtext}%' LIMIT 25;","users");
		break;
		case "planetname":
			$table 	= gettemplate('search/search_user_table');
			$row 	= gettemplate('search/search_user_row');
			$search = doquery("SELECT * FROM {{table}} WHERE name LIKE '%{$searchtext}%' LIMIT 25",'planets');
		break;
		case "allytag":
			$table 	= gettemplate('search/search_ally_table');
			$row 	= gettemplate('search/search_ally_row');
			$search = doquery("SELECT * FROM {{table}} WHERE ally_tag LIKE '%{$searchtext}%' LIMIT 25","alliance");
		break;
		case "allyname":
			$table 	= gettemplate('search/search_ally_table');
			$row 	= gettemplate('search/search_ally_row');
			$search = doquery("SELECT * FROM {{table}} WHERE ally_name LIKE '%{$searchtext}%' LIMIT 25","alliance");
		break;
		default:
			$table 	= gettemplate('search/search_user_table');
			$row 	= gettemplate('search/search_user_row');
			$search = doquery("SELECT * FROM {{table}} WHERE username LIKE '%{$searchtext}%' LIMIT 25","users");
	}
   }
	if(isset($searchtext) && isset($type))
	{
		while($s = mysql_fetch_array($search, MYSQL_BOTH))
		{
			if($type == 'playername' or $type == 'planetname')
			{
				if($s['ally_id'] != 0 && $s['ally_request'] == 0)
				{
					$aquery = doquery("SELECT id,ally_name FROM {{table}} WHERE id = ".intval($s['ally_id'])."","alliance",true);
				}
				else
				{
					$aquery = array();
				}

				if ($type == "planetname")
				{
					$pquery 			= doquery("SELECT username,ally_id,ally_name FROM {{table}} WHERE id = ".intval($s['id_owner'])."","users",true);
					$s['planet_name'] 	= $s['name'];
					$s['username'] 		= $pquery['username'];
					$s['ally_name'] 	= ($pquery['ally_name']!='')?"<a href=\"game.php?page=alliance&mode=ainfo&a={$pquery['ally_id']}\">{$pquery['ally_name']}</a>":'';
				}
				else
				{
					$pquery 			= doquery("SELECT name FROM {{table}} WHERE id = ".intval($s['id_planet'])."","planets",true);
					$s['planet_name']	= $pquery['name'];
					$s['ally_name'] 	= ($aquery['ally_name']!='')?"<a href=\"game.php?page=alliance&mode=ainfo&a={$aquery['id']}\">{$aquery['ally_name']}</a>":'';
				}

				$s['position'] 		= "<a href=\"game.php?page=statistics&start=".$s['rank']."\">".$s['rank']."</a>";
				$s['dpath'] 		= $dpath;
				$s['coordinated'] 	= "{$s['universe']}:{$s['galaxy']}:{$s['system']}:{$s['planet']}";
				$result_list 	   .= parsetemplate($row, $s);
			}
			elseif($type=='allytag'||$type=='allyname')
			{
				$s['ally_points'] = pretty_number($s['ally_points']);

				$s['ally_tag'] = "<a href=\"game.php?page=alliance&mode=ainfo&tag={$s['ally_tag']}\">{$s['ally_tag']}</a>";
				$result_list .= parsetemplate($row, $s);
			}
		}
		if($result_list!='')
		{
			$parse['result_list'] = $result_list;
			$search_results = parsetemplate($table, $parse);
		}
	}

	$parse['type_playername'] 	= ($_POST["type"] == "playername") ? " SELECTED" : "";
	$parse['type_planetname'] 	= ($_POST["type"] == "planetname") ? " SELECTED" : "";
	$parse['type_allytag'] 		= ($_POST["type"] == "allytag") ? " SELECTED" : "";
	$parse['type_allyname'] 	= ($_POST["type"] == "allyname") ? " SELECTED" : "";
	$parse['searchtext'] 		= $searchtext;
	$parse['search_results'] 	= $search_results;

	display(parsetemplate(gettemplate('search/search_body'), $parse));
}
?>