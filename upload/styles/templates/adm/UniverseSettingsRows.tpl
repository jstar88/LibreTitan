<table width="90%">
 <tr>
	<td width="60%">
    {universe_name}
  </td>
	<td width=15%>
    {iduniverse}</td>
	<td width=5%>
    <div align=center>
	        <img border=0 src=../styles/images/r3.png width=15 height=15 onclick="showhidesimple('{iduniverse}')" alt={universe_settings_mod} title={universe_settings_mod}></a>
	        <a href=UniverseSettingsPage.php?iduniverse={iduniverse}&action=Cancella><img border=0 src=../styles/images/r1.png width=15 height=15 alt={universe_settings_delete} title={universe_settings_delete}></a>
    </div>    
	</td>
 </tr>
</table>
<table width="90%" id="{iduniverse}" style='display:none'>
<tr>
<td width="20%" class="c">
&nbsp;
</td>
 <td class="c" width="20%">
  <textarea  name="{iduniverse}" onkeyup="getElementById('universe_to_mod').value=this.name"  cols="20" rows="1" ></textarea>  
  <input type='submit'  name='submit' value='{universe_settings_save}'  /> 
 </td>
 <td width="40%" class="c">
 &nbsp;
 </td>
</tr>
</table>  