<?php

/**
 * @project XG Proyect
 * @version 2.10.x build 0000
 * @copyright Copyright (C) 2008 - 2012
 */

if(!defined('INSIDE')){ die(header("location:../../"));}

class ShowTraderPage
{
	function __construct ( $CurrentUser , $CurrentPlanet )
	{
		global $lang;

		$parse 		= $lang;
		$template	= '';

		if($CurrentUser['darkmatter'] < TR_DARK_MATTER)
		{
			message(str_replace('%s',TR_DARK_MATTER,$lang['tr_darkmatter_needed']), '', '', TRUE);
			die();
		}

		if (isset($_POST['ress']) && $_POST['ress'] != '')
		{
			switch ($_POST['ress'])
			{
				case 'metal':
				{
					if ($_POST['cristal'] < 0 or $_POST['deut'] < 0)
					{
						message($lang['tr_only_positive_numbers'], "game.php?page=trader",1);
					}
					else
					{
						$necessaire	= (($_POST['cristal'] * 2) + ($_POST['deut'] * 4));
						$amout 		= array ( 'metal' => 0,
												'crystal' => $_POST['cristal'],
												'deuterium' => $_POST['deut']
											);

						$storage	= $this->check_storage ( $CurrentPlanet , $amout );

						if ( is_string ( $storage ) )
						{
							die ( message ( $storage , 'game.php?page=trader' , '2' ) );
						}

						if ($CurrentPlanet['metal'] > $necessaire)
						{
							$QryUpdatePlanet  = "UPDATE {{table}} SET ";
							$QryUpdatePlanet .= "`metal` = `metal` - ".round($necessaire).", ";
							$QryUpdatePlanet .= "`crystal` = `crystal` + ".round($_POST['cristal']).", ";
							$QryUpdatePlanet .= "`deuterium` = `deuterium` + ".round($_POST['deut'])." ";
							$QryUpdatePlanet .= "WHERE ";
							$QryUpdatePlanet .= "`id` = '".$CurrentPlanet['id']."';";

							doquery($QryUpdatePlanet , 'planets');

							$planetrow['metal']     -= $necessaire;
							$CurrentPlanet['cristal']   += $_POST['cristal'];
							$CurrentPlanet['deuterium'] += $_POST['deut'];
							doquery("UPDATE `{{table}}` SET `darkmatter` = `darkmatter` - ".TR_DARK_MATTER." WHERE `id` = ".$CurrentUser['id']."", 'users');
						}
						else
						{
							message($lang['tr_not_enought_metal'], "game.php?page=trader",1);
						}
					}
					break;
				}
				case 'cristal':
				{
					if ($_POST['metal'] < 0 or $_POST['deut'] < 0)
					{
						message($lang['tr_only_positive_numbers'], "game.php?page=trader",1);
					}
					else
					{
						$necessaire   = ((abs($_POST['metal']) * 0.5) + (abs($_POST['deut']) * 2));
						$amout 		= array ( 'metal' => $_POST['metal'],
												'crystal' => 0,
												'deuterium' => $_POST['deut']
											);

						$storage	= $this->check_storage ( $CurrentPlanet , $amout );

						if ( is_string ( $storage ) )
						{
							die ( message ( $storage , 'game.php?page=trader' , '2' ) );
						}

						if ($CurrentPlanet['crystal'] > $necessaire)
						{
							$QryUpdatePlanet  = "UPDATE {{table}} SET ";
							$QryUpdatePlanet .= "`metal` = `metal` + ".round($_POST['metal']).", ";
							$QryUpdatePlanet .= "`crystal` = `crystal` - ".round($necessaire).", ";
							$QryUpdatePlanet .= "`deuterium` = `deuterium` + ".round($_POST['deut'])." ";
							$QryUpdatePlanet .= "WHERE ";
							$QryUpdatePlanet .= "`id` = '".$CurrentPlanet['id']."';";

							doquery($QryUpdatePlanet , 'planets');

							$CurrentPlanet['metal']     += $_POST['metal'];
							$CurrentPlanet['cristal']   -= $necessaire;
							$CurrentPlanet['deuterium'] += $_POST['deut'];
							doquery("UPDATE `{{table}}` SET `darkmatter` = `darkmatter` - ".TR_DARK_MATTER." WHERE `id` = ".$CurrentUser['id']."", 'users');
						}
						else
						{
							message($lang['tr_not_enought_crystal'], "game.php?page=trader",1);
						}
					}
					break;
				}
				case 'deuterium':
				{
					if ($_POST['cristal'] < 0 or $_POST['metal'] < 0)
					{
						message($lang['tr_only_positive_numbers'], "game.php?page=trader",1);
					}
					else
					{
						$necessaire   = ((abs($_POST['metal']) * 0.25) + (abs($_POST['cristal']) * 0.5));
						$amout 		= array ( 'metal' => $_POST['metal'],
												'crystal' => $_POST['cristal'],
												'deuterium' => 0
											);

						$storage	= $this->check_storage ( $CurrentPlanet , $amout );

						if ( is_string ( $storage ) )
						{
							die ( message ( $storage , 'game.php?page=trader' , '2' ) );
						}

						if ($CurrentPlanet['deuterium'] > $necessaire)
						{
							$QryUpdatePlanet  = "UPDATE {{table}} SET ";
							$QryUpdatePlanet .= "`metal` = `metal` + ".round($_POST['metal']).", ";
							$QryUpdatePlanet .= "`crystal` = `crystal` + ".round($_POST['cristal']).", ";
							$QryUpdatePlanet .= "`deuterium` = `deuterium` - ".round($necessaire)." ";
							$QryUpdatePlanet .= "WHERE ";
							$QryUpdatePlanet .= "`id` = '".$CurrentPlanet['id']."';";

							doquery($QryUpdatePlanet , 'planets');

							$CurrentPlanet['metal']     += $_POST['metal'];
							$CurrentPlanet['cristal']   += $_POST['cristal'];
							$CurrentPlanet['deuterium'] -= $necessaire;
							doquery("UPDATE `{{table}}` SET `darkmatter` = `darkmatter` - ".TR_DARK_MATTER." WHERE `id` = ".$CurrentUser['id']."", 'users');
						}
						else
						{
							message($lang['tr_not_enought_deuterium'], "game.php?page=trader",1);
						}
					}
					break;
				}
			}

			message($lang['tr_exchange_done'],"game.php?page=trader",1);
		}
		else
		{
			if (!$_POST)
			{
				$template = gettemplate('trader/trader_main');
			}
			else
			{
				$parse['mod_ma_res'] = '1';

				switch ( ( isset ( $_POST['choix'] ) ? $_POST['choix'] : NULL ) )
				{
					case 'metal':
						$template = gettemplate('trader/trader_metal');
						$parse['mod_ma_res_a'] = '2';
						$parse['mod_ma_res_b'] = '4';
					break;
					case 'cristal':
						$template = gettemplate('trader/trader_cristal');
						$parse['mod_ma_res_a'] = '0.5';
						$parse['mod_ma_res_b'] = '2';
					break;
					case 'deut':
						$template = gettemplate('trader/trader_deuterium');
						$parse['mod_ma_res_a'] = '0.25';
						$parse['mod_ma_res_b'] = '0.5';
					break;
				}
			}
		}
		display(parsetemplate($template,$parse));
	}

