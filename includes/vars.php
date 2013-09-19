<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

if ( defined('INSIDE'))
{

	$resource = array(
	  1 => "metal_mine",
	  2 => "crystal_mine",
	  3 => "deuterium_sintetizer",
	  4 => "solar_plant",
	 12 => "fusion_plant",
	 14 => "robot_factory",
	 15 => "nano_factory",
	 21 => "hangar",
	 22 => "metal_store",
	 23 => "crystal_store",
	 24 => "deuterium_store",
	 31 => "laboratory",
	 33 => "terraformer",
	 34 => "ally_deposit",
	 41 => "mondbasis",
	 42 => "phalanx",
	 43 => "sprungtor",
	 44 => "silo",

	106 => "spy_tech",
	108 => "computer_tech",
	109 => "military_tech",
	110 => "defence_tech",
	111 => "shield_tech",
	113 => "energy_tech",
	114 => "hyperspace_tech",
	115 => "combustion_tech",
	117 => "impulse_motor_tech",
	118 => "hyperspace_motor_tech",
	120 => "laser_tech",
	121 => "ionic_tech",
	122 => "buster_tech",
	123 => "intergalactic_tech",
	124 => "expedition_tech",
	199 => "graviton_tech",

	202 => "small_ship_cargo",
	203 => "big_ship_cargo",
	204 => "light_hunter",
	205 => "heavy_hunter",
	206 => "crusher",
	207 => "battle_ship",
	208 => "colonizer",
	209 => "recycler",
	210 => "spy_sonde",
	211 => "bomber_ship",
	212 => "solar_satelit",
	213 => "destructor",
	214 => "dearth_star",
	215 => "battleship",

	401 => "misil_launcher",
	402 => "small_laser",
	403 => "big_laser",
	404 => "gauss_canyon",
	405 => "ionic_canyon",
	406 => "buster_canyon",
	407 => "small_protection_shield",
	408 => "big_protection_shield",
	502 => "interceptor_misil",
	503 => "interplanetary_misil",

	601 => "rpg_geologue",
	602 => "rpg_amiral",
	603 => "rpg_ingenieur",
	604 => "rpg_technocrate",
	);

	$requeriments = array(
		 12 => array(   3 =>   5, 113 =>   3),
		 15 => array(  14 =>  10, 108 =>  10),
		 21 => array(  14 =>   2),
		 33 => array(  15 =>   1, 113 =>  12),

		 42 => array(  41 =>   1),
		 43 => array(  41 =>   1, 114 =>   7),
		 44 => array(  21 =>   1),

		106 => array(  31 =>   3),
		108 => array(  31 =>   1),
		109 => array(  31 =>   4),
		110 => array( 113 =>   3,  31 =>   6),
		111 => array(  31 =>   2),
		113 => array(  31 =>   1),
		114 => array( 113 =>   5, 110 =>   5,  31 =>   7),
		115 => array( 113 =>   1,  31 =>   1),
		117 => array( 113 =>   1,  31 =>   2),
		118 => array( 114 =>   3,  31 =>   7),
		120 => array(  31 =>   1, 113 =>   2),
		121 => array(  31 =>   4, 120 =>   5, 113 =>   4),
		122 => array(  31 =>   5, 113 =>   8, 120 =>  10, 121 =>   5),
		123 => array(  31 =>  10, 108 =>   8, 114 =>   8),
		124 => array(  31 =>   3, 108 =>   4, 117 =>   3),
		199 => array(  31 =>  12),

		202 => array(  21 =>   2, 115 =>   2),
		203 => array(  21 =>   4, 115 =>   6),
		204 => array(  21 =>   1, 115 =>   1),
		205 => array(  21 =>   3, 111 =>   2, 117 =>   2),
		206 => array(  21 =>   5, 117 =>   4, 121 =>   2),
		207 => array(  21 =>   7, 118 =>   4),
		208 => array(  21 =>   4, 117 =>   3),
		209 => array(  21 =>   4, 115 =>   6, 110 =>   2),
		210 => array(  21 =>   3, 115 =>   3, 106 =>   2),
		211 => array( 117 =>   6,  21 =>   8, 122 =>   5),
		212 => array(  21 =>   1),
		213 => array(  21 =>   9, 118 =>   6, 114 =>   5),
		214 => array(  21 =>  12, 118 =>   7, 114 =>   6,  199 =>   1),
		215 => array( 114 =>   5, 120 =>  12, 118 =>   5,   21 =>   8),

		401 => array(  21 =>   1),
		402 => array( 113 =>   1,  21 =>   2, 120 =>   3),
		403 => array( 113 =>   3,  21 =>   4, 120 =>   6),
		404 => array(  21 =>   6, 113 =>   6, 109 =>   3, 110 =>   1),
		405 => array(  21 =>   4, 121 =>   4),
		406 => array(  21 =>   8, 122 =>   7),
		407 => array( 110 =>   2,  21 =>   1),
		408 => array( 110 =>   6,  21 =>   6),
		502 => array(  44 =>   2,  21 =>   1),
		503 => array(  44 =>   4,  21 =>   1, 117 =>   1),
	);

	$pricelist = array(
		  1 => array ( 'metal' =>      60, 'crystal' =>      15, 'deuterium' =>       0, 'energy' =>    0, 'factor' => 3/2),
		  2 => array ( 'metal' =>      48, 'crystal' =>      24, 'deuterium' =>       0, 'energy' =>    0, 'factor' => 1.6),
		  3 => array ( 'metal' =>     225, 'crystal' =>      75, 'deuterium' =>       0, 'energy' =>    0, 'factor' => 3/2),
		  4 => array ( 'metal' =>      75, 'crystal' =>      30, 'deuterium' =>       0, 'energy' =>    0, 'factor' => 3/2),
		 12 => array ( 'metal' =>     900, 'crystal' =>     360, 'deuterium' =>     180, 'energy' =>    0, 'factor' => 1.8),
		 14 => array ( 'metal' =>     400, 'crystal' =>     120, 'deuterium' =>     200, 'energy' =>    0, 'factor' =>   2),
		 15 => array ( 'metal' => 1000000, 'crystal' =>  500000, 'deuterium' =>  100000, 'energy' =>    0, 'factor' =>   2),
		 21 => array ( 'metal' =>     400, 'crystal' =>     200, 'deuterium' =>     100, 'energy' =>    0, 'factor' =>   2),
		 22 => array ( 'metal' =>    2000, 'crystal' =>       0, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		 23 => array ( 'metal' =>    2000, 'crystal' =>    1000, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		 24 => array ( 'metal' =>    2000, 'crystal' =>    2000, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		 31 => array ( 'metal' =>     200, 'crystal' =>     400, 'deuterium' =>     200, 'energy' =>    0, 'factor' =>   2),
		 33 => array ( 'metal' =>       0, 'crystal' =>   50000, 'deuterium' =>  100000, 'energy_max' => 1000, 'factor' =>   2),

		 34 => array ( 'metal' =>   20000, 'crystal' =>   40000, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		 41 => array ( 'metal' =>   20000, 'crystal' =>   40000, 'deuterium' =>   20000, 'energy' =>    0, 'factor' =>   2),
		 42 => array ( 'metal' =>   20000, 'crystal' =>   40000, 'deuterium' =>   20000, 'energy' =>    0, 'factor' =>   2),
		 43 => array ( 'metal' => 2000000, 'crystal' => 4000000, 'deuterium' => 2000000, 'energy' =>    0, 'factor' =>   2),
		 44 => array ( 'metal' =>   20000, 'crystal' =>   20000, 'deuterium' =>    1000, 'energy' =>    0, 'factor' =>   2),

		106 => array ( 'metal' =>     200, 'crystal' =>    1000, 'deuterium' =>     200, 'energy' =>    0, 'factor' =>   2),
		108 => array ( 'metal' =>       0, 'crystal' =>     400, 'deuterium' =>     600, 'energy' =>    0, 'factor' =>   2),
		109 => array ( 'metal' =>     800, 'crystal' =>     200, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		110 => array ( 'metal' =>     200, 'crystal' =>     600, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		111 => array ( 'metal' =>    1000, 'crystal' =>       0, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		113 => array ( 'metal' =>       0, 'crystal' =>     800, 'deuterium' =>     400, 'energy' =>    0, 'factor' =>   2),
		114 => array ( 'metal' =>       0, 'crystal' =>    4000, 'deuterium' =>    2000, 'energy' =>    0, 'factor' =>   2),
		115 => array ( 'metal' =>     400, 'crystal' =>       0, 'deuterium' =>     600, 'energy' =>    0, 'factor' =>   2),
		117 => array ( 'metal' =>    2000, 'crystal' =>    4000, 'deuterium' =>    600, 'energy' =>    0, 'factor' =>   2),
		118 => array ( 'metal' =>   10000, 'crystal' =>   20000, 'deuterium' =>    6000, 'energy' =>    0, 'factor' =>   2),
		120 => array ( 'metal' =>     200, 'crystal' =>     100, 'deuterium' =>       0, 'energy' =>    0, 'factor' =>   2),
		121 => array ( 'metal' =>    1000, 'crystal' =>     300, 'deuterium' =>     100, 'energy' =>    0, 'factor' =>   2),
		122 => array ( 'metal' =>    2000, 'crystal' =>    4000, 'deuterium' =>    1000, 'energy' =>    0, 'factor' =>   2),
		123 => array ( 'metal' =>  240000, 'crystal' =>  400000, 'deuterium' =>  160000, 'energy' =>    0, 'factor' =>   2),
		124 => array ( 'metal' =>    4000, 'crystal' =>    8000, 'deuterium' =>    4000, 'energy' =>    0, 'factor' =>   2),
		199 => array ( 'metal' =>       0, 'crystal' =>       0, 'deuterium' =>       0, 'energy_max' => 300000, 'factor' =>   3),

		202 => array ( 'metal' =>     2000, 'crystal' =>     2000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 10  , 'consumption2' => 10  , 'speed' =>      5000, 'speed2' =>     10000, 'capacity' =>     5000 ),
		203 => array ( 'metal' =>     6000, 'crystal' =>     6000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 25  , 'consumption2' => 25  , 'speed' =>      7500, 'speed2' =>      7500, 'capacity' =>    25000 ),
		204 => array ( 'metal' =>     3000, 'crystal' =>     1000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 10  , 'consumption2' => 10  , 'speed' =>     12500, 'speed2' =>     12500, 'capacity' =>       50 ),
		205 => array ( 'metal' =>     6000, 'crystal' =>     4000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 30  , 'consumption2' => 30  , 'speed' =>     10000, 'speed2' =>     15000, 'capacity' =>      100 ),
		206 => array ( 'metal' =>    20000, 'crystal' =>     7000, 'deuterium' =>    2000, 'energy' => 0, 'factor' => 1, 'consumption' => 150 , 'consumption2' => 150 , 'speed' =>     15000, 'speed2' =>     15000, 'capacity' =>      800 ),
		207 => array ( 'metal' =>    45000, 'crystal' =>    15000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 250 , 'consumption2' => 250 , 'speed' =>     10000, 'speed2' =>     10000, 'capacity' =>     1500 ),
		208 => array ( 'metal' =>    10000, 'crystal' =>    20000, 'deuterium' =>   10000, 'energy' => 0, 'factor' => 1, 'consumption' => 500 , 'consumption2' => 500 , 'speed' =>      2500, 'speed2' =>      2500, 'capacity' =>     7500 ),
		209 => array ( 'metal' =>    10000, 'crystal' =>     6000, 'deuterium' =>    2000, 'energy' => 0, 'factor' => 1, 'consumption' => 150 , 'consumption2' => 150 , 'speed' =>      2000, 'speed2' =>      2000, 'capacity' =>    20000 ),
		210 => array ( 'metal' =>        0, 'crystal' =>     1000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1, 'consumption' => 1   , 'consumption2' => 1   , 'speed' => 100000000, 'speed2' => 100000000, 'capacity' =>        5 ),
		211 => array ( 'metal' =>    50000, 'crystal' =>    25000, 'deuterium' =>   15000, 'energy' => 0, 'factor' => 1, 'consumption' => 500 , 'consumption2' => 500 , 'speed' =>      4000, 'speed2' =>      5000, 'capacity' =>      500 ),
		212 => array ( 'metal' =>        0, 'crystal' =>     2000, 'deuterium' =>     500, 'energy' => 0, 'factor' => 1, 'consumption' => 0   , 'consumption2' => 0   , 'speed' =>         0, 'speed2' =>         0, 'capacity' =>        0 ),
		213 => array ( 'metal' =>    60000, 'crystal' =>    50000, 'deuterium' =>   15000, 'energy' => 0, 'factor' => 1, 'consumption' => 500 , 'consumption2' => 500 , 'speed' =>      5000, 'speed2' =>      5000, 'capacity' =>     2000 ),
		214 => array ( 'metal' =>  5000000, 'crystal' =>  4000000, 'deuterium' => 1000000, 'energy' => 0, 'factor' => 1, 'consumption' => 1   , 'consumption2' => 1   , 'speed' =>       100, 'speed2' =>       100, 'capacity' =>  1000000 ),
		215 => array ( 'metal' =>    30000, 'crystal' =>    40000, 'deuterium' =>   15000, 'energy' => 0, 'factor' => 1, 'consumption' => 125 , 'consumption2' => 125 , 'speed' =>     10000, 'speed2' =>     10000, 'capacity' =>      750 ),

		401 => array ( 'metal' =>    2000, 'crystal' =>       0, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),
		402 => array ( 'metal' =>    1500, 'crystal' =>     500, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),
		403 => array ( 'metal' =>    6000, 'crystal' =>    2000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),
		404 => array ( 'metal' =>   20000, 'crystal' =>   15000, 'deuterium' =>    2000, 'energy' => 0, 'factor' => 1 ),
		405 => array ( 'metal' =>    2000, 'crystal' =>    6000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),
		406 => array ( 'metal' =>   50000, 'crystal' =>   50000, 'deuterium' =>   30000, 'energy' => 0, 'factor' => 1 ),
		407 => array ( 'metal' =>   10000, 'crystal' =>   10000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),
		408 => array ( 'metal' =>   50000, 'crystal' =>   50000, 'deuterium' =>       0, 'energy' => 0, 'factor' => 1 ),

		502 => array ( 'metal' =>    8000, 'crystal' =>    0, 'deuterium' =>       2000, 'energy' => 0, 'factor' => 1 ),
		503 => array ( 'metal' =>   12500, 'crystal' =>    2500, 'deuterium' =>   10000, 'energy' => 0, 'factor' => 1 ),

		601 => array ( 'darkmatter' => 	  5000, 'max' =>  1),
		602 => array ( 'darkmatter' =>    5000, 'max' =>  1),
		603 => array ( 'darkmatter' =>   12500, 'max' =>  1),
		604 => array ( 'darkmatter' =>   10000, 'max' =>  1),
	);

	$CombatCaps = array(
		202 => array ( 'shield' =>    10, 'attack' =>      5, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		203 => array ( 'shield' =>    25, 'attack' =>      5, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		204 => array ( 'shield' =>    10, 'attack' =>     50, 'sd' => array (202 =>   2, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		205 => array ( 'shield' =>    25, 'attack' =>    150, 'sd' => array (202 =>   3, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		206 => array ( 'shield' =>    50, 'attack' =>    400, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   6, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>  10, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		207 => array ( 'shield' =>   200, 'attack' =>   1000, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   8, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		208 => array ( 'shield' =>   100, 'attack' =>     50, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		209 => array ( 'shield' =>    10, 'attack' =>      0, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		210 => array ( 'shield' =>     0, 'attack' =>      0, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    0, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		211 => array ( 'shield' =>   500, 'attack' =>   1000, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>  20, 402 =>  20, 403 =>  10, 404 =>   0, 405 =>  10, 406 =>   0, 407 =>   0, 408 =>   0)),
		212 => array ( 'shield' =>    10, 'attack' =>      0, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    1, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		213 => array ( 'shield' =>   500, 'attack' =>   2000, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   2, 401 =>   0, 402 =>  10, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),
		214 => array ( 'shield' => 50000, 'attack' => 200000, 'sd' => array (202 => 250, 203 => 250, 204 => 200, 205 => 100, 206 =>  33, 207 =>  30, 208 => 250, 209 => 250, 210 => 1250, 211 =>  25, 212 => 1250, 213 =>   5, 214 =>   0, 215 =>  15, 401 => 200, 402 => 200, 403 => 100, 404 =>  50, 405 => 100, 406 =>   0, 407 =>   0, 408 =>   0)),
		215 => array ( 'shield' =>   400, 'attack' =>    700, 'sd' => array (202 =>   3, 203 =>   3, 204 =>   0, 205 =>   4, 206 =>   4, 207 =>   7, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    5, 213 =>   0, 214 =>   0, 215 =>   0, 401 =>   0, 402 =>   0, 403 =>   0, 404 =>   0, 405 =>   0, 406 =>   0, 407 =>   0, 408 =>   0)),

		401 => array ( 'shield' =>      20, 'attack' =>      80, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		402 => array ( 'shield' =>      25, 'attack' =>     100, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		403 => array ( 'shield' =>     100, 'attack' =>     250, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		404 => array ( 'shield' =>     200, 'attack' =>    1100, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		405 => array ( 'shield' =>     500, 'attack' =>     150, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		406 => array ( 'shield' =>     300, 'attack' =>    3000, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		407 => array ( 'shield' =>    2000, 'attack' =>       1, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),
		408 => array ( 'shield' =>   10000, 'attack' =>       1, 'sd' => array (202 =>   0, 203 =>   0, 204 =>   0, 205 =>   0, 206 =>   0, 207 =>   0, 208 =>   0, 209 =>   0, 210 =>    5, 211 =>   0, 212 =>    0, 213 =>   0, 214 =>   0, 215 =>   0) ),

		502 => array ( 'shield' =>     1, 'attack' =>      1 ),
		503 => array ( 'shield' =>     1, 'attack' =>  12000 )
	);

	$ProdGrid = array(

		1   => array( 'metal' =>   40, 'crystal' =>   10, 'deuterium' =>    0, 'energy' => 0, 'factor' => 3/2,
			'formule' => array(
				'metal'     => 'return   (30 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);',
				'crystal'   => 'return   "0";',
				'deuterium' => 'return   "0";',
				'energy'    => 'return - (10 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);')
		),

		2   => array( 'metal' =>   30, 'crystal' =>   15, 'deuterium' =>    0, 'energy' => 0, 'factor' => 1.6,
			'formule' => array(
				'metal'     => 'return   "0";',
				'crystal'   => 'return   (20 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);',
				'deuterium' => 'return   "0";',
				'energy'    => 'return - (10 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);')
		),

		3   => array( 'metal' =>  150, 'crystal' =>   50, 'deuterium' =>    0, 'energy' => 0, 'factor' => 3/2,
			'formule' => array(
				'metal'     => 'return   "0";',
				'crystal'   => 'return   "0";',
				'deuterium' => 'return  ((10 * $BuildLevel * pow((1.1), $BuildLevel)) * (-0.002 * $BuildTemp + 1.28))  * (0.1 * $BuildLevelFactor);',
				'energy'    => 'return - (20 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);')
                ),

		4   => array( 'metal' =>   50, 'crystal' =>   20, 'deuterium' =>    0, 'energy' => 0, 'factor' => 3/2,
			'formule' => array(
				'metal'     => 'return   "0";',
				'crystal'   => 'return   "0";',
				'deuterium' => 'return   "0";',
				'energy'    => 'return   (20 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor);')
		),

		12  => array( 'metal' =>  500, 'crystal' =>  200, 'deuterium' =>  100, 'energy' => 0, 'factor' => 1.8,
            'formule' => array(
                'metal'     => 'return   "0";',
                'crystal'   => 'return   "0";',
                'deuterium' => 'return - (10 * $BuildLevel * pow(1.1,$BuildLevel) * (0.1 * $BuildLevelFactor));',
                'energy'    => 'return   (30 * $BuildLevel * pow((1.05 + $BuildEnergy * 0.01), $BuildLevel)) * (0.1 * $BuildLevelFactor);')
        ),

		212 => array( 'metal' =>    0, 'crystal' => 2000, 'deuterium' =>  500, 'energy' => 0, 'factor' => 0.5,
			'formule' => array(
				'metal'     => 'return   "0";',
				'crystal'   => 'return   "0";',
				'deuterium' => 'return   "0";',
				'energy'    => 'return  (($BuildTemp / 4) + 20) * $BuildLevel * (0.1 * $BuildLevelFactor);')
		)
	);

	$reslist['build']    = array (   1,   2,   3,   4,  12,  14,  15,  21,  22,  23,  24,  31,  33,  34,  41,  42,  43, 44);
	$reslist['tech']     = array ( 106, 108, 109, 110, 111, 113, 114, 115, 117, 118, 120, 121, 122, 123, 124, 199);
	$reslist['fleet']    = array ( 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215 );
	$reslist['defense']  = array ( 401, 402, 403, 404, 405, 406, 407, 408, 502, 503 );
	$reslist['officier'] = array ( 601, 602, 603, 604);
	$reslist['prod']     = array (   1,   2,   3,   4,  12, 212 );
}
?>