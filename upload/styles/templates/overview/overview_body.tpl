{addScript:timer}
<script>updateTimes('bxx','{fleet_count}')</script>
<script>updateTimes('wxx','{wars_count}')</script>
<div id="content">
   <table width="519">
      <tr>
         <td class="c" colspan="4"><a href="game.php?page=overview&mode=renameplanet" title="{Planet_menu}">{ov_planet} "{planet_name}"</a> ({user_username})</td>
      </tr>
      {group:overview:new_mes}
         <tr>
            <th>{ov_server_time}</th>
        	   <th colspan="3">{date_time}</th>
        </tr>
        <tr>
        	   <td colspan="4" class="c">{ov_events}</td>
        </tr>
         {fleet_list}
        <tr>
        	   <th>{group:overview:moon_link}</th>
        	   <th colspan="2">
               <img src="{dpath}planeten/{planet_image}.jpg" height="200" width="200"><br>{group:overview:building}
            </th>
        	   <th class="s">
            	<table class="s" align="top" border="0">
                	<tr>{groups:overview:anothers_planets}</tr>
               </table>
            </th>
        </tr>
        <tr>
            <th>{ov_diameter}</th>
            <th colspan="3">{planet_diameter} {ov_distance_unit} (<a title="{Developed_fields}">{planet_field_current}</a> / <a title="{max_eveloped_fields}">{planet_field_max}</a> {fields})</th>
        </tr>
        <tr>
            <th>{ov_temperature}</th>
            <th colspan="3">{ov_aprox} {planet_temp_min}{ov_temp_unit} {ov_to} {planet_temp_max}{ov_temp_unit}</th>
        </tr>
        <tr>
            <th>{ov_position}</th>
            <th colspan="3"><a href="game.php?page=galaxy&mode=0&universe={galaxy_universe}&galaxy={galaxy_galaxy}&system={galaxy_system}">[{galaxy_universe}:{galaxy_galaxy}:{galaxy_system}:{galaxy_planet}]</a></th>
        </tr>
        <tr>
            <th>{ov_points}</th>
            <th colspan="3">
               {group:overview:user_rank}
            </th>
        </tr>
    </table>
    <table width="519">
      <tr>
         <td class="c" colspan="4">{ov_countdown_title}</th>
      </tr>
      {group:overview:ov_countdown}
   </table>
</div>