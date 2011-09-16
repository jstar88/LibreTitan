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

class GalaxyRows
{
    protected $CurrentUniverse;
    protected $CurrentSystem;
    protected $CurrentGalaxy;
    protected $CurrentPlanet;
    protected $CurrentPlanetType;
    protected $CurrentPlanetId;
    
    protected $TargetUniverse;
    protected $TargetGalaxy;
    protected $TargetSystem;
    protected $TargetPlanet;
    
    protected $fleetmax;
    protected $CurrentPlID;
    protected $CurrentMIP;
    protected $CurrentRC;
    protected $CurrentSP;
    protected $HavePhalanx;
    protected $CurrentUserId;
    protected $CanDestroy;
    protected $maxfleet_count;
    protected $interplanetary_misil;   
    protected $CurrentDeuterium;
    
    public function __construct($CurrentUser,$CurrentPlanet){
        $this->CurrentUniverse = $CurrentPlanet['universe'];
        $this->CurrentSystem   = $CurrentPlanet['system'];
        $this->CurrentGalaxy   = $CurrentPlanet['galaxy'];
        $this->CurrentPlanet   = $CurrentPlanet['planet'];
        $this->CurrentPlanetType=$CurrentPlanet["planet_type"];
        $this->CurrentPlanetId=$CurrentPlanet['id'];
        
        $this->TargetUniverse = $this->CurrentUniverse;
        $this->TargetGalaxy   = $this->CurrentGalaxy;
        $this->TargetSystem   = $this->CurrentSystem;
        $this->TargetPlanet   = $this->CurrentPlanet;

        $this->fleetmax      = ($CurrentUser['computer_tech'] + 1) + ($CurrentUser['rpg_commandant'] * COMMANDANT);
        $this->CurrentPlID   = $CurrentPlanet['id'];
        $this->CurrentMIP    = $CurrentPlanet['interplanetary_misil'];
        $this->CurrentRC     = $CurrentPlanet['recycler'];
        $this->CurrentSP     = $CurrentPlanet['spy_sonde'];
        $this->HavePhalanx   = $CurrentPlanet['phalanx'];
        $this->CurrentUserId =$CurrentUser['id'];
        $this->CanDestroy    = $CurrentPlanet[$resource[213]] + $CurrentPlanet[$resource[214]];
        $this->interplanetary_misil=$CurrentPlanet['interplanetary_misil'];  
        $this->CurrentDeuterium=$CurrentPlanet['deuterium'];

        $row = mysql_fetch_object(doquery("SELECT COUNT(*) as total FROM {{table}} WHERE `fleet_owner` = '" . $CurrentUser['id'] . "';", 'fleets'));
        $this->maxfleet_count = $row->total;    
    }
	private function GetMissileRange ()
	{
		global $resource, $user;

		if ($user[$resource[117]] > 0)
		{
			$MissileRange = ($user[$resource[117]] * 2) - 1;
		}
		elseif($user[$resource[117]] == 0)
		{
			$MissileRange = 0;
		}
		return $MissileRange;
	}

	public function GetPhalanxRange()
	{
		$PhalanxRange = 0;
        $PhalanxLevel=$this->HavePhalanx;
		if ($PhalanxLevel > 1)
		{
			$PhalanxRange = pow($PhalanxLevel, 2) - 1;
		}
		elseif($PhalanxLevel == 1)
		{
			$PhalanxRange = 1;
		}

		return $PhalanxRange;
	}

	public function CheckAbandonMoonState($lunarow)
	{
		if (($lunarow['destruyed_moon'] + 172800) <= time() && $lunarow['destruyed_moon'] != 0)		
			$QryUpdateGalaxy  = "UPDATE {{table}} SET `id_luna` = '0' WHERE `universe` = '". intval($lunarow['universe']) ."' AND `galaxy` = '". intval($lunarow['galaxy']) ."' AND `system` = '". intval($lunarow['system']) ."' AND `planet` = '". intval($lunarow['planet']) ."' LIMIT 1;";
	
   	doquery( $QryUpdateGalaxy , 'galaxy');
		doquery("DELETE FROM {{table}} WHERE `id` = ".intval($lunarow['id'])."", 'planets');
	}

