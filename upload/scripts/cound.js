function tfs1() {
currentDate = new Date();
var elemento = document.getElementById('elemento');
targetDate = new Date();
secondi = inizio;
secondi = secondi - Math.round((currentDate.getTime() - targetDate.getTime()) / 1000.);
minuti = 0;
ore = 0;
if (secondi < 0) {
 elemento.innerHTML = "-";
} else {
 if (secondi > 59) {
 minuti = Math.floor(secondi / 60);
 secondi = secondi - minuti * 60;
 }
 if (minuti > 59) {
 ore = Math.floor(minuti / 60);
 minuti = minuti - ore * 60;
 }
 if (secondi < 10) {
 secondi = "0" + secondi;
 }
 if (minuti < 10) {
 minuti = "0" + minuti;
 }
 elemento.innerHTML = ore + ":" + minuti + ":" + secondi;
}
inizio = inizio - 1;
window.setTimeout("tfs1();", 999);
} 