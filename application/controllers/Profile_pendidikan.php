<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile_pendidikan extends CI_Controller
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

		$Q = "SELECT * FROM pendidikan_karyawan WHERE KARYAWAN_ID='$ID' ORDER BY TAHUN_SELESAI ASC";
		$data['PEND_FORMAL'] = $this->db->query($Q);

		$Q2 = "SELECT * FROM kursus_karyawan WHERE KARYAWAN_ID='$ID'";
		$data['PEND_NONFORMAL'] = $this->db->query($Q2);

		$Q3 = "SELECT * FROM bahasa_karyawan WHERE KARYAWAN_ID='$ID'";
		$data['BHS_ASING'] = $this->db->query($Q3);


		$data['update_url'] = site_url('profile_pendidikan/proses_update/' . $ID);
		$data['js_script'] = 'js/karyawan_2_js';

		$data['content'] = 'web/profile/profile_pendidikan';
		$this->load->view("web/templates",$data);



	}

	public function proses_add_pend_formal()
	{

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$FILE_PENDIDIKAN = '';
		if ($_FILES['FILE_PENDIDIKAN']['name']) {
			$cek_upload = false;
			$cek_upload =  $this->upload($ID , 'FILE_PENDIDIKAN' , 'uploads/karyawan/');

			if($cek_upload) $FILE_PENDIDIKAN = $cek_upload; 

		}
		$data = array('KARYAWAN_ID' => $ID,
									'TINGKAT' => $this->input->post('TINGKAT'),
									'JURUSAN' => $this->input->post('JURUSAN'),
									'INSTITUSI' => $this->input->post('INSTITUSI'),
									'LOKASI' => $this->input->post('LOKASI'),
									'TAHUN_MULAI' => $this->input->post('TAHUN_MULAI'),
									'TAHUN_SELESAI' => $this->input->post('TAHUN_SELESAI'),
									'GPA' => $this->input->post('GPA'),
									'APPROVED' => "PENDING",
									'FILE_PENDIDIKAN' => $FILE_PENDIDIKAN,
									);
		$last_id = $this->crud->buat('pendidikan_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data pendidikan formal berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_pendidikan'));
	}

	public function proses_add_pend_non_formal()
	{
		$FILE_KURSUS = '';
		// print_r($_FILES['FILE_KURSUS']['name']); die();
		if ($_FILES['FILE_KURSUS']['name']) {
			$cek_upload = false;
			$cek_upload =  $this->upload($ID , 'FILE_KURSUS' , 'uploads/karyawan/');

			if($cek_upload) $FILE_KURSUS = $cek_upload; 

		}
		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data = array('KARYAWAN_ID' => $ID,
									'NAMA_KURSUS' => $this->input->post('NAMA_KURSUS'),
									'TEMPAT' => $this->input->post('TEMPAT'),
									'PERIODE_MULAI' => $this->input->post('PERIODE_MULAI'),
								//	'PERIODE_SELESAI' => $this->input->post('PERIODE_SELESAI'),
									'KETERANGAN' => $this->input->post('KETERANGAN'),
									'APPROVED' => "PENDING",
									'FILE_KURSUS' => $FILE_KURSUS,

									);
		$last_id = $this->crud->buat('kursus_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data Kursus berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_pendidikan'));
	}

	public function proses_add_bahasa()
	{

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data = array('KARYAWAN_ID' => $ID,
									'BAHASA' => $this->input->post('BAHASA'),
									'LISAN' => $this->input->post('LISAN'),
									'TULISAN' => $this->input->post('TULISAN'),
									'APPROVED' => "PENDING",
									);
		$last_id = $this->crud->buat('bahasa_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data Bahasa berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_pendidikan'));
	}

	public function delete($tipe=null , $id=null)
	{
		if($tipe == 1){
			$Q="DELETE FROM pendidikan_karyawan WHERE PENDIDIKAN_KARYAWAN_ID = '$id'";
			$this->db->query($Q);
			$this->session->set_flashdata('success', "Data pendidikan formal berhasil di hapus");
		}

		if($tipe == 2){
			$Q="DELETE FROM kursus_karyawan WHERE KURSUS_KARYAWAN_ID = '$id'";
			$this->db->query($Q);
			$this->session->set_flashdata('success', "Data kursus berhasil di hapus");
		}

		if($tipe == 3){
			$Q="DELETE FROM bahasa_karyawan WHERE BAHASA_KARYAWAN_ID = '$id'";
			$this->db->query($Q);
			$this->session->set_flashdata('success', "Data Bahasa berhasil di hapus");
		}

		redirect(base_url('profile_pendidikan'));
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
