
<form name="universeform" method="get" action="UniverseSettingsPage.php">
  <table width="90%">
    <tr> 
      <td class="c" colspan="3"> 
        <div align="center">{universe_settings_title}</div>
      </td>
    </tr>
  </table>
  <table width="90%">
    <tr> 
      <td class="c" width="60%"><div align="center">{universe_settings_insertname}</div></th>     
      <td class="c" width="20%">&nbsp;</th>
    </tr>
    <tr> 
      <td class="c" width="60%"><input name="universe_name" size="20" value="" type="text"></td>
      <td class="c" width="20%"><input type="submit" name="tipo" value="{universe_settings_add}"></td>
    </tr>
  </table>
  <input type="hidden" name="universe_to_mod" id="universe_to_mod"  value="Nessuno" >
   
   
    {universe_list}
</form>