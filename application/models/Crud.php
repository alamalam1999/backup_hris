<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Crud extends CI_Model
{

	public function buat($table, $data)
	{
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	public function tampil($table)
	{
		$query = $this->db->get($table);
		return $query->result();
	}

	public function tampil_where($table, $where)
	{
		$query = $this->db->get_where($table, $where);
		return $query->result();
	}

	public function tampil_where_desc($table, $where, $id_table)
	{
		$this->db->from($table);
		$this->db->where($where);
		$this->db->order_by($id_table, 'DESC');
		$query = $this->db->get();

		return $query->result();
	}

	public function tampil_desc($table, $id_table)
	{
		$this->db->from($table);
		$this->db->order_by($id_table, 'DESC');
		$query = $this->db->get();

		return $query->result();
	}



	public function update($where, $table, $data)
	{
		$this->db->where($where);
		return $this->db->update($table, $data);
		#echo $this->db->last_query(); die;
	}

	public function hapus($where, $table)
	{
		$this->db->where($where);
		$this->db->delete($table);
	}
	public function non_aktif($where, $table)
	{
		//echo "disini lah";die();
		$this->db->where($where);
		$data = array('aktif' => 0);
		$this->db->update($table, $data);
	}



	/*buatan agung*/

	public function set_query($Q)
	{
		$query = $this->db->query($Q);
		#echo $this->db->last_query(); die;

		return $query;
		#return $query->num_rows();
	}

	public function set_query_result($Q)
	{
		$query = $this->db->query($Q);
		#echo $this->db->last_query(); die;

		return $query->result();
		#return $query->num_rows();
	}

	public function set_query_cek($Q)
	{
		$query = $this->db->query($Q);
		#echo $this->db->last_query(); die;

		#return $query->result();
		return $query->num_rows();
	}

	/*public function detail($data,$table)
	{
		$this->db->insert($table, $data);

		return $this->db->insert_id();

	}*/

	public function cek_username($where, $table)
	{
		$query = $this->db->get_where($table, $where);
		return $query->num_rows();
	}

	public function not_null($id)
	{
		if ($id) {
			return true;
		} else {
			$this->alert('failed', 'Load Data ');
			redirect(base_url('login/logout'));
		}
	}

	public function alert($status, $message)
	{
		switch ($status) {
			case 'success':
				$alert = '<div class="alert alert-dismissible alert-success" style="color: #fff">';
				$alert .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
				$alert .= '<h4>Sukses!</h4>';
				$alert .= "<p>$message</p>";
				$alert .= '</div>';
				$this->session->set_flashdata('pesan', $alert);
				break;

			case 'failed':
				$alert = '<div class="alert alert-dismissible alert-warning" style="color: #000">';
				$alert .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
				$alert .= '<h4>Gagal!</h4>';
				$alert .= "<p>$message</p>";
				if ($status == 'Upload') $alert .= "<p> - Periksa Kembali Jenis File yang yang diupload</p>";
				$alert .= '</div>';
				$this->session->set_flashdata('pesan', $alert);
				break;
		}
	}


	#pecah multidata ke array  HashTag
	function pecahData($data, $karakter, $table, $id)
	{
		$k = explode($karakter, $data);
		#$dat= array('Semua');
		$dat = array();
		$i = 1;
		foreach ($k as $idx) {
			$Q = "SELECT * FROM $table WHERE $id='$idx'";
			$row = $this->db->query($Q)->result();

			foreach ($row as $t) {
				$dat[$i] = $t->hashtag;
				$i++;
			}
		}

		#gabungin data array dipisahkan dengan comma(,)
		//print_r($dat);
		if (empty($dat)) {
			$data = "Semua";
		} else {
			$data = implode(', ', $dat);
		}
		return $data;
	}
}
