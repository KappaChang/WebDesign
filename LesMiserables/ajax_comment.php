<?
require '../../include/app_top.php';
global $db;



foreach ($_POST as $k => $value) $$k = stripslashes_($value);
$error = 0;
$msg;

switch (@$action){
	case 'comment':
			$nowstamp = time();

			$a = strptime('08-02-2013', '%d-%m-%Y');
			$startstamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);

			$e = strptime('22-02-2013', '%d-%m-%Y');
			$endstamp = mktime(0, 0, 0, $e['tm_mon']+1, $e['tm_mday'], $e['tm_year']+1900);

			if($nowstamp < $startstamp){
				$error = 2;
				$msg = "活動於2月8號開始";
				$tmp = array(
					'error'=> $error,
					'msg'=>$msg
				);

			}else if($nowstamp > $endstamp){
				$error = 3;
				$msg = "活動已經結束";
				$tmp = array(
					'error'=> $error,
					'msg'=>$msg
				);

			}
			else{

				$fbid = $_SESSION['fb_339999192774080_user_id'];
				$member_id = $_SESSION['member_id'];
				$rs = $db->getArray("SELECT * FROM lesniserables WHERE mid = ?", array($db->addq($member_id)));

				if(count($rs) == 0){
				//沒過言 新增留言
					$field_date = array(
						'mid'=> $member_id,
						'nickname' => $user_name,
						'comment' => $comment,
						'datetime' => 'now()',
						'fbid'=>$fbid
						);
					$sql = tep_db_perform("lesniserables", $field_date, 'insert');
					$db->execute($sql);

					$tmp = array(
						'erroe' => 0,
						'msg'=> '留言成功!'
						);

				}else{

					$error = 1;
					$msg = "您已經留過言了";

					$tmp = array(
						'error'=> $error,
						'msg'=>$msg
						);
				}

			}
		break;

	case 'load':

		$rs = $db->getArray("SELECT * FROM lesniserables WHERE 1 ORDER BY datetime DESC");
		// print_r($rs);
		foreach ($rs as $key => $value) {
			$tmp[$key] = array(
				'nickname'=> $value['nickname'],
				'comment'=> $value['comment'],
				'datetime'=> $value['datetime'],
				// 'head_photo'=> MemberHead($rs[$key],50),
				'fbid'=> $value['fbid']
				);

		}


		break;




}

echo json_encode(@$tmp);











// require 'include/app_bottom.php';

?>