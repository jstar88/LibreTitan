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

class ShowGalaxyPage extends GalaxyRows
{
	
	public function __construct($CurrentUser, $CurrentPlanet)
	{
		global $xgp_root, $phpEx, $dpath, $resource, $lang, $planetcount;

		$TargetUniverse=0;
      $TargetGalaxy=0;
		$TargetSystem=0;
		$TargetPlanet=0;
		
    $fleetmax      	= ($CurrentUser['computer_tech'] + 1) + ($CurrentUser['rpg_commandant'] * COMMANDANT);
		$CurrentPlID   	= $CurrentPlanet['id'];
		$CurrentMIP    	= $CurrentPlanet['interplanetary_misil'];
		$CurrentRC     	= $CurrentPlanet['recycler'];
		$CurrentSP     	= $CurrentPlanet['spy_sonde'];
		$HavePhalanx   	= $CurrentPlanet['phalanx'];
		$CurrentUniverse 	= $CurrentPlanet['universe'];
		$CurrentSystem 	= $CurrentPlanet['system'];
		$CurrentGalaxy 	= $CurrentPlanet['galaxy'];
		$CurrentPlanet  = $CurrentPlanet['planet'];
		$CanDestroy    	= $CurrentPlanet[$resource[213]] + $CurrentPlanet[$resource[214]];
      
		$maxfleet       = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". intval($CurrentUser['id']) ."';", 'fleets');
		$maxfleet_count = mysql_num_rows($maxfleet);

		if (!isset($mode))
		{
			if (isset($_GET['mode']))
			{
				$mode = intval($_GET['mode']);
			}
			else
			{
				$mode = 0;
			}
		}

		if ($mode == 0)
		{
	  	$TargetUniverse      = $CurrentUniverse;
			$TargetGalaxy        = $CurrentGalaxy;
			$TargetSystem        = $CurrentSystem;
			$TargetPlanet        = $CurrentPlanet;
		}
		else if ($mode == 1)
		{
		//start mod		
		 
		 
		 
		 $TargetPlanet  =intval($_POST["planet"]);
		 $TargetUniverse=0;
		 $TargetIndex=0;
	    
		 
		 if ($_POST["universe_target"] )
			{			  
			  $TargetIndex=intval($_POST["universe_target"]);	
        	
			}
		
			
		 if ($_POST["universe"] )
			{
			  
			  $TargetUniverse=intval($_POST["universe"]);
			  $TargetUniverse = ereg_replace("[^0-9]","",$TargetUniverse);
			  if(!isUniverseInWar($TargetUniverse,$CurrentUniverse)){
           	$TargetUniverse = $CurrentUniverse;
        }
				
				
			}
			else
			{
				$TargetUniverse = $CurrentUniverse;
			}
			//end mod
		  // echo $_POST["galaxy"];
			if ($_POST["galaxy"])
			{
		   $TargetGalaxy  =intval($_POST["galaxy"]);
			 $TargetGalaxy = ereg_replace("[^0-9]","",$TargetGalaxy);
			}
			else
			{
				$TargetGalaxy = 1;
			}

			if ($_POST["system"])
			{
			  $TargetSystem  =intval($_POST["system"]);
				$TargetSystem = ereg_replace("[^0-9]","",$TargetSystem);
			}
			else
			{
				$TargetSystem = 1;
			}
      //start mod
      if ($TargetUniverse > MAX_UNIVERSE_IN_WORLD){
				
        $universes_in_war_with_this_universe=universesInWarWith($CurrentUniverse);
			  $lenght=count($universes_in_war_with_this_universe);
			  if($lenght>0)
			    $TargetUniverse= $universes_in_war_with_this_universe[$lenght-1];
			  else
			    $TargetUniverse=$CurrentUniverse;
      }
			//end mod

			if ($TargetGalaxy > MAX_GALAXY_IN_WORLD)
				$TargetGalaxy = MAX_GALAXY_IN_WORLD;

			if ($TargetSystem > MAX_SYSTEM_IN_GALAXY)
				$TargetSystem = MAX_SYSTEM_IN_GALAXY;
			
      
       //start mod
       if ($_POST["universeLeft"])
        
			{ 
				if ($TargetUniverse < 1)
				{
					$TargetUniverse = $CurrentUniverse;
					 $TargetIndex=0;
				}
				else
				{  
				  $universes_in_war_with_this_universe=universesInWarWith($CurrentUniverse);
				  $index=array_search($CurrentUniverse,$universes_in_war_with_this_universe);
				  $TargetIndex--;
          
          if( ($index !== false) && ($index+$TargetIndex>0) ){
            
					  $TargetUniverse = $universes_in_war_with_this_universe[$index+$TargetIndex];
					  }
					else
					  $TargetUniverse=$CurrentUniverse;
					   $TargetIndex=0;
				}
			}
			elseif ($_POST["universeRight"])
			{
				if ($_POST["universe"] > MAX_UNIVERSE_IN_WORLD)
				{
					$TargetUniverse      = 	$CurrentUniverse;
					$TargetIndex=0;
				}			
				else
				{
				  $universes_in_war_with_this_universe=universesInWarWith($CurrentUniverse);
		      $index=array_search($CurrentUniverse,$universes_in_war_with_this_universe);
			    $lenght=count($universes_in_war_with_this_universe);
				   
				  $TargetIndex++;
				  
          if( ($index !== false) && ($index + $TargetIndex<$lenght) ){
               
					  $TargetUniverse = $universes_in_war_with_this_universe[$index +  $TargetIndex];
					  }
					else{
					
					  $TargetUniverse=$CurrentUniverse;
					  $TargetIndex=0;
           
            }
				}
		  }
			//end mod

			if ($_POST["galaxyLeft"])
			{
				if ($TargetGalaxy <= 1)
				{
					$TargetGalaxy = 1;
					
				}
				else
				{
					$TargetGalaxy = $TargetGalaxy - 1;
				}
			}
			elseif ($_POST["galaxyRight"])
			{
				if ($TargetGalaxy > MAX_GALAXY_IN_WORLD OR $_POST["galaxyRight"] > MAX_GALAXY_IN_WORLD)
				{
					$TargetGalaxy      = MAX_GALAXY_IN_WORLD;
					$_POST["galaxyRight"] = MAX_GALAXY_IN_WORLD;
				}
				elseif ($TargetGalaxy == MAX_GALAXY_IN_WORLD)
				{
					$TargetGalaxy               = MAX_GALAXY_IN_WORLD;
				}
				else
				{
					$TargetGalaxy += 1;
				}
			}

			if ($_POST["systemLeft"])
			{
				if ($TargetSystem <= 1)
				{
					$TargetSystem = 1;
				}
				else
				{
				$TargetSystem = $TargetSystem - 1;
				}
			}
			elseif ($_POST["systemRight"])
			{
				if ($TargetSystem> MAX_SYSTEM_IN_GALAXY OR $_POST["systemRight"] > MAX_SYSTEM_IN_GALAXY)
				{
			    	$TargetSystem      = MAX_SYSTEM_IN_GALAXY;
				}
				elseif ($TargetSystem == MAX_SYSTEM_IN_GALAXY)
				{
					$TargetSystem      = MAX_SYSTEM_IN_GALAXY;
				}
				else
				{
					$TargetSystem += 1;
				}
			}
		}
		elseif ($mode == 2)
		{
		//start mod
		  $TargetUniverse        = intval($_GET['universe']);
		  //end mod
			$TargetGalaxy        = intval($_GET['galaxy']);
	  	$TargetSystem        = intval($_GET['system']);
			$TargetPlanet        = intval($_GET['planet']);
		}
		elseif ($mode == 3)
		{   //start mod
		  $TargetUniverse        = intval($_GET['universe']);
		  //end mod
			$TargetGalaxy        = intval($_GET['galaxy']);
			$TargetSystem        = intval($_GET['system']);
		}
		else
		{  //start mod
		  $TargetUniverse      = $CurrentUniverse;
		  //end mod
			$TargetGalaxy        = 1;
			$TargetSystem        = 1;
		}
		
		// START FIX BY alivan
		/*
		if ($mode != 2)
		{
			if ( ( $CurrentPlanet['system'] != ( $_POST["system"] - 1 ) ) && ( $CurrentPlanet['system'] != $_GET['system'] or $CurrentPlanet['galaxy'] != $_GET['galaxy'] ) && ( $mode != 0 ) && ( $CurrentPlanet['deuterium'] < 10 ) )
			{
				die (message($lang['gl_no_deuterium_to_view_galaxy'], "game.php?page=galaxy&mode=0", 2));
			}
			elseif( ( $CurrentPlanet['system'] != ( $_POST["system"] - 1 ) ) && ( $CurrentPlanet['system'] != $_GET['system'] or $CurrentPlanet['galaxy'] != $_GET['galaxy'] ) && ( $mode != 0 ) )
			{
				$QryGalaxyDeuterium   = "UPDATE {{table}} SET ";
				$QryGalaxyDeuterium  .= "`deuterium` = `deuterium` -  10 ";
				$QryGalaxyDeuterium  .= "WHERE ";
				$QryGalaxyDeuterium  .= "`id` = '". $CurrentPlanet['id'] ."' ";
				$QryGalaxyDeuterium  .= "LIMIT 1;";
				doquery($QryGalaxyDeuterium, 'planets');
			}
		}
		elseif ($mode == 2 && $CurrentPlanet['interplanetary_misil'] < 1)
		{
			die (message($lang['ma_no_missiles'], "game.php?page=galaxy&mode=0", 2));
		}
		// END FIX BY alivan
      */
      
      
    
       
       $GalaxyInfo = doquery( "SELECT {{table}}galaxy.metal, {{table}}galaxy.crystal, {{table}}galaxy.id_luna, {{table}}galaxy.id_planet, {{table}}planets.universe, {{table}}planets.galaxy, {{table}}planets.system, {{table}}planets.planet, {{table}}planets.destruyed, {{table}}planets.name, {{table}}planets.image, {{table}}planets.last_update, {{table}}users.id, {{table}}users.ally_id, {{table}}users.bana, {{table}}users.urlaubs_modus, {{table}}users.onlinetime, {{table}}users.username, {{table}}statpoints.stat_type, {{table}}statpoints.stat_code, {{table}}statpoints.total_rank, {{table}}statpoints.total_points, {{table}}moons.diameter, {{table}}moons.temp_min, {{table}}moons.destruyed AS destruyed_moon, {{table}}moons.name AS name_moon, {{table}}alliance.ally_name, {{table}}alliance.ally_tag, {{table}}alliance.ally_web, {{table}}alliance.ally_members
									FROM {{table}}alliance RIGHT JOIN ({{table}}planets AS {{table}}moons RIGHT JOIN ({{table}}statpoints RIGHT JOIN (({{table}}planets INNER JOIN {{table}}users ON {{table}}planets.id_owner = {{table}}users.id) INNER JOIN {{table}}galaxy ON {{table}}planets.id = {{table}}galaxy.id_planet) ON {{table}}statpoints.id_owner = {{table}}users.id) ON {{table}}moons.id = {{table}}galaxy.id_luna) ON {{table}}alliance.id = {{table}}users.ally_id
									WHERE ( (({{table}}galaxy.universe)='".$TargetUniverse."') AND (({{table}}galaxy.galaxy)='".$TargetGalaxy."') AND (({{table}}galaxy.system)='".$TargetSystem."') AND ({{table}}galaxy.planet>'0' AND {{table}}galaxy.planet<='".MAX_PLANET_IN_SYSTEM."'))
									GROUP BY `id_planet` ORDER BY {{table}}planets.planet;" , '' );

      
		$planetcount = 0;
		$lunacount   = 0;

		$parse						= $lang;
		$parse['universe_target'] =$TargetIndex;
		$parse['universe']			= $TargetUniverse;
		$parse['galaxy']			= $TargetGalaxy;
		$parse['system']			= $TargetSystem;
		$parse['planet']			= $TargetPlanet;
		$parse['currentmip']		= $CurrentMIP;
		$parse['maxfleetcount']		= $maxfleet_count;
		$parse['fleetmax']			= $fleetmax;
		$parse['recyclers']   		= pretty_number($CurrentRC);
		$parse['spyprobes']   		= pretty_number($CurrentSP);
		$parse['missile_count']		= sprintf($lang['gl_missil_to_launch'], $CurrentMIP);
		$parse['current']			= $_GET['current'];
		$parse['current_universe']	= $CurrentUniverse;
		$parse['current_galaxy']	= $CurrentPlanet["galaxy"];
		$parse['current_system']	= $CurrentPlanet["system"];
		$parse['current_planet']	= $CurrentPlanet["planet"];
		$parse['planet_type'] 		= $CurrentPlanet["planet_type"];
		$parse['dpath']			= $dpath;
      $page['galaxyscripts']		= parsetemplate(gettemplate('galaxy/galaxy_script'), $parse);
		//start mod
		if( isUniverseInWarGeneric($CurrentUniverse)){
		 
		  $page['universe_collision'] = parsetemplate(gettemplate('galaxy/galaxy_collision'), $parse);
		//end mod
		$page['galaxyselector']		= parsetemplate(gettemplate('galaxy/galaxy_selector_extended'), $parse);
		($mode == 2) ? $page['mip'] = parsetemplate(gettemplate('galaxy/galaxy_missile_selector_extended'), $parse) : " ";
		}
		else{
		
      $page['galaxyselector']		= parsetemplate(gettemplate('galaxy/galaxy_selector'), $parse);
		($mode == 2) ? $page['mip'] = parsetemplate(gettemplate('galaxy/galaxy_missile_selector'), $parse) : " ";		
    }
    $page['galaxytitles'] 		= parsetemplate(gettemplate('galaxy/galaxy_titles'), $parse);
		$page['galaxyrows'] 		= $this->ShowGalaxyRows   ($GalaxyInfo, $TargetUniverse, $TargetGalaxy, $TargetSystem, $HavePhalanx, $CurrentUniverse, $CurrentGalaxy, $CurrentSystem, $CurrentRC, $CurrentMIP);

		$parse['planetcount'] 		= $planetcount ." ". $lang['gl_populed_planets'];

		$page['galaxyfooter'] 		= parsetemplate(gettemplate('galaxy/galaxy_footer'), $parse);

		return display(parsetemplate(gettemplate('galaxy/galaxy_body'), $page), false);
	}
	
	private function ShowGalaxyRows($GalaxyQuery, $Universe, $Galaxy, $System, $HavePhalanx, $CurrentUniverse, $CurrentGalaxy, $CurrentSystem, $CurrentRC, $CurrentMIP)
	{
		global $planetcount, $dpath, $user, $xgp_root, $phpEx;

		$Result	= "";
		$start	= 1;

		while ( $GalaxyInfo = mysql_fetch_array ( $GalaxyQuery ) )
		{
			for ($Planet = $start; $Planet < 1+(MAX_PLANET_IN_SYSTEM); $Planet++)
			{
				$parcialCount++;

				if ( $GalaxyInfo['universe'] == $Universe && $GalaxyInfo['galaxy'] == $Galaxy && $GalaxyInfo['system'] == $System && $GalaxyInfo['planet'] == $Planet )
				{
					$Result .= "\n";
					$Result .= "<tr>";

					if ($GalaxyInfo["id_planet"] != 0)
					{
						if ($GalaxyInfo['destruyed'] != 0 && $GalaxyInfo['id_owner'] != '' && $GalaxyInfo["id_planet"] != '')
						{
							$this->CheckAbandonPlanetState ($GalaxyInfo);
						}
						else
						{
							$planetcount++;
						}

						if ($GalaxyInfo["id_luna"] != 0 && $GalaxyInfo["destruyed_moon"] != 0)
						{
							$this->CheckAbandonMoonState ($GalaxyInfo);
						}
					}
               
					$Result .= $this->GalaxyRowPos        ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet );
				   $Result .= $this->GalaxyRowPlanet     ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet, 1, $HavePhalanx, $CurrentUniverse, $CurrentGalaxy, $CurrentSystem);
					$Result .= $this->GalaxyRowPlanetName ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet, 1, $HavePhalanx, $CurrentUniverse, $CurrentGalaxy, $CurrentSystem);
					$Result .= $this->GalaxyRowMoon       ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet, 3 );
					$Result .= $this->GalaxyRowDebris     ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet, 2, $CurrentRC);
					$Result .= $this->GalaxyRowUser       ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet );
					$Result .= $this->GalaxyRowAlly       ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet );
					$Result .= $this->GalaxyRowActions    ( $GalaxyInfo, $Universe, $Galaxy, $System, $Planet, $CurrentUniverse, $CurrentGalaxy, $CurrentSystem, $CurrentMIP);
					$Result .= "</tr>";

					$start++;
					break;
				}
				else
				{
					$parse['pos']	= $start;
					$Result .= parsetemplate(gettemplate('galaxy/galaxy_row'), $parse);
					$start++;
				}
			}
		}


		for ( $i = $start; $i <= MAX_PLANET_IN_SYSTEM; $i++ )
		{
			$parse['pos']	= $i;
			$Result .= parsetemplate(gettemplate('galaxy/galaxy_row'), $parse);
		}

		unset($GalaxyInfo);
		return $Result;

	}

}
?>