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

define('INSIDE'  , true);
define('INSTALL' , false);

$xgp_root = dirname(__FILE__).DIRECTORY_SEPARATOR;

//the autoloader: you can forget to include external classes :)
//---- 
require($xgp_root . 'includes/vendor/autoloader/Autoloader.class.php');
Autoloader::setCacheFilePath('tmp/class_path_cache.txt',$xgp_root);
Autoloader::excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader::setClassPaths(array(
     'includes/'
));
spl_autoload_register(array('Autoloader', 'loadClass'));
//----

include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);
$engine->assignLang('SERVER');

switch($_GET[page])
{
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'changelog':
		$page=new ShowChangelogPage();
		$page->show();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'overview':
		include_once($xgp_root . 'includes/pages/ShowOverviewPage.' . $phpEx);
		ShowOverviewPage($user, $planetrow);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'galaxy':
		$ShowGalaxyPage = new ShowGalaxyPage($user, $planetrow);
      $ShowGalaxyPage->updatePosition();
      $ShowGalaxyPage->show();
	break;
	case'phalanx':
		include_once($xgp_root . 'includes/pages/ShowPhalanxPage.' . $phpEx);
		ShowPhalanxPage($user, $planetrow);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'imperium':
		$page=new ShowImperiumPage($user);
		$page->show();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'fleet':
		include_once($xgp_root . 'includes/pages/ShowFleetPage.' . $phpEx);
		ShowFleetPage($user, $planetrow);
	break;
	case'fleet1':
		include_once($xgp_root . 'includes/pages/ShowFleet1Page.' . $phpEx);
		ShowFleet1Page($user, $planetrow);
	break;
	case'fleet2':
		include_once($xgp_root . 'includes/pages/ShowFleet2Page.' . $phpEx);
		ShowFleet2Page($user, $planetrow);
	break;
	case'fleet3':
		include_once($xgp_root . 'includes/pages/ShowFleet3Page.' . $phpEx);
		ShowFleet3Page($user, $planetrow);
	break;
	case'fleetACS':
		include_once($xgp_root . 'includes/pages/ShowFleetACSPage.' . $phpEx);
		ShowFleetACSPage($user, $planetrow);
	break;
	case'shortcuts':
		include_once($xgp_root . 'includes/pages/ShowFleetShortcuts.' . $phpEx);
		ShowFleetShortcuts($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'buildings':
		switch ($_GET['mode'])
		{
			case 'research':
				new ShowResearchPage($planetrow, $user, $IsWorking['OnWork'], $IsWorking['WorkOn']);
			break;
			case 'fleet':
				$FleetBuildingPage = new ShowShipyardPage();
				$FleetBuildingPage->FleetBuildingPage ($planetrow, $user);
			break;
			case 'defense':
				$DefensesBuildingPage = new ShowShipyardPage();
				$DefensesBuildingPage->DefensesBuildingPage ($planetrow, $user);
			break;
			default:
				new ShowBuildingsPage($planetrow, $user);
			break;
		}
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'resources':
		include_once($xgp_root . 'includes/pages/ShowResourcesPage.' . $phpEx);
		ShowResourcesPage($user, $planetrow);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'officier':
		new ShowOfficierPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'trader':
		$ShowTraderPage= new ShowTraderPage($user, $planetrow);
		$ShowTraderPage->exchange();
		$ShowTraderPage->show();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'techtree':
		include_once($xgp_root . 'includes/pages/ShowTechTreePage.' . $phpEx);
		ShowTechTreePage($user, $planetrow);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'infos':
		new ShowInfosPage($user, $planetrow, $_GET['gid']);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'messages':
		include_once($xgp_root . 'includes/pages/ShowMessagesPage.' . $phpEx);
		ShowMessagesPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'alliance':
		new ShowAlliancePage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'buddy':
		include_once($xgp_root . 'includes/pages/ShowBuddyPage.' . $phpEx);
		ShowBuddyPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'notes':
		include_once($xgp_root . 'includes/pages/ShowNotesPage.' . $phpEx);
		ShowNotesPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'statistics':
		include_once($xgp_root . 'includes/pages/ShowStatisticsPage.' . $phpEx);
		ShowStatisticsPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'search':
		include_once($xgp_root . 'includes/pages/ShowSearchPage.' . $phpEx);
		ShowSearchPage();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'options':
		new ShowOptionsPage($user);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'banned':
		$page=ShowBannedPage();
		$page->show();
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	case'logout':
		setcookie($game_config['COOKIE_NAME'], "", time()-100000, "/", "", 0);
		message($engine->get('see_you_soon'), "/", 1, false, false);
	break;
// ----------------------------------------------------------------------------------------------------------------------------------------------//
	default:
		die(message($engine->get('page_doesnt_exist')));
// ----------------------------------------------------------------------------------------------------------------------------------------------//
}
?>
