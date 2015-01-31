<?php

class Menu {
    function Left($add = null) {
        if ($add == null)
            return array();
        else
            return array($add);
    }
    
    function Right($add = null) {
        if ($add == null) {
            return array(array('help' => 'Logout', 'link' => RootApp().'user/logout', 'title' => 'Logout'),
                array('help' => 'Lista utenti', 'link' => RootApp().'user/index', 'title' => 'Utenti'),
                array('help' => 'Credits', 'link' => RootApp().'main/credits', 'title' => 'Credits')
            );
        }
        else {
            return array(array('help' => 'Logout', 'link' => RootApp().'user/logout', 'title' => 'Logout'),
                array('help' => 'Lista utenti', 'link' => RootApp().'user/index', 'title' => 'Utenti'),
                array('help' => 'Credits', 'link' => RootApp().'main/credits', 'title' => 'Credits'),
                $add
            );
        }
    }
}
