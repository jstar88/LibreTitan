<?php  

define('INSIDE'  , true);
define('INSTALL' , false);
define('IN_ADMIN', true);
 
  $contatore=0;

  function add(){
  $contatore++;
 }
 
  function remove(){
   $contatore--;
 }
 
 function getCount(){
    return  $contatore;
 }



?>