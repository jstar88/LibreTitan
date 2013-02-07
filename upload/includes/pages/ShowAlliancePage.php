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

class ShowAlliancePage
{
	private function bbcode($string)
	{
		$pattern = array(
		    '/\\n/',
		    '/\\r/',
		    '/\[list\](.*?)\[\/list\]/ise',
		    '/\[b\](.*?)\[\/b\]/is',
		    '/\[strong\](.*?)\[\/strong\]/is',
		    '/\[i\](.*?)\[\/i\]/is',
		    '/\[u\](.*?)\[\/u\]/is',
		    '/\[s\](.*?)\[\/s\]/is',
		    '/\[del\](.*?)\[\/del\]/is',
		    '/\[url=(.*?)\](.*?)\[\/url\]/ise',
		    '/\[email=(.*?)\](.*?)\[\/email\]/is',
		    '/\[img](.*?)\[\/img\]/ise',
		    '/\[color=(.*?)\](.*?)\[\/color\]/is',
		    '/\[quote\](.*?)\[\/quote\]/ise',
		    '/\[code\](.*?)\[\/code\]/ise',
		    '/\[font=(.*?)\](.*?)\[\/font\]/ise',
		    '/\[bg=(.*?)\](.*?)\[\/bg\]/ise',
		    '/\[size=(.*?)\](.*?)\[\/size\]/ise'
		);

		$replace = array(
		    '<br/>',
		    '',
		    '$this->sList(\'\\1\')',
		    '<b>\1</b>',
		    '<strong>\1</strong>',
		    '<i>\1</i>',
		    '<span style="text-decoration: underline;">\1</span>',
		    '<span style="text-decoration: line-through;">\1</span>',
		    '<span style="text-decoration: line-through;">\1</span>',
		    '$this->urlfix(\'\\1\',\'\\2\')',
		    '<a href="mailto:\1" title="\1">\2</a>',
		    '$this->imagefix(\'\\1\')',
		    '<span style="color: \1;">\2</span>',
		    '$this->sQuote(\'\1\')',
		    '$this->sCode(\'\1\')',
		    '$this->fontfix(\'\\1\',\'\\2\')',
		    '$this->bgfix(\'\\1\',\'\\2\')',
		    '$this->sizefix(\'\\1\',\'\\2\')'
		);

		return preg_replace($pattern, $replace, nl2br(htmlspecialchars(stripslashes($string))));
	}

	private function sCode($string)
	{
		$pattern =  '/\<img src=\\\"(.*?)img\/smilies\/(.*?).png\\\" alt=\\\"(.*?)\\\" \/>/s';
		$string = preg_replace($pattern, '\3', $string);
		return '<pre style="color: #DDDD00; background-color:gray ">' . trim($string) . '</pre>';
	}

	private function sQuote($string)
	{
		$pattern =  '/\<img src=\\\"(.*?)img\/smilies\/(.*?).png\\\" alt=\\\"(.*?)\\\" \/>/s';
		$string = preg_replace($pattern, '\3', $string);
		return '<blockquote><p style="color: #000000; font-size: 10pt; background-color:55AACC; font-family: Arial">' . trim($string) . '</p></blockquote>';
	}

	private function sList($string)
	{
		$tmp = explode('[*]', stripslashes($string));
		$out = null;
		foreach($tmp as $list) {
			if(strlen(str_replace('', '', $list)) > 0) {
				$out .= '<li>' . trim($list) . '</li>';
			}
		}
		return '<ul>' . $out . '</ul>';
	}

	private function imagefix($img)
	{
		if(substr($img, 0, 7) != 'http://')
		{
			$img = './images/' . $img;
		}
		return '<img src="' . $img . '" alt="' . $img . '" title="' . $img . '" />';
	}

	private function urlfix($url, $title)
	{
		$title = stripslashes($title);
		$url   = trim($url);
		return (substr ($url, 0, 5) == 'data:' || substr ($url, 0, 5) ==  'file:' || substr ($url, 0, 11) == 'javascript:' || substr  ($url, 0, 4) == 'jar:' || substr ($url, 0, 1) == '#') ? '' : '<a  href="' . $url . '" title="'.htmlspecialchars($title,  ENT_QUOTES).'">'.htmlspecialchars($title, ENT_QUOTES).'</a>';
	}

	private function fontfix($font, $title)
	{
		$title = stripslashes($title);
		return '<span style="font-family:' . $font . '">' . $title . '</span>';
	}

	private function bgfix($bg, $title)
	{
		$title = stripslashes($title);
		return '<span style="background-color:' . $bg . '">' . $title . '</span>';
	}

	private function sizefix($size, $text)
	{
		$title = stripslashes($text);
		return '<span style="font-size:' . $size . 'px">' . $title . '</span>';
	}

	private function MessageForm($Title, $Message, $Goto = '', $Button = ' ok ', $TwoLines = false)
	{
		$Form .= "<div id=\"content\"><form action=\"". $Goto ."\" method=\"post\">";
		$Form .= "<table width=\"519\">";
		$Form .= "<tr>";
		$Form .= "<td class=\"c\" colspan=\"2\">". $Title ."</td>";
		$Form .= "</tr><tr>";
		if ($TwoLines == true)
		{
			$Form .= "<th colspan=\"2\">". $Message ."</th>";
			$Form .= "</tr><tr>";
			$Form .= "<th colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"". $Button ."\"></th>";
		}
		else
			$Form .= "<th colspan=\"2\">". $Message ."<input type=\"submit\" value=\"". $Button ."\"></th>";
		$Form .= "</tr>";
		$Form .= "</table>";
		$Form .= "</form>";
		$Form .= "</div>";

		return $Form;
	}

