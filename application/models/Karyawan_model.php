<?php defined('BASEPATH') or exit('No direct script access allowed');

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class Karyawan_model extends CI_Model
{
  private $user = 'karyawan';

  public function get_by_id($id)
  {
    return $this->db->get_where($this->user, ['KARYAWAN_ID' => $id])->row();
  }



  public function update($id)
  {
    //$post = $this->input->post();
    //$provonsi_ktp = $this->input->post('PROVINSI_KTP');
    // echo $provonsi_ktp;
    // echo "<pre>";
    // print_r($post ); die();
    $this->NAMA_PANGGILAN = $this->input->post('NAMA_PANGGILAN');
    $this->JK = $this->input->post('JK');
    $this->TP_LAHIR = $this->input->post('TP_LAHIR');
    $this->TGL_LAHIR = $this->input->post('TGL_LAHIR');
    $this->KEWARGANEGARAAN = $this->input->post('KEWARGANEGARAAN');
    $this->SUKU = $this->input->post('SUKU');
    $this->AGAMA = $this->input->post('AGAMA');
    $this->GOL_DARAH = $this->input->post('GOL_DARAH');
    $this->TINGGI = $this->input->post('TINGGI');
    $this->BERAT = $this->input->post('BERAT');
    $this->UKURAN_BAJU = $this->input->post('UKURAN_BAJU');
    $this->UKURAN_SEPATU = $this->input->post('UKURAN_SEPATU');
    $this->TELP = $this->input->post('TELP');
    $this->HP = $this->input->post('HP');
    //$this->EMAIL = $this->input->post('EMAIL');

    $this->NO_IDENTITAS = $this->input->post('NO_IDENTITAS'); #cek upload => FC_KTP
    if($_FILES['FC_KTP']['name'])
    {
       $cek_upload = false;
       $cek_upload =  $this->upload($id , 'FC_KTP' , '/uploads/cv/');
       if($cek_upload) $this->FC_KTP  = $cek_upload;
    }



    $this->NPWP = $this->input->post('NPWP'); #cek upload => FC_NPWP
    if($_FILES['FC_NPWP']['name'])
    {
      $cek_upload = false;
      $cek_upload =  $this->upload($id , 'FC_NPWP');
      if($cek_upload) $this->FC_NPWP  = $cek_upload;
    }

    $this->BPJS_KESEHATAN = $this->input->post('BPJS_KESEHATAN'); #cek upload => FC_BPJS_KESEHATAN
    if($_FILES['FC_BPJS_KESEHATAN']['name'])
    {
      $cek_upload = false;
      $cek_upload =  $this->upload($id , 'FC_BPJS_KESEHATAN');
      if($cek_upload) $this->FC_BPJS_KESEHATAN  = $cek_upload;
    }


    $this->BPJS_KETENAGAKERJAAN = $this->input->post('BPJS_KETENAGAKERJAAN'); #cek upload => FC_BPJS_KETENAGAKERJAAN
    if($_FILES['FC_BPJS_KETENAGAKERJAAN']['name'])
    {
      $cek_upload = false;
      $cek_upload =  $this->upload($id , 'FC_BPJS_KETENAGAKERJAAN');
      if($cek_upload) $this->FC_BPJS_KETENAGAKERJAAN  = $cek_upload;
    }

    $this->ST_KAWIN = $this->input->post('ST_KAWIN');
    $this->PUNYA_KENDARAAN = $this->input->post('PUNYA_KENDARAAN');
    $this->JENIS_KENDARAAN = $this->input->post('JENIS_KENDARAAN');
    $this->MILIK_KENDARAAN = $this->input->post('MILIK_KENDARAAN');


    $this->ALAMAT_KTP = $this->input->post('ALAMAT_KTP');
    $this->KELURAHAN_KTP = $this->input->post('KELURAHAN_KTP');
    $this->KECAMATAN_KTP = $this->input->post('KECAMATAN_KTP');
    $this->PROVINSI_KTP = $this->input->post('PROVINSI_KTP');
    $this->KOTA_KTP = $this->input->post('KOTA_KTP');
    $this->KODE_POS_KTP = $this->input->post('KODE_POS_KTP');
    $this->RT_KTP = $this->input->post('RT_KTP');
    $this->RW_KTP = $this->input->post('RW_KTP');


    $this->ALAMAT = $this->input->post('ALAMAT');
    $this->KELURAHAN = $this->input->post('KELURAHAN');
    $this->KECAMATAN = $this->input->post('KECAMATAN');
    $this->PROVINSI = $this->input->post('PROVINSI');
    $this->KOTA = $this->input->post('KOTA');
    $this->KODE_POS = $this->input->post('KODE_POS');
    $this->RT = $this->input->post('RT');
    $this->RW = $this->input->post('RW');


    $this->TEMPAT_TINGGAL = $this->input->post('TEMPAT_TINGGAL');
     #cek upload => FOTO
     if($_FILES['FOTO']['name'])
     {
       $cek_upload = false;
       $cek_upload =  $this->upload($id , 'FOTO' , '/uploads/foto/');
       if($cek_upload) $this->FOTO  = $cek_upload;
     }

    $this->SCAN_IJAZAH = $this->input->post('SCAN_IJAZAH'); #cek upload => FILE_SCAN_IJAZAH
     #cek upload => CV
     if($_FILES['FILE_SCAN_IJAZAH']['name'])
     {
       $cek_upload = false;
       $cek_upload =  $this->upload($id , 'FILE_SCAN_IJAZAH' , '/uploads/ijazah/');
       if($cek_upload) $this->IJAZAH  = $cek_upload;
     }
     if($_FILES['CV']['name'])
     {
       $cek_upload = false;
       $cek_upload =  $this->upload($id , 'CV' , '/uploads/cv/');
       if($cek_upload) $this->CV  = $cek_upload;
     }

    // echo $cek_upload; die();
    $this->session->set_flashdata('success', "Success to Update file");
   $this->db->update('karyawan', $this, array('KARYAWAN_ID' => $id));

  }


  function upload($id = null ,$file = "" , $path = '/uploads/cv/')
  {

    // echo "<pre>";
    // print_r($_SERVER['DOCUMENT_ROOT']); die();
    //echo clear_txt($_FILES[$file]['name'],"_","."); die();
    $filename = $id . '_'.$file.'_' . date('YmdHis') . '_' .  clear_txt($_FILES[$file]['name'],"_",".");
    $config['upload_path'] = hris('root'). $path ;
    //$config['upload_path'] = $_SERVER['DOCUMENT_ROOT'] . '/ypap/hadir/upload/';
    //echo   $config['upload_path']; die();
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

  function update_password($id, $new_password)
  {
    return $this->db->query(" UPDATE karyawan SET PASSWORD='$new_password' WHERE KARYAWAN_ID='$id' ");
  }


}
