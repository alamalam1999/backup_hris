<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
  private $customer = 'customer';
  private $pelatihan_peserta = 'pelatihan_peserta';

  public function get_by_id($id)
  {
    return $this->db->get_where($this->customer, ['id_customer' => $id])->row();
  }

  public function get_peserta_by_id($id)
  {
    return $this->db->query(" SELECT PP.*, C.nama AS nama_customer
      FROM $this->pelatihan_peserta PP
      LEFT JOIN $this->customer C ON C.id_customer = PP.id_customer
      WHERE PP.id_peserta = $id
    ")->row();
  }

  public function update($id)
  {
    $post = $this->input->post();
    $this->telpon = $post['telpon'];
    $this->id_propinsi = $post['id_propinsi'];
    $this->fax = $post['fax'];
    $this->pic = $post['pic'];
    $this->email = $post['email'];
    $this->alamat = $post['alamat'];
    $this->username = $post['username'];

    $filename = 'CID_' . $id . '_' . date('YmdHis') . '_' .  $_FILES['file_foto']['name'];
    $config['upload_path'] = './upload/customer/';
    $config['file_name'] = $filename;
    $config['allowed_types'] = 'jpg|jpeg|png|pdf';
    $config['max_size'] = 10000;
    $this->load->library('upload', $config);
    if ($this->upload->do_upload('file_foto')) {
      $this->user_avatar = str_replace(' ', '_', $filename);
    } else {
      $this->session->set_flashdata('error', strip_tags($this->upload->display_errors()));
      return false;
    }

    $this->db->update('customer', $this, array('id_customer' => $id));
    $this->db->where('id_customer', $id);
    $user = $this->db->get('customer')->row();
    return $this->session->set_userdata(['user_logged' => $user]);
  }

  public function update_up($id)
  {
    $post = $this->input->post();
    $this->nama_kepala = $post['nama_kepala'];
    $this->no_akreditasi = $post['no_akreditasi'];
    $this->no_izin_operasional = $post['no_izin_operasional'];

    $file_sk = 'FSK_' . $id . '_' . date('YmdHis') . '_' . $_FILES['file_izin_operasional']['name'];
    $config['upload_path'] = './upload/customer/';
    $config['file_name'] = $file_sk;
    $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
    $config['max_size'] = 10000;
    //$this->load->library('upload', $config);
    $this->upload->initialize($config);
    if ($this->upload->do_upload('file_izin_operasional')) {
      $this->file_izin_operasional = str_replace(' ', '_', $file_sk);
    } else {
      array('error' => $this->upload->display_errors());
    }

    $this->email_pic = $post['email_pic'];
    $this->no_hp_pic = $post['no_hp_pic'];
    $this->telpon = $post['telpon'];
    $this->id_propinsi = $post['id_propinsi'];
    $this->fax = $post['fax'];
    $this->pic = $post['pic'];
    $this->email = $post['email'];
    $this->alamat = $post['alamat'];
    $this->username = $post['username'];

    $filename = 'CID_' . $id . '_' . date('YmdHis') . '_' .  $_FILES['file_foto']['name'];
    $config_pp['upload_path'] = './upload/customer/';
    $config_pp['file_name'] = $filename;
    $config_pp['allowed_types'] = 'gif|jpg|png|jpeg';
    $config_pp['max_size'] = 10000;
    $config_pp['max_width'] = 1024;
    $config_pp['max_height'] = 768;
    $this->upload->initialize($config_pp);
    if ($this->upload->do_upload('file_foto')) {
      $this->user_avatar = str_replace(' ', '_', $file_sk);
    } else {
      array('error' => $this->upload->display_errors());
    }

    $this->db->update('customer', $this, array('id_customer' => $id));
    $this->db->where('id_customer', $id);
    $user = $this->db->get('customer')->row();
    $this->session->set_userdata(['user_logged' => $user]);
  }

  public function update_user_pelatihan($id)
  {
    $post = $this->input->post();
    $this->telephone = $post['telephone'];
    $this->email = $post['email'];

    $filename = 'CID_' . $id . '_' . date('YmdHis') . '_' .  $_FILES['file_foto']['name'];;
    $config['upload_path'] = './upload/customer/';
    $config['file_name'] = $filename;
    $config['allowed_types'] = 'gif|jpg|png|jpeg';
    $config['max_size'] = 10000;
    $config['max_width'] = 1024;
    $config['max_height'] = 768;
    $this->load->library('upload', $config);
    if ($this->upload->do_upload('file_foto')) {
      $this->user_avatar = str_replace(' ', '_', $filename);
    } else {
      array('error' => $this->upload->display_errors());
    }

    $this->db->update('pelatihan_peserta', $this, array('id_peserta' => $id));
    $this->db->where('id_peserta', $id);
    $user = $this->db->get('pelatihan_peserta')->row();
    $this->session->set_userdata(['user_logged' => $user]);
  }
}
