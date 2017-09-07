<?php
//define('DB','mps_icc');
require 'vendor/autoload.php';
require 'config.php';
require 'cryptojs-aes.php';
//require 'AES.class.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Firebase\JWT\JWT;
define('CRYPTOJSKEY','mpsicctocontrold');
$app = new \Slim\App();
// CORS
/*

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
*/

$app->post('/login','login' );
$app->post('/updatePwd','updatePwd' );
$app->get('/genPtype/{uType}/{scope}/{pv}','genPtype');
$app->get('/genPcode/{uType}/{scope}/{pv}/{custptype}','genPcode');
$app->get('/isUrl','isUrl');
$app->post('/genProvince','genProvince');
$app->post('/listRegist','listRegist');
$app->post('/getJob','getJob');
$app->post('/saveProblem','saveProblem');
$app->post('/upload','upload');
$app->post('/saveComment','saveComment');
$app->post('/uploadComment','uploadComment');
$app->post('/getComment','getComment');
$app->get('/isConnect','isConnect');
$app->post('/getSvdata','getSvdata');
$app->post('/confirmClose','confirmClose');
$app->post('/test','test');
$app->run();
function test(Request $request, Response $response){
		/*try{
			//$db=getDB();
			//$db->exec("set names utf8");
			/*$sql="select * from cen_province where province_id in(:p1,:p2,:p3)";
			$stmt=$db->prepare($sql);
			$stmt->execute([
				"p1"=>'65',
				"p2"=>'66',
				"p3"=>'67'
			]);*/
			/*$sql="select * from cen_province where province_id in(?,?,?)";
			$stmt=$db->prepare($sql);
			$stmt->execute([
				'65',
				'66',
				'67'
			]);*/
			/*$sql="select * from cen_province where province_id in(?,?,?)";
			$stmt=$db->prepare($sql);
			$s1=65;
			$s2=66;
			$s3=67;
			$stmt->bindParam(1,$s1,PDO::PARAM_STR);
			$stmt->bindParam(2,$s2,PDO::PARAM_STR);
			$stmt->bindParam(3,$s3,PDO::PARAM_STR);
			$stmt->execute();
			*/
			/*$sql="select * from cen_province where province_id in(:x,:y,:z)";
			$stmt=$db->prepare($sql);
			$s1=65;
			$s2=66;
			$s3=67;
			$stmt->bindParam('x',$s1,PDO::PARAM_STR);
			$stmt->bindParam('y',$s2,PDO::PARAM_STR);
			$stmt->bindParam('z',$s3,PDO::PARAM_STR);
			$stmt->execute();
			*/
			/*$sql="select * from cen_province where province_id in(:x,:y,:z)";
			$stmt=$db->prepare($sql);
			$s1=65;
			$s2=66;
			$s3=67;
			$stmt->bindParam(':x',$s1,PDO::PARAM_STR);
			$stmt->bindParam(':y',$s2,PDO::PARAM_STR);
			$stmt->bindParam(':z',$s3,PDO::PARAM_STR);
			$stmt->execute();
			*/
			//$rs=$stmt->fetchAll(PDO::FETCH_ASSOC);
			
			
			
		/*	
			print_r($rs);
		}catch(PDOException $e){
			$arr=["status"=>false,"msg"=>$e->getMessage()];
		}*/
		
		$db=new DB();
		$db->beginTransaction("ทดสอบ");
		$sql="select * from cen_province where province_id in(?,?,?)";
		$rs=$db->sqlexe($sql,[65,66,67]);
		$db->sqlexe("update cen_province set section_id=6 where province_id=65");
		$sql="select * from cen_province where section_id=:s1 and province_id in(:s2,:s3,:s4)";
		$rs=$db->sqlexe($sql,[
			's1'=>6,
			's2'=>65,
			's3'=>66,
			's4'=>67
			]
			);
		
		if($db->isOk()){
			$arr=["status"=>true,"data"=>$rs];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
		$db->endTransaction();
		echo json_encode($arr);
		
}
	
function isConnect(Request $request,Response $response){
	$arr=["status"=>true];
	header('Content-type: text/html; charset=UTF-8');
	echo json_encode($arr);
}
function genPtype(Request $request, Response $response,$args){
	$uType=$args['uType'];
	$scope=$args['scope'];
	$pv=$args['pv'];
	if($pv=='null'){
		$pv='';
	}
	$scope=$scope=='null'?'':$scope;
	$db=new DB();
	if($uType=='P'){
		$condi=" and a2.cc=:scope ";
	}elseif($uType=='R'){
		if($pv==''){
			$condi=" and  a3.section_id=:scope ";
		}else{
			$scope=$pv;
			$condi=" and  a2.cc=:scope ";
		}
	}else{
		if($pv==''){
			$condi="";
		}else{
			$scope=$pv;
			$condi=" and  a2.cc=:scope ";
		}
	}
	$sql="select 
				a1.cust_ptype,
					a1.cust_desc
				from 
					".DB.".cen_type_custptype a1,
					".DB.".cen_cust_place a2,
					".DB.".cen_province a3
				where 
					a1.cust_ptype=a2.cust_ptype
					and a2.cc=a3.province_id
					$condi
					group by a1.cust_ptype;
				";
	$params=null;
	if($condi!=""){
		$params['scope']=$scope;
	}
	$rs=$db->sqlexe($sql,$params);
	if($db->isOk()){
		$arr=[
			'status'=>true,
			'data'=>$rs
		];
	}else{
		$arr=["status"=>false,"msg"=>$db->getError()];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo json_encode($arr);
	echo "\n\r";
	echo "\n\r";
	echo "\n\r";
	echo "\n\r";
	
};
/*
function genPtype(Request $request, Response $response,$args){
	$uType=$args['uType'];
	$scope=$args['scope'];
	$pv=$args['pv'];
	if($pv=='null'){
		$pv='';
	}
	$scope=$scope=='null'?'':$scope;
	try{
		$db=getDB();
		$db->exec("set names utf8");
		if($uType=='P'){
			$condi=" and a2.cc=:scope ";
		}elseif($uType=='R'){
			if($pv==''){
				$condi=" and  a3.section_id=:scope ";
			}else{
				$scope=$pv;
				$condi=" and  a2.cc=:scope ";
			}
		}else{
			if($pv==''){
				$condi="";
			}else{
				$scope=$pv;
				$condi=" and  a2.cc=:scope ";
			}
		}
		$sql="select 
					a1.cust_ptype,
					a1.cust_desc
				from 
					".DB.".cen_type_custptype a1,
					".DB.".cen_cust_place a2,
					".DB.".cen_province a3
				where 
					a1.cust_ptype=a2.cust_ptype
					and a2.cc=a3.province_id
					$condi
					group by a1.cust_ptype;
				";
		$stmt=$db->prepare($sql);
		if($condi!=""){
			$stmt->bindParam('scope',$scope,PDO::PARAM_STR);
		}
		$stmt->execute();
		$rs= $stmt->fetchAll(PDO::FETCH_ASSOC);
		$arr=[
			'status'=>true,
			'data'=>$rs
		];
		
	}catch(PDOException $e){
		$arr=["status"=>false,"msg"=>$e->getMessage()];
	}
	header('Content-type: text/html; charset=UTF-8');
	//print_r($arr);
	echo json_encode($arr);
	echo "\n\r";
	echo "\n\r";
	echo "\n\r";
	echo "\n\r";
	
};
*/


/*function genPcode(Request $request, Response $response,$args){
	//$token=$request->hasHeader('x-access-token');
	$uType=$args['uType'];
	$scope=$args['scope'];
	$pv=$args['pv'];
	$custptype=$args['custptype'];
	if($pv=='null'){
		$pv='';
	}
	$custptype=$custptype=='null'?'':$custptype;
	try{
		$db=getDB();
		$db->exec("set names utf8");
		if($uType=='P'){

			$condi=" and cp.cc=:scope ";
		}elseif($uType=='R'){

			if($pv==''){

				$condi=" and  p.section_id=:scope ";
			}else{

				$scope=$pv;
				$condi=" and  cp.cc=:scope ";
			}
		}else{

			if($pv==''){

				$condi="";
			}else{

				$scope=$pv;
				$condi=" and  cp.cc=:scope ";
			}
		}
		
		if($custptype!=''){
			$condi .=" and cp.cust_ptype=:custptype ";
		}

		$sql="select 
				cp.* 
			from 
				".DB.".cen_cust_place cp,
				".DB.".cen_province p  
			where 
				cp.cc=p.province_id
				$condi  
			order by cp.cust_ptype,cp.cust_pcode";

		$stmt=$db->prepare($sql);
		if($condi!=""){
			$stmt->bindParam('scope',$scope,PDO::PARAM_STR);
		}
		if($custptype!=""){
			$stmt->bindParam('custptype',$custptype,PDO::PARAM_STR);
		}
		$stmt->execute();
		$rs= $stmt->fetchAll(PDO::FETCH_ASSOC);
		$arr=[
			'status'=>true,
			'data'=>$rs
		];
	}catch(PDOException $e){
		$arr=["status"=>false,"msg"=>$e->getMessage()];
	}
	header('Content-type: text/html; charset=UTF-8');
	//print_r($arr);
	echo json_encode($arr);
	echo "\n\r";
	echo "\n\r";
};
*/
function genPcode(Request $request, Response $response,$args){
	$uType=$args['uType'];
	$scope=$args['scope'];
	$pv=$args['pv'];
	$custptype=$args['custptype'];
	if($pv=='null'){
		$pv='';
	}
	$custptype=$custptype=='null'?'':$custptype;
	$db=new DB();
	if($uType=='P'){
		$condi=" and cp.cc=:scope ";
	}elseif($uType=='R'){
		if($pv==''){
			$condi=" and  p.section_id=:scope ";
		}else{
			$scope=$pv;
			$condi=" and  cp.cc=:scope ";
		}
	}else{
		if($pv==''){
			$condi="";
		}else{
			$scope=$pv;
			$condi=" and  cp.cc=:scope ";
		}
	}
	if($custptype!=''){
		$condi .=" and cp.cust_ptype=:custptype ";
	}
	$sql="select 
			cp.* 
		from 
			".DB.".cen_cust_place cp,
			".DB.".cen_province p  
		where 
			cp.cc=p.province_id
			$condi  
			order by cp.cust_ptype,cp.cust_pcode";
	$params=null;
	if($condi!=""){
		$params['scope']=$scope;
	}
	if($custptype!=""){
		$params['custptype']=$custptype;
	}
	$rs=$db->sqlexe($sql,$params);
	if($db->isOk()){
		$arr=[
			'status'=>true,
			'data'=>$rs
		];
	}else{
		$arr=["status"=>false,"msg"=>$db->getError()];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo json_encode($arr);
	echo "\n\r";
	echo "\n\r";
};

function listRegist(Request $request, Response $response){
	$json =$request->getParams();
	$data=$json['data'];
	try{
		$fname=$data['fname'];
		$lname=$data['lname'];
		$params=[':fname'=>"'%$fname%'"];
		$sql="select  * from ".DB.".cen_user where user_fname like  :fname   and user_lname like :lname";
		$db=getDB();
		$db->exec("set names utf8");
		$stmt=$db->prepare($sql);
		$stmt->bindParam("fname", $fname, PDO::PARAM_STR);
		$stmt->bindParam("lname", $lname, PDO::PARAM_STR);
		$stmt->execute();
		$rs=$stmt->fetchAll();
		$arr=["status"=>true,"data"=>$rs];
	}catch(PDOException $e){
		$arr=["status"=>false,"msg"=>$e->getMessage()];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;

}
function updatePwd(Request $request, Response $response){
	$json =$request->getParams();
	$data=$json['data'];
	try{
		
		$userid=$data['user_id'];
		$custPtype=$data['cust_ptype'];
		$userRcode=$data['user_rcode'];
		$password=md5($data['password']);
		$userTel=$data['user_tel'];
		$sql="update ".DB.".cen_user set user_mobile=1,user_pwd=:password,user_tel=:userTel where user_id=:userid and cust_ptype=:custPtype and user_rcode=:userRcode";
		$db=getDB();
		$db->exec("set names utf8");
		$stmt=$db->prepare($sql);
		$stmt->bindParam("userid", $userid, PDO::PARAM_STR);
		$stmt->bindParam("custPtype", $custPtype, PDO::PARAM_STR);
		$stmt->bindParam("userRcode", $userRcode, PDO::PARAM_STR);
		$stmt->bindParam("password",$password,PDO::PARAM_STR);
		$stmt->bindParam("userTel",$userTel,PDO::PARAM_STR);
		$stmt->execute();
		//$rs=$stmt->fetchAll();
		$arr=["status"=>true,"msg"=>"ACTIVE OK"];
	}catch(PDOException $e){
		$arr=["status"=>false,"msg"=>$e->getMessage()];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;

}
function genProvince(Request $request, Response $response){
	$uType=$request->getParam('uType');
	$scope=$request->getParam('scope');
	try{
		$db=getDB();
		$db->exec("set names utf8");
		if($uType=='P'){
			$condi=" and province_id=:scope";
		}elseif($uType=='R'){
			$condi=" and section_id=:scope";
		}else{
			$condi="";
		}
		$sql="select province_id,province_name  from ".DB.".cen_province where province_id not in(0,99) $condi order by province_id asc";
		$stmt=$db->prepare($sql);
		if($condi!=''){
			$stmt->bindParam('scope',$scope,PDO::PARAM_STR);
		}
		$stmt->execute();
		$rs= $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$arr=[
			"status"=>true,
			"data"=>$rs
		];
		
	}catch(PDOException $e){
		$arr=["status"=>false,"msg"=>$e->getMessage()];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
};
function upload(Request $request, Response $response){
	header('Access-Control-Allow-Origin: *');
	$target_path = "uploads/msv-pic/";
	 
	$target_path = $target_path . basename( $_FILES['file']['name']);
	 
	if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
		$arr=[
			"status"=>true,
			"msg"=>"upload pic ok"
		];
	} else {
		$arr=[
			"status"=>false,
			"msg"=>"There was an error uploading the file to $target_path, please try again!"
		];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
}
function uploadComment(Request $request, Response $response){
	header('Access-Control-Allow-Origin: *');
	$target_path = "uploads/comment-pic/";
	$target_path = $target_path . basename( $_FILES['file']['name']);
	$sv_no=$_POST['sv_no'];
	$comment_no=$_POST['comment_no'];
	$comment_photo=$target_path;
	if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
		//update pic to mobile_comment
		try{
			$sql="update ".DB.".mobile_comment set comment_photo=:commentPhoto where sv_no=:svNo and comment_no=:commentNo ";
			$db=getDB();
			$db->exec("set names utf8");
			$stmt=$db->prepare($sql);
			$stmt->bindParam('commentPhoto',$comment_photo,PDO::PARAM_STR);
			$stmt->bindParam('svNo',$sv_no,PDO::PARAM_STR);
			$stmt->bindParam('commentNo',$comment_no,PDO::PARAM_INT);
			$stmt->execute();
			$arr=[
				"status"=>true,
				"msg"=>"upload pic ok and update to mobile_comment"
			];
		}catch(PDOException $e){//ทำรายการไม่สำเร็จ
			//delete file 
			@unlink($comment_photo);
			$arr=[
				"status"=>false,
				"msg"=>"upload pic ok .But ".$e->getMessage()
			];
		}
	} else {
		$arr=[
			"status"=>false,
			"msg"=>"There was an error uploading the file to $target_path, please try again!"
		];
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
}
function saveProblem(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	$msvDetail=$data['msv_detail'];
	$ptype=$data['msv_type'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$userData=$ck['data'];
		$db=new DB();
		$db->beginTransaction("บันทึกการแจ้งปัญหา");
		$sql="
			select 
				concat('0',p.section_id) as rg 
			from 
				".DB.".cen_province p 
				,".DB.".cen_cust_place p2 
			where 
				p2.cust_ptype=:custPtype 
				and p2.cust_pcode=:custPcode
				and p2.cc=p.province_id 
		";
		$rs=$db->sqlexe($sql,['custPtype'=>$custPtype,'custPcode'=>$custPcode]);
		$rg=$rs[0]['rg'];
		$yy=substr(date('Y')+543,2,2);
		
		$prekey="MRG".$rg.$yy."%";
		//msv_no
		$sql="
		select max(substr(msv_no,8,7))+1 as no from ".DB.".mobile_sv where msv_no like :prekey 
		";
		$rs=$db->sqlexe($sql,['prekey'=>$prekey]);
		$no=$rs[0]['no'];
		$msvNo="MRG$rg$yy".sprintf('%07d',$no);
		$sql="insert into ".DB.".mobile_sv(msv_no,msv_uid,msv_type,msv_detail,cust_ptype,cust_pcode,msv_adate,msv_atime,msv_status)  values(?,?,?,?,?,?,now(),now(),0)";
		$params=[$msvNo,$userId,$ptype,$msvDetail,$custPtype,$custPcode];
		$db->sqlexe($sql,$params);
		
		//เก็บ ทราน ไว้ที่ sv_trans
		$sql=" insert into ".DB.".sv_trans(sv_no,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time) values(?,?,?,?,?,?,?,?,?,0,now(),now())";
		$seq=1;//ทรานแรกสุด
		$userType=1;
		$kpid=$userData['job_id'];
		$skpid=$userData['job_id'];
		$rkpid=$userData['job_id'];
		$params=[$msvNo,$seq,$userId,$userType,$kpid,$skpid,$rkpid,$custPtype,$custPcode];
		$db->sqlexe($sql,$params);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"Insert Ok",
				"msv_no"=>$msvNo
			];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
		$db->endTransaction();
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
};
function saveComment(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$msvNo=$data['msv_no'];
	$detail=$data['comment_detail'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$db->beginTransaction("เพิ่ม comment");
		$sql= " select if(max(comment_no) is null ,1,max(comment_no)+1) as `no` from ".DB.".mobile_comment where sv_no=:svNo";
		$rs=$db->sqlexe($sql,['svNo'=>$msvNo]);
		$no=$rs[0]['no'];
		$pno=sprintf('%07d',$no);
		$sql="insert into ".DB.".mobile_comment(sv_no,comment_no,comment_uid,comment_utype,comment_custptype,comment_custpcode,comment_detail,comment_adate,comment_atime) 
		values(?,?,?,1,?,?,?,now(),now())";
		$userData=$ck['data'];
		$userType=$userData['user_type'];
		$custptype=$userType==1?$userData['cust_ptype']:$userData['place_type'];
		$custpcode=$userType==1?$userData['cust_pcode']:$userData['place_code'];
		$arr=[
			$msvNo,
			$no,
			$userId,
			$custptype,
			$custpcode,
			$detail
		];
		$db->sqlexe($sql,$arr);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"Insert Ok",
				"sv_no"=>$msvNo,
				"comment_no"=>$no,
				"pno"=>$pno	
			];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
		$db->endTransaction();
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
};
function confirmClose(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$msvNo=$data['msv_no'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$user=$ck['data'];
		$rate='';
		$db=new DB();
		$db->beginTransaction('เริ่มทำ ยืนยันการปิดงาน');
		$isMoi=substr($msvNo,0,1);	
		$sql="select skp_id from ".DB.".sv_trans where sv_no=:svNo and seq=1";	
		$rs=$db->sqlexe($sql,['svNo'=>$svNo]);
		$skpid=$rs[0]['skp_id'];
		$rkpid=$skpid;
		$kpid=$skpid;
		
		$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
		$rs=$db->sqlexe($sqlmax,['svNo'=>$svNo]);
		$seq=$rs[0]['m']+1;
		
		$custptype=$isMoi=='M'?$user['cust_ptype']:$user['place_type'];
		$custpcode=$isMoi=='M'?$user['cust_pcode']:$user['place_code'];
		$params['msvNo']=$msvNo;
		$params['userId']=$userId;
		if($isMoi=='M'){
			$userType=1;
			$sql="update ".DB.".mobile_sv set
						msv_status='4',
						msv_udate=now(),
						msv_utime=now(),
						msv_updid=:userId
					where 
						msv_no=:msvNo	
					";
		}else{
			$userType=2;
			$sql="update ".DB.".sv_service set
						msv_status='4',
						sv_fin_date=now(),
						sv_fin_time=now(),
						sv_fin_emp=:userId,
						sv_satisfaction=:rate
					where 
						sv_no=:msvNo
					
			";
			$params['rate']=$data['rate'];
		}
		$db->sqlexe($sql,$params);
		$sql=" insert into ".DB.".sv_trans(sv_no,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time)
					values(:F1,:F2,:F3,:F4,:F5,:F6,:F7,:F8,:F9,:F10,:F11,:F12)
				";
		$db->sqlexe($sql,[
				"F1"=>$msvNo,
				"F2"=>$seq,
				"F3"=>$userId,
				"F4"=>$userType,
				"F5"=>$kpid,
				"F6"=>$skpid,
				"F7"=>$rkpid,
				"F8"=>$custptype,
				"F9"=>$custpcode,
				"F10"=>4,
				"F11"=>date('Y-m-d'),
				"F12"=>date('H:i:s')
		]);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"close Job Ok",
			];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
		$db->endTransaction();
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
};

function getJob(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	$uType=$data['uType'];
	$scope=$data['scope'];
	$pv=$data['pv'];

	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$cptype=$custPtype!=''?' and sv.cust_ptype=:custPtype ':'';
		$cpcode=$custPcode!=''?' and sv.cust_pcode=:custPcode ':'';
		if($uType=='P'){
			$cScope =' and p.cc=:scope ';
		}elseif($uType=='R'){
			if($pv==''){
				$cScope = ' and pp.section_id=:scope ';
			}else{
				$scope=$pv;
				$cScope =' and p.cc=:scope ';
			}
		}else{
			if($pv==''){
				$cScope = '';
			}else{
				$scope=$pv;
				$cScope =' and p.cc=:scope ';
			}
		}		
		$sql="select 
				sv.*,
				concat(u.user_fname,' ',u.user_lname) as thiname
			from 
					".DB.".mobile_sv sv,
					".DB.".cen_user u,
					".DB.".cen_cust_place p,
					".DB.".cen_province pp
				where
					sv.msv_status in(0,1,3,5)
					$cptype
                    $cpcode					 
					and sv.cust_ptype=u.cust_ptype
					and sv.cust_pcode=u.user_rcode
					and sv.msv_uid=u.user_id
					and sv.cust_ptype=p.cust_ptype
					and sv.cust_pcode=p.cust_pcode
					and pp.province_id=p.cc
					$cScope
				";
		$params=null;
		if($custPtype!=''){$params['custPtype']=$custPtype;}
		if($custPcode!=''){$params['custPcode']=$custPcode;}
		if($scope!=''){$params['scope']=$scope;}
		$rs=$db->sqlexe($sql,$params);	
		$sql="select 
				sv.sv_no as msv_no
				,case sv.problem_type
					when 'P1' then 1
					when 'P2' then 2
					when 'P3' then 3
				 end as msv_type
				 ,(select concat(user_fname,' ',user_lname) from cen_user  where  user_id=sv.user_id and cust_ptype=:custPtype and user_rcode=:custPcode) as thiname
				 ,sv.sv_date as msv_adate
				 ,sv.sv_time as msv_atime
				 ,sv.sv_detail as msv_detail
				 ,sv.cust_ptype
				 ,sv.cust_pcode
				 ,sv.msv_no as msv_no2
				 ,sv.msv_status

				 
			from 
				sv_service sv
			where
				sv.msv_status in(0,1,3,5)
				and sv.cust_ptype=:custPtype
				and sv.cust_pcode=:custPcode
				
							";

		$rs1=$db->sqlexe($sql,[
			'custPtype'=>$custPtype,
			'custPcode'=>$custPcode
		]);
		
		if($db->isOk()){		
			$rs=array_merge($rs,$rs1);
			$arr=[
					"status"=>true,
					"data"=>$rs
				];			
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}

function getSvdata(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$msvNo=$data['msv_no'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$sql="
			select  sv.*
					,(select concat(user_fname,' ',user_lname) from ".DB.".cen_user where user_id=sv.msv_uid and cust_ptype=sv.cust_ptype and user_rcode=sv.cust_pcode) as thiname
			from ".DB.".mobile_sv sv 
			where 
				sv.msv_no=:msvNo";
			
		$rs=$db->sqlexe($sql,['msvNo'=>$msvNo]);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"data"=>$rs
			];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
};
function getComment(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$msvNo=$data['msv_no'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();	
		$sv=substr($msvNo,0,2);
		if($sv=='RG'){
			$sql="select msv_no from ".DB.".sv_service where  sv_no=:msvNo";
			$stmt=$db->prepare($sql);
			$stmt->bindParam('msvNo',$msvNo,PDO::PARAM_STR);
			$stmt->execute();
			$rs=$stmt->fetchAll();
			$svno=$rs[0]['msv_no'];
		}else{
			$svno=$msvNo;
		}
		$sql="
			select 
					c.*,
					case c.comment_utype
						when 1 then (select concat(u.user_fname,' ',u.user_lname) from ".DB.".mobile_sv s,".DB.".cen_user u where s.msv_no=:svNo2 and s.msv_uid=u.user_id and s.cust_ptype=u.cust_ptype and s.cust_pcode=u.user_rcode) 
						when 2 then (select concat(e.emp_fname,' ',e.emp_lname) from ".DB.".cen_emp e where  e.emp_id=c.comment_uid )
					end as thiname
				from 
					".DB.".mobile_comment c
				where 
				c.sv_no=:svNo
				";
		$rs=$db->sqlexe($sql,['svNo'=>$msvNo,'svNo2'=>$svno]);
		if($db->isOk()){			
			$arr=[
					"status"=>true,
					"data"=>$rs
			];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}
	}else{
		$arr=$ck;
	}
	header('Content-type: text/html; charset=UTF-8');
	echo "\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
};
function login(Request $request, Response $response) {
	$tel=$request->getParam('tel');
	$password=md5($request->getParam('password'));
	$loginType=$request->getParam('loginType');
	/*
		check phone and password
	*/
	$db=new DB();
	/*
		ในระบบ login จะมี 2 ฐานข้อมูลคือ 
		1. cen_user เป็นของข้าราชการ login
		2. cen_emp เป็นของ cdg login
		-ดังนั้น ต้องมีตัวเลือกให้เลือกก่อนว่าจะใช้ idcode หรือว่า  หมายเลขโทรศัพท์   ถ้า idcode จะเป็นของ cdg ถ้าเป็น เบอร์โทรจะเป็นของลูกค้า
			
	*/
	if($loginType==1){//ข้าราชการให้ดึงจาก cen_user
		$sql="
			select 
				a1.*
				,a3.section_id 
				,a2.cc
			from 
				cen_user a1,
				cen_cust_place a2,
				cen_province a3
			where 
				a1.user_tel=:tel
				and a1.user_pwd=:password
				and a1.cust_ptype=a2.cust_ptype
				and a1.user_rcode=a2.cust_pcode
				and a2.cc=a3.province_id
		";
	}else{//พนักงานบริษัท ให้ดึงจาก cen_emp
			$sql="select * from ".DB.".cen_emp where emp_id=:tel and emp_pwd=:password";
	}
	$userData=$db->sqlexe($sql,['tel'=>$tel,'password'=>$password]);
	if(count($userData)<1){
		$arr=["status"=>false,"msg"=>"ไม่พบรายการในฐานข้อมูล"];
	}else{
		$userData=$userData[0];
		$data=[
				'user_id'=>($loginType==1?$userData['user_id']:$userData['emp_id']),
				'user_title'=>($loginType==1?$userData['user_title']:$userData['emp_title']),
				'user_fname'=>($loginType==1?$userData['user_fname']:$userData['emp_fname']),
				'user_lname'=>($loginType==1?$userData['user_lname']:$userData['emp_lname']),
				'user_type'=>$loginType,
				'job_id'=>($loginType==1?$userData['job_id']:''),
				'section_id'=>($loginType==1?$userData['section_id']:''),
				'cc'=>($loginType==1?$userData['cc']:''),
				'cust_ptype'=>($loginType==1?$userData['cust_ptype']:''),
				'cust_pcode'=>($loginType==1?$userData['user_rcode']:''),
				'place_type'=>($loginType==1?'':$userData['place_type']),
				'place_code'=>($loginType==1?'':$userData['place_code']),
				'dept_id'=>($loginType==1?'':$userData['dept_id']),
				'sect_id'=>($loginType==1?'':$userData['sect_id']),
				'jg_id'=>($loginType==1?'':$userData['jg_id'])
		];
		$token=genToken($data);
		if($db->isOk()){
			$arr=["status"=>true,"data"=>$token];
		}else{
			$arr=["status"=>false,"msg"=>$db->getError()];
		}	
	}
	header('Content-type: text/html; charset=UTF-8');
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
}
//echo genToken($data);
//echo date('d/m/Y H:i:s', 1502190010);
//echo "<hr>";
//echo date('d/m/Y H:i:s', 1502182800);
//echo date('H:i:s');
function ckToken($token,$uid){
	JWT::$leeway = 60;
	$key=genKey($uid);
	try{
		$decoded = JWT::decode($token, $key, array('HS256'));
		$data=(Array)$decoded;
		$data=(Array)$data['data'];
		$arr=['status'=>true,'data'=>$data];
		return $arr;
	}catch(UnexpectedValueException $e){
		//echo $e->getMessage();
		return ['status'=>false,'msg'=>$e->getMessage()];
	}
}
function genToken($datax){
	$tokenId = base64_encode(mcrypt_create_iv(32));
	$issuedAt   = time();
	$notBefore  = $issuedAt + 10;  //Adding 10 seconds
	$expire     = $notBefore + ($issuedAt+(60*60*24)); // Adding  1 days
	$serverName = 'http://localhost/php-json/'; /// set your domain name 
	$data = [
		'iat'  => $issuedAt,         // Issued at: time when the token was generated
		'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
		'iss'  => $serverName,       // Issuer
		'nbf'  => $notBefore,        // Not before
		'exp'  => $expire,           // Expire
		'data' => $datax
	];
	
	/**
	 * IMPORTANT:
	 * You must specify supported algorithms for your application. See
	 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
	 * for a list of spec-compliant algorithms.
	 */ 
	$key=genKey($datax['user_id']);
	$jwt = JWT::encode($data, $key);
	return $jwt;
	//$unencodedArray = ['jwt' => $jwt];
    //return "[{'status' : 'success','resp':".json_encode($unencodedArray)."}]";
	//JWT::$leeway = 60;
	//$decoded = JWT::decode($jwt, $key, array('HS256'));
}
function isExpire($exp){
	$now=time();
	return $exp<=$now;
}

?>