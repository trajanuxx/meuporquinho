<?php
/*

 * mbe.ro
 *
 * @author     Ciprian Mocanu <http://www.mbe.ro> <ciprian@mbe.ro>
 * @license    Do whatever you like, just please reference the author
 * @version    1.56
 */
class mysql {
	var $con;
	function __construct($db=array()) {
		$default = array(
		  'host' => HOST_BANCO,
			'user' => USER_BANCO,
			'pass' => SENHA_BANCO,
			'db' => BANCO
		);
		$db = array_merge($default,$db);
		$this->con=mysql_connect($db['host'],$db['user'],$db['pass'],true) or die ('Error connecting to MySQL');
		mysql_select_db($db['db'],$this->con) or die('Database '.$db['db'].' does not exist!');
		mysql_set_charset('utf8');
		mysql_query("SET NAMES 'utf8'");
		mysql_query('SET character_set_connection=utf8');
		mysql_query('SET character_set_client=utf8');
		mysql_query('SET character_set_results=utf8');
		
	}
	function __destruct() {
		mysql_close($this->con);
	}
	function query($s='',$rows=false,$organize=true) {
		if (!$q=mysql_query($s,$this->con)) return false;
		if ($rows!==false) $rows = intval($rows);
		$rez=array(); $count=0;
		$type = $organize ? MYSQL_NUM : MYSQL_ASSOC;
		while (($rows===false || $count<$rows) && $line=mysql_fetch_array($q,$type)) {
			if ($organize) {
				foreach ($line as $field_id => $value) {
					$table = mysql_field_table($q, $field_id);
					if ($table==='') $table=0;
					$field = mysql_field_name($q,$field_id);
					$rez[$count][$table][$field]=$value;
				}
			} else {
				$rez[$count] = $line;
			}
			++$count;
		}
		if (!mysql_free_result($q)) return false;
		return $rez;
	}
        
        function queryL($s){
          //echo($s);
          //exit;
            $result = mysql_query($s,$this->con);
            $array = Array();
            while($row = mysql_fetch_array($result,MYSQL_ASSOC )){
                array_push($array,$row);
            }
           // echo var_dump($array);
            return $array;  
 
        }
        
	function execute($s='') {
		if (mysql_query($s,$this->con)) return true;
		return false;
	}
	function executeQ($s='') {
		return mysql_fetch_array($s,$this->con);
	}
	function select($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1',
			'limit' => '1000000000000'
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']} LIMIT {$options['limit']}";
		//echo $sql;
		return $this->query($sql);
	}
	function row($options) {
		$default = array (
			'table' => '',
			'fields' => '*',
			'condition' => '1',
			'order' => '1'
		);
		$options = array_merge($default,$options);
		$sql = "SELECT {$options['fields']} FROM {$options['table']} WHERE {$options['condition']} ORDER BY {$options['order']}";
		$result = $this->query($sql,1,false);
		if (empty($result[0])) return false;
		return $result[0];
	}
	function get($table=null,$field=null,$conditions='1') {
		if ($table===null || $field===null) return false;
		$result=$this->row(array(
			'table' => $table,
			'condition' => $conditions,
			'fields' => $field
		));
		if (empty($result[$field])) return false;
		return $result[$field];
	}
	function update($table=null,$array_of_values=array(),$conditions='FALSE') {

		if ($table===null || empty($array_of_values)) return false;
		$what_to_set = array();
		foreach ($array_of_values as $field => $value) {
			if (is_array($value) && !empty($value[0])) $what_to_set[]="`$field`='{$value[0]}'";
			else $what_to_set []= "`$field`='".mysql_real_escape_string($value,$this->con)."'";
		}
		$what_to_set_string = implode(',',$what_to_set);
		//echo "UPDATE $table SET $what_to_set_string WHERE $conditions";
		return $this->execute("UPDATE $table SET $what_to_set_string WHERE $conditions");
	}
	function ultimo($table) {
		return $this->query("select * from $table order by id desc limit 1");
	}
	function insert($table=null,$array_of_values=array()) {

		if ($table===null || empty($array_of_values) || !is_array($array_of_values)) return false;
		$fields=array(); $values=array();
		foreach ($array_of_values as $id => $value) {
			$fields[]=$id;
			if (is_array($value) && !empty($value[0])) $values[]=$value[0];
			else $values[]="'".mysql_real_escape_string($value,$this->con)."'";
		}
		$s = "INSERT INTO $table (".implode(',',$fields).') VALUES ('.implode(',',$values).')';
//echo $s;
		if (mysql_query($s,$this->con)) return mysql_insert_id($this->con);
		echo $s;
		return false;
	}
	function delete($table=null,$conditions='FALSE') {
		if ($table===null) return false;
	  // echo "DELETE FROM $table WHERE $conditions";
		return $this->execute("DELETE FROM $table WHERE $conditions");
	}
}
?>
