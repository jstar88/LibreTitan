<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

if(!defined('INSIDE')){ die(header("location:../../"));}

include_once(XGP_ROOT . 'includes/classes/class.GalaxyRows.php');

class ShowGalaxyPage extends GalaxyRows
{
	private $planet_count = 0;

	public function __construct($CurrentUser, $CurrentPlanet)
	{
		global $resource, $lang;

		$fleetmax      	= Fleets::get_max_fleets ( $CurrentUser['computer_tech'] , $CurrentUser['rpg_amiral'] );
		$CurrentPlID   	= $CurrentPlanet['id'];
		$CurrentMIP    	= $CurrentPlanet['interplanetary_misil'];
		$CurrentRC     	= $CurrentPlanet['recycler'];
		$CurrentSP     	= $CurrentPlanet['spy_sonde'];
		$HavePhalanx   	= $CurrentPlanet['phalanx'];
		$CurrentSystem 	= $CurrentPlanet['system'];
		$CurrentGalaxy 	= $CurrentPlanet['galaxy'];
		$CanDestroy    	= $CurrentPlanet[$resource[213]] + $CurrentPlanet[$resource[214]];

		$maxfleet       = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '". intval($CurrentUser['id']) ."';", 'fleets');
		$maxfleet_count = mysql_num_rows($maxfleet);

		$postedGalaxy	= isset ( $_POST["galaxy"] ) ? $_POST["galaxy"] : NULL;
		$postedSystem	= isset ( $_POST["system"] ) ? $_POST["system"] : NULL;

		$getGalaxy		= isset ( $_GET['galaxy'] ) ? $_GET['galaxy'] : NULL;
		$getSystem		= isset ( $_GET['system'] ) ? $_GET['system'] : NULL;
		$getPlanet		= isset ( $_GET['planet'] ) ? $_GET['planet'] : NULL;

		$galaxyLeft		= isset ( $_POST["galaxyLeft"] ) ? $_POST["galaxyLeft"] : NULL;
		$galaxyRight	= isset ( $_POST["galaxyRight"] ) ? $_POST["galaxyRight"] : NULL;
		$systemLeft		= isset ( $_POST["systemLeft"] ) ? $_POST["systemLeft"] : NULL;
		$systemRight	= isset ( $_POST["systemRight"] ) ? $_POST["systemRight"] : NULL;

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
			$galaxy        = $CurrentPlanet['galaxy'];
			$system        = $CurrentPlanet['system'];
			$planet        = $CurrentPlanet['planet'];
		}
		elseif ($mode == 1)
		{
			if (intval($postedGalaxy))
			{
				// ereg_replace REEMPLAZADO POR preg_replace PARA PHP MAYORES A 5.3.0
				$postedGalaxy = preg_replace("[^0-9]","",$postedGalaxy);
			}
			else
			{
				$postedGalaxy = 1;
			}

			if (intval($postedSystem))
			{
				// ereg_replace REEMPLAZADO POR preg_replace PARA PHP MAYORES A 5.3.0
				$postedSystem = preg_replace("[^0-9]","",$postedSystem);
			}
			else
			{
				$postedSystem = 1;
			}

			if ($postedGalaxy > MAX_GALAXY_IN_WORLD)
				$postedGalaxy = MAX_GALAXY_IN_WORLD;

			if ($postedSystem > MAX_SYSTEM_IN_GALAXY)
				$postedSystem = MAX_SYSTEM_IN_GALAXY;

			if ($galaxyLeft)
			{
				if ($postedGalaxy < 1)
				{
					$postedGalaxy = 1;
					$galaxy          = 1;
				}
				elseif ($postedGalaxy == 1)
				{
					$postedGalaxy = 1;
					$galaxy          = 1;
				}
				else
				{
					$galaxy = $postedGalaxy - 1;
				}
			}
			elseif ($galaxyRight)
			{
				if ($postedGalaxy > MAX_GALAXY_IN_WORLD OR $galaxyRight > MAX_GALAXY_IN_WORLD)
				{
					$postedGalaxy  	= MAX_GALAXY_IN_WORLD;
					$galaxyRight 	= MAX_GALAXY_IN_WORLD;
					$galaxy       	= MAX_GALAXY_IN_WORLD;
				}
				elseif ($postedGalaxy == MAX_GALAXY_IN_WORLD)
				{
					$postedGalaxy  	= MAX_GALAXY_IN_WORLD;
					$galaxy       	= MAX_GALAXY_IN_WORLD;
				}
				else
				{
					$galaxy = $postedGalaxy + 1;
				}
			}
			else
			{
				$galaxy = $postedGalaxy;
			}

			if ($systemLeft)
			{
				if ($postedSystem < 1)
				{
					$postedSystem = 1;
					$system          = 1;
				}
				elseif ($postedSystem == 1)
				{
					$postedSystem = 1;
					$system          = 1;
				}
				else
				{
					$system = $postedSystem - 1;
				}
			}
			elseif ($systemRight)
			{
				if ($postedSystem      > MAX_SYSTEM_IN_GALAXY OR $systemRight > MAX_SYSTEM_IN_GALAXY)
				{
					$postedSystem	= MAX_SYSTEM_IN_GALAXY;
					$system       	= MAX_SYSTEM_IN_GALAXY;
				}
				elseif ($postedSystem == MAX_SYSTEM_IN_GALAXY)
				{
					$postedSystem  	= MAX_SYSTEM_IN_GALAXY;
					$system       	= MAX_SYSTEM_IN_GALAXY;
				}
				else
				{
					$system = $postedSystem + 1;
				}
			}
			else
			{
				$system = $postedSystem;
			}
		}
		elseif ($mode == 2)
		{
			$galaxy        = intval($getGalaxy);
			$system        = intval($getSystem);
			$planet        = intval($getPlanet);
		}
		elseif ($mode == 3)
		{
			$galaxy        = intval($getGalaxy);
			$system        = intval($getSystem);
		}
		else

