<?php

class Menu {
    function Left($add = null) {
        if ($add == null)
            return array();
        else
            return array($add);
    }
    
    function Right() {
        return array(array('help' => 'Credits', 'link' => RootApp().'main/credits', 'title' => 'Credits'));
    }
}
