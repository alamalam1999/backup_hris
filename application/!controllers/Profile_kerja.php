<?php defined('BASEPATH') or exit('No direct script access allowed');

class profile_kerja extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('karyawan_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}

	public function index()
	{
		$data['title'] ="Profile  Penglaman Kerja";

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		//$data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);

		$Q = "SELECT * FROM pengalaman_karyawan WHERE KARYAWAN_ID='$ID'";
		$data['PENGALAMAN_KARYAWAN'] = $this->db->query($Q);

		//$data['update_url'] = site_url('profile_pendidikan/proses_update/' . $ID);
		// $data['js_script'] = 'js/karyawan_2_js';

		$data['content'] = 'web/profile/profile_kerja';
		$this->load->view("web/templates",$data);



	}

	public function proses_add()
	{

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data = array('KARYAWAN_ID' => $ID,
		'NAMA_PERUSAHAAN' => $this->input->post('NAMA_PERUSAHAAN'),
		'BIDANG_USAHA' => $this->input->post('BIDANG_USAHA'),
		'JABATAN_AWAL' => $this->input->post('JABATAN_AWAL'),
		'JABATAN_AKHIR' => $this->input->post('JABATAN_AKHIR'),
		'ALAMAT_PERUSAHAAN' => $this->input->post('ALAMAT_PERUSAHAAN'),
		'ATASAN' => $this->input->post('ATASAN'),
		'NO_TELP_PERUSAHAAN' => $this->input->post('NO_TELP_PERUSAHAAN'),
		'PERIODE_BEKERJA' => $this->input->post('PERIODE_BEKERJA'),
		'GAPOK_SEBELUMNYA' => $this->input->post('GAPOK_SEBELUMNYA'),
		'TUNJANGAN_LAINNYA' => $this->input->post('TUNJANGAN_LAINNYA'),
		'ALASAN_RESIGN' => $this->input->post('ALASAN_RESIGN'),
		'DESKRIPSI_PEKERJAAN' => $this->input->post('DESKRIPSI_PEKERJAAN'),
		'APPROVED' => "PENDING",
		);
		$last_id = $this->crud->buat('pengalaman_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data pengalaman kerja berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_kerja'));
	}

	public function proses_edit()
	{

		$ID = $this->input->post('last_id');
		$data = array(
									'NAMA_PERUSAHAAN' => $this->input->post('NAMA_PERUSAHAAN'),
									'BIDANG_USAHA' => $this->input->post('BIDANG_USAHA'),
									'JABATAN_AWAL' => $this->input->post('JABATAN_AWAL'),
									'JABATAN_AKHIR' => $this->input->post('JABATAN_AKHIR'),
									'ALAMAT_PERUSAHAAN' => $this->input->post('ALAMAT_PERUSAHAAN'),
									'ATASAN' => $this->input->post('ATASAN'),
									'NO_TELP_PERUSAHAAN' => $this->input->post('NO_TELP_PERUSAHAAN'),
									'PERIODE_BEKERJA' => $this->input->post('PERIODE_BEKERJA'),
									'GAPOK_SEBELUMNYA' => $this->input->post('GAPOK_SEBELUMNYA'),
									'TUNJANGAN_LAINNYA' => $this->input->post('TUNJANGAN_LAINNYA'),
									'ALASAN_RESIGN' => $this->input->post('ALASAN_RESIGN'),
									'DESKRIPSI_PEKERJAAN' => $this->input->post('DESKRIPSI_PEKERJAAN'),
									);
		$last_id = $this->crud->update(array('PENGALAMAN_KARYAWAN_ID' => $ID), 'pengalaman_karyawan', $data);

		if($last_id) $this->session->set_flashdata('success', "Data pengalaman kerja berhasil di ubah ");
		else $this->session->set_flashdata('failed', "Data gagal di ubah");

		redirect(base_url('profile_kerja'));
	}

	function show_form_edit($id = null){
		$Q = "SELECT * FROM pengalaman_karyawan WHERE PENGALAMAN_KARYAWAN_ID = '$id'";
		$data['detail'] = $this->db->query($Q);
		$this->load->view("web/profile/profile_kerja_edit", $data);
	}



	public function delete($id=null)
	{
		$Q="DELETE FROM pengalaman_karyawan WHERE PENGALAMAN_KARYAWAN_ID = '$id'";
		$this->db->query($Q);
		$this->session->set_flashdata('success', "Data pengalaman kerja berhasil di hapus");

		redirect(base_url('profile_kerja'));
	}



}
