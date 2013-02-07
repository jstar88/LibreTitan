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
class ShowChangelogPage
{
    public function show()
    {
        global $engine;

        $engine->assignLangFile('CHANGELOG');
        $story = $engine->get('changelog');
        foreach ($story as $a => $b)
        {
            $engine->assign('version_number', $a);
            $engine->assign('description', nl2br($b));
            $engine->append('body', $engine->output('changelog_table', true));
        }

        return display($engine->output('changelog_body'));
    }
}

?>