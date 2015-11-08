<?php
// Title
$title_page = 'eTunnel';

// js aggiuntivo (se necessario)
//$custom_js = '**.js';

// css aggiuntivo (se necessario)
//$custom_css = '**.css';

class Main extends AppController {
    public $models = array('nodes', 'users');
    public $components = array('Menu');
    private $utype;
    
    private function RegNodes() {
        $reg_json = DataDir().'/reg.json';
        if (file_exists($reg_json)) {
            $str = file_get_contents($reg_json);
            $reg = json_decode($str, true);
            // eliminazione dei dati vecchi 24 ore
            foreach ($reg as $sn => $data) {
                if ($data['time'] < time()-(24*3600))
                    unset($reg[$sn]);
            }
        }
        else {
            $reg = array();
        }
        return $reg;
    }

    private function NodeCheck() {
        $check = TRUE;
        // verifica dati
        if ($_POST['name'] == '') {
            EsMessage(_("Errore: Indicare un nome per il Nodo"));
            $check = FALSE;
        }
        else if ($_POST['phone'] != '' && !is_numeric($_POST['phone'])) {
            EsMessage(_("Errore: Indicare un numero di telefono valido"));
            $check = FALSE;
        }
        if (!isset($_POST['phone']))
            $_POST['phone'] = '';
        if (!isset($_POST['descrip']))
            $_POST['descrip'] = '';
        if (!isset($_POST['sms_updown']))
            $_POST['sms_updown'] = 0;
        else
            $_POST['sms_updown'] = intval($_POST['sms_updown']);
        if (!isset($_POST['auto_start']))
            $_POST['auto_start'] = 0;
        else
            $_POST['auto_start'] = intval($_POST['auto_start']);
        if (!isset($_POST['disable']))
            $_POST['disable'] = 0;
        else
            $_POST['disable'] = intval($_POST['disable']);
        
        return $check;
    }

    private function TunneCheck() {
        $check = TRUE;
        // verifica dati
        if ($_POST['name'] == '') {
            EsMessage(_("Errore: Indicare un nome per il Tunnel"));
            $check = FALSE;
        }
        else if ($_POST['sport'] == '' || !is_numeric($_POST['sport'])) {
            EsMessage(_("Errore: Indicare la porta di accesso"));
            $check = FALSE;
        }
        else if ($_POST['dhost'] == '') {
            EsMessage(_("Errore: Indicare l'IP del dispositivo remoto"));
            $check = FALSE;
        }
        else if ($_POST['dport'] == '' || !is_numeric($_POST['dport'])) {
            EsMessage(_("Errore: Indicare la porta del dispositivo remoto"));
            $check = FALSE;
        }
        
        return $check;
    }
    
    function EsBefore() {
        // setup menu, left and right
        TemplVar('menu_left', $this->Menu->Left());
        TemplVar('menu_left_active', -1);
        TemplVar('menu_right', $this->Menu->Right());
        TemplVar('menu_right_active', -1);
        TemplVar('title', '---');
        if (!SesVarCheck('user_type') && EsPage() != 'node')
            EsRedir('user', 'login');
        $reg = $this->RegNodes();
        if (count($reg) != 0) {
            TemplVar('menu_left', $this->Menu->Left(array('help' => 'Nodi registrati', 'link' => RootApp().'main/regist_list', 'title' => 'Nuovi nodi')));
        }
        $str = file_get_contents(RootDir().'/../data/app.json');
        $appl = json_decode($str, true);
        TemplVar('app_version', $appl['version']);
        ViewVar('app_version', $appl['version']);
        $this->utype = SesVarGet('user_type');
    }
    
	function Index() {
        TemplVar('menu_left_active', -1);
        $str = file_get_contents(DataDir().'/app.json');
        $appl = json_decode($str, true);
        $cfg = FALSE;
        $ssh_cfg = DataDir().'/server.json';
        if (file_exists($ssh_cfg)) {
            $str = file_get_contents($ssh_cfg);
            $scfg = json_decode($str, true);
            if (isset($scfg['server']) && isset($scfg['user']) && isset($scfg['password']) && isset($scfg['sshport'])) {
                $cfg = TRUE;
            }
        }
        if ($cfg) {
            EsRedir('main', 'nodes_list');
        }
        ViewVar('cfg', $cfg);
        ViewVar('appl', $appl);
	}

    function Start() {
        EsTemplate('esmsg');
        if (!SesVarCheck('node_id') && !isset($_GET['id'])) {
            EsMessage(_("Azione non valida"));
            return;
        }
        if (isset($_GET['id']))
            $node_id = $_GET['id'];
        else
            $node_id = SesVarGet('node_id');
        $this->nodes->StartStop($node_id, SesVarGet('user_type'));
        EsMessage(_("Tunnel in avvio"));
    }

