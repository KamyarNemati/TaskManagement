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

class GetTasks extends Utils {
    
    /**
     * @author Kamyar
     */
    public function index_get() {
        $obj = NULL;
        
        /*
         * UI Grid parameters
         */
        $params = $this->_get_args["params"];
        if (isset($params)) {
            $params_arr = json_decode($params, TRUE);
            if (isset($params_arr["length"])) {
                $uig_length = $params_arr["length"];
            }
            if (isset($params_arr["start"])) {
                $uig_start = $params_arr["start"];
            }
            if (isset($params_arr["order"])) {
                $uig_order = $params_arr["order"];
            }
            if (isset($params_arr["sort_field"])) {
                $uig_sort_field = $params_arr["sort_field"];
            }               
            if (isset($params_arr["filter"])) {
                $uig_filter = $params_arr["filter"];
            }
        }
        
        $dbc = $this->pg_connect();
        $this->load->model("Task");
        
        if ((isset($uig_length) && isset($uig_start) && isset($uig_order) && isset($uig_sort_field) && isset($uig_filter)) || (isset($uig_length) && isset($uig_start))) {
            $obj = $this->Task->getTasks($dbc, $uig_filter, $uig_start, $uig_length, $uig_sort_field, $uig_order);
        } else {
            $obj = $this->Task->getTasks($dbc);
        }
        
        $this->output_json($obj);
    }
}