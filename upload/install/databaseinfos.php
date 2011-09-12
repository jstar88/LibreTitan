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

$QryTableAks         = "CREATE TABLE `{{table}}` ( ";
$QryTableAks        .= "`id` bigint(20) unsigned NOT NULL auto_increment, ";
$QryTableAks        .= "`name` varchar(50) collate latin1_general_ci default NULL, ";
$QryTableAks        .= "`teilnehmer` text collate latin1_general_ci, ";
$QryTableAks        .= "`flotten` text collate latin1_general_ci, ";
$QryTableAks        .= "`ankunft` int(32) default NULL, ";
//start mod
$QryTableAks        .= "`universe` int(2) default NULL, ";
//end mod
$QryTableAks        .= "`galaxy` int(2) default NULL, ";
$QryTableAks        .= "`system` int(4) default NULL, ";
$QryTableAks        .= "`planet` int(2) default NULL, ";
$QryTableAks        .= "`planet_type` tinyint(1) default NULL, ";
$QryTableAks        .= "`eingeladen` text character set latin1 default NULL, ";
$QryTableAks        .= "PRIMARY KEY  (`id`) ";
$QryTableAks        .= ") ENGINE=MyISAM;";

$QryTableAlliance    = "CREATE TABLE `{{table}}` ( ";
$QryTableAlliance   .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTableAlliance   .= "`ally_name` varchar(32) character set latin1 default '', ";
$QryTableAlliance   .= "`ally_tag` varchar(8) character set latin1 default '', ";
$QryTableAlliance   .= "`ally_owner` int(11) NOT NULL default '0', ";
$QryTableAlliance   .= "`ally_register_time` int(11) NOT NULL default '0', ";
$QryTableAlliance   .= "`ally_description` text character set latin1, ";
$QryTableAlliance   .= "`ally_web` varchar(255) character set latin1 default '', ";
$QryTableAlliance   .= "`ally_text` text character set latin1, ";
$QryTableAlliance   .= "`ally_image` varchar(255) character set latin1 default '', ";
$QryTableAlliance   .= "`ally_request` text character set latin1, ";
$QryTableAlliance   .= "`ally_request_waiting` text character set latin1, ";
$QryTableAlliance   .= "`ally_request_notallow` tinyint(4) NOT NULL default '0', ";
$QryTableAlliance   .= "`ally_owner_range` varchar(32) character set latin1 default '', ";
$QryTableAlliance   .= "`ally_ranks` text character set latin1, ";
$QryTableAlliance   .= "`ally_members` int(11) NOT NULL default '0', ";
$QryTableAlliance   .= "PRIMARY KEY  (`id`) ";
$QryTableAlliance   .= ") ENGINE=MyISAM;";

$QryTableBanned      = "CREATE TABLE `{{table}}` ( ";
$QryTableBanned     .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTableBanned     .= "`who` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableBanned     .= "`theme` text character set latin1 NOT NULL, ";
$QryTableBanned     .= "`who2` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableBanned     .= "`time` int(11) NOT NULL default '0', ";
$QryTableBanned     .= "`longer` int(11) NOT NULL default '0', ";
$QryTableBanned     .= "`author` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableBanned     .= "`email` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableBanned     .= "KEY `ID` (`id`) ";
$QryTableBanned     .= ") ENGINE=MyISAM;";

$QryTableBuddy       = "CREATE TABLE `{{table}}` ( ";
$QryTableBuddy      .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTableBuddy      .= "`sender` int(11) NOT NULL default '0', ";
$QryTableBuddy      .= "`owner` int(11) NOT NULL default '0', ";
$QryTableBuddy      .= "`active` tinyint(3) NOT NULL default '0', ";
$QryTableBuddy      .= "`text` text character set latin1, ";
$QryTableBuddy      .= "PRIMARY KEY  (`id`) ";
$QryTableBuddy      .= ") ENGINE=MyISAM;";

