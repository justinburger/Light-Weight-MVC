<?php
/**
 * Information Abstractions Plugin Communication Layer.
 * This abstract class is used to store any methods that require interaction with the storage engine plugins.
 * 
 * @category InformationAbstraction
 * @package IA
 * @subpackage IAPlugin
 *
 */
abstract class IAPlugin{
	/**
	 * Table Name
	 * @var String
	 */
	protected $name;
	/**
	 * Connection Resource
	 *
	 * @var resource
	 */
	protected $connection;
	/**
	 * Schema Name
	 *
	 * @var String
	 */
	protected $schema;
	/**
	 * Tables within this schema.
	 *
	 * @var Array
	 */
	protected $tables;
	/**
	 * Memory Cache
	 *
	 * @var Boolean
	 */
	protected $memCacheEnabled; 
	
	/**
	 * Set Schema Name
	 *
	 * @param String $name Schema Name
	 */
	public function setSchemaName($name){$this->schema = $name;}
	/**
	 * Set Table Name
	 * 
	 * @param String $name
	 */
	public function setName($name){$this->name = $name;}
	/**
	 * Get Name
	 *
	 * @return String
	 */
	public function getName(){return $this->name;}
	/**
	 * Set Primary Key
	 *
	 * @param String $k
	 */
	public function setKey($k){$this->key = $k;}
	/**
	 * Use MemCache?
	 *
	 * @param Boolean $enabled
	 */
	public function useMemCache($enabled){$this->memCacheEnabled = $enabled;}
	/**
	 * Get Primary Key
	 *
	 * @return String
	 */
	public function getKey(){
		global $settings;
		if(empty($this->key) && !isset($settings['tables'][$this->name]['key'])){
			throw new Exception('You must set the tables primary key via the setKey method, or by setting it in a global $settings var, like: $settings[\'tables\'][\'user\'][\'key\']',002);
		}
		
		if(empty($this->key)){
			$this->key = $settings['tables'][$this->name]['key'];
		}
		
		return $this->key;
	}	
}