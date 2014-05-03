<?php

class Menu {
    function Left() {
        return array();
    }
    
    function Right() {
        return array(array('help' => 'Credits', 'link' => RootApp().'main/credits', 'title' => 'Credits'));
    }
}