    function Stop() {
        EsTemplate('esmsg');
        if (!SesVarCheck('node_id') && !isset($_GET['id'])) {
            EsMessage(_("Azione non valida"));
            return;
        }
        if (isset($_GET['id']))
            $node_id = $_GET['id'];
        else
            $node_id = SesVarGet('node_id');
        $this->nodes->StartStop($node_id, -1);
        EsMessage(_("Tunnel in arresto"));
    }

    function Tunnels() {
        if (!isset($_GET['id']) || $this->utype == 3) { 
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $id = $_GET['id'];
        $tunnels = $this->nodes->Tunnels($id, SesVarGet('user_type'));
        SesVarSet('node_id', $id);
        ViewVar('node_id', $id);
        ViewVar('tunnels', $tunnels);
        ViewVar('levels', $this->users->Types());
        ViewVar('user_type', SesVarGet('user_type'));
    }

    function TunnelAdd() {
        if (!SesVarCheck('node_id') || $this->utype > 1) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $node_id = SesVarGet('node_id');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = EsSanitize($_POST);
            if ($this->TunneCheck() == TRUE) {
                $this->nodes->TunnelAdd($node_id, $_POST['name'], $_POST['sport'], $_POST['dhost'], $_POST['dport'], $_POST['utype']);
                EsMessage(_("Tunnel inserito"));
                EsRedir('main', 'tunnels', 'id='.$node_id);
            }
        }
        ViewVar('node_id', $node_id);
        ViewVar('levels', $this->users->Types(SesVarGet('user_type')));
    }

