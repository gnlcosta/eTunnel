<?php

// Title
$title_page = 'eTunnel';

// js aggiuntivo (se necessario)
//$custom_js = '**.js';

// css aggiuntivo (se necessario)
//$custom_css = '**.css';

class User extends AppController {
    public $models = array('users');
    public $components = array('Menu', 'PwdRandom', 'Log');
    public $usr_type = -1;
    
    function EsBefore() {
        // setup menus
        TemplVar('menu_left', $this->Menu->Left());
        TemplVar('menu_left_active', -1);
        TemplVar('menu_right', $this->Menu->Right());
        TemplVar('menu_right_active', 1);
        TemplVar('title', '---');
        if (!SesVarCheck('user_type')) {
            if (EsPage() != 'login')
                EsRedir('user', 'login');
            else {
                TemplVar('user', '---');
            }
        }
        else {
            TemplVar('user', SesVarGet('user'));
            $this->usr_type = SesVarGet('user_type');
        }
        $str = file_get_contents(RootDir().'/../data/app.json');
        $appl = json_decode($str, true);
        TemplVar('app_version', $appl['version']);
        ViewVar('app_version', $appl['version']);
    }
    
    function Login() {
        global $log_dir;
        if (!file_exists($log_dir))
            @mkdir($log_dir, 0777, TRUE);
        SesVarSet('kbtouch', FALSE);
        TemplVar('title', 'Login');
        if ($this->usr_type == -1) {
            TemplVarUnset('menu_left');
            TemplVarUnset('menu_right');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['user']) || !isset($_POST['password'])) {
                EsMessage(_('Inserire il nome utente  e la password'));
                EsRedir('user', 'login');
            }
            $user = EsSanitize($_POST['user']);
            $password = EsSanitize($_POST['password']);
            $udata = $this->users->Search($user);
            if ($udata !== FALSE) {
                if (password_verify($password, $udata['password'])) {
                    SesVarSet('user_id', $udata['id']);
                    SesVarSet('user', $udata['user']);
                    SesVarSet('user_type',$udata['type'] );
                    $this->Log->Append($log_dir.'/user.log', 'Login utente: '.$udata['user']);
                    EsRedir('main');
                }
                else if ($udata['password'] == 'random') {
                    $pwd = $this->PwdRandom->Password();
                    if ($this->PwdRandom->Check($password, $pwd)) {
                        SesVarSet('user_id', $udata['id']);
                        SesVarSet('user', $udata['user']);
                        SesVarSet('user_type',$udata['type'] );
                        $this->Log->Append($log_dir.'/user.log', 'Login utente: '.$udata['user']);
                        EsRedir('main');
                    }
                }
            }
            sleep(1);
            EsMessage(_('Nome utente o Password errata/i'));
            EsRedir('user', 'login');
        }
    }
    
    function Logout() {
        global $log_dir;
        $this->Log->Append($log_dir.'/user.log', 'Logout utente: '.SesVarGet('user'));

        session_unset();
        session_destroy();
        session_start();
        EsMessage(_('Logout eseguito'));
        EsRedir('user', 'login');
    }
    
    function SendPassword() {
    }
    
    function ChangePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && SesVarCheck('usredit')) {
            $id = SesVarGet('usredit');
            $udata = $this->users->SearchByID($id);
            if ($udata !== FALSE && isset($_POST['password_rep']) && isset($_POST['password'])) {
                if ($_POST['password_rep'] != $_POST['password'])
                    EsMessage(_('Le due password non coincidono'));
                else if (password_verify($_POST['password'], $udata['password']))
                    EsMessage(_("La password deve essere diversa dall'attuale"));
                else {
                    $this->users->Save($id, array('password' => password_hash($_POST['password'], PASSWORD_DEFAULT)));
                    EsMessage(_('Password Cambiata'));
                    EsRedir('user');
                }
            }
        }
        else if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
             EsRedir('user');
        }
        else {
            $id = $_GET['id'];
            $udata = $this->users->SearchByID($id);
        }
        TemplVar('title', 'Modifica Password');
        $usr_id = SesVarGet('user_id');
        if ($udata !== FALSE && ($udata['type'] > $this->usr_type || $udata['id'] == $usr_id || $this->users->FullAccess($usr_id)) && $udata['password'] != 'random') {
            SesVarSet('usredit', $udata['id']);
        }
        else {
            EsMessage(_('Operazione non consentita'));
            EsRedir('user');
        }
    }
    
    function Add() {
        if ($this->usr_type == 3) {
            EsMessage(_('Acesso negato'));
            EsRedir('main');
        }
        TemplVar('title', 'Nuovo Utente');
        TemplVar('menu_left_active', 1);
        $types = $this->users->Types($this->usr_type);
        ViewVar('types', $types);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['user']) && isset($_POST['password']) && isset($_POST['type'])) {
                $new = array();
                $new['user'] = EsSanitize($_POST['user']);
                $new['password'] = password_hash(EsSanitize($_POST['password'], PASSWORD_DEFAULT));
                $udata = $this->users->Search($new['user']);
                if ($udata !== FALSE) {
                    EsMessage(_('Nome Utente già presente'));
                }
                else if (!is_numeric($_POST['type']) || $_POST['type'] < $this->usr_type) {
                    EsMessage(_('Tipo utente non valido'));
                }
                else if ($_POST['email'] != '' && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
                    EsMessage(_('Indirizzo email non valido'));
                else {
                    $new['email'] = $_POST['email'];
                    $new['type'] = $_POST['type'];
                    $this->users->Add($new);
                    EsRedir('user');
                }
            }
            else {
                EsMessage(_('Nome Utente e Password sono necessari'));
            }
        }
    }
    
    function Edit() {
        if ($this->usr_type == 3) {
            EsMessage(_('Acesso negato'));
            EsRedir('main');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && SesVarCheck('usredit')) {
            $id = SesVarGet('usredit');
            if (isset($_POST['email'])) {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $new = array('email' => $_POST['email']);
                    $this->users->Save($id, $new);
                    EsMessage(_('Dati utente salvati'));
                    EsRedir('user');
                }
                else {
                    EsMessage(_('Indirizzo email non valido'));
                }
            }
        }
        else if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
             EsRedir('user');
        }
        else
            $id = $_GET['id'];
        TemplVar('title', 'Modifica Utente');
        $udata = $this->users->SearchByID($id);
        $usr_id = SesVarGet('user_id');
        if ($udata !== FALSE && ($udata['type'] > $this->usr_type || $udata['id'] == $usr_id || $this->users->FullAccess($usr_id))) {
            ViewVar('user', $udata);
            SesVarSet('usredit', $udata['id']);
        }
        else {
            EsMessage(_('Operazione non consentita'));
            EsRedir('user');
        }
    }
    
    function Index() {
        TemplVar('title', 'Lista Utenti');
        if ($this->usr_type != 3)
            $list = $this->users->View($this->usr_type);
        else
            $list = null;
        $types = $this->users->Types();
        ViewVar('users', $list);
        ViewVar('user_tp', $this->usr_type);
        ViewVar('user_id', SesVarGet('user_id'));
        ViewVar('types', $types);
        ViewVar('contr', $this);
    }

    function Log() {
        global $log_dir;
        TemplVar('title', _('Log Utenti'));
        $logs = $this->Log->Read($log_dir.'/user.log');
        ViewVar('logs', $logs);
    }
    
    function Delete() {
        if (!isset($_GET['id']) || !is_numeric($_GET['id']))
            EsRedir('user');
            
        $usr_id = SesVarGet('user_id');
        $udata = $this->users->SearchByID($_GET['id']);
        if ($udata !== FALSE && $this->users->Permanent($udata['id']) == FALSE) {
            if ($udata['type'] > $this->usr_type || $udata['id'] == $usr_id || $this->users->FullAccess($usr_id)) {
                $this->users->Delete($udata['id']);
                EsMessage(_('Utente rimosso'));
                if ($udata['id'] == $usr_id)
                    EsRedir('user', 'logout');
                EsRedir('user');
            }
        }
        EsMessage(_('Operazione non consentita'));
        EsRedir('user');
    }
}
