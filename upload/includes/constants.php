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
	die(header("location:../"));

define('DEFAULT_LANG', 'english');
// time after that position will be ready for colonization
define('PLANET_DELETE_TIME', 24*60*60);
// time after that moon will be deleted in galaxy 
define('MOON_DELETE_TIME', 24*60*60);

//min time to see minute-inactivity in galaxy  (*)
define('INACTIVITY_MIN',15*60);
//max time to see minute-inactivity in galaxy  (tot minutes)
define('INACTIVITY_MAX',59*60);
//time to see short-inactivity in galaxy  (i)
define('INACTIVITY_SHORT',60*60*24*7);
//time to see long-inactivity in galaxy  (i)
define('INACTIVITY_LONG',60*60*24*28);

//the current time in seconds from 1970
define('CURRENT_TIME',time());                           
//CACHE DEFAULT SETTINGS
define('CACHE_DIR', 'cache');
//LANG DEFAULT SETTINGS
define('LANGUAGE_DIR', 'language');
//TEMPLATES DEFAULT SETTINGS
define('DEFAULT_SKIN','stargate');
define('SKIN_DIR', 'styles/skins/');
define('TEMPLATE_DIR', 'styles/templates/');

// ADMINISTRATOR EMAIL AND GAME URL - THIS DATA IS REQUESTED BY REG.PHP
define('ADMINEMAIL', "info@xgproyect.com");
define('GAMEURL', "http://" . $_SERVER['HTTP_HOST'] . "/");

// UNIVERSE DATA, GALAXY, SYSTEMS AND PLANETS || DEFAULT 9-499-15 RESPECTIVELY
//start mod
define('MAX_UNIVERSE_IN_WORLD', 9);
//end mod
define('MAX_GALAXY_IN_WORLD', 9);
define('MAX_SYSTEM_IN_GALAXY', 499);
define('MAX_PLANET_IN_SYSTEM', 15);

// NUMBER OF COLUMNS FOR SPY REPORTS
define('SPY_REPORT_ROW', 3);

// FIELDS FOR EACH LEVEL OF THE LUNAR BASE
define('FIELDS_BY_MOONBASIS_LEVEL', 3);

// FIELDS FOR EACH LEVEL OF THE TERRAFORMER
define('FIELDS_BY_TERRAFORMER', 5);

// NUMBER OF PLANETS THAT MAY HAVE A PLAYER
define('MAX_PLAYER_PLANETS', 9);

// NUMBER OF BUILDINGS THAT CAN GO IN THE CONSTRUCTION QUEUE
define('MAX_BUILDING_QUEUE_SIZE', 5);

// NUMBER OF SHIPS THAT CAN BUILD FOR ONCE
define('MAX_FLEET_OR_DEFS_PER_ROW', 1000000);

// PERCENTAGE OF RESOURCES THAT CAN BE OVER STORED
// 1.0 TO 100% - 1.1% FOR 110 AND SO ON
define('MAX_OVERFLOW', 1);

// INITIAL RESOURCE OF NEW PLANETS
define('BASE_STORAGE_SIZE', 100000);
define('BUILD_METAL', 500);
define('BUILD_CRISTAL', 500);
define('BUILD_DEUTERIUM', 0);

// OFFICIERS DEFAULT VALUES
define('COMMANDANT', 3);
define('AMIRAL', 0.05);
define('ESPION', 5);
define('CONSTRUCTEUR', 0.1);
define('SCIENTIFIQUE', 0.1);
define('GENERAL', 0.10);
define('DEFENSEUR', 0.25);
define('TECHNOCRATE', 0.05);
define('STOCKEUR', 0.5);
define('GEOLOGUE', 0.05);
define('INGENIEUR', 0.05);

// TRADER DARK MATTER DEFAULT VALUE
define('TR_DARK_MATTER', 2500);
?>