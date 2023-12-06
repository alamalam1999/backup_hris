<?php defined('BASEPATH') or exit('No direct script access allowed');

class Data_json extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}

	public function getPropinsi()
	{
			$q = get_input('q');
			$DATA = $this->db->query(" SELECT * FROM provinsi WHERE (UCASE(PROVINSI) LIKE UCASE('$q%')) ORDER BY PROVINSI ASC LIMIT 10 ")->result();

			$t = array();
			if(count($DATA)>0)
			{
				foreach($DATA as $key => $row){
					$t[$key]['id'] = $row->PROVINSI;
					$t[$key]['text'] = $row->PROVINSI;
					$t[$key]['kode'] = $row->PROVINSI_ID;
				}
			}

			$res['results'] = $t;
			echo json_encode($res);
	}



	public function getKota()
	{
		$q = get_input('q');
		$PROVINSI_ID = get_input('provinsi_id');

		$PAGE = get_input('page_limit');

		$DATA = $this->db->query(" SELECT * FROM kota WHERE PROVINSI_ID='$PROVINSI_ID' AND (UCASE(KOTA) LIKE UCASE('$q%')) ORDER BY KOTA ASC LIMIT $PAGE ")->result();

		$t = array();
		if(count($DATA)>0)
		{
			foreach($DATA as $key => $row){
				$t[$key]['id'] = $row->KOTA;
				$t[$key]['text'] = $row->KOTA;
			}
		}

		$res['results'] = $t;
		echo json_encode($res);

	}


}
