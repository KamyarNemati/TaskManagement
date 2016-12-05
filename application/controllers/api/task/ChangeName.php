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

class ChangeName extends Utils {
    
    /**
     * 
     * @return type
     * @throws Exception
     * @author Kamyar
     */
    public function index_post() {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $id = $this->_post_args["id"];
        $name = $this->_post_args["name"];
        
        try {
            if(!(isset($id) && !is_null($id))) {
                throw new Exception("Missing argument(s): 'id'");
            }
            if(!(isset($name) && !is_null($name))) {
                throw new Exception("Missing argument(s): 'name'");
            }
            $id = intval($id);
            $name = strval($name);
        } catch (Exception $ex) {
            $obj["msg"] = $ex->getMessage();
            $this->output_json($obj);
            return;
        }
        
        $dbc = $this->pg_connect();
        $this->load->model("Task");
        
        $obj = $this->Task->renameTask($dbc, $id, $name);
        
        $this->output_json($obj);
    }
}