$QryTableConfig      = "CREATE TABLE `{{table}}` ( ";
$QryTableConfig     .= "`config_name` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableConfig     .= "`config_value` text character set latin1 NOT NULL ";
$QryTableConfig     .= ") ENGINE=MyISAM;";

$QryInsertConfig     = "INSERT INTO `{{table}}` ";
$QryInsertConfig    .= "(`config_name`           , `config_value`) VALUES ";
$QryInsertConfig    .= "('VERSION'          	 , '2.9.9'), ";
$QryInsertConfig    .= "('users_amount'          , '0'), ";
$QryInsertConfig    .= "('moderation'            , '1,0,0,1,1;1,1,0,1,1;1;'), ";
$QryInsertConfig    .= "('game_speed'            , '2500'), ";
$QryInsertConfig    .= "('fleet_speed'           , '2500'), ";
$QryInsertConfig    .= "('resource_multiplier'   , '1'), ";
$QryInsertConfig    .= "('Fleet_Cdr'             , '30'), ";
$QryInsertConfig    .= "('Defs_Cdr'              , '30'), ";
$QryInsertConfig    .= "('initial_fields'        , '163'), ";
$QryInsertConfig    .= "('COOKIE_NAME'           , 'XGProyect'), ";
$QryInsertConfig    .= "('game_name'             , 'XG Proyect'), ";
$QryInsertConfig    .= "('game_disable'          , '1'), ";
$QryInsertConfig    .= "('close_reason'          , '�En este momento el servidor se encuentra cerrado!'), ";
$QryInsertConfig    .= "('metal_basic_income'    , '20'), ";
$QryInsertConfig    .= "('crystal_basic_income'  , '10'), ";
$QryInsertConfig    .= "('deuterium_basic_income', '0'), ";
$QryInsertConfig    .= "('energy_basic_income'   , '0'), ";
$QryInsertConfig    .= "('BuildLabWhileRun'      , '0'), ";
$QryInsertConfig    .= "('LastSettedGalaxyPos'   , '1'), ";
$QryInsertConfig    .= "('LastSettedSystemPos'   , '8'), ";
$QryInsertConfig    .= "('LastSettedPlanetPos'   , '3'), ";
$QryInsertConfig    .= "('noobprotection'        , '1'), ";
$QryInsertConfig    .= "('noobprotectiontime'    , '5000'), ";
$QryInsertConfig    .= "('noobprotectionmulti'   , '5'), ";
$QryInsertConfig    .= "('forum_url'             , 'http://www.xgproyect.net/' ), ";
$QryInsertConfig    .= "('adm_attack'         	 , '0' ), ";
$QryInsertConfig    .= "('debug'                 , '0'), ";
$QryInsertConfig    .= "('lang'                  , 'spanish'), ";
$QryInsertConfig    .= "('stat'                  , '1'), ";
$QryInsertConfig    .= "('stat_level'            , '2'), ";
$QryInsertConfig    .= "('stat_last_update'      , '".time()."'), ";
$QryInsertConfig    .= "('stat_settings'         , '1000'), ";
$QryInsertConfig    .= "('stat_amount'           , '25'), ";
$QryInsertConfig    .= "('stat_update_time'      , '15'), ";
$QryInsertConfig    .= "('stat_flying'           , '1') ";
$QryInsertConfig    .= ";";

$QryTableErrors      = "CREATE TABLE `{{table}}` ( ";
$QryTableErrors     .= "`error_id` bigint(11) NOT NULL auto_increment, ";
$QryTableErrors     .= "`error_sender` varchar(32) character set latin1 NOT NULL default '0', ";
$QryTableErrors     .= "`error_time` int(11) NOT NULL default '0', ";
$QryTableErrors     .= "`error_type` varchar(32) character set latin1 NOT NULL default 'unknown', ";
$QryTableErrors     .= "`error_text` text character set latin1, ";
$QryTableErrors     .= "PRIMARY KEY  (`error_id`) ";
$QryTableErrors     .= ") ENGINE=MyISAM;";

