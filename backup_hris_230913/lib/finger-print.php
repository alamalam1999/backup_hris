<?php

function fp_connect($IP,$PORT = "80")
{
	try{
		$Connect = @fsockopen($IP, $PORT, $errno, $errstr, 1);
		if($Connect) return TRUE;
		else return FALSE;
	}
	catch(Exception $e){
		return FALSE;
	}
}

function fp_import_log($IP,$PORT = "80")
{
	$Connect = @fsockopen($IP, $PORT, $errno, $errstr, 10);
	if($Connect){
		$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
		fputs($Connect, "Content-Type: text/xml".$newLine);
		fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
		fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
		return parse_data($buffer,'<GetAttLogResponse>','</GetAttLogResponse>');
	}else{
		return FALSE;
	}
}

function parse_data($data,$p1,$p2)
{
	$data=" ".$data;
	$res="";
	$awal=strpos($data,$p1);
	if($awal!=""){
		$akhir=strpos(strstr($data,$p1),$p2);
		if($akhir!=""){
			$res=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
		}
	}
	return $res;	
}

function clear_log($ip){
	$Connect = @fsockopen($ip, "80", $errno, $errstr, 10);
		if($Connect){
			$soap_request="<ClearData><ArgComKey xsi:type=\"xsd:integer\">0</ArgComKey><Arg><Value xsi:type=\"xsd:integer\">3</Value></Arg></ClearData>";
			$newLine="\r\n";
			fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
			fputs($Connect, "Content-Type: text/xml".$newLine);
			fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
			fputs($Connect, $soap_request.$newLine);
			$buffer="";
			while($Response=fgets($Connect, 1024)){
				$buffer=$buffer.$Response;
			}
			return $buffer;
		}else{
			return FALSE;
		}
}

//echo clear_log('192.168.0.201');
//$tes = fp_import_log('192.168.0.201');
//print_r($tes);