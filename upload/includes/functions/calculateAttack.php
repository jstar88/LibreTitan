<?php

##############################################################################
# *																			 #
# * XG PROYECT																 #
# *  																		 #
# * @copyright Copyright (C) 2008 - 2009 By lucky from xgproyect.net      	 #
# *																			 #
# *																			 #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.									 #
# *																			 #
# *  This program is distributed in the hope that it will be useful,		 #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of			 #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the			 #
# *  GNU General Public License for more details.							 #
# *																			 #
##############################################################################

if(!defined('INSIDE')){ die(header("location:../../"));}

    function calculateAttack (&$attackers, &$defenders)
    {
        global $pricelist, $CombatCaps, $game_config, $resource;

        $totalResourcePoints = array('attacker' => 0, 'defender' => 0);
        $resourcePointsAttacker = array('metal' => 0, 'crystal' => 0);

        foreach ($attackers as $fleetID => $attacker) {
            foreach ($attacker['detail'] as $element => $amount) {
                $resourcePointsAttacker['metal'] += $pricelist[$element]['metal'] * $amount;
                $resourcePointsAttacker['crystal'] += $pricelist[$element]['crystal'] * $amount ;

                $totalResourcePoints['attacker'] += $pricelist[$element]['metal'] * $amount ;
                $totalResourcePoints['attacker'] += $pricelist[$element]['crystal'] * $amount ;
            }
        }

        $resourcePointsDefender = array('metal' => 0, 'crystal' => 0);
        foreach ($defenders as $fleetID => $defender) {
            foreach ($defender['def'] as $element => $amount) {                                //Line20
                if ($element < 300) {
                    $resourcePointsDefender['metal'] += $pricelist[$element]['metal'] * $amount ;
                    $resourcePointsDefender['crystal'] += $pricelist[$element]['crystal'] * $amount ;

                    $totalResourcePoints['defender'] += $pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] += $pricelist[$element]['crystal'] * $amount ;
                } else {
                    if (!isset($originalDef[$element])) $originalDef[$element] = 0;
                    $originalDef[$element] += $amount;

                    $totalResourcePoints['defender'] += $pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] += $pricelist[$element]['crystal'] * $amount ;
                }
            }
        }

		$max_rounds = 6;

        for ($round = 0, $rounds = array(); $round < $max_rounds; $round++) {
            $attackDamage  = array('total' => 0);
            $attackShield  = array('total' => 0);
            $attackAmount  = array('total' => 0);
            $defenseDamage = array('total' => 0);
            $defenseShield = array('total' => 0);
            $defenseAmount = array('total' => 0);
            $attArray = array();
            $defArray = array();

            foreach ($attackers as $fleetID => $attacker) {
                $attackDamage[$fleetID] = 0;
                $attackShield[$fleetID] = 0;
                $attackAmount[$fleetID] = 0;

                foreach ($attacker['detail'] as $element => $amount) {
                    $attTech    = (1 + (0.1 * ($attacker['user']['military_tech']) + (0.05 * $attacker['user']['rpg_amiral']))); //attaque
                    $defTech    = (1 + (0.1 * ($attacker['user']['defence_tech']) + (0.05 * $attacker['user']['rpg_amiral']))); //bouclier
                    $shieldTech = (1 + (0.1 * ($attacker['user']['shield_tech']) + (0.05 * $attacker['user']['rpg_amiral']))); //coque

                    $attackers[$fleetID]['techs'] = array($shieldTech, $defTech, $attTech);

                    $thisAtt    = $amount * ($CombatCaps[$element]['attack']) * $attTech * (rand(80, 120) / 100); //attaque
                    $thisDef    = $amount * ($CombatCaps[$element]['shield']) * $defTech ; //bouclier
                    $thisShield    = $amount * ($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / 10 * $shieldTech; //coque

                    $attArray[$fleetID][$element] = array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt);

                    $attackDamage[$fleetID] += $thisAtt;
                    $attackDamage['total'] += $thisAtt;
                    $attackShield[$fleetID] += $thisDef;
                    $attackShield['total'] += $thisDef;
                    $attackAmount[$fleetID] += $amount;
                    $attackAmount['total'] += $amount;
                }
            }

            foreach ($defenders as $fleetID => $defender) {
                $defenseDamage[$fleetID] = 0;
                $defenseShield[$fleetID] = 0;
                $defenseAmount[$fleetID] = 0;

                foreach ($defender['def'] as $element => $amount) {
                    $attTech    = (1 + (0.1 * ($defender['user']['military_tech']) + (0.05 * $defender['user']['rpg_amiral']))); //attaquue
                    $defTech    = (1 + (0.1 * ($defender['user']['defence_tech']) + (0.05 * $defender['user']['rpg_amiral']))); //bouclier
                    $shieldTech = (1 + (0.1 * ($defender['user']['shield_tech']) + (0.05 * $defender['user']['rpg_amiral']))); //coque

                    $defenders[$fleetID]['techs'] = array($shieldTech, $defTech, $attTech);

                    $thisAtt    = $amount * ($CombatCaps[$element]['attack']) * $attTech * (rand(80, 120) / 100); //attaque
                    $thisDef    = $amount * ($CombatCaps[$element]['shield']) * $defTech ; //bouclier
                    $thisShield    = $amount * ($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / 10 * $shieldTech; //coque

                    if ($element == 407 || $element == 408 || $element == 409) $thisAtt = 0;

                    $defArray[$fleetID][$element] = array('def' => $thisDef, 'shield' => $thisShield, 'att' => $thisAtt);

                    $defenseDamage[$fleetID] += $thisAtt;
                    $defenseDamage['total'] += $thisAtt;
                    $defenseShield[$fleetID] += $thisDef;
                    $defenseShield['total'] += $thisDef;
                    $defenseAmount[$fleetID] += $amount;
                    $defenseAmount['total'] += $amount;
                }
            }

            $rounds[$round] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackDamage, 'defense' => $defenseDamage, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount, 'infoA' => $attArray, 'infoD' => $defArray);

            if ($defenseAmount['total'] <= 0 || $attackAmount['total'] <= 0) {
                break;
            }

            // Calculate hit percentages (ACS only but ok)
            $attackPct = array();
            foreach ($attackAmount as $fleetID => $amount) {
                if (!is_numeric($fleetID)) continue;
                $attackPct[$fleetID] = $amount / $attackAmount['total'];
            }

            $defensePct = array();
            foreach ($defenseAmount as $fleetID => $amount) {
                if (!is_numeric($fleetID)) continue;
                $defensePct[$fleetID] = $amount / $defenseAmount['total'];
            }

            // CALCUL DES PERTES !!!
            $attacker_n = array();
            $attacker_shield = 0;
            foreach ($attackers as $fleetID => $attacker) {
                $attacker_n[$fleetID] = array();

                foreach($attacker['detail'] as $element => $amount) {
                    $defender_moc = $amount * ($defenseDamage['total'] * $attackPct[$fleetID]) / $attackAmount[$fleetID];

                    if ($amount > 0) {
                        if ($attArray[$fleetID][$element]['def']/$amount < $defender_moc) {
                            $max_removePoints = floor($amount * $defenseAmount['total'] / $attackAmount[$fleetID] * $attackPct[$fleetID]);

                            $defender_moc -= $attArray[$fleetID][$element]['def'];
                            $attacker_shield += $attArray[$fleetID][$element]['def'];
                            $ile_removePoints = floor($defender_moc / (($pricelist[$element]['metal'] + $pricelist[$element]['crystal'])  / 10));

                            if ($max_removePoints < 0) $max_removePoints = 0;
                            if ($ile_removePoints < 0) $ile_removePoints = 0;

                            if ($ile_removePoints > $max_removePoints) {
                                $ile_removePoints = $max_removePoints;
                            }

                            $attacker_n[$fleetID][$element] = ceil($amount - $ile_removePoints);
                            if ($attacker_n[$fleetID][$element] <= 0) {
                                $attacker_n[$fleetID][$element] = 0;
                            }
                        } else {
                            $attacker_n[$fleetID][$element] = round($amount);
                            $attacker_shield += $defender_moc;
                        }
                    } else {
                        $attacker_n[$fleetID][$element] = round($amount);
                        $attacker_shield += $defender_moc;
                    }
                }
            }

            $defender_n = array();
            $defender_shield = 0;

            foreach ($defenders as $fleetID => $defender) {
                $defender_n[$fleetID] = array();

                foreach($defender['def'] as $element => $amount) {
                    $attacker_moc = $amount * ($attackDamage['total'] * $defensePct[$fleetID]) / $defenseAmount[$fleetID];

                    if ($amount > 0) {
                        if ($defArray[$fleetID][$element]['def']/$amount < $attacker_moc) {
                            $max_removePoints = floor($amount * $attackAmount['total'] / $defenseAmount[$fleetID] * $defensePct[$fleetID]);
                            $attacker_moc -= $defArray[$fleetID][$element]['def'];
                            $defender_shield += $defArray[$fleetID][$element]['def'];
                            $ile_removePoints = floor($attacker_moc / (($pricelist[$element]['metal'] + $pricelist[$element]['crystal']) / 10));

                            if ($max_removePoints < 0) $max_removePoints = 0;
                            if ($ile_removePoints < 0) $ile_removePoints = 0;

                            if ($ile_removePoints > $max_removePoints) {
                                $ile_removePoints = $max_removePoints;
                            }

                            $defender_n[$fleetID][$element] = ceil($amount - $ile_removePoints);
                            if ($defender_n[$fleetID][$element] <= 0) {
                                $defender_n[$fleetID][$element] = 0;
                            }

                        } else {
                            $defender_n[$fleetID][$element] = round($amount);
                            $defender_shield += $attacker_moc;
                        }
                    } else {
                        $defender_n[$fleetID][$element] = round($amount);
                        $defender_shield += $attacker_moc;
                    }
                }
            }

            // "Rapidfire"
            foreach ($attackers as $fleetID => $attacker) {
                foreach ($defenders as $fleetID2 => $defender) {
                    foreach($attacker['detail'] as $element => $amount) {
                        if ($amount > 0) {
                            foreach ($CombatCaps[$element]['sd'] as $c => $d) {
                                if (isset($defender['def'][$c])) {
                                    if ($d > 0) {
                                        $e = ($d / $defender['techs'][0]) / ($defender['techs'][1] * $attacker['techs'][2]);
                                        $defender_n[$fleetID2][$c] -= ceil(($amount * $e * (rand(50,120)/ 100)/ 2) * $defensePct[$fleetID2] * ($amount / $attackAmount[$fleetID]));
                                        if ($defender_n[$fleetID2][$c] <= 0) {
                                            $defender_n[$fleetID2][$c] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    foreach($defender['def'] as $element => $amount) {
                        if ($amount > 0) {
                            foreach ($CombatCaps[$element]['sd'] as $c => $d) {
                                if (isset($attacker['detail'][$c])) {
                                    if ($d > 0) {
                                        $e = ($d / $defender['techs'][0]) / ($defender['techs'][1] * $attacker['techs'][2]);
                                        $attacker_n[$fleetID][$c] -= ceil(($amount * $e * (rand(50,120)/ 100)/ 2) * $attackPct[$fleetID] * ($amount / $defenseAmount[$fleetID2]));
                                        if ($attacker_n[$fleetID][$c] <= 0) {
                                            $attacker_n[$fleetID][$c] = 0;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $rounds[$round]['attackShield'] = $attacker_shield;
            $rounds[$round]['defShield'] = $defender_shield;

            foreach ($attackers as $fleetID => $attacker) {
                $attackers[$fleetID]['detail'] = array_map('round', $attacker_n[$fleetID]);
            }

            foreach ($defenders as $fleetID => $defender) {
                $defenders[$fleetID]['def'] = array_map('round', $defender_n[$fleetID]);
            }
        }

        if ($attackAmount['total'] <= 0) {
            $won = "r"; // defender

        } elseif ($defenseAmount['total'] <= 0) {
            $won = "a"; // attacker

        } else {
            $won = "w"; // draw
            $rounds[count($rounds)] = array('attackers' => $attackers, 'defenders' => $defenders, 'attack' => $attackDamage, 'defense' => $defenseDamage, 'attackA' => $attackAmount, 'defenseA' => $defenseAmount);
        }

        // CDR
        foreach ($attackers as $fleetID => $attacker) {                                       // flotte attaquant en CDR
            foreach ($attacker['detail'] as $element => $amount) {
                $totalResourcePoints['attacker'] -= $pricelist[$element]['metal'] * $amount ;
                $totalResourcePoints['attacker'] -= $pricelist[$element]['crystal'] * $amount ;

                $resourcePointsAttacker['metal'] -= $pricelist[$element]['metal'] * $amount ;
                $resourcePointsAttacker['crystal'] -= $pricelist[$element]['crystal'] * $amount ;
            }
        }

        $resourcePointsDefenderDefs = array('metal' => 0, 'crystal' => 0);
        foreach ($defenders as $fleetID => $defender) {
            foreach ($defender['def'] as $element => $amount) {
                if ($element < 300) {                                                        // flotte defenseur en CDR
                    $resourcePointsDefender['metal'] -= $pricelist[$element]['metal'] * $amount ;
                    $resourcePointsDefender['crystal'] -= $pricelist[$element]['crystal'] * $amount ;

                    $totalResourcePoints['defender'] -= $pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] -= $pricelist[$element]['crystal'] * $amount ;
                } else {                                                                    // defs defenseur en CDR + reconstruction
                    $totalResourcePoints['defender'] -= $pricelist[$element]['metal'] * $amount ;
                    $totalResourcePoints['defender'] -= $pricelist[$element]['crystal'] * $amount ;

                    $lost = $originalDef[$element] - $amount;
                    $giveback = round($lost * (rand(70*0.8, 70*1.2) / 100));
                    $defenders[$fleetID]['def'][$element] += $giveback;
                    $resourcePointsDefenderDefs['metal'] += $pricelist[$element]['metal'] * ($lost - $giveback) ;
                    $resourcePointsDefenderDefs['crystal'] += $pricelist[$element]['crystal'] * ($lost - $giveback) ;

                }
            }
        }


        $totalLost = array('att' => $totalResourcePoints['attacker'], 'def' => $totalResourcePoints['defender']);
        $debAttMet = ($resourcePointsAttacker['metal'] * ($game_config['Fleet_Cdr'] / 100));
        $debAttCry = ($resourcePointsAttacker['crystal'] * ($game_config['Fleet_Cdr'] / 100));
        $debDefMet = ($resourcePointsDefender['metal'] * ($game_config['Fleet_Cdr'] / 100)) + ($resourcePointsDefenderDefs['metal'] * ($game_config['Defs_Cdr'] / 100));
        $debDefCry = ($resourcePointsDefender['crystal'] * ($game_config['Fleet_Cdr'] / 100)) + ($resourcePointsDefenderDefs['crystal'] * ($game_config['Defs_Cdr'] / 100));

        return array('won' => $won, 'debree' => array('att' => array($debAttMet, $debAttCry), 'def' => array($debDefMet, $debDefCry)), 'rw' => $rounds, 'lost' => $totalLost);
    }
?>