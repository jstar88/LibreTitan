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

class CheckSession
{
	private function CheckCookies ($IsUserChecked)
	{
		global $game_config, $xgp_root, $phpEx, $lang;

		$UserRow = array();

		include($xgp_root . 'config.' . $phpEx);

		if (isset($_COOKIE[$game_config['COOKIE_NAME']]))
		{
			$TheCookie  = explode("/%/", $_COOKIE[$game_config['COOKIE_NAME']]);
			$UserResult = doquery("SELECT * FROM {{table}} WHERE `username` = '". mysql_real_escape_string($TheCookie[1]). "';", 'users');

			if (mysql_num_rows($UserResult) != 1)
			{
				message($lang['ccs_multiple_users'], $xgp_root, 5, false, false);
			}

			$UserRow    = mysql_fetch_array($UserResult);

			if ($UserRow["id"] != $TheCookie[0])
			{
				message($lang['ccs_other_user'], $xgp_root, 5,  false, false);
			}

			if (md5($UserRow["password"] . "--" . $dbsettings["secretword"]) !== $TheCookie[2])
			{
				message($lang['css_different_password'], $xgp_root, 5,  false, false);
			}

			$NextCookie = implode("/%/", $TheCookie);

			if ($TheCookie[3] == 1)
			{
				$ExpireTime = time() + 31536000;
			}
			else
			{
				$ExpireTime = 0;
			}

			if ($IsUserChecked == false)
			{
				setcookie ($game_config['COOKIE_NAME'], $NextCookie, $ExpireTime, "/", "", 0);
				$QryUpdateUser  = "UPDATE {{table}} SET ";
				$QryUpdateUser .= "`onlinetime` = '". time() ."', ";
				$QryUpdateUser .= "`current_page` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['REQUEST_URI'])) ."', ";
				$QryUpdateUser .= "`user_lastip` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['REMOTE_ADDR'])) ."', ";
				$QryUpdateUser .= "`user_agent` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['HTTP_USER_AGENT'])) ."' ";
				$QryUpdateUser .= "WHERE ";
				$QryUpdateUser .= "`id` = '". intval($TheCookie[0]) ."' LIMIT 1;";
				doquery( $QryUpdateUser, 'users');
				$IsUserChecked = true;
			}
			else
			{
				$QryUpdateUser  = "UPDATE {{table}} SET ";
				$QryUpdateUser .= "`onlinetime` = '". time() ."', ";
				$QryUpdateUser .= "`current_page` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['REQUEST_URI'])) ."', ";
				$QryUpdateUser .= "`user_lastip` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['REMOTE_ADDR'])) ."', ";
				$QryUpdateUser .= "`user_agent` = '". mysql_real_escape_string(htmlspecialchars($_SERVER['HTTP_USER_AGENT'])) ."' ";
				$QryUpdateUser .= "WHERE ";
				$QryUpdateUser .= "`id` = '". intval($TheCookie[0]) ."' LIMIT 1;";
				doquery( $QryUpdateUser, 'users');
				$IsUserChecked = true;
			}
		}

		unset($dbsettings);

		$Return['state']  = $IsUserChecked;
		$Return['record'] = $UserRow;

		return $Return;
	}

	public function CheckUser($IsUserChecked)
	{
		global $user, $xgp_root, $lang;

		$Result        = $this->CheckCookies($IsUserChecked);
		$IsUserChecked = $Result['state'];

		if ($Result['record'] != false)
		{
			$user = $Result['record'];

			if ($user['bana'] == 1)
			{
				die("<div align=\"center\"><h1>".$lang['css_account_banned_message']."</h1><br /> <strong>".$lang['css_account_banned_expire'].date("d-m-y H:i", $user['banaday'])."</strong></div>");
			}

			$RetValue['record'] = $user;
			$RetValue['state']  = $IsUserChecked;
		}
		else
		{
			$RetValue['record'] = array();
			$RetValue['state']  = false;
			header("location:".$xgp_root);
		}

		return $RetValue;
	}
}
?>