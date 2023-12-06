<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
  public function is_not_login()
  {
    $data = $this->session->userdata('user_logged');
    $page = $this->session->userdata('page');
    if ($data === null && $page === null) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function do_login()
  {
    $page = $this->input->post('login_as');
    $email = $this->input->post('email');
    $password = $this->input->post('password');


    switch ($page) {
      case 'karyawan':
        $this->db->where('EMAIL', $email);
        $this->db->where('IS_ACTIVE', 1);
        $user = $this->db->get('karyawan')->row();
        if ($user) {
          $isPasswordTrue = password_verify($password, $user->PASSWORD);
          //$isPasswordTrue = TRUE;
          //if($password == $user->PASSWORD) $isPasswordTrue = true;
          if ($isPasswordTrue) {
            $this->session->set_userdata(['user_logged' => $user]);
            $this->session->set_userdata(['page' => $page]);
            return TRUE;
          }
        }
        break;

    }

    // login failed
    return FALSE;
  }

  public function register_customer($category)
  {
    $this->db->trans_start();
    $email = $this->input->post('email', true);
    $data = [
      'kategori' => $category,
      'status' => htmlspecialchars($this->input->post('status', true)),
      'nama' => htmlspecialchars($this->input->post('nama', true)),
      'pic' => htmlspecialchars($this->input->post('pic', true)),
      'id_propinsi' => htmlspecialchars($this->input->post('id_propinsi', true)),
      'alamat' => htmlspecialchars($this->input->post('alamat', true)),
      'telpon' => htmlspecialchars($this->input->post('telpon', true)),
      'email' => htmlspecialchars($email),
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'is_active' => 0,
      'is_validate' => 0,
      'date_created' => time()
    ];

    // generate unique_id
    $unique_id = $this->db->query("SELECT UUID() AS unique_id")->row()->unique_id;
    $this->db->set('unique_id', $unique_id);
    $add_customer = $this->db->insert('customer', $data);

    // siapkan token
    $token = base64_encode(random_bytes(32));
    $user_token = [
      'email' => $email,
      'token' => $token,
      'date_created' => time()
    ];
    $create_token = $this->db->insert('customer_token', $user_token);

    $name = '';
    $address = '';
    if ($add_customer && $create_token) {
      $this->_sendEmail($token, 'verify', 'customer', $name, $address);
    }

    return $this->db->trans_complete();
  }

  public function register_pelatihan()
  {
    $this->db->trans_start();
    $email = $this->input->post('email', true);
    $category = 3;
    $data = [
      'kategori' => $category,
      'id_customer' => htmlspecialchars($this->input->post('id_customer', true)),
      'nama' => htmlspecialchars($this->input->post('nama', true)),
      'telephone' => htmlspecialchars($this->input->post('telpon', true)),
      'email' => htmlspecialchars($email),
      'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
      'is_active' => 0,
      'is_validate' => 0,
      'date_created' => time()
    ];

    // generate unique_id
    //$unique_id = $this->db->query("SELECT UUID() AS unique_id")->row()->unique_id;
    //$this->db->set('unique_id', $unique_id);
    $add_peserta_pelatihan = $this->db->insert('pelatihan_peserta', $data);

    // siapkan token
    $token = base64_encode(random_bytes(32));
    $user_token = [
      'email' => $email,
      'token' => $token,
      'date_created' => time()
    ];
    $create_token = $this->db->insert('pelatihan_peserta_token', $user_token);

    $name = '';
    $address = '';
    if ($add_peserta_pelatihan && $create_token) {
      $this->_sendEmail($token, 'verify', 'pelatihan', $name, $address);
    }

    return $this->db->trans_complete();
  }

  public function verify_email_customer()
  {
    $this->db->trans_start();
    $email = $this->input->get('email');
    $token = $this->input->get('token');

    $user = $this->db->get_where('customer', ['email' => $email])->row_array();

    if ($user) {
      $customer_token = $this->db->get_where('customer_token', ['token' => $token])->row_array();

      if ($customer_token) {
        if (time() - $customer_token['date_created'] < (60 * 60 * 24)) {
          $this->db->set('is_active', 1);
          $this->db->where('email', $email);
          $this->db->update('customer');
          $this->db->delete('customer_token', ['email' => $email]);
          $result['status'] = 'success';
          $result['message'] = $email . ' telah terverifikasi! silakan login ke Aplikasi BPFK CSS, untuk melakukan monitoring pelayanan Anda.';
        } else {
          $this->db->delete('customer', ['email' => $email]);
          $this->db->delete('customer_token', ['email' => $email]);
          $result['status'] = 'error';
          $result['message'] = 'Aktivasi akun gagal! Token kadaluwarsa';
        }
      } else {
        $result['status'] = 'error';
        $result['message'] = 'Aktivasi akun gagal! Token salah';
      }
    } else {
      $result['status'] = 'error';
      $result['message'] = 'Aktivasi akun gagal! Email salah';
    }

    $this->db->trans_complete();
    return $result;
  }

  public function verify_email_pelatihan()
  {
    $this->db->trans_start();
    $email = $this->input->get('email');
    $token = $this->input->get('token');

    $user = $this->db->get_where('pelatihan_peserta', ['email' => $email])->row_array();

    if ($user) {
      $pelatihan_peserta_token = $this->db->get_where('pelatihan_peserta_token', ['token' => $token])->row_array();

      if ($pelatihan_peserta_token) {
        if (time() - $pelatihan_peserta_token['date_created'] < (60 * 60 * 24)) {
          $this->db->set('is_active', 1);
          $this->db->where('email', $email);
          $this->db->update('pelatihan_peserta');
          $this->db->delete('pelatihan_peserta_token', ['email' => $email]);
          $result['status'] = 'success';
          $result['message'] = $email . ' telah terverifikasi! sebelum dapat melakukan login, mohon tunggu sampai petugas kami melakukan validasi pada akun Anda';
        } else {
          $this->db->delete('pelatihan_peserta', ['email' => $email]);
          $this->db->delete('pelatihan_peserta_token', ['email' => $email]);
          $result['status'] = 'error';
          $result['message'] = 'Aktivasi akun gagal! Token kadaluwarsa';
        }
      } else {
        $result['status'] = 'error';
        $result['message'] = 'Aktivasi akun gagal! Token salah';
      }
    } else {
      $result['status'] = 'error';
      $result['message'] = 'Aktivasi akun gagal! Email salah';
    }

    $this->db->trans_complete();
    return $result;
  }

  public function forgot_password_account()
  {
    $account = $this->input->post('account_type');
    if ($account == 'customer' || $account == 'ujiprofisiensi') {
      $table = 'customer';
      $table_token = 'customer_token';
    } else if ($account == 'pelatihan') {
      $table = 'pelatihan_peserta';
      $table_token = 'pelatihan_peserta_token';
    }

    $this->db->trans_start();

    $email = $this->input->post('email');
    $customer = $this->db->get_where($table, ['email' => $email, 'is_active' => 1])->row_array();

    if ($customer) {
      $token = base64_encode(random_bytes(32));
      $customer_token = [
        'email' => $email,
        'token' => $token,
        'date_created' => time()
      ];

      $name = $customer['nama'];
      $address = $customer['alamat'];

      $this->db->insert($table_token, $customer_token);
      $this->_sendEmail($token, 'forgot', $account, $name, $address);

      $result['status'] = 'success';
      $result['message'] = 'Silahkan periksa email Anda untuk mengatur ulang sandi';
    } else {
      $result['status'] = 'error';
      $result['message'] = 'Email tidak terdaftar atau belum diaktifkan';
    }

    $this->db->trans_complete();
    return $result;
  }

  public function reset_password_account()
  {
    $account = $this->input->get('account_type');
    if ($account == 'customer' || $account == 'ujiprofisiensi') {
      $table = 'customer';
      $table_token = 'customer_token';
    } else if ($account == 'pelatihan') {
      $table = 'pelatihan_peserta';
      $table_token = 'pelatihan_peserta_token';
    }

    $email = $this->input->get('email');
    $token = $this->input->get('token');

    $this->db->trans_start();

    $user = $this->db->get_where($table, ['email' => $email])->row_array();

    if ($user) {
      $user_token = $this->db->get_where($table_token, ['token' => $token])->row_array();
      if ($user_token) {
        $result['email'] = $email;
        $result['account'] = $account;
        $result['status'] = 'success';
        $result['message'] = 'Redirect to change password page';
      } else {
        $result['status'] = 'error';
        $result['message'] = 'Reset kata sandi gagal! Token salah';
      }
    } else {
      $result['status'] = 'error';
      $result['message'] = 'Reset kata sandi gagal! Email salah';
    }

    $this->db->trans_complete();
    return $result;
  }

  public function change_password_account()
  {
    $account = $this->session->userdata('account_type');
    if ($account == 'customer' || $account == 'ujiprofisiensi') {
      $table = 'customer';
      $table_token = 'customer_token';
    } else if ($account == 'pelatihan') {
      $table = 'pelatihan_peserta';
      $table_token = 'pelatihan_peserta_token';
    }

    $this->db->trans_start();
    $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
    $email = $this->session->userdata('reset_email');

    $this->db->set('password', $password);
    $this->db->where('email', $email);
    $this->db->update($table);

    $this->session->unset_userdata('reset_email');
    $this->session->unset_userdata('account_type');
    $this->db->delete($table_token, ['email' => $email]);
    return $this->db->trans_complete();
  }

  private function _sendEmail($token, $type, $account, $name, $address)
  {
    $this->load->library('email');
    /* $config = [
      'protocol'  => 'smtp',
      'smtp_host' => 'ssl://mail.bpfkjakarta.or.id',
      'smtp_user' => 'css@bpfkjakarta.or.id',
      'smtp_pass' => '!pti202!*4$$#',
      'smtp_port' => 465,
      'mailtype'  => 'html',
      'charset'   => 'utf-8',
      'newline'   => "\r\n"
    ]; */

    $config = [
      'protocol'  => 'smtp',
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_user' => 'bpfkdemo@gmail.com',
      'smtp_pass' => 'ormruikakpfnzmhn',
      'smtp_port' => 465,
      'mailtype'  => 'html',
      'charset'   => 'utf-8',
      'newline'   => "\r\n"
    ];

    $this->email->initialize($config);
    $this->email->from('css@bpfkjakarta.or.id', 'BPFK CSS (Customer Self Service)');
    $this->email->to($this->input->post('email'));

    if ($account == 'customer' || $account == 'ujiprofisiensi') {
      if ($type == 'verify') {
        $this->email->subject('Account Verification');
        $this->email->message('
        <strong>Yth. ' . $this->input->post('nama') . '</strong><br>
        ' . $this->input->post('alamat') . '<br><br>

        Terimakasih telah mendaftar sebagai customer BPFK Jakarta.<br>
        Klik <a href="' . site_url() . 'auth/verify_customer?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Tautan ini</a> untuk Aktivasi akun anda.<br><br>

        Setelah aktivasi berhasil gunakan data email dan password saat registrasi untuk melakukan monitoring pelayanan Anda di :<br><br>
        <strong><a href="https://css.bpfkjakarta.or.id">https://css.bpfkjakarta.or.id</a></strong>
        ');
      } else if ($type == 'forgot') {
        $this->email->subject('Reset Password');
        //$this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/reset_password?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '&account_type=' . $account . '">Reset Password</a>');

        $this->email->message('
        <strong>Yth. ' . $name . '</strong><br>
        ' . $address .'<br><br>

        Terimakasih telah mendaftar sebagai customer BPFK Jakarta.<br>
        Klik <a href="' . base_url() . 'auth/reset_password?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '&account_type=' . $account . '">Tautan ini</a> untuk Reset password anda.<br><br>

        Selanjutnya silakan buat password baru Anda.<br><br>
        <strong><a href="https://css.bpfkjakarta.or.id">https://css.bpfkjakarta.or.id</a></strong>
        ');
      }
    } else if ($account == 'pelatihan') {
      if ($type == 'verify') {
        $this->email->subject('Account Verification');
        $this->email->message('Click this link to verify you account : <a href="' . base_url() . 'auth/verify_pelatihan?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '">Activate</a>');
      } else if ($type == 'forgot') {
        $this->email->subject('Reset Password');
        $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/reset_password?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '&token=' . urlencode($token) . '&account_type=' . $account . '">Reset Password</a>');
      }
    }

    if ($this->email->send()) {
      return true;
    } else { // in development stage
      echo $this->email->print_debugger();
      die;
    }
  }

  public function lookup_option_city($q = FALSE)
  {
    $q = $this->input->get('q');

    $sql = $this->db->query(" SELECT id_biaya, propinsi
      FROM biaya
      WHERE (UCASE(propinsi) LIKE UCASE('%$q%'))
      ORDER BY propinsi ASC
      LIMIT 100
    ");

    return $sql;
  }

  public function city_name($id = FALSE)
  {
    $this->db->select('propinsi');
    $this->db->where('id_biaya', $id);
    $query = $this->db->get('biaya');
    $row = $query->row();
    return isset($row->propinsi) ? $row->propinsi : '';
  }

  public function lookup_option_sarpelkes($q = FALSE)
  {
    $q = $this->input->get('q');

    $sql = $this->db->query(" SELECT 0 AS id_customer, 'Umum/Pribadi' AS nama, '' AS status
      UNION ALL
      SELECT id_customer, nama, status
      FROM customer
      WHERE (UCASE(nama) LIKE UCASE('%$q%')) AND is_validate = 1
      ORDER BY id_customer ASC
      LIMIT 50
    ");

    return $sql;
  }
}
