<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Kamyar
 */

class Task extends CI_Model {
    
    /**
     * 
     * @param type $dbc
     * @return int
     * @author Kamyar
     */
    public function checkTaskTable(&$dbc) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $sql = "CREATE TABLE IF NOT EXISTS tasks(
                id serial NOT NULL PRIMARY KEY, 
                title VARCHAR(255) NOT NULL, 
                status SMALLINT NOT NULL DEFAULT 0, 
                parent_id INT NOT NULL DEFAULT 0
                );";
        
        $query = $dbc->query($sql);
        
        if($query) {
            $obj["stat"] = 0;
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @return int
     * @author Kamyar
     */
    public function getTasks(&$dbc, &$condition = NULL, &$start = NULL, &$length = NULL, &$columns = NULL, &$order = NULL) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        if(($condition != NULL && $start != NULL && $length != NULL && $columns != NULL && $order != NULL) || ($start != NULL && $length)) {
            $explicit = [
                "id" => TRUE, 
                "title" => FALSE, 
                "status" => FALSE, 
                "parent_id" => TRUE, 
            ];
            $mask = [
                "status" => "CASE
                                 WHEN status = 0 THEN 'IN PROGRESS'
                                 WHEN status = 1 THEN 'DONE'
                                 WHEN status = 2 THEN 'COMPLETE'
                                 ELSE ''
                             END"
            ];
            $this->_normalise_start($start);
            $this->_translate_order($order);
            $this->_prepare_condition($condition, $mask, $explicit);
            $extended_support = TRUE;
        } else {
            $extended_support = FALSE;
        }
        
        $sql = "SELECT 
                    id, 
                    title, 
                    CASE
                        WHEN status = 0 THEN 'IN PROGRESS'
                        WHEN status = 1 THEN 'DONE'
                        WHEN status = 2 THEN 'COMPLETE'
                        ELSE ''
                    END AS status, 
                    parent_id 
                FROM 
                    tasks 
                WHERE 
                    id > 0" .
                ($extended_support ? "$condition 
                                        ORDER BY $columns $order 
                                        LIMIT $length 
                                        OFFSET $start" 
                : "") . ";";
        
        $query = $dbc->query($sql);
        
        if($query) {
            $obj["data"] = $query->result_array();
            $cnt = $this->getTaskCount($dbc);
            $obj["count"] = ($cnt["stat"] == 0 ? $cnt["data"] : count($obj["data"]));
            $obj["stat"] = 0;
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @return int
     * @author Kamyar
     */
    private function getTaskCount(&$dbc) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $sql = "SELECT COUNT(*) AS sum FROM tasks;";
        
        $query = $dbc->query($sql);
        
        if($query) {
            $obj["data"] = $query->row_array()["sum"];
            $obj["stat"] = 0;
        }
        
        return $obj;
    }

    /**
     * 
     * @param type $order
     * @return boolean
     * 
     * @author Kamyar
     * Begin: function
     */
    private function _translate_order(&$order) {
        switch ($order) {
            case 1:
                $order = "ASC";
                break;
            case 2:
                $order = "DESC";
                break;
            default:
                return FALSE;
        }
        return TRUE;
    }
    /*
     * Kamyar
     * End: function
     */
    
    /**
     * 
     * @param type $start
     * 
     * @author Kamyar
     * Begin: function
     */
    private function _normalise_start(&$start) {
        --$start;
    }
    /*
     * Kamyar
     * End: function
     */
    
    /**
     * 
     * @param type $condition
     * @param type $mask
     * @param type $explicit
     * 
     * @author Kamyar
     * Begin: function
     */
    private function _prepare_condition(&$condition, $mask = NULL, $explicit = NULL) {
        $sentence = "";
        foreach($condition as $key => $value) {
            if($value != "" && $value != NULL) {
                if($mask != NULL && array_key_exists($key, $mask)) {
                    $sentence .= " AND {$mask[$key]} " . (!is_null($explicit) && array_key_exists($key, $explicit) ? ($explicit["$key"] ? "= {$value}" : "ilike '%{$value}%'") : "ilike '%{$value}%'");
                } else {
                    $sentence .= " AND {$key} " . (!is_null($explicit) && array_key_exists($key, $explicit) ? ($explicit["$key"] ? "= {$value}" : "ilike '%{$value}%'") : "ilike '%{$value}%'");
                }
            }
        }
        $condition = $sentence;
    }
    /*
     * Kamyar
     * End: function
     */
    
    /**
     * 
     * @param type $dbc
     * @return type
     * @author Kamyar
     */
    public function createHierarchicalVisual(&$dbc) {
        $obj = [
            "stat"      =>      -1, 
            "msg"       =>      "", 
            "int_msg"   =>      "", 
            "data"      =>      [], 
            "count"     =>      0, 
            "view"      =>      ""
        ];

        $data = $this->getTasks($dbc);
        if ($data["stat"] == 0) {
            try {
                $tree_obj = new TreeProcessor($data["data"]);
                $tree_obj->processTree($obj["count"], $obj["view"]);
            } catch (Exception $ex) {
                $obj["msg"] = $ex->getMessage();
                $obj["int_msg"] = $ex->getCode();
            } finally {
                $obj["data"] = $data;
                $obj["stat"] = 0;
            }
        } else {
            $obj["msg"] = $data["msg"];
        }

        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $title
     * @param type $parent_id
     * @return int
     * @author Kamyar
     */
    public function saveTask(&$dbc, &$title, &$parent_id) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $sql = "INSERT INTO 
                    tasks (
                        title, 
                        parent_id
                    ) 
                VALUES (
                    ?, 
                    ?
                );";
        
        $query = $dbc->query($sql, [
            $title, 
            $parent_id
        ]);
        
        if($query) {
            $obj["data"] = $dbc->insert_id();
            $obj["stat"] = 0;
            $this->reviewStatus($dbc);
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $id
     * @return string
     * @author Kamyar
     */
    public function changeStatus(&$dbc, &$id) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $req_sql = "SELECT status FROM tasks WHERE id = ?;";
        
        $req_query = $dbc->query($req_sql, $id);
        
        if($req_query) {
            $status = $req_query->row_array()["status"];
            
            if($status == 0) {
                if($this->toggleStatus($dbc, $id)) {
                    $this->reviewStatus($dbc);
                    $obj["stat"] = 0;
                }
            } else {
                $check_sql = "SELECT COUNT(*) AS sum FROM tasks WHERE parent_id = ?;";
                    
                $check_query = $dbc->query($check_sql, $id);

                if($check_query) {
                    $check = $check_query->row_array()["sum"];

                    if($check == 0) {
                        if($this->toggleStatus($dbc, $id, 0)) {
                            $this->reviewStatus($dbc);
                            $obj["stat"] = 0;
                        }
                    } else {
                        $obj["msg"] = "Parent task's status cannot be reverted.";
                    }
                }
            }
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $id
     * @param type $name
     * @return int
     * @author Kamyar
     */
    public function renameTask(&$dbc, &$id, &$name) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $sql = "UPDATE tasks SET title = ? WHERE id = ?;";
        
        $query = $dbc->query($sql, [
            $name, 
            $id
        ]);
        
        if($query) {
            $obj["data"] = [
                "id"    =>  $id, 
                "name"  =>  $name
            ];
            $obj["stat"] = 0;
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $id
     * @param type $parent_id
     * @return string
     * @author Kamyar
     */
    public function changeParent(&$dbc, &$id, &$parent_id) {
        $obj = [
            "stat"   =>   -1, 
            "msg"    =>   "",
            "data"   =>   [],
            "count"  =>  0
        ];
        
        $check_sql = "SELECT COUNT(*) as cnt FROM tasks WHERE id = ?;";
        
        $check_query = $dbc->query($check_sql, [
            $id
        ]);
        
        if($check_query) {
            $check_data = $check_query->row_array()["cnt"];
            if($check_data > 1) {
                $obj["msg"] = "Data integrity is gone.";
            } else {
                if($check_data < 1) {
                    $obj["msg"] = "No such task found.";
                } else {
                    $res = $this->getTasks($dbc);
                    if($res["stat"] == 0) {
                        $data = $res["data"];
                        $Tree = new TreeProcessor($data);
                        $tree = $Tree->generateTree();
                        if($this->checkCirculation($tree, $id, $parent_id)) {
                            $sql = "UPDATE tasks SET parent_id = ? WHERE id = ?;";
                            $query = $dbc->query($sql, [
                                $parent_id,
                                $id
                            ]);
                            if($query) {
                                $obj["data"] = [
                                    "id" => $id,
                                    "parent_id" => $parent_id
                                ];
                                $this->reviewStatus($dbc);
                                $obj["stat"] = 0;
                            }
                        } else {
                            $obj["msg"] = "The new Parent of the task cannot be a child of itself. Otherwise, circulation would happen.";
                        }
                    }
                }
            }
        }
        
        return $obj;
    }
    
    /**
     * 
     * @param type $tree
     * @param type $id
     * @param type $parent_id
     * @return boolean
     * @author Kamyar
     */
    private function checkCirculation(&$tree, &$id, &$parent_id) {
        $rootParent = $this->getParent($tree, $id);
        if(!is_null($rootParent)) {
            return $this->checkChilds($rootParent, $parent_id);
        }
        return FALSE;
    }
    
    /**
     * 
     * @param type $tree
     * @param type $parent_id
     * @return boolean
     * @author Kamyar
     */
    private function checkChilds(&$tree, &$parent_id) {
        foreach($tree as &$node) {
            if($node["id"] == $parent_id) {
                return FALSE;
            }
            if(count($node["data"]) > 0) {
                return $this->checkChilds($node["data"], $parent_id);
            }
        }
        return TRUE;
    }

    /**
     * 
     * @param type $tree
     * @param type $id
     * @return boolean
     * @author Kamyar
     */
    private function getParent(&$tree, &$id) {
        foreach($tree as $key => &$item) {
            if($item["id"] == $id) {
                return [$key => $item];
            }
            if(count($item["data"]) > 0) {
                return $this->getParent($item["data"], $id);
            }
        }
        return FALSE;
    }

    /**
     * 
     * @param type $dbc
     * @param type $id
     * @param type $status
     * @return boolean
     * @author Kamyar
     */
    private function toggleStatus(&$dbc, &$id, $status = 1) {
        $sql = "UPDATE tasks SET status = ? WHERE id = ?;";
        $query = $dbc->query($sql, [
            $status, 
            $id
        ]);
        if($query) {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * 
     * @param type $dbc
     * @author Kamyar
     */
    private function reviewStatus(&$dbc) {
        $res = $this->getTasks($dbc);
        if($res["stat"] == 0) {
            $data = $res["data"];
            $Tree = new TreeProcessor($data);
            $tree = $Tree->generateTree();
            $this->propagateStatus($tree, $dbc);
        }
    }
    
    /**
     * 
     * @param type $tree
     * @param type $dbc
     * @return boolean
     * @author Kamyar
     */
    private function propagateStatus(&$tree, &$dbc) {
        $ok = TRUE;
        foreach($tree as &$node) {
            $good = TRUE;
            if(count($node["data"]) > 0) {
                $tmp = $this->propagateStatus($node["data"], $dbc);
                $good = $tmp;
                if($ok/* || $good*/) {
                    $ok = $tmp;
                }
            }
            if($node["status"] == "IN PROGRESS") {
                $ok = FALSE;
            }
            $id = $node["id"];
            if($ok || $good) {
                if($node["status"] == "DONE") {
                    $this->completeStatus($dbc, $id);
                }
            } else {
                if(!($ok && $good)) {
                    if($node["status"] == "COMPLETE") {
                        $this->revertStatus($dbc, $id);
                    }
                }
            }
        }
        return $ok;
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $id
     * @return type
     * @author Kamyar
     */
    private function completeStatus(&$dbc, &$id) {
        return (
                $dbc->query("UPDATE tasks SET status = 2 WHERE id = ?;", $id) 
                ? TRUE 
                : FALSE);
    }
    
    /**
     * 
     * @param type $dbc
     * @param type $id
     * @return type
     * @author Kamyar
     */
    private function revertStatus(&$dbc, &$id) {
        return (
                $dbc->query("UPDATE tasks SET status = 1 WHERE id = ?;", $id) 
                ? TRUE 
                : FALSE);
    }
}

/**
 * @author Kamyar
 */
class TreeProcessor {
    
    /**
     *
     * @var type 
     * @access private
     */
    private $data;

    /**
     * 
     * @param type $data
     * @author Kamyar
     */
    public function __construct(&$data) {
        $this->data = $data;
    }
    
    /**
     * 
     * @param type $count
     * @param type $view
     * @author Kamyra
     */
    public function processTree(&$count, &$view, $returnData = FALSE) {
        $this->taskNormaliseToMakeHierarchy($this->data);
        $this->taskMakeHierarchy($this->data, $count);
        $this->taskMakeHierarchyView($this->data, $view, 0);
        return ($returnData ? $this->data : TRUE);
    }
    
    /**
     * 
     * @param type $count
     * @return type
     * @author Kamyar
     */
    public function generateTree(&$count = 0) {
        $this->taskNormaliseToMakeHierarchy($this->data);
        $this->taskMakeHierarchy($this->data, $count);
        return $this->data;
    }

    /**
     * 
     * @param type $data
     * @author Kamyar
     */
    private function taskNormaliseToMakeHierarchy(&$data) {
        foreach($data as &$task) {
            if($task["parent_id"] == 0) {
                continue;
            }
            $ok = FALSE;
            foreach($data as $ptr) {
                if($task["parent_id"] == $ptr["id"]) {
                    $ok = TRUE;
                    break;
                }
            }
            if(!$ok) {
                $task["parent_id"] = 0;
            }
        }
    }
    
    /**
     * 
     * @param type $data_arr
     * @param type $total_count
     * @author Kamyar
     */
    private function taskMakeHierarchy(&$data_arr, &$total_count) {
        $dataRoot = array();
        $dataSub = array();
        for ($i = 0; $i < count($data_arr); ++$i) {
            $node = array();
            $this->taskMakeNode($data_arr[$i], $node);
            if ($data_arr[$i]['parent_id'] == 0) {
                array_push($dataRoot, $node);
            } else {
                array_push($dataSub, $node);
            }
            ++$total_count;
        }
        $i = 0;
        while (count($dataSub) > 0) {
            $this->taskMakeHierarchyRecursive($dataRoot, $dataSub[$i]);
            if($this->cleanArray($dataSub)) {
                $i = 0;
            } else {
                ++$i;
            }
        }
        $data_arr = $dataRoot;
    }
    
    /**
     * 
     * @param type $array
     * @return boolean
     * @author Kamyar
     */
    private function cleanArray(&$array) {
        foreach ($array as $key => &$val) {
            if($val == NULL) {
                unset($array[$key]);
                $array = array_values($array);
                return TRUE;
            }
        }
        return FALSE;
    }
    
    /**
     * 
     * @param type $dataRoot
     * @param type $subItem
     * @return type
     * @author Kamyar
     */
    private function taskMakeHierarchyRecursive(&$dataRoot, &$subItem) {
        for ($i = 0; $i < count($dataRoot); ++$i) {
            if (count($dataRoot[$i]['data']) != 0) {
                $dataRoot[$i]['data'] = $this->taskMakeHierarchyRecursive($dataRoot[$i]['data'], $subItem);
            }
            if ($subItem['parent_id'] == $dataRoot[$i]['id']) {
                $node = array();
                $this->taskMakeNode($subItem, $node);
                array_push($dataRoot[$i]['data'], $node);
                $subItem = NULL;
                return $dataRoot;
            }
        }
        return $dataRoot;
    }
    
    /**
     * 
     * @param type $data
     * @param type $node
     * @author Kamyar
     */
    private function taskMakeNode(&$data, &$node) {
        $node = array(
            "id" => $data["id"],
            "title" => $data["title"],
            "parent_id" => $data["parent_id"],
            "status" => $data["status"],
            "data" => array(),
        );
    }
    
    /**
     * 
     * @param type $data
     * @param type $view
     * @param type $level
     * @author Kamyar
     */
    private function taskMakeHierarchyView(&$data, &$view, $level) {
        $view .= "<ul>";
        for ($i = 0; $i < count($data); ++$i) {
            $class = $level == 0 ? "root" : "child";
            $view .= $level == 0 ? "<div class='row' style='margin: 32px 0;'>" : "";
            $view .= "<li class='" . $class . "'>";
            $view .= "<a style='text-transform: uppercase; color: black; height: 50px !important;" . (($data[$i]['status'] == "IN PROGRESS") ? "background: red !important;" : (($data[$i]['status'] == "DONE") ? "background: blue !important;" : (($data[$i]['status'] == "COMPLETE") ? "background: green !important;" : ""))) . "'>";
//            $view .= "<img src='" . $img . "' style='align: top; padding: 3px;'><br>";
            $view .= "<p style='font-weight: bold; font-size: small; '>{$this->taskNameCutter($data[$i]['title'], 100)}</p>";
            $view .= "<p style='font-weight: normal; font-size: 12px !important; width: 85px;'>{$this->taskNameCutter($data[$i]['status'], 100)}</p>";
            $view .= "<p style='clear: both;'></p>";
            $view .= "</a>";
            if (count($data[$i]['data']) != 0) {
                $this->taskMakeHierarchyView($data[$i]['data'], $view, $level + 1);
            }
            $view .= "</li>";
            $view .= $level == 0 ? "</div>" : "";
        }
        $view .= "</ul>";
    }
    
    /**
     * 
     * @param type $name
     * @param type $charNo
     * @return string
     * @author Kamyar
     */
    private function taskNameCutter(&$name, $charNo) {
        $len = 0;
        try {
            if(mb_strlen($name, "utf-8") == strlen($name)) {
                $len = mb_strlen($name, mb_internal_encoding());
            }
        } finally {
            if ($len <= $charNo) {
                return $name;
            }
            $result = "";
            for ($i = 0; $i < $len; ++$i) {
                $result .= $name[$i];
                if ($i >= $charNo) {
                    break;
                }
            }
            $result .= " ...";
        }
        return $result;
    }
}