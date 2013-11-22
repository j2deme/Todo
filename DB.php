<?php
/**
* DB
*/
class DB {
  private $user;
  private $pass;
  private $host;
  private $dbname;
  private $dbh;
  private $result;
  private $st;

  public function __construct($user, $pass, $host, $dbname){
    $this->user = $user;
    $this->pass = $pass;
    $this->host = $host;
    $this->dbname = $dbname;
    try {
      $this->connect();
    } catch (PDOException $e) {
      return $e->getMessage();
    }
  }

  function __destruct(){
    $this->disconnect();
  }

  private function connect(){
    $this->dbh = new PDO("mysql:host=".$this->host.";dbname=".$this->dbname."", $this->user, $this->pass);
    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $this->dbh->setAttribute(PDO::ATTR_PERSISTENT, true);
    $this->dbh->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
  }

  private function disconnect(){
    $this->dbh = null;
  }

  public function begin(){
    return $this->dbh->beginTransaction();
  }

  public function end(){
    return $this->dbh->commit();
  }

  public function cancel(){
    return $this->dbh->rollBack();
  }

  public function find($table, $data = array()){
    $sql = "SELECT * FROM $table WHERE ";
    $attrs = array();
    foreach ($data as $col => $value) {
      $attrs[] = "$col=:$col";
    }
    if(empty($attrs)){
      $sql .= "1";
    } else {
      for ($i=0; $i < count($attrs); $i++) {
        if($i == (count($attrs) - 1)){
          $sql .= $attrs[$i]." ";
        } else {
          $sql .= $attrs[$i]." AND ";
        }
      }
    }

    $this->st = $this->dbh->prepare($sql);
    $this->st->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($data as $key => $value) {
      $this->bind($key,$value);
    }
    $this->st->execute();
    $this->result = $this->st->fetch();
    return $this->utf8_decode_mix($this->result);
  }

  public function findAll($table, $data = array()){
    $sql = "SELECT * FROM $table WHERE ";
    $attrs = array();
    foreach ($data as $col => $value) {
      $attrs[] = "$col=:$col";
    }
    if(empty($attrs)){
      $sql .= "1";
    } else {
      for ($i=0; $i < count($attrs); $i++) {
        if($i == (count($attrs) - 1)){
          $sql .= $attrs[$i]." ";
        } else {
          $sql .= $attrs[$i]." AND ";
        }
      }
    }

    $this->st = $this->dbh->prepare($sql);
    $this->st->setFetchMode(PDO::FETCH_ASSOC);
    foreach ($data as $key => $value) {
      $this->bind($key,$value);
    }
    $this->st->execute();
    $this->result = $this->st->FetchAll();
    $this->disconnect();
    return $this->utf8_decode_mix($this->result);
  }

  public function save($table, $data = array()){
    $sql = "";
    if(!empty($data)){
      if(isset($data['id'])){
        $sql .= "UPDATE $table SET ";
        $attrs = array();
        foreach ($data as $col => $value) {
          if($col != 'id'){
            $attrs[] = "$col=:$col";
          }
        }
        for ($i=0; $i < count($attrs); $i++) {
          if($i == (count($attrs) - 1)){
            $sql .= $attrs[$i]." ";
          } else {
            $sql .= $attrs[$i].", ";
          }
        }
        $sql .= "WHERE id=:id";
      } else {
        $sql .= "INSERT INTO $table (";
        $attrs = array();
        foreach ($data as $col => $value) {
            $attrs[] = $col;
        }
        for ($i=0; $i < count($attrs); $i++) {
          if($i == (count($attrs) - 1)){
            $sql .= $attrs[$i]."";
          } else {
            $sql .= $attrs[$i].",";
          }
        }
        $sql .= ") VALUES (";
        for ($i=0; $i < count($attrs); $i++) {
          if($i == (count($attrs) - 1)){
            $sql .= ":".$attrs[$i]."";
          } else {
            $sql .= ":".$attrs[$i].",";
          }
        }
        $sql .= ")";
      }

      $this->st = $this->dbh->prepare($sql);
      foreach ($data as $key => $value) {
        $this->bind($key,$value);
      }
      $this->result = $this->st->execute();
      $this->disconnect();
      return $this->result;
    } else {
      return false;
    }
  }