		{
			$galaxy        = 1;
			$system        = 1;
		}

		// START FIX BY alivan
		if ($mode != 2)
		{
			if ( ( $CurrentPlanet['system'] != ( $postedSystem - 1 ) ) && ( $CurrentPlanet['system'] != $getSystem or $CurrentPlanet['galaxy'] != $getGalaxy ) && ( $mode != 0 ) && ( $CurrentPlanet['deuterium'] < 10 ) )
			{
				die (message($lang['gl_no_deuterium_to_view_galaxy'], "game.php?page=galaxy&mode=0", 2));
			}
			elseif( ( $CurrentPlanet['system'] != ( $postedSystem - 1 ) ) && ( $CurrentPlanet['system'] != $getSystem or $CurrentPlanet['galaxy'] != $getGalaxy ) && ( $mode != 0 ) )
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

		$GalaxyInfo = doquery( "SELECT {{table}}galaxy.metal, {{table}}galaxy.crystal, {{table}}galaxy.id_luna, {{table}}galaxy.id_planet, {{table}}planets.galaxy, {{table}}planets.system, {{table}}planets.planet, {{table}}planets.destruyed, {{table}}planets.name, {{table}}planets.image, {{table}}planets.last_update,{{table}}planets.id_owner,{{table}}users.id, {{table}}users.ally_id, {{table}}users.bana, {{table}}users.urlaubs_modus, {{table}}users.onlinetime, {{table}}users.username,{{table}}statpoints.stat_type, {{table}}statpoints.stat_code, {{table}}statpoints.total_rank, {{table}}statpoints.total_points, {{table}}moons.diameter, {{table}}moons.temp_min, {{table}}moons.destruyed AS destruyed_moon, {{table}}moons.name AS name_moon, {{table}}alliance.ally_name, {{table}}alliance.ally_tag, {{table}}alliance.ally_web, {{table}}alliance.ally_members,{{table}}buddy.owner AS friends_owner,{{table}}buddy.sender AS friends_sender
			FROM {{table}}alliance RIGHT JOIN ({{table}}planets AS {{table}}moons RIGHT JOIN ({{table}}statpoints RIGHT JOIN ((({{table}}planets INNER JOIN {{table}}users ON {{table}}planets.id_owner = {{table}}users.id ) INNER JOIN {{table}}galaxy ON {{table}}planets.id = {{table}}galaxy.id_planet ) LEFT JOIN {{table}}buddy ON ({{table}}buddy.owner = {{table}}planets.id_owner OR {{table}}buddy.sender = {{table}}planets.id_owner ))  ON {{table}}statpoints.id_owner={{table}}users.id AND {{table}}statpoints.stat_code=1 AND {{table}}statpoints.stat_type=1 ) ON {{table}}moons.id = {{table}}galaxy.id_luna) ON {{table}}alliance.id = {{table}}users.ally_id
			WHERE ({{table}}galaxy.galaxy='".$galaxy."' AND {{table}}galaxy.system='".$system."' AND ({{table}}galaxy.planet>'0' AND {{table}}galaxy.planet<='".MAX_PLANET_IN_SYSTEM."'))
			GROUP BY `id_planet`
			ORDER BY {{table}}planets.planet; " , '' );

		$parse						= $lang;
		$parse['galaxy']			= $galaxy;
		$parse['system']			= $system;
		$parse['planet']			= isset ( $planet ) ? $planet : NULL;
		$parse['currentmip']		= $CurrentMIP;
		$parse['maxfleetcount']		= $maxfleet_count;
		$parse['fleetmax']			= $fleetmax;
		$parse['recyclers']   		= Format::pretty_number($CurrentRC);
		$parse['spyprobes']   		= Format::pretty_number($CurrentSP);
		$parse['missile_count']		= sprintf($lang['gl_missil_to_launch'], $CurrentMIP);
		$parse['current']			= isset ( $_GET['current'] ) ? $_GET['current'] : NULL;
		$parse['current_galaxy']	= $CurrentPlanet["galaxy"];
		$parse['current_system']	= $CurrentPlanet["system"];
		$parse['current_planet']	= $CurrentPlanet["planet"];
		$parse['planet_type'] 		= $CurrentPlanet["planet_type"];

		$page['galaxyscripts']		= parsetemplate(gettemplate('galaxy/galaxy_script'), $parse);
		$page['galaxyselector']		= parsetemplate(gettemplate('galaxy/galaxy_selector'), $parse);
		($mode == 2) ? $page['mip'] = parsetemplate(gettemplate('galaxy/galaxy_missile_selector'), $parse) : " ";
		$page['galaxytitles'] 		= parsetemplate(gettemplate('galaxy/galaxy_titles'), $parse);
		$page['galaxyrows'] 		= $this->ShowGalaxyRows   ($GalaxyInfo, $galaxy, $system, $HavePhalanx, $CurrentGalaxy, $CurrentSystem, $CurrentRC, $CurrentMIP);

		$parse['planetcount'] 		= $this->planet_count ." ". $lang['gl_populed_planets'];

		$page['galaxyfooter'] 		= parsetemplate(gettemplate('galaxy/galaxy_footer'), $parse);

		return display(parsetemplate(gettemplate('galaxy/galaxy_body'), $page), FALSE);
	}

	private function ShowGalaxyRows($GalaxyQuery, $Galaxy, $System, $HavePhalanx, $CurrentGalaxy, $CurrentSystem, $CurrentRC, $CurrentMIP)
	{
		$rows			= '';
		$start			= 1;
		$template		= gettemplate('galaxy/galaxy_row');
		$parcialCount	= 0;

		while ( $GalaxyInfo = mysql_fetch_array ( $GalaxyQuery ) )
		{
			for ($Planet = $start; $Planet < 1+(MAX_PLANET_IN_SYSTEM); $Planet++)
			{
				$parcialCount++;

				if ( $GalaxyInfo['galaxy'] == $Galaxy && $GalaxyInfo['system'] == $System && $GalaxyInfo['planet'] == $Planet )
				{
					if ($GalaxyInfo["id_planet"] != 0)
					{
						if ($GalaxyInfo['destruyed'] != 0 && $GalaxyInfo['id_owner'] != '' && $GalaxyInfo["id_planet"] != '')
						{
							$this->CheckAbandonPlanetState ($GalaxyInfo);
						}
						else
						{
							$this->planet_count++;
						}

						if ($GalaxyInfo["id_luna"] != 0 && $GalaxyInfo["destruyed_moon"] != 0)
						{
							$this->CheckAbandonMoonState ($GalaxyInfo);
						}
					}

					$parse['pos']  	   		= $Planet;
					$parse['planetname']	= $this->GalaxyRowPlanetName ( $GalaxyInfo, $Galaxy, $System, $Planet, 1, $HavePhalanx, $CurrentGalaxy, $CurrentSystem);
                    $parse['debris']		= $this->GalaxyRowDebris     ( $GalaxyInfo, $Galaxy, $System, $Planet, 2, $CurrentRC);

                    if ( $GalaxyInfo['destruyed'] == 0 )
                    {
                        $parse['planet']	= $this->GalaxyRowPlanet     ( $GalaxyInfo, $Galaxy, $System, $Planet, 1, $HavePhalanx, $CurrentGalaxy, $CurrentSystem);
                        $parse['moon']      = $this->GalaxyRowMoon       ( $GalaxyInfo, $Galaxy, $System, $Planet, 3 );
                        $parse['username']  = $this->GalaxyRowUser       ( $GalaxyInfo, $Galaxy, $System, $Planet );
                        $parse['alliance']  = $this->GalaxyRowAlly       ( $GalaxyInfo, $Galaxy, $System, $Planet );
                        $parse['actions']   = $this->GalaxyRowActions    ( $GalaxyInfo, $Galaxy, $System, $Planet, $CurrentGalaxy, $CurrentSystem, $CurrentMIP);
                    }
                    else
                    {
                        $parse['planet']	= '';
                        $parse['moon']      = '';
                        $parse['username']  = '';
                        $parse['alliance']  = '';
                        $parse['actions']   = '';
                    }

					$rows	.= parsetemplate($template, $parse);

					$start++;
					break;
				}
				else
				{
					$parse['pos']			= $start;
					$parse['planet'] 		= '';
					$parse['planetname'] 	= '';
					$parse['moon'] 			= '';
					$parse['debris'] 		= '';
					$parse['username'] 		= '';
					$parse['alliance'] 		= '';
					$parse['actions'] 		= '';

					$rows 	.= parsetemplate($template, $parse);
					$start++;
				}
			}
		}

		for ( $i = $start; $i <= MAX_PLANET_IN_SYSTEM; $i++ )
		{
			$parse['pos']			= $i;
			$parse['planet'] 		= '';
			$parse['planetname'] 	= '';
			$parse['moon'] 			= '';
			$parse['debris'] 		= '';
			$parse['username'] 		= '';
			$parse['alliance'] 		= '';
			$parse['actions'] 		= '';
			$rows .= parsetemplate($template, $parse);
		}

		unset($GalaxyInfo);

		return	$rows;
	}
}
?>