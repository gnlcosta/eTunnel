<?php
class Users {
    private $db;
    
    function __construct() {
        $db_file = DbDir().'users.db';
        if (file_exists($db_file)) {
            $this->db = new SQLite3($db_file);
        }
        else {
            $this->db = new SQLite3($db_file);
            $this->Create();
        }
    }
    
    function __destruct() {
        $this->db->close();
    }
    
    private function Create() {
        global $prj_name;
        /* 
            type: 0 devel
                  1 admin
                  2 technician
                  3 user
        */
        $this->db->exec('CREATE TABLE IF NOT EXISTS users (
            id INTEGER NOT NULL PRIMARY KEY,
            user TEXT NOT NULL,
            password TEXT NOT NULL,
            type INTEGER,
            email TEXT,
            cng_pwd BOOLEAN DEFAULT 0
        )');
        // utenti di default
        $this->db->exec("INSERT INTO users (user, password, type, cng_pwd) VALUES ('admin', '".password_hash('admin', PASSWORD_DEFAULT)."', 1, 1);");
    }

    function Add($new) {
        $this->db->exec("INSERT INTO users (user, password, type, email) VALUES ('".$new['user']."', '".$new['password']."', '".$new['type']."', '".$new['email']."');");
    }
    
    function Delete($id) {
        $this->db->query('DELETE FROM users WHERE id = '.$id);
    }
    
    function Search($user) {
        $result = $this->db->query("SELECT * FROM users WHERE user = '".$user."'");
        if ($result !== FALSE) {
            return $result->fetchArray();
        }
        return FALSE;
    }
    
    function SearchByID($id) {
        $result = $this->db->query('SELECT * FROM users WHERE id = '.$id);
        if ($result !== FALSE) {
            return $result->fetchArray();
        }
        return FALSE;
    }
    
    function Save($id, $newdata) {
        $set = '';
        foreach ($newdata as $key => $value) {
            if ($set == '')
                $set = ' '.$key."='".$value."'";
            else
                $set .= ', '.$key."='".$value."'";
        }
        //echo "UPDATE users SET ".$set." WHERE id=".$id.";"; die();
        $this->db->exec("UPDATE users SET ".$set." WHERE id=".$id.";");
        return TRUE;
    }

    function View($level) {
        $rows = array();
        $result = $this->db->query("SELECT * FROM users WHERE password != 'random' and type >= ".$level);
        while ($row = $result->fetchArray())
            $rows[] = $row;

        return $rows;
    }
    
    function Types($level = 0) {
        $type = array(0 => _('Devel'), 1 => _('Admin'), 2 => _('Technician'), 3 => _('User'));
        for ($i=0; $i<$level; $i++)
            unset($type[$i]);
        return $type;
    }
    
    function FullAccess($id) {
        if ($id < 3)
            return TRUE;
        return FALSE;
    }
    
    function Permanent($id) {
        if ($id > 5)
            return FALSE;
        return TRUE;
    }
}
