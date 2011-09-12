// Created by Jstar
function showhide(targetElementId,thisId,newName,oldName){
var targetElement = document.getElementById(targetElementId);
var thisElement = document.getElementById(thisId);
   if(targetElement.style.display!='none'){
     hide(targetElement,thisElement,oldName);
  }
  else{
     show(targetElement,thisElement,newName);
  }
  
}
function showhidesimple(targetElementId){
var targetElement = document.getElementById(targetElementId);
   if(targetElement.style.display!='none'){
     hidesimple(targetElement);
  }
  else{
     showsimple(targetElement);
  }
  
} 
function hide(targetElement,thisElement,oldName){
   targetElement.style.display='none';
   thisElement.value=oldName;
}
function hidesimple(targetElement){
   targetElement.style.display='none';
}
function show(targetElement,thisElement,newName){
    targetElement.style.display='block';
    thisElement.value=newName;
}
function showsimple(targetElement){
    targetElement.style.display='block';
}
function hide2(targetElementId,thisId,thisId2,oldName,oldName2){
    var targetElement = document.getElementById(targetElementId);
    var thisElement = document.getElementById(thisId);
    var thisElement2 = document.getElementById(thisId2);
    
     targetElement.style.display='none';
     thisElement.value=oldName;
     thisElement2.value=oldName2;
}

function showhidePaste(startTxtId,targetTxtId,targetElementId,thisId,newName,oldName){
    showhide(targetElementId,thisId,newName,oldName);
    var targetElement = document.getElementById(targetElementId);
     if(targetElement.style.display!='none'){
        var startTxt = document.getElementById(startTxtId);
        var targetTxt= document.getElementById(targetTxtId);
         targetTxt.value="[quote]"+startTxt.innerHTML+"[/quote]";     //effettuare il parsing html->bbcode
     }
}
//-----------------------AJAX----------------------------------
// funzione per assegnare l'oggetto XMLHttpRequest
 var objHTTP;
function assegnaXMLHttpRequest() {
var XHR = null;
//nome browser 
var browserUtente = navigator.userAgent.toUpperCase();
// browser standard 
 if(typeof(XMLHttpRequest) === "function" || typeof(XMLHttpRequest) === "object")
  objHTTP = new XMLHttpRequest();

 // Internet Explorer 4
 else if(
  window.ActiveXObject &&
  browserUtente.indexOf("MSIE 4") < 0
 ) { 
  // Internet Explorer 6
  if(browserUtente.indexOf("MSIE 5") < 0)
   objHTTP = new ActiveXObject("Msxml2.XMLHTTP");
  // Internet Explorer 5
  else
   objHTTP = new ActiveXObject("Microsoft.XMLHTTP");
 }
} 
function sendMessage(parametri){
 assegnaXMLHttpRequest();
  if(!objHTTP)
     alert("Update browser to use AJAX!");
 
  var url="http://"+location.host+"/game.php?page=messaggio_rapido";
  //alert(url);
  objHTTP.onreadystatechange = handleResponse;
  objHTTP.open("POST", url, true);
  objHTTP.setRequestHeader("Content-Type","application/x-www-form-urlencoded; charset=UTF-8");
  objHTTP.setRequestHeader('Content-length',parametri.length);
  objHTTP.setRequestHeader('Connection', 'close');
  objHTTP.send(parametri);
  
}
function handleResponse(){
  if(objHTTP.readyState == 4){
    if(objHTTP.status == 200){
      //alert(objHTTP.responseText);
    }else{
      alert("Niente da fare, AJAX non funziona :(");
    }
  }
}


