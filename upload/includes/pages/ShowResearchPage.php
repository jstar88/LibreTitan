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

class ShowResearchPage
{
	private function CheckLabSettingsInQueue ($CurrentPlanet)
	{
		if ($CurrentPlanet['b_building_id'] != 0)
		{
			$CurrentQueue = $CurrentPlanet['b_building_id'];
			if (strpos ($CurrentQueue, ";"))
			{
				// FIX BY LUCKY - IF THE LAB IS IN QUEUE THE USER CANT RESEARCH ANYTHING...
				$QueueArray		= explode (";", $CurrentQueue);

				for($i = 0; $i < MAX_BUILDING_QUEUE_SIZE; $i++)
				{
					$ListIDArray	= explode (",", $QueueArray[$i]);
					$Element		= $ListIDArray[0];

					if($Element == 31)
						break;
				}
				// END - FIX
			}
			else
			{
				$CurrentBuilding = $CurrentQueue;
			}

			if ($CurrentBuilding == 31 or $Element == 31) // ADDED (or $Element == 31) BY LUCKY
			{
				$return = false;
			}
			else
			{
				$return = true;
			}
		}
		else
		{
			$return = true;
		}

		return $return;
	}

	private function GetRestPrice ($user, $planet, $Element, $userfactor = true)
	{
		global $pricelist, $resource, $lang;

		if ($userfactor)
		{
			$level = ($planet[$resource[$Element]]) ? $planet[$resource[$Element]] : $user[$resource[$Element]];
		}

		$array = array(
		'metal'      => $lang['Metal'],
		'crystal'    => $lang['Crystal'],
		'deuterium'  => $lang['Deuterium'],
		'energy_max' => $lang['Energy']
		);

		$text  = "<br><font color=\"#7f7f7f\">" . $lang['bd_remaining'] . ": ";
		foreach ($array as $ResType => $ResTitle)
		{
			if ($pricelist[$Element][$ResType] != 0)
			{
				$text .= $ResTitle . ": ";
				if ($userfactor)
				{
					$cost = floor($pricelist[$Element][$ResType] * pow($pricelist[$Element]['factor'], $level));
				}
				else
				{
					$cost = floor($pricelist[$Element][$ResType]);
				}
				if ($cost > $planet[$ResType])
				{
					$text .= "<b style=\"color: rgb(127, 95, 96);\">". pretty_number($planet[$ResType] - $cost) ."</b> ";
				}
				else
				{
					$text .= "<b style=\"color: rgb(95, 127, 108);\">". pretty_number($planet[$ResType] - $cost) ."</b> ";
				}
			}
		}
		$text .= "</font>";

		return $text;
	}