$QryTableFleets      = "CREATE TABLE `{{table}}` ( ";
$QryTableFleets     .= "`fleet_id` bigint(11) NOT NULL auto_increment, ";
$QryTableFleets     .= "`fleet_owner` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_mission` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_amount` bigint(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_array` text character set latin1, ";
$QryTableFleets     .= "`fleet_start_time` int(11) NOT NULL default '0', ";
//start mod
$QryTableFleets     .= "`fleet_start_universe` int(11) NOT NULL default '0', ";
//end mod
$QryTableFleets     .= "`fleet_start_galaxy` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_start_system` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_start_planet` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_start_type` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_end_time` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_end_stay` int(11) NOT NULL default '0', ";
//start mod
$QryTableFleets     .= "`fleet_end_universe` int(11) NOT NULL default '0', ";
//end mod
$QryTableFleets     .= "`fleet_end_galaxy` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_end_system` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_end_planet` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_end_type` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_target_obj` int(2) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_resource_metal` bigint(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_resource_crystal` bigint(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_resource_deuterium` bigint(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_resource_darkmatter` bigint(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_target_owner` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`fleet_group` varchar (15) NOT NULL DEFAULT '0', ";
$QryTableFleets     .= "`fleet_mess` int(11) NOT NULL default '0', ";
$QryTableFleets     .= "`start_time` int(11) default NULL, ";
$QryTableFleets     .= "PRIMARY KEY  (`fleet_id`) ";
$QryTableFleets     .= ") ENGINE=MyISAM;";

$QryTableGalaxy      = "CREATE TABLE `{{table}}` ( ";
//start mod
$QryTableGalaxy     .= "`universe` int(2) NOT NULL default '0', ";
//end mod
$QryTableGalaxy     .= "`galaxy` int(2) NOT NULL default '0', ";
$QryTableGalaxy     .= "`system` int(3) NOT NULL default '0', ";
$QryTableGalaxy     .= "`planet` int(2) NOT NULL default '0', ";
$QryTableGalaxy     .= "`id_planet` int(11) NOT NULL default '0', ";
$QryTableGalaxy     .= "`metal` bigint(11) NOT NULL default '0', ";
$QryTableGalaxy     .= "`crystal` bigint(11) NOT NULL default '0', ";
$QryTableGalaxy     .= "`id_luna` int(11) NOT NULL default '0', ";
$QryTableGalaxy     .= "`luna` int(2) NOT NULL default '0', ";
//start mod
$QryTableGalaxy     .= "KEY `universe` (`universe`), ";
//end mod
$QryTableGalaxy     .= "KEY `galaxy` (`galaxy`), ";
$QryTableGalaxy     .= "KEY `system` (`system`), ";
$QryTableGalaxy     .= "KEY `planet` (`planet`) ";
$QryTableGalaxy     .= ") ENGINE=MyISAM;";

$QryTableMessages    = "CREATE TABLE `{{table}}` ( ";
$QryTableMessages   .= "`message_id` bigint(11) NOT NULL auto_increment, ";
$QryTableMessages   .= "`message_owner` int(11) NOT NULL default '0', ";
$QryTableMessages   .= "`message_sender` int(11) NOT NULL default '0', ";
$QryTableMessages   .= "`message_time` int(11) NOT NULL default '0', ";
$QryTableMessages   .= "`message_type` int(11) NOT NULL default '0', ";
$QryTableMessages   .= "`message_from` varchar(48) character set latin1 default NULL, ";
$QryTableMessages   .= "`message_subject` text character set latin1, ";
$QryTableMessages   .= "`message_text` text character set latin1, ";
$QryTableMessages   .= "PRIMARY KEY  (`message_id`) ";
$QryTableMessages   .= ") ENGINE=MyISAM;";