	public function CheckAbandonPlanetState(&$planet)
	{
		if ($planet['destruyed'] <= time())
		{
			doquery("DELETE FROM {{table}} WHERE `id_planet` = '".intval($planet['id'])."' LIMIT 1;" , 'galaxy');
			doquery("DELETE FROM {{table}} WHERE `id` = ".intval($planet['id'])."", 'planets');
		}
	}

	public function GalaxyRowActions($GalaxyInfo)
	{
		global $user, $dpath, $lang;

		$Result = "<th style=\"white-space: nowrap;\" width=125>";

		if ($GalaxyInfo['id'] != $user['id'])
		{
			if ($this->CurrentMIP <> 0)
			{
				if ($GalaxyInfo['id'] != $user['id'])
				{
					if ($GalaxyInfo["galaxy"] == $this->CurrentGalaxy)
					{
						$Range = $this->GetMissileRange();
						$SystemLimitMin = $this->CurrentSystem - $Range;
						if ($SystemLimitMin < 1)
						{
							$SystemLimitMin = 1;
						}
						$SystemLimitMax = $this->CurrentSystem + $Range;

						if ($this->TargetSystem <= $SystemLimitMax)
						{
							if ($this->TargetSystem >= $SystemLimitMin)
							{
								$MissileBtn = true;
							}
							else
							{
								$MissileBtn = false;
							}
						}
						else
						{
							$MissileBtn = false;
						}
					}
					else
					{
						$MissileBtn = false;
					}
				}
				else
				{
					$MissileBtn = false;
				}
			}
			else
			{
				$MissileBtn = false;
			}

			if ($GalaxyInfo && $GalaxyInfo["destruyed"] == 0)
			{
				if ($user["settings_esp"] == "1" && $GalaxyInfo['id'])
				{  
					$Result .= "<a href=# onclick=\"javascript:doit(6, ".$this->TargetUniverse.", ".$this->TargetGalaxy.", ".$this->TargetSystem.", ".$this->TargetPlanet.", 1, ".$user["spio_anz"].");\" >";
				  
        	      $Result .= "<img src=". $dpath ."img/e.gif title=\"".$lang['gl_spy']."\" border=0></a>";
					$Result .= "&nbsp;";
				}
				if ($user["settings_wri"] == "1" && $GalaxyInfo['id'])
				{
					$Result .= "<a href=game.php?page=messages&mode=write&id=".$GalaxyInfo["id"].">";
					$Result .= "<img src=". $dpath ."img/m.gif title=\"".$lang['write_message']."\" border=0></a>";
					$Result .= "&nbsp;";
				}
				if ($user["settings_bud"] == "1" && $GalaxyInfo['id'])
				{
					$Result .= "<a href=game.php?page=buddy&mode=2&u=".$GalaxyInfo['id']." >";
					$Result .= "<img src=". $dpath ."img/b.gif title=\"".$lang['gl_buddy_request']."\" border=0></a>";
					$Result .= "&nbsp;";
				}
				if ($user["settings_mis"] == "1" && $MissileBtn == true && $GalaxyInfo['id'])
				{	
					$Result .= "<a href=game.php?page=galaxy&mode=2&universe=".$this->TargetUniverse."&galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&current=".$user['current_planet']." >";
				
        	      $Result .= "<img src=". $dpath ."img/r.gif title=\"".$lang['gl_missile_attack']."\" border=0></a>";
				}
			}
		}
		$Result .= "</th>";

		return $Result;
	}

