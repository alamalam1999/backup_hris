<?php
include 'app-load.php';
is_login('user.view');

$OP = get_input('op');
$ID = get_input('id');
$CURR_ID = get_input('CURR_ID');
if($OP=='edit' AND empty($ID)) die('<p>Data tidak ditemukan.</p>');

if($OP=='edit'){
	is_login('user.edit');
	$EDIT = db_first(" SELECT * FROM user WHERE USER_ID='$ID' ");
}
if($OP=='delete'){
	is_login('user.delete');
	db_execute(" DELETE FROM user WHERE USER_ID='$ID' ");
	header('location: user.php');
	exit;
}

if( isset($_SERVER['REQUEST_METHOD']) AND strtoupper($_SERVER['REQUEST_METHOD'])=='POST' )
{
	$REQUIRE = array('EMAIL','NAMA');
	if( in_array($OP,array('','add')) ){
		$REQUIRE[] = 'PASSWORD';
	}
	$ERROR_REQUIRE = 0;
	foreach($REQUIRE as $REQ){
		$IREQ = get_input($REQ);
		if($IREQ == "") $ERROR_REQUIRE = 1;
	}
	
	$MAIL_EXISTS = 0;
	if( ! db_exists(" SELECT EMAIL FROM user WHERE EMAIL='".db_escape(get_input('EMAIL'))."' AND USER_ID='".db_escape(get_input('CURRENT_ID'))."' "))
	{
		if(db_exists(" SELECT EMAIL FROM user WHERE EMAIL='".db_escape(get_input('EMAIL'))."' ")){
			$MAIL_EXISTS = 1;
		}
	}

	$EMAIL = db_escape(get_input('EMAIL'));
	if( $ERROR_REQUIRE ){
		$ERROR[] = 'Kolom '.implode(',',$REQUIRE).' wajib di isi.';
	/*}else if(strtoupper($CURR_ID) != strtoupper($USERNAME) AND db_exists(" SELECT 1 FROM user WHERE UCASE(USERNAME)=UCASE('".$USERNAME."') ")){
		$ERROR[] = 'Kolom USERNAME sudah terdaftar di database.';*/
	}else if( ! valid_email($EMAIL) ){
		$ERROR[] = 'Email tidak benar, contoh : nama@domain.com';
	}else if( $MAIL_EXISTS == '1' ){
		$ERROR[] = 'Email sudah digunakan';
	}else{

		$FIELDS = array(
			'NAMA','TELPON','EMAIL','LEVEL_ID','PROJECT_ID','KARYAWAN_ID'
		);

		$d = array();
		foreach($FIELDS as $F){
			$INSERT_VAL[] = "'".db_escape(get_input($F))."'";
			$UPDATE_VAL[] = $F."='".db_escape(get_input($F))."'";
		}

		if($OP=='' OR $OP=='add')
		{
			is_login('user.add');
			
			$FIELDS[] = 'PASSWORD';
			$INSERT_VAL['PASSWORD'] = "'".md5(get_input('PASSWORD'))."'";
			
			db_execute(" INSERT INTO user (".implode(',',$FIELDS).") VALUES (".implode(',',$INSERT_VAL).") ");
			
			$OP = 'edit';
			$ID = $DB->Insert_ID();
			header('location: '.$_SERVER['PHP_SELF'].'?op=edit&m=1&id='.$ID);
			exit;
		}
		else
		{
			is_login('user.edit');
			
			$UPDATE_VAL['PASSWORD'] = "PASSWORD='".md5(get_input('PASSWORD'))."'";
			if(get_input('PASSWORD')==""){
				unset($UPDATE_VAL['PASSWORD']);
			}
			
			db_execute(" UPDATE user SET ".implode(',',$UPDATE_VAL)." WHERE USER_ID='$ID' ");
			$SUCCESS = 'Data berhasil di simpan.';
		}
	}
}

if(get_input('m') == '1'){
	$SUCCESS = 'Data berhasil di simpan.';
}

include 'header.php';
?>

<section class="container" style="margin-top:70px;">
	<h1 style="margin-top:0px;" class="border-title">
		<?php echo ucfirst($OP) ?> User
		<a href="user.php" class="btn btn-warning" style="margin-left:10px;">&laquo; Back</a>
		<button class="btn btn-primary" onclick="$('#form').submit()"><span class="glyphicon glyphicon-floppy-disk"></span>&nbsp;&nbsp;Save</button>
		<?php if($OP=='edit'){ echo '<a href="user-action.php?op=add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>&nbsp;&nbsp;Add New</a>'; } ?>
	</h1>
	
	<?php include 'msg.php' ?>

	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	<form id="form" class="form-horizontal" action="user-action.php?op=<?php echo $OP ?>&id=<?php echo $ID ?>" method="POST">
	<input type="hidden" name="CURRENT_ID" value="<?php echo $ID ?>">
	<div class="row">
		<div class="col-md-6">
			<?php /*<div class="form-group">
				<label for="" class="col-sm-4 control-label">Username</label>
				<div class="col-sm-8">
					<input type="text" name="USERNAME" value="<?php echo set_value('USERNAME',$EDIT->USERNAME) ?>" class="form-control" maxlength="10">
				</div>
			</div>*/ ?>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Nama</label>
				<div class="col-sm-8">
					<input type="text" name="NAMA" value="<?php echo set_value('NAMA',$EDIT->NAMA) ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Telpon</label>
				<div class="col-sm-8">
					<input type="text" name="TELPON" value="<?php echo set_value('TELPON',$EDIT->TELPON) ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label for="" class="col-sm-4 control-label">Email</label>
				<div class="col-sm-8">
					<input type="text" name="EMAIL" value="<?php echo set_value('EMAIL',$EDIT->EMAIL) ?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="" class="col-md-4 control-label">Password</label>
				<div class="col-md-8">
					<input type="password" name="PASSWORD" value="" class="form-control">
					<?php if($EDIT->USER_ID != ""){ echo '<p>Leave blank if you dont wants update the password</p>'; } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Level</label>
				<div class="col-sm-8">
					<?php echo dropdown('LEVEL_ID',dropdown_option('user_level','LEVEL_ID','LEVEL','ORDER BY LEVEL_ID ASC'),set_value('LEVEL_ID',$EDIT->LEVEL_ID),' class="form-control" ') ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Unit</label>
				<div class="col-sm-8">
					<?php echo dropdown('PROJECT_ID',array(''=>'-- All Unit --')+dropdown_option('project','PROJECT_ID','PROJECT','ORDER BY PROJECT ASC'),set_value('PROJECT_ID',$EDIT->PROJECT_ID),' class="form-control" ') ?>
				</div>
			</div>
			<?php /*<div class="form-group">
				<label class="col-sm-4 control-label">Karyawan</label>
				<div class="col-sm-8">
					<?php echo dropdown('KARYAWAN_ID',array('' => '-- Not Connect --')+dropdown_option('karyawan','KARYAWAN_ID','NAMA',"WHERE NIK<>'' ORDER BY NAMA ASC"),set_value('KARYAWAN_ID',$EDIT->KARYAWAN_ID),' class="form-control" ') ?>
				</div>
			</div>*/ ?>
		</div>	
	</div>
	<div style="border-top:1px dashed #cccccc; margin-top:15px;">&nbsp;</div>
	</form>
</section>

<script>
$(document).ready(function(){
	$('input[name=USER_ID]').focus();
	$('input').keypress(function(e) {
		if (e.which == 13) {
			e.preventDefault();
			$('#form').submit();
		}
	});
});
</script>

<?php 
include 'footer.php'; 
?>