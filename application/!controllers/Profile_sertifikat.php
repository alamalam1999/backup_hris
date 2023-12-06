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
		$data['title'] ="Profile  Sertifikat";

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

			// $KATEGORI_KEAHLIAN_ID = db_escape(get_input('KATEGORI_KEAHLIAN_ID'));
			// $KEAHLIAN_ID = db_escape(get_input('KEAHLIAN_ID'));
			// $SQL = db_execute(" UPDATE karyawan SET KATEGORI_KEAHLIAN_ID='$KATEGORI_KEAHLIAN_ID',KEAHLIAN_ID='$KEAHLIAN_ID' WHERE KARYAWAN_ID='$ID' ");
			$DOKUMEN_KARYAWAN = "";
			$DOK_KARYAWAN = get_input('DOK_KARYAWAN');
			//print_r($DOK_KARYAWAN); die();

			if (is_array($DOK_KARYAWAN) and count($DOK_KARYAWAN))
			{
				//print_r($DOK_KARYAWAN); die();
				foreach ($DOK_KARYAWAN as $key => $val)
				{
					if ($val != '')
					{
						// $IJAZAH 			= get_input('IJAZAH');
						// $SERTIFIKAT 		= get_input('SERTIFIKAT');
						// $SIO 				= get_input('SIO');
						// $KTA 				= get_input('KTA');
						// echo "ijasa"; print_r($IJAZAH); echo "<br>";
						// echo "sertifikat"; print_r($SERTIFIKAT); echo "<br>";
						// echo "sio"; print_r($SIO); echo "<br>";
						// echo "kta"; 	print_r($KTA); echo "<br>";
						// die();

						//$IJAZAH = $SERTIFIKAT = $SIO = $KTA	= "";


						$CURR_IJAZAH 		= get_input('CURR_IJAZAH');
						$CURR_SERTIFIKAT 	= get_input('CURR_SERTIFIKAT');
						$CURR_SIO 			= get_input('CURR_SIO');
						$CURR_KTA 			= get_input('CURR_KTA');

						if(isset($CURR_IJAZAH[$key]) == "xxx" OR isset($CURR_IJAZAH[$key]) == "")  $IJAZAH =  ''; else $IJAZAH =  "ADA";
						if(isset($CURR_SERTIFIKAT[$key]) == "xxx" OR isset($CURR_SERTIFIKAT[$key]) == "")  $SERTIFIKAT =  ''; else $SERTIFIKAT =  "ADA";
						if(isset($CURR_SIO[$key]) == "xxx" OR isset($CURR_SIO[$key]) == "")  $SIO =  ''; else $SIO =  "ADA";
						if(isset($CURR_KTA[$key]) == "xxx" OR isset($CURR_KTA[$key]) == "")  $KTA =  ''; else $KTA =  "ADA";

						//SET detault file
						if(isset($CURR_IJAZAH[$key])  == 'xxx' OR isset($CURR_IJAZAH[$key])  == '')  $FILES_IJAZAH = ''; else $FILES_IJAZAH = $CURR_IJAZAH[$key];
						if(isset($CURR_SERTIFIKAT[$key]) == 'xxx' OR isset($CURR_SERTIFIKAT[$key]) == '')  $FILES_SERTIFIKAT = ''; else $FILES_SERTIFIKAT = $CURR_SERTIFIKAT[$key];
						if(isset($CURR_SIO[$key]) == 'xxx' OR isset($CURR_SIO[$key]) == '') $FILES_SIO = ''; else $FILES_SIO = $CURR_SIO[$key];
						if(isset($CURR_KTA[$key]) == 'xxx' OR isset($CURR_KTA[$key]) == '') $FILES_KTA = ''; else $FILES_KTA = $CURR_KTA[$key];

						$valid_upload = 0;
						$MASA_IJAZAH 		= get_input('MASA_IJAZAH');
						$MASA_SERTIFIKAT 	= get_input('MASA_SERTIFIKAT');
						$MASA_SIO 			= get_input('MASA_SIO');
						$MASA_KTA 			= get_input('MASA_KTA');



						$KARYAWAN_ID 		= $id;
						$DOK_KARYAWAN 		= $val;

					

						$MASA_IJAZAH 		= isset($MASA_IJAZAH[$key]) ? $MASA_IJAZAH[$key] : '';
						$MASA_SERTIFIKAT 	= isset($MASA_SERTIFIKAT[$key]) ? $MASA_SERTIFIKAT[$key] : '';
						$MASA_SIO 			= isset($MASA_SIO[$key]) ? $MASA_SIO[$key] : '';
						$MASA_KTA 			= isset($MASA_KTA[$key]) ? $MASA_KTA[$key] : '';




						if ($_FILES['FILE_IJAZAH']['name'][$key]) {
							$cek_upload = false;
							$cek_upload =  $this->upload($id , 'FILE_IJAZAH' , 'uploads/karyawan/' , $key);

							if($cek_upload) $FILES_IJAZAH = $cek_upload;  $IJAZAH = "ADA"; $valid_upload=1;

						}

						if ($_FILES['FILE_SERTIFIKAT']['name'][$key]) {

							$cek_upload = false;
							$cek_upload =  $this->upload($id , 'FILE_SERTIFIKAT' , 'uploads/karyawan/' , $key);
							if($cek_upload) $FILES_SERTIFIKAT = $cek_upload; $SERTIFIKAT = "ADA"; $valid_upload=1;

						}

						if ($_FILES['FILE_SIO']['name'][$key]) {

							$cek_upload = false;
							$cek_upload =  $this->upload($id , 'FILE_SIO' , 'uploads/karyawan/' , $key);
							if($cek_upload) $FILES_SIO = $cek_upload; $SIO = "ADA"; $valid_upload=1;

						}

						if ($_FILES['FILE_KTA']['name'][$key]) {

							$cek_upload = false;
							$cek_upload =  $this->upload($id , 'FILE_KTA' , 'uploads/karyawan/' , $key);
							if($cek_upload) $FILES_KTA = $cek_upload; $KTA = "ADA"; $valid_upload=1;

						}

						$APPROVED_IJAZAH = $APPROVED_SERTIFIKAT = $APPROVED_SIO = $APPROVED_KTA = "PENDING";

						$DOKUMEN_KARYAWAN .= "('" . $KARYAWAN_ID . "','" . $DOK_KARYAWAN . "','" . $IJAZAH . "','" . $SERTIFIKAT . "','" . $SIO . "','" . $KTA . "','" . $FILES_IJAZAH . "','" . $FILES_SERTIFIKAT . "','" . $FILES_SIO . "','" .  $FILES_KTA . "','" .  $MASA_IJAZAH . "','" .  $MASA_SERTIFIKAT . "','" .  $MASA_SIO . "','" .  $MASA_KTA . "','" .  $APPROVED_IJAZAH . "','" .  $APPROVED_SERTIFIKAT . "','" .  $APPROVED_SIO . "','" .  $APPROVED_KTA . "'),";
					}

				}

				$DOKUMEN_KARYAWAN = rtrim($DOKUMEN_KARYAWAN, ',');
				if (!empty($DOKUMEN_KARYAWAN) && $valid_upload == 1)
				{
				//	db_execute(" DELETE FROM dok_karyawan WHERE KARYAWAN_ID='$id'");
					db_execute(" INSERT INTO dok_karyawan (KARYAWAN_ID,DOK_KARYAWAN,IJAZAH,SERTIFIKAT,SIO,KTA,FILE_IJAZAH,FILE_SERTIFIKAT,FILE_SIO,FILE_KTA,MASA_IJAZAH,MASA_SERTIFIKAT,MASA_SIO,MASA_KTA,APPROVED_IJAZAH,APPROVED_SERTIFIKAT,APPROVED_SIO,APPROVED_KTA) VALUES $DOKUMEN_KARYAWAN ");
					// sql_show(" INSERT INTO dok_karyawan (KARYAWAN_ID,DOK_KARYAWAN,IJAZAH,SERTIFIKAT,SIO,KTA,FILE_IJAZAH,FILE_SERTIFIKAT,FILE_SIO,FILE_KTA,MASA_IJAZAH,MASA_SERTIFIKAT,MASA_SIO,MASA_KTA,APPROVED_IJAZAH,APPROVED_SERTIFIKAT,APPROVED_SIO,APPROVED_KTA) VALUES $DOKUMEN_KARYAWAN ");
					// die();
				}else {
					$this->session->set_flashdata('error', "Data gagal di simpan");
					redirect(base_url('profile_sertifikat'));
				}
			}
			else {
			//	db_execute(" DELETE FROM dok_karyawan WHERE KARYAWAN_ID='$id'");
			}