	public function __construct($CurrentUser)
	{
		global $dpath, $phpEx, $lang;

		$parse = $lang;

		//MODO PRINCIPAL
		$mode = $_GET['mode'];
		if (empty($mode))   { unset($mode); }
		// ORDEN ALTERNATIVA "A"
		$a     = intval($_GET['a']);
		if (empty($a))      { unset($a); }
		// ORDEN 1
		$sort1 = intval($_GET['sort1']);
		if (empty($sort1))  { unset($sort1); }
		// ORDEN 2
		$sort2 = intval($_GET['sort2']);
		if (empty($sort2))  { unset($sort2); }
		// ELIMINAR RANGO
		$d = $_GET['d'];
		if ((!is_numeric($d)) || (empty($d) && $d != 0))unset($d);
		// EDITAR
		$edit = $_GET['edit'];
		if (empty($edit))unset($edit);
		// ADMIN -> RANGOS -> MIEMBROS
		$rank = intval($_GET['rank']);
		if (empty($rank))unset($rank);
		// ADMIN -> EXPULSAR -> MIEMBROS
		$kick = intval($_GET['kick']);
		if (empty($kick))unset($kick);

		$id = intval($_GET['id']);
		if (empty($id))unset($id);

		$yes      = $_GET['yes'];
		$allyid   = intval($_GET['allyid']);
		$show     = intval($_GET['show']);
		$sendmail = intval($_GET['sendmail']);
		$t        = $_GET['t'];
		$tag      = mysql_escape_string($_GET['tag']);

		// EN ESTE CASO EL USUARIO SOLO EST� DE VISITA EN LA ALIANZA
		if ($_GET['mode'] == 'ainfo')
		{
			if (isset($tag) && $a == "")
				$allyrow = doquery("SELECT * FROM {{table}} WHERE ally_tag='".mysql_escape_string ( $tag )."'", "alliance", true);
			elseif(is_numeric($a) && $a != 0 && $tag == "")
				$allyrow = doquery("SELECT * FROM {{table}} WHERE id='".mysql_escape_string ( $a )."'", "alliance", true);
			else
				header("location:game.". $phpEx . "?page=alliance",2);

			if (!$allyrow)
				header("location:game.". $phpEx . "?page=alliance",2);

			extract($allyrow);

			if ($ally_image != "")
				$ally_image = "<tr><th colspan=2><img src=\"".$ally_image."\"></td></tr>";

			if ($ally_description != "")
				$ally_description = "<tr><th colspan=2 height=100>".nl2br($this->bbcode($ally_description))."</th></tr>";
			else
				$ally_description = "<tr><th colspan=2 height=100>".$lang['al_description_message']	."</th></tr>";

			if ($ally_web != "")
			{
				$ally_web = str_replace ( "http://" , "" , $ally_web );

				$ally_web = "<tr><th>".$lang['al_web_text']."</th><th><a href=\"http://$ally_web\">$ally_web</a></th></tr>";
			}


			$parse['ally_description'] 		= $ally_description;
			$parse['ally_image'] 			= $ally_image;
			$parse['ally_web'] 				= $ally_web;
			$parse['ally_member_scount'] 	= $ally_members;
			$parse['ally_name'] 			= $ally_name;
			$parse['ally_tag'] 				= $ally_tag;

			if ($CurrentUser['ally_id'] == 0)
				$parse['solicitud'] = "<tr><th>".$lang['al_request']."</th><th><a href=\"game.php?page=alliance&mode=apply&amp;allyid=" . $id . "\">".$lang['al_click_to_send_request']."</a></th></tr>";
			else
				$parse['solicitud'] = "";

			display(parsetemplate(gettemplate('alliance/alliance_ainfo'), $parse));
		}
		// EN ESTE CASO EL USUARIO NO SE ENCUENTRA AUN EN NINGUNA ALIANZA
		if ($CurrentUser['ally_id'] == 0)
		{	//CREAR ALIANZA
			if ($mode == 'make' && $CurrentUser['ally_request'] == 0)
			{
				if ($yes == 1 && $_POST)
				{
					if (!$_POST['atag'])
						message($lang['al_tag_required'], "game.php?page=alliance&mode=make",2);

					if (!$_POST['aname'])
						message($lang['al_name_required'],"game.php?page=alliance&mode=make",2);

					$tagquery = doquery("SELECT * FROM `{{table}}` WHERE ally_tag='".mysql_escape_string($_POST['atag'])."'", 'alliance', true);

					if ($tagquery)
						message(str_replace('%s', $_POST['atag'], $lang['al_already_exists']),"game.php?page=alliance&mode=make",2);


					doquery("INSERT INTO {{table}} SET
					`ally_name`='".mysql_escape_string($_POST['aname'])."',
					`ally_tag`='".mysql_escape_string($_POST['atag'])."' ,
					`ally_owner`='{$CurrentUser['id']}',
					`ally_owner_range`='Leader',
					`ally_members`='1',
					`ally_register_time`=" . time() , "alliance");

					$allyquery = doquery("SELECT * FROM {{table}} WHERE ally_tag='".mysql_escape_string($_POST['atag'])."'", 'alliance', true);

					doquery("UPDATE {{table}} SET
					`ally_id`='{$allyquery['id']}',
					`ally_name`='".mysql_escape_string($allyquery['ally_name'])."',
					`ally_register_time`='" . time() . "'
					WHERE `id`='{$CurrentUser['id']}'", "users");

					$page = $this->MessageForm(str_replace('%s', $_POST['atag'], $lang['al_created']),

					str_replace('%s', $_POST['atag'], $lang['al_created']) . "<br><br>", "", $lang['al_continue']);
				}
				else
					$page .= parsetemplate(gettemplate('alliance/alliance_make'), $parse);

				display($page);
			}
			//BUSCAR ALIANZA
			if ($mode == 'search' && $CurrentUser['ally_request'] == 0)
			{
				$page = parsetemplate(gettemplate('alliance/alliance_searchform'), $parse);

				if ($_POST)
				{
					$search = doquery("SELECT * FROM {{table}} WHERE ally_name LIKE '%".mysql_escape_string ($_POST['searchtext'])."%' or ally_tag LIKE '%".mysql_escape_string ($_POST['searchtext'])."%' LIMIT 30", "alliance");

					if (mysql_num_rows($search) != 0)
					{
						while ($s = mysql_fetch_array($search))
						{
							$searchData 					= array();
							$searchData['ally_tag'] 		= "<a href=\"game.php?page=alliance&mode=apply&allyid={$s['id']}\">{$s['ally_tag']}</a>";
							$searchData['ally_name'] 		= $s['ally_name'];
							$searchData['ally_members'] 	= $s['ally_members'];

							$parse['result'] .= parsetemplate(gettemplate('alliance/alliance_searchresult_row'), $searchData);
						}

						$page .= parsetemplate(gettemplate('alliance/alliance_searchresult_table'), $parse);
					}
				}
				display($page);
			}

			if ($mode == 'apply' && $CurrentUser['ally_request'] == 0)
			{ //SOLICITUDES
				if($_GET['allyid'] != NULL)
					$alianza = doquery("SELECT * FROM {{table}} WHERE id='{".intval($_GET['allyid'])."'", "alliance", true);

				if($alianza['ally_request_notallow'] == 1)
					message($lang['al_alliance_closed'], "game.php?page=alliance",2);
				else
				{
					if (!is_numeric($_GET['allyid']) || !$_GET['allyid'] || $CurrentUser['ally_request'] != 0 || $CurrentUser['ally_id'] != 0)
						header("location:game.". $phpEx . "?page=alliance",2);

					$allyrow = doquery("SELECT ally_tag,ally_request FROM {{table}} WHERE id='" . intval($_GET['allyid']) . "'", "alliance", true);

					if (!$allyrow)
						header("location:game.". $phpEx . "?page=alliance",2);

					extract($allyrow);

					if ($_POST['enviar'] == $lang['al_applyform_send'])
					{
						doquery("UPDATE {{table}} SET `ally_request`='" . intval($allyid) . "', ally_request_text='" . mysql_escape_string(strip_tags($_POST['text'])) . "', ally_register_time='" . time() . "' WHERE `id`='" . $CurrentUser['id'] . "'", "users");

						message($lang['al_request_confirmation_message'],"game.php?page=alliance",2);
					}
					else
						$text_apply = ($ally_request) ? $ally_request : $lang['al_default_request_text'];

					$parse['allyid'] 			= intval($_GET['allyid']);
					$parse['chars_count'] 		= strlen($text_apply);
					$parse['text_apply'] 		= $text_apply;
					$parse['Write_to_alliance'] = str_replace('%s', $ally_tag, $lang['al_write_request']);

					display(parsetemplate(gettemplate('alliance/alliance_applyform'), $parse));
				}
			}

			if ($CurrentUser['ally_request'] != 0)
			{
				$allyquery = doquery("SELECT ally_tag FROM {{table}} WHERE id='" . intval($CurrentUser['ally_request']) . "' ORDER BY `id`", "alliance", true);

				extract($allyquery);

				if ($_POST['bcancel'])
				{
					doquery("UPDATE {{table}} SET `ally_request`=0 WHERE `id`=" . intval($CurrentUser['id']), "users");

					$lang['request_text'] = str_replace('%s', $ally_tag, $lang['al_request_deleted']);
					$lang['button_text'] = $lang['al_continue'];
					$page = parsetemplate(gettemplate('alliance/alliance_apply_waitform'), $lang);
				}
				else
				{
					$lang['request_text'] = str_replace('%s', $ally_tag, $lang['al_request_wait_message']);
					$lang['button_text'] = $lang['al_delete_request'];
					$page = parsetemplate(gettemplate('alliance/alliance_apply_waitform'), $lang);
				}

				display($page);
			}
			else
			{
				display(parsetemplate(gettemplate('alliance/alliance_defaultmenu'), $lang));
			}
		}
		elseif ($CurrentUser['ally_id'] != 0 && $CurrentUser['ally_request'] == 0) // CUANDO YA ESTA EN UNA ALIANZA
		{
			$ally = doquery("SELECT * FROM {{table}} WHERE id='".intval($CurrentUser['ally_id'])."'", "alliance", true);
			$ally_ranks = unserialize($ally['ally_ranks']);

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['onlinestatus'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_watch_memberlist_status = true;
			else
				$user_can_watch_memberlist_status = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['memberlist'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_watch_memberlist = true;
			else
				$user_can_watch_memberlist = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['mails'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_send_mails = true;
			else
				$user_can_send_mails = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['kick'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_kick = true;
			else
				$user_can_kick = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['rechtehand'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_edit_rights = true;
			else
				$user_can_edit_rights = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['delete'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_can_exit_alliance = true;
			else
				$user_can_exit_alliance = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['bewerbungen'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_bewerbungen_einsehen = true;
			else
				$user_bewerbungen_einsehen = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['bewerbungenbearbeiten'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_bewerbungen_bearbeiten = true;
			else
				$user_bewerbungen_bearbeiten = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['administrieren'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_admin = true;
			else
				$user_admin = false;

			if ($ally_ranks[$CurrentUser['ally_rank_id']-1]['onlinestatus'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
				$user_onlinestatus = true;
			else
				$user_onlinestatus = false;

			if (!$ally)
			{
				doquery("UPDATE `{{table}}` SET `ally_id` = 0 WHERE `id` = ".intval($CurrentUser['id'])."", "users");
				header("location:game.". $phpEx . "?page=alliance",2);
			}
		// < ------------------------------------------------------------ SALIR DE LA ALIANZA ------------------------------------------------------------ >
			if ($mode == 'exit')
			{
				if ($ally['ally_owner'] == $CurrentUser['id'])
					message($lang['al_founder_cant_leave_alliance'],"game.php?page=alliance",2);

				if ($_GET['yes'] == 1)
				{
					doquery("UPDATE {{table}} SET `ally_id` = 0, `ally_name` = '', ally_rank_id = 0 WHERE `id`='".intval($CurrentUser['id'])."'", "users");
					doquery("UPDATE {{table}} SET `ally_members` = `ally_members` - 1 WHERE `id`='".intval($ally['id'])."'", "alliance");

					$lang['Go_out_welldone'] = str_replace("%s", $ally_name, $lang['al_leave_sucess']);
					$page = $this->MessageForm($lang['Go_out_welldone'], "<br>", $PHP_SELF, $lang['al_continue']);
				}
				else
				{
					$lang['Want_go_out'] = str_replace("%s", $ally_name, $lang['al_do_you_really_want_to_go_out']);
					$page = $this->MessageForm($lang['Want_go_out'], "<br>", "game.php?page=alliance&mode=exit&yes=1", $lang['al_go_out_yes']);
				}
				display($page);
			}
		// < ------------------------------------------------------------- LISTA DE MIEMBROS ------------------------------------------------------------- >
			if ($mode == 'memberslist')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_can_watch_memberlist)
					header("location:game.". $phpEx . "?page=alliance",2);

				if ($sort2)
				{
					$sort1 = intval($_GET['sort1']);
					$sort2 = intval($_GET['sort2']);

					if ($sort1 == 1) {
					$sort = " ORDER BY `username`";
					} elseif ($sort1 == 2) {
					$sort = " ORDER BY `ally_rank_id`";
					} elseif ($sort1 == 3) {
					$sort = " ORDER BY `total_points`";
					} elseif ($sort1 == 4) {
					$sort = " ORDER BY `ally_register_time`";
					} elseif ($sort1 == 5) {
					$sort = " ORDER BY `onlinetime`";
					} else {
					$sort = " ORDER BY `id`";
					}

					if ($sort2 == 1) {
					$sort .= " DESC;";
					} elseif ($sort2 == 2) {
					$sort .= " ASC;";
					}
					$listuser = doquery("SELECT * FROM `{{table}}users` inner join `{{table}}statpoints` on `{{table}}users`.`id`=`{{table}}statpoints`.`id_owner` WHERE ally_id='".intval($CurrentUser['ally_id'])."' AND STAT_type=1 $sort", '');
				}
				else
					$listuser = doquery("SELECT * FROM {{table}} WHERE ally_id='".intval($CurrentUser['ally_id'])."'", 'users');

				$i = 0;

				while ($u = mysql_fetch_array($listuser))
				{
					$UserPoints = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . intval($u['id']) . "';", 'statpoints', true);

					$i++;
					$u['i'] = $i;

					if ($u["onlinetime"] + 60 * 10 >= time() && $user_can_watch_memberlist_status)
						$u["onlinetime"] = "\"lime\">Conectado<";
					elseif ($u["onlinetime"] + 60 * 20 >= time() && $user_can_watch_memberlist_status)
						$u["onlinetime"] = "\"yellow\">15 min<";
					elseif ($user_can_watch_memberlist_status)
						$u["onlinetime"] = "\"red\">Desconectado<";
					else
						$u["onlinetime"] = "\"orange\">-<";

					if ($ally['ally_owner'] == $u['id'])
						$u["ally_range"] = ($ally['ally_owner_range'] == '')?$lang['al_founder_rank_text']:$ally['ally_owner_range'];
					elseif ($u['ally_rank_id'] == 0 )
						$u["ally_range"] = $lang['al_new_member_rank_text'];
					else
						$u["ally_range"] = $ally_ranks[$u['ally_rank_id']-1]['name'];

					$u["dpath"] 	= $dpath;
					$u['points'] 	= "" . pretty_number($UserPoints['total_points']) . "";

					if ($u['ally_register_time'] > 0)
						$u['ally_register_time'] = date("Y-m-d h:i:s", $u['ally_register_time']);
					else
						$u['ally_register_time'] = "-";

					$page_list .= parsetemplate(gettemplate('alliance/alliance_memberslist_row'), $u);
				}

				if ($sort2 == 1) {$s = 2;}
				elseif ($sort2 == 2) {$s = 1;}
				else {$s = 1;}

				if ($i != $ally['ally_members'])
					doquery("UPDATE {{table}} SET `ally_members`='".intval($i)."' WHERE `id`='".intval($ally['id'])."'", 'alliance');
				$parse['i'] = $i;
				$parse['s'] = $s;
				$parse['list'] = $page_list;

				display(parsetemplate(gettemplate('alliance/alliance_memberslist_table'), $parse));
			}
		// < ------------------------------------------------------------- CORREO CIRCULAR ------------------------------------------------------------- >
			if ($mode == 'circular')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_can_send_mails)
					header("location:game.". $phpEx . "?page=alliance",2);

				if ($sendmail == 1)
				{
					$_POST['r'] 	= intval($_POST['r']);
					$_POST['text']	= preg_replace ( "/([^\s]{80}?)/" , "\\1<br />" , trim ( nl2br ( strip_tags ( $_POST['text'], '<br>' ) ) ) );

					if ($_POST['r'] == 0)
						$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='".intval($CurrentUser['ally_id'])."'", "users");
					else
						$sq = doquery("SELECT id,username FROM {{table}} WHERE ally_id='".intval($CurrentUser['ally_id'])."' AND ally_rank_id='".intval($_POST['r'])."'", "users");

					$list = '';

					while ($u = mysql_fetch_array($sq))
					{
						SendSimpleMessage($u['id'],$CurrentUser['id'],'',2,$ally['ally_tag'],$CurrentUser['username'],$_POST['text']);

						$list .= "<br>{$u['username']} ";
					}

					$page = $this->MessageForm($lang['al_circular_sended'],$list, "game.php?page=alliance", $lang['al_continue'], true);

					display($page);
				}

				$lang['r_list'] = "<option value=\"0\">".$lang['al_all_players']."</option>";

				if ($ally_ranks)
				{
					foreach($ally_ranks as $id => $array)
					{
						$lang['r_list'] .= "<option value=\"" . ($id + 1) . "\">" . $array['name'] . "</option>";
					}
				}

				display(parsetemplate(gettemplate('alliance/alliance_circular'), $lang));
			}
		// < ------------------------------------------------------ EDICION DE LOS PERMISOS O LEYES ------------------------------------------------------ >
			if ($mode == 'admin' && $edit == 'rights')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_can_edit_rights)
					header("location:game.". $phpEx . "?page=alliance",2);
				elseif (!empty($_POST['newrangname']))
				{
					$name = mysql_escape_string(strip_tags($_POST['newrangname']));

					$ally_ranks[] = array('name' => $name,
					'mails' => 0,
					'delete' => 0,
					'kick' => 0,
					'bewerbungen' => 0,
					'administrieren' => 0,
					'bewerbungenbearbeiten' => 0,
					'memberlist' => 0,
					'onlinestatus' => 0,
					'rechtehand' => 0
					);

					$ranks = serialize($ally_ranks);

					doquery("UPDATE {{table}} SET `ally_ranks`='" . $ranks . "' WHERE `id`=" . intval($ally['id']), "alliance");

					$goto = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];

					header("Location: " . $goto);
					exit();
				}
				elseif ($_POST['id'] != '' && is_array($_POST['id']))
				{
					$ally_ranks_new = array();

					foreach ($_POST['id'] as $id)
					{
						$name = $ally_ranks[$id]['name'];

						$ally_ranks_new[$id]['name'] = $name;

						if (isset($_POST['u' . $id . 'r0'])) {
						$ally_ranks_new[$id]['delete'] = 1;
						} else {
						$ally_ranks_new[$id]['delete'] = 0;
						}

						if (isset($_POST['u' . $id . 'r1']) && $ally['ally_owner'] == $CurrentUser['id']) {
						$ally_ranks_new[$id]['kick'] = 1;
						} else {
						$ally_ranks_new[$id]['kick'] = 0;
						}

						if (isset($_POST['u' . $id . 'r2'])) {
						$ally_ranks_new[$id]['bewerbungen'] = 1;
						} else {
						$ally_ranks_new[$id]['bewerbungen'] = 0;
						}

						if (isset($_POST['u' . $id . 'r3'])) {
						$ally_ranks_new[$id]['memberlist'] = 1;
						} else {
						$ally_ranks_new[$id]['memberlist'] = 0;
						}

						if (isset($_POST['u' . $id . 'r4'])) {
						$ally_ranks_new[$id]['bewerbungenbearbeiten'] = 1;
						} else {
						$ally_ranks_new[$id]['bewerbungenbearbeiten'] = 0;
						}

						if (isset($_POST['u' . $id . 'r5'])) {
						$ally_ranks_new[$id]['administrieren'] = 1;
						} else {
						$ally_ranks_new[$id]['administrieren'] = 0;
						}

						if (isset($_POST['u' . $id . 'r6'])) {
						$ally_ranks_new[$id]['onlinestatus'] = 1;
						} else {
						$ally_ranks_new[$id]['onlinestatus'] = 0;
						}

						if (isset($_POST['u' . $id . 'r7'])) {
						$ally_ranks_new[$id]['mails'] = 1;
						} else {
						$ally_ranks_new[$id]['mails'] = 0;
						}

						if (isset($_POST['u' . $id . 'r8'])) {
						$ally_ranks_new[$id]['rechtehand'] = 1;
						} else {
						$ally_ranks_new[$id]['rechtehand'] = 0;
						}
					}

					$ranks = serialize($ally_ranks_new);

					doquery("UPDATE {{table}} SET `ally_ranks`='" . $ranks . "' WHERE `id`=" . intval($ally['id']), "alliance");

					$goto = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];

					header("Location: " . $goto);
					exit();

				}
				elseif(isset($d) && isset($ally_ranks[$d]))
				{
					unset($ally_ranks[$d]);

					$ally['ally_rank'] = serialize($ally_ranks);

					doquery("UPDATE {{table}} SET `ally_ranks`='{$ally['ally_rank']}' WHERE `id`=".intval($ally['id'])."", "alliance");
				}

				if (count($ally_ranks) == 0 || $ally_ranks == '')
				{
					$list = "<th>".$lang['al_no_ranks_defined']."</th>";
				}
				else
				{
					$list = parsetemplate(gettemplate('alliance/alliance_admin_laws_head'), $lang);
					$i = 0;
					foreach($ally_ranks as $a => $b)
					{
						if ($ally['ally_owner'] == $CurrentUser['id'])
						{
							$lang['id'] = $a;
							$lang['delete'] = "<a href=\"game.php?page=alliance&mode=admin&edit=rights&d={$a}\"><img src=\"{$dpath}pic/abort.gif\" title=\"Borrar rango\" border=\"0\"></a>";
							$lang['r0'] = $b['name'];
							$lang['a'] = $a;
							$lang['r1'] = "<input type=checkbox name=\"u{$a}r0\"" . (($b['delete'] == 1)?' checked="checked"':'') . ">"; //{$b[1]}
							$lang['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1)?' checked="checked"':'') . ">";
							$lang['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1)?' checked="checked"':'') . ">";
							$lang['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1)?' checked="checked"':'') . ">";
							$lang['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1)?' checked="checked"':'') . ">";
							$lang['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1)?' checked="checked"':'') . ">";
							$lang['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1)?' checked="checked"':'') . ">";
							$lang['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1)?' checked="checked"':'') . ">";
							$lang['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1)?' checked="checked"':'') . ">";

							$list .= parsetemplate(gettemplate('alliance/alliance_admin_laws_row'), $lang);
						}
						else
						{
							$lang['id'] = $a;
							$lang['r0'] = $b['name'];
							$lang['delete'] = "<a href=\"game.php?page=alliance&mode=admin&edit=rights&d={$a}\"><img src=\"{$dpath}pic/abort.gif\" alt=\"{$lang['Delete_range']}\" border=0></a>";
							$lang['a'] = $a;
							$lang['r1'] = "<b>-</b>";
							$lang['r2'] = "<input type=checkbox name=\"u{$a}r1\"" . (($b['kick'] == 1)?' checked="checked"':'') . ">";
							$lang['r3'] = "<input type=checkbox name=\"u{$a}r2\"" . (($b['bewerbungen'] == 1)?' checked="checked"':'') . ">";
							$lang['r4'] = "<input type=checkbox name=\"u{$a}r3\"" . (($b['memberlist'] == 1)?' checked="checked"':'') . ">";
							$lang['r5'] = "<input type=checkbox name=\"u{$a}r4\"" . (($b['bewerbungenbearbeiten'] == 1)?' checked="checked"':'') . ">";
							$lang['r6'] = "<input type=checkbox name=\"u{$a}r5\"" . (($b['administrieren'] == 1)?' checked="checked"':'') . ">";
							$lang['r7'] = "<input type=checkbox name=\"u{$a}r6\"" . (($b['onlinestatus'] == 1)?' checked="checked"':'') . ">";
							$lang['r8'] = "<input type=checkbox name=\"u{$a}r7\"" . (($b['mails'] == 1)?' checked="checked"':'') . ">";
							$lang['r9'] = "<input type=checkbox name=\"u{$a}r8\"" . (($b['rechtehand'] == 1)?' checked="checked"':'') . ">";

							$list .= parsetemplate(gettemplate('alliance/alliance_admin_laws_row'), $lang);
						}
					}

					if (count($ally_ranks) != 0)
						$list .= parsetemplate(gettemplate('alliance/alliance_admin_laws_feet'), $lang);
				}

				$lang['list'] 	= $list;
				$lang['dpath'] 	= $dpath;
				display(parsetemplate(gettemplate('alliance/alliance_admin_laws'), $lang));
			}
		// < ----------------------------------------------------- EDICIONES GENERALES DE LA ALIANZA ----------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'ally')
			{
				if ($t != 1 && $t != 2 && $t != 3)
				{
					$t = 1;
				}

				if ($_POST)
				{
					if (!get_magic_quotes_gpc())
					{
						$_POST['owner_range'] 	= stripslashes($_POST['owner_range']);
						$_POST['web'] 			= stripslashes($_POST['web']);
						$_POST['image'] 		= stripslashes($_POST['image']);
						$_POST['text'] 			= stripslashes($_POST['text']);
					}
				}

				if ($_POST['options'])
				{
					$ally['ally_owner_range'] 		= mysql_escape_string(htmlspecialchars(strip_tags($_POST['owner_range'])));
					$ally['ally_web'] 				= mysql_escape_string(htmlspecialchars(strip_tags($_POST['web'])));
					$ally['ally_image'] 			= mysql_escape_string(htmlspecialchars(strip_tags($_POST['image'])));
					$ally['ally_request_notallow'] 	= intval($_POST['request_notallow']);

					if ($ally['ally_request_notallow'] != 0 && $ally['ally_request_notallow'] != 1)
						exit(header("location:game.". $phpEx . "?page=alliance?mode=admin&edit=ally",2));

					doquery("UPDATE {{table}} SET
					`ally_owner_range`='{$ally['ally_owner_range']}',
					`ally_image`='{$ally['ally_image']}',
					`ally_web`='{$ally['ally_web']}',
					`ally_request_notallow`='{$ally['ally_request_notallow']}'
					WHERE `id`='{$ally['id']}'", "alliance");
				}
				elseif ($_POST['t'])
				{
					if ($t == 3)
					{

						$ally['ally_request'] = mysql_escape_string(strip_tags($_POST['text']));
						doquery("UPDATE {{table}} SET
						`ally_request`='{$ally['ally_request']}'
						WHERE `id`='{$ally['id']}'", "alliance");
						header ("Location: game.php?page=alliance&mode=admin&edit=ally&t=3");
					}
					elseif ($t == 2)
					{
						$ally['ally_text'] = mysql_escape_string(strip_tags($_POST['text']));
						doquery("UPDATE {{table}} SET
						`ally_text`='{$ally['ally_text']}'
						WHERE `id`='{$ally['id']}'", "alliance");
						header ("Location: game.php?page=alliance&mode=admin&edit=ally&t=2");
					}
					else
					{
						$ally['ally_description'] = mysql_escape_string(strip_tags($_POST['text']));

						doquery("UPDATE {{table}} SET
						`ally_description`='" . $ally['ally_description'] . "'
						WHERE `id`='{$ally['id']}'", "alliance");
						header ("Location: game.php?page=alliance&mode=admin&edit=ally&t=1");
					}
				}

				$lang['dpath'] = $dpath;

				if ($t == 3) {
				$lang['request_type'] = $lang['al_request_text'];
				} elseif ($t == 2) {
				$lang['request_type'] = $lang['al_inside_text'];
				} else {
				$lang['request_type'] = $lang['al_outside_text'];
				}

				if ($t == 2)
					$lang['text'] = $ally['ally_text'];
				else
					$lang['text'] = $ally['ally_description'];

				if ($t == 3)
					$lang['text'] = $ally['ally_request'];

				$lang['t'] 							= $t;
				$lang['ally_web'] 					= $ally['ally_web'];
				$lang['ally_image'] 				= $ally['ally_image'];
				$lang['ally_request_notallow_0'] 	= (($ally['ally_request_notallow'] == 1) ? ' SELECTED' : '');
				$lang['ally_request_notallow_1'] 	= (($ally['ally_request_notallow'] == 0) ? ' SELECTED' : '');
				$lang['ally_owner_range'] 			= $ally['ally_owner_range'];
				display(parsetemplate(gettemplate('alliance/alliance_admin'), $lang));
			}
		// < -------------------------------------------------------- EDICION DE LOS MIEMBROS -------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'members')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && $user_admin == false)
					header("location:game.". $phpEx . "?page=alliance",2);

				if (isset($kick))
				{
					if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_can_kick)
						header("location:game.". $phpEx . "?page=alliance",2);

					$u = doquery("SELECT * FROM {{table}} WHERE id='".intval($kick)."' LIMIT 1", 'users', true);

					if ($u['ally_id'] == $ally['id'] && $u['id'] != $ally['ally_owner'])
						doquery("UPDATE {{table}} SET `ally_id`='0', `ally_name`='', `ally_rank_id` = 0 WHERE `id`='".intval($u['id'])."' LIMIT 1;", 'users');
				}
				elseif (isset($_POST['newrang']))
				{
					$q = doquery("SELECT * FROM {{table}} WHERE id='".intval($u)."' LIMIT 1", 'users', true);

					if ((isset($ally_ranks[$_POST['newrang']-1]) || $_POST['newrang'] == 0) && $q['id'] != $ally['ally_owner'])
						doquery("UPDATE {{table}} SET `ally_rank_id`='" . mysql_escape_string(strip_tags($_POST['newrang'])) . "' WHERE `id`='" . intval($id) . "'", 'users');
				}

				if ($sort2)
				{
					$sort1 = intval($_GET['sort1']);
					$sort2 = intval($_GET['sort2']);

					if ($sort1 == 1) {
					$sort = " ORDER BY `username`";
					} elseif ($sort1 == 2) {
					$sort = " ORDER BY `ally_rank_id`";
					} elseif ($sort1 == 3) {
					$sort = " ORDER BY `total_points`";
					} elseif ($sort1 == 4) {
					$sort = " ORDER BY `ally_register_time`";
					} elseif ($sort1 == 5) {
					$sort = " ORDER BY `onlinetime`";
					} else {
					$sort = " ORDER BY `id`";
					}

					if ($sort2 == 1) {
					$sort .= " DESC;";
					} elseif ($sort2 == 2) {
					$sort .= " ASC;";
					}
					$listuser = doquery("SELECT * FROM `{{table}}users` inner join `{{table}}statpoints` on `{{table}}users`.`id`=`{{table}}statpoints`.`id_owner` WHERE ally_id='".intval($CurrentUser['ally_id'])."' AND STAT_type=1 $sort", '');
				}
				else
				{
					$listuser = doquery("SELECT * FROM {{table}} WHERE ally_id='".intval($CurrentUser['ally_id'])."'", 'users');
				}

				$i 				= 0;
				$r				= $lang;
				$s				= $lang;
				$lang['i'] 		= mysql_num_rows($listuser);

				while ($u = mysql_fetch_array($listuser))
				{
					$UserPoints = doquery("SELECT * FROM {{table}} WHERE `stat_type` = '1' AND `stat_code` = '1' AND `id_owner` = '" . intval($u['id']) . "';", 'statpoints', true);

					$i++;
					$u['i'] = $i;

					$u['points'] = "" . pretty_number($UserPoints['total_points']) . "";

					$days = floor(round(time() - $u["onlinetime"]) / 3600 * 24);

					$u["onlinetime"] = str_replace("%s", $days, "%s d");

					if ($ally['ally_owner'] == $u['id'])
						$ally_range = ($ally['ally_owner_range'] == '')?$lang['al_founder_rank_text']:$ally['ally_owner_range'];
					elseif ($u['ally_rank_id'] == 0 || !isset($ally_ranks[$u['ally_rank_id']-1]['name']))
						$ally_range = $lang['al_new_member_rank_text'];
					else
						$ally_range = $ally_ranks[$u['ally_rank_id']-1]['name'];

					if ($ally['ally_owner'] == $u['id'] || $rank == $u['id'])
						$u["acciones"] = '-';
					elseif ($ally_ranks[$CurrentUser['ally_rank_id']-1]['kick'] == 1  &&  $ally_ranks[$CurrentUser['ally_rank_id']-1]['administrieren'] == 1 || $ally['ally_owner'] == $CurrentUser['id'])
						$u["acciones"] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=$u[id]\" onclick=\"javascript:return confirm('�Est�s seguro que deseas expulsar a $u[username]?');\"><img src=\"".$dpath."pic/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=$u[id]\"><img src=\"".$dpath."pic/key.gif\" border=\"0\"></a>";
					elseif ($ally_ranks[$CurrentUser['ally_rank_id']-1]['administrieren'] == 1 )
						$u["acciones"] = "<a href=\"game.php?page=alliance&mode=admin&edit=members&kick=$u[id]\" onclick=\"javascript:return confirm('�Est�s seguro que deseas expulsar a $u[username]?');\"><img src=\"".$dpath."pic/abort.gif\" border=\"0\"></a> <a href=\"game.php?page=alliance&mode=admin&edit=members&rank=$u[id]\"><img src=\"".$dpath."pic/key.gif\" border=\"0\"></a>";
					else
						$u["acciones"] = '-';

					$u["dpath"] = $dpath;

					$u['ally_register_time'] = date("Y-m-d h:i:s", $u['ally_register_time']);

					if ($rank == $u['id'])
					{
						$r['Rank_for'] = str_replace("%s", $u['username'], $lang['Rank_for']);
						$r['options'] .= "<option onclick=\"document.editar_usu_rango.submit();\" value=\"0\">".$lang['al_new_member_rank_text']."</option>";

						if($ally_ranks != null )
						{
							foreach($ally_ranks as $a => $b)
							{
								$r['options'] 	.= "<option onclick=\"document.editar_usu_rango.submit();\" value=\"" . ($a + 1) . "\"";

								if ($u['ally_rank_id']-1 == $a)
								{
									$r['options'] .= ' selected=selected';
								}

								$r['options'] .= ">{$b['name']}</option>";
							}
						}
						$r['id'] = $u['id'];

						$editar_miembros = parsetemplate(gettemplate('alliance/alliance_admin_members_row_edit'), $r);
					}

					if ($rank != $u['id'])
						$u['ally_range'] = $ally_range;
					else
						$u['ally_range'] = $editar_miembros;

					$page_list .= parsetemplate(gettemplate('alliance/alliance_admin_members_row'), $u);

				}
				if ($sort2 == 1) {$s = 2;}
				elseif ($sort2 == 2) {$s = 1;}
				else {$s = 1;}

				if ($i != $ally['ally_members'])
					doquery("UPDATE {{table}} SET `ally_members`='".intval($i)."' WHERE `id`='".intval($ally['id'])."'", 'alliance');

				$lang['memberslist'] = $page_list;
				$lang['s'] = $s;

				display(parsetemplate(gettemplate('alliance/alliance_admin_members_table'), $lang));
			}

		// < -------------------------------------------------------- EDICION DE LAS SOLICITUDES -------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'requests')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_bewerbungen_bearbeiten)
					header("location:game.". $phpEx . "?page=alliance",2);

				if ($_POST['action'] == $lang['al_acept_request'])
				{
					$_POST['text']  = trim ( nl2br ( strip_tags ( $_POST['text'], '<br>' ) ) );

					doquery("UPDATE {{table}} SET `ally_members` = `ally_members` + 1 WHERE id='".intval($ally['id'])."'", 'alliance');

					doquery("UPDATE {{table}} SET
					ally_name='{$ally['ally_name']}',
					ally_request_text='',
					ally_request='0',
					ally_id='{$ally['id']}'
					WHERE id='{$show}'", 'users');

					SendSimpleMessage($show,$CurrentUser['id'],'', 2,$ally['ally_tag'],$lang['al_you_was_acceted'] . $ally['ally_name'], $lang['al_hi_the_alliance'] . $ally['ally_name'] . $lang['al_has_accepted'] . $_POST['text']);

					exit(header('Location:game.php?page=alliance&mode=admin&edit=ally'));
				}
				elseif($_POST['action'] == $lang['al_decline_request'] && $_POST['action'] != '')
				{
					$_POST['text']  = trim ( nl2br ( strip_tags ( $_POST['text'], '<br>' ) ) );

					doquery("UPDATE {{table}} SET ally_request_text='',ally_request='0',ally_id='0' WHERE id='".intval($show)."'", 'users');

					SendSimpleMessage($show,$CurrentUser['id'],'', 2,$ally['ally_tag'],$lang['al_you_was_declined'] . $ally['ally_name'], $lang['al_hi_the_alliance'] . $ally['ally_name'] . $lang['al_has_declined'] . $_POST['text']);

					exit(header('Location:game.php?page=alliance&mode=admin&edit=ally'));
				}

				$i = 0;

				$query = doquery("SELECT id,username,ally_request_text,ally_register_time FROM {{table}} WHERE ally_request='".($ally['id'])."'", 'users');

				while ($r = mysql_fetch_array($query))
				{
					$s = $lang;
					if (isset($show) && $r['id'] == $show)
					{
						$s['username'] 			= $r['username'];
						$s['ally_request_text'] = nl2br($r['ally_request_text']);
						$s['id'] 				= $r['id'];
					}

					$r['time'] 		= date("Y-m-d h:i:s", $r['ally_register_time']);
					$parse['list'] .= parsetemplate(gettemplate('alliance/alliance_admin_request_row'), $r);
					$i++;
				}

				if ($parse['list'] == '')
				{
					$parse['list'] = "<tr><th colspan=2>".$lang['al_no_requests']."</th></tr>";
				}

				if (isset($show) && $show != 0 && $parse['list'] != '')
				{
					$s['Request_from'] 	= str_replace('%s', $s['username'], $lang['al_request_from']);
					$parse['request'] 	= parsetemplate(gettemplate('alliance/alliance_admin_request_form'), $s);
				}
				else
					$parse['request'] = '';

				$parse['ally_tag'] 					= $ally['ally_tag'];
				$parse['There_is_hanging_request'] 	= str_replace('%n', $i, $lang['al_no_request_pending']);

				display(parsetemplate(gettemplate('alliance/alliance_admin_request_table'), $parse));
			}
		// < -------------------------------------------------------- CAMBIAR NOMBRE DE LA ALIANZA -------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'name')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_admin)
					header("location:game.". $phpEx . "?page=alliance",2);

				if ($_POST['nombre'] && !empty($_POST['nombre']))
				{
					$ally['ally_name'] = mysql_escape_string(strip_tags($_POST['nombre']));
					doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `id` = '". intval($CurrentUser['ally_id']) ."';", 'alliance');
					doquery("UPDATE {{table}} SET `ally_name` = '". $ally['ally_name'] ."' WHERE `ally_id` = '". intval($ally['id']) ."';", 'users');
				}

				$parse[caso] 		= $lang['al_name'];
				$parse[caso_titulo]	= $lang['al_new_name'];

				display(parsetemplate(gettemplate('alliance/alliance_admin_rename'), $parse));
			}
		// < ------------------------------------------------------- CAMBIAR ETIQUETA DE LA ALIANZA ------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'tag')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_admin)
					header("location:game.". $phpEx . "?page=alliance",2);

				if ($_POST['etiqueta'] && !empty($_POST['etiqueta']))
					doquery("UPDATE {{table}} SET `ally_tag` = '". mysql_escape_string(strip_tags($_POST['etiqueta'])) ."' WHERE `id` = '". $CurrentUser['ally_id'] ."';", 'alliance');


				$parse[caso] 		= $lang['al_tag'];
				$parse[caso_titulo]	= $lang['al_new_tag'];

				display(parsetemplate(gettemplate('alliance/alliance_admin_rename'), $parse));
			}
		// < ------------------------------------------------------------- SALIR DE LA ALIANZA ------------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'exit')
			{
				if ($ally['ally_owner'] != $CurrentUser['id'] && !$user_can_exit_alliance)
					header("location:game.". $phpEx . "?page=alliance",2);

				$BorrarAlianza = doquery("SELECT id FROM {{table}} WHERE `ally_id`='".intval($ally['id'])."'",'users');

				while ($v = mysql_fetch_array($BorrarAlianza))
				{
					doquery("UPDATE {{table}} SET `ally_name` = '', `ally_id`='0' WHERE `id`='".intval($v['id'])."'", 'users');
				}

				doquery("DELETE FROM {{table}} WHERE id='".intval($ally['id'])."' LIMIT 1", "alliance");

				exit(header("location:game.". $phpEx . "?page=alliance",2));
			}
		// < ----------------------------------------------------------- TRANSFERIR LA ALIANZA ----------------------------------------------------------- >
			if ($mode == 'admin' && $edit == 'transfer')
			{
				if (isset($_POST['newleader']))
				{
					doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`=".intval($CurrentUser['id'])."", 'users');
					doquery("UPDATE {{table}} SET `ally_owner`='" . mysql_escape_string(strip_tags($_POST['newleader'])) . "' WHERE `id`=".intval($CurrentUser['ally_id'])."", 'alliance');
					doquery("UPDATE {{table}} SET `ally_rank_id`='0' WHERE `id`='" . mysql_escape_string(strip_tags($_POST['newleader'])) . "' ", 'users');
					exit(header("location:game.". $phpEx . "?page=alliance",2));
				}
				if ($ally['ally_owner'] != $CurrentUser['id'])
					header("location:game.". $phpEx . "?page=alliance",2);
				else
				{
					$listuser 		= doquery("SELECT * FROM {{table}} WHERE ally_id='".intval($CurrentUser['ally_id'])."'", 'users');
					$righthand		= $lang;

					while ($u = mysql_fetch_array($listuser))
					{
						if ($ally['ally_owner'] != $u['id'])
						{
							if ($u['ally_rank_id'] != 0 )
							{
								if ($ally_ranks[$u['ally_rank_id']-1]['rechtehand'] == 1)
								{
									$righthand['righthand'] .= "\n<option value=\"" . $u['id'] . "\"";
									$righthand['righthand'] .= ">";
									$righthand['righthand'] .= "".$u['username'];
									$righthand['righthand'] .= "&nbsp;[".$ally_ranks[$u['ally_rank_id']-1]['name'];
									$righthand['righthand'] .= "]&nbsp;&nbsp;</option>";
								}
							}
						}
						$righthand["dpath"] = $dpath;
					}

					$page_list 	   .= parsetemplate(gettemplate('alliance/alliance_admin_transfer_row'), $righthand);
					$parse['s'] 	= $s;
					$parse['list'] 	= $page_list;

					display(parsetemplate(gettemplate('alliance/alliance_admin_transfer'), $parse));
				}
			}
		// < -------------------------------------------------------- PARTE DEFAULT DE LA ALIANZA -------------------------------------------------------- >
			{
				// IMAGEN
				if ($ally['ally_ranks'] != '')
					$ally['ally_ranks'] = "<tr><td colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>";

				//RANGOS
				if ($ally['ally_owner'] == $CurrentUser['id'])
					$range = ($ally['ally_owner_range'] != '') ? $ally['ally_owner_range'] : $lang['al_founder_rank_text'];
				elseif ($CurrentUser['ally_rank_id'] != 0 && isset($ally_ranks[$CurrentUser['ally_rank_id']-1]['name']))
					$range = $ally_ranks[$CurrentUser['ally_rank_id']-1]['name'];
				else
					$range = $lang['al_new_member_rank_text'];

				// LISTA DE MIEMBROS
				if ($ally['ally_owner'] == $CurrentUser['id'] || $ally_ranks[$CurrentUser['ally_rank_id']-1]['memberlist'] != 0)
					$lang['members_list'] = " (<a href=\"game.php?page=alliance&mode=memberslist\">".$lang['al_user_list']."</a>)";
				else
					$lang['members_list'] = '';

				// ADMINISTRAR ALIANZA
				if ($ally['ally_owner'] == $CurrentUser['id'] || $ally_ranks[$CurrentUser['ally_rank_id']-1]['administrieren'] != 0)
					$lang['alliance_admin'] = " (<a href=\"game.php?page=alliance&mode=admin&edit=ally\">".$lang['al_manage_alliance']."</a>)";
				else
					$lang['alliance_admin'] = '';

				// CORREO CIRCULAR
				if ($ally['ally_owner'] == $CurrentUser['id'] || $ally_ranks[$CurrentUser['ally_rank_id']-1]['mails'] != 0)
					$lang['send_circular_mail'] = "<tr><th>".$lang['al_circular_message']."</th><th><a href=\"game.php?page=alliance&mode=circular\">".$lang['al_send_circular_message']."</a></th></tr>";
				else
					$lang['send_circular_mail'] = '';

				// SOLICITUDES
				$request_count = mysql_num_rows(doquery("SELECT id FROM {{table}} WHERE ally_request='".intval($ally['id'])."'", 'users'));

				if ($request_count != 0)
				{
					if ($ally['ally_owner'] == $CurrentUser['id'] || $ally_ranks[$CurrentUser['ally_rank_id']-1]['bewerbungen'] != 0)
						$lang['requests'] = "<tr><th>".$lang['al_requests']."</th><th><a href=\"game.php?page=alliance&mode=admin&edit=requests\">{$request_count} ".$lang['al_new_requests']."</a></th></tr>";
				}
				// SALIR DE LA ALIANZA
				if ($ally['ally_owner'] != $CurrentUser['id'])
				{
					$lang['ally_owner'] .= "<table width=\"519\">";
					$lang['ally_owner'] .= "<tr><td class=\"c\">".$lang['al_leave_alliance']."</td>";
					$lang['ally_owner'] .= "</tr><tr>";
					$lang['ally_owner'] .= "<th><input type=\"button\" onclick=\"javascript:location.href='game.php?page=alliance&mode=exit';\" value=\"".$lang['al_continue']."\"/></th>";
					$lang['ally_owner'] .= "</tr></table>";
				}
				else
					$lang['ally_owner'] .= '';

				// IMAGEN DEL LOGO
				$lang['ally_image'] 		= ($ally['ally_image'] != '')?"<tr><th colspan=2><img src=\"{$ally['ally_image']}\"></td></tr>":'';
				$lang['range'] 				= $range;

				$lang['ally_description'] 	= nl2br($this->bbcode($ally['ally_description']));
				$lang['ally_text'] 			= nl2br($this->bbcode($ally['ally_text']));

				if($ally['ally_web'] != '')
					$lang['ally_web'] 		= str_replace ( "http://" , "" , $ally['ally_web'] );
				else
					$lang['ally_web']		= "-";

				$lang['ally_tag'] 			= $ally['ally_tag'];
				$lang['ally_members'] 		= $ally['ally_members'];
				$lang['ally_name'] 			= $ally['ally_name'];

				display(parsetemplate(gettemplate('alliance/alliance_frontpage'), $lang));
			}
		}
	}
}
?>