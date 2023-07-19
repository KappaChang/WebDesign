<?php

	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);

	include __DIR__.'/global.php';
	include __DIR__.'/PHPExcel.php';
	include __DIR__.'/PHPExcel/IOFactory.php';

	
	$event_name = 'luckybag2014';

	$gen_limit = 4000;

	$code_name = 'car_code';
	$code_len = 4;
	$code_array = [];

	//main process
	main();

	function main() {
		global $gen_limit, $code_array, $code_name, $code_len;
		
		//Generate car code
		for($i=0; $i < $gen_limit; $i++){
			if(!process())
				process();
		}
		writeExcel($code_array);

		//Generate concert code
		$code_name="concert_code";
		$code_len = 8;
		$code_array = [];

		for($i=0; $i < $gen_limit; $i++){
			if(!process())
				process();
		}
		writeExcel($code_array);
	}

	
	function process() {
		global $code_array;

		$code = generatorCode();		

		if(is_numeric($code))
			$code = generatorCode();

		if(chkCodeInDB($code))
		{
			$result = writeCodeToDB($code);

			if($result){
				array_push($code_array, $code);
				return true;
			}else{
				return false;
			}
		}else {
			return false;
		}
	}
	

	//Generate Code
	function generatorCode()
	{
		global $event_name, $code_name, $code_len;

		$code = '';

		$word = 'abcdefghijkmnpqrstuvwxyz123456789';
		$len = strlen($word);
		for($i=0; $i<$code_len; $i++){
			$code .= $word[rand() % $len];
		}
		return $code;
	}

	//Check DB
	function chkCodeInDB($chkCode){
		global $event_name, $code_name, $code_len;

		$rs = ORM::for_table('event')
			->select_many('event_id', 'event_name', 'dummy')
			->where('event_name', $event_name.'_'.$code_name)
			->where('dummy', $chkCode)
			->where('deleted_by', '')
			->find_one();
		if(!$rs){
			return true;
		}
		return false;
	}

	//Write Db
	function writeCodeToDB($chkCode){
		global $event_name, $code_name, $code_len;

		$rs = ORM::for_table('event')->create();
		$rs->event_name = $event_name.'_'.$code_name;
		$rs->created_at = date('Y-m-d H:i:s');
		$rs->created_by = $event_name;
		$rs->dummy = $chkCode;
		if($rs->save()){
			return true;
		}
		return false;
	}

	//Write Excel
	function writeExcel($code_array){
		global $event_name, $code_name;

		$PHPExcel = new PHPExcel();

		//Set editable sheet in index 0;
		$PHPExcel->setActiveSheetIndex(0);
		$i = 1;
		foreach($code_array as $c){
			//$PHPExcel->getActiveSheet()->setCellValue('A'.$i, $c);	
			$PHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $c);
			$i++;
		}
		
		//Export to Excel file
		$PHPExcelWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
		$PHPExcelWriter->save('/var/www/html/kuan/public/event/luckybag2014/output/'.$code_name.'.xls');
	}
?>