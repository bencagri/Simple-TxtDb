<?php
/**
 * Simple txtdb Class
 * API Documentation: https://github.com/bencagri/Simple-TxtDb
 * Required: PHP5.5+
 * 
 * @author Cagri S. Kirbiyik, Km.Van
 * @since 07.01.2016
 * @version 2.0.3
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */

class txtdb {

	/**
	 * The path to the cache file folder
	 *
	 * @var string
	 */
	private $_db_dir = __DIR__ . '/db/';

	/**
	 * The name of the default db file
	 *
	 * @var string
	 */
	private $_tablename = 'default';

	/**
	 * The db file extension
	 *
	 * @var string
	 */
	private $_extension = 'txtdb';

	/**
	 * File encryption
	 *
	 * @var string
	 */
	private $_encrypt = false;
	
	/**
	 * The db cache array
	 */
	private $db_cache = [];
	
	/**
	 * Default constructor
	 *
	 * @param array $config
	 *  @type string $dir
	 *  @type string $extension
	 *  @type string $encrypt
	 * @return void
	 */
	public function __construct(array $config = []) {
		$config = array_merge([
			'extension' => $this->_extension,
			'encrypt' => $this->_encrypt,
			'dir' => $this->_db_dir,
		],$config);
		
		$this->set_db_dir($config['dir']);
		$this->set_extension($config['extension']);
		$this->set_encryption($config['encrypt']);
	}
	private function set_db_dir($dir){
		$this->_db_dir = $dir;
	}
	/**
	 * Store data in the table file
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param integer [optional] $expiration
	 * @return mixed ID success or false
	 */

	public function insert($table, $new_data){
		$this->_load_table($table);

		if(!empty($new_data)){
			$id = $this->get_unique_id();
			$this->db_cache[$table][$id] = $new_data;
			if($this->write_to_disk($table)){
				return $id;
			}
			return false;
		}
		return false;
	}
	private function get_unique_id(){
		return md5($_SERVER['REQUEST_TIME'] + mt_rand(1000,9999));
	}
	/**
	 * Retrieve data by its key
	 * 
	 * @param string $table name
	 * @param mixed $condition Key name or array
	 * @return mixed
	 */
	public function select($table, $condition = null){
		$this->_load_table($table);
		
		if(empty($this->db_cache[$table]))
			return $this->select_all($table);
			
		/** no condition */
		if (!$condition) {
			return $this->db_cache[$table];
		}
		/** array condition */
		if(is_array($condition)){
			$data = [];
			foreach($this->db_cache[$table] as $k => $v){
				foreach($condition as $condition_key => $condition_value){
					if(!isset($v[$condition_key]) || $v[$condition_key] != $condition_value){
						continue 2;
					}
				}
				$data[$k] = $v;
			}
			return $data;
		/** id condition */
		}else{
			return isset($this->db_cache[$table][$condition]) ? [$condition => $this->db_cache[$table][$condition]] : false;
		}
	}

	public function select_all($table){
		$this->_load_table($table);
		return $this->db_cache[$table];
	}

	private function write_to_disk($table){
		if(!isset($this->db_cache[$table])){
			return file_put_contents($this->get_table_path($table),'');
		}
		return file_put_contents($this->get_table_path($table),json_encode($this->db_cache[$table]));
	}
	/**
	 * Delete content by id
	 * 
	 * @param string $table Table name
	 * @param string $id Row key
	 * @return boolean
	 */
	public function delete($table, $id = null){
		$this->_load_table($table);

		if(!$id){
			return $this->delete_all($table);
		}else{
			if(isset($this->db_cache[$table][$id])){
				unset($this->db_cache[$table][$id]);
				return $this->write_to_disk($table);
			}
		}
		return false;
	}

	/**
	 * Delete all contents of table
	 * 
	 * @param string $table Nable name
	 * @return object
	 */
	public function delete_all($table) {
		$this->set_table($table);
		unset($this->db_cache[$table]);
		return $this->write_to_disk($table);
	}

	/**
	 * Update content by id
	 * 
	 * @return object
	 */
	public function update($table, array $data, $id){
		$this->_load_table($table);

		if(isset($this->db_cache[$table][$id])){
			$this->db_cache[$table][$id] = array_merge(
				$this->db_cache[$table][$id],
				$data
			);
			if($this->write_to_disk($table)){
				return $this->db_cache[$table];
			}else{
				return false;
			}
		}
		return false;
	}

	/**
	 * Get the file directory path
	 * 
	 * @return string
	 */
	private function get_table_path($table) {
		if ($this->_check_table_dir()) {
			$filename = strtolower($table);
			return $this->get_db_dir() . $this->_get_hash($table) . '.' . $this->getExtension();
		}
	}
	private function get_db_dir(){
		return $this->_db_dir . '/';
	}
	/**
	 * Check if a writable file directory exists and if not create a new one
	 * 
	 * @return boolean
	 */
	private function _check_table_dir() {
		static $cache = null;
		if($cache !== null){
			return $cache;
		}
		if (!is_dir($this->get_db_dir()) && !mkdir($this->get_db_dir(), 0775, true)) {
			$cache = false;
			throw new Exception('Unable to create file directory ' . $this->get_db_dir());
		} elseif (!is_readable($this->get_db_dir()) || !is_writable($this->get_db_dir())) {
			if (!chmod($this->get_db_dir(), 0775)) {
				$cache = false;
				throw new Exception($this->get_db_dir() . ' must be readable and writeable');
			}
		}
		$cache = true;
		return true;
	}


	/**
	 * Get the filename hash
	 * 
	 * @return string
	 */
	private function _get_hash($filename) {
		if($this->_encrypt)
			return md5($filename);
		return $filename;
	}

	/**
	 * Table name Setter
	 * 
	 * @param string $name
	 * @return object
	 */
	private function set_table($name){
		if(!isset($this->db_cache[$name])){
			$this->db_cache[$name] = [];
		}
		$this->current_tablename = $name;
	}



	/**
	 * Cache name Getter
	 * 
	 * @return void
	 */
	private function get_table($table) {
		return $this->_tablename;
	}


	/**
	 * Load appointed table
	 * @param string $tablename Table name
	 * 
	 * @return array
	 */
	private function _load_table($table) {
		if(!isset($this->db_cache[$table])){
			if(!is_file($this->get_table_path($table))){
				$this->db_cache[$table] = [];
			}else{
				$this->db_cache[$table] = json_decode(file_get_contents($this->get_table_path($table)),true);
			}
		}
		$this->current_tablename = $table;
		return $this->db_cache[$table];
	}


	/**
	 * Table file extension Setter
	 * 
	 * @param string $ext
	 * @return object
	 */
	private function set_extension($ext) {
		$this->_extension = $ext;
		return $this;
	}

	/**
	 * Table file encryption Setter
	 * 
	 * @param string $ext
	 * @return object
	 */
	private function set_encryption($ext){
		$this->_encrypt = $ext;
		return $this;
	}

	/**
	 * Table file extension Getter
	 * 
	 * @return string
	 */
	private function getExtension() {
		return $this->_extension;
	}
}