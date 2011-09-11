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

function ShowMessagesPage($CurrentUser)
{
	global $xgp_root, $phpEx, $game_config, $dpath, $lang;

	$OwnerID       = intval($_GET['id']);
	$MessCategory  = intval($_GET['messcat']);
	$MessPageMode  = addslashes(mysql_escape_string($_GET["mode"]));
	$DeleteWhat    = $_POST['deletemessages'];

	if (isset ($DeleteWhat))
		$MessPageMode = "delete";

	$UsrMess       = doquery("SELECT * FROM {{table}} WHERE `message_owner` = '".intval($CurrentUser['id'])."' ORDER BY `message_time` DESC;", 'messages');
	$UnRead        = doquery("SELECT * FROM {{table}} WHERE `id` = '". intval($CurrentUser['id']) ."';", 'users', true);

	$MessageType   = array ( 0, 1, 2, 3, 4, 5, 15, 99, 100 );
	$TitleColor    = array ( 0 => '#FFFF00', 1 => '#FF6699', 2 => '#FF3300', 3 => '#FF9900', 4 => '#773399', 5 => '#009933', 15 => '#030070', 99 => '#007070', 100 => '#ABABAB'  );

	for ($MessType = 0; $MessType < 101; $MessType++)
	{
		if (in_array($MessType, $MessageType))
		{
			$WaitingMess[$MessType] = $UnRead[$messfields[$MessType]];
			$TotalMess[$MessType]   = 0;
		}
	}

	while ($CurMess = mysql_fetch_array($UsrMess))
	{
		$MessType              = $CurMess['message_type'];
		$TotalMess[$MessType] += 1;
		$TotalMess[100]       += 1;
	}

	$page  .= "<script language=\"JavaScript\">\n";
	$page .= "function f(target_url, win_name) {\n";
	$page .= "var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=800,height=600,top=0,left=0');\n";
	$page .= "new_win.focus();\n";
	$page .= "}\n";
	$page .= "</script>\n";
	$page .= "<br />\n";
	$page .= "<div id=\"content\">\n";

	switch ($MessPageMode)
	{
		case 'write':
			if (!is_numeric($OwnerID))
				header("location:game.php?page=messages");

			$OwnerRecord = doquery("SELECT * FROM {{table}} WHERE `id` = '".intval($OwnerID)."';", 'users', true);

			if (!$OwnerRecord)
				header("location:game.php?page=messages");

			$OwnerHome   = doquery("SELECT * FROM {{table}} WHERE `id_planet` = '". intval($OwnerRecord["id_planet"]) ."';", 'galaxy', true);
			if (!$OwnerHome)
				header("location:game.php?page=messages");

			if ($_POST)
			{
				$error = 0;
				if (!$_POST["subject"])
				{
					$error++;
					$page .= "<table><tr><td><font color=#FF0000".$lang['mg_no_subject']."</font></td></tr></table>";
				}
				if (!$_POST["text"])
				{
					$error++;
					$page .= "<table><tr><td><font color=#FF0000>".$lang['mg_no_text']."</font></td></tr></table>";
				}
				if ($error == 0)
				{
					$page .= "<table><tr><td><font color=\"#00FF00\">".$lang['mg_msg_sended']."</font></td></tr></table>";

					$_POST['text'] = str_replace("'", '&#39;', $_POST['text']);

					$Owner   	= $OwnerID;
					$Sender  	= intval($CurrentUser['id']);
					//start mod
					$From    	= $CurrentUser['username'] ." [".$CurrentUser['universe'].":".$CurrentUser['galaxy'].":".$CurrentUser['system'].":".$CurrentUser['planet']."]";
					//end mod
          $Subject 	= $_POST['subject'];
                    $Message	= preg_replace ( "/([^\s]{80}?)/" , "\\1<br />" , trim ( nl2br ( strip_tags ( $_POST['text'], '<br>' ) ) ) );

					SendSimpleMessage($Owner, $Sender, '', 1, $From, $Subject, $Message);

					$subject 	= "";
					$text    	= "";
				}
			}
			$parse['id']           = $OwnerID;
			//start mod
			$parse['to']           = $OwnerRecord['username'] ." [".$OwnerHome['universe'].":".$OwnerHome['galaxy'].":".$OwnerHome['system'].":".$OwnerHome['planet']."]";
			//end mod
      $parse['subject']      = (!isset($subject)) ? $lang['mg_no_subject'] : $subject ;
			$parse['text']         = $text;
			$page                 .= parsetemplate(gettemplate('messages_pm_form'), $parse);
		break;
		case 'delete':
			$DeleteWhat = $_POST['deletemessages'];
			if($DeleteWhat == 'deleteall')
				doquery("DELETE FROM {{table}} WHERE `message_owner` = '". intval($CurrentUser['id']) ."';", 'messages');
			elseif ($DeleteWhat == 'deletemarked')
			{
				foreach($_POST as $Message => $Answer)
				{
					if (preg_match("/delmes/i", $Message) && $Answer == 'on')
					{
						$MessId   = str_replace("delmes", "", $Message);
						$MessHere = doquery("SELECT * FROM {{table}} WHERE `message_id` = '". intval($MessId) ."' AND `message_owner` = '". intval($CurrentUser['id']) ."';", 'messages');
						if ($MessHere)
							doquery("DELETE FROM {{table}} WHERE `message_id` = '".intval($MessId)."';", 'messages');

					}
				}
			}
			elseif ($DeleteWhat == 'deleteunmarked')
			{
				foreach($_POST as $Message => $Answer)
				{
					$CurMess    = preg_match("/showmes/i", $Message);
					$MessId     = str_replace("showmes", "", $Message);
					$Selected   = "delmes".$MessId;
					$IsSelected = $_POST[ $Selected ];
					if (preg_match("/showmes/i", $Message) && !isset($IsSelected))
					{
						$MessHere = doquery("SELECT * FROM {{table}} WHERE `message_id` = '". intval($MessId) ."' AND `message_owner` = '". intval($CurrentUser['id']) ."';", 'messages');
						if ($MessHere)
							doquery("DELETE FROM {{table}} WHERE `message_id` = '".intval($MessId)."';", 'messages');

					}
				}
			}
			$MessCategory = $_POST['category'];
			header("location:game.php?page=messages");
		break;
		case 'show':
			$page .= "<table width=\"519\">\n";
			$page .= "<form action=\"game.php?page=messages\" method=\"post\">\n";
			$page .= "<table>\n";
			$page .= "<tr>\n";
			$page .= "<td>\n<input name=\"messages\" value=\"1\" type=\"hidden\">\n";
			$page .= "<table width=\"519\">\n";
			$page .= "<tr><td class=\"c\" colspan=\"4\">".$lang['mg_message_title']."</td></tr>";
			$page .= "<tr>\n";
			$page .= "<th>".$lang['mg_action']."</th>\n";
			$page .= "<th>".$lang['mg_date']."</th>\n";
			$page .= "<th>".$lang['mg_from']."</th>\n";
			$page .= "<th>".$lang['mg_subject']."</th>\n";
			$page .= "</tr>\n";

			if ($MessCategory == 100)
			{
				$UsrMess       = doquery("SELECT * FROM {{table}} WHERE `message_owner` = '".intval($CurrentUser['id'])."' ORDER BY `message_time` DESC;", 'messages');
				$SubUpdateQry  = "";

				$QryUpdateUser  = "UPDATE {{table}} SET ";
				$QryUpdateUser .= "`new_message` = '0' ";
				$QryUpdateUser .= "WHERE ";
				$QryUpdateUser .= "`id` = '".intval($CurrentUser['id'])."';";
				doquery ( $QryUpdateUser, 'users');

				while ($CurMess = mysql_fetch_array($UsrMess))
				{
					$page .= "<tr>\n";
					$page .= "<input name=\"showmes". $CurMess['message_id'] . "\" type=\"hidden\" value=\"1\">\n";
					$page .= "<th><input name=\"delmes". $CurMess['message_id'] . "\" type=\"checkbox\"></th>\n";
					$page .= "<th>". date("m-d H:i:s", $CurMess['message_time']) ."</th>\n";
					$page .= "<th>". stripslashes( $CurMess['message_from'] ) ."</th>\n";
					$page .= "<th>". stripslashes( $CurMess['message_subject'] ) ." ";
					if ($CurMess['message_type'] == 1)
					{
						$page .= "<a href=\"game.php?page=messages&mode=write&amp;id=". $CurMess['message_sender'] ."&amp;subject=Re: " . htmlspecialchars( $CurMess['message_subject']) ."\">";
						$page .= "<img src=\"". $dpath ."img/m.gif\" border=\"0\"></a></th>\n";
					}
					else
					{
						$page .= "</th>";
					}
					$page .= "</tr><tr>";
					$page .= "<td>&nbsp;</td>";
					$page .= "<td colspan=\"3\" class=\"b\">". stripslashes( nl2br( $CurMess['message_text'] ) ) ."</td>";
					$page .= "</tr>";
				}
			}
			else
			{
				$UsrMess       = doquery("SELECT * FROM {{table}} WHERE `message_owner` = '".intval($CurrentUser['id'])."' AND `message_type` = '".$MessCategory."' ORDER BY `message_time` DESC;", 'messages');

				while ($CurMess = mysql_fetch_array($UsrMess))
				{
					if ($CurMess['message_type'] == $MessCategory)
					{
						$QryUpdateUser  = "UPDATE {{table}} SET ";
						$QryUpdateUser .= "`new_message` = '0' ";
						$QryUpdateUser .= "WHERE ";
						$QryUpdateUser .= "`id` = '".intval($CurrentUser['id'])."';";
						doquery ( $QryUpdateUser, 'users');

						$page .= "\n<tr>";
						$page .= "<input name=\"showmes". $CurMess['message_id'] . "\" type=\"hidden\" value=\"1\">";
						$page .= "<th><input name=\"delmes". $CurMess['message_id'] ."\" type=\"checkbox\"></th>";
						$page .= "<th>". date("m-d H:i:s O", $CurMess['message_time']) ."</th>";
						$page .= "<th>". stripslashes( $CurMess['message_from'] ) ."</th>";
						$page .= "<th>". stripslashes( $CurMess['message_subject'] ) ." ";
						if ($CurMess['message_type'] == 1)
						{
							$page .= "<a href=\"game.php?page=messages&mode=write&amp;id=". $CurMess['message_sender'] ."&amp;subject=Re:". htmlspecialchars( $CurMess['message_subject']) ."\">";
							$page .= "<img src=\"". $dpath ."img/m.gif\" border=\"0\"></a></th>";
						}
						else
							$page .= "</th>";

						$page .= "</tr><tr>";
						$page .= "<td class=\"b\"> </td>";
						$page .= "<td colspan=\"3\" class=\"b\">". nl2br( stripslashes( $CurMess['message_text'] ) ) ."</td>";
						$page .= "</tr>";
					}
				}
			}


			$page .= "<tr>";
			$page .= "<th colspan=\"4\">";
			$page .= "<input id=\"fullreports\" name=\"fullreports\" type=\"checkbox\">".$lang['mg_show_only_header_spy_reports']."</th>";
			$page .= "</tr><tr>";
			$page .= "<th colspan=\"4\">";
			$page .= "<select id=\"deletemessages\" name=\"deletemessages\">";
			$page .= "<option value=\"deletemarked\">".$lang['mg_delete_marked']."</option>";
			$page .= "<option value=\"deleteunmarked\">".$lang['mg_delete_unmarked']."</option>";
			$page .= "<option value=\"deleteall\">".$lang['mg_delete_all']."</option>";
			$page .= "</select>";
			$page .= "<input value=\"".$lang['mg_confirm_delete']."\" type=\"submit\">";
			$page .= "</th>";
			$page .= "</tr><tr>";
			$page .= "<td colspan=\"4\"></td>";
			$page .= "</tr>";
			$page .= "</table>\n";
			$page .= "<table width=\"100%\">";
			$page .= "<tr>";
			$page .= "<td class=\"c\">".$lang['mg_game_operators']."</td>";
			$page .= "</tr>";

			$QrySelectUser  = "SELECT `username`, `email` ";
			$QrySelectUser .= "FROM `{{table}}` ";
			$QrySelectUser .= "WHERE `authlevel` != '0' ORDER BY `username` ASC;";
			$GameOps = doquery ($QrySelectUser, 'users');

			while($Ops = mysql_fetch_assoc($GameOps))
				$page .= "<tr><th>".$Ops['username']." <a href=\"mailto:".$Ops['email']."\"><img src=\"". $dpath ."img/m.gif\" /></a></th></tr>";

			$page .= "</table>";
			$page .= "</td>";
			$page .= "</tr>";
			$page .= "</table>\n";
			$page .= "</form>";
		break;
		default:
			$page .= "<br>";
			$page .= "<table width=\"569\">";
			$page .= "<tr>";
			$page .= "	<td class=\"c\" colspan=\"3\">".$lang['mg_message_title']."</td>";
			$page .= "</tr><tr>";
			$page .= "	<th colspan=\"2\">".$lang['mg_message_type']."</th>";
			$page .= "	<th>".$lang['mg_total']."</th>";
			$page .= "</tr>";
			$page .= "<tr>";
			$page .= "	<th colspan=\"2\"><a href=\"game.php?page=messages&mode=show&amp;messcat=100\"><font color=\"". $TitleColor[100] ."\">". $lang['mg_type'][100] ."</a></th>";
			$page .= "	<th><font color=\"". $TitleColor[100] ."\">". $TotalMess[100] ."</font></th>";
			$page .= "</tr>";
			for ($MessType = 0; $MessType < 100; $MessType++)
			{
				if ( in_array($MessType, $MessageType) )
				{
					$page .= "<tr>";
					$page .= "	<th colspan=\"2\"><a href=\"game.php?page=messages&mode=show&amp;messcat=". $MessType ." \"><font color=\"". $TitleColor[$MessType] ."\">". $lang['mg_type'][$MessType] ."</a></th>";
					$page .= "	<th><font color=\"". $TitleColor[$MessType] ."\">". $TotalMess[$MessType] ."</font></th>";
					$page .= "</tr>";
				}
			}
			$page .= "</table>";

		break;
	}
	$page .= "</div>";
	display($page);
}
?>