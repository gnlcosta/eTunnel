<?php
// debug enable/disable
$enable_dbg = true;

// PHP session
$es_session = 'etunnel'; // session name
$sessiondir = '/tmp'; // if not defined will be used "data/sessions" as session dir
$session_lifetime = 28800; //in secconds

// default controller
$default_controller = 'main';

// permanent users (number)
$permanent_user_num = 1;

$prj_name = 'eTunnel';
// $app_dir = '/tmp/';
$log_dir = '/tmp/'.$es_session.'/log';
$log_max_lines = 10000;
