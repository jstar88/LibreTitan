<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

define('INSIDE'  , TRUE);
define('INSTALL' , FALSE);
define('IN_ADMIN', TRUE);
define('XGP_ROOT', './../');

include(XGP_ROOT . 'global.php');

if ($user['authlevel'] < 1) die(message ($lang['404_page']));

function check_updates()
{
	if ( function_exists ( 'file_get_contents' ) )
	{
		$last_v 	= file_get_contents ( 'http://xgproyect.xgproyect.net/current.php' );
		$system_v	= read_config ( 'version' );

		return version_compare ( $system_v , $last_v , '<' );
	}
}

$parse		= $lang;
$Message	= '';
$error		= 0;

if(file_exists(XGP_ROOT . 'install/') && defined('IN_ADMIN'))
{
	$Message	.= "<font color=\"red\">".$lang['ow_install_file_detected']."</font><br/><br/>";
	$error++;
}

if ($user['authlevel'] >= 3)
{
	if ( is_writable ( XGP_ROOT . 'config.php' ) )
	{
		$Message	.= "<font color=\"red\">".$lang['ow_config_file_writable']."</font><br/><br/>";
		$error++;
	}

	$Errors = doquery("SELECT COUNT(*) AS `errors` FROM {{table}} WHERE 1;", 'errors', TRUE);

	if($Errors['errors'] != 0)
	{
		$Message	.= "<font color=\"red\">".$lang['ow_database_errors']."</font><br/><br/>";
		$error++;
	}

	if(check_updates())
	{
		$Message	.= "<font color=\"red\">".$lang['ow_old_version']."</font><br/><br/>";
		$error++;
	}
}

if($error != 0)
{
	$parse['error_message']		=	$Message;
	$parse['color']				=	"red";}
else
{
	$parse['error_message']		= 	$lang['ow_none'];
	$parse['color']				=	"lime";
}


display( parsetemplate(gettemplate('adm/OverviewBody'), $parse), FALSE, '', TRUE, FALSE);
?>