$this->session->set_flashdata('success', "Data berhasil di simpan");
redirect(base_url('profile_sertifikat'));
	

	// $input = array('id_jo' => $data->id_jo,
	// 							 'id_wo' => $data->id_wo,
	// 							 'nomor_wo' => $nomor_wo,
	// 							 'lab' => 'kalibrasi',
	// 							 'no_surtug' => $data->nomor_surtug_pk,
	// 							 'tanggal_mulai' => $data->tanggal_surtug_pk,
	// 							 'tanggal_selesai' => $data->tanggal_surtug_selesai_pk,
	// 							 'catatan' => $data->catatan_surtug_pk,
	// 							 'redaksi_tujuan' => $data->keterangan_surtug_pk,
	// 							 'tempat' => $data->tempat_surtug_pk,
	// 							 //'download_file' => '',
	// 							 'peserta' => $data->petugas_kalibrasi,
	// 							);
	// 	$this->db->insert('log_surpel_history', $input);
}

public function delete($id=null)
{
	$Q="DELETE FROM dok_karyawan WHERE DOK_KARYAWAN_ID='$id'";
	$this->db->query($Q);
	$this->session->set_flashdata('success', "Data berhasil di hapus");
	redirect(base_url('profile_sertifikat'));
}



	function upload($id = null ,$file = "" , $path = '/uploads/cv/' , $i=null)
  {

    //echo "<pre>";
    //print_r($file); die();
    //echo clear_txt($_FILES[$file]['name'],"_","."); die();
    $filename = $id . '_'.$file.'_' . date('YmdHis') . '_' .  clear_txt($_FILES[$file]['name'][$i],"_",".");
    $config['upload_path'] = hris('root'). $path ;

    //$config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/ypap/hadir/upload/';
    //echo   $config['upload_path']; die();
    //$config['upload_path'] = './upload/';
    $config['file_name'] = $filename;
    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
    $config['max_size'] = 100000;
    //$this->load->library('upload', $config);

    $this->load->library('upload');
		$_FILES['userfile']['name']= $_FILES[$file]['name'][$i];
    $_FILES['userfile']['type']= $_FILES[$file]['type'][$i];
    $_FILES['userfile']['tmp_name']= $_FILES[$file]['tmp_name'][$i];
    $_FILES['userfile']['error']= $_FILES[$file]['error'][$i];
    $_FILES['userfile']['size']= $_FILES[$file]['size'][$i];



    $this->upload->initialize($config);
    if ($this->upload->do_upload()) {
    	//print_r($filename); die();
      return $filename;
    } else {
    	print_r($this->upload->display_errors()); die();
      $pesan = $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
      return false;
    }
  }




}
