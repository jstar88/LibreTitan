<form action="game.php?page=galaxy&mode=1" method="post" id="galaxy_form">
<input type="hidden" id="auto" value="dr" >
<input type="hidden" name="universe_target" value="{universe_target}" >
<table border="1" class="header">
  <tr class="header">
  <td class="header">
      <table class="header">
        <tr class="header">
         <td class="c" colspan="3">{gl_universe}</td>
        </tr>
        <tr class="header">
          <td class="l"><input type="button" name="universeLeft" value="&lt;-" onClick="universe_submit('universeLeft')"></td>
          <td class="l"><input type="text" name="universe" value="{universe}" size="5" maxlength="3" tabindex="1"></td>
          <td class="l"><input type="button" name="universeRight" value="-&gt;" onClick="universe_submit('universeRight')"></td>
        </tr>
       </table>
      </td>
    <td class="header">
      <table class="header">
        <tr class="header">
         <td class="c" colspan="3">{gl_galaxy}</td>
        </tr>
        <tr class="header">
          <td class="l"><input type="button" name="galaxyLeft" value="&lt;-" onClick="galaxy_submit('galaxyLeft')"></td>
          <td class="l"><input type="text" name="galaxy" value="{galaxy}" size="5" maxlength="3" tabindex="1"></td>
          <td class="l"><input type="button" name="galaxyRight" value="-&gt;" onClick="galaxy_submit('galaxyRight')"></td>
        </tr>
       </table>
      </td>
      <td class="header">
       <table class="header">
        <tr class="header">
         <td class="c" colspan="3">{gl_solar_system}</td>
        </tr>
         <tr class="header">
          <td class="l"><input type="button" name="systemLeft" value="&lt;-" onClick="galaxy_submit('systemLeft')"></td>
          <td class="l"><input type="text" name="system" value="{system}" size="5" maxlength="3" tabindex="2"></td>
          <td class="l"><input type="button" name="systemRight" value="-&gt;" onClick="galaxy_submit('systemRight')"></td>
         </tr>
        </table>
       </td>
      </tr>
      <tr class="header">
        <td class="header" style="background-color:transparent;border:0px;" colspan="3" align="center"> 
        	<input type="submit" value="{gl_show}">
        </td>
      </tr>
</table>
</form>