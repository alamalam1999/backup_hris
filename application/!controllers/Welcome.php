<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		if ($this->auth_model->is_not_login()) redirect(site_url('auth'));
	}

	/* 
	public function index()
	{
		switch ($this->session->userdata('page')) {
			case 'customer':
				redirect(site_url('customer'));
				break;
			case 'ujiprofisiensi':
				redirect(site_url('uji_profisiensi'));
				break;
			case 'pelatihan':
				redirect(site_url('pelatihan'));
				break;
		}
	} 
	*/
}
