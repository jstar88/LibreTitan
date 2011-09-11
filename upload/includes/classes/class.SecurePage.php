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

 // This class is originally developed by "Bendikt Martin Myklebust" this is updated by "Rakesh Chandel".
 //To Secure Global varaible values consisting in array while posting from GET,Session, and POST.


class SecureSqlInjection
{
	function specificData($val)
	{
		$val = htmlspecialchars(stripslashes(trim($val)));
		$val = str_ireplace("script", "blocked", $val);
		$val = mysql_escape_string($val);
		return $val;
	}

	function secureSuperGlobalGET(&$value, $key)
	{
		if(!is_array($_GET[$key]))
		{
			$_GET[$key] = htmlspecialchars(stripslashes($_GET[$key]));
			$_GET[$key] = str_ireplace("script", "blocked", $_GET[$key]);
			$_GET[$key] = mysql_escape_string($_GET[$key]);
		}
		else
		{
			$c=0;
			foreach($_GET[$key] as $val)
			{
				$_GET[$key][$c] = mysql_escape_string($_GET[$key][$c]);
				$c++;
			}
		}

		return $_GET[$key];
	}

	function secureSuperGlobalPOST(&$value, $key)
	{
		if(!is_array($_POST[$key]))
		{
			$_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
			$_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
			$_POST[$key] = mysql_escape_string($_POST[$key]);
		}
		else
		{
			$c=0;
			foreach($_POST[$key] as $val)
			{
				if (!is_array($_POST[$key][$c]))
				{
					$_POST[$key][$c] = mysql_escape_string($_POST[$key][$c]);
				}
				else
				{
					$_POST[$key][$c] = array_map("mysql_escape_string",$_POST[$key][$c]);
				}
				$c++;
			}
		}
		return $_POST[$key];
	}

	function secureGlobals()
	{

		array_walk($_GET, array($this, 'secureSuperGlobalGET'));
		array_walk($_POST, array($this, 'secureSuperGlobalPOST'));
	}
}

 ?>