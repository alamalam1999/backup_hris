<?php defined('BASEPATH') or exit('No direct script access allowed');

class Absensi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}

	public function index()
	{

		$p = $this->input->post('PERIODE_ID');
		$data['periode'] = $p;

		if ($p) {
			$month = getPeriode($p, 'BULAN');
			$year = getPeriode($p, 'TAHUN');
		} else {
			$month = date('m');
			$year = date('Y');
		}

		$ID = getUserActive("KARYAWAN_ID");
		$data['title'] = "Absensi";
		$data_absensi = " SELECT A.* , K.NAMA FROM tabel_absen A
						LEFT JOIN karyawan K ON K.KARYAWAN_ID = A.ID_KARYAWAN
						WHERE  A.ID_KARYAWAN = $ID  AND MONTH(TGL_JADWAL) = '$month' AND YEAR(TGL_JADWAL) = '$year'
						ORDER BY TGL_JADWAL DESC LIMIT 31";
		$data['absen'] = $this->db->query($data_absensi)->result();
		$data['content'] = 'web/absensi/absensi';

		//$this->load->view('web/absensi/absensi');
		$this->load->view("web/templates", $data);
	}
}
