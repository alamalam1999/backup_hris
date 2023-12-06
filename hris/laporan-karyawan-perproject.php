<?php
include 'app-load.php';
is_login('karyawan.view');
include 'header.php';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<!-- <div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:120%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<li><a href="karyawan-export.php" id="btn-import" style=""><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;Export to excel</a></li>
				</ul>
			</div>
		</div> -->
		<div class="col-sm-2">
			<select name="TAHUN" id="TAHUN" class="form-control input-sm">
			<?php $TAHUN = db_fetch(" SELECT DISTINCT(TAHUN) FROM periode ");
					if(count($TAHUN) > 0){ foreach ($TAHUN as $row) { 
					echo '<option value="'.$row->TAHUN.'" selected="selected">'.$row->TAHUN.'</option>';
				}}
			?>
			</select>
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">Summary Karyawan Per Unit</h1>
		</div>
	</div>

	<section class="content">
			
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:200px;"></table>
		</div>
			
	</section>
</section>

<script>
$(document).ready(function(){
	$('#t').datagrid({
		queryParams: {
			'TAHUN': $('#TAHUN').val()
		},
		url:'karyawan-report-json.php',
		fit: true,
		border: true,
		nowrap: true,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'NAMA',
		sortOrder: 'asc',
		singleSelect:false,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		
		columns:[[
			{field:'JANUARI',title:'Januari',width:120,sortable:false,align:'center',colspan: 2},
			{field:'FEBRUARI',title:'Februari',width:120,sortable:false,align:'center',colspan: 2},
			{field:'MARET',title:'Maret',width:120,sortable:false,align:'center',colspan: 2},
			{field:'APRIL',title:'April',width:120,sortable:false,align:'center',colspan: 2},
			{field:'MEI',title:'Mei',width:120,sortable:false,align:'center',colspan: 2},
			{field:'JUNI',title:'Juni',width:120,sortable:false,align:'center',colspan: 2},
			{field:'JULI',title:'Juli',width:120,sortable:false,align:'center',colspan: 2},
			{field:'AGUSTUS',title:'Agustus',width:120,sortable:false,align:'center',colspan: 2},
			{field:'SEPTEMBER',title:'September',width:120,sortable:false,align:'center',colspan: 2},
			{field:'OKTOBER',title:'Oktober',width:120,sortable:false,align:'center',colspan: 2},
			{field:'NOVEMBER',title:'November',width:120,sortable:false,align:'center',colspan: 2},
			{field:'DESEMBER',title:'Desember',width:120,sortable:false,align:'center',colspan: 2},
		],[
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			{field:'AKTIF',title:'Aktif',width:60,sortable:false,align:'center'},
			{field:'PASIF',title:'Pasif',width:60,sortable:false,align:'center'},
			
		]],
		rowStyler:function(index,row){
			if (row.STATUS_KEY == "VOID"){
				return 'background-color:#ffaeae;';
			}
		}
	});

	$('#TAHUN').change(function(){
		doSearch();
		return false;
	});
	
	$('#btn-reset').click(function(){
		$('#TAHUN').val("");
		doSearch();
		return false;
	});
	datagrid();
});

function datagrid()
{
	var wind = parseInt($(window).height());
	var top = parseInt($('.navbar').outerHeight());
	$('#t-responsive').height(wind - top - 70);
	$('#t').datagrid('resize');
}

function doSearch(){
	$('#t').datagrid('load',{
		TAHUN: $('#TAHUN').val(),
	});
}
</script>

<?php
include 'footer.php';
?>