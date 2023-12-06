<?php defined('BASEPATH') or exit('No direct script access allowed');

class Profile_sertifikat extends CI_Controller
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
		$data['title'] ="Dokumen Karyawan";

		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);

		$Q = "SELECT *
					FROM dok_karyawan DK
					WHERE DK.KARYAWAN_ID='$ID'";
		$data['data_sertifikat'] = $this->db->query($Q);



		// echo "<pre>";
		// print_r($data['data_sertifikat']); die();

		$data['update_url'] = site_url('profile_sertifikat/proses_update/' . $ID);
		$data['js_script'] = 'js/karyawan_2_js';

		$data['content'] = 'web/profile/profile_sertifikat';
		$this->load->view("web/templates",$data);


	}


	public function proses_update($id)
	{
		$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		$FILE_DOK = '';
		if ($_FILES['FILE_DOK']['name']) {
			$cek_upload = false;
			$cek_upload =  $this->upload($ID , 'FILE_DOK' , 'uploads/karyawan/');

			if($cek_upload) $FILE_DOK = $cek_upload; 

		}
		$data = array('KARYAWAN_ID' => $ID,
									'MASA_BERLAKU' => $this->input->post('MASA_BERLAKU'),
									'DOK_KARYAWAN' => $this->input->post('DOK_KARYAWAN'),
									'APPROVED' => "PENDING",
									'FILE_DOK' => $FILE_DOK,
									);
		$last_id = $this->crud->buat('dok_karyawan',$data);

		if($last_id) $this->session->set_flashdata('success', "Data Dokumen berhasil di tambah ");
		else $this->session->set_flashdata('failed', "Data gagal di tambah");

		redirect(base_url('profile_sertifikat'));


}

public function delete($id=null)
{
	$Q="DELETE FROM dok_karyawan WHERE DOK_KARYAWAN_ID='$id'";
	$this->db->query($Q);
	$this->session->set_flashdata('success', "Data berhasil di hapus");
	redirect(site_url('profile_sertifikat'));
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