	/**
	 * method check_storage
	 * param $current_planet
	 * param $amount
	 * param $force
	 * return amount of resource production
	 */
	public static function check_storage ( $current_planet , $amount , $force = NULL )
	{
		global $resource, $lang;


		if ( !is_array ( $amount ) )
		{
			throw new Exception ( "Must be array" , 1 );
		}


		$hangar	= array ( 'metal' => 22 , 'crystal' => 23 , 'deuterium' => 24 );
		$check 	= array();


		foreach ( $hangar as $k => $v )
		{
			if ( $amount[$k] == 0 )
			{
				unset ( $amount[$k] );
			}

			if ( array_key_exists ( $k , $amount ) )
			{
				if ( $current_planet[$k] + $amount[$k] >= Production::max_storable ( $current_planet[$resource[$v]] ) )
				{
					$check[$k] = FALSE;
				}
				else
				{
					$check[$k] = TRUE;
				}
			}
			else
			{
				$check[$k] = TRUE;
			}
		}


		if ( $check['metal'] === true && $check['crystal'] === true && $check['deuterium'] === true )
		{
			return FALSE;
		}
		else
		{
			if ( is_null ( $force ) )
			{
				foreach ( $hangar as $k => $v )
				{
					if ( $check[$k] === false )
					{
						return sprintf ( $lang['tr_full_storage'] , strtolower ( $lang['info'][$v]['name'] ) );
					}
					else
					{
						continue;
					}
				}
			}
			else
			{
				return $check;
			}
		}
	}
}
?>