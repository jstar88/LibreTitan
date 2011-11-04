<?php

/**
 *  LibreTitan
 *  Copyright (C) 2011  Jstar,Tomtom
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Jstar 
 * @copyright 2009 => Lucky  XGProyect
 * @copyright 2011 => Jstar,Tomtom  Fork/LibreTitan
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @link https://github.com/jstar88/LibreTitan
 */

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}

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

    public function __construct($CurrentUser, $CurrentPlanet)
    {
        global $resource;
        $this->CurrentUniverse = $CurrentPlanet['universe'];
        $this->CurrentSystem = $CurrentPlanet['system'];
        $this->CurrentGalaxy = $CurrentPlanet['galaxy'];
        $this->CurrentPlanet = $CurrentPlanet['planet'];
        $this->CurrentPlanetType = $CurrentPlanet["planet_type"];
        $this->CurrentPlanetId = $CurrentPlanet['id'];

        $this->TargetUniverse = $this->CurrentUniverse;
        $this->TargetGalaxy = $this->CurrentGalaxy;
        $this->TargetSystem = $this->CurrentSystem;

        $this->fleetmax = $CurrentUser['computer_tech'] + 1 + $CurrentUser['rpg_commandant'] * COMMANDANT;
        $this->CurrentPlID = $CurrentPlanet['id'];
        $this->CurrentMIP = $CurrentPlanet['interplanetary_misil'];
        $this->CurrentRC = $CurrentPlanet['recycler'];
        $this->CurrentSP = $CurrentPlanet['spy_sonde'];
        $this->HavePhalanx = $CurrentPlanet['phalanx'];
        $this->CurrentUserId = $CurrentUser['id'];
        $this->CanDestroy = $CurrentPlanet[$resource[213]] + $CurrentPlanet[$resource[214]];
        $this->interplanetary_misil = $CurrentPlanet['interplanetary_misil'];
        $this->CurrentDeuterium = $CurrentPlanet['deuterium'];

        $row = mysql_fetch_object(doquery("SELECT COUNT(*) as total FROM {{table}} WHERE `fleet_owner` = '" . $CurrentUser['id'] . "';", 'fleets'));
        $this->maxfleet_count = $row->total;
    }

    private function canSendMissile()
    {
        global $resource, $user;
        if ($this->CurrentMIP > 0 && $this->TargetGalaxy == $this->CurrentGalaxy)
        {
            $Range = Formules::getMissileRange($user[$resource[117]]);
            $SystemLimitMin = max(1, $this->CurrentSystem - $Range);
            $SystemLimitMax = min($this->CurrentSystem + $Range, MAX_SYSTEM_IN_GALAXY);
            if ($this->TargetSystem <= $SystemLimitMax || $this->TargetSystem >= $SystemLimitMin)
            {
                return true;
            }
        }
        return false;
    }

    private function canPhalanx()
    {
        if ($this->TargetGalaxy == $this->CurrentGalaxy)
        {
            $PhRange = Formules::getPhalanxRange($this->HavePhalanx);
            $SystemLimitMin = $this->CurrentSystem - $PhRange;
            $SystemLimitMin = max(1, $this->CurrentSystem - $Range);
            $SystemLimitMax = min($this->CurrentSystem + $Range, MAX_SYSTEM_IN_GALAXY);
            if ($this->TargetSystem <= $SystemLimitMax || $this->TargetSystem >= $SystemLimitMin)
            {
                return true;
            }
        }
        return false;
    }

    public function CheckAbandonMoonState(&$lunarow)
    {
        if ($lunarow['destruyed_moon'] <= time() && $lunarow['destruyed_moon'] != 0)
        {
            $QryUpdateGalaxy = "UPDATE {{table}} SET `id_luna` = '0',`destruyed_moon` = '0' WHERE `galaxy` = '" . $this->TargetGalaxy . "' AND `system` = '" . $this->TargetSystem . "' AND `planet` = '" . $lunarow['planet'] . "' LIMIT 1;";
            doquery($QryUpdateGalaxy, 'galaxy');
        }
    }

    public function CheckAbandonPlanetState(&$planet)
    {
        if ($planet['destruyed'] <= time())
        {
            doquery("DELETE FROM {{table}} WHERE `id_planet` = '" . $planet['id_planet'] . "' LIMIT 1;", 'galaxy');
            doquery("DELETE FROM {{table}} WHERE `id` = '" . $planet['id_planet'] . "'", 'planets');
        }
    }

    public function GalaxyRowActions(&$RowInfo)
    {
        global $user, $dpath, $engine;

        $Result = "<th style=\"white-space: nowrap;\" width=125>";

        if ($RowInfo['id'] != $user['id'])
        {
            return "</th>";
        }

        if ($RowInfo["destruyed"] == 0 && !empty($RowInfo['id']))
        {
            if ($user["settings_esp"] == "1")
            {
                $Result .= "<a href=# onclick=\"javascript:doit(6, " . $this->TargetUniverse . ", " . $this->TargetGalaxy . ", " . $this->TargetSystem . ", " . $RowInfo['planet'] . ", 1, " . $user["spio_anz"] . ");\" >";
                $Result .= "<img src=" . $dpath . "img/e.gif title=\"" . $engine->get('gl_spy') . "\" border=0></a>";
                $Result .= "&nbsp;";
            }
            if ($user["settings_wri"] == "1")
            {
                $Result .= "<a href=game.php?page=messages&mode=write&id=" . $RowInfo["id"] . ">";
                $Result .= "<img src=" . $dpath . "img/m.gif title=\"" . $engine->get('write_message') . "\" border=0></a>";
                $Result .= "&nbsp;";
            }
            if ($user["settings_bud"] == "1")
            {
                $Result .= "<a href=game.php?page=buddy&mode=2&u=" . $RowInfo['id'] . " >";
                $Result .= "<img src=" . $dpath . "img/b.gif title=\"" . $engine->get('gl_buddy_request') . "\" border=0></a>";
                $Result .= "&nbsp;";
            }
            if ($user["settings_mis"] == "1" && $this->canSendMissile())
            {
                $Result .= "<a href=game.php?page=galaxy&mode=2&universe=" . $this->TargetUniverse . "&galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&current=" . $user['current_planet'] . " >";
                $Result .= "<img src=" . $dpath . "img/r.gif title=\"" . $engine->get('gl_missile_attack') . "\" border=0></a>";
            }
        }
        $Result .= "</th>";

        return $Result;
    }

    public function GalaxyRowAlly(&$RowInfo)
    {
        global $user, $engine;

        $Result = "<th width=80>";

        if ($RowInfo['ally_id'] && $RowInfo['ally_id'] != 0)
        {
            if ($RowInfo['ally_members'] > 1)
            {
                $add = $engine->get('gl_member_add');
            }
            else
            {
                $add = "";
            }

            $Result .= "<a style=\"cursor: pointer;\"";
            $Result .= " onmouseover='return overlib(\"";
            $Result .= "<table width=240>";
            $Result .= "<tr>";
            $Result .= "<td class=c>" . $engine->get('gl_alliance') . " " . $RowInfo['ally_name'] . $engine->get('gl_with') . $RowInfo['ally_members'] . $engine->get('gl_member') . $add . "</td>";
            $Result .= "</tr>";
            $Result .= "<th>";
            $Result .= "<table>";
            $Result .= "<tr>";
            $Result .= "<td><a href=game.php?page=alliance&mode=ainfo&a=" . $RowInfo['ally_id'] . ">" . $engine->get('gl_alliance_page') . "</a></td>";
            //$Result .= "<td><a href=game.php?page=alliance&mode=ainfo&a=". $RowInfo['id'] .">".$engine->get('gl_alliance_page']."</a></td>";
            $Result .= "</tr><tr>";
            $Result .= "<td><a href=game.php?page=statistics&start=101&who=ally>" . $engine->get('gl_see_on_stats') . "</a></td>";
            if ($RowInfo["ally_web"] != "")
            {
                $Result .= "</tr><tr>";
                $Result .= "<td><a href=" . $RowInfo["ally_web"] . " target=_new>" . $engine->get('gl_alliance_web_page') . "</td>";
            }
            $Result .= "</tr>";
            $Result .= "</table>";
            $Result .= "</th>";
            $Result .= "</table>\"";
            $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
            $Result .= " onmouseout='return nd();'>";
            if ($user['ally_id'] == $RowInfo['ally_id'])
            {
                $Result .= "<span class=\"allymember\">" . $RowInfo['ally_tag'] . "</span></a>";
            } elseif ($RowInfo['ally_id'] == $user['ally_id'])
            {
                $Result .= "<font color=lime>" . $RowInfo['ally_tag'] . "</font></a>";
            }
            else
            {
                $Result .= $RowInfo['ally_tag'] . "</a>";
            }
        }

        $Result .= "</th>";
        return $Result;
    }

    public function GalaxyRowDebris(&$RowInfo)
    {
        global $dpath, $user, $pricelist, $engine;

        $Result = "<th style=\"white-space: nowrap;\" width=30>";
        if (!empty($RowInfo["metal"]) || !empty($RowInfo["crystal"]))
        {
            $RecNeeded = ceil(($RowInfo["metal"] + $RowInfo["crystal"]) / $pricelist[209]['capacity']);
            $RecSended = min($this->CurrentRC, $RecNeeded);

            $Result .= "<a style=\"cursor: pointer;\"";
            $Result .= " onmouseover='return overlib(\"";
            $Result .= "<table width=240>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>";
            $Result .= $engine->get('gl_debris_field') . "[" . $this->TargetUniverse . ":" . $this->TargetGalaxy . ":" . $this->TargetSystem . ":" . $RowInfo['planet'] . "]";
            $Result .= "</td>";
            $Result .= "</tr><tr>";
            $Result .= "<th width=80>";
            $Result .= "<img src=" . $dpath . "planeten/debris.jpg height=75 width=75 />";
            $Result .= "</th>";
            $Result .= "<th>";
            $Result .= "<table>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>" . $engine->get('gl_resources') . ":</td>";
            $Result .= "</tr><tr>";
            $Result .= "<th>" . $engine->get('Metal') . ": </th><th>" . number_format($RowInfo['metal'], 0, '', '.') . "</th>";
            $Result .= "</tr><tr>";
            $Result .= "<th>" . $engine->get('Crystal') . ": </th><th>" . number_format($RowInfo['crystal'], 0, '', '.') . "</th>";
            $Result .= "</tr><tr>";
            $Result .= "<td class=c colspan=2>" . $engine->get('gl_actions') . ":</td>";
            $Result .= "</tr><tr>";
            $Result .= "<th colspan=2 align=left>";
            $Result .= "<a href= # onclick=&#039javascript:doit (8," . $this->TargetUniverse . ", " . $this->TargetGalaxy . ", " . $this->TargetSystem . ", " . $RowInfo['planet'] . ", 2, " . $RecSended . "); return nd();&#039 >" . $engine->get('gl_collect') . "</a>";
            $Result .= "</tr>";
            $Result .= "</table>";
            $Result .= "</th>";
            $Result .= "</tr>";
            $Result .= "</table>\"";
            $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
            $Result .= " onmouseout='return nd();'>";
            $Result .= "<img src=" . $dpath . "planeten/debris.jpg height=22 width=22></a>";
        }
        $Result .= "</th>";
        return $Result;
    }

    public function GalaxyRowMoon(&$RowInfo)
    {
        global $user, $dpath, $engine;

        $Result = "<th style=\"white-space: nowrap;\" width=30>";

        if ($RowInfo['id'] != $user['id'])
        {
            $MissionType1Link = "<a href=game.php?page=fleet&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&amp;system=" . $this->TargetSystem . "&amp;planet=" . $RowInfo['planet'] . "&amp;planettype=3&amp;target_mission=1>" . $engine->get('type_mission', 1) . "</a><br />";
            $MissionType3Link = "<a href=game.php?page=fleet&&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=3&target_mission=3>" . $engine->get('type_mission', 3) . "</a><br />";
            $MissionType4Link = "";
            $MissionType5Link = "<a href=game.php?page=fleet&&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=3&target_mission=5>" . $engine->get('type_mission', 5) . "</a><br />";
            $MissionType6Link = "<a href=# onclick=&#039javascript:doit(6," . $this->TargetUniverse . ", " . $this->TargetGalaxy . ", " . $this->TargetSystem . ", " . $RowInfo['planet'] . ", 3, " . $user["spio_anz"] . ");&#039 >" . $engine->get('type_mission', 6) . "</a><br /><br />";
            if ($this->CanDestroy > 0)
                $MissionType9Link = "<a href=game.php?page=fleet&&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=3&target_mission=9>" . $engine->get('type_mission', 9) . "</a>";
            else
                $MissionType9Link = "";
        }
        else
        {
            $MissionType1Link = "";
            $MissionType3Link = "<a href=game.php?page=fleet&&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=3&target_mission=3>" . $engine->get('type_mission', 3) . "</a><br />";
            $MissionType4Link = "<a href=game.php?page=fleet&&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=3&target_mission=4>" . $engine->get('type_mission', 4) . "</a><br />";
            $MissionType5Link = "";
            $MissionType6Link = "";
            $MissionType9Link = "";
        }

        if ($RowInfo["destruyed"] == 0 && $RowInfo["id_luna"] != 0)
        {
            $Result .= "<a style=\"cursor: pointer;\"";
            $Result .= " onmouseover='return overlib(\"";
            $Result .= "<table width=240>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>";
            $Result .= $engine->get('gl_moon') . " " . $RowInfo["name"] . " [" . $this->TargetUniverse . ":" . $this->TargetGalaxy . ":" . $this->TargetSystem . ":" . $RowInfo['planet'] . "]";
            $Result .= "</td>";
            $Result .= "</tr><tr>";
            $Result .= "<th width=80>";
            $Result .= "<img src=" . $dpath . "planeten/mond.jpg height=75 width=75 />";
            $Result .= "</th>";
            $Result .= "<th>";
            $Result .= "<table>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>" . $engine->get('gl_features') . "</td>";
            $Result .= "</tr><tr>";
            $Result .= "<th>" . $engine->get('gl_diameter') . "</th>";
            $Result .= "<th>" . number_format($RowInfo['diameter'], 0, '', '.') . "</th>";
            $Result .= "</tr><tr>";
            $Result .= "<th>" . $engine->get('gl_temperature') . "</th><th>" . number_format($RowInfo['temp_min'], 0, '', '.') . "</th>";
            $Result .= "</tr><tr>";
            $Result .= "<td class=c colspan=2>" . $engine->get('gl_actions') . "</td>";
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
            $Result .= "<img src=" . $dpath . "planeten/small/s_mond.jpg height=22 width=22>";
            $Result .= "</a>";
        } elseif ($RowInfo["destruyed_moon"] != 0 && $RowInfo["id_luna"] != 0)
        {
            $Result .= "<img src=" . $dpath . "planeten/small/s_mond.jpg height=22 width=22 style=\"border:1px solid red;\">";
        }
        $Result .= "</th>";
        return $Result;
    }

    public function GalaxyRowPlanet(&$RowInfo)
    {
        global $dpath, $user, $engine;

        $Result = "<th width=30>";

        if ($RowInfo["destruyed"] == 0 && $RowInfo["id_planet"] != 0 && $RowInfo['id'] != 0)
        {
            if ($RowInfo['id'] != $user['id'])
            {
                if ($this->canPhalanx())
                {
                    $PhalanxTypeLink = "<a href=# onclick=fenster('game.php?page=phalanx&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&amp;system=" . $this->TargetSystem . "&amp;planet=" . $RowInfo['planet'] . "&amp;planettype=1') >" . $engine->get('gl_phalanx') . "</a><br />";
                }
                else
                {
                    $PhalanxTypeLink = "";
                }
                if ($this->canSendMissile())
                {
                    $MissionType10Link = "<a href=game.php?page=galaxy&mode=2&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&current=" . $user['current_planet'] . " >" . $engine->get('gl_missile_attack') . "</a><br />";
                }
                else
                {
                    $MissionType10Link = "";
                }
                $MissionType1Link = "<a href=game.php?page=fleet&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&amp;system=" . $this->TargetSystem . "&amp;planet=" . $RowInfo['planet'] . "&amp;planettype=1&amp;target_mission=1>" . $engine->get('type_mission', 1) . "</a><br />";
                $MissionType3Link = "<a href=game.php?page=fleet&galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=1&target_mission=3>" . $engine->get('type_mission', 3) . "</a><br />";
                $MissionType4Link = "";
                $MissionType5Link = "";
                $MissionType6Link = "<a href=# onclick=&#039javascript:doit(6, " . $this->TargetUniverse . ", " . $this->TargetGalaxy . ", " . $this->TargetSystem . ", " . $RowInfo['planet'] . ", 1, " . $user["spio_anz"] . ");&#039 >" . $engine->get('type_mission', 6) . "</a><br /><br />";
            }
            else
            {
                $PhalanxTypeLink = "";
                $MissionType1Link = "";
                $MissionType3Link = "<a href=game.php?page=fleet&galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=1&target_mission=3>" . $engine->get('type_mission', 3) . "</a><br />";
                $MissionType4Link = "<a href=game.php?page=fleet&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=1&target_mission=4>" . $engine->get('type_mission', 4) . "</a><br />";
                $MissionType5Link = "<a href=game.php?page=fleet&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=1&target_mission=5>" . $engine->get('type_mission', 5) . "</a><br />";
                $MissionType6Link = "";
                $MissionType10Link = "";
            }
            $Result .= "<a style=\"cursor: pointer;\"";
            $Result .= " onmouseover='return overlib(\"";
            $Result .= "<table width=240>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>";
            $Result .= $engine->get('gl_planet') . " " . $RowInfo["name"] . " [" . $this->TargetUniverse . ":" . $this->TargetGalaxy . ":" . $this->TargetSystem . ":" . $RowInfo['planet'] . "]";
            $Result .= "</td>";
            $Result .= "</tr>";
            $Result .= "<tr>";
            $Result .= "<th width=80>";
            $Result .= "<img src=" . $dpath . "planeten/small/s_" . $RowInfo["image"] . ".jpg height=75 width=75 />";
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
            $Result .= "<img src=" . $dpath . "planeten/small/s_" . $RowInfo["image"] . ".jpg height=30 width=30>";
            $Result .= "</a>";
        }
        $Result .= "</th>";

        return $Result;
    }

    public function GalaxyRowPlanetName(&$RowInfo)
    {
        global $user, $engine;

        $Result = "<th style=\"white-space: nowrap;\" width=130>";

        if ($RowInfo["destruyed"] == 0)
        {
            if ($RowInfo['id'] != $user['id'])
            {
                if ($this->canPhalanx())
                {
                    $Result .= "<a href=# onclick=fenster('game.php?page=phalanx&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&amp;system=" . $this->TargetSystem . "&amp;planet=" . $RowInfo['planet'] . "&amp;planettype=1')  title=\"Phalanx\">" . $RowInfo['name'] . "</a><br />";
                }
                else
                {
                    $Result .= $RowInfo['name'];
                }

                if ($RowInfo['last_update'] > (time() - INACTIVITY_MAX))
                {
                    if ($RowInfo['last_update'] > (time() - INACTIVITY_MIN))
                    {
                        $Result .= "(*)";
                    }
                    else
                    {
                        $Inactivity = pretty_time_hour(time() - $RowInfo['last_update']);
                        $Result .= " (" . $Inactivity . ")";
                    }
                }
            }
        }
        else
        {
            $Result .= $engine->get('gl_planet_destroyed');
        }
        $Result .= "</th>";
        return $Result;
    }

    public function GalaxyRowPos(&$RowInfo)
    {
        $Result = "<th width=30>";
        $Result .= "<a href=\"game.php?page=fleet&universe=" . $this->TargetUniverse . "&amp;galaxy=" . $this->TargetGalaxy . "&system=" . $this->TargetSystem . "&planet=" . $RowInfo['planet'] . "&planettype=0&target_mission=7\"";
        $Result .= " tabindex=\"" . ($RowInfo['planet'] + 1) . "\"";
        $Result .= ">" . $RowInfo['planet'] . "</a>";
        $Result .= "</th>";
        return $Result;
    }

    public function GalaxyRowUser(&$RowInfo)
    {
        global $game_config, $user, $engine;

        $Result = "<th width=150>";

        if ($RowInfo["destruyed"] == 0)
        {
            $protection = $game_config['noobprotection'];
            $protectiontime = $game_config['noobprotectiontime'];
            $protectionmulti = $game_config['noobprotectionmulti'];
            $MyGameLevel = $user['total_points'];
            $HeGameLevel = $RowInfo['total_points'];

            if ($RowInfo['bana'] == 1 && $RowInfo['urlaubs_modus'] == 1)
            {
                $Systemtatus2 = "v <a href=\"game.php?page=banned\"><span class=\"banned\">" . $engine->get('gl_b') . "</span></a>";
                $Systemtatus = "<span class=\"vacation\">";
            } elseif ($RowInfo['bana'] == 1)
            {
                $Systemtatus2 = "<a href=\"game.php?page=banned\"><span class=\"banned\">" . $engine->get('gl_b') . "</span></a>";
                $Systemtatus = "";
            } elseif ($RowInfo['urlaubs_modus'] == 1)
            {
                $Systemtatus2 = "<span class=\"vacation\">" . $engine->get('gl_v') . "</span>";
                $Systemtatus = "<span class=\"vacation\">";
            } elseif ($RowInfo['onlinetime'] < (time() - INACTIVITY_SHORT) && $RowInfo['onlinetime'] > (time() - INACTIVITY_LONG))
            {
                $Systemtatus2 = "<span class=\"inactive\">" . $engine->get('gl_i') . "</span>";
                $Systemtatus = "<span class=\"inactive\">";
            } elseif ($RowInfo['onlinetime'] < (time() - INACTIVITY_LONG))
            {
                $Systemtatus2 = "<span class=\"inactive\">" . $engine->get('gl_i') . "</span><span class=\"longinactive\">" . $engine->get('gl_I') . "</span>";
                $Systemtatus = "<span class=\"longinactive\">";
            } elseif (($MyGameLevel > ($HeGameLevel * $protectionmulti)) && $protection == 1 && ($HeGameLevel < $protectiontime))
            {
                $Systemtatus2 = "<span class=\"noob\">" . $engine->get('gl_w') . "</span>";
                $Systemtatus = "<span class=\"noob\">";
            } elseif ((($MyGameLevel * $protectionmulti) < $HeGameLevel) && $protection == 1 && ($MyGameLevel < $protectiontime))
            {
                $Systemtatus2 = $engine->get('gl_s');
                $Systemtatus = "<span class=\"strong\">";
            }
            else
            {
                $Systemtatus2 = "";
                $Systemtatus = "";
            }
            $Systemtatus4 = $RowInfo['total_rank'];

            if ($Systemtatus2 != '')
            {
                $Systemtatus6 = "<font color=\"white\">(</font>";
                $Systemtatus7 = "<font color=\"white\">)</font>";
            }
            if ($Systemtatus2 == '')
            {
                $Systemtatus6 = "";
                $Systemtatus7 = "";
            }

            $Systemtart = $RowInfo['total_rank'];

            if (strlen($Systemtart) < 3)
                $Systemtart = 1;
            else
                $Systemtart = (floor($RowInfo['total_rank'] / 100) * 100) + 1;

            $Result .= "<a style=\"cursor: pointer;\"";
            $Result .= " onmouseover='return overlib(\"";
            $Result .= "<table width=190>";
            $Result .= "<tr>";
            $Result .= "<td class=c colspan=2>" . $engine->get('gl_player') . $RowInfo['username'] . $engine->get('gl_in_the_rank') . $Systemtatus4 . "</td>";
            $Result .= "</tr><tr>";
            if ($RowInfo['id'] != $user['id'])
            {
                $Result .= "<td><a href=game.php?page=messages&mode=write&id=" . $RowInfo['id'] . ">" . $engine->get('write_message') . "</a></td>";
                $Result .= "</tr><tr>";
                $Result .= "<td><a href=game.php?page=buddy&mode=2&u=" . $RowInfo['id'] . ">" . $engine->get('gl_buddy_request') . "</a></td>";
                $Result .= "</tr><tr>";
            }
            $Result .= "<td><a href=game.php?page=statistics&who=player&start=" . $Systemtart . ">" . $engine->get('gl_stat') . "</a></td>";
            $Result .= "</tr>";
            $Result .= "</table>\"";
            $Result .= ", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -40, OFFSETY, -40 );'";
            $Result .= " onmouseout='return nd();'>";
            $Result .= $Systemtatus;
            $Result .= $RowInfo["username"] . "</span>";
            $Result .= $Systemtatus6;
            $Result .= $Systemtatus;
            $Result .= $Systemtatus2;
            $Result .= $Systemtatus7 . " " . $admin;
            $Result .= "</span></a>";
        }
        $Result .= "</th>";

        return $Result;
    }
}

?>