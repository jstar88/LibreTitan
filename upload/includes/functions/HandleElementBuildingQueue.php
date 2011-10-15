<?php

##############################################################################
# *                                                                             #
# * XG PROYECT                                                                 #
# *                                                                           #
# * @copyright Copyright (C) 2008 - 2009 By lucky from xgproyect.net           #
# *                                                                             #
# *                                                                             #
# *  This program is free software: you can redistribute it and/or modify    #
# *  it under the terms of the GNU General Public License as published by    #
# *  the Free Software Foundation, either version 3 of the License, or       #
# *  (at your option) any later version.                                     #
# *                                                                             #
# *  This program is distributed in the hope that it will be useful,         #
# *  but WITHOUT ANY WARRANTY; without even the implied warranty of             #
# *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             #
# *  GNU General Public License for more details.                             #
# *                                                                             #
##############################################################################

if(!defined('INSIDE')){ die(header("location:../../"));}

    function HandleElementBuildingQueue ( $CurrentUser, &$CurrentPlanet, $ProductionTime )
    {
        global $resource, $LegacyPlanet;

        if (!empty($CurrentPlanet['b_hangar_id']))
        {
            $Builded                    = array ();
            $CurrentPlanet['b_hangar'] += $ProductionTime;
            $BuildQueue                 = explode(';', $CurrentPlanet['b_hangar_id']);
            $BuildArray                    = array();
            
			foreach ($BuildQueue as $Node => $Array)
			{
				if ($Array != '')
				{
					$Item              = explode(',', $Array);
					$AcumTime		   += GetBuildingTime ($CurrentUser, $CurrentPlanet, $Item[0]);
					$BuildArray[$Node] = array($Item[0], $Item[1], $AcumTime);
				}
			}

			$CurrentPlanet['b_hangar_id'] 	= '';
			$UnFinished 					= false;

                    foreach ( $BuildArray as $Node => $Item ){
                        $Element   = $Item[0];
                        $Count     = $Item[1];
                        $BuildTime = $Item[2];
                        $Builded[$Element] = 0;
                        if (!$UnFinished and $BuildTime > 0){
                            $AllTime = $BuildTime * $Count;
                            if($CurrentPlanet['b_hangar'] >= $BuildTime){
                                $Done = min($Count, floor( $CurrentPlanet['b_hangar'] / $BuildTime));
                                if($Count > $Done){
                                    $CurrentPlanet['b_hangar'] -= $BuildTime * $Done;                                
                                    $UnFinished = true;    
                                    $Count -= $Done;                                                        
                                }else{
                                    $CurrentPlanet['b_hangar'] -= $AllTime;                                        
                                    $Count = 0;
                                }
                                $Builded[$Element] += $Done;
                                $CurrentPlanet[$resource[$Element]] += $Done;
                                $LegacyPlanet[$Element]   			= $resource[$Element];
                            }else{
                                $UnFinished = true;    
                            }
                        }elseif(!$UnFinished){    
                                $Builded[$Element] 			+= $Count;
                                $CurrentPlanet[$resource[$Element]] 	+= $Count;  
                                $LegacyPlanet[$Element]   		= $resource[$Element];                           
                                $Count = 0;                            
                        }
                        if ( $Count != 0 ){
                            $CurrentPlanet['b_hangar_id'] .= $Element.",".$Count.";";
                        }
                    }
        }
        else
        {
            $Builded                   = '';
            $CurrentPlanet['b_hangar'] = 0;
        }  
        return $Builded;
    }
?>