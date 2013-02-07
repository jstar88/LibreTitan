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
 * 
 * UNDER CONSTRUCTION
 */
class ShowFleet3Page
{
    private $CurrentUser;
    private $TargetUser;
    private $CurrentPlanet;
    private $TargetPlanet;
    private $Fleet;

    public function __construct($CurrentUser, $CurrentPlanet)
    {
        $this->CurrentPlanet = $CurrentPlanet;
        $this->CurrentUser = $CurrentUser;
        $this->TargetPlanet = array();
        $this->TargetUser = array();
        $this->Fleet = array();
    }

    public function show()
    {

        $this->generalControll();
        $this->missionControll($_POST['mission']);
        $this->missionSender();

        $missiontype = array(1 => Xtreme::get('type_mission', 1), 2 => Xtreme::get('type_mission', 2), 3 => Xtreme::get('type_mission', 3), 4 => Xtreme::get('type_mission', 4), 5 => Xtreme::get('type_mission', 5), 6 => Xtreme::get('type_mission', 6), 7 => Xtreme::get('type_mission', 7), 8 => Xtreme::get('type_mission', 8), 9 => Xtreme::get('type_mission', 9), 15 => Xtreme::get('type_mission', 15));
        Xtreme::assign('mission', $missiontype[$_POST['mission']]);
        Xtreme::assign('distance', pretty_number($this->Fleet['distance']));
        Xtreme::assign('speedallsmin', pretty_number($_POST['speedallsmin']));
        Xtreme::assign('consumption', pretty_number($this->Fleet['consumption']));
        if ($in_war)
        {
            $from = $this->CurrentPlanet['universe'] . ":" . $this->CurrentPlanet['galaxy'] . ":" . $this->CurrentPlanet['system'] . ":" . $this->CurrentPlanet['planet'];
            $destination = $this->TargetPlanet['universe'] . ":" . $this->TargetPlanet['galaxy'] . ":" . $this->TargetPlanet['system'] . ":" . $this->TargetPlanet['planet'];
        }
        else
        {
            $from = $this->CurrentPlanet['galaxy'] . ":" . $this->CurrentPlanet['system'] . ":" . $this->CurrentPlanet['planet'];
            $destination = $this->TargetPlanet['galaxy'] . ":" . $this->TargetPlanet['system'] . ":" . $this->TargetPlanet['planet'];
        }
        Xtreme::assign('from', $from);
        Xtreme::assign('destination', $destination);
        Xtreme::assign('start_time', date("M D d H:i:s", $this->Fleet['start_time']));
        Xtreme::assign('end_time', date("M D d H:i:s", $this->Fleet['end_time']));

        $fleet_list = '';
        foreach ($this->Fleet['fleet_array'] as $Ship => $Count)
        {
            $fleet_list .= "</tr><tr height=\"20\">";
            $fleet_list .= "<th>" . Xtreme::get('tech', $Ship) . "</th>";
            $fleet_list .= "<th>" . pretty_number($Count) . "</th>";
        }

        Xtreme::assign('fleet_list', $fleet_list);

        if ($in_war)
        {
            Xtreme::output('fleet/fleet3_table_extended', true);
        }
        else
        {
            Xtreme::output('fleet/fleet3_table', true);
        }
    }

