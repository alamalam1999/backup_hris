<script>

	$(document).ready(function() {


		delete_dokumen();
		delete_doc_detail();
		delete_formal();
		delete_nonf();
		delete_bahasa();
		delete_organisasi();
		delete_keluarga_inti();
		delete_keluarga_besar();
		delete_pengalaman();
		delete_penanggung();
	});

	$(function() {
		if ($('#jenis').val() == 'KONTRAK' || $('#jenis').val() == 'MAGANG') {
			$('#tgl_keluar').show();
		} else {
			$('#tgl_keluar').hide();
		}
		//$('#tgl_keluar').hide();
		$('#jenis').change(function() {
			if ($('#jenis').val() == 'KONTRAK' || $('#jenis').val() == 'MAGANG') {
				$('#tgl_keluar').show();
			} else {
				$('#tgl_keluar').hide();
			}
		});

		/* ---------- change code ----------- */
		$('.all-history').click(function() {
			$('.status-kerja-view').show();
			$('.jenjang-karir-view').show();
			$('.posisi-view').show();
			$('.gaji-view').show();
			$('.equipment-view').show();
			$('.sp-view').show();
		});

		$('.status-kerja').click(function() {
			$('.status-kerja-view').show();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.jenjang-karir').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').show();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.posisi').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').show();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.gaji').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').show();
			$('.equipment-view').hide();
			$('.sp-view').hide();
		});

		$('.equipment').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').show();
			$('.sp-view').hide();
		});

		$('.sp').click(function() {
			$('.status-kerja-view').hide();
			$('.jenjang-karir-view').hide();
			$('.posisi-view').hide();
			$('.gaji-view').hide();
			$('.equipment-view').hide();
			$('.sp-view').show();
		});

		$('#add-dokumen').click(function(i) {

			$('.dokumen').append('<tbody style="border-top: 0;"><tr><td rowspan="4"><input type="text" name="DOK_KARYAWAN[]" value="" class="form-control"></td><td> IJAZAH</td><td><input type="file" name="FILE_IJAZAH[]" class="form-control"></td><td><input type="text" name="MASA_IJAZAH[]" value="" class="form-control"></td><td rowspan="4"> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-dokumen" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus</button> </span></td></tr><tr><td> SERTIFIKAT</td><td><input type="file" name="FILE_SERTIFIKAT[]" class="form-control"></td><td><input type="text" name="MASA_SERTIFIKAT[]" value="" class="form-control"></td></tr><tr><td> SIO</td><td><input type="file" name="FILE_SIO[]" value="ADA" class="form-control"></td><td><input type="text" name="MASA_SIO[]" value="" class="form-control"></td></tr><tr><td> KTA</td><td><input type="file" name="FILE_KTA[]"  class="form-control"></td><td><input type="text" name="MASA_KTA[]" value="" class="form-control"></td></tr></tbody>');

				// $('.dokumen').append('<tbody style="border-top: 0;"><tr><td rowspan="4"><input type="text" name="DOK_KARYAWAN[]" value="" class="form-control"></td><td><input type="checkbox" name="IJAZAH[]" value="ADA" readonly> IJAZAH</td><td><input type="file" name="FILE_IJAZAH[]" class="form-control"></td><td><input type="text" name="MASA_IJAZAH[]" value="" class="form-control"></td><td rowspan="4"> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-dokumen" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Dokumen</button> </span></td></tr><tr><td><input type="checkbox" name="SERTIFIKAT[]" value="ADA" readonly> SERTIFIKAT</td><td><input type="file" name="FILE_SERTIFIKAT[]" class="form-control"></td><td><input type="text" name="MASA_SERTIFIKAT[]" value="" class="form-control"></td></tr><tr><td><input type="checkbox" name="SIO[]" value="ADA" readonly> SIO</td><td><input type="file" name="FILE_SIO[]"  class="form-control"></td><td><input type="text" name="MASA_SIO[]" value="" class="form-control"></td></tr><tr><td><input type="checkbox" name="KTA[]" value="ADA" readonly> KTA</td><td><input type="file" name="FILE_KTA[]"  class="form-control"></td><td><input type="text" name="MASA_KTA[]" value="" class="form-control"></td></tr></tbody>');
			return false;
		});

		$('#add-formal').click(function(i) {
			$('.formal').append('<tr><td></td><td><select class="form-control" name="TINGKAT[]"><option value="SD">SD</option><option value="SMP">SMP</option><option value="SMA">SMA</option><option value="SMK">SMK</option><option value="D3">DIPLOMA (D3)</option><option value="S1">SARJANA (S1)</option><option value="S2">PASCA SARJANA (S2)</option></select></td><td><input type="text" name="JURUSAN[]" class="form-control"></td><td><input type="text" name="INSTITUSI[]" class="form-control"></td><td><input type="text" name="LOKASI[]" class="form-control"></td><td><input type="text" name="TAHUN_MULAI[]" class="form-control"></td><td><input type="text" name="TAHUN_SELESAI[]" class="form-control"></td><td><input type="text" name="GPA[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-formal" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			return false;
		});

		$('#add-nonf').click(function(i) {
			$('.nonf').append('<tr><td></td><td><input type="text" name="NAMA_KURSUS[]" class="form-control"></td><td><input type="text" name="TEMPAT[]" class="form-control"></td><td class="text-center"><input type="text" name="PERIODE_MULAI[]" autocomplete="off" class="form-control datepicker" style="display: inline !important; width: 150px;"> - <input type="text" name="PERIODE_SELESAI[]" autocomplete="off" class="form-control datepicker" style="display: inline !important; width: 150px;"></td><td><input type="text" name="KETERANGAN[]" class="form-control"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-nonf" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			$('.datepicker').datepick({
				dateFormat: 'yyyy-mm-dd'
			});
			return false;
		});

		$('#add-bahasa').click(function(i) {
			$('.bahasa').append('<tr><td></td><td><input type="text" name="BAHASA[]" class="form-control"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="kurang"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="cukup"></td><td class="text-center"><input type="radio" class="form-check-input" name="LISAN[]" value="baik"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="kurang"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="cukup"></td><td class="text-center"><input type="radio" class="form-check-input" name="TULISAN[]" value="baik"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-bahasa" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			return false;
		});

		$('#add-organisasi').click(function(i) {
			$('.organisasi').append('<tr><td></td><td><input type="text" name="NAMA_ORGANISASI[]" class="form-control"></td><td><input type="text" name="JABATAN_ORGANISASI[]" class="form-control"></td><td><input type="text" name="LOKASI_ORGANISASI[]" class="form-control"></td><td><input type="text" name="PERIODE_ORGANISASI[]" class="form-control"></td><td class="text-center"><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-organisasi" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			return false;
		});

		$('#add-keluarga-inti').click(function(i) {
			$('.keluarga-inti').append('<tr><td></td><td><select class="form-control" name="ANGGOTA_KELUARGA_INTI[]"><option value="SUAMI">SUAMI</option><option value="ISTRI">ISTRI</option><option value="ANAK1">ANAK 1</option><option value="ANAK2">ANAK 2</option><option value="ANAK3">ANAK 3</option></select></td><td><input type="text" name="NAMA_KELUARGA_INTI[]" class="form-control"></td><td><select class="form-control" name="GENDER_INTI[]"><option value="L">L</option><option value="P">P</option></select></td><td><input type="text" name="TP_LAHIR_KELUARGA_INTI[]" class="form-control"></td><td><input type="text" name="TGL_LAHIR_KELUARGA_INTI[]" class="form-control datepicker"></td><td><input type="text" name="PENDIDIKAN_KELUARGA_INTI[]" class="form-control"></td><td><input type="text" name="PEKERJAAN_KELUARGA_INTI[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			$('.datepicker').datepick({
				dateFormat: 'yyyy-mm-dd'
			});
			return false;
		});

		$('#add-keluarga-besar').click(function(i) {
			$('.keluarga-besar').append('<tr><td></td><td><select class="form-control" name="ANGGOTA_KELUARGA_BESAR[]"><option value="AYAH">AYAH</option><option value="IBU">IBU</option><option value="ANAK1">ANAK 1</option><option value="ANAK2">ANAK 2</option><option value="ANAK3">ANAK 3</option><option value="ANAK4">ANAK 4</option><option value="ANAK5">ANAK 5</option></select></td><td><input type="text" name="NAMA_KELUARGA_BESAR[]" class="form-control"></td><td><select class="form-control" name="GENDER_BESAR[]"><option value="L">L</option><option value="P">P</option></select></td><td><input type="text" name="TP_LAHIR_KELUARGA_BESAR[]" class="form-control"></td><td><input type="text" name="TGL_LAHIR_KELUARGA_BESAR[]" class="form-control datepicker"></td><td><input type="text" name="PENDIDIKAN_KELUARGA_BESAR[]" class="form-control"></td><td><input type="text" name="PEKERJAAN_KELUARGA_BESAR[]" class="form-control"></td><td><span class="input-group-btn"><button type="button" class="btn btn-danger btn-flat del-keluarga-inti" title="Hapus Data"><span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span></button></span></td></tr>');
			$('.datepicker').datepick({
				dateFormat: 'yyyy-mm-dd'
			});
			return false;
		});

		$('#add-pengalaman').click(function(i) {
			$('.pengalaman').append('<table class="table"><tr><td class="text-right" style="vertical-align: middle; width: 180px;">Nama Perusahaan</td><td><input type="text" name="NAMA_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle; width: 180px;">Jabatan Awal</td><td><input type="text" name="JABATAN_AWAL[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Bergerak di Bidang</td><td><input type="text" name="BIDANG_USAHA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Jabatan Akhir</td><td><input type="text" name="JABATAN_AWKHIR[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Alamat</td><td><input type="text" name="ALAMAT_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Gaji Pokok</td><td><input type="text" name="GAPOK_SEBELUMNYA[]" class="form-control currency"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Nama Atasan Langsung</td><td><input type="text" name="ATASAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Tunjangan Lainnya</td><td><input type="text" name="TUNJANGAN_LAINNYA[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">No. Telepon</td><td><input type="text" name="NO_TELP_PERUSAHAAN[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Alasan Pengunduran Diri</td><td><input type="text" name="ALASAN_RESIGN[]" class="form-control"></td></tr><tr><td class="text-right" style="vertical-align: middle;">Periode Kerja</td><td><input type="text" name="PERIODE_BEKERJA[]" class="form-control"></td><td></td><td class="text-right" style="vertical-align: middle;">Deskripsi Pekerjaan</td><td><input type="text" name="DESKRIPSI_PEKERJAAN[]" class="form-control"></td></tr><tr><td colspan="5" class="text-right"> <span class="input-group-btn" style="display: inline;"> <button type="button" class="btn btn-danger btn-flat del-pengalaman" title="Hapus Data" style="width: 150px;"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> Hapus Data </button> </span></td></tr></table>');
			$('.datepicker').datepick({
				dateFormat: 'yyyy-mm-dd'
			});
			$('.currency').mask('000,000,000,000,000', {
				reverse: true
			});
			return false;
		});

		$('#add-penanggung').click(function(i) {
			$('.penanggung').append('<tr><td></td><td><input type="text" name="NAMA_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="ALAMAT_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="TELP_PENANGGUNG[]" class="form-control"></td><td><input type="text" name="HUBUNGAN_PENANGGUNG[]" class="form-control"></td><td> <span class="input-group-btn"> <button type="button" class="btn btn-danger btn-flat del-penanggung" title="Hapus Data"> <span class="glyphicon glyphicon-trash btn-danger" style="border-bottom: none;"></span> </button> </span></td></tr>');
			return false;
		});

		$('.riwayat-status').hide();
		$('#add-status').on('click', function() {
			$('.riwayat-status').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-level-jabatan').hide();
		$('#add-level-jabatan').on('click', function() {
			$('.riwayat-level-jabatan').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-posisi').hide();
		$('#add-posisi').on('click', function() {
			$('.riwayat-posisi').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

		$('.riwayat-gaji').hide();
		$('#add-gaji').on('click', function() {
			$('.riwayat-gaji').toggle();
			$(this).find('span')
				.toggleClass('glyphicon-minus')
				.toggleClass('glyphicon-plus')
		});

	});



	function delete_dokumen() {
		$(document).on('click', '.del-dokumen', function() {
			$(this).closest('tbody').remove();
		});
	}

	function delete_doc_detail() {

		$(document).on('click', '.btn-del-doc', function() {
			$(this).closest('span').remove();
		});
	}

	function delete_formal() {
		$(document).on('click', '.del-formal', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_nonf() {
		$(document).on('click', '.del-nonf', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_bahasa() {
		$(document).on('click', '.del-bahasa', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_organisasi() {
		$(document).on('click', '.del-organisasi', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_keluarga_inti() {
		$(document).on('click', '.del-keluarga-inti', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_keluarga_besar() {
		$(document).on('click', '.del-keluarga-besar', function() {
			$(this).closest('tr').remove();
		});
	}

	function delete_pengalaman() {
		$(document).on('click', '.del-pengalaman', function() {
			$(this).closest('table').remove();
		});
	}

	function delete_penanggung() {
		$(document).on('click', '.del-penanggung', function() {
			$(this).closest('tr').remove();
		});
	}
</script>
