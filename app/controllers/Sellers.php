<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sellers extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->model('sellers_model');
    }

   public function index()
    {
        $this->data['page_title'] = 'Vendedores';
        $bc = array(array('link' => '#', 'page' => 'Vendedores'));
        $meta = array('page_title' => 'Vendedores', 'bc' => $bc);
        $this->page_construct('sellers/index', $this->data, $meta);
    }

    public function get_sellers()
    {
        $this->load->library('datatables');

        $sel = $this->db->dbprefix('sellers');
        $loj = $this->db->dbprefix('lojas');
        
        $this->datatables->select("$sel.id as sid, name, status");

        $this->datatables->from($sel)
        ->join($loj, "$loj.cod=$sel.cod_loja");

        echo $this->datatables->generate();
    }

    public function sync()
    {
        $post = [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA,
        ];

        $resp = json_decode(consumirApi("syncSellers", $post));
        
        if (!$resp->sucesso) {
            return;
        }

        foreach ($resp->dados as $seller) {

            if ($this->sellers_model->get($seller->id)) {
                $this->sellers_model->update($seller->id, $seller);
            } else {                
                $this->sellers_model->insert($seller);
            }
        }
    }
}
