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
 * @author XG 
 * @copyright 2009 => Lucky  XGProyect
 * @copyright 2011 => Jstar,Tomtom  Fork/LibreTitan
 * @license http://www.gnu.org/licenses/gpl.html GNU GPLv3 License
 * @link https://github.com/jstar88/LibreTitan
 */

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}

class ShowResourcesPage
{
    private $CurrentPlanet;
    private $CurrentUser;
    public function __construct($CurrentUser, &$CurrentPlanet)
    {
        $this->CurrentPlanet=$CurrentPlanet;
        $this->CurrentUser=$CurrentUser;    
    }
    public function show()
    {
        global $engine,$ProdGrid, $resource, $reslist, $game_config, $LegacyPlanet;

        if ($this->CurrentPlanet['planet_type'] == 3)
        {
            $game_config['metal_basic_income'] = 0;
            $game_config['crystal_basic_income'] = 0;
            $game_config['deuterium_basic_income'] = 0;
        }

        $ValidList['percent'] = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);

        if ($_POST)
        {
            foreach ($_POST as $Field => $Value)
            {
                $FieldName = $Field . "_porcent";
                if (isset($this->CurrentPlanet[$FieldName]))
                {
                    if (!in_array($Value, $ValidList['percent']))
                    {
                        header("Location: game.php?page=ressources");
                        exit;
                    }

                    $Value = $Value / 10;
                    $this->CurrentPlanet[$FieldName] = $Value;
                    $LegacyPlanet[$FieldName] = $FieldName;
                }
            }
        }
        $engine->assign('production_level',100);
        if ($this->CurrentPlanet['energy_max'] == 0 && $this->CurrentPlanet['energy_used'] > 0)
        {
            $post_porcent = 0;
        } elseif ($this->CurrentPlanet['energy_max'] > 0 && ($this->CurrentPlanet['energy_used'] + $this->CurrentPlanet['energy_max']) < 0)
        {
            $post_porcent = floor(($this->CurrentPlanet['energy_max']) / ($this->CurrentPlanet['energy_used'] * -1) * 100);
        } else
        {
            $post_porcent = 100;
        }

        if ($post_porcent > 100)
        {
            $post_porcent = 100;
        }

