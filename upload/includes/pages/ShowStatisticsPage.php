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

function ShowStatisticsPage($CurrentUser)
{
	global $game_config, $dpath, $lang;

	$parse	= $lang;
	$who   	= (isset($_POST['who']))   ? $_POST['who']   : $_GET['who'];
	if (!isset($who))
		$who   = 1;

	$type  	= (isset($_POST['type']))  ? $_POST['type']  : $_GET['type'];
	if (!isset($type))
		$type  = 1;

	$range 	= (isset($_POST['range'])) ? $_POST['range'] : $_GET['range'];
	if (!isset($range))
		$range = 1;

	$parse['who']    = "<option value=\"1\"". (($who == "1") ? " SELECTED" : "") .">".$lang['st_player']."</option>";
	$parse['who']   .= "<option value=\"2\"". (($who == "2") ? " SELECTED" : "") .">".$lang['st_alliance']."</option>";

	$parse['type']   = "<option value=\"1\"". (($type == "1") ? " SELECTED" : "") .">".$lang['st_points']."</option>";
	$parse['type']  .= "<option value=\"2\"". (($type == "2") ? " SELECTED" : "") .">".$lang['st_fleets']."</option>";
	$parse['type']  .= "<option value=\"3\"". (($type == "3") ? " SELECTED" : "") .">".$lang['st_researh']."</option>";
	$parse['type']  .= "<option value=\"4\"". (($type == "4") ? " SELECTED" : "") .">".$lang['st_buildings']."</option>";
	$parse['type']  .= "<option value=\"5\"". (($type == "5") ? " SELECTED" : "") .">".$lang['st_defenses']."</option>";

	switch ($type)
	{
		case 1:
			$Order   = "total_points";
			$Points  = "total_points";
			$Counts  = "total_count";
			$Rank    = "total_rank";
			$OldRank = "total_old_rank";
		break;
		case 2:
			$Order   = "fleet_count";
			$Points  = "fleet_points";
			$Counts  = "fleet_count";
			$Rank    = "fleet_rank";
			$OldRank = "fleet_old_rank";
		break;
		case 3:
			$Order   = "tech_count";
			$Points  = "tech_points";
			$Counts  = "tech_count";
			$Rank    = "tech_rank";
			$OldRank = "tech_old_rank";
		break;
		case 4:
			$Order   = "build_points";
			$Points  = "build_points";
			$Counts  = "build_count";
			$Rank    = "build_rank";
			$OldRank = "build_old_rank";
		break;
		case 5:
			$Order   = "defs_points";
			$Points  = "defs_points";
			$Counts  = "defs_count";
			$Rank    = "defs_rank";
			$OldRank = "defs_old_rank";
		break;
		default:
			$Order   = "total_points";
			$Points  = "total_points";
			$Counts  = "total_count";
			$Rank    = "total_rank";
			$OldRank = "total_old_rank";
		break;
	}

	if ($who == 2)
	{
		$MaxAllys = doquery ("SELECT COUNT(*) AS `count` FROM {{table}};", 'alliance', true);

		if ($MaxAllys['count'] > 100)
		{
			$LastPage = floor($MaxAllys['count'] / 100);
		}

		$parse['range'] = "";

		for ($Page = 0; $Page <= $LastPage; $Page++)
		{
			$PageValue      = ($Page * 100) + 1;
			$PageRange      = $PageValue + 99;
			$parse['range'] .= "<option value=\"". $PageValue ."\"". (($range >= $PageValue && $range <= $PageRange) ? " SELECTED" : "") .">". $PageValue ."-". $PageRange ."</option>";
		}

		$parse['stat_header'] = parsetemplate(gettemplate('stat/stat_alliancetable_header'), $parse);
		$start = floor($range / 100 % 100) * 100;
		$stats_sql	=	'SELECT s.*, a.id, a.ally_members, a.ally_tag, a.ally_name FROM {{table}}statpoints as s
		INNER JOIN {{table}}alliance as a ON a.id = s.id_owner
		WHERE `stat_type` = 2 AND `stat_code` = 1
		ORDER BY `'. $Order .'` DESC LIMIT '. $start .',100;';

		$start++;
		$parse['stat_date']   = date("Y-m-d, H:i:s",$game_config['stat_last_update']);
		$parse['stat_values'] = "";
		$query = doquery($stats_sql, '');

		while ($StatRow = mysql_fetch_assoc($query))
		{
			$parse['ally_rank']       = $start;
			if ( $StatRow[ $OldRank ] == 0 || $StatRow[ $Rank ] == 0)
			{
				$rank_old				= $start;
				$QryUpdRank				= doquery("UPDATE {{table}} SET `".$Rank."` = '".$start."', `".$OldRank."` = '".$start."' WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". intval($StatRow['id_owner']) ."';" , "statpoints");
				$StatRow[ $OldRank ]	= $start;
				$StatRow[ $Rank ]		= $start;
			}

			$ranking                  = $StatRow[ $OldRank ] - $StatRow[ $Rank ];

			if ($ranking == 0)
			{
				$parse['ally_rankplus']   = "<font color=#87CEEB>*</font>";
			}

			if ($ranking < 0)
			{
				$parse['ally_rankplus']   = "<font color=red>-".$ranking."</font>";
			}

			if ($ranking > 0)
			{
				$parse['ally_rankplus']   = "<font color=green>+".$ranking."</font>";
			}

			$parse['ally_tag']        	  = $StatRow['ally_tag'];
			$parse['ally_name']       	  = $StatRow['ally_name'];
			$parse['ally_mes']        	  = '';
			$parse['ally_members']    	  = $StatRow['ally_members'];
			$parse['ally_points']     	  = pretty_number( $StatRow[ $Order ] );
			$parse['ally_members_points'] =  pretty_number( floor($StatRow[ $Order ] / $StatRow['ally_members']) );
			$parse['stat_values']    	 .= parsetemplate(gettemplate('stat/stat_alliancetable'), $parse);
			$start++;
		}
	}
	else
	{
		$MaxUsers = doquery ("SELECT COUNT(*) AS `count` FROM {{table}} WHERE `db_deaktjava` = '0';", 'users', true);

		if ($MaxUsers['count'] > 100)
		{
			$LastPage = floor($MaxUsers['count'] / 100);
		}

		$parse['range'] = "";

		for ($Page = 0; $Page <= $LastPage; $Page++)
		{
			$PageValue      = ($Page * 100) + 1;
			$PageRange      = $PageValue + 99;

			$parse['range'] .= "<option value=\"". $PageValue ."\"". (($range >= $PageValue && $range <= $PageRange) ? " SELECTED" : "") .">". $PageValue ."-". $PageRange ."</option>";
		}


		$parse['stat_header'] = parsetemplate(gettemplate('stat/stat_playertable_header'), $parse);

		$start = floor($range / 100 % 100) * 100;

		$stats_sql	=	'SELECT s.*, u.id, u.username, u.ally_id, u.ally_name FROM {{table}}statpoints as s
		INNER JOIN {{table}}users as u ON u.id = s.id_owner
		WHERE `stat_type` = 1 AND `stat_code` = 1
		ORDER BY `'. $Order .'` DESC LIMIT '. $start .',100;';

		$query = doquery($stats_sql, '');

		$start++;

		$parse['stat_date']   = date("Y-m-d, H:i:s",$game_config['stat_last_update']);
		$parse['stat_values'] = "";

		$previusId = 0;

		while ($StatRow = mysql_fetch_assoc($query))
		{
			$parse['player_rank']     = $start;
			if ( $StatRow[ $OldRank ] == 0 || $StatRow[ $Rank ] == 0)
			{
				$rank_old				= $start;
				$QryUpdRank				= doquery("UPDATE {{table}} SET `".$Rank."` = '".$start."', `".$OldRank."` = '".$start."' WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '". intval($StatRow['id_owner']) ."';" , "statpoints");
				$StatRow[ $OldRank ]	= $start;
				$StatRow[ $Rank ]		= $start;
			}

			$ranking                  = $StatRow[ $OldRank ] - $StatRow[ $Rank ];

			if ($StatRow['id'] != $previusId)
			{
				$previusId 			= $StatRow['id'];

				if ($ranking == 0)
				{
					$parse['player_rankplus'] = "<font color=#87CEEB>*</font>";
				}

				if ($ranking < 0)
					$parse['player_rankplus'] = "<font color=red>".$ranking."</font>";

				if ($ranking > 0)
					$parse['player_rankplus'] = "<font color=green>+".$ranking."</font>";

				if ($StatRow['id'] == $CurrentUser['id'])
					$parse['player_name']     = "<font color=\"lime\">".$StatRow['username']."</font>";
				else
					$parse['player_name']     = $StatRow['username'];

				if ($StatRow['id'] != $CurrentUser['id'])
					$parse['player_mes']      = "<a href=\"game.php?page=messages&mode=write&id=" . $StatRow['id'] . "\"><img src=\"" . $dpath . "img/m.gif\" border=\"0\" title=\"Escribir un mensaje\" /></a>";
				else
					$parse['player_mes']      = "";

				if ($StatRow['ally_name'] == $CurrentUser['ally_name'])
				{
					$parse['player_alliance'] = "<a href=\"game.php?page=alliance&mode=ainfo&a=".$StatRow['ally_id']."\"><font color=\"#33CCFF\">".$StatRow['ally_name']."</font></a>";
				}
				else
				{
					$parse['player_alliance'] = "<a href=\"game.php?page=alliance&mode=ainfo&a=".$StatRow['ally_id']."\">".$StatRow['ally_name']."</a>";
				}
				$parse['player_points']   = pretty_number( $StatRow[ $Order ] );
				$parse['stat_values']    .= parsetemplate(gettemplate('stat/stat_playertable'), $parse);
				$start++;
			}
		}
	}

	display(parsetemplate( gettemplate('stat/stat_body'), $parse ));
}
?>