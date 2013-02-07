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

class ShowShipyardPage
{
	private function GetMaxConstructibleElements ($Element, $Ressources)
	{
		global $pricelist;

		if ($pricelist[$Element]['metal'] != 0)
		{
			$Buildable        = floor($Ressources["metal"] / $pricelist[$Element]['metal']);
			$MaxElements      = $Buildable;
		}

		if ($pricelist[$Element]['crystal'] != 0)
			$Buildable        = floor($Ressources["crystal"] / $pricelist[$Element]['crystal']);

		if (!isset($MaxElements))
			$MaxElements      = $Buildable;
		elseif($MaxElements > $Buildable)
			$MaxElements      = $Buildable;

		if ($pricelist[$Element]['deuterium'] != 0)
			$Buildable        = floor($Ressources["deuterium"] / $pricelist[$Element]['deuterium']);

		if (!isset($MaxElements))
			$MaxElements      = $Buildable;
		elseif ($MaxElements > $Buildable)
			$MaxElements      = $Buildable;

		if ($pricelist[$Element]['energy'] != 0)
			$Buildable        = floor($Ressources["energy_max"] / $pricelist[$Element]['energy']);

		if ($Buildable < 1)
			$MaxElements      = 0;

		return $MaxElements;
	}

	private function GetElementRessources($Element, $Count)
	{
		global $pricelist;

		$ResType['metal']     = ($pricelist[$Element]['metal']     * $Count);
		$ResType['crystal']   = ($pricelist[$Element]['crystal']   * $Count);
		$ResType['deuterium'] = ($pricelist[$Element]['deuterium'] * $Count);

		return $ResType;
	}

	private function ElementBuildListBox ( $CurrentUser, &$CurrentPlanet, $Work_place )
	{
		global $lang, $pricelist,$xgp_root;

			$ElementQueue = explode(';', $CurrentPlanet['b_hangar_id']);
		$NbrePerType  = "";
		$NamePerType  = "";
		$TimePerType  = "";

		foreach($ElementQueue as $ElementLine => $Element)
		{ 
			if ($Element != '')
			{  //start mod
				$Element 		= explode(',', $Element);
				$ElementTime  	= GetBuildingTime( $CurrentUser, $CurrentPlanet, $Element[0] )-$CurrentPlanet['b_hangar'];
				$QueueTime   	+= $ElementTime * $Element[1];
				$TimePerType .= '"'.$ElementTime.'",';
				$NamePerType .= '"'.html_entity_decode($lang['tech'][$Element[0]]).'",';
				$NumberPerType .= '"'.$Element[1].'",';
			}
		}
	  
		$TimePerType=    substr($TimePerType,0,$TimePerType.length-1);
		$NamePerType=    substr($NamePerType,0,	$NamePerType.length-1);
		$NumberPerType=  substr($NumberPerType,0,$NumberPerType.length-1);

		$parse 							= $lang;
		$parse['b_hangar_id_plus'] 		= $CurrentPlanet['b_hangar'];
		$parse['pretty_time_b_hangar'] 	= pretty_time($QueueTime - $CurrentPlanet['b_hangar']);
     
    $type="hangar";
    
    include_once($xgp_root . 'includes/functions/InsertJavaScriptChronoAppletJstar2.php');
    
    $parse['type'] = $type; 
    
    
    $text  =InsertJavaScriptChronoAppletJstar2($type, $NamePerType, $NumberPerType, $TimePerType, true);
    
    
    
    $text .= parsetemplate(gettemplate('buildings/buildings_script'), $parse);
    $text .=InsertJavaScriptChronoAppletJstar2($type, $NamePerType, $NumberPerType, $TimePerType, false);
      //end mod by Jstar
		return $text;
	}

