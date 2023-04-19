<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lojas_model extends CI_Model
{

    private $tabela = 'lojas';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllLojas()
    {
        $this->db->select('id,cod,nome,obs,tipo');
        $q = $this->db->get($this->tabela);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCod($tipo)
    {
        $this->db->select('cod');
        $this->db->where('tipo', $tipo);
        $q = $this->db->get($this->tabela);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->cod;
            }
            return $data;
        }
        return false;
    }

    public function getAllLojasDepositos()
    {
        $this->db->select('id,cod,nome,obs,tipo');
        $this->db->where('tipo', 'DEPOSITO');
        $q = $this->db->get($this->tabela);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->cod] = $row;
            }
            return $data;
        }
        return false;
    }

    public function lojas_count($id = NULL)
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->count_all_results($this->tabela);
        } else {
            return $this->db->count_all($this->tabela);
        }
    }

    public function fetch_lojas($limit, $start, $id = NULL)
    {
        $this->db->select('*')
            ->limit($limit, $start)->order_by("cod", "asc");
        if ($id) {
            $this->db->where('id', $id);
        }
        $q = $this->db->get($this->tabela);

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getLojaByCod($cod)
    {
        $q = $this->db->get_where($this->tabela, array('cod' => $cod), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getLojaByCodAndToken($cod, $token)
    {
        $q = $this->db->get_where($this->tabela, array('cod' => $cod, 'token' => $token), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getLojaById($id)
    {
        $q = $this->db->get_where($this->tabela, array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getLojaBy($where)
    {
        $q = $this->db->get_where($this->tabela, $where);
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return FALSE;
    }


    public function addLoja($data)
    {
        if ($this->db->insert($this->tabela, $data)) {
            return true;
        }
        return false;
    }


    public function updateLoja($id, $data = array())
    {

        if ($this->db->update($this->tabela, $data, array('id' => $id))) {
            return true;
        }
        return false;
    }

    public function deleteLoja($id)
    {
        if ($this->db->delete($this->tabela, array('id' => $id))) {
            return true;
        }
        return FALSE;
    }
}
