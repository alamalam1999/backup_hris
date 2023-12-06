<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile_keluarga extends CI_Controller
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
		$data['title'] ="Profile  Pendidikan";

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		//$data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);

		$Q = "SELECT * FROM keluarga_karyawan WHERE KARYAWAN_ID='$ID' AND JENIS_KELUARGA='INTI'";
		$data['KEL_INTI'] = $this->db->query($Q);

		$Q2 = "SELECT * FROM keluarga_karyawan WHERE KARYAWAN_ID='$ID' AND JENIS_KELUARGA='BESAR'";
		$data['KEL_BESAR'] = $this->db->query($Q2);


		// $data['update_url'] = site_url('profile_pendidikan/proses_update/' . $ID);
		// $data['js_script'] = 'js/karyawan_2_js';

		$data['content'] = 'web/profile/profile_keluarga';
		$this->load->view("web/templates",$data);



	}

	public function proses_add()
	{

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data = array('KARYAWAN_ID' => $ID,
									'ANGGOTA_KELUARGA' => $this->input->post('ANGGOTA_KELUARGA'),
									'NAMA_KELUARGA' => $this->input->post('NAMA_KELUARGA'),
									'GENDER' => $this->input->post('GENDER'),
									'TP_LAHIR_KELUARGA' => $this->input->post('TP_LAHIR_KELUARGA'),
									'TGL_LAHIR_KELUARGA' => $this->input->post('TGL_LAHIR_KELUARGA'),
									'PENDIDIKAN_KELUARGA' => $this->input->post('PENDIDIKAN_KELUARGA'),
									'PEKERJAAN_KELUARGA' => $this->input->post('PEKERJAAN_KELUARGA'),
									'JENIS_KELUARGA' => $this->input->post('JENIS'),
									'APPROVED' => "PENDING",
									);
		$last_id = $this->crud->buat('keluarga_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data keluarga berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_keluarga'));
	}



	public function delete($id=null)
	{
		$Q="DELETE FROM keluarga_karyawan WHERE KELUARGA_KARYAWAN_ID = '$id'";
		$this->db->query($Q);
		$this->session->set_flashdata('success', "Data keluarga berhasil di hapus");

		redirect(base_url('profile_keluarga'));
	}



}
