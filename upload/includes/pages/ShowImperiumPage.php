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

class ShowImperiumPage
{
    private $CurrentUser;
    public function __construct($CurrentUser)
    {
        $this->CurrentUser = $CurrentUser;
    }
    public function show()
    {
        global $engine, $resource, $reslist, $dpath, $planetlist;

        $planetsrow = $planetlist;
        $planet = array();

        while ($p = mysql_fetch_array($planetsrow))
        {
            $planet[] = $p;
        }

        $engine->assign('mount', count($planet) + 1);

        foreach ($planet as $p)
        {
            $datat = array('<a href="game.php?page=overview&cp=' . $p['id'] . '&amp;re=0"><img src="' . $dpath . 'planeten/small/s_' . $p['image'] . '.jpg" border="0" height="80" width="80"></a>', $p['name'], "[<a href=\"game.php?page=galaxy&mode=3&universe={$p['universe']}&galaxy={$p['galaxy']}&system={$p['system']}\">{$p['universe']}:{$p['galaxy']}:{$p['system']}:{$p['planet']}</a>]", $p['field_current'] . '/' . $p['field_max'], '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['metal']) . '</a> / ' . pretty_number($p['metal_perhour']), '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['crystal']) . '</a> / ' . pretty_number($p['crystal_perhour']), '<a href="game.php?page=resources&cp=' . $p['id'] . '&amp;re=0&amp;planettype=' . $p['planet_type'] . '">' . pretty_number($p['deuterium']) . '</a> / ' . pretty_number($p['deuterium_perhour']),
                pretty_number($p['energy_max'] - $p['energy_used']) . ' / ' . pretty_number($p['energy_max']));
            $f = array('file_images', 'file_names', 'file_coordinates', 'file_fields', 'file_metal', 'file_crystal', 'file_deuterium', 'file_energy');
            for ($k = 0; $k < 8; $k++)
            {
                $engine->assign('text', $datat[$k]);
                $parse[$f[$k]] .= $engine->output('empire/empire_row', true);
            }

            foreach ($resource as $i => $res)
            {
                $engine->assign('text', ($p[$resource[$i]] == 0 && $this->CurrentUser[$resource[$i]] == 0) ? '-' : ((in_array($i, $reslist['build'])) ? "<a href=\"game.php?page=buildings&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ((in_array($i, $reslist['tech'])) ? "<a href=\"game.php?page=buildings&mode=research&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$this->CurrentUser[$resource[$i]]}</a>" : ((in_array($i, $reslist['fleet'])) ? "<a href=\"game.php?page=buildings&mode=fleet&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : ((in_array($i, $reslist['defense'])) ? "<a href=\"game.php?page=buildings&mode=defense&cp={$p['id']}&amp;re=0&amp;planettype={$p['planet_type']}\">{$p[$resource[$i]]}</a>" : '-')))));
                $r[$i] .= $engine->output('empire/empire_row', true);
            }
        }

        $m = array('build', 'tech', 'fleet', 'defense');

        $n = array('building_row', 'technology_row', 'fleet_row', 'defense_row');

        for ($j = 0; $j < 4; $j++)
        {
            foreach ($reslist[$m[$j]] as $a => $i)
            {
                $data['text'] = $lang['tech'][$i];
                $parse[$n[$j]] .= "<tr>" . $engine->output('empire/empire_row', true) . $r[$i] . "</tr>";
            }
        }

        return display($engine->output('empire/empire_table'), false);
    }
}

?>
