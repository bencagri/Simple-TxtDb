<?php

/**
 * Simple TxtDb Class
 * API Documentation: https://github.com/bencagri/Simple-TxtDb
 * 
 * @author Cagri S. Kirbiyik
 * @since 17.11.2014
 * @version 1.6
 * @license BSD http://www.opensource.org/licenses/bsd-license.php
 */

class TxtDb {

  /**
   * The path to the cache file folder
   *
   * @var string
   */
  private $_dbpath = 'db/';

  /**
   * The name of the default cache file
   *
   * @var string
   */
  private $_tablename = 'default';

  /**
   * The cache file extension
   *
   * @var string
   */
  private $_extension = '.txtdb';

  /**
   * Ffile encryption
   *
   * @var string
   */
  private $_encrypt = FALSE;

  /**
   * Default constructor
   *
   * @param string|array [optional] $config
   * @return void
   */
  public function __construct($config = null) {
    if (true === isset($config)) {
      if (is_string($config)) {
        $this->setCache($config);
      } else if (is_array($config)) {
        $this->setTable($config['name']);
        $this->getTablePath($config['path']);
        $this->setExtension($config['extension']);
        $this->setEncryption($config['encrypt']);
      }
    }
  }

  /**
  * Store data in the table file
  *
  * @param string $key
  * @param mixed $data
  * @param integer [optional] $expiration
  * @return boolean
  */

  function insert($table,$data){
    $this->setTable($table);
    $dataArray = $this->_loadTable();


    if (true === is_array($dataArray)) {
      
      end($dataArray);
      $key = key($dataArray);
      $key = $key + 1;

      $dataArray[$key] = $data;

    } else {
      $key = 1;
      $dataArray = array(1 => $data);
    }

    $saveData = json_encode($dataArray);
    $save = file_put_contents($this->getTableDir(), $saveData);
    return $save ? true : false ;

  }

  /**
  * Retrieve data by its key
  * 
  * @param string $key
  * @param boolean [optional] $timestamp
  * @return string
  */

  public function select($table,$id=NULL){
    $this->setTable($table);
    $dataArray = $this->_loadTable(true);
    if (!$id) {
      return $dataArray;
    }else{
      if (!isset($dataArray->$id)) return null; 
      return $dataArray->$id;
    }
  }

  public function selectAll($table){
    $this->setTable($table);
    return $this->_loadTable();
  }


  /**
  * Delete content by id
  * 
  * @return boolean
  */
  public function delete($table,$id=NULL){
    $this->setTable($table);

    if ($id<1 or $id === NULL) {
      $this->deleteAll($table);
    }else{
      $dataArray = $this->_loadTable();
      if (true === isset($dataArray[$id])) {
        unset($dataArray[$id]);
        $dataArray = json_encode($dataArray);
        file_put_contents($this->getTableDir(), $dataArray);
        return true;
      } else {
        echo("Error: delete() - Key '{$id}' not found.");
      }
    }
  }

  /**
  * Delete all contents of table
  * 
  * @return object
  */
  public function deleteAll($table) {
    $this->setTable($table);
    $fileDir = $this->getTableDir();
    if (true === file_exists($fileDir)) {
      $File = fopen($fileDir, 'w');
      fclose($File);
    }
    return $this;
  }

  /**
  * Update content by id
  * 
  * @return object
  */
  public function update($table,$data=array(),$id){
    $this->setTable($table);
    $dataArray = $this->_loadTable();

    if ($dataArray[$id]) {
      $dataArray[$id] = $data;
      $dataArray = json_encode($dataArray);
      file_put_contents($this->getTableDir(), $dataArray);
    }else{
      echo("Error: update() - Key '{$id}' not found.");
    }

  }

  /**
  * Get the file directory path
  * 
  * @return string
  */
  private function getTableDir() {
    if (true === $this->_checkTableDir()) {
      $filename = $this->getTable();
      $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));
      return $this->getTablePath() . $this->_getHash($filename) . $this->getExtension();
    }
  }

  /**
  * Check if a writable file directory exists and if not create a new one
  * 
  * @return boolean
  */
  private function _checkTableDir() {
    if (!is_dir($this->getTablePath()) && !mkdir($this->getTablePath(), 0775, true)) {
      throw new Exception('Unable to create file directory ' . $this->getTablePath());
    } elseif (!is_readable($this->getTablePath()) || !is_writable($this->getTablePath())) {
      if (!chmod($this->getTablePath(), 0775)) {
        throw new Exception($this->getTablePath() . ' must be readable and writeable');
      }
    }
    return true;
  }


  /**
  * Get the filename hash
  * 
  * @return string
  */
  private function _getHash($filename) {
    $this->_encrypt == true ? $file = sha1($filename) : $file = $filename;
    return $file;
    
  }



  /**
  * Table path getter
  * 
  * @param string $name
  * @return object
  */

  private function getTablePath() {
    return $this->_dbpath;
  }

  /**
   * Table name Setter
   * 
   * @param string $name
   * @return object
   */

  private function setTable($name) {
    $this->_tablename = $name;
    return $this;
  }



  /**
   * Cache name Getter
   * 
   * @return void
   */
  private function getTable() {
    return $this->_tablename;
  }


  /**
  * Load appointed table
  * 
  * @return mixed
  */
  private function _loadTable($object=false) {
    if (true === file_exists($this->getTableDir())) {
      $file = file_get_contents($this->getTableDir());
      if ($object==TRUE) {
        return json_decode($file);
      }else{
        return json_decode($file,TRUE);
      }
      
    } else {
      return false;
    }
  }


  /**
  * Table file extension Setter
  * 
  * @param string $ext
  * @return object
  */
  private function setExtension($ext) {
    $this->_extension = $ext;
    return $this;
  }

  /**
  * Table file encryption Setter
  * 
  * @param string $ext
  * @return object
  */
  private function setEncryption($ext){
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