    function TunnelEdit() {
        if (!SesVarCheck('node_id') || $this->utype > 1) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $node_id = SesVarGet('node_id');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && SesVarCheck('tun_id')) {
            $_POST = EsSanitize($_POST);
            if ($this->TunneCheck() == TRUE) {
                $this->nodes->TunnelUpdate(SesVarGet('tun_id'), $node_id, $_POST['name'], $_POST['sport'], $_POST['dhost'], $_POST['dport'], $_POST['utype']);
                EsMessage(_("Tunnel modificato"));
                EsRedir('main', 'tunnels', 'id='.$node_id);
            }
        }
        if (!isset($_GET['id']))
            EsRedir('main', 'nodes_list');
        $id = $_GET['id'];
        SesVarSet('tun_id', $id);
        $tunnel = $this->nodes->Tunnel($id, SesVarGet('user_type'));
        ViewVar('node_id', $node_id);
        ViewVar('tunnel', $tunnel);
        ViewVar('levels', $this->users->Types(SesVarGet('user_type')));
    }
    
    function TunnelRemove() {
        if (!SesVarCheck('node_id') || !isset($_GET['id']) || $this->utype > 1) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $node_id = SesVarGet('node_id');
        $id = $_GET['id'];
        $this->nodes->TunnelRemove($id);
        EsMessage(_("Tunnel rimosso"));
        EsRedir('main', 'tunnels', 'id='.$node_id);
    }
    
    function NodesList() {
        $nodes = $this->nodes->Get(SesVarGet('user_id'), SesVarGet('user_type'));
        foreach ($nodes as &$node) {
            if ($node['lastmsg'] < time()-2*$node['freq'] && $node['tunnelon']) {
                $node['tunnelon'] = 0;
                $this->nodes->UpdateTunnelSt($node['id'], 0);
            }
        }
        SesVarUnset('node_id');
        ViewVar('nodes', $nodes);
    }
    
    function NodesListUpdate() {
        $nodes = $this->nodes->Get(SesVarGet('user_id'), SesVarGet('user_type'));
        $data = array();
        foreach ($nodes as $node) {
            if ($node['lastmsg'] < time()-2*$node['freq'] && $node['tunnelon']) {
                $node['tunnelon'] = 0;
                $this->nodes->UpdateTunnelSt($node['id'], 0);
            }
            $nd = array('id' => $node['id'], 'lmsg' => date("Y-m-d", $node['lastmsg']), 'ip' => $node['ip'], 'st' => 1, 'tunnel' => $node['tunnelon']);
            if ($node['lastmsg'] < time()-2*$node['freq'])
                $nd['st'] = 0;
            if ((!$node['tunnelon'] && $node['start_utype'] != -1) || ($node['tunnelon'] && $node['start_utype'] == -1))
                $nd['tunnel'] = 2;
            $data[] = $nd;
        }
        if ($nodes == FALSE)
            $data = array('e' => 1);
        EsTemplate('none');
        ViewVar('data', json_encode($data));
    }
    
    function NodeSettings() {
        if ($this->utype > 1) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && SesVarCheck('node_id')) {
            $_POST = EsSanitize($_POST);
            $id = SesVarGet('node_id');
            $data = $this->NodeCheck($_POST);
            if ($data == FALSE) {
                EsRedir('main', 'node_settings', 'id='.$id);
            }
            $this->nodes->Update($id, $_POST['name'], $_POST['descrip'], $_POST['phone'], $_POST['sms_updown'], $_POST['auto_start'], $_POST['disable']);
            EsMessage(_("Impostazioni modificare"));
            EsRedir('main', 'nodes_list');
        }
        else if (!isset($_GET['id'])) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $id = $_GET['id'];
        SesVarSet('node_id', $id);
        $node = $this->nodes->Node($id);
        ViewVar('node', $node);
    }
        
    function RegistList() {
        $reg = $this->RegNodes();
        $nodes = array();
        foreach ($reg as $sn => $node) {
            $nodes[] = array('descrip' => '', 'name' => $node['ip'], 'sn' => $sn, 'lastmsg' => $node['time']);
        }
        ViewVar('nodes', $nodes);
    }

    function NodeAdd() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = EsSanitize($_POST);
            $reg = $this->RegNodes();
            $data = $this->NodeCheck($_POST);
            if ($data == FALSE || !isset($_POST['sn']) || !isset($reg[$_POST['sn']])) {
                EsRedir('main', 'regist_list');
            }
            $idn = md5($_POST['sn'].time());
            $enckey = md5($idn.time());
            $this->nodes->Add($_POST['name'], $_POST['descrip'], $_POST['sn'], $idn, $enckey, $_POST['phone']);
            EsMessage(_("Nodo Aggiunto"));
            EsRedir('main', 'nodes_list');
        }
        else {
            if (!isset($_GET['sn'])) 
                EsRedir('user', 'regist_list');
            ViewVar('sn', $_GET['sn']);
        }
    }
    
    function NodeRemove() {
        if (!isset($_GET['id']) || $this->utype == 3) { 
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        if (isset($_GET['id'])) {
            $this->nodes->Remove($_GET['id']);
            EsMessage(_("Nodo rimosso"));
        }
        EsRedir('main', 'nodes_list');
    }

    function Node() {
        session_unset();
        session_destroy();
        
        //$var = print_r($_GET, true);
        //file_put_contents('/tmp/get.txt', $var);
        $agent = explode('/', $_SERVER['HTTP_USER_AGENT']);
        if ($agent[0] != 'eTunnel') {
            EsRedir('user', 'login');
            die();
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
            die();
        if (!isset($_GET['sn']) && !isset($_GET['idn']))
            die();
        EsTemplate('none');
        if (isset($_GET['idn']) && isset($_GET['st'])) {
            $idn = $_GET['idn'];
            if ($_GET['st'] == 'on')
                $st = 1;
            else
                $st = 0;
            // verifica esistenza nel DB
            $node = $this->nodes->GetIdn($idn);
            if ($node == FALSE || $node['disable']) {                
                sleep(5); // per evitare che lo stesso client sovracarichi il sistema
                die();
            }
            else {
                // json con i dati per la messaggistica cifrata
                $resp = array('version' => "1.0", 'next_call' => $node['freq']);
                $utype = $node['start_utype'];
                $timenow = time();
                // auto start
                if ($node['auto_start'] && $utype == -1 && $node['lastmsg']+180 < $timenow) { // 3 min di silenzio allora riavvua il tunnel automatico
                    $utype = 3;
                    $this->nodes->StartStop($node['id'], $utype);
                }
                if ($utype != -1 && $st == 0 && $node['tunnelon'] == 0) {
                    $ssh_cfg = DataDir().'/server.json';
                    if (file_exists($ssh_cfg)) {
                        $str = file_get_contents($ssh_cfg);
                        $scfg = json_decode($str, true);
                        $resp['params'] = $scfg;
                        $resp['action'] = 'start';
                    }
                    $tunnels = $this->nodes->Tunnels($node['id'], $utype);
                    $resp['tunnels'] = array();
                    foreach ($tunnels as $tunnel) {
                        $resp['tunnels'][] = array('name' => $tunnel['name'], 'sport' => $tunnel['sport'], 'dsthost' => $tunnel['dhost'], 'dstport' => $tunnel['dport']);
                    }
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, $timenow, $timenow);
                    $resp['next_call'] = 2; // notifica veloce
                }
                elseif (($st == 1 && $utype == -1) || ($utype != -1 && $st == 0)) {
                    $resp['action'] = 'stop';
                    if ($utype != -1)
                        $this->nodes->StartStop($node['id'], -1); // stop
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, $timenow);
                    $resp['next_call'] = 2; // notifica veloce
                }
                else {
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, $timenow);
                }
                $str = json_encode($resp);
                $resp_file = '/tmp/resp_'.$idn.'.json';
                file_put_contents($resp_file, $str);
                $cmd = '/usr/bin/ccrypt -f -e -K '.$node['enckey'].' '.$resp_file;
                system($cmd);
                $resp_file = $resp_file.'.cpt';
                if (file_exists($resp_file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Expires: 0');
                    header('Content-Disposition: filename=resp.json');
                    header('Content-Length: '.filesize($resp_file));
                    @readfile($resp_file);
                    unlink($resp_file);
                    die();
                }
            }
        }
        elseif (isset($_GET['sn']) && isset($_GET['ck'])) {
            $sn = $_GET['sn'];
            $ck = $_GET['ck'];
            // verifica credenziali
            $ck_now = md5($sn.$agent[0].'/'.$agent[1]);
            if ($ck_now != $ck) {
                sleep(10); // per evitare che lo stesso client sovracarichi il sistema
                die();
            }
            sleep(3); // per evitare che lo stesso client sovracarichi il sistema
            
            $reg = $this->RegNodes();
            $node = $this->nodes->GetSn($sn);
            if ($node === FALSE) { // salvataggio info per abilitazione del nodo
                $reg[$sn] = array('time' => time(), 'ip' => $_SERVER['REMOTE_ADDR']);
                $str = json_encode($reg);
                file_put_contents(DataDir().'/reg.json', $str);
            }
            else { // invio chiave di cifratura e id nodo
                if (isset($reg[$sn])) {
                    unset($reg[$sn]);
                    $str = json_encode($reg);
                    file_put_contents(DataDir().'/reg.json', $str);
                }
                else { // cambio chiavi
                    $enckey = md5($node['idn'].time());
                    $this->nodes->UpdateEncKey($node['id'], $enckey);
                    $node = $this->nodes->GetSn($sn);
                }
                // json con i dati per la messaggistica cifrata
                $resp = array('idn' => $node['idn'], 'enckey' => $node['enckey']);
                $str = json_encode($resp);
                $resp_file = '/tmp/reg_'.$sn.'.json';
                file_put_contents($resp_file, $str);
                $cmd = '/usr/bin/ccrypt -f -e -K '.$sn.' '.$resp_file;
                system($cmd);
                $resp_file = $resp_file.'.cpt';
                if (file_exists($resp_file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Expires: 0');
                    header('Content-Disposition: filename=resp.json');
                    header('Content-Length: '.filesize($resp_file));
                    @readfile($resp_file);
                    unlink($resp_file);
                }
            }
            die();
        }
        die();
    }
    
    function UserDelete() {
        if (!isset($_GET['id']) || $this->utype == 3) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        if (isset($_GET['id'])) {
            $user_id = $_GET['id'];
            $user_info = $this->users->SearchByID($user_id);
            if ($user_info == FALSE) {
                EsMessage(_("Operazione non consentita"));
                EsRedir('main', 'nodes_list');
            }
            $this->nodes->RemoveUser($user_id);
            EsRedir('user', 'delete', 'id='.$user_id);
        }
    }
    
    function UserNodes() {
        if (!isset($_GET['id']) || $this->utype == 3) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $user_id = $_GET['id'];
        $user_info = $this->users->SearchByID($user_id);
        if ($user_info == FALSE) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $user_nodes = $this->nodes->UserNodes($user_id);
        $nodes = $this->nodes->Get(SesVarGet('user_id'), 1);
        foreach ($nodes as &$node) {
            $node['enabled'] = FALSE;
            if ($user_nodes !== FALSE) {
                foreach ($user_nodes as $unode) {
                    if ($unode['node_id'] == $node['id']) {
                        $node['enabled'] = TRUE;
                        break;
                    }
                }
            }
        }
        SesVarSet('user_id', $user_id);
        ViewVar('user_info', $user_info);
        ViewVar('nodes', $nodes);
    }
    
    function UserAddNode() {
        if (!isset($_GET['id']) || $this->utype == 3 || !SesVarCheck('user_id')) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $user_id = SesVarGet('user_id');
        $this->nodes->UserAddNode($user_id, $_GET['id']);
        EsMessage(_("Nodo Abilitato"));
        EsRedir('main', 'user_nodes', 'id='.$user_id);
    }
    
    function UserDelNode() {
        if (!isset($_GET['id']) || $this->utype == 3 || !SesVarCheck('user_id')) {
            EsMessage(_("Operazione non consentita"));
            EsRedir('main', 'nodes_list');
        }
        $user_id = SesVarGet('user_id');
        $this->nodes->UserDelNode($user_id, $_GET['id']);
        EsMessage(_("Nodo Disabilitato"));
        EsRedir('main', 'user_nodes', 'id='.$user_id);
    }

    function Credits() {
        TemplVar('menu_right_active', 2);
    }
}
