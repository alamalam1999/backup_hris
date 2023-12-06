<?php defined('BASEPATH') or exit('No direct script access allowed');

class Kinerja extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		//$this->load->model('karyawan_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}



	public function index()
	{
		// $ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		// $data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);
		// echo "<pre>";
		// print_r($data['data_karyawan'] ); die();

		// $data['update_url'] = site_url('profile/proses_update/' . $ID);
		// $data['js_script'] = 'js/karyawan_js';

		$data['title'] = "Kinerja";

		$data['content'] = 'web/kinerja/kinerja';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/kinerja/kinerja', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);
	}



}
