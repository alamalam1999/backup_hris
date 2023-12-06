<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
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
		$data['title'] ="PROFILE";

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);

		$data['update_url'] = site_url('profile/proses_update/' . $ID);
		$data['js_script'] = 'js/karyawan_js';

		$data['content'] = 'web/profile/profile_karyawan';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/profile/profile_karyawan', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);
	}


	public function proses_update($id)
	{
		// need form validation
		$this->karyawan_model->update($id);
		redirect(site_url('profile'));
	}

	public function update_password()
	{
		if ($this->input->post('update_password')) {
			$old_pass = $this->input->post('old_pass');
			$new_pass = $this->input->post('new_pass');
			$confirm_pass = $this->input->post('confirm_pass');

			$id = $this->session->userdata('user_logged')->KARYAWAN_ID;
			$query = $this->db->query(" SELECT PASSWORD FROM karyawan WHERE KARYAWAN_ID='$id' ");
			$row = $query->row();
			$isPasswordTrue = password_verify($old_pass, $row->PASSWORD);
			if ($isPasswordTrue) {
				if($new_pass === $confirm_pass){
					$new_password = password_hash($new_pass, PASSWORD_DEFAULT);
					$this->karyawan_model->update_password($id, $new_password);
					$this->session->set_flashdata('success', "Success to change password");
				} else{
					$this->session->set_flashdata('error', "Failed to change password, your password not match");
				}
			} else {
				$this->session->set_flashdata('error', "Failed to change password, your old password false");
			}
		}

		$data['title'] = 'Change Password';

		$data['content'] = 'web/profile/update_password';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/profile/update_password', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);
	}
}
