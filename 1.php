<?php
require 'vendor/autoload.php';
use \Firebase\JWT\JWT;
$t=1506486949 ;
echo "nbf = ".date('d-m-Y H:i:s',$t);
echo "<br>";
$t=1506497739;
echo "exp = ".date('d-m-Y H:i:s',($t));
echo "<br>";
$t=1506486949 ;
echo "nbf = ".date('d-m-Y H:i:s',$t);


$tokenId = base64_encode(mcrypt_create_iv(32));
$issuedAt   = time();
$notBefore  = $issuedAt + 10;  //Adding 10 seconds
$expire     = $notBefore+(60*60*24); // Adding  1 days
$serverName = 'http://localhost/php-json/'; /// set your domain name 

$data = [
    'iat'  => $issuedAt,         // Issued at: time when the token was generated
    'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
    'iss'  => $serverName,       // Issuer
    'nbf'  => $notBefore,        // Not before
    'exp'  => $expire,           // Expire
    'data' => 'xxxxxxxxxxxx',
];

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */

JWT::$leeway=10;
$key='1234';
$jwt = JWT::encode($data, $key);
echo "<br>";
echo $jwt;
echo "<hr>";
#######################################
echo "<br>";
echo date('Ymd H:i:s',$issuedAt)."---".date('Ymd H:i:s',$notBefore)."---".date('Ymd H:i:s',$expire);
echo "<br>";
$key='123';
try{
    #JWT::$leeway = 60;
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $data=(Array)$decoded;
    // $data=(Array)$data['data'];
    $arr=['status'=>true,'data'=>$data];
}catch(UnexpectedValueException $e){
    //echo $e->getMessage();
    $arr=['status'=>false,'msg'=>$e->getMessage()];
    #print_r($e);
    //print_r($arr);
}
 print_r($arr);


?>