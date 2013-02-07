<?php

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}
class ShowTopNavigationBar
{
    private $CurrentUser;
    private $CurrentPlanet; 
    
    public function __construct($CurrentUser, $CurrentPlanet){
        $this->CurrentUser=$CurrentUser;
        $this->CurrentPlanet=$CurrentPlanet;    
    } 
    public function show()
    {
        global $planetlist;

        $umod = $this->CurrentUser['db_deaktjava'] ? '<table width="100%" style="border: 2px solid red; text-align:center;background:transparent;"><tr style="background:transparent;"><td style="background:transparent;">' . Xtreme::get('tn_delete_mode') . date('d.m.Y h:i:s', $this->CurrentUser['db_deaktjava'] + ACCOUNT_DELETE_TIME) . '</td></tr></table>' : '';

        if (!$this->CurrentUser['urlaubs_modus'] || !$this->CurrentUser['db_deaktjava'])
        {
            $new = $this->CurrentUser['urlaubs_modus'] ? '<table width="100%" style="border: 2px solid #1DF0F0; text-align:center;background:transparent;"><tr style="background:transparent;"><td style="background:transparent;">' . Xtreme::get('tn_vacation_mode') . date('d.m.Y h:i:s', $this->CurrentUser['urlaubs_until']) . '</td></tr></table><br>' : '';
            $umod = $new . $umod;
        }

        $energy = pretty_number($this->CurrentPlanet["energy_max"] + $this->CurrentPlanet["energy_used"]) . "/" . pretty_number($this->CurrentPlanet["energy_max"]);
        $metal = pretty_number($this->CurrentPlanet["metal"]);
        $crystal = pretty_number($this->CurrentPlanet["crystal"]);
        $deuterium = pretty_number($this->CurrentPlanet["deuterium"]);
        if (($this->CurrentPlanet["energy_max"] + $this->CurrentPlanet["energy_used"]) < 0)
        {
            $energy = colorRed($energy);
        }
        if (($this->CurrentPlanet["metal"] >= $this->CurrentPlanet["metal_max"]))
        {
            $metal = colorRed($metal);
        }
        if (($this->CurrentPlanet["crystal"] >= $this->CurrentPlanet["crystal_max"]))
        {
            $crystal = colorRed($crystal);
        }
        if (($this->CurrentPlanet["deuterium"] >= $this->CurrentPlanet["deuterium_max"]))
        {
            $deuterium = colorRed($deuterium);
        }
        mysql_data_seek($planetlist, 0);
        $ThisUsersPlanets = $planetlist;
        $array = array();
        while ($CurPlanet = mysql_fetch_array($ThisUsersPlanets))
        {
            if ($CurPlanet['destruyed'] == 0)
            {
                $CurPlanet['selected'] = ($CurPlanet['id'] == $this->CurrentUser['current_planet']) ? 'selected' : '';
                $CurPlanet['page'] = $_GET['page'];
                $CurPlanet['gid'] = $_GET['gid'];
                $CurPlanet['mode'] = $_GET['mode'];
                if ($CurPlanet['planet_type'] == 3)
                {
                    $CurPlanet['name'] .= " (" . Xtreme::get('fcm_moon') . ")";
                }
                $array[] = $CurPlanet;
            }
        }
        Xtreme::assignToGroup('topnav', 'planetlist', 'topnav/option');
        Xtreme::assign('planetlist', $array);
        Xtreme::doLoopGroup('topnav', 'planetlist', 'planet');
        Xtreme::assign('energy', $energy);
        Xtreme::assign('metal', $metal);
        Xtreme::assign('crystal', $crystal);
        Xtreme::assign('deuterium', $deuterium);
        Xtreme::assign('darkmatter', pretty_number($this->CurrentUser["darkmatter"]));
        Xtreme::assign('show_umod_notice', $umod);
        Xtreme::assign('image', $this->CurrentPlanet['image']);
        Xtreme::assignGroupToGroup('topnav', 'topnav/main', 'main_page', 'topnav');
    }
}

?>
