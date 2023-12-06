<?php defined('BASEPATH') or exit('No direct script access allowed');

class Absen_in_out extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
		if ($this->auth_model->is_not_login() || $this->session->userdata('page') != 'karyawan') redirect(site_url('auth'));
	}

	public function index()
	{
		$today = date('Y-m-d');
		$karyawan_id = getUserActive("KARYAWAN_ID");
		$sql = " SELECT SK.*, J.JABATAN,K.NAMA, K.NIK, S.START_TIME, S.FINISH_TIME, S.START_BEGIN, S.START_END, S.FINISH_BEGIN, S.FINISH_END, S.STATUS, S.LONGITUDE, S.LATITUDE 
			FROM shift_karyawan SK
			LEFT JOIN karyawan K ON K.KARYAWAN_ID = SK.KARYAWAN_ID
			LEFT JOIN jabatan J ON J.JABATAN_ID = K.JABATAN_ID
			LEFT JOIN shift S ON S.SHIFT_CODE = SK.SHIFT_CODE
			WHERE  SK.KARYAWAN_ID = '$karyawan_id' AND SK.DATE = '$today'
			ORDER BY SK.DATE ASC  LIMIT 1
		";

		$data['data'] = $this->db->query($sql)->row();
		$data['title'] = "Absen Online";
		$data['content'] = 'web/absen_in_out/absen_in_out';
		$this->load->view("web/templates", $data);
	}

	public function attendance($type = "")
	{
		$status_device = 'destkop';
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
			$status_device = 'mobile';
		}
			
		$today = date('Y-m-d');
		$karyawan_id = getUserActive("KARYAWAN_ID");
		$sql = " SELECT SK.*, K.NAMA, K.NIK, S.START_TIME, S.FINISH_TIME, S.START_BEGIN, S.START_END, S.FINISH_BEGIN, S.FINISH_END, S.STATUS, S.LONGITUDE, S.LATITUDE, S.LONGITUDE
			FROM shift_karyawan SK
			LEFT JOIN karyawan K ON K.KARYAWAN_ID = SK.KARYAWAN_ID
			LEFT JOIN shift S ON S.SHIFT_CODE = SK.SHIFT_CODE
			WHERE  SK.KARYAWAN_ID = '$karyawan_id' AND DATE(DATE) = '$today'
			ORDER BY DATE ASC  LIMIT 1 
		";

		$row = $this->db->query($sql)->row();
		$check_valid_time = false;

		if ($row) {
			$data['data'] = $row;

			if ($type == 'checkin') {
				$TIME = $row->START_TIME;
				$check_valid_time = check_time_range($row->DATE, $TIME, $row->START_BEGIN, $row->START_END);
			} else if ($type == 'checkout') {
				$TIME = $row->FINISH_TIME;
				$check_valid_time = check_time_range($row->DATE, $TIME, $row->FINISH_BEGIN, $row->FINISH_END);
			} else {
				$TIME = '00:00:00';
				$check_valid_time = false;
			}
		}

		if (!$check_valid_time) {
			$this->session->set_flashdata('failed', "Absen Tidak Valid");
			redirect(site_url("absen_in_out"));
		}
		
		$data['status_device'] = $status_device;
		$data['type'] = $type;
		if ($type == 'checkin') {
			$data['title'] = "Absen Masuk";
			$data['content'] = 'web/absen_in_out/absen_in_out_form_checkin';
		} else if ($type == 'checkout') {
			$data['title'] = "Absen Keluar";
			$data['content'] = 'web/absen_in_out/absen_in_out_form_checkin';
		}
		$this->load->view("web/templates", $data);
	}

	function proses_add()
	{
		$img = $_POST['image'];

		// app HRIS => uploads/absen/
		$folderPath = hris('root') . "uploads/absen/"; 

		$image_parts = explode(";base64,", $img);
		$image_type_aux = explode("image/", $image_parts[0]);
		$image_type = $image_type_aux[1];

		$image_base64 = base64_decode($image_parts[1]);
		//$fileName = uniqid() . '.png';
		$fileName = date('YmdHis') . '.png';

		$file = $folderPath . $fileName;
		file_put_contents($file, $image_base64);
		if (file_exists($file)) {
			echo "SUKSES UPLOAD <br>";

			$JENIS = '';
			$tipe = $this->input->post('TIPE');
			if ($tipe == 'checkin') {
				$JENIS = 'IN';
			} else if ($tipe == 'checkout') {
				$JENIS = 'OUT';
			}

			$ID = $this->session->userdata('user_logged')->KARYAWAN_ID;
			$PERIODE_ID = $this->input->post('PERIODE_ID');
			$data = array(
				'PIN' => $ID,
				'PERIODE_ID' => $PERIODE_ID,
				'TANGGAL_ABSEN' => date("Y-m-d H:i:s"),
				'JENIS_ABSEN' => $JENIS,
				'FOTO' => $fileName,
				'LATITUDE' => $this->input->post('LATITUDE'),
				'LONGITUDE' => $this->input->post('LONGITUDE'),
				'STATUS' => 'PENDING',
			);

			$last_id = $this->crud->buat('log_online', $data);
			if ($last_id) {
				$this->session->set_flashdata('success', "Absen anda berhasil");
			}
		} else {
			echo "GAGAL UPLOAD";
		}
		
		redirect(site_url('absen_in_out'));
	}
}