		public function GalaxyRowAlly($GalaxyInfo)
	{
		global $user, $lang;

		$Result  = "<th width=80>";

		if ($GalaxyInfo['ally_id'] && $GalaxyInfo['ally_id'] != 0)
		{
			if ($GalaxyInfo['ally_members'] > 1)

			{
				$add = $lang['gl_member_add'];
			}
			else
			{
				$add = "";
			}

			$Result .= "<a style=\"cursor: pointer;\"";
			$Result .= " onmouseover='return overlib(\"";
			$Result .= "<table width=240>";
			$Result .= "<tr>"; 
			$Result .= "<td class=c>".$lang['gl_alliance']. " " . $GalaxyInfo['ally_name'] . $lang['gl_with'] . $GalaxyInfo['ally_members'] . $lang['gl_member'] . $add ."</td>";
			$Result .= "</tr>";
			$Result .= "<th>";
			$Result .= "<table>";
			$Result .= "<tr>";
			$Result .= "<td><a href=game.php?page=alliance&mode=ainfo&a=". $GalaxyInfo['ally_id'] .">".$lang['gl_alliance_page']."</a></td>";
			//$Result .= "<td><a href=game.php?page=alliance&mode=ainfo&a=". $GalaxyInfo['id'] .">".$lang['gl_alliance_page']."</a></td>";
			$Result .= "</tr><tr>";
			$Result .= "<td><a href=game.php?page=statistics&start=101&who=ally>".$lang['gl_see_on_stats']."</a></td>";
			if ($GalaxyInfo["ally_web"] != "")
			{
				$Result .= "</tr><tr>";
				$Result .= "<td><a href=". $GalaxyInfo["ally_web"] ." target=_new>".$lang['gl_alliance_web_page']."</td>";
			}
			$Result .= "</tr>";
			$Result .= "</table>";
			$Result .= "</th>";
			$Result .= "</table>\"";
			$Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
			$Result .= " onmouseout='return nd();'>";
			if ($user['ally_id'] == $GalaxyInfo['ally_id'])
			{
				$Result .= "<span class=\"allymember\">". $GalaxyInfo['ally_tag'] ."</span></a>";
			}
			elseif ($GalaxyInfo['ally_id'] == $user['ally_id'])
			{
				$Result .= "<font color=lime>".$GalaxyInfo['ally_tag'] ."</font></a>";
			}
			else
			{
				$Result .= $GalaxyInfo['ally_tag'] ."</a>";
			}
		}

		$Result .= "</th>";
		return $Result;
	}

	public function GalaxyRowDebris($GalaxyInfo)
	{
		global $dpath, $user, $pricelist, $lang;

		$Result  = "<th style=\"white-space: nowrap;\" width=30>";
		if ($GalaxyInfo)
		{
			if ($GalaxyInfo["metal"] != 0 || $GalaxyInfo["crystal"] != 0)
			{
				$RecNeeded = ceil(($GalaxyInfo["metal"] + $GalaxyInfo["crystal"]) / $pricelist[209]['capacity']);

				if ($RecNeeded < $this->CurrentRC)
					$RecSended = $RecNeeded;
				elseif ($RecNeeded >= $this->CurrentRC)
					$RecSended = $this->CurrentRC;
				else
					$RecSended = $RecyclerCount;//?

				$Result .= "<a style=\"cursor: pointer;\"";
				$Result .= " onmouseover='return overlib(\"";
				$Result .= "<table width=240>";
				$Result .= "<tr>";
				$Result .= "<td class=c colspan=2>";	
				$Result .= $lang['gl_debris_field'] . "[".$this->TargetUniverse.":".$this->TargetGalaxy.":".$this->TargetSystem.":".$this->TargetPlanet."]";
			  
            $Result .= "</td>";
				$Result .= "</tr><tr>";
				$Result .= "<th width=80>";
				$Result .= "<img src=". $dpath ."planeten/debris.jpg height=75 width=75 />";
				$Result .= "</th>";
				$Result .= "<th>";
				$Result .= "<table>";
				$Result .= "<tr>";
				$Result .= "<td class=c colspan=2>".$lang['gl_resources'].":</td>";
				$Result .= "</tr><tr>";
				$Result .= "<th>".$lang['Metal'].": </th><th>". number_format( $GalaxyInfo['metal'], 0, '', '.') ."</th>";
				$Result .= "</tr><tr>";
				$Result .= "<th>".$lang['Crystal'].": </th><th>". number_format( $GalaxyInfo['crystal'], 0, '', '.') ."</th>";
				$Result .= "</tr><tr>";
				$Result .= "<td class=c colspan=2>".$lang['gl_actions'].":</td>";
				$Result .= "</tr><tr>";
				$Result .= "<th colspan=2 align=left>";	
				$Result .= "<a href= # onclick=&#039javascript:doit (8,".$this->TargetUniverse.", ".$this->TargetGalaxy.", ".$this->TargetSystem.", ".$this->TargetPlanet.", 2, ".$RecSended."); return nd();&#039 >".$lang['gl_collect']."</a>";
			 
         	$Result .= "</tr>";
				$Result .= "</table>";
				$Result .= "</th>";
				$Result .= "</tr>";
				$Result .= "</table>\"";
				$Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
				$Result .= " onmouseout='return nd();'>";
				$Result .= "<img src=". $dpath ."planeten/debris.jpg height=22 width=22></a>";
			}
		}
		$Result .= "</th>";
		return $Result;
	}

