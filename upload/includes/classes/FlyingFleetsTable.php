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

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}

class FlyingFleetsTable
{
    private function CreateFleetPopupedMissionLink($FleetRow, $Texte, $FleetType)
    {
        global $lang;

        $FleetTotalC = $FleetRow['fleet_resource_metal'] + $FleetRow['fleet_resource_crystal'] + $FleetRow['fleet_resource_deuterium'];
        if ($FleetTotalC <> 0)
        {
            $FRessource = "<table width=200>";
            $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Metal'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_metal']) . "<font></td></tr>";
            $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Crystal'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_crystal']) . "<font></td></tr>";
            $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Deuterium'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_deuterium']) . "<font></td></tr>";
            if ($FleetRow['fleet_resource_darkmatter'] > 0)
                $FRessource .= "<tr><td width=50% align=left><font color=white>" . $lang['Darkmatter'] . "<font></td><td width=50% align=right><font color=white>" . pretty_number($FleetRow['fleet_resource_darkmatter']) . "<font></td></tr>";
            $FRessource .= "</table>";
        }
        else
            $FRessource = "";

        if ($FRessource <> "")
        {
            $MissionPopup = "<a href='#' onmouseover=\"return overlib('" . $FRessource . "');";
            $MissionPopup .= "\" onmouseout=\"return nd();\" class=\"" . $FleetType . "\">" . $Texte . "</a>";
        }
        else
            $MissionPopup = $Texte . "";

        return $MissionPopup;
    }

    private function CreateFleetPopupedFleetLink($FleetRow, $Texte, $FleetType)
    {
        global $lang, $user;

        $FleetRec = explode(";", $FleetRow['fleet_array']);
        $FleetPopup = "<a href='#' onmouseover=\"return overlib('";
        $FleetPopup .= "<table width=200>";
        if (!defined('IN_ADMIN'))
        {
            if ($user['spy_tech'] < 2 && $FleetRow['fleet_owner'] != $user['id'])
            {
                $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['cff_no_fleet_data'] . "<font></td></tr>";
            } elseif ($user['spy_tech'] >= 2 && $user['spy_tech'] < 4 && $FleetRow['fleet_owner'] != $user['id'])
            {
                $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['cff_aproaching'] . $FleetRow[fleet_amount] . $lang['cff_ships'] . "<font></td></tr>";
            }
            else
            {
                if ($FleetRow['fleet_owner'] != $user['id'])
                    $FleetPopup .= "<tr><td width=100% align=left><font color=white>" . $lang['cff_aproaching'] . $FleetRow[fleet_amount] . $lang['cff_ships'] . ":<font></td></tr>";

                foreach ($FleetRec as $Item => $Group)
                {
                    if ($Group != '')
                    {
                        $Ship = explode(",", $Group);
                        if ($FleetRow['fleet_owner'] == $user['id'])
                            $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['tech'][$Ship[0]] . ":<font></td><td width=50% align=right><font color=white>" . pretty_number($Ship[1]) . "<font></td></tr>";
                        elseif ($FleetRow['fleet_owner'] != $user['id'])
                        {
                            if ($user['spy_tech'] >= 4 && $user['spy_tech'] < 8)
                                $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['tech'][$Ship[0]] . "<font></td></tr>";
                            elseif ($user['spy_tech'] >= 8)
                                $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['tech'][$Ship[0]] . ":<font></td><td width=50% align=right><font color=white>" . pretty_number($Ship[1]) . "<font></td></tr>";
                        }
                    }
                }
            }
        } elseif (defined('IN_ADMIN'))
        {
            foreach ($FleetRec as $Item => $Group)
            {
                if ($Group != '')
                {
                    $Ship = explode(",", $Group);
                    $FleetPopup .= "<tr><td width=50% align=left><font color=white>" . $lang['tech'][$Ship[0]] . ":<font></td><td width=50% align=right><font color=white>" . pretty_number($Ship[1]) . "<font></td></tr>";
                }
            }
        }

        $FleetPopup .= "</table>";
        $FleetPopup .= "');\" onmouseout=\"return nd();\" class=\"" . $FleetType . "\">" . $Texte . "</a>";

        return $FleetPopup;
    }

    private function BuildHostileFleetPlayerLink($FleetRow)
    {
        global $lang, $dpath;

        $PlayerName = doquery("SELECT `username` FROM {{table}} WHERE `id` = '" . intval($FleetRow['fleet_owner']) . "';", 'users', true);
        $Link = $PlayerName['username'] . " ";
        $Link .= "<a href=\"game.php?page=messages&mode=write&id=" . $FleetRow['fleet_owner'] . "\">";
        $Link .= "<img src=\"" . $dpath . "/img/m.gif\" title=\"" . $lang['write_message'] . "\" border=\"0\"></a>";

        return $Link;
    }

    // For ShowFlyingFleets.php in admin panel.
    public function BuildFlyingFleetTable()
    {
        global $lang;

        $FlyingFleets = doquery("SELECT * FROM {{table}} ORDER BY `fleet_end_time` ASC;", 'fleets');

        while ($CurrentFleet = mysql_fetch_assoc($FlyingFleets))
        {
            $FleetOwner = doquery("SELECT `username` FROM {{table}} WHERE `id` = '" . intval($CurrentFleet['fleet_owner']) . "';", 'users', true);
            $TargetOwner = doquery("SELECT `username` FROM {{table}} WHERE `id` = '" . intval($CurrentFleet['fleet_target_owner']) . "';", 'users', true);

            $Bloc['Id'] = $CurrentFleet['fleet_id'];
            $Bloc['Mission'] = $this->CreateFleetPopupedMissionLink($CurrentFleet, $lang['type_mission'][$CurrentFleet['fleet_mission']], '');
            $Bloc['Mission'] .= "<br>" . (($CurrentFleet['fleet_mess'] == 1) ? "R" : "A");
            $Bloc['Fleet'] = $this->CreateFleetPopupedFleetLink($CurrentFleet, $lang['tech'][200], '');
            $Bloc['St_Owner'] = "[" . $CurrentFleet['fleet_owner'] . "]<br>" . $FleetOwner['username'];
            //start mod
            $Bloc['St_Posit'] = "[" . $CurrentFleet['fleet_start_universe'] . ":" . $CurrentFleet['fleet_start_galaxy'] . ":" . $CurrentFleet['fleet_start_system'] . ":" . $CurrentFleet['fleet_start_planet'] . "]<br>" . (($CurrentFleet['fleet_start_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_start_type'] == 2) ? "D" : "L")) . "";
            //end mod
            $Bloc['St_Time'] = date('G:i:s d/n/Y', $CurrentFleet['fleet_start_time']);
            if (is_array($TargetOwner))
                $Bloc['En_Owner'] = "[" . $CurrentFleet['fleet_target_owner'] . "]<br>" . $TargetOwner['username'];
            else
                $Bloc['En_Owner'] = "";
            //start mod
            $Bloc['En_Posit'] = "[" . $CurrentFleet['fleet_end_universe'] . ":" . $CurrentFleet['fleet_end_galaxy'] . ":" . $CurrentFleet['fleet_end_system'] . ":" . $CurrentFleet['fleet_end_planet'] . "]<br>" . (($CurrentFleet['fleet_end_type'] == 1) ? "[P]" : (($CurrentFleet['fleet_end_type'] == 2) ? "D" : "L")) . "";
            //end mod
            if ($CurrentFleet['fleet_mission'] == 5)
            {
                if ($CurrentFleet['fleet_mess'] == 2)
                    $Bloc['Wa_Time'] = date('G:i:s d/n/Y', $CurrentFleet['fleet_end_stay']);
                elseif ($CurrentFleet['fleet_mess'] == 1)
                    $Bloc['Wa_Time'] = $lang['cff_back'];
                else
                    $Bloc['Wa_Time'] = $lang['cff_to_destination'];
            }
            else
            {
                $Bloc['Wa_Time'] = "";
            }

            $Bloc['En_Time'] = date('G:i:s d/n/Y', $CurrentFleet['fleet_end_time']);

            $table .= parsetemplate(gettemplate('adm/fleet_rows'), $Bloc);
        }

        return $table;
    }

    //For overview and phalanx
    public function BuildFleetEventTable($FleetRow, $Status, $Owner,$Record)
    {

        if ($Owner)
            $FleetPrefix = 'own';
        else
            $FleetPrefix = '';

        $RowsTPL = gettemplate('overview/overview_fleet_event');
        $MissionType = $FleetRow['fleet_mission'];
        $FleetContent = $this->CreateFleetPopupedFleetLink($FleetRow, "flotas", $FleetPrefix . $FleetStyle[$MissionType]);
        $FleetCapacity = $this->CreateFleetPopupedMissionLink($FleetRow, $lang['type_mission'][$MissionType], $FleetPrefix . $FleetStyle[$MissionType]);
        $StartPlanet = $FleetRow['fleet_start_name'];
        $StartType = $FleetRow['fleet_start_type'];
        $TargetPlanet = $FleetRow['fleet_end_name'];
        $TargetType = $FleetRow['fleet_end_type'];

        $FleetStyle = array(1 => 'attack', 2 => 'federation', 3 => 'transport', 4 => 'deploy', 5 => 'hold', 6 => 'espionage', 7 => 'colony', 8 => 'harvest', 9 => 'destroy', 10 => 'missile', 15 => 'transport', );
        $FleetStatus = array(0 => 'flight', 1 => 'holding', 2 => 'return');
        $bloc['fleet_status'] = $FleetStatus[$Status];
        $bloc['fleet_prefix'] = $FleetPrefix;
        $bloc['fleet_style'] = $FleetStyle[$MissionType];
        $bloc['fleet_order'] = $Record;
        $bloc['fleet_descr'] = $EventString;

        return parsetemplate($RowsTPL, $bloc);
    }
}

?>