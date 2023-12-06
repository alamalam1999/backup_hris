<?php defined('BASEPATH') or exit('No direct script access allowed');

class Beranda extends CI_Controller
{
	var $var = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		if ($this->auth_model->is_not_login()) redirect(site_url('auth'));
	}

	public function index()
	{
		$KARYAWAN_ID = getUserActive("KARYAWAN_ID");
		$data_karyawan =  $this->db->query(" SELECT * FROM karyawan WHERE KARYAWAN_ID = $KARYAWAN_ID ")->row();
		 if($data_karyawan->COMPLETED == 0){
		 	redirect(site_url('profile'));
		 }
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

		$data_absen = " SELECT A.* , K.NAMA FROM tabel_absen A
						LEFT JOIN karyawan K ON K.KARYAWAN_ID = A.ID_KARYAWAN
						WHERE A.ID_KARYAWAN = '$ID' AND MONTH(TGL_JADWAL) = '$month' AND YEAR(TGL_JADWAL) = '$year'
						ORDER BY TGL_JADWAL DESC LIMIT 31 ";

		$data_jadwal = " SELECT SK.* , K.NAMA , K.NIK , S.START_TIME , S.FINISH_TIME , S.STATUS FROM shift_karyawan SK
						LEFT JOIN karyawan K ON K.KARYAWAN_ID = SK.KARYAWAN_ID
						LEFT JOIN shift S ON S.SHIFT_CODE = SK.SHIFT_CODE
						WHERE  SK.KARYAWAN_ID = '$ID' AND MONTH(DATE) = '$month' AND YEAR(DATE) = '$year'
						ORDER BY DATE ASC  LIMIT 31 ";

		$data['jadwal'] = $this->db->query($data_jadwal)->result();
		$data['absen'] = $this->db->query($data_absen)->result();
		$data['title'] = 'Halaman Beranda';
		$data['content'] = 'web/beranda/beranda';

		//$this->load->view('web/beranda/beranda');
		$this->load->view("web/templates", $data);
	}
}
