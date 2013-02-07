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
class ShowTraderPage
{
    private $metal_storage;
    private $crystal_storage;
    private $deuterium_storage;
    private $CurrentMetal;
    private $CurrentCrystal;
    private $CurrentDeuterium;
    private $CurrentPlanetId;

    public function __construct($CurrentUser, $CurrentPlanet)
    {
        global $resource;
        $this->metal_storage = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $CurrentPlanet[$resource[22]])) - 1)) * (1 + ($CurrentUser['rpg_stockeur'] * STOCKEUR));
        $this->crystal_storage = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $CurrentPlanet[$resource[23]])) - 1)) * (1 + ($CurrentUser['rpg_stockeur'] * STOCKEUR));
        $this->deuterium_storage = (BASE_STORAGE_SIZE + 50000 * (roundUp(pow(1.6, $CurrentPlanet[$resource[24]])) - 1)) * (1 + ($CurrentUser['rpg_stockeur'] * STOCKEUR));
        $this->CurrentMetal = $CurrentPlanet['metal'];
        $this->CurrentCrystal = $CurrentPlanet['crystal'];
        $this->CurrentDeuterium = $CurrentPlanet['deuterium'];
        $this->CurrentPlanetId = $CurrentPlanet['id'];
        $this->sanitizeinput();
    }
    public function show()
    {
        global $engine;
        if ($_POST['action'] != 2)
        {
            display($engine->output('trader/trader_main'));
        } else
        {
            $parse['mod_ma_res'] = '1';
            switch ($_POST['choix'])
            {
                case 'metal':
                    $template = 'trader/trader_metal';
                    $engine->assign('mod_ma_res_a', '2');
                    $engine->assign('mod_ma_res_b', '4');
                    $engine->assign('max_resource', $this->CurrentMetal);
                    $engine->assign('max_resource1', floor($this->crystal_storage - $this->CurrentCrystal));
                    $engine->assign('max_resource2', floor($this->deuterium_storage - $this->CurrentDeuterium));
                    break;
                case 'crystal':
                    $template = 'trader/trader_cristal';
                    $engine->assign('mod_ma_res_a', '0.5');
                    $engine->assign('mod_ma_res_b', '2');
                    $engine->assign('max_resource', $this->CurrentCrystal);
                    $engine->assign('max_resource1', floor($this->metal_storage - $this->CurrentMetal));
                    $engine->assign('max_resource2', floor($this->deuterium_storage - $this->CurrentDeuterium));
                    break;
                case 'deut':
                    $template = 'trader/trader_deuterium';
                    $engine->assign('mod_ma_res_a', '0.25');
                    $engine->assign('mod_ma_res_b', '0.5');
                    $engine->assign('max_resource', $this->CurrentDeuterium);
                    $engine->assign('max_resource1', floor($this->metal_storage - $this->CurrentMetal));
                    $engine->assign('max_resource2', floor($this->crystal_storage - $this->CurrentCrystal));
                    break;
            }
            display($engine->output($template));
        }
    }
    public function exchange()
    {
      global $engine,$phpEx;
        if (empty($_POST['ress']))
            return;
        $proposal = $_POST['ress'];
        if ($proposal == 'metal')
        {
            if (empty($_POST['crystal']) && empty($_POST['deuterium']))
            {
                message($engine->get('tr_only_positive_numbers'), "game." . $phpEx . "?page=trader", 1);
            }
            $crystal    = max(0,min($this->crystal_storage   - $this->CurrentCrystal,   $_POST['crystal']));
            $deuterium  = max(0,min($this->deuterium_storage - $this->CurrentDeuterium, $_POST['deuterium']));
            $necessaire = $crystal * 2 + $deuterium * 4;
            if ($this->CurrentMetal < $necessaire)
            {
                message($engine->get('tr_not_enought_metal'), "game." . $phpEx . "?page=trader", 1);
            }
            $this->CurrentMetal     -= $necessaire;
            $this->CurrentCrystal   += $crystal;
            $this->CurrentDeuterium += $deuterium;

        } elseif ($proposal == 'crystal')
        {
            if (empty($_POST['metal']) && empty($_POST['deuterium']))
            {
                message($engine->get('tr_only_positive_numbers'), "game." . $phpEx . "?page=trader", 1);
            }
            $metal      = max(0,min($this->metal_storage     - $this->CurrentMetal,     $_POST['metal']));
            $deuterium  = max(0,min($this->deuterium_storage - $this->CurrentDeuterium, $_POST['deuterium']));
            $necessaire = round($metal * 0.5 + $deuterium * 2);
            if ($this->CurrentCrystal < $necessaire)
            {
                message($engine->get('tr_not_enought_crystal'), "game." . $phpEx . "?page=trader", 1);
            }
            $this->CurrentMetal     += $metal;
            $this->CurrentCrystal   -= $necessaire;
            $this->CurrentDeuterium += $deuterium;
        } else
        {
            if (empty($_POST['metal']) && empty($_POST['crystal']))
            {
                message($engine->get('tr_only_positive_numbers'), "game." . $phpEx . "?page=trader", 1);
            }
            $metal      = max(0,min($this->metal_storage   - $this->CurrentMetal,   $_POST['metal']));
            $crystal    = max(0,min($this->crystal_storage - $this->CurrentCrystal, $_POST['crystal']));
            $necessaire = round($metal * 0.25 + $crystal * 0.5);
            if ($this->CurrentDeuterium < $necessaire)
            {
                message($engine->get('tr_not_enought_deuterium'), "game." . $phpEx . "?page=trader", 1);
            }
            $this->CurrentMetal     += $metal;
            $this->CurrentCrystal   += $crystal;
            $this->CurrentDeuterium -= $necessaire;
        }
        $this->save();
    }
    private function save()
    {
        $QryUpdatePlanet = "UPDATE {{table}} SET";
        $QryUpdatePlanet .= " `metal` ="      . $this->CurrentMetal;
        $QryUpdatePlanet .= " ,`crystal` ="   . $this->CurrentCrystal;
        $QryUpdatePlanet .= " ,`deuterium` =" . $this->CurrentDeuterium;
        $QryUpdatePlanet .= " WHERE ";
        $QryUpdatePlanet .= "`id` = '" . $this->CurrentPlanetId . "';";
        doquery($QryUpdatePlanet, 'planets');
        doquery("UPDATE `{{table}}` SET `darkmatter` = `darkmatter` - " . TR_DARK_MATTER . " WHERE `id` = " . $this->CurrentPlanetId, 'users');
    }
    private function sanitizeinput()
    {
        $args = array(
            'metal'     => array('filter'  => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1)), 
            'crystal'   => array('filter'  => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1)),
            'deuterium' => array('filter'  => FILTER_VALIDATE_INT, 'options' => array('min_range' => 1)), 
            'choix'     => array("filter"  => FILTER_SANITIZE_STRING), 'action' => array("filter" => FILTER_SANITIZE_STRING),
            'ress'      => array($proposal => FILTER_CALLBACK, array('options' => array($this, 'validateRess'))));
        $_POST = filter_input_array(INPUT_POST, $args);
        $_GET = filter_input_array(INPUT_GET, $args);
    }
    private function validateRess($proposal)
    {
        if ($proposal != 'metal' && $proposal != 'crystal' && $proposal != 'deuterium')
            return false;
        return $proposal;
    }

}

?>
