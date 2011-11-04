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
    private $CurrentPlanet;
    private $TargetPlanet;
    private $Fleet;

    public function __construct($CurrentUser, $CurrentPlanet)
    {
        $this->CurrentPlanet = $CurrentPlanet;
        $this->CurrentUser = $CurrentUser;
        $this->TargetPlanet = array();
        $this->Fleet = array('mission'=>0,'ship_count'=>0,'fleet_array'=>0,'start_time'=>0,'end_time'=>0,'stay_time'=>0,'metal'=>0,'crystal'=>0,'deuterium'=>0,'fleet_group_mr'=>0);
    }
    public function show()
    {
        global $resource, $pricelist, $reslist, $phpEx, $engine, $xgp_root, $game_config, $LegacyPlanet;


        $thisUniverse = $this->CurrentPlanet['universe'];
        $universe = $_POST['universe'];
        if ($universe == 0)
            $universe = $thisUniverse;
        $in_war = isUniverseInWar($universe, $thisUniverse);
        $galaxy = $_POST['galaxy'];
        $system = $_POST['system'];
        $planet = $_POST['planet'];
        $planettype = $_POST['planettype'];
        $fleetmission = $_POST['mission'];

        if (empty($universe) || empty($galaxy) || empty($system) || empty($planet) || empty($planettype) || empty($fleetmission))
            die();

        $info = array();
        $info['in_war'] = $in_war;
        $this->TargetPlanet = doquery("SELECT * FROM {{table}} WHERE `universe` = '$universe' AND`galaxy` = '$galaxy' AND `system` = '$system' AND planet` = '$planet' AND `planet_type` = '$planettype';", 'planets', true);
        $fleetarray = unserialize(base64_decode(str_rot13($_POST["usedfleet"])));
        $this->Fleet['fleet_array'] = $fleetarray;
         $this->Fleet['mission']=$fleetmission;
        $this->generalControll();
        $this->missionControll($_POST['mission']);
        
        $distance = GetTargetDistance($thisUniverse, $universe, $this->CurrentPlanet['galaxy'], $galaxy, $this->CurrentPlanet['system'], $system, $this->CurrentPlanet['planet'], $planet);
        $duration = GetMissionDuration($GenFleetSpeed, $MaxFleetSpeed, $distance, $SpeedFactor);
        $consumption = GetFleetConsumption($fleetarray, $SpeedFactor, $duration, $distance, $MaxFleetSpeed, $this->CurrentUser);
        $this->Fleet['speed_possible'] = range(1,10);
        $AllFleetSpeed = GetFleetMaxSpeed($this->Fleet['fleet_array'], 0, $this->CurrentUser);
        $this->Fleet['GenFleetSpeed'] = $_POST['speed'];
        $SpeedFactor = $game_config['fleet_speed'] / 2500;
        $MaxFleetSpeed = min($AllFleetSpeed);
        $StayDuration = $StayDuration * 3600;
        $StayTime = $fleet['start_time'] + $StayDuration;
        $this->Fleet['start_time'] = Formules::getArriveTime($duration,CURRENT_TIME);
        $this->Fleet['stay_time'] =  Formules::getEndStayTime($StayDuration,$duration,CURRENT_TIME);
        $this->Fleet['end_time'] = Formules::getReturnTime($StayDuration,$duration,CURRENT_TIME);
        $this->missionSender();


        $MyDBRec = doquery("SELECT `id`,`onlinetime`,`ally_id`,`urlaubs_modus` FROM {{table}} WHERE `id` = '" . intval($this->CurrentUser['id']) . "';", 'users', true);

        $error = 0;

        if ($_POST['mission'] != 15)
        {
            if (mysql_num_rows($select) < 1 && $fleetmission != 7)
                exit(header("location:game." . $phpEx . "?page=fleet"));
            elseif ($fleetmission == 9 && mysql_num_rows($select) < 1)
                exit(header("location:game." . $phpEx . "?page=fleet"));
        }

        $select = mysql_fetch_array($select);

        //fix by jstar
        if ($fleetmission == 9)
        {


            if ($HeDBRec['urlaubs_modus'] && $_POST['mission'] != 8)
                message("<font color=\"lime\"><b>" . $lang['fl_in_vacation_player'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);


            if ($_POST['resource1'] + $_POST['resource2'] + $_POST['resource3'] < 1 && $_POST['mission'] == 3)
                message("<font color=\"lime\"><b>" . $lang['fl_empty_transport'] . "</b></font>", "game." . $phpEx . "?page=fleet", 1);

            if ($_POST['mission'] != 15)
            {

                if ($HeDBRec['ally_id'] != $MyDBRec['ally_id'] && $_POST['mission'] == 4)
                    message("<font color=\"red\"><b>" . $lang['fl_stay_not_on_enemy'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);

                if (($TargetPlanet["id_owner"] == $this->CurrentPlanet["id_owner"]) && (($_POST["mission"] == 1) or ($_POST["mission"] == 6)))
                    exit(header("location:game." . $phpEx . "?page=fleet"));

                if (($TargetPlanet["id_owner"] != $this->CurrentPlanet["id_owner"]) && ($_POST["mission"] == 4))
                    message("<font color=\"red\"><b>" . $lang['fl_deploy_only_your_planets'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
                if ($_POST['mission'] == 5)
                {
                    $buddy = doquery("SELECT count(*) FROM {{table}} WHERE `owner` = '" . intval($TargetPlanet['id_owner']) . "' OR `sender`='" . intval($TargetPlanet['id_owner']) . "' AND `active` = '1';", 'buddy');

                    if ($_POST['planettype'] == 3)
                    {
                        $x = doquery("SELECT `ally_deposit` FROM {{table}} WHERE `universe` = '" . intval($_POST['universe']) . "' AND `galaxy` = '" . intval($_POST['galaxy']) . "' AND `system` = '" . intval($_POST['system']) . "' AND `planet` = '" . intval($_POST['planet']) . "' AND `planet_type` = 1;", 'planets', true);
                    }
                    else
                    {
                        $x = $TargetPlanet;
                    }

                    if (($HeDBRec['ally_id'] != $MyDBRec['ally_id'] && $buddy < 1) || $x['ally_deposit'] < 1)
                    {
                        message("<font color=\"red\"><b>" . $lang['fl_stay_not_on_enemy'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
                    }

                }
            }


            $FleetStorage = 0;
            $FleetShipCount = 0;
            $fleet_array = "";
            $FleetSubQRY = "";

            foreach ($fleetarray as $Ship => $Count)
            {
                $FleetStorage += $pricelist[$Ship]["capacity"] * $Count;
                $FleetShipCount += $Count;
                $fleet_array .= $Ship . "," . $Count . ";";
            }

            $FleetStorage -= $consumption;
            $StorageNeeded = 0;

            $_POST['resource1'] = max(0, (int)trim($_POST['resource1']));
            $_POST['resource2'] = max(0, (int)trim($_POST['resource2']));
            $_POST['resource3'] = max(0, (int)trim($_POST['resource3']));

            if ($_POST['resource1'] < 1)
                $TransMetal = 0;
            else
            {
                $TransMetal = $_POST['resource1'];
                $StorageNeeded += $TransMetal;
            }

            if ($_POST['resource2'] < 1)
                $TransCrystal = 0;
            else
            {
                $TransCrystal = $_POST['resource2'];
                $StorageNeeded += $TransCrystal;
            }
            if ($_POST['resource3'] < 1)
                $TransDeuterium = 0;
            else
            {
                $TransDeuterium = $_POST['resource3'];
                $StorageNeeded += $TransDeuterium;
            }

            $StockMetal = $this->CurrentPlanet['metal'];
            $StockCrystal = $this->CurrentPlanet['crystal'];
            $StockDeuterium = $this->CurrentPlanet['deuterium'];
            $StockDeuterium -= $consumption;

            $StockOk = false;
            if ($StockMetal >= $TransMetal)
                if ($StockCrystal >= $TransCrystal)
                    if ($StockDeuterium >= $TransDeuterium)
                        $StockOk = true;
            if (!$StockOk)
                message("<font color=\"red\"><b>" . $lang['fl_no_enought_deuterium'] . pretty_number($consumption) . "</b></font>", "game." . $phpEx . "?page=fleet", 2);

            if ($StorageNeeded > $FleetStorage)
                message("<font color=\"red\"><b>" . $lang['fl_no_enought_cargo_capacity'] . pretty_number($StorageNeeded - $FleetStorage) . "</b></font>", "game." . $phpEx . "?page=fleet", 2);

            if ($TargetPlanet['id_level'] > $this->CurrentUser['authlevel'] && $game_config['adm_attack'] == 0)
                message($lang['fl_admins_cannot_be_attacked'], "game." . $phpEx . "?page=fleet", 2);

            if ($fleet_group_mr != 0)
            {
                $AksStartTime = doquery("SELECT MAX(`fleet_start_time`) AS Start FROM {{table}} WHERE `fleet_group` = '" . $fleet_group_mr . "';", "fleets", true);

                if ($AksStartTime['Start'] >= $fleet['start_time'])
                {
                    $fleet['end_time'] += $AksStartTime['Start'] - $fleet['start_time'];
                    $fleet['start_time'] = $AksStartTime['Start'];
                }
                else
                {
                    $QryUpdateFleets = "UPDATE {{table}} SET ";
                    $QryUpdateFleets .= "`fleet_start_time` = '" . $fleet['start_time'] . "', ";
                    $QryUpdateFleets .= "`fleet_end_time` = fleet_end_time + '" . ($fleet['start_time'] - $AksStartTime['Start']) . "' ";
                    $QryUpdateFleets .= "WHERE ";
                    $QryUpdateFleets .= "`fleet_group` = '" . $fleet_group_mr . "';";
                    doquery($QryUpdateFleets, 'fleets');
                    $fleet['end_time'] += $fleet['start_time'] - $AksStartTime['Start'];
                }
            }

            $parse['mission'] = $missiontype[$_POST['mission']];
            $parse['distance'] = pretty_number($distance);
            $parse['speedallsmin'] = pretty_number($_POST['speedallsmin']);
            $parse['consumption'] = pretty_number($consumption);
            //start mod
            if ($in_war)
            {
                $parse['from'] = $_POST['thisuniverse'] . ":" . $_POST['thisgalaxy'] . ":" . $_POST['thissystem'] . ":" . $_POST['thisplanet'];
                $parse['destination'] = $_POST['universe'] . ":" . $_POST['galaxy'] . ":" . $_POST['system'] . ":" . $_POST['planet'];
            }
            else
            {
                $parse['from'] = $_POST['thisgalaxy'] . ":" . $_POST['thissystem'] . ":" . $_POST['thisplanet'];
                $parse['destination'] = $universe . ":" . $_POST['system'] . ":" . $_POST['planet'];
            }

            //end mod
            $parse['start_time'] = date("M D d H:i:s", $fleet['start_time']);
            $parse['end_time'] = date("M D d H:i:s", $fleet['end_time']);

            foreach ($fleetarray as $Ship => $Count)
            {
                $fleet_list .= "</tr><tr height=\"20\">";
                $fleet_list .= "<th>" . $lang['tech'][$Ship] . "</th>";
                $fleet_list .= "<th>" . pretty_number($Count) . "</th>";
            }

            $parse['fleet_list'] = $fleet_list;

            if ($in_war)
            {
                display(parsetemplate(gettemplate('fleet/fleet3_table_extended'), $parse), false);
            }
            else
            {
                display(parsetemplate(gettemplate('fleet/fleet3_table'), $parse), false);
            }
        }
    }
    private function missionControll($idMission, &$info)
    {
        global $missions;
        $funcName = $mission[$idMission] . 'Controll';
        $this->$funcName($info);
    }
    /***
    * this function is finished
    * */
    private function missionSender()
    {
        global $LegacyPlanet, $resource;

        $QryInsertFleet = "INSERT INTO {{table}} SET ";
        $QryInsertFleet .= "`fleet_owner` = '" . $this->CurrentUser['id'] . "', ";
        $QryInsertFleet .= "`fleet_mission` = '" . $this->Fleet['mission'] . "',  ";
        $QryInsertFleet .= "`fleet_amount` = '" . $this->Fleet['ship_count'] . "', ";
        $QryInsertFleet .= "`fleet_array` = '" . $this->Fleet['fleet_array'] . "', ";
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

        foreach ($info['fleet_array'] as $Ship => $Count)
        {
            $this->CurrentPlanet[$resource[$Ship]] -= $Count;
            $LegacyPlanet[$resource[$Ship]] = $resource[$Ship];
        }

        $this->CurrentPlanet['metal'] -= $info['metal'];
        $this->CurrentPlanet['crystal'] -= $info['crystal'];
        $this->CurrentPlanet['deuterium'] -= ($info['deuterium'] + $info['consumption']);
    }
    private function generalControll()
    {
        global $resource, $xgp_root, $game_config, $phpEx, $lang;
        //the target planet can't be the start planet
        if ($this->CurrentPlanet['universe'] == $this->TargetPlanet['universe'] && $this->CurrentPlanet['galaxy'] == $this->TargetPlanet['galaxy'] && $this->CurrentPlanet['system'] == $this->TargetPlanet['system'] && $this->CurrentPlanet['planet'] == $this->TargetPlanet['planet'] && $this->CurrentPlanet['planet_type'] == $this->TargetPlanet['planettype'])
            exit(header("location:game." . $phpEx . "?page=fleet"));
        //if you are in vacationmode ,you can't send fleets
        include_once ($xgp_root . 'includes/functions/IsVacationMode.' . $phpEx);
        if (IsVacationMode($this->CurrentUser))
            exit(message($lang['fl_vacation_mode_active'], "game.php?page=overview", 2));
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

        //------max fleet-slots
        $FlyingFleets = mysql_fetch_assoc(doquery("SELECT COUNT(fleet_id) as Number FROM {{table}} WHERE `fleet_owner`='" . intval($this->CurrentUser['id']) . "'", 'fleets'));
        $ActualFleets = $FlyingFleets["Number"];
        if ((1 + $this->CurrentUser[$resource[108]]) + ($this->CurrentUser['rpg_commandant'] * COMMANDANT) <= $ActualFleets)
            message($lang['fl_no_slots'], "game.$phpEx?page=fleet", 1);
        //--------------------

        if (!in_array($this->Fleet['GenFleetSpeed'], $this->Fleet['speed_possible']))
            exit(header("location:game.$phpEx?page=fleet"));
        if (!isset($_POST['planettype']))
            exit(header("location:game.$phpEx?page=fleet"));

    }
    private function attackControll()
    {
        global $user, $phpEx, $game_config, $lang;
        //----------check if the target planet exist
        if (empty($this->TargetPlanet['id']) || empty($this->TargetPlanet['id_owner']))
            exit(header("location:game.$phpEx?page=fleet"));
        //-------------------------------------------

        //--------noob check
        $protection = $game_config['noobprotection'];
        $protectiontime = $game_config['noobprotectiontime'];
        $protectionmulti = $game_config['noobprotectionmulti'];
        if ($protectiontime < 1)
            $protectiontime = 9999999999999999;
        $MyDBRec = $user;
        $HeDBRec = doquery("SELECT `id`,`onlinetime`,`ally_id`,`urlaubs_modus` FROM {{table}} WHERE `id` = '" . $this->TargetPlanet['id_owner'] . "';", 'users', true);
        $User2Points = doquery("SELECT `total_points` FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . $HeDBRec['id'] . "';", 'statpoints', true);
        $MyGameLevel = $MyDBRec['total_points'];
        $HeGameLevel = $User2Points['total_points'];
        if ($HeDBRec['onlinetime'] >= (time() - INACTIVITY_SHORT))
        {
            if ($MyGameLevel > ($HeGameLevel * $protectionmulti) && $protection == 1 && $HeGameLevel < $protectiontime)
                message("<font color=\"lime\"><b>" . $lang['fl_week_player'] . "</b></font>", "game.$phpEx?page=fleet", 2);

            if (($MyGameLevel * $protectionmulti) < $HeGameLevel && $protection == 1 && $MyGameLevel < $protectiontime)
                message("<font color=\"red\"><b>" . $lang['fl_strong_player'] . "</b></font>", "game.$phpEx?page=fleet", 2);
        }
        //--------------------------------------------------

        //-----check if target user is in vacation mode
        if ($HeDBRec['urlaubs_modus'])
            message("<font color=\"lime\"><b>" . $lang['fl_in_vacation_player'] . "</b></font>", "game.$phpEx?page=fleet", 2);
        //---------------------------------------------

    }
    private function acsControll(&$info)
    {
        if ($TargetPlanet['id_owner'] == '')
            exit(header("location:game." . $phpEx . "?page=fleet"));
        if (empty($_POST['fleet_group']))
            exit(message("you must select an acs id", "game.php?page=overview", 2));
        if ($in_war)
            $target = "u" . $universe . "g" . intval($_POST["galaxy"]) . "s" . intval($_POST["system"]) . "p" . intval($_POST["planet"]) . "t" . intval($_POST["planettype"]);
        else
            $target = "g" . intval($_POST["galaxy"]) . "s" . intval($_POST["system"]) . "p" . intval($_POST["planet"]) . "t" . intval($_POST["planettype"]);
        //end mod
        if ($_POST['acs_target_mr'] == $target)
        {
            $aks_count_mr = mysql_result(doquery("SELECT count(*) FROM {{table}} WHERE `id` = '" . intval($_POST['fleet_group']) . "'", 'aks'), 0);
            //$aks_count_mr = doquery("SELECT * FROM {{table}} WHERE id = '".intval($_POST['fleet_group'])."'",'aks');
            if ($aks_count_mr > 0)
            {
                $fleet_group_mr = $_POST['fleet_group'];
            }
        }
    }
    private function transportControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
    }
    private function stayControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
    }
    private function stayAllyControll()
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
        $StayDuration=floor($_POST['expeditiontime']);
        if ($StayDuration > floor(sqrt($this->CurrentUser['expedition_tech'])) || $StayDuration < 0)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));

        }
    }
    private function spyControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
    }
    private function colonisationControll(&$info)
    {
        global $engine;
        if (!isset($fleetarray[208]))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        if (!empty($info['target_planet']))
            message("<font color=\"red\"><b>" . $lang['fl_planet_populed'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
    }
    private function recyclingControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']) || $info['target_planet']['planettype'] != 2)
            exit(header("location:game." . $phpEx . "?page=fleet"));
        $select2 = doquery("SELECT metal, crystal FROM {{table}} WHERE universe = '" . $universe . "' AND galaxy = '" . $galaxy . "' AND system = '" . $system . "' AND planet = '" . $planet . "'", "galaxy", true);
        if ($select2['metal'] == 0 && $select2['crystal'] == 0)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }

    }
    private function destructionControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
        $countfleettype = count($fleetarray);

        if ($YourPlanet or !$UsedPlanet or $planettype != 3)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype == 1 && !(isset($fleetarray[214]) or isset($fleetarray[216])))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype == 2 && !(isset($fleetarray[214]) && isset($fleetarray[216])))
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        } elseif ($countfleettype > 2)
        {
            exit(header("location:game." . $phpEx . "?page=fleet"));
        }
        if (empty($fleetmission))
            exit(header("location:game." . $phpEx . "?page=fleet"));
    }
    private function mipControll(&$info)
    {
        //check if the target planet exist
        if (empty($info['target_planet']))
            exit(header("location:game." . $phpEx . "?page=fleet"));
    }
    private function expeditionControll(&$info)
    {
        global $resource;
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
            message("<font color=\"red\"><b>" . $lang['fl_expedition_tech_required'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);
        elseif ($ExpeditionEnCours >= $EnvoiMaxExpedition)
            message("<font color=\"red\"><b>" . $lang['fl_expedition_fleets_limit'] . "</b></font>", "game." . $phpEx . "?page=fleet", 2);

        $StayDuration = floor($_POST['expeditiontime']);

        // END CODE BY JSTAR
    }

    private function sanitizecoordinates()
    {
        $args = array('universe' => array($targetUniverse => FILTER_CALLBACK, array('options' => array($this, 'validateUniverse'))), 'galaxy' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD)), 'system' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY)), 'planet' => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_PLANET_IN_SYSTEM)), 'mission' => array($mission => FILTER_CALLBACK, array('options' => array($this, 'validateMission'))), 'fleet_group' => array('filter' => FILTER_VALIDATE_INT));
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
}

?>