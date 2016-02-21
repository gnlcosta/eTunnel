<?php
class Nodes {
    private $db;
    
    function __construct() {
        $db_file = DbDir().'nodes.db';
        if (file_exists($db_file)) {
            $this->db = new SQLite3($db_file);
            $this->db->busyTimeout(5000);
            $this->db->exec('PRAGMA foreign_keys = ON');
        }
        else {
            $this->db = new SQLite3($db_file);
            $this->db->busyTimeout(5000);
            $this->db->exec('PRAGMA foreign_keys = ON');
            $this->Create();
        }
    }
    
    function __destruct() {
        $this->db->close();
    }
    
    private function Create() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS nodes (
            id INTEGER NOT NULL PRIMARY KEY,
            creation DATETIME DEFAULT (datetime('now','localtime')),
            name TEXT NOT NULL,
            descrip TEXT,
            sn TEXT NOT NULL,
            idn INTEGER NOT NULL,
            enckey TEXT NOT NULL,
            master_enckey TEXT NOT NULL,
            phone TEXT,
            sms_updown BOOLEAN DEFAULT 0,
            auto_start BOOLEAN DEFAULT 0,
            disable BOOLEAN DEFAULT 0,
            start_utype INTEGER DEFAULT -1,
            freq INTEGER DEFAULT 5,
            ip TEXT DEFAULT '---',
            lastmsg INTEGER DEFAULT 0,
            tunnelon BOOLEAN DEFAULT 0,
            sms_cnt INTEGER DEFAULT 0,
            started INTEGER
        )");
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS node_users (
            id INTEGER NOT NULL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            node_id INTEGER NOT NULL,
            FOREIGN KEY(node_id) REFERENCES nodes(id) ON DELETE CASCADE
        )");
        
