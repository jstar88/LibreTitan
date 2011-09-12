<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2010 By Neko from xgproyect.net	         #
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

if ($user['authlevel'] < 1) die(message ($lang['404_page']));

$parse		=	$lang;
$Archive	=	"Log/".$_GET['file'].".".$phpEx;

switch ($_GET['options'])
{
	case 'delete':
		if ($user['authlevel']	!=	3) die();
		$FP	=	fopen($Archive, "w+");
		fclose($FP);

		message($lang['log_delete_succes'].$_GET['file'], "LogToolPage.php?options=links&file=".$_GET['file']."", 2);
	break;

	case 'edit':
		if ($user['authlevel']	!=	3) die();
		$Fopen		=	fopen($Archive, "r+");

		while(!feof($Fopen))
		{
    		$parse['display']	.= fgets($Fopen);
		}
		fclose($Fopen);


		if ($_POST['editnow'])
		{
			$Fopen2	=	fopen($Archive, "w+");
			fputs($Fopen2, $_POST['text']);
			fclose($Fopen2);
			message($lang['log_edit_succes'], "LogToolPage.php?options=edit&file=".$_GET['file']."", 2);
		}

		$FileSize				=	filesize($Archive);
		$FinalSize				=	$FileSize / 1000;
		$parse['setsize']		=	"&nbsp;&nbsp;(".$FinalSize." KB)";
		$parse['setarchive']	=	$_GET['file'];

		display (parsetemplate(gettemplate('adm/LogEditBody'), $parse), false, '', true, false);
	break;

	case 'links':
		$Archive	=	"Log/".$_GET['file'].".".$phpEx;
		if (!file_exists($Archive))
		{
			fopen($Archive, "w+");
			fclose(fopen($Archive, "w+"));
		}


		$Log	=	fopen($Archive, "r");


		if($user['authlevel']	==	3)
		{
			$Excuse_me		=
			"<a href=\"LogToolPage.php?options=delete&file=".$_GET['file']."\" onClick=\" return confirm('".$lang['log_alert']."');\">
			".$lang['log_delete_link']."</a>&nbsp;
			<a href=\"LogToolPage.php?options=edit&file=".$_GET['file']."\">".$lang['log_edit_link']."</a>";
		}
		else
		{
			$Excuse_me		=	$lang['log_log_title_22'];
		}
		$EditAndDelete	=
			"<tr><td class=\"c\" colspan=2>".$Excuse_me."</td></tr>";

		$parse['display']	=	$EditAndDelete;
		if (filesize($Archive) == 0)
		{
			$parse['display']	.= "<tr><th align=\"left\" colspan=2>".$lang['log_filesize_0']."</th></tr>";
		}
		else
		{
			$parse['display']	.=	"<tr><th align=\"left\" colspan=2><font color=#E6E6E6>";
			while(!feof($Log))
			{
    			$parse['display']	.= fgets($Log)."<br>";
			}
			$parse['display']	.=	"</font></th></tr>";
			$parse['display']	.=	$EditAndDelete;
		}

		fclose($Log);

		$FileSize				=	filesize($Archive);
		$FinalSize				=	$FileSize / 1000;
		$parse['setsize']		=	"&nbsp;&nbsp;(".$FinalSize." KB)";
		$parse['setarchive']	=	$_GET['file'];
		display (parsetemplate(gettemplate('adm/LogBody'), $parse), false, '', true, false);
	break;

	default:
		display (parsetemplate(gettemplate('adm/LogBody'), $parse), false, '', true, false);
}
?>