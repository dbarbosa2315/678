<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sellers_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get($id) {
        $query = $this->db->get_where('sellers', array('id' => $id), 1);

        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return false;
    }
    
    public function getAll() {
        $query = $this->db->get('sellers');

        return $query->result();
    }
    
    public function getActive() {
        $query = $this->db->get_where('sellers', array('status' => 'A'));

        return $query->result();
    }

    public function insert($data = array()) {
        if ($this->db->insert('sellers', $data)) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function update($id, $data = array()) {
        if ($this->db->update('sellers', $data, array('id' => $id))) {
            return true;
        }

        return false;
    }

}
