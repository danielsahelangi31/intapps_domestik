<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

/**
 *
 * Logger library for Code Igniter.
 *
 * @package        Logger
 * @author         Parth Sutariya (https://github.com/pathusutariya)
 * @version        1.0.0
 * @license        GPL v3
 */
class Logger {

  private $tableName    = 'logger';
  private $table_fields = array(
    'id'         => 'id',
    'created_on' => 'created_on',
    'created_by' => 'created_by',
    'function_name'       => 'function_name',
    'comment'    => 'comment',
    'old_value'    => 'old_value',
    'new_value'    => 'new_value',
  );
  private $ci; //Codeigniter Instance
  private $logid        = 0; //LogId to Retrive
  private $comment      = ''; //Comment adding
  private $function_name      = ''; //Comment adding
  private $old_value      = null;
  private $new_value      = null;
  private $created_by   = ''; //User ID
  private $from_date; //From Date
  private $to_date; //To Date

  /**
   * Intilize Codeigniter
   */

  public function __construct() {
    $this->ci = &get_instance();
  }

  /**
   * Set UserID
   * @param int $userid
   * @return $this
   */
  public function user($userid) {
    $this->created_by = $userid;
    return $this;
  }

  /**
   * Set TypeID
   * @param string $type
   * @return $this
   */
  public function function_name($type) {
    $this->function_name = $type;
    return $this;
  }

  /**
   * Set  TypeID
   * @param int $id
   * @return $this
   */
  public function id($type_id) {
    $this->type_id = $type_id;
    return $this;
  }

  /**
   * Set Comment
   * @param string $comment
   * @return $this
   */
  public function comment($comment) {
    $this->comment = $comment;
    return $this;
  }

    public function old_value($old_value) {
        $this->old_value = $old_value;
        return $this;
    }

    public function new_value($new_value) {
        $this->new_value = $new_value;
        return $this;
    }

  /**
   * 
   * @param type $from
   * @param type $to
   * @return $this
   */
  public function date_range($from, $to) {
    $this->from_date = $from;
    $this->to_date   = $to;
    return $this;
  }

  /**
   * Add Log, as Database Entry
   * @param void
   * @return void
   */
  public function log() {
    $data        = array(
      $this->table_fields['created_by'] => $this->created_by,
      $this->table_fields['function_name']       => $this->function_name,
      $this->table_fields['comment']    => $this->comment,
      $this->table_fields['old_value']    => $this->old_value,
      $this->table_fields['new_value']    => $this->new_value,
    );
    $this->ci->db->insert($this->tableName, $data);
    $this->logid = $this->ci->db->insert_id();
    $this->flush_parameter();
  }

  /**
   * Get last Log
   * @return array
   */
  public function last_log() {
    return $this->ci->db->where('id', $this->logid)->get($this->tableName)->row();
  }

  protected function _getQueryMaker() {
    if ($this->created_by)
      $this->ci->db->where($this->table_fields['created_by'], $this->created_by);
    if ($this->function_name)
      $this->ci->db->where($this->table_fields['function_name'], $this->function_name);
    if ($this->logid)
      $this->ci->db->where($this->table_fields['id'], $this->logid);
    if ($this->from_date)
      $this->ci->db->where("{$this->table_fields['timestamp']} >", $this->from_date);
    if ($this->to_date)
      $this->ci->db->where("{$this->table_fields['created_at']} <=", $this->to_date);
  }

  public function get_num() {
    $this->_getQueryMaker();
    return $this->ci->db->from($this->tableName)->count_all_results();
  }

  public function get() {
    $this->_getQueryMaker();
    $result = $this->ci->db->get($this->tableName);
    return $this->_dbcleanresult($result);
  }

  public function remove_log() {
    $this->_getQueryMaker();
    $this->ci->db->delete($this->tableName);
  }

  protected function _dbcleanresult($result) {
    if ($result->num_rows() > 1)
      return $result->result();
    if ($result->num_rows() == 1)
      return $result->row();
    else
      return false;
  }

  /**
   * Reset Parameter
   */
  public function flush_parameter() {
    $this->comment = '';
    $this->function_name    = '';
  }

}

?>