	public function GalaxyRowMoon($GalaxyInfo)
	{
		global $user, $dpath, $lang;

		$Result  = "<th style=\"white-space: nowrap;\" width=30>";
		if ($GalaxyInfo['id'] != $user['id'])
			$MissionType6Link = "<a href=# onclick=&#039javascript:doit(6,".$this->TargetUniverse.", ".$this->TargetGalaxy.", ".$this->TargetSystem.", ".$this->TargetPlanet.", 3, ".$user["spio_anz"].");&#039 >".$lang['type_mission'][6]."</a><br /><br />";
		
    elseif ($GalaxyInfo['id'] == $user['id'])
			$MissionType6Link = "";

		if ($GalaxyInfo['id'] != $user['id'])
			$MissionType1Link = "<a href=game.php?page=fleet&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&amp;system=".$this->TargetSystem."&amp;planet=".$this->TargetPlanet."&amp;planettype=3&amp;target_mission=1>".$lang['type_mission'][1]."</a><br />";
	    
  	elseif ($GalaxyInfo['id'] == $user['id'])
			$MissionType1Link = "";

		if ($GalaxyInfo['id'] != $user['id'])	
			$MissionType5Link = "<a href=game.php?page=fleet&&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=3&target_mission=5>".$lang['type_mission'][5]."</a><br />";
	  
  	elseif ($GalaxyInfo['id'] == $user['id'])
			$MissionType5Link = "";

		if ($GalaxyInfo['id'] == $user['id'])	
			$MissionType4Link = "<a href=game.php?page=fleet&&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=3&target_mission=4>".$lang['type_mission'][4]."</a><br />";
	  
  	elseif ($GalaxyInfo['id'] != $user['id'])
			$MissionType4Link = "";

		if ($GalaxyInfo['id'] != $user['id'])
			if ($this->CanDestroy > 0)	
				$MissionType9Link = "<a href=game.php?page=fleet&&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=3&target_mission=9>".$lang['type_mission'][9]."</a>";
	    
  	else
			$MissionType9Link = "";
		elseif ($GalaxyInfo['id'] == $user['id'])
			$MissionType9Link = "";
    
		$MissionType3Link = "<a href=game.php?page=fleet&&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=".$PlanetType."&target_mission=3>".$lang['type_mission'][3]."</a><br />";
    
		if ($GalaxyInfo && $GalaxyInfo["destruyed"] == 0 && $GalaxyRow["id_luna"] != 0)
		{
			$Result .= "<a style=\"cursor: pointer;\"";
			$Result .= " onmouseover='return overlib(\"";
			$Result .= "<table width=240>";
			$Result .= "<tr>";
			$Result .= "<td class=c colspan=2>";		
			$Result .= $lang['gl_moon'] . " ".$GalaxyInfo["name"]." [".$this->TargetUniverse.":".$this->TargetGalaxy.":".$this->TargetSystem.":".$this->TargetPlanet."]";
			
            $Result .= "</td>";
			$Result .= "</tr><tr>";
			$Result .= "<th width=80>";
			$Result .= "<img src=". $dpath ."planeten/mond.jpg height=75 width=75 />";
			$Result .= "</th>";
			$Result .= "<th>";
			$Result .= "<table>";
			$Result .= "<tr>";
			$Result .= "<td class=c colspan=2>".$lang['gl_features']."</td>";
			$Result .= "</tr><tr>";
			$Result .= "<th>".$lang['gl_diameter']."</th>";
			$Result .= "<th>". number_format($GalaxyRowPlanet['diameter'], 0, '', '.') ."</th>";
			$Result .= "</tr><tr>";
			$Result .= "<th>".$lang['gl_temperature']."</th><th>". number_format($GalaxyInfo['temp_min'], 0, '', '.') ."</th>";
			$Result .= "</tr><tr>";
			$Result .= "<td class=c colspan=2>".$lang['gl_actions']."</td>";
			$Result .= "</tr><tr>";
			$Result .= "<th colspan=2 align=center>";
			$Result .= $MissionType6Link;
			$Result .= $MissionType3Link;
			$Result .= $MissionType4Link;
			$Result .= $MissionType1Link;
			$Result .= $MissionType5Link;
			$Result .= $MissionType9Link;
			$Result .= "</tr>";
			$Result .= "</table>";
			$Result .= "</th>";
			$Result .= "</tr>";
			$Result .= "</table>\"";
			$Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
			$Result .= " onmouseout='return nd();'>";
			$Result .= "<img src=". $dpath ."planeten/small/s_mond.jpg height=22 width=22>";
			$Result .= "</a>";
		}
		$Result .= "</th>";
		return $Result;
	}
	