$QryTableNotes       = "CREATE TABLE `{{table}}` ( ";
$QryTableNotes      .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTableNotes      .= "`owner` int(11) default NULL, ";
$QryTableNotes      .= "`time` int(11) default NULL, ";
$QryTableNotes      .= "`priority` tinyint(1) default NULL, ";
$QryTableNotes      .= "`title` varchar(32) character set latin1 default NULL, ";
$QryTableNotes      .= "`text` text character set latin1, ";
$QryTableNotes      .= "PRIMARY KEY  (`id`) ";
$QryTableNotes      .= ") ENGINE=MyISAM;";

$QryTablePlanets     = "CREATE TABLE `{{table}}` ( ";
$QryTablePlanets    .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTablePlanets    .= "`name` varchar(255) character set latin1 default 'Planeta Principal', ";
$QryTablePlanets    .= "`id_owner` int(11) default NULL, ";
$QryTablePlanets    .= "`id_level` int(11) default NULL, ";
//start mod
$QryTablePlanets     .= "`universe` int(11) NOT NULL default '0', ";
//end mod
$QryTablePlanets    .= "`galaxy` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`system` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`planet` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`last_update` int(11) default NULL, ";
$QryTablePlanets    .= "`planet_type` int(11) NOT NULL default '1', ";
$QryTablePlanets    .= "`destruyed` int(11) NOT NULL default '0', ";
//start mod
$QryTablePlanets    .= "`have_moon` int(11) NOT NULL default '0', ";
//end mod
$QryTablePlanets    .= "`b_building` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`b_building_id` text character set latin1 NOT NULL, ";
$QryTablePlanets    .= "`b_tech` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`b_tech_id` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`b_hangar` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`b_hangar_id` text character set latin1 NOT NULL, ";
$QryTablePlanets    .= "`b_hangar_plus` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`image` varchar(32) character set latin1 NOT NULL default 'normaltempplanet01', ";
$QryTablePlanets    .= "`diameter` int(11) NOT NULL default '12800', ";
$QryTablePlanets    .= "`points` bigint(20) default '0', ";
$QryTablePlanets    .= "`ranks` bigint(20) default '0', ";
$QryTablePlanets    .= "`field_current` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`field_max` int(11) NOT NULL default '163', ";
$QryTablePlanets    .= "`temp_min` int(3) NOT NULL default '-17', ";
$QryTablePlanets    .= "`temp_max` int(3) NOT NULL default '23', ";
$QryTablePlanets    .= "`metal` double(132,8) NOT NULL default '0.00000000', ";
$QryTablePlanets    .= "`metal_perhour` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`metal_max` bigint(20) default '100000', ";
$QryTablePlanets    .= "`crystal` double(132,8) NOT NULL default '0.00000000', ";
$QryTablePlanets    .= "`crystal_perhour` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`crystal_max` bigint(20) default '100000', ";
$QryTablePlanets    .= "`deuterium` double(132,8) NOT NULL default '0.00000000', ";
$QryTablePlanets    .= "`deuterium_perhour` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`deuterium_max` bigint(20) default '100000', ";
$QryTablePlanets    .= "`energy_used` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`energy_max` bigint(20) NOT NULL default '0', ";
$QryTablePlanets    .= "`metal_mine` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`crystal_mine` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`deuterium_sintetizer` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`solar_plant` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`fusion_plant` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`robot_factory` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`nano_factory` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`hangar` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`metal_store` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`crystal_store` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`deuterium_store` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`laboratory` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`terraformer` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`ally_deposit` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`silo` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`small_ship_cargo` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`big_ship_cargo` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`light_hunter` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`heavy_hunter` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`crusher` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`battle_ship` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`colonizer` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`recycler` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`spy_sonde` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`bomber_ship` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`solar_satelit` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`destructor` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`dearth_star` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`battleship` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`supernova` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`misil_launcher` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`small_laser` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`big_laser` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`gauss_canyon` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`ionic_canyon` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`buster_canyon` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`small_protection_shield` tinyint(1) NOT NULL default '0', ";
$QryTablePlanets    .= "`planet_protector` tinyint(1) NOT NULL default '0', ";
$QryTablePlanets    .= "`big_protection_shield` tinyint(1) NOT NULL default '0', ";
$QryTablePlanets    .= "`interceptor_misil` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`interplanetary_misil` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`metal_mine_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`crystal_mine_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`deuterium_sintetizer_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`solar_plant_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`fusion_plant_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`solar_satelit_porcent` int(11) NOT NULL default '10', ";
$QryTablePlanets    .= "`mondbasis` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`phalanx` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`sprungtor` bigint(11) NOT NULL default '0', ";
$QryTablePlanets    .= "`last_jump_time` int(11) NOT NULL default '0', ";
$QryTablePlanets    .= "PRIMARY KEY  (`id`) ";
$QryTablePlanets    .= ") ENGINE=MyISAM;";

