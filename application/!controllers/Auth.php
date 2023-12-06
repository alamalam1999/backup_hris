<?php defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->load->model('auth_model');
    $this->load->library('recaptcha');
  }

  public function index()
  {
    if (!$this->auth_model->is_not_login()) {
      redirect(site_url('welcome'));
    }

    $data['recaptcha'] = $this->recaptcha->create_box();
    $data['title'] = 'Login';

    $this->form_validation->set_rules('login_as', 'Login as', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required');

    if ($this->form_validation->run() == FALSE) {
      $this->load->view('templates/header', $data);
      $this->load->view('auth/login', $data);
      $this->load->view('templates/footer');
    } else {
      //$is_valid = $this->recaptcha->is_valid();
      $is_valid['success'] = true; //close reCAPTCHA
      
      //if ($is_valid['success']) {
      $this->_login_check();
      // } else {
      //   $this->session->set_flashdata('error', 'reCAPTCHA harus diverifikasi (checklist)');
      //   $this->load->view('templates/header', $data);
      //   $this->load->view('auth/login', $data);
      //   $this->load->view('templates/footer');
      // }
    }
  }

  private function _login_check()
  {
    $is_login = $this->auth_model->do_login();
    if ($is_login) {
      redirect(site_url());
    } else {
      $data['recaptcha'] = $this->recaptcha->create_box();
      $this->session->set_flashdata('error', 'Email dan password tidak sesuai, atau akun Anda belum diverifikasi');
      $this->load->view('templates/header', $data);
      $this->load->view('auth/login', $data);
      $this->load->view('templates/footer');
    }
  }

  public function register()
  {
    redirect(site_url('auth/register_type'));
  }

  public function register_type()
  {
    if (!$this->auth_model->is_not_login()) {
      redirect(site_url('welcome'));
    }

    $data['title'] = 'Pilih Registrasi';

    $this->load->view('templates/header', $data);
    $this->load->view('auth/register_type');
    $this->load->view('templates/footer');
  }

  public function register_customer($category)
  {
    if (!$this->auth_model->is_not_login()) {
      redirect(site_url('welcome'));
    }

    if ($category != 1 && $category != 2) {
      $this->session->set_flashdata('error', 'Url tidak valid');
      redirect(site_url('auth/register_type'));
    }

    $data['recaptcha'] = $this->recaptcha->create_box();
    $data['title'] = 'Registrasi Customer';
    $data['js_script'] = 'js/register_customer_js';

    $this->form_validation->set_rules('status', 'Registration Type', 'required');
    $this->form_validation->set_rules('nama', 'Name', 'required|trim|is_unique[customer.nama]', [
      'is_unique' => 'This Sarpelkes has already registered!'
    ]);
    $this->form_validation->set_rules('pic', 'PIC', 'required');
    $this->form_validation->set_rules('alamat', 'Address', 'required');
    $this->form_validation->set_rules('telpon', 'Phone', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[customer.email]', [
      'is_unique' => 'This Email has already registered!'
    ]);
    $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[3]|matches[password_verify]', [
      'matches' => 'Password dont match!',
      'min_length' => 'Password too short!'
    ]);
    $this->form_validation->set_rules('password_verify', 'Password Verify', 'required|trim|matches[password]');

    if ($this->form_validation->run() == FALSE) {
      $this->load->view('templates/header', $data);
      $this->load->view('auth/register_customer', $data);
      $this->load->view('templates/footer', $data);
    } else {
      $is_valid = $this->recaptcha->is_valid();
      $category = $this->input->post('category');

      if ($is_valid['success']) {
        if ($category != 1 && $category != 2) {
          $this->session->set_flashdata('error', 'Url tidak valid');
          redirect(site_url('auth/register_type'));
        } else {
          $this->auth_model->register_customer($category);
          $this->session->set_flashdata('success', 'Selamat! Akun anda telah berhasil dibuat. Silakan cek email untuk verifikasi akun Anda');
          redirect(site_url('auth'));
        }
      } else {
        $this->session->set_flashdata('error', 'reCAPTCHA harus diverifikasi (checklist)');
        $this->load->view('templates/header', $data);
        $this->load->view('auth/register_customer/' . $category, $data);
        $this->load->view('templates/footer', $data);
      }
    }
  }

  public function register_pelatihan()
  {

    if (!$this->auth_model->is_not_login()) {
      redirect(site_url('welcome'));
    }

    $data['recaptcha'] = $this->recaptcha->create_box();
    $data['title'] = 'Registrasi Pelatihan';
    $data['js_script'] = 'js/register_pelatihan_js';

    $this->form_validation->set_rules('id_customer', 'Sarpelkes', 'required');
    $this->form_validation->set_rules('nama', 'Name', 'required');
    $this->form_validation->set_rules('telpon', 'Phone', 'required');
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[pelatihan_peserta.email]', [
      'is_unique' => 'This email has already registered!'
    ]);
    $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[3]|matches[password_verify]', [
      'matches' => 'Password dont match!',
      'min_length' => 'Password too short!'
    ]);
    $this->form_validation->set_rules('password_verify', 'Password Verify', 'required|trim|matches[password]');

    if ($this->form_validation->run() == FALSE) {
      $this->load->view('templates/header', $data);
      $this->load->view('auth/register_pelatihan', $data);
      $this->load->view('templates/footer', $data);
    } else {
      $is_valid = $this->recaptcha->is_valid();

      if ($is_valid['success']) {
        $this->auth_model->register_pelatihan();
        $this->session->set_flashdata('success', 'Selamat! Akun anda telah berhasil dibuat. Silakan cek email untuk verifikasi akun Anda');
        redirect(site_url('auth'));
      } else {
        $this->session->set_flashdata('error', 'reCAPTCHA harus diverifikasi (checklist)');
        $this->load->view('templates/header', $data);
        $this->load->view('auth/register_pelatihan', $data);
        $this->load->view('templates/footer', $data);
      }
    }
  }

  public function verify_customer()
  {
    $verify = $this->auth_model->verify_email_customer();
    if ($verify) {
      $this->session->set_flashdata($verify['status'], $verify['message']);
      redirect('auth');
    }
  }

  public function verify_pelatihan()
  {
    $verify = $this->auth_model->verify_email_pelatihan();
    if ($verify) {
      $this->session->set_flashdata($verify['status'], $verify['message']);
      redirect('auth');
    }
  }

  public function lookup_city()
  {
    $query = $this->auth_model->lookup_option_city();

    $t = array();
    if ($query->num_rows() > 0) {
      foreach ($query->result() as $key => $row) {
        $t[$key]['id'] = $row->id_biaya;
        $t[$key]['text'] = $row->propinsi;
      }
    }

    $res['results'] = $t;

    echo json_encode($res);
  }

  public function lookup_sarpelkes()
  {
    $query = $this->auth_model->lookup_option_sarpelkes();

    $t = array();
    if ($query->num_rows() > 0) {
      /*
      $t[0]['id'] = 0;
      $t[0]['text'] = 'Umum/Pribadi';
      foreach ($query->result() as $key => $row) {
        $t[$key + 1]['id'] = $row->id_customer;
        $t[$key + 1]['text'] = $row->nama;
      }
      */
      foreach ($query->result() as $key => $row) {
        $status = '';
        if ($row->status != '') $status = ' (' . $row->status . ')';

        $t[$key]['id'] = $row->id_customer;
        $t[$key]['text'] = $row->nama . $status;
      }
    }

    $res['results'] = $t;

    echo json_encode($res);
  }

  public function forgot_password()
  {
    $data['title'] = 'Lupa Kata Sandi';
    $data['recaptcha'] = $this->recaptcha->create_box();
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
    $this->form_validation->set_rules('account_type', 'Type Account', 'trim|required');

    if ($this->form_validation->run() == false) {
      $this->load->view('templates/header', $data);
      $this->load->view('auth/forgot_password', $data);
      $this->load->view('templates/footer', $data);
    } else {
      $is_valid = $this->recaptcha->is_valid();

      if ($is_valid['success']) {
        $forgot = $this->auth_model->forgot_password_account();
        if ($forgot) {
          $this->session->set_flashdata($forgot['status'], $forgot['message']);
          redirect('auth/forgot_password');
        }
      } else {
        $this->session->set_flashdata('error', 'reCAPTCHA harus diverifikasi (checklist)');
        $this->load->view('templates/header', $data);
        $this->load->view('auth/forgot_password', $data);
        $this->load->view('templates/footer', $data);
      }
    }
  }

  public function reset_password()
  {
    $reset = $this->auth_model->reset_password_account();
    if ($reset['status'] == 'success') {
      $this->session->set_userdata('reset_email', $reset['email']);
      $this->session->set_userdata('account_type', $reset['account']);
      redirect('auth/change_password');
    } else if ($reset['status'] == 'error') {
      $this->session->set_flashdata($reset['status'], $reset['message']);
      redirect('auth');
    }
  }

  public function change_password()
  {
    if (!$this->session->userdata('reset_email') && !$this->session->userdata('account')) {
      redirect('auth');
    }

    $data['title'] = 'Ubah Kata Sandi';
    $data['recaptcha'] = $this->recaptcha->create_box();
    $this->form_validation->set_rules('password1', 'Password', 'trim|required|min_length[3]|matches[password2]');
    $this->form_validation->set_rules('password2', 'Repeat Password', 'trim|required|min_length[3]|matches[password1]');

    if ($this->form_validation->run() == false) {
      $data['title'] = 'Change Password';
      $this->load->view('templates/header', $data);
      $this->load->view('auth/change_password', $data);
      $this->load->view('templates/footer', $data);
    } else {
      $is_valid = $this->recaptcha->is_valid();

      if ($is_valid['success']) {
        $this->auth_model->change_password_account();
        $this->session->set_flashdata('success', 'Password berhasil diubah! Silakan login');
        redirect('auth');
      } else {
        $this->session->set_flashdata('error', 'reCAPTCHA harus diverifikasi (checklist)');
        $this->load->view('auth/change_password', $data);
      }
    }
  }

  public function logout()
  {
    $this->session->sess_destroy();
    redirect(site_url('auth'));
  }
}
