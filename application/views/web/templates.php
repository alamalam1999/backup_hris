<?php
$this->load->view('templates/header');
$this->load->view('templates/topbar');
$this->load->view('templates/sidebar');
$this->load->view($content);
$this->load->view('templates/copyright');
$this->load->view('templates/footer');
