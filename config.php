<?php
define('SITE_KEY','it[[mepsicczjko,nv5nv');
define('DB','mps_icc');
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','');

function genKey($uid){
	$key=md5(SITE_KEY.$uid);
	return hash('sha256', $key.$_SERVER['REMOTE_ADDR']);
}
function getDB() {
	$dbhost=DBHOST;
	$dbuser=DBUSER;
	$dbpass=DBPASS;
	$dbname=DB;
	$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
}

?>
<?php
class DB{
	private $host = DBHOST;
	private $dbName = DB;
	private $user = DBUSER;
	private $pass = DBPASS;
	private $dbh;
	private $error;
	private $aerror;
	private $query_ok=true;
	private $qError;
	private $stmt;
	private $log="error.log";
	private $title="";
	public function __construct(){
      //dsn for mysql
		
		$dsn = "mysql:host=".$this->host.";dbname=".$this->dbName;
		$options = array(
			PDO::ATTR_PERSISTENT    => true,
			PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
			);
		try{
			$this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
			$this->setname();
		}
		//catch any errors
		catch (PDOException $e){
			$this->error = $e->getMessage();
			$this->aerror[] = $e->getMessage();
		}
	}
	public function title($title){
		$this->title=$title;
	}
	public function log($file){
		$this->log=$file;
	}
	public function wlog($str){
		error_log(date('Y-m-d H:i:s').'-->'.$str."\n",3,$this->log);
	}
	public function hlog($title){
		error_log("########################< START::$title >########################################\n",3,$this->log);
	}
	public function elog($title){
		error_log("########################< END::$title >########################################\n",3,$this->log);
	}
	public function setName($name='utf8'){
		$this->dbh->exec("set names $name");
	}
	public function close(){
		$this->dbg=null;
		$this->error=null;
		$this->aerror=null;
	}
	public function isOk(){
		return $this->query_ok;
	}
	public function getError(){
		return $this->aerror;
	}
	public function query($query){
		$this->stmt = $this->dbh->prepare($query);
	}
	public function sqlexe($query,$param=null){
		try{
			$this->stmt = $this->dbh->prepare($query);
			$this->stmt->execute($param);
			try{
				$rs=$this->stmt->fetchAll(PDO::FETCH_ASSOC);
				$this->stmt->closeCursor();
				return $rs;
			}catch(PDOException $e){ return true;}
		}catch(PDOException $e){
			$this->error=$e->getMessage();
			$this->aerror[]="SQL:".$query." \n\r ".$e->getMessage();
			$this->query_ok=false;
			$this->wlog("SQLERROR");
			$this->wlog("-->SQLCODE::".$query);
			$this->wlog("-->ERROR::".$this->error);
			$this->wlog("#########");
			$this->stmt->closeCursor();
			return false;
		}
	}
	public function insertTable($arr,$tbname){
		foreach($arr as $key=>$value){
			$keyarr[]=$key;
			$valuearr[]="'$value'";
		}
		$str="insert into $tbname(".implode(',',$keyarr).") values(".implode(',',$valuearr).")";
		$this-sqlexe($str);
	}
	
	public function bind($param, $value, $type = null){
		if(is_null($type)){
			switch (true){
				case is_int($value):
					$type = PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = PDO::PARAM_NULL;
					break;
				default:
					$type = PDO::PARAM_STR;
			}
		}
		$this->stmt->bindValue($param, $value, $type);
	}
	public function execute(){
		return $this->stmt->execute();
		$this->qError = $this->dbh->errorInfo();
		if(!is_null($this->qError[2])){
			echo $this->qError[2];
		}
		echo 'done with query';
	}
	public function resultset(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}
  
	public function single(){
		$this->execute();
		return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}
	public function rowCount(){
		return $this->stmt->rowCount();
	}
	public function lastInsertId(){
		return $this->dbh->lastInsertId();
	}
	public function beginTransaction($title=null){
		$this->title($title);
		$this->hlog($this->title);
		return $this->dbh->beginTransaction();
	}
	public function endTransaction(){
		if(!$this->query_ok){
			$this->cancelTransaction();
			$this->wlog('Have Error in transaction ,Now rollback ');
		}else{
			$this->dbh->commit();
			$this->wlog('transaction complete!!!!');
		} 
		//close connection 
		$this->close();
		$this->elog($this->title);
	}
	public function cancelTransaction(){
		return $this->dbh->rollBack();
	}
  
	public function debugDumpParams(){
		return $this->stmt->debugDumpParams();
	}
  
	public function queryError(){
		$this->qError = $this->dbh->errorInfo();
		if(!is_null($qError[2])){
			echo $qError[2];
		}
	}  
}
?>