   public function GalaxyRowPlanet($GalaxyInfo)
	{
		global $dpath, $user, $game_config, $lang;

		$Result  = "<th width=30>";
		
      if ($GalaxyInfo && $GalaxyInfo["destruyed"] == 0 && $GalaxyInfo["id_planet"] != 0)
		{
			if ($this->HavePhalanx <> 0)
			{
				if ($GalaxyInfo['id'] != $user['id'])
				{
					if ($GalaxyInfo["galaxy"] == $this->CurrentGalaxy)
					{
						$PhRange = $this->GetPhalanxRange ();
						$SystemLimitMin = $this->CurrentSystem - $PhRange;
						if ($SystemLimitMin < 1)
							$SystemLimitMin = 1;

						$SystemLimitMax = $this->CurrentSystem + $PhRange;
						if ($this->TargetSystem <= $SystemLimitMax)
						{
							if ($this->TargetSystem >= $SystemLimitMin)
                        $PhalanxTypeLink = "<a href=# onclick=fenster('game.php?page=phalanx&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&amp;system=".$this->TargetSystem."&amp;planet=".$this->TargetPlanet."&amp;planettype=1') >".$lang['gl_phalanx']."</a><br />";
                        
							else
								$PhalanxTypeLink = "";
						}
						else
						{
							$PhalanxTypeLink = "";
						}
					}
					else
					{
						$PhalanxTypeLink = "";
					}
				}
				else
				{
					$PhalanxTypeLink = "";
				}
			}
			else
			{
				$PhalanxTypeLink = "";
			}

			if ($this->CurrentMIP <> 0)
			{
				if ($GalaxyInfo['id'] != $user['id'])
				{
					if ($GalaxyInfo["galaxy"] == $this->CurrentGalaxy)
					{
						$MiRange = $this->GetMissileRange();
						$SystemLimitMin = $this->CurrentSystem - $MiRange;
						if ($SystemLimitMin < 1)
							$SystemLimitMin = 1;

						$SystemLimitMax = $this->CurrentSystem + $MiRange;

						if ($this->TargetSystem <= $SystemLimitMax)
						{
							if ($this->TargetSystem >= $SystemLimitMin)
								$MissileBtn = true;
							else
								$MissileBtn = false;
						}
						else
						{
							$MissileBtn = false;
						}
					}
					else
					{
						$MissileBtn = false;
					}
				}
				else
				{
					$MissileBtn = false;
				}
			}
			else
			{
				$MissileBtn = false;
			}

			if ($GalaxyInfo['id'] != $user['id'])
				$MissionType6Link = "<a href=# onclick=&#039javascript:doit(6, ".$this->TargetUniverse.", ".$this->TargetGalaxy.", ".$this->TargetSystem.", ".$this->TargetPlanet.", 1, ".$user["spio_anz"].");&#039 >".$lang['type_mission'][6]."</a><br /><br />";
		 
    	elseif ($GalaxyInfo['id'] == $user['id'])
				$MissionType6Link = "";

			if ($GalaxyInfo['id'] != $user['id'])
				$MissionType1Link = "<a href=game.php?page=fleet&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&amp;system=".$this->TargetSystem."&amp;planet=".$this->TargetPlanet."&amp;planettype=1&amp;target_mission=1>".$lang['type_mission'][1]."</a><br />";
		 
    	elseif ($GalaxyInfo['id'] == $user['id'])
				$MissionType1Link = "";

			if ($GalaxyInfo['id'] == $user['id'])
				$MissionType5Link = "<a href=game.php?page=fleet&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=1&target_mission=5>".$lang['type_mission'][5]."</a><br />";
			
      elseif ($GalaxyInfo['id'] == $user['id'])
				$MissionType5Link = "";

			if ($GalaxyInfo['id'] == $user['id'])
				$MissionType4Link = "<a href=game.php?page=fleet&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=1&target_mission=4>".$lang['type_mission'][4]."</a><br />";
			
      elseif ($GalaxyInfo['id'] != $user['id'])
				$MissionType4Link = "";

			if ($user["settings_mis"] == "1" AND $MissileBtn == true && $GalaxyInfo['id'])
				$MissionType10Link = "<a href=game.php?page=galaxy&mode=2&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&current=".$user['current_planet']." >".$lang['gl_missile_attack']."</a><br />";
		  
    	elseif ($GalaxyInfo['id'] != $user['id'])
				$MissionType10Link = "";
       
			$MissionType3Link = "<a href=game.php?page=fleet&galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=1&target_mission=3>".$lang['type_mission'][3]."</a><br />";
       
			$Result .= "<a style=\"cursor: pointer;\"";
			$Result .= " onmouseover='return overlib(\"";
			$Result .= "<table width=240>";
			$Result .= "<tr>";
			$Result .= "<td class=c colspan=2>";
			$Result .= $lang['gl_planet'] . " " . $GalaxyInfo["name"] ." [".$this->TargetUniverse.":".$this->TargetGalaxy.":".$this->TargetSystem.":".$this->TargetPlanet."]";
			
         $Result .= "</td>";
			$Result .= "</tr>";
			$Result .= "<tr>";
			$Result .= "<th width=80>";
			$Result .= "<img src=". $dpath ."planeten/small/s_". $GalaxyInfo["image"] .".jpg height=75 width=75 />";
			$Result .= "</th>";
			$Result .= "<th align=left>";
			$Result .= $MissionType6Link;
			$Result .= $PhalanxTypeLink;
			$Result .= $MissionType1Link;
			$Result .= $MissionType5Link;
			$Result .= $MissionType4Link;
			$Result .= $MissionType3Link;
			$Result .= $MissionType10Link;
			$Result .= "</th>";
			$Result .= "</tr>";
			$Result .= "</table>\"";
			$Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
			$Result .= " onmouseout='return nd();'>";
			$Result .= "<img src=".   $dpath ."planeten/small/s_". $GalaxyInfo["image"] .".jpg height=30 width=30>";
			$Result .= "</a>";
		}
		$Result .= "</th>";

		return $Result;
	}
	
