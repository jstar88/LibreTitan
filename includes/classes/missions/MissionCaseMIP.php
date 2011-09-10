<?php
class MissionCaseMIP extends FlyingFleetHandler
{
   public function MissionCaseMIP ($FleetRow) 
    { 
        global $user, $pricelist, $lang, $resource, $CombatCaps,$planetrow; 
 
 
        if(isArriveToDestination($FleetRow))
        {
               $real_language_target=getRealLanguage($user['id'],$FleetRow['fleet_target_owner']);  
               if(empty($real_language_target))
		             $real_language_target=$lang;
                
                //caching
                $TargetPlanet = $this->getTargetPlanetInfoInCache($FleetRow);
                $UserPlanet   = $this->getStartPlanetInfoInCache($FleetRow);
                // double query in one
                $query ='(SELECT defence_tech FROM  {{table}} WHERE `id` = '.$FleetRow['fleet_target_owner'].')';
                $query.=' UNION ';
                $query.='(SELECT  military_tech FROM  {{table}} WHERE `id` = '.$FleetRow['fleet_owner'].')';
                $userX =doquery($query, 'users', true);

                if ($TargetPlanet['interceptor_misil'] >= $FleetRow['fleet_amount'] && $FleetRow['fleet_amount'] > 0) 
                { 
                    $message = $real_language_target["ma_all_destroyed"] . '<br>';
                    doquery("UPDATE {{table}} SET ".$resource[502]." = '0' WHERE id = ".$TargetPlanet['id'], 'planets'); 
                } 
                else 
                {   
                  doquery("UPDATE {{table}} SET ".$resource[502]." = ".$resource[502]." - ".$FleetRow['fleet_amount']." WHERE id = ".$TargetPlanet['id'],  'planets');      
                    if ($TargetPlanet['interceptor_misil'] > 0)
                        $message = $TargetPlanet['interceptor_misil'].$real_language_target['ma_some_destroyed']." <br>"; 

                    $attack = floor(($FleetRow['fleet_amount'] - $TargetPlanet['interceptor_misil']) * ($CombatCaps[503]['attack'] * (1 + ($userX["military_tech"] / 10))));

                    switch ($FleetRow['fleet_target_obj'])
                    {
                        case 0:
                            $attack_order = Array(401, 402, 403, 404, 405, 406, 407, 408, 409, 503);
                            break;
                        case 1:
                            $attack_order = Array(402, 401, 403, 404, 405, 406, 407, 408, 409, 503);
                            break;
                        case 2:
                            $attack_order = Array(403, 401, 402, 404, 405, 406, 407, 408, 409, 503);
                            break;
                        case 3:
                            $attack_order = Array(404, 401, 402, 403, 405, 406, 407, 408, 409, 503);
                            break;
                        case 4:
                            $attack_order = Array(405, 401, 402, 403, 404, 406, 407, 408, 409, 503);
                            break;
                        case 5:
                            $attack_order = Array(406, 401, 402, 403, 404, 405, 407, 408, 409, 503);
                            break;
                        case 6:
                            $attack_order = Array(407, 401, 402, 403, 404, 405, 406, 408, 409, 503);
                            break;
                        case 7:
                            $attack_order = Array(408, 401, 402, 403, 404, 405, 406, 407, 409, 503);
                            break;
                        case 8:
                            $attack_order = Array(409, 401, 402, 403, 404, 405, 406, 407, 408, 503);
                            break;
                    }

                    for ($t = 0; $t < 10; $t++)
                    {
                        $n = $attack_order[$t];
                        
                        if (!empty($TargetPlanet[$resource[$n]]))
                        {
                            $defense = (($pricelist[$n]['metal'] + $pricelist[$n]['crystal']) / 10) * (1 + ($userX['defence_tech'] / 10));

                            if ($attack >= ($defense * $TargetPlanet[$resource[$n]]))
                            {
                                $destroyed = $TargetPlanet[$resource[$n]];
                            }
                            else
                            {
                                $destroyed = floor($attack / $defense);
                            }
                            
                            $attack -= $destroyed * $defense;
                            if($attack<=0)
                              return;
                            
                            if ($destroyed != 0) 
                            {
                                $message .= $real_language_target['tech'][$n] . " (-" . $destroyed . ")<br>";
                                $newArr  .= "`".$n."` = '".$resource[$n]-$destroyed."',";
                           }
                        }
                    }
                } 
                //one query, no more 10!
                if(!empty($newArr))
                  doquery('UPDATE {{table}} SET '.substr($newArr,0,-1).' WHERE id = '.$TargetPlanet['id'], 'planets');
                
                $search=array('%1%','%2%','%3%');
                $replace=array($FleetRow['fleet_amount'], $UserPlanet['name'].' ['.  $FleetRow['fleet_start_universe'] .':'.$FleetRow['fleet_start_galaxy'] .':'. $FleetRow['fleet_start_system']  .':'. $FleetRow['fleet_start_planet'].'] ', $TargetPlanet['name']. ' ['.  $FleetRow['fleet_end_universe'] .':'. $FleetRow['fleet_end_galaxy'] .':'. $FleetRow['fleet_end_system'] .':'.  $FleetRow['fleet_end_planet'].'] ');
                $message_vorlage=str_replace($search,$replace,$real_language_target['ma_missile_string']); 
 
                if (empty($message)) 
                    $message = $real_language_target['ma_planet_without_defens']; 

                SendSimpleMessage($FleetRow['fleet_target_owner'], '', $FleetRow['fleet_end_time'], 3, $real_language_target['sys_mess_tower'], $real_language_target['gl_missile_attack'], $message_vorlage . $message); 
 
                doquery("DELETE FROM {{table}} WHERE fleet_id = '" . intval($FleetRow['fleet_id']) . "'", 'fleets'); 
             
        } 
    }
} 
?>