$QryTablePlugins	.= "CREATE TABLE `{{table}}` (";
$QryTablePlugins	.= "`status` tinyint(11) NOT NULL DEFAULT '0',";
$QryTablePlugins	.= "`plugin` varchar(32) NOT NULL";
$QryTablePlugins	.= ") ENGINE=MyISAM;";

$QryTableRw          = "CREATE TABLE `{{table}}` ( ";
//$QryTableRw         .= "`id_owner1` int(11) NOT NULL default '0', ";
//$QryTableRw         .= "`id_owner2` int(11) NOT NULL default '0', ";
$QryTableRw      	.= "`owners` VARCHAR(255) character set latin1 NOT NULL, ";
$QryTableRw         .= "`rid` VARCHAR(72) character set latin1 NOT NULL, ";
$QryTableRw         .= "`raport` text character set latin1 NOT NULL, ";
$QryTableRw         .= "`a_zestrzelona` tinyint(3) unsigned NOT NULL default '0', ";
$QryTableRw         .= "`time` int(10) unsigned NOT NULL default '0', ";
$QryTableRw         .= "UNIQUE KEY `rid` (`rid`), ";
$QryTableRw         .= "KEY `time` (`time`) ";
$QryTableRw         .= ") ENGINE=MyISAM;";

$QryTableStatPoints  = "CREATE TABLE `{{table}}` ( ";
$QryTableStatPoints .= "`id_owner` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`id_ally` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`stat_type` int(2) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`stat_code` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`tech_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`tech_old_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`tech_points` bigint(20) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`tech_count` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`build_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`build_old_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`build_points` bigint(20) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`build_count` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`defs_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`defs_old_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`defs_points` bigint(20) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`defs_count` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`fleet_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`fleet_old_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`fleet_points` bigint(20) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`fleet_count` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`total_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`total_old_rank` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`total_points` bigint(20) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`total_count` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "`stat_date` int(11) NOT NULL DEFAULT '0', ";
$QryTableStatPoints .= "KEY `TECH` (`tech_points`), ";
$QryTableStatPoints .= "KEY `BUILDS` (`build_points`), ";
$QryTableStatPoints .= "KEY `DEFS` (`defs_points`), ";
$QryTableStatPoints .= "KEY `FLEET` (`fleet_points`), ";
$QryTableStatPoints .= "KEY `TOTAL` (`total_points`) ";
$QryTableStatPoints .= ") ENGINE=MyISAM;";

