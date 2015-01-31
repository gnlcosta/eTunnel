<?php

class PwdRandom {
    function Password($date = null) {
        // customize
        return '';
    }
    
    function Check($a, $b) {
        // customize
        if ($a == $b)
            return TRUE;
        else
            return FALSE;
    }
}
