<?php

class Log {
    function Append($file, $str) {
        global $log_max_lines;
        file_put_contents($file, date('Y-m-d H-i-s').' '.$str."\n", FILE_APPEND);
        $lines = shell_exec('wc -l '.$file);
        if ($lines > $log_max_lines + 100) {
            $ret = shell_exec("sed -i -e '".$lines-$log_max_lines."d' ".$file);
        }
    }

    function Read($file) {
        if (!file_exists($file))
            return array();
        $logs = file($file);
        $logs = array_reverse($logs);
        return $logs;
    }
}
