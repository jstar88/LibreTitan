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

	function formatCR (&$result_array,&$steal_array,&$moon_int,&$moon_string,&$time_float,$real_language)
	{

		$html 		= "";
		$bbc 		= "";
		$html 		.= $real_language['sys_attack_title']." ".date("D M j H:i:s", time()).".<br /><br />";
		$round_no 	= 1;
		$destroyed	= 0;

		foreach( $result_array['rw'] as $round => $data1)
		{
			if($round_no <= 6)
			{
				$html 		.= $real_language['sys_attack_round']." ".$round_no." :<br /><br />";
				$attackers1 = $data1['attackers'];
				$attackers2 = $data1['infoA'];
				$attackers3 = $data1['attackA'];
				$defenders1 = $data1['defenders'];
				$defenders2 = $data1['infoD'];
				$defenders3 = $data1['defenseA'];
				$coord4 	= 0;
				$coord5 	= 0;
				$coord6 	= 0;
				//start mod
				$coord7 	= 0;
				//end mod

				foreach( $attackers1 as $fleet_id1 => $data2)
				{
					$name 	= $data2['user']['username'];
					//start mod
					$coord0 = $data2['fleet']['fleet_start_universe'];
					//end mod
					$coord1 = $data2['fleet']['fleet_start_galaxy'];
					$coord2 = $data2['fleet']['fleet_start_system'];
					$coord3 = $data2['fleet']['fleet_start_planet'];
					$weap 	= ($data2['user']['military_tech'] * 10);
					$shie 	= ($data2['user']['defence_tech'] * 10);
					$armr 	= ($data2['user']['shield_tech'] * 10);
          //start mod
          if($coord7 == 0){$coord7 += $data2['fleet']['fleet_end_universe'];}
          //end mod
					if($coord4 == 0){$coord4 += $data2['fleet']['fleet_end_galaxy'];}
					if($coord5 == 0){$coord5 += $data2['fleet']['fleet_end_system'];}
					if($coord6 == 0){$coord6 += $data2['fleet']['fleet_end_planet'];}

					$fl_info1  	= "<table><tr><th>";
					//start mod
					$fl_info1 	.= $real_language['sys_attack_attacker_pos']." ".$name." ([".$coord0.":".$coord1.":".$coord2.":".$coord3."])<br />";
					//end mod
          $fl_info1 	.= $real_language['sys_ship_weapon']." ".$weap."% - ".$real_language['sys_ship_shield']." ".$shie."% - ".$real_language['sys_ship_armour']." ".$armr."%";
					$table1  	= "<table border=1 align=\"center\">";

					if (number_format($data1['attack']['total']) >= 0 && $round_no == 1)
					{
						if(number_format($data1['attack']['total']) == 0)
						{
							$ships1 = "<tr><br /><br />". $real_language['sys_destroyed']."<br /></tr>";
							$count1 = "";
							$destroyed = 1;
						}
						else
						{
							$destroyed = 0;
						}

						$ships1  = "<tr><th>".$real_language['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$real_language['sys_ship_count']."</th>";

						foreach( $data2['detail'] as $ship_id1 => $ship_count1)
						{
						   if ($ship_count1 > 0)
						   {
						       $ships1 .= "<th>[ship[".$ship_id1."]]</th>";
						       $count1 .= "<th>".number_format($ship_count1)."</th>";
						   }
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					elseif(number_format($data1['attack']['total']) > 0)
					{
						$ships1  = "<tr><th>".$real_language['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$real_language['sys_ship_count']."</th>";

						foreach( $data2['detail'] as $ship_id1 => $ship_count1)
						{
							if ($ship_count1 > 0)
							{
								$ships1 .= "<th>[ship[".$ship_id1."]]</th>";
								$count1 .= "<th>".number_format($ship_count1)."</th>";
							}
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					else
					{
						$ships1 = "<tr><br /><br />". $real_language['sys_destroyed']."<br /></tr>";
						$count1 = "";
					}

					$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
				}

				foreach( $attackers2 as $fleet_id2 => $data3)
				{
					$weap1  = "<tr><th>".$real_language['sys_ship_weapon']."</th>";
					$shields1  = "<tr><th>".$real_language['sys_ship_shield']."</th>";
					$armour1  = "<tr><th>".$real_language['sys_ship_armour']."</th>";

					foreach( $data3 as $ship_id2 => $ship_points1)
					{
						if ($ship_points1['shield'] > 0)
						{
						   $weap1 		.= "<th>".number_format($ship_points1['att'])."</th>";
						   $shields1 	.= "<th>".number_format($ship_points1['def'])."</th>";
						   $armour1 	.= "<th>".number_format($ship_points1['shield'])."</th>";
						}
					}

					$weap1 		.= "</tr>";
					$shields1 	.= "</tr>";
					$armour1 	.= "</tr>";
					$endtable1 	.= "</table></th></tr></table>";

					$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

					if (number_format($data1['attackA']['total']) > 0)
					{
						$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
						$html .= "<br /><br />";
					}
					else
					{
						$html .= $info_part1[$fleet_id2];
						$html .= "</table></th></tr></table><br /><br />";
					}
				}

				foreach( $defenders1 as $fleet_id1 => $data2)
				{
					$name = $data2['user']['username'];
					$weap = ($data2['user']['military_tech'] * 10);
					$shie = ($data2['user']['defence_tech'] * 10);
					$armr = ($data2['user']['shield_tech'] * 10);

					$fl_info1  = "<table><tr><th>";
					//start mod
					$fl_info1 .= $real_language['sys_attack_defender_pos']." ".$name." ([".$coord7.":".$coord4.":".$coord5.":".$coord6."])<br />";
					//end mod
          $fl_info1 .= $real_language['sys_ship_weapon']." ".$weap."% - ".$real_language['sys_ship_shield']." ".$shie."% - ".$real_language['sys_ship_armour']." ".$armr."%";

					$table1  = "<table border=1 align=\"center\">";

					if (number_format($data1['defenseA']['total']) > 0)
					{
						$ships1  = "<tr><th>".$real_language['sys_ship_type']."</th>";
						$count1  = "<tr><th>".$real_language['sys_ship_count']."</th>";

						foreach( $data2['def'] as $ship_id1 => $ship_count1)
						{
							if ($ship_count1 > 0)
							{
								$ships1 .= "<th>[ship[".$ship_id1."]]</th>";
								$count1 .= "<th>".number_format($ship_count1)."</th>";
							}
						}

						$ships1 .= "</tr>";
						$count1 .= "</tr>";
					}
					else
					{
						$ships1 = "<tr><br /><br />". $real_language['sys_destroyed']."<br /></tr>";
						$count1 = "";
					}

					$info_part1[$fleet_id1] = $fl_info1.$table1.$ships1.$count1;
				}

				foreach( $defenders2 as $fleet_id2 => $data3)
				{
					$weap1  	= "<tr><th>".$real_language['sys_ship_weapon']."</th>";
					$shields1  	= "<tr><th>".$real_language['sys_ship_shield']."</th>";
					$armour1  	= "<tr><th>".$real_language['sys_ship_armour']."</th>";

					foreach( $data3 as $ship_id2 => $ship_points1)
					{
						if ($ship_points1['shield'] > 0)
						{
							$weap1 .= "<th>".number_format($ship_points1['att'])."</th>";
							$shields1 .= "<th>".number_format($ship_points1['def'])."</th>";
							$armour1 .= "<th>".number_format($ship_points1['shield'])."</th>";
						}
					}

					$weap1 		.= "</tr>";
					$shields1 	.= "</tr>";
					$armour1 	.= "</tr>";
					$endtable1 	.= "</table></th></tr></table>";

					$info_part2[$fleet_id2] = $weap1.$shields1.$armour1.$endtable1;

					if (number_format($data1['defenseA']['total']) > 0)
					{
						$html .= $info_part1[$fleet_id2].$info_part2[$fleet_id2];
						$html .= "<br /><br />";
					}
					else
					{
						$html .= $info_part1[$fleet_id2];
						$html .= "</table></th></tr></table><br /><br />";
					}
				}
				$html .=  $real_language['fleet_attack_1']." ".number_format($data1['attack']['total'])." ".$real_language['fleet_attack_2']." ".number_format($data1['defShield'], 0, ' ', ' ')." ".$real_language['damage']."<br />";
				$html .= $real_language['fleet_defs_1']." ".number_format($data1['defense']['total'])." ".$real_language['fleet_defs_2']." ".number_format($data1['attackShield'], 0, ' ', ' ')." ".$real_language['damage']."<br /><br />";
				$round_no++;
			}
		}

		if ($result_array['won'] == "r")
		{
			$result1  = $real_language['sys_defender_won']."<br />";
		}
		elseif ($result_array['won'] == "a")
		{
			$result1  = $real_language['sys_attacker_won']."<br />";
			$result1 .= $real_language['sys_stealed_ressources']." ".$steal_array['metal']." ".$real_language['Metal'].", ".$steal_array['crystal']." ".$real_language['Crystal']." ".$real_language['and']." ".$steal_array['deuterium']." ".$real_language['Deuterium']."<br />";
		}
		else
		{
			$result1  = $real_language['sys_both_won'].".<br />";
		}

		$html .= "<br /><br />";
		$html .= $result1;
		$html .= "<br />";

		$debirs_meta = ($result_array['debree']['att'][0] + $result_array['debree']['def'][0]);
		$debirs_crys = ($result_array['debree']['att'][1] + $result_array['debree']['def'][1]);

		$html .= $real_language['sys_attacker_lostunits']." ".$result_array['lost']['att']." ".$real_language['sys_units']."<br />";
		$html .= $real_language['sys_defender_lostunits']." ".$result_array['lost']['def']." ".$real_language['sys_units']."<br />";
		$html .= $real_language['debree_field_1']." ".$debirs_meta." ".$real_language['Metal']." ".$real_language['sys_and']." ".$debirs_crys." ".$real_language['Crystal']." ".$real_language['debree_field_2']."<br /><br />";
		$html .= $real_language['sys_moonproba']." ".floor($moon_int)." %<br />";
		$html .= $moon_string."<br /><br />";

		return array('html' => $html, 'bbc' => $bbc, 'destroyed' => $destroyed);
	}
?>