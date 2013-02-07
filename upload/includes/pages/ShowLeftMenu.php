<?php

if (!defined('INSIDE'))
{
    die(header("location:../../"));
}

class ShowLeftMenu
{
    private $user;
    public function __construct($user){
        $this->user=$user;        
    }
    public function show()
    {
        global $game_config;

        Xtreme::assign('version', VERSION);
        Xtreme::assign('servername', $game_config['game_name']);
        Xtreme::assign('forum_url', $game_config['forum_url']);
        Xtreme::assign('user_rank', $this->user['total_rank']);

        if ($this->user['authlevel'] > 0)
        {
            Xtreme::assignToGroup('leftmenu', 'admin_link', 'leftmenu/adminLink');
        }
        Xtreme::assignGroupToGroup('leftmenu', 'leftmenu/main', 'main_page', 'leftmenu'); 
        
    }
}

?>