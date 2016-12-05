<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Kamyar
 */

require_once dirname(__FILE__) . '/../utils/Utils.php';

class TaskTableCheck extends Utils {
    
    /**
     * @author Kamyar
     */
    public function index_get() {
        $obj = NULL;
        
        $dbc = $this->pg_connect();
        $this->load->model("Task");
        $obj = $this->Task->checkTaskTable($dbc);
        
        $this->output_json($obj);
    }
}