        $this->CurrentPlanet['metal_max'] = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $this->CurrentPlanet[$resource[22]])) - 1)) * (1 + ($this->CurrentUser['rpg_stockeur'] * STOCKEUR));
        $this->CurrentPlanet['crystal_max'] = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $this->CurrentPlanet[$resource[23]])) - 1)) * (1 + ($this->CurrentUser['rpg_stockeur'] * STOCKEUR));
        $this->CurrentPlanet['deuterium_max'] = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $this->CurrentPlanet[$resource[24]])) - 1)) * (1 + ($this->CurrentUser['rpg_stockeur'] * STOCKEUR));

        $engine->assign('resource_row','');
        $this->CurrentPlanet['metal_perhour'] = 0;
        $this->CurrentPlanet['crystal_perhour'] = 0;
        $this->CurrentPlanet['deuterium_perhour'] = 0;
        $this->CurrentPlanet['energy_max'] = 0;
        $this->CurrentPlanet['energy_used'] = 0;
        $BuildTemp = $this->CurrentPlanet['temp_max'];

        foreach ($reslist['prod'] as $ProdID)
        {
            if ($this->CurrentPlanet[$resource[$ProdID]] > 0 && isset($ProdGrid[$ProdID]))
            {
                $BuildLevelFactor = $this->CurrentPlanet[$resource[$ProdID] . "_porcent"];
                $BuildLevel = $this->CurrentPlanet[$resource[$ProdID]];
                $metal = floor(eval($ProdGrid[$ProdID]['formule']['metal']) * ($game_config['resource_multiplier']) * (1 + ($this->CurrentUser['rpg_geologue'] * GEOLOGUE)));
                $crystal = floor(eval($ProdGrid[$ProdID]['formule']['crystal']) * ($game_config['resource_multiplier']) * (1 + ($this->CurrentUser['rpg_geologue'] * GEOLOGUE)));
                $deuterium = floor(eval($ProdGrid[$ProdID]['formule']['deuterium']) * ($game_config['resource_multiplier']) * (1 + ($this->CurrentUser['rpg_geologue'] * GEOLOGUE)));

                if ($ProdID >= 4)
                {
                    $energy = floor(eval($ProdGrid[$ProdID]['formule']['energy']) * ($game_config['resource_multiplier']) * (1 + ($this->CurrentUser['rpg_ingenieur'] * INGENIEUR)));
                } else
                    $energy = floor(eval($ProdGrid[$ProdID]['formule']['energy']) * ($game_config['resource_multiplier']));
                if ($energy > 0)
                {
                    $this->CurrentPlanet['energy_max'] += $energy;
                } else
                {
                    $this->CurrentPlanet['energy_used'] += $energy;
                }

                $this->CurrentPlanet['metal_perhour'] += $metal;
                $this->CurrentPlanet['crystal_perhour'] += $crystal;
                $this->CurrentPlanet['deuterium_perhour'] += $deuterium;

                $metal = $metal * 0.01 * $post_porcent;
                $crystal = $crystal * 0.01 * $post_porcent;
                $deuterium = $deuterium * 0.01 * $post_porcent;
                $energy = $energy * 0.01 * $post_porcent;
                $Field = $resource[$ProdID] . "_porcent";
                $CurrRow = array();
                $engine->assign('name',$resource[$ProdID]);
                $engine->assign('porcent',$this->CurrentPlanet[$Field]);

                for ($Option = 10; $Option >= 0; $Option--)
                {
                    $OptValue = $Option * 10;

                    if ($Option == $CurrRow['porcent'])
                    {
                        $OptSelected = " selected=selected";
                    } else
                    {
                        $OptSelected = "";
                    }
                    $option .= "<option value=\"" . $OptValue . "\"" . $OptSelected . ">" . $OptValue . "%</option>";
                    $engine->assign('option',$option);
                }
                
                $engine->assign('type',$lang['tech'][$ProdID]);
                $engine->assign('level', ($ProdID > 200) ? $lang['rs_amount'] : $lang['rs_lvl']);
                $engine->assign('level_type',$this->CurrentPlanet[$resource[$ProdID]]);
                $engine->assign('metal_type', pretty_number($metal));
                $engine->assign('crystal_type', pretty_number($crystal));
                $engine->assign('deuterium_type', pretty_number($deuterium));
                $engine->assign('energy_type', pretty_number($energy));
                $engine->assign('metal_type', colorNumber($CurrRow['metal_type']));
                $engine->assign('crystal_type', colorNumber($CurrRow['crystal_type']));
                $engine->assign('deuterium_type', colorNumber($CurrRow['deuterium_type']));
                $engine->assign('energy_type', colorNumber($CurrRow['energy_type']));
                $engine->assign('resource_row',$engine->output('resources/resources_row',true));
            }
        }
        $engine->assign('Production_of_resources_in_the_planet',str_replace('%s', $this->CurrentPlanet['name'], $lang['rs_production_on_planet']));

        if ($this->CurrentPlanet['energy_max'] == 0 && $this->CurrentPlanet['energy_used'] > 0)
        {
            $engine->assign('production_level', 0);
        } elseif ($this->CurrentPlanet['energy_max'] > 0 && abs($this->CurrentPlanet['energy_used']) > $this->CurrentPlanet['energy_max'])
        {
            $engine->assign('production_level', floor(($this->CurrentPlanet['energy_max']) / ($this->CurrentPlanet['energy_used'] * -1) * 100));
        } elseif ($this->CurrentPlanet['energy_max'] == 0 && abs($this->CurrentPlanet['energy_used']) > $this->CurrentPlanet['energy_max'])
        {
            $engine->assign('production_level', 0);
        } else
        {
            $engine->assign('production_level', 100);
        }
        $engine->assign('production_level', min(100,$engine->get('production_level')));

        $engine->assign('metal_basic_income', $game_config['metal_basic_income']);
        $engine->assign('crystal_basic_income', $game_config['crystal_basic_income']);
        $engine->assign('deuterium_basic_income', $game_config['deuterium_basic_income']);
        $engine->assign('energy_basic_income', $game_config['energy_basic_income']);

        if ($this->CurrentPlanet['metal_max'] < $this->CurrentPlanet['metal'])
        {
            $engine->assign('metal_max', "<font color=\"#ff0000\">");
        } else
        {
            $engine->assign('metal_max',"<font color=\"#00ff00\">");
        }
        $new= pretty_number($this->CurrentPlanet['metal_max'] / 1000) . "k</font>";
        $engine->append('metal_max',$new);

        if ($this->CurrentPlanet['crystal_max'] < $this->CurrentPlanet['crystal'])
        {
            $engine->assign('crystal_max', "<font color=\"#ff0000\">");
        } else
        {
            $engine->assign('crystal_max', "<font color=\"#00ff00\">");
        }
        $new=pretty_number($this->CurrentPlanet['crystal_max'] / 1000) . "k</font>";
        $engine->append('crystal_max',$new);

        if ($this->CurrentPlanet['deuterium_max'] < $this->CurrentPlanet['deuterium'])
        {
            $engine->assign('deuterium_max', "<font color=\"#ff0000\">");
        } else
        {
            $engine->assign('deuterium_max',"<font color=\"#00ff00\">");
        }
        $new=pretty_number($this->CurrentPlanet['deuterium_max'] / 1000) . "k</font>";
        $engine->append('deuterium_max' , $new);

        $engine->assign('metal_total',colorNumber(pretty_number(floor((($this->CurrentPlanet['metal_perhour'] * 0.01 * $engine->get('production_level')) +  $engine->get('metal_basic_income'))))));
        $engine->assign('crystal_total', colorNumber(pretty_number(floor((($this->CurrentPlanet['crystal_perhour'] * 0.01 *  $engine->get('production_level')) +  $engine->get('crystal_basic_income'))))));
        $engine->assign('deuterium_total', colorNumber(pretty_number(floor((($this->CurrentPlanet['deuterium_perhour'] * 0.01 *  $engine->get('production_level')) +  $engine->get('deuterium_basic_income'))))));
        $engine->assign('energy_total', colorNumber(pretty_number(floor(($this->CurrentPlanet['energy_max'] +  $engine->get('energy_basic_income')) + $this->CurrentPlanet['energy_used']))));

        $engine->assign('daily_metal', floor($this->CurrentPlanet['metal_perhour'] * 24 * 0.01 * $engine->get('production_level') + $engine->get('metal_basic_income') * 24));
        $engine->assign('weekly_metal', floor($this->CurrentPlanet['metal_perhour'] * 24 * 7 * 0.01 * $engine->get('production_level') + $engine->get('metal_basic_income') * 24 * 7));

        $engine->assign('daily_crystal', floor($this->CurrentPlanet['crystal_perhour'] * 24 * 0.01 * $engine->get('production_level') + $engine->get('crystal_basic_income') * 24));
        $engine->assign('weekly_crystal', floor($this->CurrentPlanet['crystal_perhour'] * 24 * 7 * 0.01 * $engine->get('production_level') + $engine->get('crystal_basic_income') * 24 * 7));

        $engine->assign('daily_deuterium', floor($this->CurrentPlanet['deuterium_perhour'] * 24 * 0.01 * $engine->get('production_level') + $engine->get('deuterium_basic_income') * 24));
        $engine->assign('weekly_deuterium', floor($this->CurrentPlanet['deuterium_perhour'] * 24 * 7 * 0.01 * $engine->get('production_level') + $engine->get('deuterium_basic_income') * 24 * 7));

        $engine->assign('daily_metal', colorNumber(pretty_number($engine->get('daily_metal'))));
        $engine->assign('weekly_metal', colorNumber(pretty_number($engine->get('weekly_metal'))));

        $engine->assign('daily_crystal', colorNumber(pretty_number($engine->get('daily_crystal'))));
        $engine->assign('weekly_crystal', colorNumber(pretty_number($engine->get('weekly_crystal'))));

        $engine->assign('daily_deuterium', colorNumber(pretty_number($engine->get('daily_deuterium'))));
        $engine->assign('weekly_deuterium', colorNumber(pretty_number($engine->get('weekly_deuterium'))));


        $QryUpdatePlanet = "UPDATE {{table}} SET ";
        $QryUpdatePlanet .= "`id` = '" . $this->CurrentPlanet['id'] . "' ";
        $QryUpdatePlanet .= $SubQry;
        $QryUpdatePlanet .= "WHERE ";
        $QryUpdatePlanet .= "`id` = '" . $this->CurrentPlanet['id'] . "';";
        doquery($QryUpdatePlanet, 'planets');

        return display($engine->output('resources/resources'));
    }
}

?>