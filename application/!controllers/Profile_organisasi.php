<?php defined('BASEPATH') or exit('No direct script access allowed');

class profile_organisasi extends CI_Controller
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
		$data['title'] ="Profile  Organisasi";

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		//$data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);

		$Q = "SELECT * FROM organisasi_karyawan WHERE KARYAWAN_ID='$ID'";
		$data['ORGANISASI'] = $this->db->query($Q);

		//$data['update_url'] = site_url('profile_pendidikan/proses_update/' . $ID);
		// $data['js_script'] = 'js/karyawan_2_js';

		$data['content'] = 'web/profile/profile_organisasi';
		$this->load->view("web/templates",$data);



	}

	public function proses_add()
	{

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data = array('KARYAWAN_ID' => $ID,
									'NAMA_ORGANISASI' => $this->input->post('NAMA_ORGANISASI'),
									'JABATAN_ORGANISASI' => $this->input->post('JABATAN_ORGANISASI'),
									'LOKASI_ORGANISASI' => $this->input->post('LOKASI_ORGANISASI'),
									'PERIODE_ORGANISASI' => $this->input->post('PERIODE_ORGANISASI'),
									'APPROVED' => "PENDING",
									);
		$last_id = $this->crud->buat('organisasi_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data organisasi  berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_organisasi'));
	}



	public function delete($id=null)
	{
		$Q="DELETE FROM organisasi_karyawan WHERE ORGANISASI_KARYAWAN_ID = '$id'";
		$this->db->query($Q);
		$this->session->set_flashdata('success', "Data organisasi berhasil di hapus");

		redirect(base_url('profile_organisasi'));
	}



}
