<div style="top: 10px;" id="content"><script language="JavaScript">
function galaxy_submit(value) {
	document.getElementById('auto').name = value;
	document.getElementById('galaxy_form').submit();
}
function fenster(target_url,win_name) {
	var new_win = window.open(target_url,win_name,'resizable=yes,scrollbars=yes,menubar=no,toolbar=no,width=640,height=480,top=0,left=0');
	new_win.focus();
}
function universe_submit(value) {
 document.getElementById('auto').name = value;
 document.getElementById('galaxy_form').submit();
}
</script><script language="JavaScript" src="scripts/tw-sack.js"></script><script type="text/javascript">
var ajax = new sack();
var strInfo = ""
function whenResponse () {
	retVals = this.response.split("|");
	Message = retVals[0];
	Infos = retVals[1];
	retVals = Infos.split(" ");
	UsedSlots = retVals[0];
	SpyProbes = retVals[1];
	Recyclers = retVals[2];
	Missiles = retVals[3];
	retVals = Message.split(";");
	CmdCode = retVals[0];
	strInfo = retVals[1];
	addToTable("done", "success");
	changeSlots( UsedSlots );
	setShips("probes", SpyProbes );
	setShips("recyclers", Recyclers );
	setShips("missiles", Missiles );
}
function doit (order, universe, galaxy, system, planet, planettype, shipcount) {
	ajax.requestFile = "FleetAjax.php?action=send";
	ajax.runResponse = whenResponse;
	ajax.execute = true;
	ajax.setVar("thisuniverse", <?php echo $this->get(universe); ?>);
	ajax.setVar("thisgalaxy", <?php echo $this->get(galaxy); ?>);
	ajax.setVar("thissystem", <?php echo $this->get(system); ?>);
	ajax.setVar("thisplanet", <?php echo $this->get(planet); ?>);
	ajax.setVar("thisplanettype", <?php echo $this->get(planet_type); ?>);
	ajax.setVar("mission", order);
	ajax.setVar("universe", universe);
	ajax.setVar("galaxy", galaxy);
	ajax.setVar("system", system);
	ajax.setVar("planet", planet);
	ajax.setVar("planettype", planettype);
	if (order == 6)
		ajax.setVar("ship210", shipcount);
	if (order == 7) {
		ajax.setVar("ship208", 1);
		ajax.setVar("ship203", 2);
	}
	if (order == 8)
		ajax.setVar("ship209", shipcount);
	ajax.runAJAX();
}
function addToTable(strDataResult, strClass) {
	var e = document.getElementById('fleetstatusrow');
	var e2 = document.getElementById('fleetstatustable');
	e.style.display = '';
	if(e2.rows.length > 2) {
		e2.deleteRow(2);
	}
	var row = e2.insertRow(0);
	var td1 = document.createElement("td");
	var td1text = document.createTextNode(strInfo);
	td1.appendChild(td1text);
	var td2 = document.createElement("td");
	var span = document.createElement("span");
	var spantext = document.createTextNode(strDataResult);
	var spanclass = document.createAttribute("class");
	spanclass.nodeValue = strClass;
	span.setAttributeNode(spanclass);
	span.appendChild(spantext);
	td2.appendChild(span);
	row.appendChild(td1);
	row.appendChild(td2);
}
function changeSlots(slotsInUse) {
	var e = document.getElementById('slots');
	e.innerHTML = slotsInUse;
}
function setShips(ship, count) {
	var e = document.getElementById(ship);
	e.innerHTML = count;
}
</script><body onUnload=""><form action="game.php?page=galaxy&mode=1" method="post" id="galaxy_form"><input type="hidden" id="auto" value="dr" ><table border="1" class="header"><tr class="header"><td class="header"><table class="header"><tr class="header"><td class="c" colspan="3"><?php echo $this->get(gl_galaxy); ?></td></tr><tr class="header"><td class="l"><input type="button" name="galaxyLeft" value="&lt;-" onClick="galaxy_submit('galaxyLeft')"></td><td class="l"><input type="text" name="galaxy" value="<?php echo $this->get(galaxy); ?>" size="5" maxlength="3" tabindex="1"></td><td class="l"><input type="button" name="galaxyRight" value="-&gt;" onClick="galaxy_submit('galaxyRight')"></td></tr></table></td><td class="header"><table class="header"><tr class="header"><td class="c" colspan="3"><?php echo $this->get(gl_solar_system); ?></td></tr><tr class="header"><td class="l"><input type="button" name="systemLeft" value="&lt;-" onClick="galaxy_submit('systemLeft')"></td><td class="l"><input type="text" name="system" value="<?php echo $this->get(system); ?>" size="5" maxlength="3" tabindex="2"></td><td class="l"><input type="button" name="systemRight" value="-&gt;" onClick="galaxy_submit('systemRight')"></td></tr></table></td></tr><tr class="header"><td class="header" style="background-color:transparent;border:0px;" colspan="2" align="center"><input type="submit" value="<?php echo $this->get(gl_show); ?>"></td></tr></table></form><table width="569"><tr><td class="c" colspan="8"><?php echo $this->get(gl_solar_system); ?><?php echo $this->get(galaxy); ?>:<?php echo $this->get(system); ?></td></tr><tr><td class="c"><?php echo $this->get(gl_pos); ?></td><td class="c"><?php echo $this->get(gl_planet); ?></td><td class="c"><?php echo $this->get(gl_name_activity); ?></td><td class="c"><?php echo $this->get(gl_moon); ?></td><td class="c"><?php echo $this->get(gl_debris); ?></td><td class="c"><?php echo $this->get(gl_player_estate); ?></td><td class="c"><?php echo $this->get(gl_alliance); ?></td><td class="c"><?php echo $this->get(gl_actions); ?></td></tr><?php echo $this->get(galaxyrows); ?><tr><th width="30">16</th><th colspan="7"><a href="game.php?page=fleet&universe=<?php echo $this->get(universe); ?>&amp;galaxy=<?php echo $this->get(galaxy); ?>&amp;system=<?php echo $this->get(system); ?>&amp;planet=16&amp;planettype=1&amp;target_mission=15"><?php echo $this->get(gl_out_space); ?></a></th></tr><tr><td class=c colspan="6">( <?php echo $this->get(planetcount); ?> )</td><td class=c colspan="2"><a href="#" style="cursor: pointer;" onmouseover='return overlib("<table width=240><tr><td class=c colspan=2><?php echo $this->get(gl_legend); ?></td></tr><tr><td width=220><?php echo $this->get(gl_strong_player); ?></td><td><span class=strong><?php echo $this->get(gl_s); ?></span></td></tr><tr><td width=220><?php echo $this->get(gl_week_player); ?></td><td><span class=noob><?php echo $this->get(gl_w); ?></span></td></tr><tr><td width=220><?php echo $this->get(gl_vacation); ?></td><td><span class=vacation><?php echo $this->get(gl_v); ?></span></td></tr><tr><td width=220><?php echo $this->get(gl_banned); ?></td><td><span class=banned><?php echo $this->get(gl_b); ?></span></td></tr><tr><td width=220><?php echo $this->get(gl_inactive_seven); ?></td><td><span class=inactive><?php echo $this->get(gl_i); ?></span></td></tr><tr><td width=220><?php echo $this->get(gl_inactive_twentyeight); ?></td><td><span class=longinactive><?php echo $this->get(gl_I); ?></span></td></tr></table>", STICKY, MOUSEOFF, DELAY, 750, CENTER, OFFSETX, -150, OFFSETY, -150 );' onmouseout='return nd();'><?php echo $this->get(gl_legend); ?></a></td></tr><tr><td class=c colspan="3"><span id="missiles"><?php echo $this->get(currentmip); ?></span><?php echo $this->get(gl_avaible_missiles); ?></td><td class=c colspan="3"><span id="slots"><?php echo $this->get(maxfleetcount); ?></span>/<?php echo $this->get(fleetmax); ?><?php echo $this->get(gl_fleets); ?></td><td class=c colspan="2"><span id="recyclers"><?php echo $this->get(recyclers); ?></span><?php echo $this->get(gl_avaible_recyclers); ?><br><span id="probes"><?php echo $this->get(spyprobes); ?></span><?php echo $this->get(gl_avaible_spyprobes); ?></td></tr><tr style="display: none;" id="fleetstatusrow"><th class=c colspan="8"><table style="font-weight: bold" width="100%" id="fleetstatustable"><!-- will be filled with content later on while processing ajax replys --></table></th></tr></table></body> 