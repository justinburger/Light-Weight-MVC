<?php
/**
 * Postgres Storage Plugin for:Information Abstract Framework plugin for lwmvc.
 * @package IAStorage
 * @subpackage pg_storage
 *
 */
class pg_storage extends IAPlugin implements IAStorage{
	
	/**
	 * Get All (or Multiple) Records from the database at once.
	 *
	 * @param Array $columns
	 * @param Array $sortBy
	 * @return Array
	 */
	public function getAll($columns, $sortBy = null){
		$where = null;
		return $this->query('select '.(sizeof($columns) > 0 ? implode(',',$columns): '*').' FROM ' . $this->schema . '.' . $this->getName() . (!is_null($where) ? ' WHERE ' . $where : null) . (!is_null($sortBy) && is_array($sortBy) && sizeof($sortBy) > 0 ? ' ORDER BY ' . implode(',',$sortBy):''));
	}
	/**
	 * Get Single Record
	 *
	 * @param Integer $id
	 * @param Array $columns
	 * @return Array, Or False
	 */
	public function get($id, $columns = array()){
		$id = (int) $id;
		
		global $settings;
		
		
		$q = 'SELECT '.((is_array($columns) && sizeof($columns) > 0) ? implode(',',$columns) : '*') .' FROM ' . $this->schema .'.'. $this->getName() . ' WHERE ' . $this->getKey() . '=$1';

		$result = $this->query($q,array($id));
		
		return (isset($result[0])) ? $result[0] : false;
		
	}
	/**
	 * Add New Record
	 *
	 * @param Array $data
	 * @param Array $columns
	 */
	function add($data, $columns = null){
		$q = "INSERT INTO {$this->name} " . (sizeof($columns) > 0 ? '('.implode(',',$columns).')' : '' ). " VALUES(";
		
		foreach ($columns as $column){
			$i++;
			$q.= '$' . $i . (sizeof($columns) == ($i) ? '':','); 
		}

		
		$q.=")";
		
		
		$this->query($q,$data);
		return true;	
	}
	
	function delete($id){
		global $settings;
		
		$q = "DELETE FROM {$this->name} WHERE " . $settings['tables']['ftdealers']['key'] . '=$1';
		$this->query($q, array($id));
		return true;
	}
	/**
	 * Update Single Record
	 *
	 * @param Integer $id
	 * @param Array $data
	 * @param Array $columns
	 * @return Boolean
	 */
	public function update($id, $data, $columns){
		global $memcache;
		if($this->memCacheEnabled){
			$memcache->flush();
		}
		
		$q = "UPDATE " . $this->schema .'.'. $this->getName() . ' SET ';
		$i=0;
		foreach ($columns as $column){
			$i++;
			$q.= $column . '=$' . $i . (sizeof($columns) == ($i) ? '':','); 
		}
		
		$q.= " WHERE " . $this->getKey() . '= $' . ++$i;
		
		$data[] = $id;
		return $this->query($q,$data, false);
	}
	/**
	 * Get Database resource.
	 *
	 * @return resource
	 */
	private function getConnection(){
		if(!is_resource($this->connection) || pg_connection_status($this->connection) == PGSQL_CONNECTION_BAD){

			$this->connection = $this->connect();
			
			if(!is_resource($this->connection) || pg_connection_status($this->connection) == PGSQL_CONNECTION_BAD){
				throw new Exception('Cannot Make Connection to the Postgres Database.','TABLE_CANNOT_CONNECT');
			}
		}
		return $this->connection;
	}
	/**
	 * Get New Database Connection
	 *
	 * @return resource
	 */
	private function connect(){
			global $settings ;
			
			$dbconn = pg_connect("host={$settings['pg_host']} port={$settings['pg_port']} dbname={$settings['pg_db']} user={$settings['pg_user']} password={$settings['pg_pass']}")
		  						or die ("Cannot Connect to PG:" . pg_last_error($dbconn));
		  
		  			pg_query('SET search_path TO '.$this->schema.',public');
		  			
			return $dbconn;
	}
	/**
	 * Query Datbase
	 *
	 * @param String $sql
	 * @param Array $params
	 * @param Boolean $return
	 * @return Array
	 */
	private function query($sql,$params = array(), $return = true){	
		global $memcache;
		$statementName = (string) md5($sql) . rand(0,(9*100000));
		
		$memCacheStorageName = 'STMT_' . md5($sql . '-' . implode('-',$params));
		
		if($this->memCacheEnabled){
			$results = $memcache->get($memCacheStorageName);
			if(is_array($results) && sizeof($results) > 0){
				return $results;
			}
		}
		
		$r = pg_prepare($this->getConnection(), $statementName, $sql) or die('PREPARE:' . pg_errormessage() . $sql);
		$r = pg_execute($this->getConnection(), $statementName, $params) or die('EXEC:' . $sql . pg_errormessage() . $sql);

		$rows = array();
		if(pg_numrows($r) > 0){
		while($row = pg_fetch_assoc($r)){
			$rows[] = $row;
		}
		}
		
		if($this->memCacheEnabled){
			$memcache->set($memCacheStorageName,$rows,90);		
		}
		return $rows;
	}
	/**
	 * Validate Table Name
	 *
	 * @param String $table_name
	 * @return Boolean
	 */
	public function validateTableName($table_name){
			$dataeRaw = $this->query('select table_name from information_schema.tables WHERE table_schema=$1 ',array($this->schema));	
			foreach ($dataeRaw as $name){
				$this->tables[] = $name['table_name'];
			}
		
		if(!in_array($table_name,$this->tables)){
			return false;
		}else{		
			return true;
		}
		
	}
	
	
}