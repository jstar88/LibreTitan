<script type="text/javascript" >
function calcul() {
	var Cristal = document.forms['trader'].elements['crystal'].value;
	var Deuterium = document.forms['trader'].elements['deut'].value;

	Cristal   = Cristal * {mod_ma_res_a};
	Deuterium = Deuterium * {mod_ma_res_b};

	var Metal = Cristal + Deuterium;
    var max={max_resource};
    Metal = Math.min(Metal,max);
	document.getElementById("met").innerHTML = Math.round(Metal);

	if (isNaN(document.forms['trader'].elements['crystal'].value)) {
		document.getElementById("met").innerHTML = "Solo numeri";
	}
	if (isNaN(document.forms['trader'].elements['deut'].value)) {
		document.getElementById("met").innerHTML = "Solo numeri";
	}
}
</script>
<br />
<div id="content">
    <form id="trader" action="" method="post">
    <input type="hidden" name="ress" value="metal">
    <table width="569">
    <tr>
        <td class="c" colspan="5"><b>{tr_sell_metal}</b></td>
    </tr><tr>
        <th>{tr_resource}</th>
        <th>{tr_amount}</th>
        <th>{tr_quota_exchange}</th>
    </tr><tr>
        <th>{Metal}</th>
        <th><span id='met'></span>&nbsp;</th>
        <th>{mod_ma_res}</th>
    </tr><tr>
        <th>{Crystal}</th>
        <th><input name="crystal" id="cryTrader" type="text" value="0" onkeyup="calcul()"/><a href="#" onclick="document.getElementById('cryTrader').value={max_resource1};calcul()"><font color="green">max</font></a></th>
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