<?php
// Title
$title_page = 'eTunnel';

// js aggiuntivo (se necessario)
//$custom_js = '**.js';

// css aggiuntivo (se necessario)
//$custom_css = '**.css';

class Main extends AppController {
    public $components = array('Menu');
    private $cfg_file = '/data/embed/etunnel/etunnel_cfg.json';
    //private $cfg_file = '/tmp/etunnel_cfg.json';
    
    function EsBefore() {
        // setup menu, left and right
        TemplVar('menu_left', $this->Menu->Left());
        TemplVar('menu_left_active', -1);
        TemplVar('menu_right', $this->Menu->Right());
        TemplVar('menu_right_active', -1);
    }
    
	function Index() {
        TemplVar('menu_left_active', -1);
        $str = file_get_contents(RootDir().'/../../app.json');
        $appl = json_decode($str, true);
        ViewVar('cfg', FALSE);
        ViewVar('url', '');
        $post = TRUE;
        if (file_exists($this->cfg_file)) {
            $str = file_get_contents($this->cfg_file);
            $cfg = json_decode($str, true);
            if (isset($cfg['idn'])) {
                $post = FALSE;
                ViewVar('idn', $cfg['idn']);
                ViewVar('cfg', TRUE);
            }
            if (isset($cfg['scheme']))
                ViewVar('url', $cfg['scheme'].'://'.$cfg['host'].$cfg['path']);
        }
        if ($post && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = EsSanitize($_POST);
            if (isset($_POST['url'])) {
                $url = parse_url($_POST['url']);
                if ($url['scheme'] == null) {
                    EsMessage(_('Errore, URL errato'));
                    EsRedir('main');
                }
                
                $cfg['scheme'] = $url['scheme'];
                $cfg['host'] = $url['host'];
                if ($url['port'] != null)
                    $cfg['host'] .= ':'.$url['port'];
                if ($url['path'] == null)
                    $cfg['path'] = '';
                else
                    $cfg['path'] = $url['path'];
                $file = fopen($this->cfg_file, 'w');
                if ($file !== FALSE) {
                    fwrite($file, json_encode($cfg));
                    fclose($file);
                    EsMessage(_('Impostazioni salvate'));
                }
                else {
                    EsMessage(_('Errore nel salvataggio delle impostazioni'));
                }
                EsRedir('main');
            }
        }
        if (!isset($cfg['sn']))
            $sn = '---';
        else
            $sn = $cfg['sn'];
        ViewVar('sn', $sn);
        ViewVar('appl', $appl);
	}

    function Status() {
        EsTemplate('none');
        if (file_exists($this->cfg_file)) {
            $str = file_get_contents($this->cfg_file);
            $cfg = json_decode($str, true);
            if (isset($cfg['idn'])) {
                ViewVar('data', 1);
                return ;
            }
        }
        ViewVar('data', 0);
    }
    

    function Credits() {
        TemplVar('menu_right_active', 0);
    }
}
