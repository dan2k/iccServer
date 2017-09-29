<?php
require 'config.php';
$db=new db();
//$sql="select * from  cen_province where province_id in(?,?,?)";
//$param=[65,66,67];
//$rs=$db->exe($sql,$param);
/*
$db->title('เริ่มทดสอบการทำทรานเซ็คชั่น');
$db->beginTransaction();
$sql="update cen_province set section_id=00 where province_id=65";
$rs=$db->sqlexe($sql);

$rs=$db->sqlexe($sql);
echo json_encode($rs);
$db->endTransaction();
*/
$sql="select * from cen_province ";
$rs=$db->sqlexe($sql);
$db->close();
echo $rs[0]['province_name'];






?>