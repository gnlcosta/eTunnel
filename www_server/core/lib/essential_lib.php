<?php
/**
 * Essential : Minimalistic PHP Framework
 * Copyright 2013-2014, Gianluca Costa (http://www.xplico.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

$_view_vars = array();
$_templ_vars = array();
$esalert = null;

// enable log
function ErrorReport($errno, $errstr, $errfile, $errline) {
    global $php_errors;
    $php_errors[0] = "<b>Error Report:</b> [$errno] $errstr<br />";
    $php_errors[0] .= " Error on line $errline in $errfile<br />";
    $php_errors[0] .= "Stack:<br />";
    $php_errors[1] = debug_backtrace();
}

if (isset($enable_dbg) && $enable_dbg) {
	if (function_exists('set_error_handler')) {
	    set_error_handler("ErrorReport");
    }
    ini_set('display_errors', $enable_dbg);
    error_reporting(E_ALL);
}

function FromCamelCase($input) {
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
        $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
}

// Modules (Models and Contents) loagin funcion
function LoadModules($model_dir) {
    $files = scandir($model_dir);
    foreach ($files as $file) {
		if (strstr($file, '.php') != FALSE) {
	    	include $model_dir.'/'.$file;
		}
    }
}

function LoadPageContent_bis($file) {
    ob_start(); // start buffer
    include($file);
    $tmp = addslashes(ob_get_contents()); // assign buffer contents to variable
    ob_end_clean(); // end buffer and remove buffer contents
    eval("\$page_content=\"$tmp\";");
    return $page_content;
}

function LoadPageContentb($file) {
    ob_start(); // start buffer
    include($file);
    $tmp = ob_get_contents(); // assign buffer contents to variable
    ob_end_clean(); // end buffer and remove buffer contents
    return $tmp;
}

function LoadPageContent($file) {
    global $_view_vars;
    
    // espandiamo le variabili
    foreach ($_view_vars as $key => $val) {
        $$key = $val;
    }
    
    ob_start(); // start buffer
    include($file);
    $tmp = ob_get_clean(); // end buffer and remove buffer contents
    return $tmp;
}

function ViewVar($name, $value) {
    global $_view_vars;
    
    $_view_vars[$name] = $value;
}

function ViewVarUnset($name) {
    global $_view_vars;
    
    unset($_view_vars[$name]);
}

function TemplVar($name, $value) {
    global $_templ_vars;
    
    $_templ_vars[$name] = $value;
}

function TemplVarUnset($name) {
    global $_templ_vars;
    
    unset($_templ_vars[$name]);
}

function ControllerPage() {
    global $default_controller;
    $controller = $default_controller;
    $page = 'index';
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (isset($_GET['url']) && $_GET['url'] != '') {
            $elements = explode("/", $_GET['url']);
            $controller = $elements[0];
            if (isset($elements[1])) {
                $page = $elements[1];
    	    }
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if ($_SERVER['QUERY_STRING'] != '') {
            $qs = explode('=', $_SERVER['QUERY_STRING']);
            if ($qs[0] == 'url') {
                $first = explode('&', $qs[1]);
                $elements = explode('/', $first[0]);
                $controller = $elements[0];
                if (isset($elements[1])) {
                    $page = $elements[1];
        		}
            }
        }
    }
    
    return array($controller, $page);
}

function SesVarSet($name, $value) {
    $_SESSION[$name] = $value;
}

function SesVarGet($name) {
    if (isset($_SESSION[$name]))
        return $_SESSION[$name];
    
    return false;
}

function SesVarCheck($name) {
    if (isset($_SESSION[$name]))
        return true;
    return false;
}

function SesVarUnset($name) {
    unset($_SESSION[$name]);
}

function SesDelete() {
    session_destroy();
}

function EsRedir($controler = null, $page = null, $param = null) {
    global $ROOT_APP;
    if ($controler == null) {
        header('Location: '.$ROOT_APP);
    }
    else if ($page == null || $page == '') {
        header('Location: '.$ROOT_APP.$controler);
    }
    else {
        if ($param == null) {
            header('Location: '.$ROOT_APP.$controler.'/'.$page);
        }
        else {
            header('Location: '.$ROOT_APP.$controler.'/'.$page.'?'.$param);
        }
    }
    die();
}

function EsNewUrl($controler, $page = null, $param = null) {
    global $ROOT_APP;
    if ($page == null) {
        return $ROOT_APP.$controler;
    }
    else {
        if ($param == null)
            return $ROOT_APP.$controler.'/'.$page;
        else
            return $ROOT_APP.$controler.'/'.$page.'?'.$param;
    }
}

function EsPage() {
    global $page;
    return $page;
}

function EsSetPage($new_page) {
    global $page;
    $page = $new_page;
}

function EsMessage($msg) {
    SesVarSet('esalert', $msg);
}

function EsTemplate($tmp = null) {
    global $template;
	
    $template = $tmp;
}

function EsSanitize($var) {
    $subt = array('<', '>', '"', "'", '?');
    if (is_array($var)) {
        $ret = array();
        foreach($var as $key => $elem) {
            $ret[$key] = str_replace($subt, '', $elem);
        }
    }
    else {
        $ret = str_replace($subt, '', $var);
    }
    return $ret;
}

function RootDir() {
    global $ROOT_DIR;
    
    return $ROOT_DIR.'/app/';
}

function DataDir() {
    global $ROOT_DIR;
    global $app_dir;
    if (isset($app_dir))
        return $app_dir;
    return $ROOT_DIR.'/data/';
}

function DbDir() {
    global $db_dir;
    if (isset($db_dir))
        return $db_dir.'/';
    return DataDir();
}

function RootApp() {
    global $ROOT_APP;
    
    return $ROOT_APP;
}

function DebStop($data, $stop = true) {
    print_r($data);
    if ($stop)
        die();
}
