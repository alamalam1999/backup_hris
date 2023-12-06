<?php defined('BASEPATH') or exit('No direct script access allowed');

class penggajian extends CI_Controller
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
		$data['title'] = "Penggajian";

		$p = $this->input->post('PERIODE_ID');
		$data['periode'] = $p;
		if(!$p)
		{
			$p = getPeriodeActive('PERIODE_ID');
		}



		$ID = getUserActive("KARYAWAN_ID");
		$Q = " SELECT P.* , K.NAMA , K.NIK  FROM penggajian P
						LEFT JOIN karyawan K ON K.KARYAWAN_ID = P.KARYAWAN_ID
						WHERE  P.KARYAWAN_ID = '$ID' AND PERIODE_ID = '$p'
						ORDER BY PENGGAJIAN_ID ASC  LIMIT 31";
		$data['data'] = $this->db->query($Q)->result();

		// echo $Q; die();
		// echo "<pre>";
		// print_r($data['data']); die();

		$data['content'] = 'web/penggajian/penggajian';
		$this->load->view("web/templates",$data);
	}

	public function download($ID = null)
	{

		$Q = "
		SELECT P.*, K.*, J.JABATAN
		FROM penggajian P
		LEFT JOIN karyawan K ON (K.KARYAWAN_ID=P.KARYAWAN_ID)
		LEFT JOIN jabatan J ON (J.JABATAN_ID=K.JABATAN_ID)
		WHERE PENGGAJIAN_ID = '$ID'
		ORDER BY K.NAMA ASC
		";
		$row = $this->db->query($Q)->row();
		$data['data'] = $row;

		$this->load->library('pdfgenerator');
		$file_pdf = "Slip Gaji- $row->NAMA [$row->NIK]";
		$paper = 'A4';
		$orientation = "portrait";

		$html = $this->load->view("web/penggajian/download",$data , true);
		$this->pdfgenerator->generate($html, $file_pdf, $paper, $orientation);
	}



}
