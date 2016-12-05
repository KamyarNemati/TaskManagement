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

class SaveTask extends Utils {
    
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
        
        $title = $this->_post_args["title"];
        $parent_id = $this->_post_args["parent_id"];
        
        try {
            if(!(isset($title) && !is_null($title))) {
                throw new Exception("Missing argument(s): 'title'");
            }
            if(!(isset($parent_id) && !is_null($parent_id))) {
                throw new Exception("Missing argument(s): 'parent_id'");
            }
            $title = strval($title);
            $parent_id = intval($parent_id);
        } catch (Exception $ex) {
            $obj["msg"] = $ex->getMessage();
            $this->output_json($obj);
            return;
        }
        
        $dbc = $this->pg_connect();
        $this->load->model("Task");
        
        $obj = $this->Task->saveTask($dbc, $title, $parent_id);
        
        $this->output_json($obj);
    }
}