  public function delete($table, $data = array()){
    $sql = "DELETE FROM $table WHERE ";
    $attrs = array();
    foreach ($data as $col => $value) {
      $attrs[] = "$col=:$col";
    }

    if(empty($attrs)){
      $sql .= "1";
    } else {
      for ($i=0; $i < count($attrs); $i++) {
        if($i == (count($attrs) - 1)){
          $sql .= $attrs[$i]." ";
        } else {
          $sql .= $attrs[$i]." AND ";
        }
      }
    }
    $this->st = $this->dbh->prepare($sql);
    foreach ($data as $key => $value) {
      $this->bind($key,$value);
    }
    $this->result = $this->st->execute();
    $this->disconnect();
    return $this->result;
  }

  public function count(){
    return $this->st->rowCount();
  }

  public function lastId(){
    return $this->dbh->lastInsertId();
  }

  public function sql($sql, $data = array()){
    $this->st = $this->dbh->prepare($sql);
    foreach ($data as $key => $value) {
      $this->bind($key,$value);
    }
    $this->result = $this->st->execute();
    $this->disconnect();
    if(strpos(strtolower($sql),"select ") !== false ){
      if($this->count() > 1){
        $this->result = $this->st->FetchAll();
      } else {
        $this->result = $this->st->fetch();
      }
    }
    return $this->utf8_decode_mix($this->result);
  }

  private function bind($param, $value, $type = null){
    if (is_null($type)) {
      switch (true) {
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
    if($type == PDO::PARAM_STR){
      $this->st->bindValue($param, utf8_encode($value), $type);
    } else {
      $this->st->bindValue($param, $value, $type);
    }
  }

  public function debug(){
    return $this->st->debugDumpParams();
  }

  public function create($name, $cols = array()){
    $sql = "CREATE TABLE IF NOT EXISTS $name (";
    if (empty($cols)) {
      $cols[] = array('id' => 'int NOT NULL PRIMARY KEY AUTO_INCREMENT');
    }
    $temp = array_keys($cols);
    $last_key = end($temp);
    foreach ($cols as $key => $value) {
      if($key == $last_key){
        $sql .= "$key $value";
      } else {
        $sql .= "$key $value, ";
      }
    }
    $sql .= ") CHARACTER SET utf8 COLLATE utf8_general_ci";
    return $this->sql($sql);
  }

  public function drop($name){
    if(!empty($name)){
      return $this->sql("DROP TABLE IF EXISTS $name");
    } else {
      return false;
    }
  }

  public function truncate($table){
    if(!empty($name)){
      return $this->sql("TRUNCATE TABLE $table");
    } else {
      return false;
    }
  }

  public function pretty($arr = array()){
    echo "<pre>";
    if(!empty($arr)){
      print_r($arr);
    } else {
      print_r($this->result);
    }
    echo "</pre>";
  }

  private function utf8_decode_mix($input, $decode_keys = false){
    if(is_array($input)){
        $result = array();
        foreach($input as $k => $v){
          $key = ($decode_keys)? utf8_decode($k) : $k;
          $result[$key] = $this->utf8_decode_mix( $v, $decode_keys);
        }
    } else {
      $result = utf8_decode($input);
    }
    return $result;
  }

  public function toJson(){
    $rows = $this->result;
    if(empty($this->result)){
      return json_encode(array(), JSON_NUMERIC_CHECK);
    } else {
      if(is_array($this->result) && !isset($this->result['id'])){
        foreach ($this->result as $row) {
          foreach ($row as $key => $value) {
            if(is_string($value)){
              $row[$key] = json_encode($value);
            } else {
              $row[$key] = $value;
            }
          }
        }
      }
      return json_encode($rows, JSON_NUMERIC_CHECK);
    }
  }

  public function getJson($url, $use_curl = true){
    if ($use_curl) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      $feed = curl_exec($ch);
      curl_close($ch);
    } else {
      $feed = @file_get_contents($url);
    }
    return $feed;
  }

  public function toArray($string){
    return json_decode(utf8_decode($string),true);
  }
}
?>