	public function GalaxyRowPlanetName($GalaxyInfo)
	{
		global $user, $lang;

		$Result  = "<th style=\"white-space: nowrap;\" width=130>";

		if ($GalaxyInfo['last_update'] > (time()-59 * 60) && $GalaxyInfoUser['id'] != $user['id'])
		{
         $Inactivity = pretty_time_hour(time() - $GalaxyInfo['last_update']);
      }
      
		if ($GalaxyInfo && $GalaxyInfo["destruyed"] == 0)
		{
			if ($this->HavePhalanx <> 0)
			{
				if ($GalaxyInfo["galaxy"] == $this->CurrentGalaxy)
				{
					$Range = $this->GetPhalanxRange ();
					if ($this->CurrentGalaxy + $Range <= $this->CurrentSystem && $this->CurrentSystem >= $this->CurrentGalaxy - $Range)
						$PhalanxTypeLink = "<a href=# onclick=fenster('game.php?page=phalanx&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&amp;system=".$this->TargetSystem."&amp;planet=".$this->TargetPlanet."&amp;planettype=1')  title=\"Phalanx\">".$GalaxyInfo['name']."</a><br />";
					
               else
						$PhalanxTypeLink = $GalaxyInfo['name'];
				}
				else
				{
					$PhalanxTypeLink = $GalaxyInfo['name'];
				}
			}
			else
			{
				$PhalanxTypeLink = $GalaxyInfo['name'];
			}

			$Result .= $TextColor . $PhalanxTypeLink . $EndColor;

			if ($GalaxyInfo['last_update']  > (time()-59 * 60) && $GalaxyInfoUser['id'] != $user['id'])
			{
				if ($GalaxyInfo['last_update']  > (time()-10 * 60) && $GalaxyInfoUser['id'] != $user['id'])
				{	
               $Result .= "(*)";
            }
				else
				{
					$Result .= " (".$Inactivity.")";
				}
			}
		}
		elseif($GalaxyInfo["destruyed"] != 0)
		{
			$Result .= $lang['gl_planet_destroyed'];
		}

		$Result .= "</th>";

		return $Result;
	}

