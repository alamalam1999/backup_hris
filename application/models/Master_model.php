<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class master_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();

    $this->tables = array(
      'user' => 'user',
      'user_access' => 'user_access',
      'customer' => 'customer',
      'customer_pdp' => 'customer_pdp',
      'alat_kesehatan' => 'alat_kesehatan',
      'work_order' => 'work_order',
      'work_order_revisi' => 'work_order_revisi',
      'work_order_detail' => 'work_order_detail',
      'work_order_biaya' => 'work_order_biaya',
      'surat_jalan' => 'surat_jalan',
      'wo_code' => 'wo_code',
      'inhouse' => 'inhouse',
      'inhouse_detail' => 'inhouse_detail',
      'inhouse_code' => 'inhouse_code',
      'biaya' => 'biaya',
      'paket' => 'paket',
      'paket_detail' => 'paket_detail',
      'job_order' => 'job_order',
      'job_order_detail' => 'job_order_detail',
      'surat' => 'surat',
      'surat_pelayanan' => 'surat_pelayanan',
      'work_order_pdp' => 'work_order_pdp',
      'wo_code_pdp' => 'wo_code_pdp',
      'fb_member' => 'fb_member',
      'kasir_fb' => 'kasir_fb',
      'fb' => 'fb',
      'fb_lk' => 'fb_lk',
      'lap_fb' => 'lap_fb',
      'tld_member' => 'tld_member',
      'kasir_tld' => 'kasir_tld',
      'tld' => 'tld',
      'tld_lk' => 'tld_lk',
      'lap_tld' => 'lap_tld',
      'pengaduan' => 'pengaduan',
      'propinsi' => 'propinsi',
      'mpelatihan' => 'mpelatihan',
      'pelatihan' => 'pelatihan',
      'pkl' => 'pkl',
      'kunjungan' => 'kunjungan',
      'options' => 'options',
      'karyawan' => 'karyawan',
      'surtug' => 'surtug',
      'pengembangan' => 'pengembangan',
      'perbaikan' => 'perbaikan',
      'periode_fb' => 'periode_fb',
      'periode_tld' => 'periode_tld',

      // 'pdp' => 'pdp',
      // 'pdp_member' => 'pdp_member',
      // 'pdp_pemakaian' => 'pdp_pemakaian',
      // 'pdp_pemakaian_detail' => 'pdp_pemakaian_detail',
      // 'tld' => 'tld',
      // 'tld_member' => 'tld_member',
      // 'tld_pemakaian' => 'tld_pemakaian',
      // 'tld_pemakaian_detail' => 'tld_pemakaian_detail',

      'pelatihan_eksternal' => 'pelatihan_eksternal',
      'pelatihan_peserta' => 'pelatihan_peserta',
      'pelatihan_approval_peserta' => 'pelatihan_approval_peserta',
      'pelatihan_penilaian' => 'pelatihan_penilaian',

      'permohonan' => 'permohonan',
      'permohonan_detail' => 'permohonan_detail',
      'penawaran' => 'penawaran',
      'penagihan' => 'penagihan',
      'pembayaran' => 'pembayaran',
      'uji_profisiensi' => 'ujiprofisiensi',
      'ujiprofisiensi_detail_alat' => 'ujiprofisiensi_detail_alat',
      'group_customer' => 'ujiprofisiensi_group_customer',
      'group_customer_detail' => 'ujiprofisiensi_group_customer_detail',
      'ujiprofisiensi_master_alat' => 'ujiprofisiensi_master_alat',
      'ujiprofisiensi_undangan' => 'ujiprofisiensi_undangan',
      'ujiprofisiensi_registrasi' => 'ujiprofisiensi_registrasi',
      'ujiprofisiensi_registrasi_detail' => 'ujiprofisiensi_registrasi_detail',
      'ujiprofisiensi_undangan' => 'ujiprofisiensi_undangan',
      'ujiprofisiensi_peserta' => 'ujiprofisiensi_peserta',
      'ujiprofisiensi_hasil' => 'ujiprofisiensi_hasil',
      'ujiprofisiensi_hasil_perhitungan' => 'ujiprofisiensi_hasil_perhitungan',
      'rekalibrasi' => 'rekalibrasi',
      'alat_standard' => 'alat_standard',
      'institut_rekalibrasi' => 'institut_rekalibrasi',
      'rekalibrasi_pengajuan' => 'rekalibrasi_pengajuan',
      'rekalibrasi_pengajuan_detail' => 'rekalibrasi_pengajuan_detail',
      'format_penomeran' => 'format_penomeran',
      'rekalibrasi_detail' => 'rekalibrasi_detail',
      'hasil_rekalibrasi' => 'hasil_rekalibrasi',
      'log_laporan_pekerjaan' => 'log_laporan_pekerjaan',
      'log_surpel_history' => 'log_surpel_history',
      'e_dok' => 'e_dok',
      'log_aplikasi' => 'log_aplikasi',
      'peminjaman_alat' => 'peminjaman_alat',
      'peminjaman_alat_detail' => 'peminjaman_alat_detail',
      'alat_standard_inventory' => 'alat_standard_inventory',
      'tld_fb' => 'tld_fb',

    );

    /* initialize table name for this class */
    $this->get_tables($this);
  }

  /**
   * Define table name for universal model
   */

  function get_tables($class)
  {
    if (count($this->tables) > 0) {
      foreach ($this->tables as $key => $t) {
        $class->{$key} = $this->db->dbprefix($t);
      }
    }
  }

  /**
   * Escaping several data in a time
   *
   */

  function escape_all($data)
  {
    if (!is_array($data)) return $this->db->escape_str($data);

    $tmp = array();
    if (count($data) > 0) {
      foreach ($data as $key => $val) {
        $tmp[$key] = $this->db->escape_str($val);
      }
    }
    return $tmp;
  }

  /**
   * Get option vars stored in database
   */

  function get_option($var)
  {
    static $v;

    if (!is_array($v)) {
      $v = array();
      $query = $this->db->query(" SELECT * FROM {$this->options} ");
      $result = $query->result();
      if (count($result) > 0) {
        foreach ($result as $row) {
          $v[$row->var] = ($row->val == '') ? $row->default : $row->val;
        }
      }
    }

    return isset($v[$this->session->userdata('SERVER') . '_' . $var]) ? $v[$this->session->userdata('SERVER') . '_' . $var] : NULL;
  }

  /**
   * Set option vars stored in database
   */

  function set_option($var, $val = '')
  {
    if (is_array($var)) {
      $tmp = array();
      if (count($var) > 0) {
        foreach ($var as $k => $v) {
          $k = $this->session->userdata('SERVER') . '_' . $this->db->escape_str($k);
          $v = $this->db->escape_str($v);
          $tmp[] = "('$k','$v')";
        }

        $data = @implode(',', $tmp);
        $insert_string = " INSERT INTO `{$this->options}` (`var`,`val`) VALUES $data ON DUPLICATE KEY UPDATE val=VALUES(val) ";
        return $this->db->query($insert_string);
      }

      return FALSE;
    } else {
      $query = $this->db->query(" SELECT * FROM {$this->options} WHERE var='$var' ORDER BY var ASC LIMIT 1 ");
      if ($query->num_rows() > 0) {
        $d['val'] = $val;
        $this->db->where('var', $var);
        return $this->db->update($this->options, $d);
      } else {
        $d['var'] = $this->session->userdata('SERVER') . '_' . $var;
        $d['val'] = $val;
        return $this->db->insert($this->options, $d);
      }
    }
  }
}
