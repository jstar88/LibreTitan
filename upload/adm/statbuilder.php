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

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);

$xgp_root = './../';
include($xgp_root . 'extension.inc.php');
include($xgp_root . 'common.' . $phpEx);
include($xgp_root . 'adm/statfunctions.' . $phpEx);

if ($user['authlevel'] < 2) die(message ($lang['not_enough_permissions']));

	$result			= MakeStats();
	$memory_p		= str_replace(array("%p", "%m"), $result['memory_peak'], $lang['sb_top_memory']);
	$memory_e		= str_replace(array("%e", "%m"), $result['end_memory'], $lang['sb_final_memory']);
	$memory_i		= str_replace(array("%i", "%m"), $result['initial_memory'], $lang['sb_start_memory']);
	$stats_end_time	= str_replace("%t", $result['totaltime'], $lang['sb_stats_update']);
	$stats_block	= str_replace("%n", $result['amount_per_block'], $lang['sb_users_per_block']);

	update_config( 'stat_last_update', $result['stats_time']);

	$using_flying 	= (($game_config['stat_flying']==1) ? $lang['sb_using_fleet_array'] : $lang['sb_using_fleet_query']);

	message($lang['sb_stats_updated'].$stats_end_time.$memory_i.$memory_e.$memory_p.$stats_block.$using_flying);


?>