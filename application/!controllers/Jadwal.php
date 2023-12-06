<?php defined('BASEPATH') or exit('No direct script access allowed');

class Jadwal extends CI_Controller
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
		$data['title'] = "Jadwal";

		$p = $this->input->post('PERIODE_ID');
		$data['periode'] = $p;
		if($p)
		{
			$month = getPeriode($p,'BULAN');
			$year = getPeriode($p,'TAHUN');
		}else
		{
			$month = date('m');
			$year = date('Y');
		}



		$ID = getUserActive("KARYAWAN_ID");
		$Q = " SELECT SK.* , K.NAMA , K.NIK , S.START_TIME , S.FINISH_TIME , S.STATUS FROM shift_karyawan SK
						LEFT JOIN karyawan K ON K.KARYAWAN_ID = SK.KARYAWAN_ID
						LEFT JOIN shift S ON S.SHIFT_CODE = SK.SHIFT_CODE
						WHERE  SK.KARYAWAN_ID = '$ID' AND MONTH(DATE) = '$month' AND YEAR(DATE) = '$year'
						ORDER BY DATE ASC  LIMIT 31";
		$data['data'] = $this->db->query($Q)->result();

		//echo $Q; die();
		// echo "<pre>";
		// print_r($data['data']); die();

		$data['content'] = 'web/jadwal/jadwal';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/jadwal/jadwal', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);
	}



}
