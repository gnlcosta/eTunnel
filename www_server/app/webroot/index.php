<?php
/**
 * Essential : Minimalistic PHP Framework
 * Copyright 2013-2014, Gianluca Costa (http://www.xplico.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

//DON'T MODIFY THIS FILE

// root dirs initializations
$sa = strlen('app/webroot/index.php');
$sb = strlen($_SERVER['DOCUMENT_ROOT']);
$ROOT_APP = substr($_SERVER['SCRIPT_FILENAME'], $sb, -$sa);
$WEB_ROOT_DIR = $_SERVER['DOCUMENT_ROOT'];
$ROOT_DIR  = substr($_SERVER['SCRIPT_FILENAME'], 0, -$sa);
if ($ROOT_APP == '')
    $ROOT_APP = '/';

// configuration loading
if (file_exists('../configs/configs.php')) {
    include '../configs/configs.php';
}

// session initialization ore rescuse
if (isset($sessiondir)) {
    session_save_path($sessiondir);
}
else {
    session_save_path($ROOT_DIR.'/app/tmp/sessions/');
}
if (isset($es_session)) {
    session_name($es_session);
}
session_start();

// custom template empty (the controllor can upfate it with EsTemplate function)
$template = null;

// disabilito la cache del browser
session_cache_limiter('nocache');
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.

// caricamento delle funzioni di libreria
include '../../core/lib/essential_lib.php';

// per la generazione della pagina
ViewVar('ROOT_APP', $ROOT_APP);

// scelta della lingua
if (SesVarCheck('locale')) {
	$locale = SesVarGet('locale');
}
else {
    $locale = 'it_IT.utf-8';
}
setlocale(LC_ALL, $locale);
putenv('LC_ALL='.$locale);
putenv('LANG='.$locale); 
putenv('LANGUAGE='.$locale);

// Specify location of translation tables
bindtextdomain('default', dirname(__FILE__).'/../locale');
bind_textdomain_codeset('default', 'UTF-8');
// Choose domain
textdomain('default');

// models and components loading
LoadModules('../models');
LoadModules('../controllers/components');

// controler loading
include '../../core/lib/controller.php';

// identify controller and page requested (by url)
list($controller, $page) = ControllerPage();

// check controller file
if (!file_exists('../controllers/'.$controller.'.php')) {
    $controller = $default_controller;
    $page = 'error';
}

// specific controler loading
include '../controllers/'.$controller.'.php';

// controller class name and member function (ie page name)
$cntr_class = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
$page_function = str_replace(' ', '', ucwords(str_replace('_', ' ', $page)));

if (!class_exists($cntr_class)) {
    SesVarSet('esalert', _('La Classe della pagina richiesta non esiste'));
    EsRedir();
}
$contr = new $cntr_class();
if (!method_exists($contr, $page_function)) {
    SesVarSet('esalert', _('La pagina richiesta non esiste'));
    $page = 'index';
    $page_function = 'Index';
}

// setup controller (its models and components)
$contr->__ModulesInit();
$contr->EsBefore();
$contr->$page_function();

if (!isset($title_page)) {
    if ($controller != $page)
        $title_page = '..:: '.ucfirst($cntr_class).'->'.ucfirst($page_function).' ::..';
    else
        $title_page = '..:: '.ucfirst($cntr_class).' ::..';
}

// template variables defined by the controller
foreach ($_templ_vars as $key => $val) {
    $$key = $val;
}

// page loading and elaboration
if (file_exists('../pages/'.$controller.'/'.$page.'.php'))
    $page_content = LoadPageContent('../pages/'.$controller.'/'.$page.'.php');
else
    $page_content = '';

// setup alert message
if (SesVarCheck('esalert')) {
	$esalert = SesVarGet('esalert');
}
SesVarUnset('esalert');

// template loading and visualization
if ($template == null)
    include '../template/default.php';
else
    include '../template/'.$template.'.php';

