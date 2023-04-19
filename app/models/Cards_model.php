<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cards_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getAllTax() {
        $query = $this->db->get('cards_tax');
        return $query->result();
    }

    public function getTax($type) {
        $query = $this->db->get_where('cards_tax', array('type' => $type), 1);

        if ($query->num_rows() == 1) {
            return $query->row();
        }

        return false;
    }

    public function addTax($data = array()) {
        if ($this->db->insert('cards_tax', $data)) {
            return true;
        }

        return false;
    }

    public function updateTax($type, $data = array()) {
        if ($this->db->update('cards_tax', $data, array('type' => $type))) {
            return true;
        }

        return false;
    }

}
