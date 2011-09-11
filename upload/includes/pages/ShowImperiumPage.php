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

function ShowImperiumPage($CurrentUser)
{
	global $lang, $resource, $reslist, $dpath;

	$planetsrow = doquery("
	SELECT `id`,`name`,`universe`,`galaxy`,`system`,`planet`,`planet_type`,
	`image`,`field_current`,`field_max`,`metal`,`metal_perhour`,
	`crystal`,`crystal_perhour`,`deuterium`,`deuterium_perhour`,
	`energy_used`,`energy_max`,`metal_mine`,`crystal_mine`,`deuterium_sintetizer`,
	`solar_plant`,`fusion_plant`,`robot_factory`,`nano_factory`,`hangar`,`metal_store`,
	`crystal_store`,`deuterium_store`,`laboratory`,`terraformer`,`ally_deposit`,`silo`,
	`small_ship_cargo`,`big_ship_cargo`,`light_hunter`,`heavy_hunter`,`crusher`,`battle_ship`,
	`colonizer`,`recycler`,`spy_sonde`,`bomber_ship`,`solar_satelit`,`destructor`,`dearth_star`,
	`battleship`,`supernova`,`misil_launcher`,`small_laser`,`big_laser`,`gauss_canyon`,`ionic_canyon`,
	`buster_canyon`,`small_protection_shield`,`planet_protector`,`big_protection_shield`,`interceptor_misil`,
	`interplanetary_misil`, `mondbasis`, `phalanx`, `sprungtor` FROM {{table}} WHERE `id_owner` = '" . intval($CurrentUser['id']) . "' AND `destruyed` = 0;", 'planets');

	$parse 	= $lang;
	$planet = array();

	while ($p = mysql_fetch_array($planetsrow))
	{
		$planet[] = $p;
	}

	$parse['mount'] = count($planet) + 1;

	foreach ($planet as $p)
	{
	  //start mod
		$datat = array('<a href="game.php?page=overview&cp=' . $p['id'] . '&amp;re=0"><img src="' . $dpath . 'planeten/small/s_' . $p['image'] . '.jpg" border="0" height="80" width="80"></a>', $p['name'], "[<a href=\"game.php?page=galaxy&mode=3&universe={$p['universe']}&galaxy={$p['galaxy']}&system={$p['system']}\">{$p['universe']}:{$p['galaxy']}:{$p['system']}:{$p['planet']}</a>]", $p['field_current'] . '/' . $p['field_max'], '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['metal']) . '</a> / ' . pretty_number($p['metal_perhour']), '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['crystal']) . '</a> / ' . pretty_number($p['crystal_perhour']), '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['deuterium']) . '</a> / ' . pretty_number($p['deuterium_perhour']), pretty_number($p['energy_max'] - $p['energy_used']) . ' / ' . pretty_number($p['energy_max']));	
	//end mod
  	$f = array('file_images', 'file_names', 'file_coordinates', 'file_fields', 'file_metal', 'file_crystal', 'file_deuterium', 'file_energy');
		for ($k = 0; $k < 8; $k++)
		{
			$data['text'] = $datat[$k];
			$parse[$f[$k]] .= parsetemplate(gettemplate('empire/empire_row'), $data);
		}

		foreach ($resource as $i => $res)
		{
			$data['text'] = ($p[$resource[$i]] == 0 && $CurrentUser[$resource[$i]] == 0) ? '-' : ((in_array($i, $reslist['build'])) ? "<a href=\"game.php?page=buildings&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ((in_array($i, $reslist['tech'])) ? "<a href=\"game.php?page=buildings&mode=research&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$CurrentUser[$resource[$i]]}</a>" : ((in_array($i, $reslist['fleet'])) ? "<a href=\"game.php?page=buildings&mode=fleet&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ((in_array($i, $reslist['defense'])) ? "<a href=\"game.php?page=buildings&mode=defense&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : '-'))));
			$r[$i] .= parsetemplate(gettemplate('empire/empire_row'), $data);
		}
	}

	$m = array('build', 'tech', 'fleet', 'defense');

	$n = array('building_row', 'technology_row', 'fleet_row', 'defense_row');

	for ($j = 0; $j < 4; $j++)
	{
		foreach ($reslist[$m[$j]] as $a => $i)
		{
			$data['text'] = $lang['tech'][$i];
			$parse[$n[$j]] .= "<tr>" . parsetemplate(gettemplate('empire/empire_row'), $data) . $r[$i] . "</tr>";
		}
	}

	return display(parsetemplate(gettemplate('empire/empire_table'), $parse), false);
}
?>