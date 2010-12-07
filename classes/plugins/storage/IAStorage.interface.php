<?php
/**
 * Information Abstraction Storage Plugin Interface.
 * All plugins must implement the following interface in order to 
 * propperly hook into the lwmvc storage abstraction system.
 *
 */
interface IAStorage{
	/**
	 * Validate Table Name
	 * should be implemented to verify the name passed as the "table" name.
	 *
	 * @param String $name Table Name.
	 */
	function validateTableName($name);
	/**
	 * Set Schema Name
	 * should be implemented to store schema name.
	 *
	 * @param unknown_type $name
	 */
	function setSchemaName($name);
	function get($id, $columns = array());
	function add($data, $columns = null);
	function getAll($columns, $sortBy = null);
	function getKey();
	function setKey($k);
	function setName($name);
	function useMemCache($enabled);
}