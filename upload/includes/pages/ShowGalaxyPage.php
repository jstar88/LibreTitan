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

class ShowGalaxyPage extends GalaxyRows
{
    public function __construct($CurrentUser, $CurrentPlanet)
    {
        parent::__construct($CurrentUser,$CurrentPlanet);
        $this->sanitizecoordinates();
    }
    public function updatePosition()
    {
        $mode = 0;
        if (isset($_GET['mode']))
            $mode = $_GET['mode'];                

        if ($mode == 0)
        {
            
        } 
        elseif ($mode == 1)
        {
            if (!empty($_POST["universe"]))
            {
                $this->TargetUniverse=$_POST["universe"];
            }                
            if (!empty($_POST["galaxy"]))
            {
                $this->TargetGalaxy = $_POST["galaxy"];
            } 
            if (!empty($_POST["system"]))
            {
                $this->TargetSystem = $_POST["system"];
            }

            if (!empty($_POST["universeLeft"]))
            {   
                $universes_in_war_with_this_universe = universesInWarWith($this->TargetUniverse);
                $index = array_search($this->TargetUniverse, $universes_in_war_with_this_universe);
                $index--;
                if ($index >= 0)
                {
                    $this->TargetUniverse = $universes_in_war_with_this_universe[$index]; 
                }
                else
                {
                    $lenght=count($universes_in_war_with_this_universe);
                    $this->TargetUniverse = $universes_in_war_with_this_universe[$lenght-1]; 
                }                 
            } 
            elseif (!empty($_POST["universeRight"]))
            {
                $universes_in_war_with_this_universe = universesInWarWith($this->TargetUniverse);
                $index = array_search($this->TargetUniverse, $universes_in_war_with_this_universe);
                $lenght = count($universes_in_war_with_this_universe);
                $index++;
                if ($index < $lenght)
                {
                    $this->TargetUniverse = $universes_in_war_with_this_universe[$index];
                } 
                else
                {
                    $this->TargetUniverse = $universes_in_war_with_this_universe[0];
                }
            }
            elseif (!empty($_POST["galaxyLeft"]))
            {
                if($this->TargetGalaxy > 1)
                {
                    $this->TargetGalaxy--;
                }
            }
            elseif (!empty($_POST["galaxyRight"]))
            {
                if ($this->TargetGalaxy < MAX_GALAXY_IN_WORLD-1)
                {
                    $this->TargetGalaxy++;
                } 
            }
            elseif (!empty($_POST["systemLeft"]))
            {
                if ($this->TargetSystem > 1)
                {
                    $this->TargetSystem--;
                }
            } 
            elseif (!empty($_POST["systemRight"]))
            {
                if ($this->TargetSystem < MAX_SYSTEM_IN_GALAXY-1)
                {
                    $this->TargetSystem++;
                } 
            } 
        }
        elseif ($mode == 2 || $mode== 3)
        {
            if (!empty($_GET["universe"]))
            {
                $this->TargetUniverse=$_GET["universe"];
            }                
            if (!empty($_POST["galaxy"]))
            {
                $this->TargetGalaxy = $_GET["galaxy"];
            } 
            if (!empty($_GET["system"]))
            {
                $this->TargetSystem = $_GET["system"];
            }               
        }
        $this->consumption(); 
    }
    
    public function consumption()
    {
        $mode=$_GET['mode'];
        if ($mode == 1 || $mode == 3)
        {
            if ( $this->CurrentSystem != $this->TargetSystem  ||  $this->CurrentGalaxy != $this->TargetGalaxy  )
            {
                if($this->CurrentDeuterium < 10)
                {
                    die (message(Xtreme::get('gl_no_deuterium_to_view_galaxy'), "game.php?page=galaxy&mode=0", 2));
                }
                else
                {
                    $QryGalaxyDeuterium   = "UPDATE {{table}} SET ";
                    $QryGalaxyDeuterium  .= "`deuterium` = `deuterium` -  10 ";
                    $QryGalaxyDeuterium  .= "WHERE ";
                    $QryGalaxyDeuterium  .= "`id` = '". $this->CurrentPlanetId ."' ";
                    $QryGalaxyDeuterium  .= "LIMIT 1;";
                    doquery($QryGalaxyDeuterium, 'planets');
                }
            }
        }    
    }

