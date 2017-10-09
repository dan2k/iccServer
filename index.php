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
$app->post('/close','close');
$app->post('/getWorktype','getWorktype');
$app->post('/getEquipset','getEquipset');
$app->post('/listEquip','listEquip');
$app->post('/listSymptom','listSymptom');
$app->post('/getProblemsub','getProblemsub');
$app->post('/genProblemsub2','genProblemsub2');
$app->post('/genProblemgroup','genProblemgroup');
$app->post('/genProblemsub','genProblemsub');
$app->post('/createSv','createSv');
$app->post('/returnJob','returnJob');
$app->post('/hwEdit','hwEdit');
$app->post('/swEdit','swEdit');
$app->post('/deleteSv','deleteSv');

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
		$sql="select * from ".DB.".cen_province where province_id in(?,?,?)";
		$rs=$db->sqlexe($sql,[65,66,67]);
		$db->sqlexe("update ".DB.".cen_province set section_id=6 where province_id=65");
		$sql="select * from ".DB.".cen_province where section_id=:s1 and province_id in(:s2,:s3,:s4)";
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

function getWorktype(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select 
			wt.work_type_id
			,wt.work_type_desc
		from 
		".DB.".cen_cust_equip e,
		".DB.".cen_work_type wt
		where 
			e.cust_ptype=? 
			and e.cust_pcode=?
			and e.work_type_id <>0 
			and e.work_type_id=wt.work_type_id
		group by e.work_type_id
		order by wt.work_type_id
		";
		$rs= $db->sqlexe($sql,[$custPtype,$custPcode]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}
function getEquipset(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	$workTypeId=$data['work_type_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select 
			s.equip_set_id
			,s.equip_set_desc
		from 
		".DB.".cen_cust_equip e
			,".DB.".cen_contract c
			,".DB.".cen_equip_set s
		where 
			e.cust_ptype=?
			and e.cust_pcode=?
			and e.contract_no=c.contract_no
			and c.contract_no_ext='N'
			and c.contract_status=''
			and e.work_type_id=?
			and e.equip_set_id=s.equip_set_id
		group by  e.equip_set_id
		";
		$rs= $db->sqlexe($sql,[$custPtype,$custPcode,$workTypeId]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}
function listEquip(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	$workTypeId=$data['work_type_id'];
	$equipSetId=$data['equip_set_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();

	
		$sql="
		select 
			e.pno
			,e.sno
			,e.contract_no
			,w.work_type_id
			,w.work_type_desc
			,e.equip_set_id
			,s.equip_set_desc 
			,e.equip_pair_id
			,pno.pic
			,( select g.prob_gid from ".DB.".cen_problem_sub p,".DB.".cen_problem_group g where p.problem_sub_desc=e.pno and p.prob_gid=g.prob_gid limit 1) as prob_gid
			,( select g.prob_gdesc from ".DB.".cen_problem_sub p,".DB.".cen_problem_group g where p.problem_sub_desc=e.pno and p.prob_gid=g.prob_gid limit 1) as prob_gdesc
		from 
			".DB.".cen_cust_equip e
			,".DB.".cen_work_type w
			,".DB.".cen_contract c
			,".DB.".cen_equip_set s
			,".DB.".cen_group_contract g
			,".STOCK.".st_equip pno

		where 
			e.cust_ptype=?
			and e.cust_pcode=?
			and e.work_type_id=?
			and e.equip_set_id=?
			and e.pno=pno.pno
			and e.work_type_id=w.work_type_id
			and e.equip_set_id=s.equip_set_id
			and e.cust_ptype=g.cust_ptype
			and e.cust_pcode=g.cust_pcode
			and e.contract_group=g.contract_group
			and g.contract_group=c.contract_group
			and c.contract_no_ext='N'
			and c.contract_status='';

		order by e.pno;
		";
		$rs= $db->sqlexe($sql,[$custPtype,$custPcode,$workTypeId,$equipSetId]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}		
function listSymptom(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$pno=$data['pno'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select s2.* from ".DB.".cen_problem_sub s,".DB.".cen_problem_sub2 s2 where s.problem_sub_desc=? and s.problem_sub_id=s2.problem_sub_id;
		";
		$rs= $db->sqlexe($sql,[$pno]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}


function getProblemsub(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$pno=$data['pno'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select problem_sub_id,prob_gid from ".DB.".cen_problem_sub  where problem_sub_desc=? 
		";
		$rs= $db->sqlexe($sql,[$pno]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}		
function genProblemsub2(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$problem_sub_id=$data['problem_sub_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select problem_sub2_id,problem_sub2_desc from ".DB.".cen_problem_sub2  where problem_sub_id=? 
		";
		$rs= $db->sqlexe($sql,[$problem_sub_id]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}		
function genProblemgroup(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select prob_gid,prob_gdesc,contract_no from ".DB.".cen_problem_group where problem_type='P2'   
		";
		$rs= $db->sqlexe($sql);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
}		
function genProblemsub(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$prob_gid=$data['prob_gid'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();		
		$sql="
		select problem_sub_id,problem_sub_desc from ".DB.".cen_problem_sub where prob_gid=?   
		";
		$rs= $db->sqlexe($sql,[$prob_gid]);
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
	echo "\n\r\n\r\n\r\n\r\n\r";
	$response = $response->withStatus(201);
    $response = $response->withJson($arr);
    return $response;
	
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
	
}

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
function createSv(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$custPtype=$data['cust_ptype'];
	$custPcode=$data['cust_pcode'];
	
	$msvno=$data['msv_no'];
	$msvUid=$data['msv_uid'];
	$data2=$data['data'];

	//print_r($data2);
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$userData=$ck['data'];
		$custptype=$userData['place_type'];
		$custpcode=$userData['place_code'];
		$db=new DB();
		$db->beginTransaction("บันทึกการแจ้งปัญหา");
		$sql="select comment_adate,comment_atime from ".DB.".mobile_comment where sv_no=? order by comment_no desc limit 1";
		$rs=$db->sqlexe($sql,[$msvno]);
		$svDate=$rs[0]['comment_adate'];
		$svTime=$rs[0]['comment_atime'];
		$sql="select skp_id from ".DB.".sv_trans where sv_no=:svNo and seq=1";	
		$rs=$db->sqlexe($sql,['svNo'=>$msvno]);
		$kpid=typeTokeeper($userData['place_type']);
		$skpid=$rs[0]['skp_id'];
		$rkpid=$rs[0]['skp_id'];
		for($i=0;$i<count($data2);$i++){
			
			if(in_array($userData['place_type'],['P','R'])){
				$rg=substr($userData['sect_id'],1,1);
			}else{
				$rg='0';
			}
			$y=date('Y')+543;
			$yy=substr($y,2,2);
			$pre="RG0${rg}${yy}";
			//$sql="select lpad(if(max(substr(sv_no,7,length(sv_no))) is null,1,max(substr(sv_no,7,length(sv_no)))+1),4,'0') as nox from ".DB.".sv_service where sv_no like '%$pre' ";
			//$sql="select if(max(substr(sv_no,7,length(sv_no))) is null,1,max(substr(sv_no,7,length(sv_no)))+1) as no  from ".DB.".sv_service where sv_no like :pre";
			$sql="select if(max(substr(sv_no,7,length(sv_no))) is null,1,max(substr(sv_no,7,length(sv_no)))+1) as no from sv_service where sv_no like '$pre%'";
			//$rs=$db->sqlexe($sql,['pre'=>$pre]);
			$rs=$db->sqlexe($sql);
			$svno=$pre.sprintf('%04d',$rs[0]['no']);


			$sql="
			INSERT INTO ".DB.".`sv_service` (
				`sv_no`
				, `cust_ptype`
				, `cust_pcode`
				, `problem_type`
				, `work_type_id`
				, `prob_gid`
				, `problem_sub_id`
				, `problem_sub2_id`
				, `sv_date`
				, `sv_time`
				, `user_id`
				, `sv_detail`
				, `sv_resp_date`
				, `sv_resp_time`
				, `sv_resp_emp`
				, `cause`
				, `sv_flag`
				, `draw_flag`
				, `repair_flag`
				, `doc_no`
				, `sv_start_date`
				, `sv_start_time`
				, `sv_solve_detail`
				, `sv_solve_date`
				, `sv_solve_time`
				, `sv_solve_emp`
				, `sv_fin_date`
				, `sv_fin_time`
				, `sv_fin_emp`
				, `contract_no`
				, `status_id`
				, `equip_pair_id`
				, `kp_id`
				, `sv_ip`
				, `sv_sn`
				, `sv_seq`
				, `sv_approve_flag`
				, `sv_satisfaction`
				, `appointments_date`
				, `appointments_time`
				, `sv_assist_emp1`
				, `sv_assist_emp2`
				, `rkp_id`
				, `type_id`
				, `job_type`
				, `msv_no`
				, `msv_status`
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
			";
			$params=[
			    $svno
				, $custPtype
				, $custPcode
				, $data2[$i]['problem_type']
				, $data2[$i]['work_type_id']
				, $data2[$i]['prob_gid']
				, $data2[$i]['problem_sub_id']
				, $data2[$i]['problem_sub2_id']
				, $svDate
				, $svTime
				, $msvUid//`user_id`
				, $data2[$i]['detail']
				, date('Y-m-d')//`sv_resp_date
				, date('H:i:s')//`sv_resp_time`
				, $userId//`sv_resp_emp`
				, ''//`cause`
				, ''//`sv_flag`
				, ''//`draw_flag`
				, ''//`repair_flag`
				, ''//`doc_no`
				, ''//`sv_start_date`
				, ''//`sv_start_time`
				, ''//`sv_solve_detail`
				, ''//`sv_solve_date`
				, ''//`sv_solve_time`
				, ''//`sv_solve_emp`
				, ''//`sv_fin_date`
				, ''//`sv_fin_time`
				, ''//`sv_fin_emp`
				, $data2[$i]['contract_no']//`contract_no`
				, 4//`status_id`
				, ''//`equip_pair_id`
				, $kpid//`kp_id`
				, ''//`sv_ip`
				, $data2[$i]['problem_type']=='P1'?$data2[$i]['sno']:''//`sv_sn`
				, ''//`sv_seq`
				, ''//`sv_approve_flag`
				, ''//`sv_satisfaction`
				, ''//`appointments_date`
				, ''//`appointments_time`
				, ''//`sv_assist_emp1`
				, ''//`sv_assist_emp2`
				, $rkpid//`rkp_id`
				, ''//`type_id`
				, ''//`job_type`
				, $msvno//`msv_no`
				, 4//`msv_status`
			];
			
			$db->sqlexe($sql,$params);
			
			// เริ่มทำการเก็บทราน
			// 1.copy ทรานจาก job ต้นทาง
			$sql="
			insert into ".DB.".sv_trans 
				select :svno,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time from ".DB.".sv_trans where sv_no=:msvno ;
			";
			$rsx=$db->sqlexe($sql,['svno'=>$svno,'msvno'=>$msvno]);
			//echo $sql."\n\r";
			// 2.เพิ่มทราน รับจากต้นทาง
			$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
			$rs=$db->sqlexe($sqlmax,['svNo'=>$svno]);
			$seq=$rs[0]['m']+1;
			$sql=" insert into ".DB.".sv_trans(sv_no,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time)
			values(:F1,:F2,:F3,:F4,:F5,:F6,:F7,:F8,:F9,:F10,:F11,:F12)
					";
			$db->sqlexe($sql,[
					"F1"=>$svno,
					"F2"=>$seq,
					"F3"=>$userId,
					"F4"=>2,
					"F5"=>$kpid,
					"F6"=>$skpid,
					"F7"=>$rkpid,
					"F8"=>$custptype,
					"F9"=>$custpcode,
					"F10"=>4,
					"F11"=>date('Y-m-d'),
					"F12"=>date('H:i:s')
			]);
			//echo $sql;	
		}
		// 3.ทำการปิดทรานต้นทางโดย set msv_status=9
		$sql="select skp_id from ".DB.".sv_trans where sv_no=:svNo and seq=1";	
		$rs=$db->sqlexe($sql,['svNo'=>$msvno]);
		$rpid=typeTokeeper($userData['place_type']);
		$skpid=$rs[0]['skp_id'];
		$kpid=$rs[0]['skp_id'];

		$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
		$rs=$db->sqlexe($sqlmax,['svNo'=>$msvno]);
		$seq=$rs[0]['m']+1;
		$sql=" insert into ".DB.".sv_trans(sv_no,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time)
		values(:F1,:F2,:F3,:F4,:F5,:F6,:F7,:F8,:F9,:F10,:F11,:F12)
				";
		$db->sqlexe($sql,[
				"F1"=>$msvno,
				"F2"=>$seq,
				"F3"=>$userId,
				"F4"=>2,
				"F5"=>$kpid,
				"F6"=>$skpid,
				"F7"=>$rkpid,
				"F8"=>$custptype,
				"F9"=>$custpcode,
				"F10"=>9,
				"F11"=>date('Y-m-d'),
				"F12"=>date('H:i:s')
		]);
		$sql="update ".DB.".mobile_sv set msv_status=9,msv_udate=now(),msv_utime=now(),msv_updid=:userId where msv_no=:msvno";
		$db->sqlexe($sql,['userId'=>$userId,'msvno'=>$msvno]);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"สร้าง service เรียบร้อยแล้ว้",
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
		select if(max(substr(msv_no,8,7)) is null,1,max(substr(msv_no,8,7))+1) as no from ".DB.".mobile_sv where msv_no like :prekey 
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
	$userType=$data['user_type'];
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
		values(?,?,?,?,?,?,?,now(),now())";
		$userData=$ck['data'];
		$userType=$userData['user_type'];
		$custptype=$userType==1?$userData['cust_ptype']:$userData['place_type'];
		$custpcode=$userType==1?$userData['cust_pcode']:$userData['place_code'];
		$arr=[
			$msvNo,
			$no,
			$userId,
			$userType,
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
function hwEdit(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$db->beginTransaction("แก้ไข job hw");
		$sql="select problem_sub_id,prob_gid from ".DB.".cen_problem_sub where problem_sub_desc=?";
		$rs=$db->sqlexe($sql,[$data['problem_sub_desc']]);
		$problem_sub_id=$rs[0]['problem_sub_id'];
		$prob_gid=$rs[0]['prob_gid'];
		$userData=$ck['data'];
		$sql="update ".DB.".sv_service  set
				work_type_id=?,
				prob_gid=?,
				problem_sub_id=?,
				problem_sub2_id=?,
				sv_detail=?,
				sv_sn=?,
				contract_no=?
			  where 
				  sv_no=?
				  	
		";
		$arr=[
			$data['work_type_id'],
			$prob_gid,
			$problem_sub_id,
			$data['problem_sub2_id'],
			$data['detail'],
			$data['sno'],
			$data['contract_no'],
			$data['sv_no'],
		];
		$db->sqlexe($sql,$arr);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"update  Ok",
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
function swEdit(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$db->beginTransaction("แก้ไข job sw");
		$userData=$ck['data'];
		$sql="update ".DB.".sv_service  set
				prob_gid=?,
				problem_sub_id=?,
				problem_sub2_id=?,
				sv_detail=?,
				contract_no=?
			  where 
				  sv_no=?
				  	
		";
		$arr=[
			$data['prob_gid'],
			$data['problem_sub_id'],
			$data['problem_sub2_id'],
			$data['detail'],
			$data['contract_no'],
			$data['sv_no'],
		];
		$db->sqlexe($sql,$arr);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"update  Ok",
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
function deleteSv(Request $request, Response $response){
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$db=new DB();
		$db->beginTransaction("แก้ไข job sw");
		$userData=$ck['data'];
		$sql="delete from ".DB.".sv_service where sv_no=?";
		$db->sqlexe($sql,[$data['sv_no']]);
		$sql="delete from ".DB.".sv_trans where sv_no=?";
		$db->sqlexe($sql,[$data['sv_no']]);

		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"delete  Ok",
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
function jobTokeeper($job_id){
	switch($job_id){
		case 3:$kpid=4;break;//ศภ
		case 4:$kpid=6;break;//สก
		case 5:$kpid=9;break;//ศลก
		case 8:$kpid=10;break;//ห้างสรรพสินค้า
		default : $kpid=$job_id;break;
	}
	return $kpid;
}
function typeTokeeper($type){
	switch($type){
		case 'P':$kpid=3;break;
		case 'R':$kpid=5;break;
		default :$kpid=7;break;
	}
	return $kpid;
}
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
		$rs=$db->sqlexe($sql,['svNo'=>$msvNo]);
		$skpid=$rs[0]['skp_id'];
		$kpid=$skpid;//กลับสู่ผู้เปิด
		$rkpid=jobTokeeper($user['job_id']);
		$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
		$rs=$db->sqlexe($sqlmax,['svNo'=>$msvNo]);
		$seq=$rs[0]['m']+1;
		
		$custptype=$isMoi=='M'?$user['cust_ptype']:$user['place_type'];
		$custpcode=$isMoi=='M'?$user['cust_pcode']:$user['place_code'];
		$params['msvNo']=$msvNo;
		$params['userId']=$userId;
		if($isMoi=='M'){
			$userType=1;
			$sql="update ".DB.".mobile_sv set
						msv_status='8',
						msv_udate=now(),
						msv_utime=now(),
						msv_updid=:userId
					where 
						msv_no=:msvNo	
					";
			$db->sqlexe($sql,$params);
		}else{
			$userType=2;
			$sql="update ".DB.".sv_service set
						msv_status='8',
						status_id='8',
						kp_id=:kpid,
						rkp_id=:rkpid,
						sv_fin_date=now(),
						sv_fin_time=now(),
						sv_fin_emp=:userId,
						sv_satisfaction=:rate
					where 
						sv_no=:msvNo
					
			";
			$params['kpid']=$kpid;
			$params['rkpid']=$rkpid;
			$params['rate']=$data['rate'];
			$db->sqlexe($sql,$params);
		}
		
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
				"F10"=>8,
				"F11"=>date('Y-m-d'),
				"F12"=>date('H:i:s')
		]);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"close Job Ok"
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

function close(Request $request, Response $response){//ตอนที่ cdg ปิดจ๊อบ หรือ  ศจ.ปิดจ๊อบ
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$msvNo=$data['msv_no'];
	$solve=$data['solve'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$user=$ck['data'];
		$rate='';
		$db=new DB();
		$db->beginTransaction('เริ่มทำ ยืนยันการปิดงาน');
		$isMoi=substr($msvNo,0,1);	
		
		$sql="select skp_id,rkp_id from ".DB.".sv_trans where sv_no=:svNo and seq=1";	
		$rs=$db->sqlexe($sql,['svNo'=>$msvNo]);
		$skpid=$rs[0]['skp_id'];
		$kpid=$skpid;
		
		$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
		$rs=$db->sqlexe($sqlmax,['svNo'=>$msvNo]);
		$seq=$rs[0]['m']+1;
		
		$custptype=$isMoi=='M'?$user['cust_ptype']:$user['place_type'];
		$custpcode=$isMoi=='M'?$user['cust_pcode']:$user['place_code'];
		$params['msvNo']=$msvNo;
		$params['userId']=$userId;
		$params['solve']=$solve;
		if($isMoi=='M'){
			//สลับระหว่าง cen_job กับ cen_keeper  โดยใช้ job_id เทียบ
			$rkpid=jobTokeeper($user['job_id']);
			$userType=1;
			$sql="update ".DB.".mobile_sv set
						msv_status='6',
						msv_solve=:solve,
						msv_udate=now(),
						msv_utime=now(),
						msv_updid=:userId
					where 
						msv_no=:msvNo	
			";
			$db->sqlexe($sql,$params);
		}else{
			$userType=2;
			$rkpid=typeTokeeper($user['place_type']);
			$sql="update ".DB.".sv_service set
						kp_id=:kpid,
						rkp_id=:rkpid,
						status_id='6',
						msv_status='6',
						sv_solve_detail=:solve,
						sv_solve_date=now(),
						sv_solve_time=now(),
						sv_solve_emp=:userId,
					where 
						sv_no=:msvNo
					
			";
			$params['kpid']=$kpid;
			$params['rkpid']=$rkpid;
			$db->sqlexe($sql,$params);
		}
		
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
				"F10"=>6,
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
function returnJob(Request $request, Response $response){//ตอนที่ cdg ปิดจ๊อบ หรือ  ศจ.ปิดจ๊อบ
	$headers=$request->getHeader('x-access-token');
	$token=$headers[0];
	$data=$request->getParam('data');
	$userId=$data['user_id'];
	$svNo=$data['svno'];
	$ck=ckToken($token,$userId);
	if($ck['status']){
		$user=$ck['data'];
		$db=new DB();
		$db->beginTransaction('เริ่มทำ ยืนยันการปิดงาน');
		$isMoi=substr($svNo,0,1);	


		$sql="select kp_id,skp_id,rkp_id from ".DB.".sv_trans where sv_no=:svNo order by seq desc limit 1";	
		$rs=$db->sqlexe($sql,['svNo'=>$svNo]);
		$skpid=$rs[0]['skp_id'];
		$kpid=$rs[0]['rkp_id'];
		$rkpid=$rs[0]['kp_id'];

		
		$sqlmax="select max(seq) as m,skp_id from ".DB.".sv_trans where sv_no=:svNo";
		$rs=$db->sqlexe($sqlmax,['svNo'=>$svNo]);
		$seq=$rs[0]['m']+1;
		
		$custptype=$isMoi=='M'?$user['cust_ptype']:$user['place_type'];
		$custpcode=$isMoi=='M'?$user['cust_pcode']:$user['place_code'];
		$params['msvNo']=$svNo;
		$params['userId']=$userId;

		if($isMoi=='M'){
			$userType=1;
			$sql="update ".DB.".mobile_sv set
						msv_status='6',
						msv_udate=now(),
						msv_utime=now(),
						msv_updid=:userId
					where 
						msv_no=:msvNo	
			";
			$db->sqlexe($sql,$params);
		}else{
			$userType=2;
			$rkpid=typeTokeeper($user['place_type']);
			$sql="update ".DB.".sv_service set
						kp_id=:kpid,
						rkp_id=:rkpid,
						status_id='6',
						msv_status='6',
						sv_solve_detail=:solve,
						sv_solve_date=now(),
						sv_solve_time=now(),
						sv_solve_emp=:userId,
					where 
						sv_no=:msvNo
					
			";
			$params['kpid']=$kpid;
			$params['rkpid']=$rkpid;
			$db->sqlexe($sql,$params);
		}
		
		$sql=" insert into ".DB.".sv_trans(sv_no,seq,user_id,user_type,kp_id,skp_id,rkp_id,cust_ptype,cust_pcode,status_id,upd_date,upd_time)
					values(:F1,:F2,:F3,:F4,:F5,:F6,:F7,:F8,:F9,:F10,:F11,:F12)
				";
		$db->sqlexe($sql,[
				"F1"=>$svNo,
				"F2"=>$seq,
				"F3"=>$userId,
				"F4"=>$userType,
				"F5"=>$kpid,
				"F6"=>$skpid,
				"F7"=>$rkpid,
				"F8"=>$custptype,
				"F9"=>$custpcode,
				"F10"=>6,
				"F11"=>date('Y-m-d'),
				"F12"=>date('H:i:s')
		]);
		if($db->isOk()){
			$arr=[
				"status"=>true,
				"msg"=>"return Job Ok",
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
					,(select cust_pdesc from ".DB.".cen_cust_place where cust_ptype=sv.cust_ptype and cust_pcode=sv.cust_pcode) as cust_pdesc
					,(select cust_desc from ".DB.".cen_type_custptype where cust_ptype=sv.cust_ptype) as cust_ptype_desc
					,pp.province_name
					from 
						".DB.".mobile_sv sv,
						".DB.".cen_cust_place c,
						".DB.".cen_province pp 
			where 
				sv.msv_no=:msvNo
				and sv.cust_ptype=c.cust_ptype
				and sv.cust_pcode=c.cust_pcode
				and c.cc=pp.province_id";
			
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
				,(select status_desc from ".DB.".cen_status where sv.msv_status=status_id) as msv_status_desc
				,(select k.kp_desc from ".DB.".cen_keeper k,".DB.".sv_trans t where k.kp_id=t.kp_id and t.sv_no=sv.msv_no and t.status_id=sv.msv_status order by seq desc limit 1) as kp_desc
				,(select kp_id from ".DB.".sv_trans where sv_no=sv.msv_no order by seq desc limit 1) as  kp_idx
				,(select skp_id from ".DB.".sv_trans where sv_no=sv.msv_no order by seq desc limit 1) as  skp_idx
				,(select cust_pdesc from ".DB.".cen_cust_place where cust_ptype=sv.cust_ptype and cust_pcode=sv.cust_pcode) as cust_pdesc
				,(select cust_desc from ".DB.".cen_type_custptype where cust_ptype=sv.cust_ptype) as cust_ptype_desc
				,pp.province_name
				,case sv.msv_no
					when substr(sv.msv_no,1,2)='RG' then true
					else false
				end as isRG
				from 
					".DB.".mobile_sv sv,
					".DB.".cen_user u,
					".DB.".cen_cust_place p,
					".DB.".cen_province pp
				where
					sv.msv_status in(0,4,6)
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
				//echo $sql;
		$params=null;
		if($custPtype!=''){$params['custPtype']=$custPtype;}
		if($custPcode!=''){$params['custPcode']=$custPcode;}
		if($cScope!=''){$params['scope']=$scope;}

		$rs=$db->sqlexe($sql,$params);	

		$sql="select 
				sv.sv_no as msv_no
				,case sv.problem_type
					when 'P1' then 1
					when 'P2' then 2
					when 'P3' then 3
				 end as msv_type
				 ,(select concat(user_fname,' ',user_lname) from cen_user  where  user_id=sv.user_id and cust_ptype=sv.cust_ptype and user_rcode=sv.cust_pcode) as thiname
				 ,sv.sv_date as msv_adate
				 ,sv.sv_time as msv_atime
				 ,sv.sv_detail as msv_detail
				 ,sv.cust_ptype
				 ,sv.cust_pcode
				 ,sv.msv_no as msv_no2
				 ,sv.msv_status
				 ,sv.contract_no
				 ,(select status_desc from ".DB.".cen_status where sv.msv_status=status_id) as msv_status_desc
				 ,(select k.kp_desc from ".DB.".cen_keeper k,".DB.".sv_trans t where k.kp_id=t.kp_id and t.sv_no=sv.sv_no and t.status_id=sv.status_id) as kp_desc
				 ,(select kp_id from ".DB.".sv_trans where sv_no=sv.sv_no order by seq desc limit 1) as  kp_idx
				 ,(select skp_id from ".DB.".sv_trans where sv_no=sv.sv_no order by seq desc limit 1) as  skp_idx
				 ,(select cust_pdesc from ".DB.".cen_cust_place where cust_ptype=sv.cust_ptype and cust_pcode=sv.cust_pcode) as cust_pdesc
				 ,(select cust_desc from ".DB.".cen_type_custptype where cust_ptype=sv.cust_ptype) as cust_ptype_desc
				 ,pp.province_name
				 ,case sv.sv_no
					when substr(sv.sv_no,1,2)='RG' then true
					else false
				 end as isRG
				 ,sv.sv_sn
				 ,sv.work_type_id
				 ,(select work_type_desc from ".DB.".cen_work_type where work_type_id=sv.work_type_id) as work_type_desc
				 #,sv.equip_set_id
				 #,(select equip_set_desc from ".DB.".cen_equip_set where equip_set_id=sv.equip_set_id) as equip_set_desc
				 ,sv.prob_gid
				 ,(select prob_gdesc from ".DB.".cen_problem_group where prob_gid=sv.prob_gid) as prob_gdesc
				 ,sv.problem_sub_id
				 ,(select problem_sub_desc from ".DB.".cen_problem_sub where problem_sub_id=sv.problem_sub_id) as problem_sub_desc
				 ,sv.problem_sub2_id
				 ,(select problem_sub2_desc from ".DB.".cen_problem_sub2 where problem_sub2_id=sv.problem_sub2_id) as problem_sub2_desc
				 ,(select e.equip_set_id from ".DB.".cen_cust_equip e,".DB.".cen_problem_sub ps where e.cust_ptype=sv.cust_ptype and e.cust_pcode=sv.cust_pcode and e.sno=sv.sv_sn and e.pno=ps.problem_sub_desc and ps.problem_sub_id=sv.problem_sub_id  ) as equip_set_id
				 ,(select s.equip_set_desc from ".DB.".cen_equip_set s,".DB.".cen_cust_equip e,".DB.".cen_problem_sub ps where e.cust_ptype=sv.cust_ptype and e.cust_pcode=sv.cust_pcode and e.sno=sv.sv_sn and e.pno=ps.problem_sub_desc and ps.problem_sub_id=sv.problem_sub_id and e.equip_set_id=s.equip_set_id ) as equip_set_desc
				 ,(select e.pno from ".DB.".cen_cust_equip e,".DB.".cen_problem_sub ps  where e.pno=ps.problem_sub_desc and ps.problem_sub_id=sv.problem_sub_id and  e.cust_ptype=sv.cust_ptype and e.cust_pcode=sv.cust_pcode and e.sno=sv.sv_sn) as pno
			from 
				".DB.".sv_service sv,
				".DB.".cen_cust_place p,
				".DB.".cen_province pp
			where
				sv.msv_status in(0,6,4)
				$cptype
				$cpcode
				and sv.cust_ptype=p.cust_ptype
				and sv.cust_pcode=p.cust_pcode
				and pp.province_id=p.cc
				$cScope
		";
		$rs1=$db->sqlexe($sql,$params);
		
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
			$rs=$db->sqlexe($sql,['msvNo'=>$msvNo]);
			$svno=$rs[0]['msv_no'];
		}else{
			$svno=$msvNo;
		}
		$sql="
			select 
					c.*,
					case c.comment_utype
						when 1 then (select concat(u.user_fname,' ',u.user_lname) from ".DB.".cen_user u where c.comment_uid=u.user_id and c.comment_custptype=u.cust_ptype and c.comment_custpcode=u.user_rcode) 
						when 2 then (select concat(e.emp_fname,' ',e.emp_lname) from ".DB.".cen_emp e where  e.emp_id=c.comment_uid )
					end as thiname
				from 
					".DB.".mobile_comment c
				where 
				c.sv_no=:svNo
				";
		$rs=$db->sqlexe($sql,['svNo'=>$msvNo]);
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
				,a2.cust_pdesc
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
		//$userData=$userData[0];
		$data=[];
		$i=0;
		foreach($userData as $k=>$v){
			$data[$i]=[
				'user_id'=>($loginType==1?$v['user_id']:$v['emp_id']),
				'user_title'=>($loginType==1?$v['user_title']:$v['emp_title']),
				'user_fname'=>($loginType==1?$v['user_fname']:$v['emp_fname']),
				'user_lname'=>($loginType==1?$v['user_lname']:$v['emp_lname']),
				'user_type'=>$loginType,
				'job_id'=>($loginType==1?$v['job_id']:''),
				'section_id'=>($loginType==1?$v['section_id']:''),
				'cc'=>($loginType==1?$v['cc']:''),
				'cust_ptype'=>($loginType==1?$v['cust_ptype']:''),
				'cust_pcode'=>($loginType==1?$v['user_rcode']:''),
				'cust_pdesc'=>($loginType==1?$v['cust_pdesc']:''),
				'place_type'=>($loginType==1?'':$v['place_type']),
				'place_code'=>($loginType==1?'':$v['place_code']),
				'dept_id'=>($loginType==1?'':$v['dept_id']),
				'sect_id'=>($loginType==1?'':$v['sect_id']),
				'jg_id'=>($loginType==1?'':$v['jg_id'])
			];
			$i++;
		}
		
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
	$expire     = $notBefore +(1*60*60*24); // Adding  1 days
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
	$key=genKey($datax[0]['user_id']);
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