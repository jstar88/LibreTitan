SELECT 
{{table}}galaxy.metal, 
{{table}}galaxy.crystal, 
{{table}}galaxy.id_luna,
{{table}}galaxy.destruyed_moon, 
{{table}}galaxy.id_planet,
{{table}}planets.universe,
{{table}}planets.galaxy, 
{{table}}planets.system, 
{{table}}planets.planet, 
{{table}}planets.destruyed, 
{{table}}planets.name, 
{{table}}planets.image, 
{{table}}planets.last_update,
{{table}}planets.id_owner,
 
{{table}}users.id, 
{{table}}users.ally_id, 
{{table}}users.bana, 
{{table}}users.urlaubs_modus, 
{{table}}users.onlinetime, 
{{table}}users.username,
 
{{table}}statpoints.stat_type, 
{{table}}statpoints.stat_code, 
{{table}}statpoints.total_rank, 
{{table}}statpoints.total_points, 

{{table}}moons.diameter, 
{{table}}moons.temp_min, 
{{table}}moons.name AS name_moon, 

{{table}}alliance.ally_name, 
{{table}}alliance.ally_tag, 
{{table}}alliance.ally_web, 
{{table}}alliance.ally_members,

{{table}}buddy.owner AS friends_owner,
{{table}}buddy.sender AS friends_sender

FROM {{table}}alliance 
RIGHT JOIN 
(
   {{table}}planets AS {{table}}moons 
   RIGHT JOIN  
   (  
      {{table}}statpoints 
      RIGHT JOIN  
      (  
         (
            ( 
               {{table}}planets 
               INNER JOIN 
               {{table}}users 
               ON 
               {{table}}planets.id_owner = {{table}}users.id
            ) 
            INNER JOIN 
            {{table}}galaxy 
            ON 
            {{table}}planets.id = {{table}}galaxy.id_planet
         )
         LEFT JOIN 
         {{table}}buddy
         ON 
         ({{table}}buddy.owner = {{table}}planets.id_owner OR {{table}}buddy.sender = {{table}}planets.id_owner)
      )  
      ON
      {{table}}statpoints.id_owner={{table}}users.id AND {{table}}statpoints.stat_code=1 AND {{table}}statpoints.stat_type=1
   ) 
   ON 
      {{table}}moons.id = {{table}}galaxy.id_luna
) 
ON 
   {{table}}alliance.id = {{table}}users.ally_id

WHERE 
(
   {{table}}galaxy.universe='".$this->TargetUniverse."' 
   AND {{table}}galaxy.galaxy='".$this->TargetGalaxy."' 
   AND {{table}}galaxy.system='".$$this->TargetSystem."' 
   AND ({{table}}galaxy.planet>'0' AND {{table}}galaxy.planet<='".MAX_PLANET_IN_SYSTEM."')
)
GROUP BY `id_planet` 
ORDER BY {{table}}planets.planet; 