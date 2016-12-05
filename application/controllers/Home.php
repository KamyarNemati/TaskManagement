<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Kamyar
 */

class Home extends CI_Controller {
    
    public function index($page = "home") {
        $this->load->view("layout/header");
        $this->load->view("layout/left");
        $this->load->view("home/{$page}");
        $this->load->view("layout/footer");
    }
}