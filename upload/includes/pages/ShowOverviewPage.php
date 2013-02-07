<?php

class ShowOverviewPage
{
    private $mode;
    private $CurrentUser;
    private $CurrentPlanet;
    public function __construct($CurrentUser, $CurrentPlanet)
    {
        $this->mode = 'default';
        $this->CurrentUser = $CurrentUser;
        $this->CurrentPlanet = $CurrentPlanet;
    }
    public function controll()
    {
        if ($_GET['mode'] === 'renameplanet')
        {
            if ($_POST['action'] === 'ov_planet_rename_action')
            {
                $this->mode = 'renameplanet';
                $this->renamePlanet();
            } elseif ($_POST['action'] === 'ov_abandon_planet')
            {
                $this->mode = 'deleteplanet';
            } elseif (intval($_POST['kolonieloeschen']) == 1 && intval($_POST['deleteid']) == $this->CurrentUser['current_planet'])
            {
                $this->deletePlanet();
            }
        }
    }
    private function renamePlanet()
    {
        $newname = mysql_escape_string(strip_tags(trim($_POST['newname'])));

        if (preg_match("/[^A-z0-9_\- ]/", $newname) == 1)
        {
            message('ov_newname_error', "game.php?page=overview&mode=renameplanet", 2);
        }
        if ($newname != "")
        {
            doquery("UPDATE {{table}} SET `name` = '" . $newname . "' WHERE `id` = '" . $this->CurrentUser['current_planet'] . "' LIMIT 1;", "planets");
        }
    }
    private function deletePlanet()
    {
        $query = "SELECT count(*) as count FROM {{table}} WHERE fleet_start_id = '" . $this->CurrentPlanet['id'] . "' OR fleet_end_id = '" . $this->CurrentPlanet['id'] . "'";
        $result = mysql_fetch_object(doquery($query, 'fleets'));
        if ($result->count > 0)
        {
            message('ov_abandon_planet_not_possible', 'game.php?page=overview&mode=renameplanet');
        }
        else
        {
            if (md5($_POST['pw']) == $this->CurrentUser["password"] && $this->CurrentUser['id_planet'] != $this->CurrentUser['current_planet'])
            {
                doquery("UPDATE {{table}} SET `current_planet` = `id_planet` WHERE `id` = '" . $this->CurrentUser['id'] . "' LIMIT 1", "users");
                if ($this->CurrentPlanet['planet_type'] == 3)
                {
                    doquery("UPDATE {{table}} SET `destruyed_moon` = '" . (time() + MOON_DELETE_TIME) . "' WHERE `id_luna` = '" . $this->CurrentUser['current_planet'] . "' LIMIT 1;", 'galaxy');
                }
                else
                {
                    doquery("UPDATE {{table}} SET `destruyed_planet` = '" . (time() + PLANET_DELETE_TIME) . "' WHERE `id_luna` = '" . $this->CurrentUser['current_planet'] . "' LIMIT 1;", 'galaxy');
                }
                doquery("DELETE FROM {{table}} WHERE `id` ='" . $this->CurrentPlanet['id'] . "' LIMIT 1", 'planets');
                message('ov_planet_abandoned', 'game.php?page=overview&mode=renameplanet');
            } elseif ($this->CurrentUser['id_planet'] == $this->CurrentUser["current_planet"])
            {
                message('ov_principal_planet_cant_abanone', 'game.php?page=overview&mode=renameplanet');
            }
            else
            {
                message('ov_wrong_pass', 'game.php?page=overview&mode=renameplanet');
            }
        }


    }
    public function show()
    {
        Xtreme::assign('planet_id', $this->CurrentPlanet['id']);
        Xtreme::assign('planet_name', $this->CurrentPlanet['name']);
        Xtreme::assign('galaxy_universe', $this->CurrentPlanet['universe']);
        Xtreme::assign('galaxy_galaxy', $this->CurrentPlanet['galaxy']);
        Xtreme::assign('galaxy_system', $this->CurrentPlanet['system']);
        Xtreme::assign('galaxy_planet', $this->CurrentPlanet['planet']);

        if ($this->mode == 'renameplanet')
            $this->showRenamePlanet();
        elseif ($this->mode == 'deleteplanet')
            $this->showDeletePlanet();
        else
            $this->showDefault();
    }
    private function showDefault()
    {
        Xtreme::assign('planet_diameter', pretty_number($this->CurrentPlanet['diameter']));
        Xtreme::assign('planet_field_current', $this->CurrentPlanet['field_current']);
        Xtreme::assign('date_time', date("D M j H:i:s", time()));
        Xtreme::assign('planet_field_max', CalculateMaxPlanetFields($this->CurrentPlanet));
        Xtreme::assign('planet_temp_min', $this->CurrentPlanet['temp_min']);
        Xtreme::assign('planet_temp_max', $this->CurrentPlanet['temp_max']);
        Xtreme::assign('user_username', $this->CurrentUser['username']);
        Xtreme::assign('planet_image', $this->CurrentPlanet['image']);
        $this->buildFleetEvents();
        $this->buildNewMessages();
        $this->buildPlanetList();
        $this->buildMoonLink();
        $this->buildCurrentPlanetImage();
        $this->buildStat();
        $this->buildWars();
        display2('overview', 'overview/overview_body');
    }
    private function showRenamePlanet()
    {
        display2('', 'overview/overview_renameplanet');
    }
    private function showDeletePlanet()
    {
        display('', 'overview/overview_deleteplanet');
    }
    private function buildPlanetList()
    {
        global $planetlist;
        $planets_query = $planetlist;
        while ($OtherUserPlanet = mysql_fetch_array($planets_query))
        {
            if ($OtherUserPlanet["id"] != $this->CurrentUser["current_planet"] && $OtherUserPlanet['planet_type'] != 3)
            {
                $Coloneshow++;
                Xtreme::assign('name', $OtherUserPlanet['name']);
                Xtreme::assign('id', $OtherUserPlanet['id']);
                Xtreme::assign('image', $OtherUserPlanet['image']);
                Xtreme::assignToGroup('overview', 'anothers_planets', 'overview/colonies/colonies');

                if ($OtherUserPlanet['b_building'] != 0)
                {
                    UpdatePlanetBatimentQueueList($OtherUserPlanet, $this->CurrentUser);
                    if ($OtherUserPlanet['b_building'] != 0)
                    {
                        $BuildQueue = $OtherUserPlanet['b_building_id'];
                        $QueueArray = explode(";", $BuildQueue);
                        $CurrentBuild = explode(",", $QueueArray[0]);
                        $BuildElement = $CurrentBuild[0];
                        $BuildLevel = $CurrentBuild[1];
                        $BuildRestTime = pretty_time($CurrentBuild[3] - time());
                        Xtreme::assign('BuildLevel', Xtreme::get('tech', $BuildElement));
                        Xtreme::assign('BuildRestTime', $BuildRestTime);
                        Xtreme::assign('colonies_buildings', Xtreme::output('overview/colonies/colonies_buildings_something', true));
                    }
                    else
                    {
                        Xtreme::assign('colonies_buildings', Xtreme::output('overview/colonies/colonies_buildings_nothing', true));
                    }
                }
                else
                {
                    Xtreme::assign('colonies_buildings', Xtreme::output('overview/colonies/colonies_buildings_nothing', true));
                }
            }
        }
        mysql_free_result($planets_query);
        Xtreme::clearReadyCompiled();
    }
    private function buildMoonLink()
    {
        $lunarow = doquery("SELECT * FROM {{table}} WHERE `id_owner` = '" . $this->CurrentPlanet['id_owner'] . "' AND 
            `universe` = '" . $this->CurrentPlanet['universe'] . "' AND
            `galaxy` = '" . $this->CurrentPlanet['galaxy'] . "' AND 
            `system` = '" . $this->CurrentPlanet['system'] . "' AND  
            `planet` = '" . $this->CurrentPlanet['planet'] . "' AND 
            `planet_type`='3'", 'planets', true);
        if (!empty($lunarow['id']) && $this->CurrentPlanet['planet_type'] == 1)
        {
            Xtreme::assignToGroup('overview', 'moon_link', 'overview/moon_link');
            Xtreme::assign('lunarow_id', $lunarow['id']);
            Xtreme::assign('lunarow_name', $lunarow['name']);
            Xtreme::assign('lunarow_image', $lunarow['image']);
        }
    }
    private function buildNewMessages()
    {
        if ($this->CurrentUser['new_message'] == 1)
        {
            Xtreme::assignToGroup('overview', 'new_mes', 'overview/messages/new_message');
        } elseif ($this->CurrentUser['new_message'] > 1)
        {
            Xtreme::assignToGroup('overview', 'new_mes', 'overview/messages/new_messages');
            Xtreme::assign('ov_have_new_messages', str_replace('%m', pretty_number($this->CurrentUser['new_message']), Xtreme::get('ov_have_new_messages')));
        }
    }
    private function buildFleetEvents()
    {
        $OwnFleets = doquery("SELECT * FROM {{table}} WHERE `fleet_owner` = '" . $this->CurrentUser['id'] . "' OR `fleet_target_owner` = '" . $this->CurrentUser['id'] . "';", 'fleets');
        while ($FleetRow = mysql_fetch_array($OwnFleets))
        {
            $Record++;
            $status = $FleetRow['fleet_mess'];
            //fleet incoming to target
            if ($status == 0)
            {
                $fInfo[$StartTime] = $FleetRow;
            }
            //sta stazionando
            elseif ($status == 1)
            {
                $sInfo[$StayTime] = $FleetRow;
            }
            //tornando
            elseif ($status == 2)
            {
                $rInfo[$EndTime] = $FleetRow;
            }
        }
        mysql_free_result($OwnFleets);
        $info = ksort(array_merge($sInfo, $fInfo, $rInfo));
        Xtreme::assign('fleets', $info);
        Xtreme::assignToGroup();
        Xtreme::doLoopGroup('overview', 'fleets', 'fleet');
    }
    private function buildCurrentPlanetImage()
    {
        global $planetrow, $user;
        if ($this->CurrentPlanet['b_building'] != 0)
        {
            UpdatePlanetBatimentQueueList($planetrow, $user);
            if ($this->CurrentPlanet['b_building'] != 0)
            {
                $BuildQueue = explode(";", $this->CurrentPlanet['b_building_id']);
                $CurrBuild = explode(",", $BuildQueue[0]);
                $RestTime = $this->CurrentPlanet['b_building'] - time();
                $PlanetID = $this->CurrentPlanet['id'];
                Xtreme::assign('CallProgram', 'overview');
                Xtreme::assign('RestTime', $RestTime);
                Xtreme::assign('PlanetID', $PlanetID);
                Xtreme::assign('PrettyRestTime', pretty_time($RestTime));
                Xtreme::assign('b_name', Xtreme::get('tech', $CurrBuild[0]));
                Xtreme::assign('b_lvl', $CurrBuild[1]);
                Xtreme::assignToGroup('overview', 'building', 'overview/building/building');
            }
            else
            {
                Xtreme::assignToGroup('overview', 'building', 'overview/building/no_building');
            }
        }
        else
        {
            Xtreme::assignToGroup('overview', 'building', 'overview/building/no_building');
        }
    }
    private function buildStat()
    {
        global $game_config;
        if ($game_config['stat'] == 0 || ($game_config['stat'] == 1 && $this->CurrentUser['authlevel'] < $game_config['stat_level']))
        {
            Xtreme::assignToGroup('overview', 'user_rank', 'overview/rank/stats');
            Xtreme::assign('stats_total_rank', $this->CurrentUser['total_rank']);
            Xtreme::assign('pretty_stats_total_points', pretty_number($this->CurrentUser['total_points']));
            Xtreme::assign('users_amount', $game_config['users_amount']);
        }
        else
            Xtreme::assignToGroup('overview', 'user_rank', 'overview/rank/no_stats');
    }
    private function buildWars()
    {
        $value = 0;
        $wars = willFightWithThis($this->CurrentPlanet['universe']);

        if ($wars)
        {
            foreach ($wars as $war)
            {
                $value++;
                if ($war['war_start']>time())
                {
                    $war['descr'] = Xtreme::get('ov_countdown_lookuniverse');
                    $war['status'] = "flight ownattack";
                    $war['start']=$war['war_start']-time();
                }
                else
                {
                    $war['descr'] = Xtreme::get('ov_countdown_warinfight');
                    $war['status'] = "flight attack";
                    $war['end']=$war['war_end']-time();
                }                
            }
            Xtreme::assign('wars',$war);
            Xtreme::assign('wars_count',$value);
            Xtreme::assignToGroup('overview','wars','overviews/wars');
            Xtreme::doLoopGroup('overview','wars','war');
        }

    }
}

?>