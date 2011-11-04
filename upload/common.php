<?php
if (isset ($_GET["xgp_root"]) or isset ($_POST["xgp_root"]))
	die();

//-------------what we need always
error_reporting(E_ERROR | E_PARSE);

$xgp_root= dirname(__FILE__).DIRECTORY_SEPARATOR;
$phpEx = "php";
if (filesize($xgp_root . 'config.php') == 0 && INSTALL != true) {
	exit (header("location:" . $xgp_root . "install/"));
}
//------------

//-------------what we need always
require ($xgp_root . 'includes/constants.' . $phpEx);
require ($xgp_root . 'includes/vendor/phputf8/php-utf8.'.$phpEx);
require ($xgp_root . 'includes/GeneralFunctions.' . $phpEx);
//---------------------------

//-------------the autoloader
require ($xgp_root . 'includes/vendor/autoloader/Autoloader.class.'.$phpEx);
Autoloader :: setRoot($xgp_root);
Autoloader :: setCacheFilePath(CACHE_DIR,'class_path_cache.txt');
Autoloader :: excludeFolderNamesMatchingRegex('/^CVS|\..*$/');
Autoloader :: setClassPaths(array (
	'includes/'
));
Autoloader::init();
//---------------------------

//------------the template engine
$engine = new Xtreme();
$engine->setBaseDirectory($xgp_root);
$engine->setCompileDirectory(CACHE_DIR);
$engine->setTemplateDirectories(TEMPLATE_DIR);
$engine->setLangDirectory(LANGUAGE_DIR);
$engine->switchCountry(DEFAULT_LANG);
$engine->assignLangFile('INGAME');
$engine->assignLangFile('SERVER');
//---------------------------

$game_config = array ();
$war_config = array ();
$user = array ();
$lang = array ();
$LegacyPlanet = array ();
$link = "";
$IsUserChecked = false;
$dpath = "../" . DEFAULT_SKINPATH;
 

includeLang('INGAME'); //only for now
//------------------the debug
$debug = new debug();
//---------------------------

if (INSTALL != true) {
	include ($xgp_root . 'includes/vars.' . $phpEx);
	include ($xgp_root . 'includes/functions/RoundUp.' . $phpEx);
	include ($xgp_root . 'includes/functions/CreateOneMoonRecord.' . $phpEx);
	include ($xgp_root . 'includes/functions/CreateOnePlanetRecord.' . $phpEx);
	include ($xgp_root . 'includes/functions/SendSimpleMessage.' . $phpEx);
	include ($xgp_root . 'includes/functions/calculateAttack.' . $phpEx);
	include ($xgp_root . 'includes/functions/formatCR.' . $phpEx);
	include ($xgp_root . 'includes/functions/GetBuildingTime.' . $phpEx);
	include ($xgp_root . 'includes/functions/HandleElementBuildingQueue.' . $phpEx);
	include ($xgp_root . 'includes/functions/PlanetResourceUpdate.' . $phpEx);
	include ($xgp_root . 'includes/functions/CheckPlanetUsedFields.' . $phpEx);
	include ($xgp_root . 'includes/functions/HandleTechnologieBuild.' . $phpEx);
	include ($xgp_root . 'includes/functions/UpdatePlanetBatimentQueueList.' . $phpEx);
	include ($xgp_root . 'includes/functions/CheckPlanetBuildingQueue.' . $phpEx);
	include ($xgp_root . 'includes/functions/GetBuildingPrice.' . $phpEx);
	include ($xgp_root . 'includes/functions/SetNextQueueElementOnTop.' . $phpEx);
	include ($xgp_root . 'includes/functions/IsElementBuyable.' . $phpEx);
	include ($xgp_root . 'includes/functions/SortUserPlanets.' . $phpEx);
	include ($xgp_root . 'includes/functions/SetSelectedPlanet.' . $phpEx);

	$query = doquery("SELECT * FROM {{table}}", 'config');

	//----------------game config
	while ($row = mysql_fetch_assoc($query)) {
		$game_config[$row['config_name']] = $row['config_value'];
	}
	//---------------------------

	//-----------------war config
	$query = doquery("SELECT * FROM {{table}}", 'universewar');
	$i = 0;
	while ($row = mysql_fetch_assoc($query)) {
		$one_war['id'] = $row['id'];
		$one_war['id_universe_in_war1'] = $row['id_universe_in_war1'];
		$one_war['id_universe_in_war2'] = $row['id_universe_in_war2'];
		$one_war['war_start'] = getTimeFromDatabaseDate($row['war_start']);
		$one_war['war_end'] = getTimeFromDatabaseDate($row['war_end']);
		$one_war['distance'] = $row['distance'];
		$one_war['on_time_to_war'] = 0;

		$war_config[$i] = $one_war;
		$i++;
	}
	//---------------------------

	define('VERSION', ($game_config['VERSION'] == '') ? "		" : "v" . $game_config['VERSION']);
	
	//----------------user check
	if ($InLogin != true) {
		$Result = new CheckSession();
		$Result = $Result->CheckUser($IsUserChecked);
		$IsUserChecked = $Result['state'];
		$user = $Result['record'];

		if ($game_config['game_disable'] == 0 && $user['authlevel'] == 0) {
			message($game_config['close_reason'], '', '', false, false);
		}
	}
	//---------------------------
	
	//----------update stats task
	if (isset ($user) && (time() >= ($game_config['stat_last_update'] + (60 * $game_config['stat_update_time'])))) {
		include ($xgp_root . 'adm/statfunctions.' . $phpEx);
		$result = MakeStats();
		update_config('stat_last_update', $result['stats_time']);
	}
	//---------------------------
	
	if (isset ($user)) {
		
		if (defined('IN_ADMIN')) {
			includeLang('ADMIN');
			include ('../adm/AdminFunctions/Autorization.' . $phpEx);
		} 
		else {
		   //----------update fleet task
		    new FlyingFleetHandler();
	    //---------------------------
			$dpath = (!$user["dpath"]) ? SKIN_DIR.DEFAULT_SKIN.'/' : $user["dpath"];
		}

		SetSelectedPlanet($user);

		$planetrow = doquery("SELECT * FROM `{{table}}` WHERE `id` = '" . $user['current_planet'] . "';", "planets", true);
		$planetlist = SortUserPlanets($user);
		//We include the plugin system 0.3
		include ($xgp_root . 'includes/plugins.' . $phpEx);
		
		//----------update resources task
		PlanetResourceUpdate($user, $planetrow, time(), true);
		//---------------------------
		
		//----------update buildings task
		UpdatePlanetBatimentQueueList($planetrow, $user);
		$IsWorking = HandleTechnologieBuild($planetrow, $user);
		$ProductionTime = (CURRENT_TIME - $planetrow['last_update']);
		HandleElementBuildingQueue($user, $planetrow, $ProductionTime);
		//---------------------------
		
	}
	SecurePage :: run();
}
?>
