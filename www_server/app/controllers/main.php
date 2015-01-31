<?php
// Title
$title_page = 'eTunnel';

// js aggiuntivo (se necessario)
//$custom_js = '**.js';

// css aggiuntivo (se necessario)
//$custom_css = '**.css';

class Main extends AppController {
    public $models = array('nodes');
    public $components = array('Menu');
    
    private function RegNodes() {
        $reg_json = DataDir().'/reg.json';
        if (file_exists($reg_json)) {
            $str = file_get_contents($reg_json);
            $reg = json_decode($str, true);
            // elimnazione dei dati vecchi 24 ore
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
    }
    
	function Index() {
        TemplVar('menu_left_active', -1);
        $str = file_get_contents(RootDir().'/../../app.json');
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
        ViewVar('cfg', $cfg);
        ViewVar('appl', $appl);
	}

    function Start() {
        if (!SesVarCheck('node_id'))
            EsRedir('main', 'nodes_list');
        $node_id = SesVarGet('node_id');
        $this->nodes->StartStop($node_id, SesVarGet('user_type'));
        EsMessage(_("Tunnel in avvio"));
        EsRedir('main', 'nodes_list');
    }

    function Stop() {
        if (!SesVarCheck('node_id'))
            EsRedir('main', 'nodes_list');
        $node_id = SesVarGet('node_id');
        $this->nodes->StartStop($node_id, -1);
        EsMessage(_("Tunnel in arresto"));
        EsRedir('main', 'nodes_list');
    }

    function Tunnels() {
        if (!isset($_GET['id'])) 
            EsRedir('main', 'nodes_list');
        $id = $_GET['id'];
        $tunnels = $this->nodes->Tunnels($id, SesVarGet('user_type'));
        SesVarSet('node_id', $id);
        ViewVar('tunnels', $tunnels);
    }

    function TunnelAdd() {
        if (!SesVarCheck('node_id'))
            EsRedir('main', 'nodes_list');
        $node_id = SesVarGet('node_id');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = EsSanitize($_POST);
            if ($this->TunneCheck() == TRUE) {
                $this->nodes->TunnelAdd($node_id, $_POST['name'], $_POST['sport'], $_POST['dhost'], $_POST['dport']);
                EsMessage(_("Tunnel inserito"));
                EsRedir('main', 'tunnels', 'id='.$node_id);
            }
        }
    }
    
    function TunnelRemove() {
        if (!SesVarCheck('node_id') || !isset($_GET['id']))
            EsRedir('main', 'nodes_list');
        $node_id = SesVarGet('node_id');
        $id = $_GET['id'];
        $this->nodes->TunnelRemove($id);
        EsMessage(_("Tunnel rimosso"));
        EsRedir('main', 'tunnels', 'id='.$node_id);
    }
    
    function NodesList() {
        $nodes = $this->nodes->Get(SesVarGet('user_id'), SesVarGet('user_type'));
        ViewVar('nodes', $nodes);
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
            if ($node == FALSE) {                
                sleep(5); // per evitare che lo stesso client sovracarichi il sistema
                die();
            }
            else {
                // json con i dati per la messaggistica cifrata
                $resp = array('version' => "1.0", 'next_call' => $node['freq']);
                $utype = $node['start_utype'];
                if ($utype != -1 && $st == 0) {
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
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, time(), time());
                }
                elseif ($st == 1 && $utype == -1) {
                    $resp['action'] = 'stop';
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, time());
                }
                else {
                    $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, time());
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
                $this->nodes->UpdateStatus($node['id'], $_SERVER['REMOTE_ADDR'], $st, time());
                if (isset($reg[$sn])) {
                    unset($reg[$sn]);
                    $str = json_encode($reg);
                    file_put_contents(DataDir().'/reg.json', $str);
                }
                else { // cambio chiavi
                    $enckey = md5($node['idn'].time());
                    $this->nodes->Update($node['id'], $node['name'], $node['descrip'], $node['sn'], $node['idn'], $enckey, $node['phone']);
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

    function Credits() {
        TemplVar('menu_right_active', 2);
    }
}
