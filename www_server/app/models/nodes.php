<?php
class Nodes {
    private $db;
    
    function __construct() {
        $db_file = DbDir().'nodes.db';
        if (file_exists($db_file)) {
            $this->db = new SQLite3($db_file);
            $this->db->busyTimeout(5000);
        }
        else {
            $this->db = new SQLite3($db_file);
            $this->db->busyTimeout(5000);
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
            phone TEXT,
            ip TEXT,
            lastmsg INTEGER,
            tunnel BOOLEAN DEFAULT 0,
            freq INTEGER DEFAULT 5,
            start_utype INTEGER DEFAULT -1,
            started INTEGER
        )");
        
        $this->db->exec("CREATE TABLE IF NOT EXISTS node_users (
            id INTEGER NOT NULL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            node_id INTEGER NOT NULL
        )");
        
        $this->db->exec('CREATE TABLE IF NOT EXISTS tunnels (
            id INTEGER NOT NULL PRIMARY KEY,
            node_id INTEGER NOT NULL,
            utype INTEGER DEFAULT 3,
            name TEXT,
            sport INTEGER DEFAULT 0,
            dhost TEXT NOT NULL,
            dport INTEGER NOT NULL
        )');
    }

    function Get($user_id, $utype) {
        if ($utype < 2) {
            $result = $this->db->query('SELECT * FROM nodes;');
        }
        else {
            $result = $this->db->query('SELECT * FROM nodes;');
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
        $result = $this->db->query('SELECT * FROM tunnels WHERE node_id='.$node_id.' AND utype >= '.$utype.';');
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

    function TunnelAdd($node_id, $name, $sport, $dhost, $dport) {
        $this->db->exec("INSERT INTO tunnels (node_id, name, sport, dhost, dport) VALUES ('".$node_id."', '".$name."', '".$sport."', '".$dhost."', '".$dport."');");
    }

    function TunnelRemove($id) {
        $this->db->query('DELETE FROM tunnels WHERE id = '.$id);
    }
    
    function Add($name, $descrip, $sn, $idn, $enckey, $phone) {
        $this->db->exec("INSERT INTO nodes (name, descrip, sn, idn, enckey, phone) VALUES ('".$name."', '".$descrip."', '".$sn."', '".$idn."', '".$enckey."', '".$phone."');");
    }

    function Update($id, $name, $descrip, $sn, $idn, $enckey, $phone) {
        $this->db->exec("UPDATE nodes SET name='".$name."',  descrip='".$descrip."', sn='".$sn."', idn='".$idn."', enckey='".$enckey."', phone='".$phone."' WHERE id=".$id.";");        
    }

    function UpdateStatus($id, $ip, $tunnel, $lastmsg, $started = null) {
        if ($started == null)
            $this->db->exec("UPDATE nodes SET ip='".$ip."', tunnel='".$tunnel."', lastmsg='".$lastmsg."' WHERE id=".$id.";");
        else
            $this->db->exec("UPDATE nodes SET ip='".$ip."', tunnel='".$tunnel."', lastmsg='".$lastmsg."', started='".$started."' WHERE id=".$id.";");
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
    
    function Search($src) {
        
    }
}
