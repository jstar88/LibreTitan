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

	function InsertJavaScriptChronoAppletJstar2 ($Type, $nome,$numero,$tempo, $Init)
	{
	global $lang;
	
	
		if ($Init == true)
		{
			$JavaString  = "<script type=\"text/javascript\">\n";
			$JavaString .= "function t". $Type . "() {\n";
		
		
	  	$JavaString .= "  if ( hs".$Type . " == 0 ) {\n";
	  	$JavaString .= "    insertInList".$Type."();\n";
	  	$JavaString .= "    hs".$Type." = 1;\n";
	    $JavaString .= "  }\n";
    
    	$JavaString .= "  v".$Type." = new Date();\n";
			$JavaString .= "  var timer". $Type . " = document.getElementById(\"bxx". $Type . "\");\n";
			$JavaString .= "  var currentindex". $Type . " = document.getElementById(\"currentindex". $Type . "\");\n";
      $JavaString .= "  var currenttime". $Type . " = document.getElementById(\"currenttime". $Type . "\");\n";
      $JavaString .= "  n".$Type." = new Date();\n";
			$JavaString .= "  ss". $Type . " = tempo". $Type . "[indice".$Type."];\n";
			$JavaString .= "  ss". $Type . " = ss". $Type . " - Math.round((n".$Type.".getTime() - v".$Type.".getTime()) / 1000.);\n";
			$JavaString .= "  m". $Type . " = 0;\n";
			$JavaString .= "  h". $Type . " = 0;\n";
			$JavaString .= "  d". $Type . " = 0;\n";
			$JavaString .= "  if (ss". $Type . " < 0) {\n";
			$JavaString .= "     numero".$Type . "[indice".$Type . "]--;\n";  //  cambio nave
      $JavaString .= "      if(numero".$Type . "[indice".$Type . "] <= 0){\n";
      $JavaString .= "        indice".$Type . "++;\n";  //cambio tipo nave
      $JavaString .= "        if(indice".$Type . ">(numero".$Type . ".length-1))\n";
      $JavaString .= "          stop".$Type . "=true;\n";
      $JavaString .= "      }\n";
	  	$JavaString .= "      insertInList".$Type."();\n ";
	  	$JavaString .= "  }\n";
			$JavaString .= "  if(!stop".$Type.") {\n";
      $JavaString .= "	  currentindex". $Type . ".value = indice".$Type .";\n";
      $JavaString .= "	  currenttime". $Type . ".value = tempo".$Type."[indice".$Type."];\n";
			$JavaString .= "    if (ss". $Type . " > 59) {\n";
			$JavaString .= "	    m". $Type . " = Math.floor(ss". $Type . " / 60);\n";
			$JavaString .= "		  ss". $Type . " = ss". $Type . " - m". $Type . " * 60;\n";
			$JavaString .= "    }\n";
			$JavaString .= "	  if (m". $Type . " > 59) {\n";
			$JavaString .= "		  h". $Type . " = Math.floor(m". $Type . " / 60);\n";
			$JavaString .= "		  m". $Type . " = m". $Type . " - h". $Type . " * 60;\n";
			$JavaString .= "    }\n";
      $JavaString .= "	  if (h". $Type . " > 23) {\n";
			$JavaString .= "		  d". $Type . " = Math.floor(h". $Type . " / 24);\n";
			$JavaString .= "		  h". $Type . " = h". $Type . " - d". $Type . " * 24;\n";
			$JavaString .= "    }\n";
			$JavaString .= "	  if (ss". $Type . " < 10) {\n";
			$JavaString .= "	  	ss". $Type . " = \"0\" + ss". $Type . ";\n";
			$JavaString .= "    	}\n";
			$JavaString .= "	  if (m". $Type . " < 10) {\n";
			$JavaString .= "	    m". $Type . " = \"0\" + m". $Type . ";\n";
			$JavaString .= "	    }\n";
			$JavaString .= "    if(d". $Type . ">0){\n";
			$JavaString .= "	    timer". $Type . ".innerHTML =nome".$Type."[indice".$Type."] +\" \"+ d". $Type . " + \"g  \" + h". $Type . " + \"h \" + m". $Type . " + \"m \" + ss". $Type . " + \"s\";\n";
			$JavaString .= "      }\n";
			$JavaString .= "    else if (h". $Type . ">0) {\n";
			$JavaString .= "	    timer". $Type . ".innerHTML = nome".$Type."[indice".$Type."] +\" \"+ h". $Type . " + \"h \" + m". $Type . " + \"m \" + ss". $Type . "+ \"s\";\n";
			$JavaString .= "      }\n";
			$JavaString .= "    else if (m". $Type . ">0) {\n";
			$JavaString .= "	    timer". $Type . ".innerHTML = nome".$Type."[indice".$Type."] +\" \"+ m". $Type . " + \"m \" + ss". $Type . "+ \"s\";\n";
			$JavaString .= "      }\n";
			$JavaString .= "    else {\n";
			$JavaString .= "	    timer". $Type . ".innerHTML = nome".$Type."[indice".$Type."] +\" \"+ ss". $Type . "+ \"s\";\n";
			$JavaString .= "      }\n";
			$JavaString .= "    tempo".$Type."[indice".$Type."] --;\n";
      $JavaString .= "  }\n";
      $JavaString .= "  else{ ";
      $JavaString .= "    timer". $Type . ".innerHTML =\"".$lang['bd_completed']."\";\n"; 
			 $JavaString.= "  }\n";
			
			$JavaString .= "  window.setTimeout(\"t". $Type . "();\", 999);\n";
			$JavaString .= "}\n";
			
			
			
			
		  $JavaString .="	function insertInList".$Type."() { \n";
      $JavaString .="	  while (document.form".$Type.".list".$Type.".length > 0) {\n";
		  $JavaString .="      document.form".$Type.".list".$Type.".options[document.form".$Type.".list".$Type.".length-1] = null;\n";
      $JavaString .="    }\n";
      $JavaString .="	  if ( indice".$Type . " > nome".$Type . ".length - 1 ) {\n";
      $JavaString .="	    document.form".$Type.".list".$Type.".options[document.form".$Type.".list".$Type.".length] = new Option(\"complete".$Type."\");\n";
      $JavaString .="	  }";
      $JavaString .="	  for ( iv = indice".$Type . "; iv <= tempo".$Type . ".length - 1; iv++ ) {\n";
      $JavaString .="		  if ( numero".$Type . "[iv] < 1 ) {\n";
      $JavaString .="		  	 ae = \" \";\n";
      $JavaString .="		  } else {\n";
      $JavaString .="			   ae = \" \";\n";
      $JavaString .="		  }\n";
      $JavaString .="		  if ( iv == indice".$Type . " ) {\n";
      $JavaString .="		     act = \" ".$lang['bd_operating']."\";\n";
      $JavaString .="		 } else {\n";
      $JavaString .="			  act = \"\";\n";
      $JavaString .="		 }\n";
      $JavaString .="     document.form".$Type.".list".$Type.".options[document.form".$Type.".list".$Type.".length] = new Option( numero".$Type . "[iv] + ae + \" \" + nome".$Type . "[iv] + \" \" + act, iv + 1 );\n";
      $JavaString .="	 }\n";
      $JavaString .="}\n";
      $JavaString .="</script>\n";
      }
		else
		{
			$JavaString  = "<script type=\"text/javascript\">\n";
			$JavaString .= "nome". $Type . " = new Array(". $nome .");\n";
			$JavaString .= "numero". $Type . " =new Array( ". $numero .");\n";
			$JavaString .= "tempo". $Type . " =new Array( ". $tempo .");\n";
			$JavaString .= "indice".$Type . "=0;\n";
			$JavaString .= "stop".$Type . "=false;\n";  
			$JavaString .= "complete".$Type . "=\"complete\";\n"; 
			$JavaString .= "hs". $Type . "=0;\n"; 
			$JavaString .= "t". $Type . "();\n"; 
			$JavaString .= "</script>\n";
		}

		return $JavaString;
	}
?>