    public function __construct (&$CurrentPlanet, $CurrentUser, $InResearch, $ThePlanet)
    {
        global $lang, $resource, $reslist, $phpEx, $dpath, $game_config, $_GET, $LegacyPlanet;

		include_once($xgp_root . 'includes/functions/IsTechnologieAccessible.' . $phpEx);
		include_once($xgp_root . 'includes/functions/GetElementPrice.' . $phpEx);

		$PageParse			= $lang;
		$NoResearchMessage 	= "";
		$bContinue         	= true;

		if ($CurrentPlanet[$resource[31]] == 0)
			message($lang['bd_lab_required'], '', '', true);

		if (!$this->CheckLabSettingsInQueue ($CurrentPlanet))
		{
			$NoResearchMessage = $lang['bd_building_lab'];
			$bContinue         = false;
		}

		if (isset($_GET['cmd']))
		{
			$TheCommand 	= $_GET['cmd'];
			$Techno     	= intval($_GET['tech']);

            if ( isset ($Techno) )
            {
                if (!strstr ( $Techno, ",") && !strchr ( $Techno, " ") &&
                    !strchr ( $Techno, "+") && !strchr ( $Techno, "*") &&
                    !strchr ( $Techno, "~") && !strchr ( $Techno, "=") &&
                    !strchr ( $Techno, ";") && !strchr ( $Techno, "'") &&
                    !strchr ( $Techno, "#") && !strchr ( $Techno, "-") &&
                    !strchr ( $Techno, "_") && !strchr ( $Techno, "[") &&
                    !strchr ( $Techno, "]") && !strchr ( $Techno, ".") &&
                    !strchr ( $Techno, ":"))
                {
                    if ( in_array($Techno, $reslist['tech']) )
                    {
                        if ($CurrentUser['b_tech_planet'] != $CurrentPlanet['id'] && !empty($CurrentUser['b_tech_planet'])  )
                        {
                            $WorkingPlanet = $ThePlanet;
                        }
                        else
                        {
                            $WorkingPlanet = $CurrentPlanet;
                        }

                        switch($TheCommand)
                        {
                            case 'cancel':
                                if ($ThePlanet['b_tech_id'] == $Techno)
                                {
                                    $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
                                    $WorkingPlanet['metal']      += $costs['metal'];
                                    $WorkingPlanet['crystal']    += $costs['crystal'];
                                    $WorkingPlanet['deuterium']  += $costs['deuterium'];
                                    $WorkingPlanet['b_tech_id']   = 0;
                                    $WorkingPlanet["b_tech"]      = 0;
                                    $CurrentUser['b_tech_planet'] = 0;
                                    $UpdateData                   = true;
                                    $InResearch                   = false;
                                }
                                break;
                            case 'search':
                                if (IsTechnologieAccessible($CurrentUser, $WorkingPlanet, $Techno) && IsElementBuyable($CurrentUser, $WorkingPlanet, $Techno))
                                {
                                    $costs                        = GetBuildingPrice($CurrentUser, $WorkingPlanet, $Techno);
                                    $WorkingPlanet['metal']      -= $costs['metal'];
                                    $WorkingPlanet['crystal']    -= $costs['crystal'];
                                    $WorkingPlanet['deuterium']  -= $costs['deuterium'];
                                    $WorkingPlanet["b_tech_id"]   = $Techno;
                                    $WorkingPlanet["b_tech"]      = time() + GetBuildingTime($CurrentUser, $WorkingPlanet, $Techno);
                                    $CurrentUser["b_tech_planet"] = $WorkingPlanet["id"];
                                    $UpdateData                   = true;
                                    $InResearch                   = true;
                                }
                                break;
                        }

                        if ($UpdateData == true)
                        {
                        	if ($CurrentUser['b_tech_planet'] != $CurrentPlanet['id'] && !empty($CurrentUser['b_tech_planet'])  )
                        	{
                            		$QryUpdatePlanet  = "UPDATE {{table}} SET ";
                            		$QryUpdatePlanet .= "`b_tech_id` = '".   $WorkingPlanet['b_tech_id']   ."', ";
                           		$QryUpdatePlanet .= "`b_tech` = '".      $WorkingPlanet['b_tech']      ."', ";
                            		$QryUpdatePlanet .= "`metal` = '".       $WorkingPlanet['metal']       ."', ";
                            		$QryUpdatePlanet .= "`crystal` = '".     $WorkingPlanet['crystal']     ."', ";
                            		$QryUpdatePlanet .= "`deuterium` = '".   $WorkingPlanet['deuterium']   ."' ";
                            		$QryUpdatePlanet .= "WHERE ";
                            		$QryUpdatePlanet .= "`id` = '".          $WorkingPlanet['id']          ."';";
                            		doquery( $QryUpdatePlanet, 'planets');
                        	}else{
                            		$LegacyPlanet['b_tech_id']	=	'b_tech_id';
                            		$LegacyPlanet['b_tech']		=	'b_tech';
                            		$CurrentPlanet['b_tech_id']	=	$WorkingPlanet['b_tech_id'];
                            		$CurrentPlanet['b_tech']	=	$WorkingPlanet['b_tech'];
                            		$CurrentPlanet['metal']		=	$WorkingPlanet['metal'] ;
                            		$CurrentPlanet['crystal']	=	$WorkingPlanet['crystal'];
                            		$CurrentPlanet['deuterium']	=	$WorkingPlanet['deuterium'];
                        	}


                            $QryUpdateUser  = "UPDATE {{table}} SET ";
                            $QryUpdateUser .= "`b_tech_planet` = '". $CurrentUser['b_tech_planet'] ."' ";
                            $QryUpdateUser .= "WHERE ";
                            $QryUpdateUser .= "`id` = '".            $CurrentUser['id']            ."';";
                            doquery( $QryUpdateUser, 'users');
                        }

                        //$CurrentPlanet = $WorkingPlanet;
                        if ($CurrentUser['b_tech_planet'] != $CurrentPlanet['id'] && !empty($CurrentUser['b_tech_planet'])  )
                        {
                            $ThePlanet     = $WorkingPlanet;
                        }
                        else
                        {
                            //$CurrentPlanet = $WorkingPlanet;
                            if ($TheCommand == 'search')
                            {
                                $ThePlanet = $CurrentPlanet;
                            }
                        }
                    }
                }
                else
                    die(header("location:game.php?page=buildings&mode=research"));
            }
            else
            {
                $bContinue = false;
            }

			header ("Location: game.php?page=buildings&mode=research");

		}

		$TechRowTPL = gettemplate('buildings/buildings_research_row');
		$TechScrTPL = gettemplate('buildings/buildings_research_script');

		foreach($lang['tech'] as $Tech => $TechName)
		{
			if ($Tech > 105 && $Tech <= 199)
			{
				if ( IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Tech))
				{
					$RowParse['dpath']       = $dpath;
					$RowParse['tech_id']     = $Tech;
					$building_level          = $CurrentUser[$resource[$Tech]];

					if($Tech == 106)
					{
						$RowParse['tech_level']  = ($building_level == 0 ) ? "" : "(". $lang['bd_lvl'] . " ".$building_level .")" ;
						$RowParse['tech_level']  .= ($CurrentUser['rpg_espion'] == 0) ? "" : "<strong><font color=\"lime\"> +" . ($CurrentUser['rpg_espion'] * ESPION) . $lang['bd_spy']	. "</font></strong>";
					}
					elseif($Tech == 108)
					{
						$RowParse['tech_level']  = ($building_level == 0) ? "" : "(". $lang['bd_lvl'] . " ".$building_level .")";
						$RowParse['tech_level']  .= ($CurrentUser['rpg_commandant'] == 0) ? "" : "<strong><font color=\"lime\"> +" . ($CurrentUser['rpg_commandant'] * COMMANDANT) . $lang['bd_commander'] . "</font></strong>";
					}
					else
						$RowParse['tech_level']  = ($building_level == 0) ? "" : "(". $lang['bd_lvl'] . " ".$building_level." )";

					$RowParse['tech_name']   = $TechName;
					$RowParse['tech_descr']  = $lang['res']['descriptions'][$Tech];
					$RowParse['tech_price']  = GetElementPrice($CurrentUser, $CurrentPlanet, $Tech);
					$SearchTime              = GetBuildingTime($CurrentUser, $CurrentPlanet, $Tech);
					$RowParse['search_time'] = ShowBuildTime($SearchTime);
					$RowParse['tech_restp']  = "Restantes ". $this->GetRestPrice ($CurrentUser, $CurrentPlanet, $Tech, true);
					$CanBeDone               = IsElementBuyable($CurrentUser, $CurrentPlanet, $Tech);

					if (!$InResearch)
					{
						$LevelToDo = 1 + $CurrentUser[$resource[$Tech]];
						if ($CanBeDone)
						{
							if (!$this->CheckLabSettingsInQueue ( $CurrentPlanet ))
							{
								if ($LevelToDo == 1)
									$TechnoLink  = "<font color=#FF0000>".$lang['bd_research']."</font>";
								else
									$TechnoLink  = "<font color=#FF0000>".$lang['bd_research']."<br>".$lang['bd_lvl']." ".$LevelToDo."</font>";

							}
							else
							{
								$TechnoLink  = "<a href=\"game.php?page=buildings&mode=research&cmd=search&tech=".$Tech."\">";
								if ($LevelToDo == 1)
									$TechnoLink .= "<font color=#00FF00>".$lang['bd_research']."</font>";
								else
									$TechnoLink .= "<font color=#00FF00>".$lang['bd_research']."<br>".$lang['bd_lvl']." ".$LevelToDo."</font>";

								$TechnoLink  .= "</a>";
							}
						}
						else
						{
							if ($LevelToDo == 1)
								$TechnoLink  = "<font color=#FF0000>".$lang['bd_research']."</font>";
							else
								$TechnoLink  = "<font color=#FF0000>".$lang['bd_research']."<br>".$lang['bd_lvl']." ".$LevelToDo."</font>";
						}
					}
					else
					{
						if ($ThePlanet["b_tech_id"] == $Tech)
						{
							$bloc       = $lang;
							if ($ThePlanet['id'] != $CurrentPlanet['id'])
							{
								$bloc['tech_time']  = $ThePlanet["b_tech"] - time();
								$bloc['tech_name']  = "de<br>". $ThePlanet["name"];
								$bloc['tech_home']  = $ThePlanet["id"];
								$bloc['tech_id']    = $ThePlanet["b_tech_id"];
							}
							else
							{
								$bloc['tech_time']  = $CurrentPlanet["b_tech"] - time();
								$bloc['tech_name']  = "";
								$bloc['tech_home']  = $CurrentPlanet["id"];
								$bloc['tech_id']    = $CurrentPlanet["b_tech_id"];
							}
							$TechnoLink  = parsetemplate($TechScrTPL, $bloc);
						}
						else
						{
							$TechnoLink  = "<center>-</center>";
						}
					}
					$RowParse['tech_link']  = $TechnoLink;
					$TechnoList            .= parsetemplate($TechRowTPL, $RowParse);
				}
			}
		}

		$PageParse['noresearch']  = $NoResearchMessage;
		$PageParse['technolist']  = $TechnoList;
		$Page                    .= parsetemplate(gettemplate('buildings/buildings_research'), $PageParse);

		display($Page);
	}
}