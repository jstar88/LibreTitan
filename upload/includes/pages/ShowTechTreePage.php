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

class ShowTechTreePage
{
    private $CurrentUser;
    private $CurrentPlanet;
    public function __construct($CurrentUser, $CurrentPlanet)
    {
        $this->CurrentUser = $CurrentUser;
        $this->CurrentPlanet = $CurrentPlanet;
    }
    public function show()
    {
        global $resource, $requeriments, $engine;

        foreach ($engine->get('tech') as $Element => $ElementName)
        {
            $engine->assign('tt_name', $ElementName);

            if (!isset($resource[$Element]))
            {
                $engine->assign('Requirements', $engine->get('tt_requirements'));
                $page .= $engine->output('techtree/techtree_head', true);
            } else
            {
                if (isset($requeriments[$Element]))
                {
                    $engine->assign('required_list', "");
                    foreach ($requeriments[$Element] as $ResClass => $Level)
                    {
                        if (isset($this->CurrentUser[$resource[$ResClass]]) && $this->CurrentUser[$resource[$ResClass]] >= $Level)
                            $engine->append('required_list', "<font color=\"#00ff00\">");
                        elseif (isset($this->CurrentPlanet[$resource[$ResClass]]) && $this->CurrentPlanet[$resource[$ResClass]] >= $Level)
                            $engine->append('required_list', "<font color=\"#00ff00\">");
                        else
                            $engine->append('required_list', "<font color=\"#ff0000\">");
                        $tech = $engine->get('tech');
                        $engine->append('required_list', $tech[$ResClass] . " (" . $engine->get('tt_lvl') . $Level . ")");
                        $engine->append('required_list', "</font><br>");
                    }
                    ;
                } else
                {
                    $engine->assign('required_list', '');
                    $engine->assign('tt_detail', "");
                }
                $engine->assign('tt_info', $Element);
                $page .= $engine->output('techtree/techtree_row', true);
            }
        }
        $engone->clearReadyCompiled();
        $engine->assign('techtree_list', $page);

        return display($engine->output('techtree/techtree_body'));
    }
}

?>