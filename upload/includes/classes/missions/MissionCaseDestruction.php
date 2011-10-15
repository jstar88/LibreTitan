<?php
class MissionCaseDestruction extends FlyingFleetHandler
{
   public function MissionCaseDestruction($FleetRow)
	{
		global $user, $phpEx, $pricelist, $lang, $resource, $CombatCaps;

		if ($FleetRow['fleet_start_time'] <= time())
		{
			if ($FleetRow['fleet_mess'] == 0)
			{
				if (!isset($CombatCaps[202]['sd']))
					header("location:game." . $phpEx . "?page=fleet");

				$DepPlanet      = $this->getStartPlanetInfoInCache($FleetRow);
		      $DepName       = $DepPlanet['name'];

		      $TargetPlanet     = $this->getTargetPlanetInfoInCache($FleetRow);
		      $TargetUserID     = $TargetPlanet['id_owner'];       

				$CurrentUser      = $this->getStartUser($FleetRow);
				$CurrentUserID    = $CurrentUser['id'];

				$TargetUser       = $this->getTargetUser($FleetRow);

				for ($SetItem = 200; $SetItem < 500; $SetItem++)
				{
					if ($TargetPlanet[$resource[$SetItem]] > 0)
						$TargetSet[$SetItem]['count'] = $TargetPlanet[$resource[$SetItem]];
				}

				$TheFleet = explode(";", $FleetRow['fleet_array']);

				foreach($TheFleet as $a => $b)
				{
					if ($b != '')
					{
						$a = explode(",", $b);
						$CurrentSet[$a[0]]['count'] = $a[1];
					}
				}


				$walka        = $this->walka($CurrentSet, $TargetSet, $CurrentUser, $TargetUser);
				$CurrentSet   = $walka["atakujacy"];
				$TargetSet    = $walka["wrog"];
				$FleetResult  = $walka["wygrana"];
				$dane_do_rw   = $walka["dane_do_rw"];
				$zlom         = $walka["zlom"];
				$FleetArray   = "";
				$FleetAmount  = 0;
				$FleetStorage = 0;

				foreach ($CurrentSet as $Ship => $Count)
				{

					$FleetStorage += $pricelist[$Ship]["capacity"] * $Count['count'];
					$FleetArray   .= $Ship.",".$Count['count'].";";
					$FleetAmount  += $Count['count'];
				}

				$TargetPlanetUpd = "";

				if (!is_null($TargetSet))
				{
					foreach($TargetSet as $Ship => $Count)
					{
						$TargetPlanetUpd .= "`". $resource[$Ship] ."` = '". $Count['count'] ."', ";
					}
				}

				if ($FleetResult == "a")
				{
					$destructionl1 	= 100-sqrt($TargetPlanet['diameter']);
					$destructionl21 = $destructionl1*sqrt($CurrentSet['214']['count']);
					$destructionl2 	= $destructionl21/1;

					if ($destructionl2 > 100)
						$chance = '100';
					else
						$chance = round($destructionl2);

					$tirage 	= mt_rand(0, 100);
					$probalune	= sprintf ($lang['sys_destruc_lune'], $chance);

					if($tirage <= $chance)
					{
						$resultat 	= '1';
						$finmess 	= $lang['sys_destruc_reussi'];

						doquery("DELETE FROM {{table}} WHERE `id` = '". $TargetPlanet['id'] ."';", 'planets');

						$Qrydestructionlune  = "UPDATE {{table}} SET ";
						$Qrydestructionlune .= "`id_luna` = '0' ";
						$Qrydestructionlune .= "WHERE ";
						$Qrydestructionlune .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
						$Qrydestructionlune .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
						$Qrydestructionlune .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
						$Qrydestructionlune .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' ";
						$Qrydestructionlune .= "LIMIT 1 ;";
						doquery( $Qrydestructionlune , 'galaxy');

						$QryDetFleets1  = "UPDATE {{table}} SET ";
						$QryDetFleets1 .= "`fleet_start_type` = '1' ";
						$QryDetFleets1 .= "WHERE ";
						$QryDetFleets1 .= "`fleet_start_universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
						$QryDetFleets1 .= "`fleet_start_galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
						$QryDetFleets1 .= "`fleet_start_system` = '". $FleetRow['fleet_end_system'] ."' AND ";
						$QryDetFleets1 .= "`fleet_start_planet` = '". $FleetRow['fleet_end_planet'] ."' ";
						$QryDetFleets1 .= ";";
						doquery( $QryDetFleets1 , 'fleets');

						$QryDetFleets2  = "UPDATE {{table}} SET ";
						$QryDetFleets2 .= "`fleet_end_type` = '1' ";
						$QryDetFleets2 .= "WHERE ";
						$QryDetFleets2 .= "`fleet_end_universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
						$QryDetFleets2 .= "`fleet_end_galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
						$QryDetFleets2 .= "`fleet_end_system` = '". $FleetRow['fleet_end_system'] ."' AND ";
						$QryDetFleets2 .= "`fleet_end_planet` = '". $FleetRow['fleet_end_planet'] ."' ";
						$QryDetFleets2 .= ";";
						doquery( $QryDetFleets2 , 'fleets');

						if ($TargetUser['current_planet'] == $TargetPlanet['id'])
						{
							$QryPlanet  = "SELECT * FROM {{table}} ";
							$QryPlanet .= "WHERE ";
							$QryPlanet .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
							$QryPlanet .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
							$QryPlanet .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
							$QryPlanet .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
							$QryPlanet .= "`planet_type` = '1';";
							$Planet     = doquery( $QryPlanet, 'planets', true);
							$IDPlanet     = $Planet['id'];

							$Qryvue  = "UPDATE {{table}} SET ";
							$Qryvue .= "`current_planet` = '". $IDPlanet ."' ";
							$Qryvue .= "WHERE ";
							$Qryvue .= "`id` = '". $TargetUserID ."' ";
							$Qryvue .= ";";
							doquery( $Qryvue , 'users');
						}
					}
					else
						$resultat = '0';

					$destructionrip = sqrt($TargetPlanet['diameter'])/2;
					$chance2		= round($destructionrip);

					if ($resultat == 0)
					{
						$tirage2 	= mt_rand(0, 100);
						$probarip	= sprintf ($lang['sys_destruc_rip'], $chance2);
						if($tirage2 <= $chance2)
						{
							$resultat2 = ' detruite 1';
							$finmess = $lang['sys_destruc_echec'];
							doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
						}
						else
						{
							$resultat2 = 'sauvees 0';
							$finmess = $lang['sys_destruc_null'];
						}
					}
				}

				$introdestruc       = sprintf ($lang['sys_destruc_mess'], $DepName ,$FleetRow['fleet_start_universe'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);

				$QryUpdateTarget  = "UPDATE {{table}} SET ";
				$QryUpdateTarget .= $TargetPlanetUpd;
				$QryUpdateTarget .= "`metal` = `metal` - '". $Mining['metal'] ."', ";
				$QryUpdateTarget .= "`crystal` = `crystal` - '". $Mining['crystal'] ."', ";
				$QryUpdateTarget .= "`deuterium` = `deuterium` - '". $Mining['deuter'] ."' ";
				$QryUpdateTarget .= "WHERE ";
				//start mod
				$QryUpdateTarget .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
				//end mod
				$QryUpdateTarget .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
				$QryUpdateTarget .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
				$QryUpdateTarget .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' AND ";
				$QryUpdateTarget .= "`planet_type` = '". $FleetRow['fleet_end_type'] ."' ";
				$QryUpdateTarget .= "LIMIT 1;";
				doquery( $QryUpdateTarget , 'planets');

				$QryUpdateGalaxy  = "UPDATE {{table}} SET ";
				$QryUpdateGalaxy .= "`metal` = `metal` + '". $zlom['metal'] ."', ";
				$QryUpdateGalaxy .= "`crystal` = `crystal` + '". $zlom['crystal'] ."' ";
				$QryUpdateGalaxy .= "WHERE ";
				//start mod
				$QryUpdateTarget .= "`universe` = '". $FleetRow['fleet_end_universe'] ."' AND ";
				//end mod
				$QryUpdateGalaxy .= "`galaxy` = '". $FleetRow['fleet_end_galaxy'] ."' AND ";
				$QryUpdateGalaxy .= "`system` = '". $FleetRow['fleet_end_system'] ."' AND ";
				$QryUpdateGalaxy .= "`planet` = '". $FleetRow['fleet_end_planet'] ."' ";
				$QryUpdateGalaxy .= "LIMIT 1;";
				doquery( $QryUpdateGalaxy , 'galaxy');

				$FleetDebris      = $zlom['metal'] + $zlom['crystal'];
				$StrAttackerUnits = sprintf ($lang['sys_attacker_lostunits'], $zlom["atakujacy"]);
				$StrDefenderUnits = sprintf ($lang['sys_defender_lostunits'], $zlom["wrog"]);
				$StrRuins         = sprintf ($lang['sys_gcdrunits'], $zlom["metal"], $lang['Metal'], $zlom['crystal'], $lang['Crystal']);
				$DebrisField      = $StrAttackerUnits ."<br />". $StrDefenderUnits ."<br />". $StrRuins;
				$MoonChance       = $FleetDebris / 100000;

				if ($FleetDebris > 2000000)
				{
					$MoonChance = 20;
					$ChanceMoon = sprintf ($lang['sys_moonproba'], $MoonChance);
				}
				elseif ($FleetDebris < 100000)
				{
					$UserChance = 0;
					$ChanceMoon = sprintf ($lang['sys_moonproba'], $MoonChance);
				}
				elseif ($FleetDebris >= 100000)
				{
					$UserChance = mt_rand(1, 100);
					$ChanceMoon = sprintf ($lang['sys_moonproba'], $MoonChance);
				}

				if (($UserChance > 0) and ($UserChance <= $MoonChance) and $galenemyrow['id_luna'] == 0)
				{
					$TargetPlanetName = CreateOneMoonRecord ( $FleetRow['fleet_end_universe'],$FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'], $TargetUserID, $FleetRow['fleet_start_time'], '', $MoonChance );
					$GottenMoon       = sprintf ($lang['sys_moonbuilt'], $TargetPlanetName, $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet']);
				}
				elseif ($UserChance = 0 or $UserChance > $MoonChance)
					$GottenMoon = "";

				$AttackDate        = date("r", $FleetRow["fleet_start_time"]);
				$title             = sprintf ($lang['sys_destruc_title'], $AttackDate);
				$raport            = "<center><table><tr><td>". $title ."<br />";
				$zniszczony        = false;
				$a_zestrzelona     = 0;
				$AttackTechon['A'] = $CurrentUser["military_tech"] * 10;
				$AttackTechon['B'] = $CurrentUser["defence_tech"] * 10;
				$AttackTechon['C'] = $CurrentUser["shield_tech"] * 10;
				$AttackerData      = sprintf ($lang['sys_attack_attacker_pos'], $CurrentUser["username"],$FleetRow['fleet_start_universe'], $FleetRow['fleet_start_galaxy'], $FleetRow['fleet_start_system'], $FleetRow['fleet_start_planet'] );
				$AttackerTech      = sprintf ($lang['sys_attack_techologies'], $AttackTechon['A'], $AttackTechon['B'], $AttackTechon['C']);
				$DefendTechon['A'] = $TargetUser["military_tech"] * 10;
				$DefendTechon['B'] = $TargetUser["defence_tech"] * 10;
				$DefendTechon['C'] = $TargetUser["shield_tech"] * 10;
				$DefenderData      = sprintf ($lang['sys_attack_defender_pos'], $TargetUser["username"],$FleetRow['fleet_end_universe'], $FleetRow['fleet_end_galaxy'], $FleetRow['fleet_end_system'], $FleetRow['fleet_end_planet'] );
				$DefenderTech      = sprintf ($lang['sys_attack_techologies'], $DefendTechon['A'], $DefendTechon['B'], $DefendTechon['C']);

				foreach ($dane_do_rw as $a => $b)
				{
					$raport .= "<table border=1 width=100%><tr><th><br /><center>".$AttackerData."<br />".$AttackerTech."<table border=1>";

					if ($b["atakujacy"]['count'] > 0)
					{
						$raport1 = "<tr><th>".$lang['sys_ship_type']."</th>";
						$raport2 = "<tr><th>".$lang['sys_ship_count']."</th>";
						$raport3 = "<tr><th>".$lang['sys_ship_weapon']."</th>";
						$raport4 = "<tr><th>".$lang['sys_ship_shield']."</th>";
						$raport5 = "<tr><th>".$lang['sys_ship_armour']."</th>";

						foreach ($b["atakujacy"] as $Ship => $Data)
						{
							if (is_numeric($Ship))
							{
								if ($Data['count'] > 0)
								{
									$raport1 .= "<th>". $lang["tech_rc"][$Ship] ."</th>";
									$raport2 .= "<th>". $Data['count'] ."</th>";
									$raport3 .= "<th>". round($Data["atak"]   / $Data['count']) ."</th>";
									$raport4 .= "<th>". round($Data["tarcza"] / $Data['count']) ."</th>";
									$raport5 .= "<th>". round($Data["obrona"] / $Data['count']) ."</th>";
								}
							}
						}

						$raport1 .= "</tr>";
						$raport2 .= "</tr>";
						$raport3 .= "</tr>";
						$raport4 .= "</tr>";
						$raport5 .= "</tr>";
						$raport  .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;

					}
					else
					{
						if ($a == 2)
							$a_zestrzelona = 1;

						$zniszczony = true;
						$raport .= "<br />". $lang['sys_destroyed'];
					}

					$raport .= "</table></center></th></tr></table>";
					$raport .= "<table border=1 width=100%><tr><th><br /><center>".$DefenderData."<br />".$DefenderTech."<table border=1>";

					if ($b["wrog"]['count'] > 0)
					{
						$raport1 = "<tr><th>".$lang['sys_ship_type']."</th>";
						$raport2 = "<tr><th>".$lang['sys_ship_count']."</th>";
						$raport3 = "<tr><th>".$lang['sys_ship_weapon']."</th>";
						$raport4 = "<tr><th>".$lang['sys_ship_shield']."</th>";
						$raport5 = "<tr><th>".$lang['sys_ship_armour']."</th>";

						foreach ($b["wrog"] as $Ship => $Data)
						{
							if (is_numeric($Ship))
							{
								if ($Data['count'] > 0)
								{
									$raport1 .= "<th>". $lang["tech_rc"][$Ship] ."</th>";
									$raport2 .= "<th>". $Data['count'] ."</th>";
									$raport3 .= "<th>". round($Data["atak"]   / $Data['count']) ."</th>";
									$raport4 .= "<th>". round($Data["tarcza"] / $Data['count']) ."</th>";
									$raport5 .= "<th>". round($Data["obrona"] / $Data['count']) ."</th>";
								}
							}
						}

						$raport1 .= "</tr>";
						$raport2 .= "</tr>";
						$raport3 .= "</tr>";
						$raport4 .= "</tr>";
						$raport5 .= "</tr>";
						$raport  .= $raport1 . $raport2 . $raport3 . $raport4 . $raport5;

					}
					else
					{
						$zniszczony = true;
						$raport .= "<br />". $lang['sys_destroyed'];
					}

					$raport .= "</table></center></th></tr></table>";



					if (($zniszczony == false) and !($a == 8))
					{
						$AttackWaveStat    = sprintf ($lang['sys_attack_attack_wave'], floor($b["atakujacy"]["atak"]), floor($b["wrog"]["tarcza"]));
						$DefendWavaStat    = sprintf ($lang['sys_attack_defend_wave'], floor($b["wrog"]["atak"]), floor($b["atakujacy"]["tarcza"]));
						$raport           .= "<br /><center>".$AttackWaveStat."<br />".$DefendWavaStat."</center>";
					}
				}

				switch ($FleetResult)
				{
					case "a":
						$raport           .= $lang['sys_attacker_won'] ."<br />";
						$raport           .= $DebrisField ."<br />";
						$raport           .= $introdestruc ."<br />";
						$raport           .= $lang['sys_destruc_mess1'];
						$raport           .= $finmess ."<br />";
						$raport           .= $probalune ."<br />";
						$raport           .= $probarip ."<br />";
						break;

					case "r":
						$raport           .= $lang['sys_both_won'] ."<br />";
						$raport           .= $DebrisField ."<br />";
						$raport           .= $introdestruc ."<br />";
						$raport           .= $lang['sys_destruc_stop'] ."<br />";
						break;

					case "w":
						$raport           .= $lang['sys_defender_won'] ."<br />";
						$raport           .= $DebrisField ."<br />";
						$raport           .= $introdestruc ."<br />";
						$raport           .= $lang['sys_destruc_stop'] ."<br />";
						doquery("DELETE FROM {{table}} WHERE `fleet_id` = '". $FleetRow["fleet_id"] ."';", 'fleets');
						break;
				}

				$raport           .= "</table>";
				$rid   			   = md5($raport);

				$QryInsertRapport  = "INSERT INTO {{table}} SET ";
				$QryInsertRapport .= "`time` = UNIX_TIMESTAMP(), ";
				$QryInsertRapport .= "`id_owner1` = '". $FleetRow['fleet_owner'] ."', ";
				$QryInsertRapport .= "`id_owner2` = '". $TargetUserID ."', ";
				$QryInsertRapport .= "`rid` = '". $rid ."', ";
				$QryInsertRapport .= "`a_zestrzelona` = '". $a_zestrzelona ."', ";
				$QryInsertRapport .= "`raport` = '". addslashes ( $raport ) ."';";
				doquery( $QryInsertRapport , 'rw');

				$raport  = "<a href # OnClick=\"f( 'CombatReport.php?raport=". $rid ."', '');\" >";
				$raport .= "<center>";

				if($FleetResult == "a")
					$raport .= "<font color=\"green\">";
				elseif ($FleetResult == "r")
					$raport .= "<font color=\"orange\">";
				elseif ($FleetResult == "w")
					$raport .= "<font color=\"red\">";

				$raport .= $lang['sys_mess_destruc_report'] ." [".$FleetRow['fleet_end_universe'] .":". $FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."] </font></a><br /><br />";
				$raport .= "<font color=\"red\">". $lang['sys_perte_attaquant'] .": ". $zlom["atakujacy"] ."</font>";
				$raport .= "<font color=\"green\">   ". $lang['sys_perte_defenseur'] .":". $zlom["wrog"] ."</font><br />" ;
				$raport .= $lang['sys_debris'] ." ". $lang['Metal'] .":<font color=\"#adaead\">". $zlom['metal'] ."</font>   ". $lang['Crystal'] .":<font color=\"#ef51ef\">". $zlom['crystal'] ."</font><br /></center>";

				$QryUpdateFleet  = "UPDATE {{table}} SET ";
				$QryUpdateFleet .= "`fleet_amount` = '". $FleetAmount ."', ";
				$QryUpdateFleet .= "`fleet_array` = '". $FleetArray ."', ";
				$QryUpdateFleet .= "`fleet_mess` = '1' ";
				$QryUpdateFleet .= "WHERE fleet_id = '". intval($FleetRow['fleet_id']) ."' ";
				$QryUpdateFleet .= "LIMIT 1 ;";
				doquery( $QryUpdateFleet , 'fleets');

				SendSimpleMessage ( $CurrentUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_destruc_report'], $raport );

				$raport2  = "<a href # OnClick=\"f( 'CombatReport.php?raport=". $rid ."', '');\" >";
				$raport2 .= "<center>";

				if($FleetResult == "a")
					$raport2 .= "<font color=\"red\">";
				elseif ($FleetResult == "r")
					$raport2 .= "<font color=\"orange\">";
				elseif ($FleetResult == "w")
					$raport2 .= "<font color=\"green\">";

				$raport2 .= $lang['sys_mess_destruc_report'] ." [". $FleetRow['fleet_end_universe'] .":".$FleetRow['fleet_end_galaxy'] .":". $FleetRow['fleet_end_system'] .":". $FleetRow['fleet_end_planet'] ."] </font></a><br /><br />";

				SendSimpleMessage ( $TargetUserID, '', $FleetRow['fleet_start_time'], 3, $lang['sys_mess_tower'], $lang['sys_mess_destruc_report'], $raport2 );
			}

			$fquery = "";

			if ($FleetRow['fleet_end_time'] <= time())
			{
				if (!is_null($CurrentSet))
				{
					foreach($CurrentSet as $Ship => $Count)
					{
						$fquery .= "`". $resource[$Ship] ."` = `". $resource[$Ship] ."` + '". $Count['count'] ."', ";
					}
				}
				else
				{
					$fleet = explode(";", $FleetRow['fleet_array']);
					foreach($fleet as $a => $b)
					{
						if ($b != '')
						{
							$a = explode(",", $b);
							$fquery .= "{$resource[$a[0]]}={$resource[$a[0]]} + {$a[1]}, \n";
						}
					}
				}

				doquery ("DELETE FROM {{table}} WHERE `fleet_id` = " . $FleetRow["fleet_id"], 'fleets');

				if (!($FleetResult == "w"))
				{
					$QryUpdatePlanet  = "UPDATE {{table}} SET ";
					$QryUpdatePlanet .= $fquery;
					$QryUpdatePlanet .= "`metal` = `metal` + ". $FleetRow['fleet_resource_metal'] .", ";
					$QryUpdatePlanet .= "`crystal` = `crystal` + ". $FleetRow['fleet_resource_crystal'] .", ";
					$QryUpdatePlanet .= "`deuterium` = `deuterium` + ". $FleetRow['fleet_resource_deuterium'] ." ";
					$QryUpdatePlanet .= "WHERE ";
					//start mod
					$QryUpdatePlanet .= "`universe` = ".$FleetRow['fleet_start_universe']." AND ";
					//end mod
					$QryUpdatePlanet .= "`galaxy` = ".$FleetRow['fleet_start_galaxy']." AND ";
					$QryUpdatePlanet .= "`system` = ".$FleetRow['fleet_start_system']." AND ";
					$QryUpdatePlanet .= "`planet` = ".$FleetRow['fleet_start_planet']." AND ";
					$QryUpdatePlanet .= "`planet_type` = ".$FleetRow['fleet_start_type']." LIMIT 1 ;";
					doquery( $QryUpdatePlanet, 'planets' );
				}
			}
		}
	}
	}
?>
