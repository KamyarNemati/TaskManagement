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

class ChangeParent extends Utils {
    
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
        
        $id = intval($this->_post_args["id"]);
        $parent_id = intval($this->_post_args["parent_id"]);
        
        try {
            if(!(isset($id) && !is_null($id) && is_int($id))) {
                throw new Exception("Missing/invalid argument: 'id'");
            }
            if(!(isset($parent_id) && !is_null($parent_id) && is_int($parent_id))) {
                throw new Exception("Missing/invalid argument: 'name'");
            }
        } catch (Exception $ex) {
            $obj["msg"] = $ex->getMessage();
            $this->output_json($obj);
            return;
        }
        
        $dbc = $this->pg_connect();
        $this->load->model("Task");
        
        $obj = $this->Task->changeParent($dbc, $id, $parent_id);
        
        $this->output_json($obj);
    }
}