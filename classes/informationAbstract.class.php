<?php
/**
 * Information Abstract
 * Class to serve as a basic, abstract method of interacting with stored data. In it's current iteration, it's
 * extending a postgres table class, which allows it to store it's data in postgres. But this class was built to eventually
 * allow dynamic selection of the parent storage class.
 */
class informationAbstract{
	/** @var Table Name */
	protected $name;
	protected $key;
	protected $validated;
	
	public function __construct($name){
		if(!$this->validateName($name)){
			$this->validated = false;
			throw new Exception('Table Name: ' . $name . ' is not a valid, or accessible postgres table name. ',001);
		}else{
			$this->setName($name);
			$this->validated = true;
		}
		
		return $this->validated;
	}

	/** @var Database Connection Resource. */
	private $connection;
	/** TAPlugin Object. */
	private $plugin;
	

	/** @var Database Schema Name*/
	protected $schema;
	
	/**
	 * Validate Name
	 * Validate the name of the table we are attempting to create an object based off.
	 * 
	 * @param String $name
	 * @return Boolean
	 */
	public function validateName($name){
		$plugin = $this->getPlugin($name);
		return $plugin->validateTableName($name);
	}
		
	/**
	 * Get Plugin
	 * Load, and return an object of the IA plugin class.
	 *
	 * @return Object IAObject.
	 */
	private function getPlugin($name){
		if(!is_object($this->plugin)){
			global $lwmvc, $settings;
			$se = $lwmvc->getStorageEngine();
			$pluginFilename = $lwmvc->getFrameworkDir() . 'classes/plugins/storage/' . $se . '_storage.class.php';
			require_once($pluginFilename);
			$pluginClassName = $se . '_storage';
			$plugin = new $pluginClassName();
			if(isset($settings['tables'][$name]['schema'])){
				$plugin->setSchemaName($settings['tables'][$name]['schema']);
			}else{
				$plugin->setSchemaName($settings['pg_schema']);
			}
			
			$plugin->setName($this->name);
			$this->plugin = $plugin;
		}
		
		return $this->plugin;
	}
		
	/**
	 * Set Name
	 * set the name of database table we are going to objectify.
	 *
	 * @param String $name Database Table Name.
	 * @return Boolean
	 */
	public function setName($name){
		$this->name = $name;
		$plugin = $this->getPlugin();
		$plugin->setName($name);
		return true;
	}
	
	/**
	 * Set Primary Key
	 * set the name of the column that we should use as the primary key.
	 *
	 * @param String $k Primary Key.
	 * @return Boolean
	 */
	public function setKey($k){
		$this->key = $k;
		$plugin = $this->getPlugin();
		$plugin->setKey($k);
		return true;
	}
	
	/**
	 * Get Key
	 *
	 * @return boolean
	 */
	public function getKey(){
		$plugin = $this->getPlugin();
		return $plugin->getKey();
	}
	
	/**
	 * Get
	 *
	 * @param Integer $id
	 * @param Array $columns
	 * @return Array
	 */
	public function get($id, $columns = array()){
		$plugin = $this->getPlugin();
		return $plugin->get($id, $columns);
	}
	
	/**
	 * Add Row
	 *
	 * @param unknown_type $data
	 * @param unknown_type $columns
	 */
	public function add($data, $columns = null){
		$plugin = $this->getPlugin();
		return $plugin->add($data, $columns);
	}
	
	/**
	 * Update Row
	 *
	 * @param Integer $id
	 * @param Array $data
	 * @param Array $columns
	 * @return String
	 */
	public function update($id, $data, $columns){
		$plugin = $this->getPlugin();
		return $plugin->update($id, $data, $columns);
	}
	
	/**
	 * Delete Record
	 *
	 * @param Integer $id
	 */
	public function delete($id){
		$plugin = $this->getPlugin();
		return $plugin->delete($id);
	}
	
	/**
	 * Refresh Object from database.
	 *
	 */
	public function refresh(){}
	
	/**
	 * Get Table Name
	 *
	 * @return String
	 */
	public function getName(){
		return $this->name;
	}
	
	/**
	 * Get All Rows
	 *
	 * @param Array $columns
	 * @param Array $sortBy
	 * @return Array
	 */
	public function getAll($columns, $sortBy = null){
		$plugin = $this->getPlugin();
		return $plugin->getAll($columns, $sortBy);
	}
	
	/**
	 * Use Mem Cache?
	 *
	 * @param boolean $enabled
	 */
	public function useMemCache($enabled){
		$plugin = $this->getPlugin();
		$plugin->useMemCache($enabled);
	}
	
	
}


