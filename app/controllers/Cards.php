<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cards extends MY_Controller {

    public function __construct() {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->model('cards_model');
    }

    public function syncTax() {
        $post = [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA,
        ];

        $resp = json_decode(consumirApi("syncCardsTax", $post));

        if (!$resp->sucesso) {
            return;
        }
        
        foreach ($resp->dados as $tax) {

            if ($this->cards_model->getTax($tax->type)) {
                $this->cards_model->updateTax($tax->type, $tax);
            } else {
                $this->cards_model->addTax($tax);
            }
        }
    }

}
