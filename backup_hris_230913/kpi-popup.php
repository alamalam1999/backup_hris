<?php
include 'app-load.php';

is_login('kpi.view');

$DATA = db_fetch("
	SELECT *
	FROM struktur
	ORDER BY ORD ASC
");
$list = array();
if( count($DATA) > 0 ){
	foreach($DATA as $row){
		$thisref = & $refs[ $row->STRUKTUR_ID ];
		$thisref = array_merge((array) $thisref,(array) $row);
		if ($row->PARENT_ID == 0) {
			$list[] = & $thisref;
		} else {
			$refs[$row->PARENT_ID]['child'][] = & $thisref;
		}
	}
}
$TREE_CHAR = '_____';
$RS = hirearchy($list);
$PARENT_OPTION = array('0' => ' -- JABATAN --');
if(count($RS)>0){
	foreach($RS as $row){
		$TREE = '';
		for($i=1; $i<$row->DEPTH; $i++){
			$TREE .= $TREE_CHAR;
		}
		$PARENT_OPTION[$row->STRUKTUR_ID] = '<span style="color:#cccccc;">'.$TREE.'</span>' . ' ' . strtoupper($row->STRUKTUR);
	}
}

$NO_MENU = 1;
include 'header.php';
?>

<section class="container-fluid">
	<div class="row" style="margin:10px 0;">
		<div class="col-sm-2">
			<a href="javascript:void(0)" id="btn-add" class="btn btn-sm btn-success" style="width:100%;"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add</a>
		</div>
		<div class="col-sm-3">
			<input type="text" id="NAMA" value="<?php echo get_search('KPI','NAMA') ?>" class="form-control input-sm" autocomplete="off" placeholder="Indicator....">
		</div>
		<div class="col-sm-3">
			<?php echo dropdown('STRUKTUR_ID',$PARENT_OPTION,get_search('KPI','STRUKTUR_ID'),' id="STRUKTUR_ID" class="form-control input-sm" ') ?>
		</div>
		<div class="col-sm-4">
			<h1 style="margin:0;text-align:right;">KPI</h1>
		</div>
	</div>

	<section class="content">
			
		<div id="t-responsive" class="table-responsive">
			<table id="t" style="min-height:380px;"></table>
		</div>
			
	</section>
</section>

<script>
$(document).ready(function(){
	$('.btn-submit').click(function(){
		$(this).find('i').attr('class', function(i, c){
			return c.replace(/(^|\s)fa-\S+/g, '');
		});
		$(this).prepend('<i class="fa fa-cog fa-spin fa-lg"></i>');
	});
	$('#t').datagrid({
		queryParams: {
			'NAMA': $('#NAMA').val(),
			'STRUKTUR_ID': $('#STRUKTUR_ID').val(),
		},
		url:'kpi-popup-json.php',
		fit: true,
		border: true,
		nowrap: false,
		striped: true,
		collapsible:true,
		remoteSort: true,
		sortName: 'INDICATOR',
		sortOrder: 'asc',
		singleSelect:true,
		pagination: true,
		pageSize:50,
		pageList: [50,100],
		rownumbers:true,
		columns:[[
			{field:'INDICATOR',title:'Indicator',width:500,sortable:true,align:'left'},
			{field:'UNIT',title:'Unit',width:80,sortable:false,align:'center'},
			{field:'STRUKTUR',title:'Struktur',width:100,sortable:false,align:'center'},
		]]
	});
	$(window).resize(function(){ datagrid(); });
	
	$('#btn-add').click(function(){
		$(this).html('<i class="fa fa-cog fa-spin fa-lg"></i>&nbsp;&nbsp;Add');
		var sel = $('#t').datagrid('getSelected');
		if( sel == undefined ){
			alert('No item selected');
			$('#btn-add').html('<i class="fa fa-plus"></i>&nbsp;&nbsp;Add');
		}else{
			$.ajax({
				url: 'kpi-add-indicator.php',
				data: {
					'KPI_ID' : sel.KPI_ID,
					'KARYAWAN_ID' : '<?php echo get_input('KARYAWAN_ID') ?>',
					'TAHUN' : '<?php echo get_input('TAHUN') ?>',
				},
				dataType: 'json',
				method: 'POST',
				success: function(res){
					if(res.status != '1')
					{
						alert(res.msg);
					}
				},
				complete: function(){
					window.opener.doSearch();
					$('#btn-add').html('<i class="fa fa-plus"></i>&nbsp;&nbsp;Add');
				},
			});
		}
		return false;
	});
	$('#NAMA').keypress(function (e) {
		if (e.which == 13) {
			doSearch();
			e.preventDefault();
		}
	});
	$('#STRUKTUR_ID').change(function(){
		doSearch();
	});
	datagrid();
});
function datagrid()
{
	/*var wind = parseInt($(window).height());
	$('#t-responsive').height(wind - 70);
	$('#t').datagrid('resize');*/
}
function doSearch(){
	$('#t').datagrid('load',{
		NAMA: $('#NAMA').val(),
		STRUKTUR_ID: $('#STRUKTUR_ID').val(),
	});
}
</script>

<?php
include 'footer.php';
?>