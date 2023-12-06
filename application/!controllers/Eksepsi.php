<?php defined('BASEPATH') or exit('No direct script access allowed');

class Eksepsi extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		$this->load->model('crud');
		//$this->load->model('karyawan_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}



	public function index()
	{
		// $ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
		// $data['data_karyawan'] = $this->karyawan_model->get_by_id($ID);
		// echo "<pre>";
		// print_r($data['data_karyawan'] ); die();

		// $data['update_url'] = site_url('profile/proses_update/' . $ID);
		// $data['js_script'] = 'js/karyawan_js';
		$ID = getUserActive("KARYAWAN_ID");
		$Q = "SELECT * FROM eksepsi WHERE KARYAWAN_ID = '$ID' ORDER BY EKSEPSI_ID DESC";
		$data['data'] = $this->db->query($Q);

		$data['title'] = "Eksepsi";

		$data['content'] = 'web/eksepsi/eksepsi';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/eksepsi/eksepsi', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);
	}

	function add()
	{
		$data['title'] = "Form Tambah";

		$data['content'] = 'web/eksepsi/eksepsi_form_add';
		$this->load->view("web/templates",$data);

		// $this->load->view('templates/header', $data);
		// $this->load->view('templates/topbar');
		// $this->load->view('templates/sidebar');
		// $this->load->view('web/eksepsi/eksepsi_form_add', $data);
		// $this->load->view('templates/copyright');
		// $this->load->view('templates/footer', $data);

	}


	function add_proses()
	{

		$pesan = "";

			$data = array('PERIODE_ID' => $this->input->post('PERIODE_ID'),
										'KARYAWAN_ID' => getUserActive("KARYAWAN_ID"),
										'JENIS' => $this->input->post('JENIS'),
										'KETERANGAN' => $this->input->post('KETERANGAN'),
										'TGL_MULAI' => $this->input->post('TGL_MULAI'),
										'TGL_SELESAI' => $this->input->post('TGL_SELESAI'),
										'STATUS' => "PENDING",
									//	'FILE' => $this->input->post('PERIODE_ID'),
										);
			// echo "<pre>";
			// print_r(getUserActive("KARYAWAN_ID")); die();
			//echo $_FILES['FILE']['name']; die();


			$last_id = $this->crud->buat('eksepsi',$data);
			if($last_id)
			{


				if($_FILES['FILE']['name'])
				{
					$cek_upload = false;
					$cek_upload =  $this->upload($last_id , 'FILE' , '/uploads/skd/');
					//if($cek_upload) $this->IJAZAH  = $cek_upload;
					if($cek_upload)  $this->crud->update(array('EKSEPSI_ID' => $last_id), 'eksepsi', array('FILE' => $cek_upload));
				}


				$this->session->set_flashdata('success',"Sukses Menambah data Eksepsi");
			}else $this->session->set_flashdata('error',"Gagal Menambah data Eksepsi");


		redirect(base_url('eksepsi'));

	}


	function upload($id = null ,$file = "" , $path = '/uploads/cv/')
  {

    // echo "<pre>";
    // print_r($_SERVER['DOCUMENT_ROOT']); die();
    //echo clear_txt($_FILES[$file]['name'],"_","."); die();
    $filename = $id . '_'.$file.'_' . date('YmdHis') . '_' .  clear_txt($_FILES[$file]['name'],"_",".");
    $config['upload_path'] = hris('root'). $path ;
    //$config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/ypap/hadir/upload/';
    //$config['upload_path'] = './upload/';
    $config['file_name'] = $filename;
    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
    $config['max_size'] = 10000;
    //$this->load->library('upload', $config);

    $this->load->library('upload');
    $this->upload->initialize($config);
    if ($this->upload->do_upload($file)) {

      return $filename;
    } else {
      $pesan = $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
      return false;
    }
  }



}
