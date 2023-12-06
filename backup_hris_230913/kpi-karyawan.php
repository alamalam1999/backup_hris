<?php
include 'app-load.php';

is_login('kpi-karyawan.view');

$JS[] = 'static/select2/js/select2.min.js';
$CSS[] = 'static/select2/css/select2.min.css';
$CSS[] = 'static/select2/css/select2-bootstrap.min.css';

$TAHUN = get_search('KPI_KARYAWAN','TAHUN');
if(empty($TAHUN)) $TAHUN = date('Y');

include 'header.php';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<div class="dropdown">
				<button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dd1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="width:100%;">
					<i class="fa fa-cog"></i>&nbsp;&nbsp;Action <span class="caret"></span>
				</button>
				<ul class="dropdown-menu" aria-labelledby="dd1">
				<?php /*<li><a href="kpi-action.php?op=add" style=""><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a></li>
				<li><a href="javascript:void(0)" id="btn-edit" style=""><i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit</a></li>
				<li role="separator" class="divider"></li>*/ ?>
				<li><a href="javascript:void(0)" id="btn-delete" style="color:red;"><i class="fa fa-remove"></i>&nbsp;&nbsp;Delete</a></li>
				</ul>
			</div>
		</div>
		<div class="col-sm-2">
			<input type="text" id="TAHUN" value="<?php echo $TAHUN ?>" class="form-control input-sm" autocomplete="off">
		</div>
		<div class="col-sm-3">
			<select name="KARYAWAN_ID" id="KARYAWAN_ID" class="form-control">
				<?php
					$KARYAWAN_ID = get_search('KPI_KARYAWAN','KARYAWAN_ID');
					$K = db_first(" SELECT KARYAWAN_ID,NIK,NAMA FROM karyawan WHERE KARYAWAN_ID='".$KARYAWAN_ID."' ");
					if(isset($K->KARYAWAN_ID)){
						echo '<option value="'.$K->KARYAWAN_ID.'" selected="selected">'.$K->NIK.' - '.$K->NAMA.'</option>';
					}
				?>
			</select>
		</div>
		<div class="col-sm-2">
			<a href="javascript:void(0)" id="btn-add-indicator" class="btn btn-sm btn-warning"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Indicator</a>
		</div>
		<div class="col-sm-3">
			<h1 style="margin:0;text-align:right;">Key Performance Index</h1>
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
			'KARYAWAN_ID': $('#KARYAWAN_ID').val(),
			'TAHUN': $('#TAHUN').val(),
		},
		url:'kpi-karyawan-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'A.INDICATOR',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'INDICATOR',title:'Indicator',width:700,sortable:true,align:'left'},
			{field:'UNIT',title:'Unit',width:100,sortable:false,align:'center'},
			{field:'TARGET',title:'Target',width:100,sortable:false,align:'center'},
			{field:'REALISASI',title:'Realisasi',width:100,sortable:false,align:'center'},
			{field:'KPI',title:'KPI',width:100,sortable:false,align:'center'},
		]],
		onLoadSuccess: function(data){
			update_col();
		}
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-delete').click(function(){
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ){
			alert('No item selected');
		}else{
			if(confirm('Are you sure?')){
				$.ajax({
					url: 'kpi-delete-indicator.php',
					data: { 'KPI_KARYAWAN_ID' : sel.KPI_KARYAWAN_ID },
					dataType: 'json',
					method: 'POST',
					success: function(res){
						if(res.status != '1')
						{
							alert(res.msg);
						}
					},
					complete: function(){
						doSearch();
					},
				});
			}
		}
		return false;
	});
	$('#btn-add-indicator').click(function(){
		KARYAWAN_ID = $('#KARYAWAN_ID').val();
		if( KARYAWAN_ID == "" || KARYAWAN_ID == null ){
			alert('Silakan pilih karyawan terlebih dahulu!');
		}else{
			popup_window('kpi-popup.php?KARYAWAN_ID='+$('#KARYAWAN_ID').val()+'&TAHUN='+$('#TAHUN').val(),'kpi_popup','800','420','yes','center');
		}
	});
	$('#TAHUN').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	$('#STRUKTUR_ID').change(function(){
		doSearch();
	});
	datagrid();
	$('#KARYAWAN_ID').select2({
		theme: "bootstrap",
		ajax: {
			url: 'karyawan-ac.php',
			dataType: 'json',
		}
	});
	
	$('#KARYAWAN_ID').on('select2:select', function (e) {
		var data = e.params.data;
		doSearch();
	});
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
		KARYAWAN_ID: $('#KARYAWAN_ID').val(),
		TAHUN: $('#TAHUN').val(),
	});
}
function update_col()
{
	$('.UPDATE_COL').dblclick(function(){
		OB = $(this);
		COL = OB.attr('data-col');
		ID = OB.attr('data-id');
		VAL = OB.attr('data-val');
		OB.html('<input type="text" class="UPDATE_COL_INPUT" data-col="'+COL+'" data-id="'+ID+'" value="'+VAL+'" style="width:100%;border:0;background:#ffffcc;text-align:center;outline:0;">');
		OB.find('input').focus();
		OB.find('input').blur(function(){
			OB.html(VAL);
		});
		update_col_input();
	});
}

function update_col_input()
{
	$('.UPDATE_COL_INPUT').keypress(function (e) {
		if (e.which == 13) {
			OB = $(this);
			COL = OB.attr('data-col');
			ID = OB.attr('data-id');
			VAL = OB.val();
			OB.css({'background' : 'url(<?php echo base_url() ?>static/img/loading19.gif) no-repeat left center', 'padding-left' : '0px'});
			$.ajax({
				data : {
					'COL' : COL,
					'ID' : ID,
					'VAL' : VAL,
				},
				url : 'kpi-update-column.php',
				method : 'POST',
				dataType: 'json',
				success : function(res){
					if(res.status == '1'){
						OB.parent().attr('data-val',VAL);
					}else{
					}
						$.ajax({
							data : {
								'ID' : ID,
							},
							url : 'kpi-get-index.php',
							method : 'POST',
							dataType: 'json',
							success : function(res){
								$('.KPI_'+ID).html(res.KPI);
							}
						});
				},
				complete : function(){
					OB.css({'background':'#ffffcc','padding-left':'0'});
					OB.blur(function(){
						OB.parent().html(VAL);
					});
				}
			});
			return false;
		}
	});
}
</script>

<?php
include 'footer.php';
?>