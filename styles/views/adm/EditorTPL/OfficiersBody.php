<script>document.body.style.overflow = "auto";</script>
<body>
<br />
</table>
<form action="" method="post">
<table width="45%">
{display}
<tr>
<th colspan="3" align="left"><a href="AccountEditorPage.php">
<img src="../styles/images/Adm/arrowright.png" width="16" height="10"/> {ad_back_to_menu}</a></th>
</tr><tr>
	<td class="c" colspan="7">{officiers_title}</td>
</tr><tr>
	<th colspan="2">{input_id_user}</th>
	<th><input name="id" type="text" value="0" size="3" /></th>
</tr><tr>
	<td class="c">{ad_number}</td>
	<td class="c">{officiers_title}</td>
	<td class="c">{officiers_count}</td>
</tr><tr>
	<th>1</th>
	<th>{geologist}</th>
	<th><input name="rpg_geologue" type="text" value="0" /></th>
</tr><tr>
	<th>2</th>
	<th>{admiral}</th>
	<th><input name="rpg_amiral" type="text" value="0" /></th>
</tr><tr>
	<th>3</th>
	<th>{engineer}</th>
	<th><input name="rpg_ingenieur" type="text" value="0" /></th>
</tr><tr>
	<th>4</th>
	<th>{technocrat}</th>
	<th><input name="rpg_technocrate" type="text" value="0" /></th>
</tr><tr>
	<th colspan="3">
	<input type="reset" value="{button_reset}"/><br /><br />
	<input type="Submit" value="{button_add}" name="add"/>&nbsp;
	<input type="Submit" value="{button_delete}" name="delete"/></th>
</tr>
</table>
</form>
</body>