<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contingencia_model extends CI_Model
{

    private $tabela = 'contingencia';

    function __construct()
    {
        parent::__construct();
    }

    public function add($dados)
    {

        try {
            $this->db->insert($this->tabela, $dados);
            if ($this->db->affected_rows() == '1') {
                return true;
            } else {

                return false;
            }
        } catch (Exception $e) {

            return false;
        }
    }

    public function get($colunas, $arrWheres = false, $array = false, $limit = false, $start = 0)
    {

        $this->db->select($colunas);
        $this->db->from($this->tabela);

        if ($arrWheres) {

            foreach ($arrWheres as $arrWhere) {

                foreach ($arrWhere['consulta'] as $coluna => $valor) {
                    if (isset($arrWhere['condicao'])) {

                        if ($arrWhere['condicao'] == 'OR') {

                            $this->db->or_where($coluna, $valor);
                        } else {

                            $this->db->where($coluna, $valor);
                        }
                    } else {

                        $this->db->where($coluna, $valor);
                    }
                }
            }
        }

        if ($limit) {
            $this->db->limit($limit, $start);
        }

        $query = $this->db->get();



        if ($array) {

            return $query->result_array();
        } else {

            return $query->result();
        }
    }

    public function getSimple($colunas, $cond, $array = false, $limit = false, $start = 0, $order = false)
    {

        $this->db->select($colunas);
        $this->db->from($this->tabela);
        $this->db->where($cond);

        if ($limit) {
            $this->db->limit($limit, $start);
        }

        if ($order) {

            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($array) {

            return $query->result_array();
        } else {

            return $query->result();
        }
    }

    public function del($id)
    {
        if ($this->db->delete($this->tabela, array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
}
