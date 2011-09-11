<?php


if(!defined('INSIDE')){ die(header("location:../../"));}

	function UpdateMoonID()
	{
		$Data = doquery(	"SELECT l.id AS idluna,p.id AS idplanets,
							g.universe AS universe,g.galaxy AS galaxy, g.system AS system,
							g.planet AS planet
							FROM {{table}}lunas AS l
							Left Join {{table}}galaxy AS g ON
							l.id = g.id_luna AND
							l.universe = g.universe AND
							l.galaxy = g.galaxy AND
							l.system = g.system AND
							l.lunapos = g.planet
							Left Join {{table}}planets AS p ON
							g.universe = p.universe AND
							g.galaxy = p.galaxy AND
							g.system = p.system AND
							g.planet = p.planet AND
							p.planet_type = 3;",'');

		if($Data)
		{
			while ( $Moon = mysql_fetch_assoc ( $Data ) )
			{
				if($Moon['idplanets']!='')
				{
					$salida['planeta'][$Moon['idplanets']]=$Moon['idplanets'];
					$salida['luna'][$Moon['idluna']]=$Moon['idluna'];
					doquery ( "UPDATE {{table}} SET `id_luna` = '" . $Moon['idplanets'] . "' WHERE 
          `universe` = '" . $Moon['universe'] . "' AND 
          `galaxy` = '" . $Moon['galaxy'] . "' AND 
          `system` = '" . $Moon['system'] . "' AND 
          `planet` = '" . $Moon['planet'] . "';", 
          'galaxy' );
				}
				else
				{
					doquery ( "UPDATE {{table}} SET `id_luna` = '0' WHERE 
					`universe` = '" . $Moon['universe'] . "' AND
          `galaxy` = '" . $Moon['galaxy'] . "' AND 
          `system` = '" . $Moon['system'] . "' AND 
          `planet` = '" . $Moon['planet'] . "';", 'galaxy' );
				}
			}
			return $salida;
		}
	}

?>