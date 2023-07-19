<?php
/*
check our post variable from index.php, just to insure user isn't accessing this page directly.
You can replace this with strong function, something like HTTP_REFERER
*/
if(isset($_POST["connect"]) && $_POST["connect"]==1)
{
	include_once("../../include/app_top.php");
	include_once("../../include/fbconfig.php"); //Include configuration file.

	//Call Facebook API
	if (!class_exists('FacebookApiException')) {
	require_once('../../include/facebook.php' );
	}
		$facebook = new Facebook(array(
		'appId' => $appId,
		'secret' => $appSecret,
	));

	$fbuser = $facebook->getUser();
	if ($fbuser) {
		try {
			// Proceed knowing you have a logged in user who's authenticated.
			$me = $facebook->api('/me'); //user
			$uid = $facebook->getUser();
		}
		catch (FacebookApiException $e)
		{
			//echo error_log($e);
			$fbuser = null;
		}
	}

	// redirect user to facebook login page if empty data or fresh login requires
	if (!$fbuser){
		$loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$return_url, false));
		header('Location: '.$loginUrl);
	}

	//user details
	$fullname = $me['first_name'].' '.$me['last_name'];
	$username = $me['username'];
	$email = $me['email'];
	$head_photo = 'http://graph.facebook.com/'.$uid.'/picture?width=60&height=60';
	$gender = ($me['gender']=='male'?0:1);
	$locale = $me['locale'];
	$location = @$me['location'];

	/* connect to mysql */
	global $db;

	//Check user id in our database
	$rs = $db->execute("select member_id FROM member WHERE ( fbid='$uid' or member_account = '" . $email. "' )");
	$mid = 0;


	if(!$rs->EOF)
	{
		//getfile($head_photo);
		$member_id = $rs->fields('member_id');
		$field_date = array(
			'nickname' => $fullname,
			'gender' => $gender,
			'last_login_datetime' => 'now()',
			'locale' => $locale,
			// '$location' => $location['name'],
			// 'event' => "LesMiserables"
			);
		$sql = tep_db_perform("member", $field_date, 'update', "member_id ='".$member_id."'");
		$db->execute($sql);


		login_user(true, $fullname , $head_photo , $member_id, 1);
	}
	else
	{
		$db->execute("insert into member(member_account, fbid, nickname, account_mail, gender, reg_datetime, last_login_datetime, locale, location , event)values(?,?,?,?,?,now(),now(),?,? , 'LesMiserables')",
			array($email, $uid, $fullname, $email, $gender,$locale, $location['name']));


		$member_id = $db->Insert_ID();
		$mBoPlaylist = new BoPlaylist();
		$mBoPlaylist->Add($member_id,'歌單一');

		//User is now connected, log him in
		login_user(true, $fullname, $head_photo, $member_id, 1);
	}

// 	$mid = $member_id;
// 	$pl = array();
// 	$track = array();
// 	$friend = array();

// //

	// $mBoPlaylist = new BoPlaylist();
	// $tmp = $mBoPlaylist->GetMemberPlaylist($member_id);

	// foreach($tmp as $k => $v)
	// {
	// 	if ($k == 0)
	// 	{
	// 		$pid = $v['playlist_id'];
	// 		$tmp_track = $mBoPlaylist->GetMemberPlaylistTrack($pid);

	// 		foreach($tmp_track as $kk => $vv){
	// 			array_push($track,
	// 				array(
	// 				'tid' => $vv['track_id'],
	// 				'guid' => $vv['guid'],
	// 				'title' => $vv['play_name'], 'en_title' => $vv['play_en_name']));
	// 		}
	// 	}
	// 	array_push($pl,
	// 		array(
	// 			'playlist_id' => $v['playlist_id'],
	// 			'playlist_title' => $v['playlist_title']));
	// }

	// $friend = GetSubscribe();



	$error = 0;
	$msg = "";
	$redirect = "";
	$ret = array(
		'error' => $error,
		'msg' => $msg,
		'redirect' => $redirect,
		'nickname' => $_SESSION['member_nickname'],
		'head_photo' => $_SESSION['member_head_photo'],
		'mid' => $member_id,
		'location' => $location,
		// 'pl' => $pl,
		// 'track' => $track,
		// 'friend' => $friend
	);
	echo json_encode($ret);
}

function getfile($file){
	// facebook photo

//	$data = file_get_contents($file);
//	file_put_contents($file, '/upload/member_photo/thumbs/' . $current);

}
?>