        $this->db->exec('CREATE TABLE IF NOT EXISTS tunnels (
            id INTEGER NOT NULL PRIMARY KEY,
            node_id INTEGER NOT NULL,
            utype INTEGER DEFAULT 3,
            name TEXT,
            sport INTEGER DEFAULT 0,
            dhost TEXT NOT NULL,
            dport INTEGER NOT NULL,
            FOREIGN KEY(node_id) REFERENCES nodes(id) ON DELETE CASCADE
        )');
    }

    function Get($user_id, $utype) {
        if ($utype < 3) {
            $result = $this->db->query('SELECT * FROM nodes ORDER BY name ASC;');
        }
        else {
            $result = $this->db->query('SELECT * FROM nodes, node_users WHERE nodes.id == node_users.node_id AND node_users.user_id == '.$user_id.' ORDER BY name ASC;');
        }
        if ($result !== FALSE) {
            $resp = array();
            while ($row = $result->fetchArray()) {
                $resp[] = $row;
            }
            $result->finalize();
            return $resp;
        }
        
        return FALSE;
    }

    function Tunnels($node_id, $utype) {
        $result = $this->db->query('SELECT * FROM tunnels WHERE node_id='.$node_id.' AND utype >= '.$utype.' ORDER BY sport ASC;');
        if ($result !== FALSE) {
            $tuns = array();
            while ($row = $result->fetchArray()) {
                $tuns[] = $row;
            }
            $result->finalize();
            return $tuns;
        }
        
        return FALSE;
    }
    
    function Tunnel($tun_id, $utype) {
        $result = $this->db->query('SELECT * FROM tunnels WHERE id='.$tun_id.' AND utype >= '.$utype.';');
        if ($result !== FALSE) {
            $tun = $result->fetchArray();
            $result->finalize();
            return $tun;
        }
        
        return FALSE;
    }

    function TunnelAdd($node_id, $name, $sport, $dhost, $dport, $utype) {
        $this->db->exec("INSERT INTO tunnels (node_id, name, sport, dhost, dport, utype) VALUES ('".$node_id."', '".$name."', '".$sport."', '".$dhost."', '".$dport."', '".$utype."');");
    }

    function TunnelUpdate($tun_id, $node_id, $name, $sport, $dhost, $dport, $utype) {
        $this->db->exec("UPDATE tunnels SET name='".$name."', sport='".$sport."', dhost='".$dhost."', dport='".$dport."', utype='".$utype."' WHERE id=".$tun_id." AND node_id=".$node_id.";");
    }

    function TunnelRemove($id) {
        $this->db->exec('DELETE FROM tunnels WHERE id = '.$id.';');
    }

    function Node($id) {
        $result = $this->db->query('SELECT * FROM nodes WHERE id='.$id.';');
        if ($result !== FALSE) {
            $row = $result->fetchArray();
            $result->finalize();
            return $row;
        }
        else
            return FALSE;
    }
    
    function Add($name, $descrip, $sn, $idn, $enckey, $master_enckey, $phone) {
        $this->db->exec("INSERT INTO nodes (name, descrip, sn, idn, enckey, master_enckey, phone) VALUES ('".$name."', '".$descrip."', '".$sn."', '".$idn."', '".$enckey."', '".$master_enckey."', '".$phone."');");
    }
    
    function Remove($id) {
        $this->db->exec('DELETE FROM nodes WHERE id = '.$id.';');
        $this->db->exec('DELETE FROM tunnels WHERE node_id = '.$id.';');
        $this->db->exec('DELETE FROM node_users WHERE node_id = '.$id.';');
    }
    
    function RemoveUser($id) {
        $this->db->exec('DELETE FROM node_users WHERE user_id = '.$id.';');
    }

    function UpdateEncKey($id, $enckey) {
        $this->db->exec("UPDATE nodes SET enckey='".$enckey."' WHERE id=".$id.";");        
    }
    
    function UpdateMasterKey($id, $mkey) {
        $this->db->exec("UPDATE nodes SET master_enckey='".$mkey."' WHERE id=".$id.";");        
    }

    function Update($id, $name, $descrip, $phone, $sms_updown, $auto_start, $disable) {
        $this->db->exec("UPDATE nodes SET name='".$name."',  descrip='".$descrip."', phone='".$phone."', sms_updown='".$sms_updown."', auto_start='".$auto_start."', disable='".$disable."' WHERE id=".$id.";");
    }

    function UpdateStatus($id, $ip, $on, $lastmsg, $started = null) {
        if ($started == null)
            $this->db->exec("UPDATE nodes SET ip='".$ip."', tunnelon='".$on."', lastmsg='".$lastmsg."' WHERE id=".$id.";");
        else
            $this->db->exec("UPDATE nodes SET ip='".$ip."', tunnelon='".$on."', lastmsg='".$lastmsg."', started='".$started."' WHERE id=".$id.";");
    }
    
    function UpdateTunnelSt($id, $on) {
        $this->db->exec("UPDATE nodes SET tunnelon='".$on."' WHERE id=".$id.";");
    }

    function StartStop($id, $start) {
        if ($start != -1)
            $this->db->exec("UPDATE nodes SET start_utype='".$start."' WHERE id=".$id.";");
        else
            $this->db->exec("UPDATE nodes SET start_utype='-1' WHERE id=".$id.";");
    }
    
    function GetSn($sn) {
        $result = $this->db->query('SELECT * FROM nodes WHERE sn = "'.$sn.'";');
        if ($result !== FALSE) { 
            $row = $result->fetchArray();
            $result->finalize();
            return $row;
        }
        
        return FALSE;
    }
    
    function GetIdn($idn) {
        $result = $this->db->query('SELECT * FROM nodes WHERE idn = "'.$idn.'";');
        if ($result !== FALSE) { 
            $row = $result->fetchArray();
            $result->finalize();
            return $row;
        }
        
        return FALSE;
    }
    
    function UserNodes($uid) {
        $result = $this->db->query('SELECT * FROM node_users WHERE user_id = "'.$uid.'";');
        if ($result !== FALSE) { 
            $nodes = array();
            while ($row = $result->fetchArray()) {
                $nodes[] = $row;
            }
            $result->finalize();
            return $nodes;
        }
        
        return FALSE;
    }
    
    function UserAddNode($uid, $nid) {
        $this->db->exec("INSERT INTO node_users (user_id, node_id) VALUES (".$uid.', '.$nid.");");
    }
    
    function UserDelNode($uid, $nid) {
        $this->db->exec('DELETE FROM node_users WHERE user_id = '.$uid.' AND node_id = '.$nid.';');
    }
    
}
