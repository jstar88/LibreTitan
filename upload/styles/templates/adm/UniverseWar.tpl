<script type="text/javascript" src="{dpath}scripts/ts_picker.js"></script>

<form name="warform" method="get" action="UniverseWarPage.php">
  <table width="90%">
    <tr> 
      <td class="c" colspan="3"> 
        <div align="center">{universe_war_title}</div>
      </td>
    </tr>
  </table>
  <table width="90%">
    <tr> 
      <td class="c" width="16%"><div align="center">{universe_war_insertuniverseinwar1}</div></th>
      <td class="c" width="16%"><div align="center">{universe_war_insertuniverseinwar2}</div></th>
      <td class="c" width="16%"><div align="center">{universe_war_insertwarstart}</div></th>
      <td class="c" width="16%"><div align="center">{universe_war_insertwarend}</div></th>
      <td class="c" width="16%"><div align="center">{universe_war_insertdistance}</div></th>    
      <td class="c" width="20%">&nbsp;</th>
    </tr>
    <tr> 
      <td class="c" width="16%">
      <select name="select_id_universe_in_war1">
      {select_id_universe_in_war1}
      </select>
      </td>
      <td class="c" width="16%">
       <select name="select_id_universe_in_war2">
       {select_id_universe_in_war2}
       </select>
      </td>
      <td class="c" width="23%">
        <input type="Text" name="war_start" value="" ><a href="javascript:show_calendar('document.warform.war_start', document.warform.war_start.value,'{dpath}');"><img src="{dpath}styles/images/datepick/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
      </td>
      <td class="c" width="23%">
        <input type="Text" name="war_end" value="" ><a href="#" onclick="javascript:show_calendar('document.warform.war_end', document.warform.war_end.value,'{dpath}');"><img src="{dpath}styles/images/datepick/cal.gif" width="16" height="16" border="0" alt="Click Here to Pick up the timestamp"></a>
      </td>
      <td class="c" width="16%"><input name="distance" size="10%" value="" type="text"></td>
      <td class="c" width="22%"><input type="submit" name="tipo" value="{universe_war_add}"></td>
    </tr>
  </table>
  <table width="90%">  
    {war_list}
  </table>
</form>