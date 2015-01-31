<?php
/**
 * Essential : Minimalistic PHP Framework
 * Copyright 2013-2014, Gianluca Costa (http://www.xplico.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 */

class AppController {
    public $models;
    public $components;
    
    function __ModulesInit() {
        if (!empty($this->models)) {
            foreach ($this->models as $module) {
                if (!class_exists($module)) {
                    SesVarSet('esalert', _("Model '$module' doesn't exist"));
                    break;
                }
                $this->$module = new $module();
            }
        }
        if (!empty($this->components)) {
            foreach ($this->components as $module) {
                if (!class_exists($module)) {
                    SesVarSet('esalert', _("Component '$module' doesn't exist"));
                    break;
                }
                $this->$module = new $module();
            }
        }
    }
    
    function EsBefore() {
    }

    function Index() {
        SesVarSet('esalert', _('The page you requested does not exist'));
    }

    function Error() {
        SesVarSet('esalert', _('Content does not exist').' "'.$_GET['url'].'"');
        EsRedir();
    }
}
