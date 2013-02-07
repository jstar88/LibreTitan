<script type="text/javascript" >
function calcul() {
	var Metal = document.forms['trader'].elements['metal'].value;
	var Deuterium = document.forms['trader'].elements['deut'].value;

	Metal = Metal * {mod_ma_res_a};
	Deuterium = Deuterium * {mod_ma_res_b};

	var Cristal = Metal + Deuterium;
    var max={max_resource};
    Cristal = Math.min(Cristal,max);
	document.getElementById("crystal").innerHTML=Math.round(Cristal);

	if (isNaN(document.forms['trader'].elements['metal'].value)) {
		document.getElementById("crystal").innerHTML="Solo numeri";
	}
	if (isNaN(document.forms['trader'].elements['deut'].value)) {
		document.getElementById("crystal").innerHTML="Solo numeri";
	}
}
</script>
<br />
<div id="content">
    <form id="trader" action="" method="post">
    <input type="hidden" name="ress" value="crystal">
    <table width="569">
    <tr>
        <td class="c" colspan="5"><b>{tr_sell_crystal}</b></td>
    </tr><tr>
        <th>{tr_resource}</th>
        <th>{tr_amount}</th>
        <th>{tr_quota_exchange}</th>
    </tr><tr>
        <th>{Crystal}</th>
        <th><span id='crystal'></span>&nbsp;</th>
        <th>{mod_ma_res}</th>
    </tr><tr>
        <th>{Metal}</th>
        <th><input name="metal" id="metTrader" type="text" value="0" onkeyup="calcul()"/><a href="#" onclick="document.getElementById('metTrader').value={max_resource1};calcul()"><font color="green">max</font></a></th>
        <th>{mod_ma_res_a}</th>
    </tr><tr>
        <th>{Deuterium}</th>
        <th><input name="deut" id="deutTrader" type="text" value="0" onkeyup="calcul()"/><a href="#" onclick="document.getElementById('deutTrader').value={max_resource2};calcul()"><font color="green">max</font></a></th>
        <th>{mod_ma_res_b}</th>
    </tr><tr>
        <th colspan="6"><input type="submit" value="{tr_exchange}" /></th>
    </tr>
    </table>
    </form>
</div>