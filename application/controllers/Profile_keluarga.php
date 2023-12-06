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
		$data['title'] ="Data Keluarga";

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
		$FILE_AKTE = '';
		// print_r($_FILES['FILE_AKTE']['name']); die();
		if ($_FILES['FILE_AKTE']['name']) {
			$cek_upload = false;
			$cek_upload =  $this->upload($ID , 'FILE_AKTE' , 'uploads/karyawan/');

			if($cek_upload) $FILE_AKTE = $cek_upload; 

		}
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
									'FILE_AKTE' => $FILE_AKTE,
									
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

	function upload($id = null ,$file = "" , $path = '/uploads/karyawan/')
  {

   	$uniq_id = uniqid();
    $filename = $id .$uniq_id. '_'.$file.'_' . date('YmdHis') . '_' .  clear_txt($_FILES[$file]['name'],"_",".");
    $config['upload_path'] = hris('root'). $path ;

   
    $config['file_name'] = $filename;
    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
    $config['max_size'] = 100000;
   

    $this->load->library('upload');
		$_FILES['userfile']['name']= $_FILES[$file]['name'];
    $_FILES['userfile']['type']= $_FILES[$file]['type'];
    $_FILES['userfile']['tmp_name']= $_FILES[$file]['tmp_name'];
    $_FILES['userfile']['error']= $_FILES[$file]['error'];
    $_FILES['userfile']['size']= $_FILES[$file]['size'];



    $this->upload->initialize($config);
    if ($this->upload->do_upload()) {
    	
      return $filename;
    } else {
    	print_r($this->upload->display_errors()); die();
      $pesan = $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
      return false;
    }
  }

}
