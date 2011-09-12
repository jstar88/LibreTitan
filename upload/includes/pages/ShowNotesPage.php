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

function ShowNotesPage($CurrentUser)
{
	global $lang;

	$parse 	= $lang;
	$a 		= intval($_GET['a']);
	$n 		= intval($_GET['n']);

	if($_POST["s"] == 1 || $_POST["s"] == 2)
	{
		$time 		= time();
		$priority 	= intval($_POST["u"]);
		$title 		= ($_POST["title"]) ? mysql_escape_string(strip_tags($_POST["title"])) : "Sin t&iacute;tulo";
		$text 		= ($_POST["text"]) ? mysql_escape_string(strip_tags($_POST["text"])) : "Sin texto";

		if($_POST["s"] ==1)
		{
			doquery("INSERT INTO {{table}} SET owner=".intval($CurrentUser[id]).", time=$time, priority=$priority, title='$title', text='$text'","notes");
			header("location:game.php?page=notes");
		}
		elseif($_POST["s"] == 2)
		{
			$id = intval($_POST["n"]);
			$note_query = doquery("SELECT * FROM {{table}} WHERE id=".intval($id)." AND owner=".intval($CurrentUser[id])."","notes");

			if(!$note_query)
				header("location:game.php?page=notes");

			doquery("UPDATE {{table}} SET time=$time, priority=$priority, title='$title', text='$text' WHERE id=".intval($id)."","notes");
			header("location:game.php?page=notes");
		}
	}
	elseif($_POST)
	{
		foreach($_POST as $a => $b)
		{
			if(preg_match("/delmes/i",$a) && $b == "y")
			{
				$id = str_replace("delmes","",$a);
				$note_query = doquery("SELECT * FROM {{table}} WHERE id=".intval($id)." AND owner=".intval($CurrentUser[id])."","notes");

				if($note_query)
				{
					$deleted++;
					doquery("DELETE FROM {{table}} WHERE `id`=".intval($id).";","notes");
				}
			}
		}

		if($deleted)
			header("location:game.php?page=notes");
		else
			header("Location:game.php?page=notes");
	}
	else
	{
		if($_GET["a"] == 1)
		{
			$parse['c_Options'] = "<option value=2 selected=selected>".$lang['nt_important']."</option>
			<option value=1>".$lang['nt_normal']."</option>
			<option value=0>".$lang['nt_unimportant']."</option>";
			$parse['TITLE'] 	= $lang['nt_create_note'];
			$parse[inputs]  	= "<input type=hidden name=s value=1>";

			display(parsetemplate(gettemplate('notes/notes_form'), $parse), false, '', false, false);

		}
		elseif($_GET["a"] == 2)
		{
			$note = doquery("SELECT * FROM {{table}} WHERE owner=".intval($CurrentUser[id])." AND id=".intval($n)."",'notes',true);

			if(!$note)
				header("location:game.php?page=notes");

			$SELECTED[$note['priority']] = ' selected="selected"';

			$parse['c_Options'] = "<option value=2{$SELECTED[2]}>".$lang['nt_important']."</option>
			<option value=1{$SELECTED[1]}>".$lang['nt_normal']."</option>
			<option value=0{$SELECTED[0]}>".$lang['nt_unimportant']."</option>";

			$parse['TITLE'] 	= $lang['nt_edit_note'];
			$parse['inputs'] 	= '<input type=hidden name=s value=2><input type=hidden name=n value='.$note['id'].'>';
			$parse[asunto]		= $note[title];
			$parse[texto]		= $note[text];

			display(parsetemplate(gettemplate('notes/notes_form'), $parse), false, '', false, false);

		}
		else
		{
			$notes_query = doquery("SELECT * FROM {{table}} WHERE owner=".intval($CurrentUser[id])." ORDER BY time DESC",'notes');

			$count = 0;

			while($note = mysql_fetch_array($notes_query))
			{
				$count++;

				if($note["priority"] == 0){ $parse['NOTE_COLOR'] = "lime";}
				elseif($note["priority"] == 1){ $parse['NOTE_COLOR'] = "yellow";}
				elseif($note["priority"] == 2){ $parse['NOTE_COLOR'] = "red";}

				$parse['NOTE_ID'] 		= $note['id'];
				$parse['NOTE_TIME'] 	= date("Y-m-d h:i:s",$note["time"]);
				$parse['NOTE_TITLE'] 	= $note['title'];
				$parse['NOTE_TEXT'] 	= strlen($note['text']);

				$list .= parsetemplate(gettemplate('notes/notes_body_entry'), $parse);

			}

			if($count == 0)
			{
				$list .= "<tr><th colspan=4>".$lang['nt_you_dont_have_notes']."</th>\n";
			}

			$parse['BODY_LIST'] = $list;

			display(parsetemplate(gettemplate('notes/notes_body'), $parse), false, '', false, false);
		}
	}
}
?>