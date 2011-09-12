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

function ShowBuddyPage($CurrentUser)
{
	global $lang;

   if ( isset ( $_GET["lang"] ) or isset ( $_POST["lang"] ) )
	{
		die();
	}

	foreach($_GET as $name => $value)
	{
		$$name = intval( $value );
	}
	switch($mode)
	{
		case 1:
			switch($sm)
			{
				case 1:
					$senderID = doquery("SELECT * FROM {{table}} WHERE `id`='".intval($bid)."'","buddy",true);
					if($senderID['sender'] != $CurrentUser['id'])
					{
						SendSimpleMessage($senderID['sender'],$CurrentUser['id'],'',1,$CurrentUser['username'],$lang['bu_deleted_title'],str_replace('%u', $CurrentUser['username'],$lang['bu_deleted_text']));
					}
					elseif($senderID['sender'] == $CurrentUser['id'])
					{
						SendSimpleMessage($senderID['owner'],$CurrentUser['id'],'',1,$CurrentUser['username'],$lang['bu_deleted_title'],str_replace('%u', $CurrentUser['username'],$lang['bu_deleted_text']));
					}
					doquery("DELETE FROM {{table}} WHERE `id`='".intval($bid)."'  AND (`owner`='".$CurrentUser['id']."' OR `sender`='".$CurrentUser['id']."') ","buddy");					
               header("location:game.php?page=buddy");
				break;

				case 2:
					$senderID = doquery("SELECT * FROM {{table}} WHERE `id`='".intval($bid)."'","buddy",true);
					SendSimpleMessage($senderID['sender'],$CurrentUser['id'],'',1,$CurrentUser['username'],$lang['bu_accepted_title'],str_replace('%u', $CurrentUser['username'],$lang['bu_accepted_text']));
					doquery("UPDATE {{table}} SET `active` = '1' WHERE `id` ='".intval($bid)."' AND `owner`='".$CurrentUser['id']."'","buddy");
               header("location:game.php?page=buddy");
				break;

				case 3:
					$query = doquery("SELECT `id` FROM {{table}} WHERE `sender`='".intval($CurrentUser[id])."' AND `owner`='".intval($_POST)."' OR `owner`='".intval($CurrentUser[id])."' AND `sender`='".intval($_POST[u])."'","buddy",true);
					if($query == array())
					{
						$text = mysql_escape_string( strip_tags( $_POST['text'] ) );
						SendSimpleMessage(intval($_POST['u']),$CurrentUser['id'],'',1,$CurrentUser['username'],$lang['bu_to_accept_title'],str_replace('%u', $CurrentUser['username'],$lang['bu_to_accept_text']));
                  doquery("INSERT INTO {{table}} SET `sender`='".intval($CurrentUser[id])."' ,`owner`='".intval($_POST[u])."' ,`active`='0' ,`text`='{$text}'","buddy");
						header("location:game.php?page=buddy");
					}
					else
					{
						message($lang['bu_request_exists'], 'game.php?page=buddy',2 );
					}
				break;
			}
		break;

		case 2:
			if($u==$CurrentUser['id'])
			{
				message($lang['bu_cannot_request_yourself'],'game.php?page=buddy',2);
			}
			else
			{
				$player=doquery("SELECT `username` FROM {{table}} WHERE `id`='".intval($u)."'","users",true);
				$page="<script src=scripts/cntchar.js type=text/javascript></script>
				<center>
				<form action=game.php?page=buddy&mode=1&sm=3 method=post>
				<input type=hidden name=u value={$u}>
				<div id=\"content\"><table width=520>
				<tr><td class=c colspan=2>".$lang['bu_request_message']."</td></tr>
				<tr><th>".$lang['bu_player']."</th><th>{$player[username]}</th></tr>
				<tr><th>".$lang['bu_request_text']." (<span id=cntChars>0</span> / 5000 ".$lang['bu_characters'].")</th><th><textarea name=text cols=60 rows=10 onKeyUp=javascript:cntchar(5000)></textarea></th></tr>
				<tr><td class=c><a href=javascript:back();>".$lang['bu_back']."</a></td><td class=c><input type=submit value=\"".$lang['bu_send']."\"></td></tr>
				</table></form></div>";
				display ($page);
			}
		break;
		default:

			$liste=doquery("SELECT * FROM {{table}} WHERE `sender`='".intval($CurrentUser[id])."' OR `owner`='".intval($CurrentUser[id])."'","buddy");

			while($buddy	=	mysql_fetch_assoc($liste))
			{
				if($buddy['active']	==0)
				{
					if($buddy['sender']==$CurrentUser['id'])
					{ //start mod
						$owner=doquery("SELECT `id`, `username`, `universe`, `galaxy`, `system`, `planet`,`ally_id`, `ally_name` FROM {{table}} WHERE `id`='".intval($buddy[owner])."'","users",true);
            $myrequest.="<tr><th><a href=game.php?page=messages&mode=write&id={$owner[id]}>{$owner[username]}</a></th>
						<th><a href=game.php?page=alliance&mode=ainfo&a={$owner[ally_id]}>{$owner[ally_name]}</a></th>
            <th><a href=game.php?page=galaxy&mode=3&universe={$owner[universe]}&galaxy={$owner[galaxy]}&system={$owner[system]}>{$owner[galaxy]}:{$owner[system]}:{$owner[planet]}</a></th>
						<th>{$buddy[text]}</th>
						<th><a href=game.php?page=buddy&mode=1&sm=1&bid={$buddy[id]}>".$lang['bu_cancel_request']."</a></th></tr>";
					 	 //end mod
          }
					else
					{  //start mod
						$sender=doquery("SELECT `id`, `username`, `universe`, `galaxy`, `system`, `planet`,`ally_id`, `ally_name` FROM {{table}} WHERE `id`='".intval($buddy[sender])."'","users",true);
						$outrequest.="<tr><th><a href=game.php?page=messages&mode=write&id={$sender[id]}>{$sender[username]}</a></th>
						<th><a href=game.php?page=alliance&mode=ainfo&a={$sender[ally_id]}>{$sender[ally_name]}</a></th>
						<th><a href=game.php?page=galaxy&mode=3&universe={$sender[universe]}&galaxy={$sender[galaxy]}&system={$sender[system]}>{$sender[universe]}:{$sender[galaxy]}:{$sender[system]}:{$sender[planet]}</a></th>
						<th>{$buddy[text]}</th>
						<th><a href=game.php?page=buddy&mode=1&sm=2&bid={$buddy[id]}>".$lang['bu_accept']."</a><br><a href=game.php?page=buddy&mode=1&sm=1&bid={$buddy[id]}>".$lang['bu_decline']."</a></th></tr>";
				   //end mod
        	}
				}
				else
				{
					if($buddy['sender']==$CurrentUser['id'])
						$owner = doquery("SELECT `id`, `username`, `onlinetime`, `universe`, `galaxy`, `system`, `planet`,`ally_id`, `ally_name` FROM {{table}} WHERE `id`='".intval($buddy[owner])."'","users",true);
					else
						$owner = doquery("SELECT `id`, `username`, `onlinetime`, `universe`, `galaxy`, `system`, `planet`,`ally_id`, `ally_name` FROM {{table}} WHERE `id`='".intval($buddy[sender])."'","users",true);
             //start mod
						$myfriends.="<tr><th><a href=game.php?page=messages&mode=write&id={$owner[id]}>{$owner[username]}</a></th>
						<th><a href=game.php?page=alliance&mode=ainfo&a={$owner[ally_id]}>{$owner[ally_name]}</a></th>
						<th><a href=game.php?page=galaxy&mode=3&universe={$owner[universe]}&galaxy={$owner[galaxy]}&system={$owner[system]}>{$owner[galaxy]}:{$owner[system]}:{$owner[planet]}</a></th>
						<th><font color=".(( $owner["onlinetime"] + 60 * 10 >= time() )?"lime>".$lang['bu_connected']."":(( $owner["onlinetime"] + 60 * 15 >= time() )?"yellow>".$lang['bu_fifteen_minutes']."":"red>".$lang['bu_disconnected'].""))."</font></th>
						<th><a href=game.php?page=buddy&mode=1&sm=1&bid={$buddy[id]}>".$lang['bu_delete']."</a></th></tr>";
				}   //end mod
			}

			$myfriends=($myfriends!='')?$myfriends:'<th colspan=6></th>';
			$nor='<th colspan=6></th>';
			$outrequest=($outrequest!='')?$outrequest:$nor;
			$myrequest=($myrequest!='')?$myrequest:$nor;
			$page="<br/><div id=\"content\"><table width=520>
			<tr><td class=c colspan=6>".$lang['bu_buddy_list']."</td></tr>
			<tr><td class=c colspan=6><center>".$lang['bu_requests']."</a></td></tr>
			<tr><td class=c>".$lang['bu_player']."</td>
			<td class=c>".$lang['bu_alliance']."</td>
			<td class=c>".$lang['bu_coords']."</td>
			<td class=c>".$lang['bu_text']."</td>
			<td class=c>".$lang['bu_action']."</td>
			</tr>
			<tr>{$outrequest}</tr>
			<tr><td class=c colspan=6><center>".$lang['bu_my_requests']."</a></td></tr>
			<tr><td class=c>".$lang['bu_player']."</td>
			<td class=c>".$lang['bu_alliance']."</td>
			<td class=c>".$lang['bu_coords']."</td>
			<td class=c>".$lang['bu_text']."</td>
			<td class=c>".$lang['bu_action']."</td>
			</tr>
			<tr>{$myrequest}</tr>
			<tr><td class=c colspan=6><center>".$lang['bu_partners']."</a></td></tr>
			<tr><td class=c>".$lang['bu_player']."</td>
			<td class=c>".$lang['bu_alliance']."</td>
			<td class=c>".$lang['bu_coords']."</td>
			<td class=c>".$lang['bu_estate']."</td>
			<td class=c>".$lang['bu_action']."</td>
			</tr>
			<tr>{$myfriends}</tr>
			</table></div>";

			display ($page);

		break;
	}
}
?>