	public function GalaxyRowPos($GalaxyInfo)
	{
		$Result  = "<th width=30>";
		$Result .= "<a href=\"game.php?page=fleet&universe=".$this->TargetUniverse."&amp;galaxy=".$this->TargetGalaxy."&system=".$this->TargetSystem."&planet=".$this->TargetPlanet."&planettype=0&target_mission=7\"";
	  
  	   if ($GalaxyInfo)
  	   {    
			$Result .= " tabindex=\"". ($this->TargetPlanet + 1) ."\"";
		}
		$Result .= ">". $this->TargetPlanet ."</a>";
		$Result .= "</th>";

		return $Result;
	}

	public function GalaxyRowUser($GalaxyInfo)
	{
		global $game_config, $user, $lang;

		$Result = "<th width=150>";

		if ($GalaxyInfo && $GalaxyInfo["destruyed"] == 0)
		{
			$protection      	= $game_config['noobprotection'];
			$protectiontime  	= $game_config['noobprotectiontime'];
			$protectionmulti 	= $game_config['noobprotectionmulti'];
			$MyGameLevel		= $UserPoints['total_points'];
			$HeGameLevel		= $GalaxyInfo['total_points'];

			if ($GalaxyInfo['bana'] == 1 && $GalaxyInfo['urlaubs_modus'] == 1)
			{
				$Systemtatus2 	= "v <a href=\"game.php?page=banned\"><span class=\"banned\">".$lang['gl_b']."</span></a>";
				$Systemtatus 	= "<span class=\"vacation\">";
			}
			elseif ($GalaxyInfo['bana'] == 1)
			{
				$Systemtatus2 	= "<a href=\"game.php?page=banned\"><span class=\"banned\">".$lang['gl_b']."</span></a>";
				$Systemtatus 	= "";
			}
			elseif ($GalaxyInfo['urlaubs_modus'] == 1)
			{
				$Systemtatus2 	= "<span class=\"vacation\">".$lang['gl_v']."</span>";
				$Systemtatus 	= "<span class=\"vacation\">";
			}
			elseif ($GalaxyInfo['onlinetime'] < (time()-60 * 60 * 24 * 7) && $GalaxyInfo['onlinetime'] > (time()-60 * 60 * 24 * 28))
			{
				$Systemtatus2 	= "<span class=\"inactive\">".$lang['gl_i']."</span>";
				$Systemtatus 	= "<span class=\"inactive\">";
			}
			elseif ($GalaxyInfo['onlinetime'] < (time()-60 * 60 * 24 * 28))
			{
				$Systemtatus2 	= "<span class=\"inactive\">".$lang['gl_i']."</span><span class=\"longinactive\">".$lang['gl_I']."</span>";
				$Systemtatus 	= "<span class=\"longinactive\">";
			}
			elseif (($MyGameLevel > ($HeGameLevel * $protectionmulti)) && $protection == 1 && ($HeGameLevel < $protectiontime))
			{
				$Systemtatus2 	= "<span class=\"noob\">".$lang['gl_w']."</span>";
				$Systemtatus 	= "<span class=\"noob\">";
			}
			elseif ((($MyGameLevel * $protectionmulti) < $HeGameLevel) && $protection == 1 && ($MyGameLevel < $protectiontime))
			{
				$Systemtatus2 	= $lang['gl_s'];
				$Systemtatus 	= "<span class=\"strong\">";
			}
			else
			{
				$Systemtatus2 	= "";
				$Systemtatus 	= "";
			}
			$Systemtatus4 		= $GalaxyInfo['total_rank'];

			if ($Systemtatus2 != '')
			{
				$Systemtatus6 	= "<font color=\"white\">(</font>";
				$Systemtatus7 	= "<font color=\"white\">)</font>";
			}
			if ($Systemtatus2 == '')
			{
				$Systemtatus6 	= "";
				$Systemtatus7 	= "";
			}

			$Systemtart = $GalaxyInfo['total_rank'];

			if (strlen($Systemtart) < 3)
				$Systemtart = 1;
			else
				$Systemtart = (floor( $GalaxyInfo['total_rank'] / 100 ) * 100) + 1;

			$Result .= "<a style=\"cursor: pointer;\"";
			$Result .= " onmouseover='return overlib(\"";
			$Result .= "<table width=190>";
			$Result .= "<tr>";
			$Result .= "<td class=c colspan=2>". $lang['gl_player'] .$GalaxyInfo['username']. $lang['gl_in_the_rank'] .$Systemtatus4."</td>";
			$Result .= "</tr><tr>";
			if ($GalaxyInfo['id'] != $user['id'])
			{
				$Result .= "<td><a href=game.php?page=messages&mode=write&id=".$GalaxyInfo['id'].">".$lang['write_message']."</a></td>";
				$Result .= "</tr><tr>";
				$Result .= "<td><a href=game.php?page=buddy&mode=2&u=".$GalaxyInfo['id'].">".$lang['gl_buddy_request']."</a></td>";
				$Result .= "</tr><tr>";
			}
			$Result .= "<td><a href=game.php?page=statistics&who=player&start=".$Systemtart.">".$lang['gl_stat']."</a></td>";
			$Result .= "</tr>";
			$Result .= "</table>\"";
			$Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
			$Result .= " onmouseout='return nd();'>";
			$Result .= $Systemtatus;
			$Result .= $GalaxyInfo["username"]."</span>";
			$Result .= $Systemtatus6;
			$Result .= $Systemtatus;
			$Result .= $Systemtatus2;
			$Result .= $Systemtatus7." ".$admin;
			$Result .= "</span></a>";
		}
		$Result .= "</th>";

		return $Result;
	}
}
?>