    public function show()
    {
        global $dpath;
        
        $GalaxyInfo = doquery( "SELECT {{table}}galaxy.metal,{{table}}galaxy.crystal,{{table}}galaxy.id_luna,{{table}}galaxy.destruyed_moon,{{table}}galaxy.id_planet,{{table}}planets.planet,{{table}}planets.destruyed,{{table}}planets.name,{{table}}planets.image,{{table}}planets.last_update,{{table}}planets.id_owner,{{table}}users.id,{{table}}users.ally_id,{{table}}users.bana,{{table}}users.urlaubs_modus,{{table}}users.onlinetime,{{table}}users.username,{{table}}statpoints.stat_type,{{table}}statpoints.stat_code,{{table}}statpoints.total_rank,{{table}}statpoints.total_points,{{table}}moons.diameter,{{table}}moons.temp_min,{{table}}moons.name AS name_moon,{{table}}alliance.ally_name,{{table}}alliance.ally_tag,{{table}}alliance.ally_web,{{table}}alliance.ally_members,{{table}}buddy.owner AS friends_owner,{{table}}buddy.sender AS friends_sender
                                FROM {{table}}alliance RIGHT JOIN ({{table}}planets AS {{table}}moons RIGHT JOIN ({{table}}statpoints RIGHT JOIN ((({{table}}planets INNER JOIN {{table}}users ON {{table}}planets.id_owner = {{table}}users.id) INNER JOIN {{table}}galaxy ON {{table}}planets.id = {{table}}galaxy.id_planet) LEFT JOIN {{table}}buddy ON ({{table}}buddy.owner = {{table}}planets.id_owner OR {{table}}buddy.sender = {{table}}planets.id_owner)) ON ({{table}}statpoints.id_owner={{table}}users.id AND {{table}}statpoints.stat_code=1 AND {{table}}statpoints.stat_type=1)) ON {{table}}moons.id = {{table}}galaxy.id_luna) ON {{table}}alliance.id = {{table}}users.ally_id
                                WHERE ({{table}}galaxy.universe='".$this->TargetUniverse."' AND {{table}}galaxy.galaxy='".$this->TargetGalaxy."' AND {{table}}galaxy.system='".$this->TargetSystem."' AND ({{table}}galaxy.planet>'0' AND {{table}}galaxy.planet<='".MAX_PLANET_IN_SYSTEM."'))
                                GROUP BY `id_planet`;" , '' );
        $mode=$_GET['mode'];
        $planetcount=0;
        Xtreme::assign('universe', $this->TargetUniverse);
        Xtreme::assign('galaxy', $this->TargetGalaxy);
        Xtreme::assign('system', $this->TargetSystem);
        Xtreme::assign('currentmip', $this->CurrentMIP);
        Xtreme::assign('maxfleetcount', $this->maxfleet_count);
        Xtreme::assign('fleetmax', $this->fleetmax);
        Xtreme::assign('recyclers', pretty_number($this->CurrentRC));
        Xtreme::assign('spyprobes', pretty_number($this->CurrentSP));
        Xtreme::assign('missile_count', sprintf(Xtreme::get('gl_missil_to_launch'), $this->CurrentMIP));
        Xtreme::assign('current', $_GET['current']);//?
        Xtreme::assign('current_universe', $this->CurrentUniverse);
        Xtreme::assign('current_galaxy', $this->CurrentGalaxy);
        Xtreme::assign('current_system', $this->CurrentSystem);
        Xtreme::assign('current_planet', $this->CurrentPlanet);
        Xtreme::assign('planet_type', $this->CurrentPlanetType);
        Xtreme::assign('dpath', $dpath);
        Xtreme::assignToGroup('x', 'galaxyscripts', 'galaxy/galaxy_script');
        
        if (isUniverseInWarGeneric($this->CurrentUniverse))
        {
            Xtreme::assignToGroup('x', 'universe_collision', 'galaxy/galaxy_collision');
            Xtreme::assignToGroup('x', 'galaxyselector', 'galaxy/galaxy_selector_extended');
            if ($mode == 2)
                Xtreme::assignToGroup('x', 'mip', 'galaxy/galaxy_missile_selector_extended');
        } 
        else
        {
            if ($mode == 2)
                Xtreme::assignToGroup('x', 'mip', 'galaxy/galaxy_missile_selector');
            Xtreme::assignToGroup('x', 'galaxyselector', 'galaxy/galaxy_selector');
        }
        Xtreme::assignToGroup('x', 'galaxytitles', 'galaxy/galaxy_titles');
        $html = $this->ShowGalaxyRows($GalaxyInfo,$planetcount);
        Xtreme::assign('planetcount', $planetcount . " " . Xtreme::get('gl_populed_planets'));
        Xtreme::assign('galaxyrows', $html);
        Xtreme::assignToGroup('x', 'galaxyfooter', 'galaxy/galaxy_footer');
        $galaxypage=Xtreme::outputGroup('x', 'galaxy/galaxy_body');
        display2($galaxypage, false);
    }

    private function ShowGalaxyRows($GalaxyInfo,&$planetcount)
    {

        $positions=array_fill(1, MAX_PLANET_IN_SYSTEM, false);   
        while ($RowInfo = mysql_fetch_array($GalaxyInfo))
        {   
            if (!empty($RowInfo['id_planet']))
            {
            	 if (!empty($RowInfo['destruyed']) && !empty($RowInfo['id_owner']))
                {
                    $this->CheckAbandonPlanetState($RowInfo);
                } 
                elseif (!empty($RowInfo['id_luna']) && !empty($RowInfo['destruyed_moon']))
                {
                	$this->CheckAbandonMoonState($RowInfo);
                }
				    $row  = "\n";
                $row .= "<tr>";
                $row .= $this->GalaxyRowPos($RowInfo);
                $row .= $this->GalaxyRowPlanet($RowInfo);
                $row .= $this->GalaxyRowPlanetName($RowInfo);
                $row .= $this->GalaxyRowMoon($RowInfo);
                $row .= $this->GalaxyRowDebris($RowInfo);
                $row .= $this->GalaxyRowUser($RowInfo);
                $row .= $this->GalaxyRowAlly($RowInfo);
                $row .= $this->GalaxyRowActions($RowInfo);
                $row .= "</tr>";
				    $positions[$RowInfo['planet']]=$row;  
                $planetcount++;                          
            }
        }
        unset($RowInfo);
        foreach($positions as $position => $thereSomething )
        {
            if($thereSomething === false)
            {
               Xtreme::assign('pos', $position);
               $positions[$position]=Xtreme::output('galaxy/galaxy_row', true);
            }
        }
        return implode($positions);
    }
    
    private function sanitizecoordinates()
    {
        $args = array(
        'mode'              => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 0, 'max_range' => 3)),
        'universe'          => array($targetUniverse => FILTER_CALLBACK, array('options' => array($this,'validateUniverse'))),
        'galaxy'            => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_GALAXY_IN_WORLD)), 
        'system'            => array('filter' => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1, 'max_range' => MAX_SYSTEM_IN_GALAXY)), 
        'universeLeft'      =>array("filter"=>FILTER_SANITIZE_STRING),
        'universeRight'     =>array("filter"=>FILTER_SANITIZE_STRING),
        'galaxyLeft'        =>array("filter"=>FILTER_SANITIZE_STRING),
        'galaxyRight'       =>array("filter"=>FILTER_SANITIZE_STRING),
        'systemRight'       =>array("filter"=>FILTER_SANITIZE_STRING),
        'systemLeft'        =>array("filter"=>FILTER_SANITIZE_STRING)
        );      
        $_POST = filter_input_array(INPUT_POST, $args);
        $_GET = filter_input_array(INPUT_GET , $args);     
    }
    
    private function validateUniverse($targetUniverse){
        $targetUniverse= min(1,max($targetUniverse,MAX_UNIVERSE_IN_WORLD));
        if(isUniverseInWar($targetUniverse, $this->CurrentUniverse))
            return $targetUniverse; 
        return false;       
    }

}

?>