	public function FleetBuildingPage ( &$CurrentPlanet, $CurrentUser )
	{
		global $lang, $resource, $phpEx, $dpath, $pricelist, $CombatCaps, $xgp_root, $LegacyPlanet;

		include_once($xgp_root . 'includes/functions/IsTechnologieAccessible.' . $phpEx);
		include_once($xgp_root . 'includes/functions/GetElementPrice.' . $phpEx);

		$parse = $lang;

		if (isset($_POST['fmenge']))
		{
			$AddedInQueue = false;

			foreach($_POST['fmenge'] as $Element => $Count)
			{
			   if($Element < 200 OR $Element > 300)
				{
					continue;
				}
				$Element = intval($Element);
				$Count   = intval($Count);
				if ($Count > MAX_FLEET_OR_DEFS_PER_ROW)
				{
					$Count = MAX_FLEET_OR_DEFS_PER_ROW;
				}

				if ($Count != 0)
				{
					if ( IsTechnologieAccessible ($CurrentUser, $CurrentPlanet, $Element) )
					{
						$MaxElements   = $this->GetMaxConstructibleElements ( $Element, $CurrentPlanet );

						if ($Count > $MaxElements)
							$Count = $MaxElements;

						$Ressource = $this->GetElementRessources ( $Element, $Count );

						if ($Count >= 1)
						{
							$CurrentPlanet['metal']          -= $Ressource['metal'];
							$CurrentPlanet['crystal']        -= $Ressource['crystal'];
							$CurrentPlanet['deuterium']      -= $Ressource['deuterium'];

							if ($Element == 214 && $CurrentUser['rpg_destructeur'] == 1)
								$Count = 2 * $Count;

							$CurrentPlanet['b_hangar_id']    .= "". $Element .",". $Count .";";
							$LegacyPlanet['b_hangar_id']	=	'b_hangar_id';
						}
					}
				}
			}

			header ("Location: game.php?page=buildings&mode=fleet");

		}

		if ($CurrentPlanet[$resource[21]] == 0)
			message($lang['bd_shipyard_required'], '', '', true);

		$NotBuilding = true;

		if ($CurrentPlanet['b_building_id'] != 0)
		{
			$CurrentQueue = $CurrentPlanet['b_building_id'];
			if (strpos ($CurrentQueue, ";"))
			{
				// FIX BY LUCKY - IF THE SHIPYARD IS IN QUEUE THE USER CANT RESEARCH ANYTHING...
				$QueueArray		= explode (";", $CurrentQueue);

				for($i = 0; $i < MAX_BUILDING_QUEUE_SIZE; $i++)
				{
					$ListIDArray	= explode (",", $QueueArray[$i]);
					$Element		= $ListIDArray[0];

					if ( ($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 ) )
					{
						break;
					}
				}
				// END - FIX
			}
			else
			{
				$CurrentBuilding = $CurrentQueue;
			}

			if ( ( ( $CurrentBuilding == 21 ) or ( $CurrentBuilding == 14 ) or ( $CurrentBuilding == 15 ) ) or  (($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 )) ) // ADDED (or $Element == 21) BY LUCKY
			{
				$parse[message] = "<font color=\"red\">".$lang['bd_building_shipyard']."</font>";
				$NotBuilding = false;
			}
		}

		$TabIndex = 0;
		foreach($lang['tech'] as $Element => $ElementName)
		{
			if ($Element > 201 && $Element <= 399)
			{
				if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element))
				{
					$CanBuildOne         = IsElementBuyable($CurrentUser, $CurrentPlanet, $Element, false);
					$BuildOneElementTime = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
					$ElementCount        = $CurrentPlanet[$resource[$Element]];
					$ElementNbre         = ($ElementCount == 0) ? "" : " (". $lang['bd_available'] . pretty_number($ElementCount) . ")";

					$PageTable .= "\n<tr>";
					$PageTable .= "<th class=l>";
					$PageTable .= "<a href=game.".$phpEx."?page=infos&gid=".$Element.">";
					$PageTable .= "<img border=0 src=\"".$dpath."gebaeude/".$Element.".gif\" align=top width=120 height=120></a>";
					$PageTable .= "</th>";
					$PageTable .= "<td class=l>";
					$PageTable .= "<a href=game.".$phpEx."?page=infos&gid=".$Element.">".$ElementName."</a> ".$ElementNbre."<br>";
					$PageTable .= "".$lang['res']['descriptions'][$Element]."<br>";
					$PageTable .= GetElementPrice($CurrentUser, $CurrentPlanet, $Element, false);
					$PageTable .= ShowBuildTime($BuildOneElementTime);
					$PageTable .= "</td>";
					$PageTable .= "<th class=k>";

					if ($CanBuildOne && $NotBuilding)
					{
						$TabIndex++;
						$PageTable .= "<input type=text name=fmenge[".$Element."] alt='".$lang['tech'][$Element]."' size=6 maxlength=6 value=0 tabindex=".$TabIndex.">";
					}

					if($NotBuilding)
					{
						$parse[build_fleet] = "<tr><td class=\"c\" colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".$lang['bd_build_ships']."\"></td></tr>";
					}

					$PageTable .= "</th>";
					$PageTable .= "</tr>";

				}
			}
		}

		if ($CurrentPlanet['b_hangar_id'] != '')
			$BuildQueue .= $this->ElementBuildListBox( $CurrentUser, $CurrentPlanet );

		$parse['buildlist']    	= $PageTable;
		$parse['buildinglist'] 	= $BuildQueue;
		display(parsetemplate(gettemplate('buildings/buildings_fleet'), $parse));
	}

	public function DefensesBuildingPage ( &$CurrentPlanet, $CurrentUser )
	{
		global $lang, $resource, $phpEx, $dpath, $_POST,$xgp_root, $LegacyPlanet;

		include_once($xgp_root . 'includes/functions/IsTechnologieAccessible.' . $phpEx);
		include_once($xgp_root . 'includes/functions/GetElementPrice.' . $phpEx);

		$parse = $lang;

		if (isset($_POST['fmenge']))
		{
			$Missiles[502] = $CurrentPlanet[ $resource[502] ];
			$Missiles[503] = $CurrentPlanet[ $resource[503] ];
			$SiloSize      = $CurrentPlanet[ $resource[44] ];
			$MaxMissiles   = $SiloSize * 10;
			$BuildQueue    = $CurrentPlanet['b_hangar_id'];
			$BuildArray    = explode (";", $BuildQueue);

			for ($QElement = 0; $QElement < count($BuildArray); $QElement++)
			{
				$ElmentArray = explode (",", $BuildArray[$QElement] );
				if($ElmentArray[0] == 502)
				{
					$Missiles[502] += $ElmentArray[1];
				}
				elseif($ElmentArray[0] == 503)
				{
					$Missiles[503] += $ElmentArray[1];
				}
			}


			foreach($_POST['fmenge'] as $Element => $Count)
			{
			   if($Element < 300 OR $Element > 550)
				{
					continue;
				}
				$Element = intval($Element);
				$Count   = intval($Count);

				if ($Count > MAX_FLEET_OR_DEFS_PER_ROW)
            {
					$Count = MAX_FLEET_OR_DEFS_PER_ROW;
            }
            
				if ($Count != 0)
				{
					$InQueue = strpos ( $CurrentPlanet['b_hangar_id'], $Element.",");
					$IsBuildp = ($CurrentPlanet[$resource[407]] >= 1) ? TRUE : FALSE;
					$IsBuildg = ($CurrentPlanet[$resource[408]] >= 1) ? TRUE : FALSE;
					$IsBuildpp = ($CurrentPlanet[$resource[409]] >= 1) ? TRUE : FALSE;

					if ( $Element == 407 && !$IsBuildp && $InQueue === FALSE )
					{
						$Count = 1;
					}


					if ( $Element == 408 && !$IsBuildg && $InQueue === FALSE )
					{
						$Count = 1;
					}


					if ( $Element == 409 && !$IsBuildpp && $InQueue === FALSE )
					{
						$Count = 1;
					}


					if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element))
					{
						$MaxElements = $this->GetMaxConstructibleElements ( $Element, $CurrentPlanet );

						if ($Element == 502 || $Element == 503)
						{
							$ActuMissiles  = $Missiles[502] + ( 2 * $Missiles[503] );
							$MissilesSpace = $MaxMissiles - $ActuMissiles;
							if ($Element == 502)
							{
								if ( $Count > $MissilesSpace )
								{
									$Count = $MissilesSpace;
								}

							}
							else
							{
								if ( $Count > floor( $MissilesSpace / 2 ) )
								{
									$Count = floor( $MissilesSpace / 2 );
								}
							}

							if ($Count > $MaxElements)
							{
								$Count = $MaxElements;
							}

							$Missiles[$Element] += $Count;
						}
						else
						{
							if ($Count > $MaxElements)
							{
								$Count = $MaxElements;
							}

						}

						$Ressource = $this->GetElementRessources ( $Element, $Count );

						if ($Count >= 1)
						{
							$CurrentPlanet['metal']           -= $Ressource['metal'];
							$CurrentPlanet['crystal']         -= $Ressource['crystal'];
							$CurrentPlanet['deuterium']       -= $Ressource['deuterium'];
							$CurrentPlanet['b_hangar_id']     .= "". $Element .",". $Count .";";
							$LegacyPlanet['b_hangar_id']	=	'b_hangar_id';
                            //}
						}
					}
				}
			}

			header ("Location: game.php?page=buildings&mode=defense");

		}

		if ($CurrentPlanet[$resource[21]] == 0)
			message($lang['bd_shipyard_required'], '', '', true);

		$NotBuilding = true;

		if ($CurrentPlanet['b_building_id'] != 0)
		{
			$CurrentQueue = $CurrentPlanet['b_building_id'];
			if (strpos ($CurrentQueue, ";"))
			{
				// FIX BY LUCKY - IF THE SHIPYARD IS IN QUEUE THE USER CANT RESEARCH ANYTHING...
				$QueueArray		= explode (";", $CurrentQueue);

				for($i = 0; $i < MAX_BUILDING_QUEUE_SIZE; $i++)
				{
					$ListIDArray	= explode (",", $QueueArray[$i]);
					$Element		= $ListIDArray[0];

					if ( ($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 ) )
					{
						break;
					}
				}
				// END - FIX
			}
			else
			{
				$CurrentBuilding = $CurrentQueue;
			}

			if ( ( ( $CurrentBuilding == 21 ) or ( $CurrentBuilding == 14 ) or ( $CurrentBuilding == 15 ) ) or  (($Element == 21 ) or ( $Element == 14 ) or ( $Element == 15 )) ) // ADDED (or $Element == 21) BY LUCKY
			{
				$parse[message] = "<font color=\"red\">".$lang['bd_building_shipyard']."</font>";
				$NotBuilding = false;
			}


		}

		$TabIndex  = 0;
		$PageTable = "";
		foreach($lang['tech'] as $Element => $ElementName)
		{
			if ($Element > 400 && $Element <= 599)
			{
				if (IsTechnologieAccessible($CurrentUser, $CurrentPlanet, $Element))
				{
					$CanBuildOne         = IsElementBuyable($CurrentUser, $CurrentPlanet, $Element, false);
					$BuildOneElementTime = GetBuildingTime($CurrentUser, $CurrentPlanet, $Element);
					$ElementCount        = $CurrentPlanet[$resource[$Element]];
					$ElementNbre         = ($ElementCount == 0) ? "" : " (". $lang['bd_available'] . pretty_number($ElementCount) . ")";

					$PageTable .= "\n<tr>";
					$PageTable .= "<th class=l>";
					$PageTable .= "<a href=game.".$phpEx."?page=infos&gid=".$Element.">";
					$PageTable .= "<img border=0 src=\"".$dpath."gebaeude/".$Element.".gif\" align=top width=120 height=120></a>";
					$PageTable .= "</th>";
					$PageTable .= "<td class=l>";
					$PageTable .= "<a href=game.".$phpEx."?page=infos&gid=".$Element.">".$ElementName."</a> ".$ElementNbre."<br>";
					$PageTable .= "".$lang['res']['descriptions'][$Element]."<br>";
					$PageTable .= GetElementPrice($CurrentUser, $CurrentPlanet, $Element, false);
					$PageTable .= ShowBuildTime($BuildOneElementTime);
					$PageTable .= "</td>";
					$PageTable .= "<th class=k>";

					if ($CanBuildOne)
					{
						$InQueue = strpos ( $CurrentPlanet['b_hangar_id'], $Element.",");
						$IsBuildp = ($CurrentPlanet[$resource[407]] >= 1) ? TRUE : FALSE;
						$IsBuildg = ($CurrentPlanet[$resource[408]] >= 1) ? TRUE : FALSE;
						$IsBuildpp = ($CurrentPlanet[$resource[409]] >= 1) ? TRUE : FALSE;
						$BuildIt = TRUE;
						if ($Element == 407 || $Element == 408 || $Element == 409)
						{
							$BuildIt = false;

							if ( $Element == 407 && !$IsBuildp && $InQueue === FALSE )
								$BuildIt = TRUE;

							if ( $Element == 408 && !$IsBuildg && $InQueue === FALSE )
								$BuildIt = TRUE;

							if ( $Element == 409 && !$IsBuildpp && $InQueue === FALSE )
								$BuildIt = TRUE;

						}

						if (!$BuildIt)
							$PageTable .= "<font color=\"red\">".$lang['bd_protection_shield_only_one']."</font>";
						elseif($NotBuilding)
						{
							$TabIndex++;
							$PageTable .= "<input type=text name=fmenge[".$Element."] alt='".$lang['tech'][$Element]."' size=6 maxlength=6 value=0 tabindex=".$TabIndex.">";
							$PageTable .= "</th>";
						}

						if($NotBuilding)
						{
							$parse[build_defenses] = "<tr><td class=\"c\" colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"".$lang['bd_build_defenses']."\"></td></tr>";
						}
					}
					else
					{
						$PageTable .= "</th>";
					}

					$PageTable .= "</tr>";
				}
			}
		}

		if ($CurrentPlanet['b_hangar_id'] != '')
			$BuildQueue .= $this->ElementBuildListBox( $CurrentUser, $CurrentPlanet );

		$parse['buildlist']    	= $PageTable;
		$parse['buildinglist'] 	= $BuildQueue;
		display(parsetemplate(gettemplate('buildings/buildings_defense'), $parse));
	}
}
?>