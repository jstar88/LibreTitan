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

if(!defined('INSIDE')){ die(header("location:../../"));}

class debug
{
	var $log,$numqueries;

	function __construct()
	{
		$this->vars = $this->log = '';
		$this->numqueries = 0;
	}

	function add($mes)
	{
		$this->log .= $mes;
		$this->numqueries++;
	}

	function echo_log()
	{
		global $xgp_root;
		return  "<br><table><tr><td class=k colspan=4><a href=".$xgp_root."adm/settings.php>Debug Log</a>:</td></tr>".$this->log."</table>";
		die();
	}

	function error($message,$title)
	{
		global $link, $game_config, $lang;

		if($game_config['debug']==1)
		{
			echo "<h2>$title</h2><br><font color=red>$message</font><br><hr>";
			echo  "<table>".$this->log."</table>";
		}

		global $user,$xgp_root,$phpEx;
		include($xgp_root . 'config.'.$phpEx);

		if(!$link)
			die($lang['cdg_mysql_not_available']);

		$query = "INSERT INTO {{table}} SET
		`error_sender` = '".intval($user['id'])."' ,
		`error_time` = '".time()."' ,
		`error_type` = '".mysql_escape_string($title)."' ,
		`error_text` = '".mysql_escape_string($message)."';";

		$sqlquery = mysql_query(str_replace("{{table}}", $dbsettings["prefix"].'errors',$query)) or die($lang['cdg_fatal_error']);

		$query = "explain select * from {{table}}";

		$q = mysql_fetch_array(mysql_query(str_replace("{{table}}", $dbsettings["prefix"].'errors', $query))) or die($lang['cdg_fatal_error'].': ');

		if (!function_exists('message'))
			echo $lang['cdg_error_message']." <b>".$q['rows']."</b>";
		else
			message($lang['cdg_error_message']." <b>".$q['rows']."</b>", '', '', false, false);

		die();
	}
}
?>