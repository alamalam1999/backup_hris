<?php defined('BASEPATH') or exit('No direct script access allowed');

class Blank extends CI_Controller
{
	var $var = array();

	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{


		$data['title'] = 'Halaman kosong';
		$data['content'] = 'web/blank';

		$this->load->view("web/templates",$data);


	}



}