    private function missionControll($idMission)
    {
        global $missiontype;
        $funcName = $missiontype[$idMission] . 'Controll';
        $this->$funcName();
    }
    /***
    * this function is finished
    * */
    private function missionSender()
    {
        global $LegacyPlanet, $resource, $pricelist, $phpEx;

        $distance = GetTargetDistance($this->CurrentPlanet['universe'], $this->TargetPlanet['universe'], $this->CurrentPlanet['galaxy'], $this->TargetPlanet['galaxy'], $this->CurrentPlanet['system'], $this->TargetPlanet['system'], $this->CurrentPlanet['planet'], $this->TargetPlanet['planet']);
        $AllFleetSpeed = GetFleetMaxSpeed($this->Fleet['fleet_array'], 0, $this->CurrentUser);
        $MaxFleetSpeed = min($AllFleetSpeed);
        $SpeedFactor = $game_config['fleet_speed'] / 2500;
        $duration = GetMissionDuration($this->Fleet['GenFleetSpeed'], $MaxFleetSpeed, $distance, $SpeedFactor);
        $consumption = GetFleetConsumption($this->Fleet['fleet_array'], $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $this->CurrentUser);
        $StayDuration = $this->Fleet['stay_duration'] * 3600;
        $this->Fleet['start_time'] = Formules::getArriveTime($duration, CURRENT_TIME);
        $this->Fleet['stay_time'] = Formules::getEndStayTime($StayDuration, $duration, CURRENT_TIME);
        $this->Fleet['end_time'] = Formules::getReturnTime($StayDuration, $duration, CURRENT_TIME);
        $this->Fleet['duration'] = $duration;
        $this->Fleet['consumption'] = $consumption;

        //----------------- resources checks
        $FleetStorage = $this->Fleet['storage'];
        $StorageNeeded = 0;
        $TransMetal = $_POST['resource1'];
        $StorageNeeded += $TransMetal;
        $TransCrystal = $_POST['resource2'];
        $StorageNeeded += $TransCrystal;
        $TransDeuterium = $_POST['resource3'];
        $StorageNeeded += $TransDeuterium;
        $FleetStorage -= $consumption;
        $this->CurrentPlanet['deuterium'] -= $consumption;
        if ($this->CurrentPlanet['deuterium'] < 0)
            message("<font color=\"red\"><b>" . Xtreme::get('fl_no_enought_deuterium') . pretty_number($consumption) . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        if ($this->CurrentPlanet['metal'] < $TransMetal || $this->CurrentPlanet['crystal'] < $TransCrystal || $this->CurrentPlanet['deuterium'] < $TransDeuterium)
            message("<font color=\"red\"><b>" . Xtreme::get('fl_no_enought_deuterium') . pretty_number($consumption) . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        if ($StorageNeeded > $FleetStorage)
            message("<font color=\"red\"><b>" . Xtreme::get('fl_no_enought_cargo_capacity') . pretty_number($StorageNeeded - $FleetStorage) . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        $this->CurrentPlanet['metal'] -= $this->Fleet['metal'];
        $this->CurrentPlanet['crystal'] -= $this->Fleet['crystal'];
        $this->CurrentPlanet['deuterium'] -= $this->Fleet['deuterium'] + $consumption;
        //-------acs
        if ($this->Fleet['fleet_group_mr'] != 0)
        {
            $AksStartTime = doquery("SELECT MAX(`fleet_start_time`) AS Start FROM {{table}} WHERE `fleet_group` = '" . $this->Fleet['fleet_group_mr'] . "';", "fleets", true);
            if ($AksStartTime['Start'] / $this->Fleet['start_time'] <= ACS_MAX_TIME) //rallentamento acs

            {
                $shiftTime = $this->Fleet['start_time'] - $AksStartTime['Start'];
                $QryUpdateFleets = "UPDATE {{table}} SET ";
                $QryUpdateFleets .= "`fleet_start_time` = '" . $this->Fleet['start_time'] . "', ";
                $QryUpdateFleets .= "`fleet_end_time` = fleet_end_time + '$shiftTime' ";
                $QryUpdateFleets .= "WHERE ";
                $QryUpdateFleets .= "`fleet_group` = '$fleet_group_mr';";
                doquery($QryUpdateFleets, 'fleets');
                $this->Fleet['end_time'] += $shiftTime;
            } elseif ($this->Fleet['start_time'] <= $AksStartTime['Start']) //rallentamento flotta

            {
                $this->Fleet['start_time'] = $AksStartTime['Start'];
                $this->Fleet['end_time'] += $AksStartTime['Start'] - $this->Fleet['start_time'];
            }
            else //non è possibile

            {
                exit(header("location:game.$phpEx?page=fleet"));
            }
        }
        //--------------

        $QryInsertFleet = "INSERT INTO {{table}} SET ";
        $QryInsertFleet .= "`fleet_owner` = '" . $this->CurrentUser['id'] . "', ";
        $QryInsertFleet .= "`fleet_mission` = '" . $this->Fleet['mission'] . "',  ";
        $QryInsertFleet .= "`fleet_amount` = '" . $this->Fleet['count'] . "', ";
        $QryInsertFleet .= "`fleet_array` = '" . $this->Fleet['fleet_array_string'] . "', ";
        $QryInsertFleet .= "`fleet_start_time` = '" . $this->Fleet['start_time'] . "', ";
        $QryInsertFleet .= "`fleet_start_universe` = '" . $this->CurrentPlanet['universe'] . "', ";
        $QryInsertFleet .= "`fleet_start_galaxy` = '" . $this->CurrentPlanet['galaxy'] . "', ";
        $QryInsertFleet .= "`fleet_start_system` = '" . $this->CurrentPlanet['system'] . "', ";
        $QryInsertFleet .= "`fleet_start_planet` = '" . $this->CurrentPlanet['planet'] . "', ";
        $QryInsertFleet .= "`fleet_start_type` = '" . $this->CurrentPlanet['thisplanettype'] . "', ";
        $QryInsertFleet .= "`fleet_end_time` = '" . $this->Fleet['end_time'] . "', ";
        $QryInsertFleet .= "`fleet_end_stay` = '" . $this->Fleet['stay_time'] . "', ";
        $QryInsertFleet .= "`fleet_end_universe` = '" . $this->TargetPlanet['universe'] . "', ";
        $QryInsertFleet .= "`fleet_end_galaxy` = '" . $this->TargetPlanet['galaxy'] . "', ";
        $QryInsertFleet .= "`fleet_end_system` = '" . $this->TargetPlanet['system'] . "', ";
        $QryInsertFleet .= "`fleet_end_planet` = '" . $this->TargetPlanet['planet'] . "', ";
        $QryInsertFleet .= "`fleet_end_type` = '" . $this->TargetPlanet['planettype'] . "', ";
        $QryInsertFleet .= "`fleet_resource_metal` = '" . $this->Fleet['metal'] . "', ";
        $QryInsertFleet .= "`fleet_resource_crystal` = '" . $this->Fleet['crystal'] . "', ";
        $QryInsertFleet .= "`fleet_resource_deuterium` = '" . $this->Fleet['deuterium'] . "', ";
        $QryInsertFleet .= "`fleet_target_owner` = '" . $this->TargetPlanet['id'] . "', ";
        $QryInsertFleet .= "`fleet_group` = '" . $this->Fleet['fleet_group_mr'] . "',  ";
        $QryInsertFleet .= "`start_time` = '" . time() . "';";
        doquery($QryInsertFleet, 'fleets');
        $FleetSubQRY = '';
        foreach ($this->Fleet['fleet_array'] as $Ship => $Count)
        {
            $this->CurrentPlanet[$resource[$Ship]] -= $Count;
            $LegacyPlanet[$resource[$Ship]] = $resource[$Ship];
            $FleetSubQRY .= "`" . $resource[$Ship] . "` = `" . $resource[$Ship] . "` - " . $Count . ", ";
        }
        $QryUpdatePlanet = "UPDATE `{{table}}` SET ";
        $QryUpdatePlanet .= $FleetSubQRY;
        $QryUpdatePlanet .= "`metal` =" . $this->CurrentPlanet['metal'] . ", ";
        $QryUpdatePlanet .= "`crystal` =" . $this->CurrentPlanet['crystal'] . ", ";
        $QryUpdatePlanet .= "`deuterium` =" . $this->CurrentPlanet['deuterium'];
        $QryUpdatePlanet .= " WHERE ";
        $QryUpdatePlanet .= "`id` = " . $this->CurrentPlanet['id'] . " LIMIT 1;";
        doquery($QryUpdatePlanet, "planets");
    }
    private function generalControll()
    {
        global $resource, $xgp_root, $game_config, $phpEx, $pricelist;
        //----------- validating the input
        $universe = $_POST['universe'];
        $galaxy = $_POST['galaxy'];
        $system = $_POST['system'];
        $planet = $_POST['planet'];
        $planettype = $_POST['planettype'];
        $fleetmission = $_POST['mission'];
        $speed = $_POST['speed'];
        if (empty($universe) || empty($galaxy) || empty($system) || empty($planet) || empty($planettype) || empty($fleetmission) || empty($speed))
            exit(header("location:game." . $phpEx . "?page=fleet"));
        //----------------------------
        $thisUniverse = $this->CurrentPlanet['universe'];
        if ($universe == 0)
            $universe = $thisUniverse;
        if (!isUniverseInWar($universe, $thisUniverse))
            exit(header("location:game." . $phpEx . "?page=fleet"));

        $this->TargetPlanet = doquery("SELECT * FROM {{table}} WHERE `universe` = '$universe' AND`galaxy` = '$galaxy' AND `system` = '$system' AND planet` = '$planet' AND `planet_type` = '$planettype';", 'planets', true);
        $this->TargetUser = doquery("SELECT `id`,`onlinetime`,`ally_id`,`urlaubs_modus` FROM {{table}} WHERE `id` = '" . $this->TargetPlanet['id_owner'] . "';", 'users', true);
        $fleetarray = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));

        foreach ($fleetarray as $Ship => $Count)
        {
            $FleetStorage += $pricelist[$Ship]["capacity"] * $Count;
            $FleetShipCount += $Count;
            $fleet_array .= $Ship . "," . $Count . ";";
        }
        $this->Fleet['fleet_array_string'] = $fleet_array;
        $this->Fleet['storage'] = $FleetStorage;
        $this->Fleet['count'] = $FleetShipCount;
        $this->Fleet['mission'] = $fleetmission;
        $this->Fleet['speed_possible'] = range(1, 10);
        $this->Fleet['GenFleetSpeed'] = $speed;
        $this->Fleet['fleet_array'] = $fleetarray;


        //the target planet can't be the start planet
        if ($this->CurrentPlanet['universe'] == $this->TargetPlanet['universe'] && $this->CurrentPlanet['galaxy'] == $this->TargetPlanet['galaxy'] && $this->CurrentPlanet['system'] == $this->TargetPlanet['system'] && $this->CurrentPlanet['planet'] == $this->TargetPlanet['planet'] && $this->CurrentPlanet['planet_type'] == $this->TargetPlanet['planettype'])
            exit(header("location:game." . $phpEx . "?page=fleet"));
        //if you are in vacationmode ,you can't send fleets
        include_once ($xgp_root . 'includes/functions/IsVacationMode.' . $phpEx);
        if (IsVacationMode($this->CurrentUser))
            exit(message(Xtreme::get('fl_vacation_mode_active'), "game.php?page=overview", 2));
        //no one mission for destroyed moon or planet
        if ($this->TargetPlanet['destruyed'] != 0)
            exit(header("Location: game.php?page=fleet"));

        //--------can't send more fleets of the max
        foreach ($this->Fleet['fleet_array'] as $Ship => $Count)
            if ($Count > $this->CurrentPlanet[$resource[$Ship]])
                exit(header("location:game." . $phpEx . "?page=fleet"));
        if (!is_array($this->Fleet['fleet_array']))
            exit(header("Location: game.php?page=fleet"));
        //-------------------
        //--------noob check
        noobcheck();
        //--------------------------------------------------
        //------max fleet-slots
        $FlyingFleets = mysql_fetch_assoc(doquery("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='" . $this->CurrentUser['id'] . "'", 'fleets'));
        $ActualFleets = $FlyingFleets["Number"];
        if ((1 + $this->CurrentUser[$resource[108]]) + ($this->CurrentUser['rpg_commandant'] * COMMANDANT) <= $ActualFleets)
            message($lang['fl_no_slots'], "game.$phpEx?page=fleet", 1);
        //--------------------

        if (!in_array($this->Fleet['GenFleetSpeed'], $this->Fleet['speed_possible']))
            exit(header("location:game.$phpEx?page=fleet"));


    }
    private function attackControll()
    {
        global $phpEx, $game_config;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //----you can't attack yourself
        if ($this->TargetPlanet['id_owner'] == $this->CurrentPlanet['id_owner'])
            exit(header("location:game.$phpEx?page=fleet"));
        //---------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------


        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------

    }
    private function acsControll()
    {
        global $phpEx, $game_config;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------
        //----you can't attack yourself
        if ($this->TargetPlanet['id_owner'] == $this->CurrentPlanet['id_owner'])
            exit(header("location:game.$phpEx?page=fleet"));
        //---------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------
        if (empty($_POST['fleet_group']))
            exit(message("you must select an acs id", "game.php?page=overview", 2));
        //--------- check if the given acs link is valid
        if ($in_war)
            $target = "u" . $this->TargetPlanet['universe'] . "g" . intval($_POST["galaxy"]) . "s" . intval($_POST["system"]) . "p" . intval($_POST["planet"]) . "t" . intval($_POST["planettype"]);
        else
            $target = "g" . intval($_POST["galaxy"]) . "s" . intval($_POST["system"]) . "p" . intval($_POST["planet"]) . "t" . intval($_POST["planettype"]);

        if ($_POST['acs_target_mr'] == $target)
        {
            $aks_count_mr = mysql_result(doquery("SELECT count(*) FROM {{table}} WHERE `id` = '" . intval($_POST['fleet_group']) . "'", 'aks'), 0);
            if ($aks_count_mr > 0)
            {
                $this->Fleet['fleet_group_mr'] = mysql_real_escape_string($_POST['fleet_group']);
            }
        }
        //-------------------------------------------

    }
    private function transportControll(&$info)
    {
        global $phpEx;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------
        if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1)
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_empty_transport') . "</b></font>", "game." . $phpEx . "?page=fleet", 1);
    }
    private function stayControll()
    {
        global $phpEx;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //----you can't make happy someone
        if ($this->TargetPlanet['id_owner'] != $this->CurrentPlanet['id_owner'])
            message("<font color=\"red\"><b>" . Xtreme::get('fl_deploy_only_your_planets') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------
    }
    private function stayAllyControll()
    {
        global $phpEx, $game_config;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------
        //----------you can't stay at enemy planets

        $buddy = mysql_result(doquery("SELECT count(*) FROM {{table}} WHERE `owner` = '" . $this->TargetPlanet['id_owner'] . "' OR `sender`='" . $this->TargetPlanet['id_owner'] . "' AND `active` = '1';", 'buddy'), 0);

        if ($this->TargetPlanet['planettype'] == 3)
        {
            $x = doquery("SELECT `ally_deposit` FROM {{table}} WHERE `universe` = '" . $this->TargetPlanet['universe'] . "' AND `galaxy` = '" . $this->TargetPlanet['galaxy'] . "' AND `system` = '" . $this->TargetPlanet['system'] . "' AND `planet` = '" . $this->TargetPlanet['planet'] . "' AND `planet_type` = 1;", 'planets', true);
        }
        else
        {
            $x = $this->TargetPlanet;
        }

        if (($this->TargetUser['ally_id'] != $this->CurrentUser['ally_id'] && $buddy < 1) || $x['ally_deposit'] < 1)
        {
            message("<font color=\"red\"><b>" . Xtreme::get('fl_stay_not_on_enemy') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        }

        //-------------------------------------------
        //----you can't stay at yourself
        if ($this->TargetPlanet['id_owner'] == $this->CurrentPlanet['id_owner'])
            exit(header("location:game.$phpEx?page=fleet"));
        //---------------------
        $StayDuration = $_POST['expeditiontime'];
        if (empty($StayDuration))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        $this->Fleet['stay_duration'] = $StayDuration;
    }
    private function spyControll()
    {
        global $game_config, $phpEx;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------
        //----you can't spy yourself
        if ($this->TargetPlanet['id_owner'] == $this->CurrentPlanet['id_owner'])
            exit(header("location:game.$phpEx?page=fleet"));
        //---------------------
    }
    private function colonisationControll()
    {
        global $phpEx;
        if (!isset($fleetarray[208]))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        if (!empty($this->TargetPlanet['id']) || !empty($this->TargetPlanet['id_owner']))
            message("<font color=\"red\"><b>" . Xtreme::get('fl_planet_populed') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        $StayDuration = $_POST['expeditiontime'];
        if (empty($StayDuration))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        $StayDuration = floor($StayDuration);
        if ($StayDuration > floor(sqrt($this->CurrentUser['expedition_tech'])) || $StayDuration < 0)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        $this->Fleet['stay_duration'] = $StayDuration;
    }
    private function recyclingControll()
    {
        global $phpEx;
        if ($this->TargetPlanet['planettype'] != 2)
            exit(header("location:game." . $phpEx . "?page=fleet"));
        $select2 = doquery("SELECT metal, crystal FROM {{table}} WHERE universe = '" . $this->TargetPlanet['universe'] . "' AND galaxy = '" . $this->TargetPlanet['galaxy'] . "' AND system = '" . $this->TargetPlanet['system'] . "' AND planet = '" . $this->TargetPlanet['planet'] . "'", "galaxy", true);
        if ($select2['metal'] == 0 && $select2['crystal'] == 0)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }

    }
    private function destructionControll()
    {
        global $phpEx;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------
        $countfleettype = count($this->Fleet['fleet_array']);

        if ($this->TargetPlanet['id_owner'] == $this->CurrentPlanet['id_owner'] || $this->TargetPlanet['planettype'] != 3)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype == 1 && !(isset($this->Fleet['fleet_array'][214]) or isset($this->Fleet['fleet_array'][216])))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype == 2 && !(isset($this->Fleet['fleet_array'][214]) && isset($this->Fleet['fleet_array'][216])))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype > 2)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
    }
    private function mipControll()
    {
        global $phpEx, $game_config;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------
        //-----check if target user is in vacation mode
        if ($this->TargetUser['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . Xtreme::get('fl_in_vacation_player') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        //---------------------------------------------
        //-------- can't attack the admin
        if ($this->TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
            message(Xtreme::get('fl_admins_cannot_be_attacked'), "game." . $phpEx . "?page=fleet", 2);
        //---------------------------
    }
    private function expeditionControll()
    {
        global $resource, $phpEx;
        $MaxExpedition = $this->CurrentUser[$resource[124]];

        if ($MaxExpedition >= 1)
        {
            $maxexpde = doquery("SELECT COUNT(fleet_owner) AS `expedi` FROM {{table}} WHERE `fleet_owner` = '" . intval($this->CurrentUser['id']) . "' AND `fleet_mission` = '15';", 'fleets', true);
            $ExpeditionEnCours = $maxexpde['expedi'];
            $EnvoiMaxExpedition = 1 + floor($MaxExpedition / 3);
        }
        else
        {
            $ExpeditionEnCours = 0;
            $EnvoiMaxExpedition = 0;
        }

        if ($EnvoiMaxExpedition == 0)
            message("<font color=\"red\"><b>" . Xtreme::get('fl_expedition_tech_required') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        elseif ($ExpeditionEnCours >= $EnvoiMaxExpedition)
            message("<font color=\"red\"><b>" . Xtreme::get('fl_expedition_fleets_limit') . "</b></font>", "game." . $phpEx . "?page=fleet", 2);

    }

    private function sanitizecoordinates()
    {
        $args = array('universe' => array($targetUniverse => FILTER_CALLBACK, array('options' => array($this, 'validateUniverse'))), 'galaxy' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD)), 'system' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY)), 'planet' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_PLANET_IN_SYSTEM)), 'mission' => array($mission => FILTER_CALLBACK, array('options' => array($this, 'validateMission'))), 'fleet_group' => array('filter' => FILTER_VALIDATE_INT), 'resource1' => array($resource => FILTER_CALLBACK, array('options' => array($this, 'validateResources'))), 'resource2' => array($resource => FILTER_CALLBACK, array('options' => array($this, 'validateResources'))), 'resource3' => array($resource => FILTER_CALLBACK, array('options' => array($this, 'validateResources'))));
        $_POST = filter_input_array(INPUT_POST, $args);
        $_GET = filter_input_array(INPUT_GET, $args);
    }

    private function validateUniverse($targetUniverse)
    {
        $targetUniverse = min(1, max($targetUniverse, MAX_UNIVERSE_IN_WORLD));
        if (isUniverseInWar($targetUniverse, $CurrentUniverse))
            return $targetUniverse;
        return false;
    }
    private function validateMission($mission)
    {
        global $missions;
        if (isset($missions[$mission]))
            return $mission;
        return false;
    }
    private function validateResources($resource)
    {
        return floor(max(0, (int)trim($resource)));
    }
}

?>