$QryTableUsers       = "CREATE TABLE `{{table}}` ( ";
$QryTableUsers      .= "`id` bigint(11) unsigned NOT NULL auto_increment PRIMARY KEY, ";
$QryTableUsers      .= "`username` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`password` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`email` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`email_2` varchar(64) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`authlevel` tinyint(4) NOT NULL default '0', ";
$QryTableUsers      .= "`id_planet` int(11) NOT NULL default '0', ";
//start mod
$QryTableUsers     .= "`universe` int(11) NOT NULL default '0', ";
//end mod
$QryTableUsers      .= "`galaxy` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`system` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`planet` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`current_planet` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`user_lastip` varchar(16) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`ip_at_reg` varchar(16) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`user_agent` text character set latin1 NOT NULL, ";
$QryTableUsers      .= "`current_page` text character set latin1 NOT NULL, ";
$QryTableUsers      .= "`register_time` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`onlinetime` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`dpath` varchar(255) character set latin1 NOT NULL default '', ";
$QryTableUsers      .= "`design` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`noipcheck` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`planet_sort` tinyint(1) NOT NULL default '0', ";
$QryTableUsers      .= "`planet_sort_order` tinyint(1) NOT NULL default '0', ";
$QryTableUsers      .= "`spio_anz` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`settings_tooltiptime` tinyint(4) NOT NULL default '5', ";
$QryTableUsers      .= "`settings_fleetactions` tinyint(4) NOT NULL default '0', ";
$QryTableUsers      .= "`settings_allylogo` tinyint(4) NOT NULL default '0', ";
$QryTableUsers      .= "`settings_esp` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`settings_wri` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`settings_bud` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`settings_mis` tinyint(4) NOT NULL default '1', ";
$QryTableUsers      .= "`settings_rep` tinyint(4) NOT NULL default '0', ";
$QryTableUsers      .= "`urlaubs_modus` tinyint(4) NOT NULL default '0', ";
$QryTableUsers      .= "`urlaubs_until` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`db_deaktjava` bigint(19) NOT NULL default '0', ";
$QryTableUsers      .= "`new_message` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`fleet_shortcut` text character set latin1, ";
$QryTableUsers      .= "`b_tech_planet` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`spy_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`computer_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`military_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`defence_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`shield_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`energy_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`hyperspace_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`combustion_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`impulse_motor_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`hyperspace_motor_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`laser_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`ionic_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`buster_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`intergalactic_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`expedition_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`graviton_tech` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`ally_id` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`ally_name` varchar(32) character set latin1 default '', ";
$QryTableUsers      .= "`ally_request` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`ally_request_text` text character set latin1, ";
$QryTableUsers      .= "`ally_register_time` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`ally_rank_id` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`current_luna` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_geologue` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_amiral` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_ingenieur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_technocrate` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_espion` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_constructeur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_scientifique` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_commandant` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_stockeur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`darkmatter` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_defenseur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_destructeur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_general` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_bunker` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_raideur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`rpg_empereur` int(11) NOT NULL default '0', ";
$QryTableUsers      .= "`bana` int(11) default NULL , ";
$QryTableUsers      .= "`banaday` int(11) NOT NULL default '0' ";
$QryTableUsers      .= ") ENGINE=MyISAM;";

$QryTableUniverseWar       = "CREATE TABLE `{{table}}` ( ";
$QryTableUniverseWar      .= "`id` bigint(11) NOT NULL auto_increment, ";
$QryTableUniverseWar      .= "`id_universe_in_war1` int(11) NOT NULL, ";
$QryTableUniverseWar      .= "`id_universe_in_war2` int(11) NOT NULL, ";
$QryTableUniverseWar      .= "`war_start` DATETIME NOT NULL, ";
$QryTableUniverseWar      .= "`war_end` DATETIME NOT NULL, ";
$QryTableUniverseWar      .= "`distance` int(11) NOT NULL, ";
$QryTableUniverseWar    .= "PRIMARY KEY  (`id`) ";
$QryTableUniverseWar      .= ") ENGINE=MyISAM;";

$QryTableUniverse       = "CREATE TABLE `{{table}}` ( ";
$QryTableUniverse      .= "`id` bigint(11) NOT NULL auto_increment, ";
//$QryTableUniverse      .= "`universe_title` int(11) NOT NULL, ";
$QryTableUniverse      .= "`universe_name` text character set latin1 NOT NULL, ";
$QryTableUniverse     .= "PRIMARY KEY  (`id`) ";
$QryTableUniverse      .= ") ENGINE=MyISAM;";

$QryInsertUniverse     ="INSERT `{{table}}` SET `universe_name` = '1'";

?> 