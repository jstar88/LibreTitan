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

	function GetBuildingTime ($user, $planet, $Element, $level = false)
	{
		global $pricelist, $resource, $reslist, $game_config;

    if($level === false)    
		  $level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
		if ( is_array($reslist['build']) && in_array($Element, $reslist['build']))
		{
			$cost_metal   = floor($pricelist[$Element]['metal']   * pow($pricelist[$Element]['factor'], $level));
			$cost_crystal = floor($pricelist[$Element]['crystal'] * pow($pricelist[$Element]['factor'], $level));
			$time         = ((($cost_crystal) + ($cost_metal)) / $game_config['game_speed']) * (1 / ($planet[$resource['14']] + 1)) * pow(0.5, $planet[$resource['15']]);
			$time         = floor(($time * 60 * 60) * (1 - (($user['rpg_constructeur']) * CONSTRUCTEUR)));
		}
		elseif (in_array($Element, $reslist['tech']))
		{
			$cost_metal   = floor($pricelist[$Element]['metal']   * pow($pricelist[$Element]['factor'], $level));
			$cost_crystal = floor($pricelist[$Element]['crystal'] * pow($pricelist[$Element]['factor'], $level));
			$intergal_lab = $user[$resource[123]];

			if($intergal_lab < 1)
				$lablevel = $planet[$resource['31']];
			else
			{
				$limite = $intergal_lab+1;
				$inves = doquery("SELECT laboratory FROM {{table}} WHERE id_owner='".intval($user['id'])."' ORDER BY laboratory DESC LIMIT ".$limite."", 'planets');
				$lablevel = 0;
				while ($row= mysql_fetch_array($inves))
				{
					$lablevel += $row['laboratory'];
				}
			}

			$time         = (($cost_metal + $cost_crystal) / $game_config['game_speed']) / (($lablevel + 1) * 2);
			$time         = floor(($time * 60 * 60) * (1 - (($user['rpg_scientifique']) * SCIENTIFIQUE)));
		}
		elseif (in_array($Element, $reslist['defense']))
		{
			$time         = (($pricelist[$Element]['metal'] + $pricelist[$Element]['crystal']) / $game_config['game_speed']) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
			$time         = floor(($time * 60 * 60) * (1 - ((($user['rpg_general']) * GENERAL) + (($user['rpg_defenseur']) * DEFENSEUR))));
		}
		elseif (in_array($Element, $reslist['fleet']))
		{
			$time         = (($pricelist[$Element]['metal'] + $pricelist[$Element]['crystal']) / $game_config['game_speed']) * (1 / ($planet[$resource['21']] + 1)) * pow(1 / 2, $planet[$resource['15']]);
			$time         = floor(($time * 60 * 60) * (1 - ((($user['rpg_general']) * GENERAL) + (($user['rpg_technocrate']) * TECHNOCRATE))));
		}

      if ($time < 0)
		{
			$time = 0;
		}

		return $time;
	}

?>