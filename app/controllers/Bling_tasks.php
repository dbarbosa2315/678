<?php

class Bling_tasks extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model('bling_model');
        $this->load->helper('bling');
    }

    public function temp()
    {
        $this->load->model('products_model');

        $lista = file('relatorio-produtos.csv');

        foreach ($lista as $line) {

            list($code, $name) = explode(",", $line);

            $prod = $this->products_model->getByCode(trim($code));

            if (!$prod) {
                echo "$code: falha\n";
            }

            $this->products_model->updateProduct($prod->id, ['description' => trim($name)]);

            echo "$prod->code : Ok\n";
        }
    }

    public function syncPedidos()
    {
        $date = date('d/m/Y');

        $response = bling_pedidos($date, $date);

        if (!isset($response['retorno']['pedidos'])) {
            print_r($response);
            exit;
        }

        foreach ($response['retorno']['pedidos'] as $row) {
            echo $row['pedido']['numero'] . "\n";

            $this->bling_model->addPedido($row['pedido']);
        }
    }

    public function syncPedidosFull()
    {
        for ($d = 1; $d <= 7; $d++) {

            $data = date('d/m/Y', strtotime("-$d days"));

            $response = bling_pedidos($data, $data);

            if (!isset($response['retorno']['pedidos'])) {
                print_r($response);
                exit;
            }

            foreach ($response['retorno']['pedidos'] as $row) {

                echo "$data : {$row['pedido']['numero']}\n";

                $this->bling_model->addPedido($row['pedido']);
            }
        }
    }

    public function syncProdutos()
    {
        $this->load->helper('bling');

        $query = $this->db->query("SELECT p.code, p.name, p.price, v.* FROM tec_products_variants v, tec_products p WHERE p.id=v.id_produto order by code asc");

        $products = [];

        foreach ($query->result() as $row) {

            if (empty($row->sku)) {
                continue;
            }

            $products[$row->code][] = $row;
        }

        foreach ($products as $code => $variants) {

            $resp = bling_produto($code);

            if (!isset($resp['retorno']['produtos'])) {

                echo "$code: save\n";

                bling_add_produto($code, $variants[0]->name, $variants[0]->price, $variants);

                continue;
            }

            $produto = $resp['retorno']['produtos'][0]['produto'];

            $total_api = count($produto['variacoes']);

            $total_local = count($variants);

            if ($total_api < $total_local) {

                echo "$code: update\n";
                
                echo "variacoes $total_api => $total_local\n";

                bling_add_produto($code, $produto['descricao'], $variants[0]->price, $variants);

                continue;
            }

            echo "$code: Ok\n";
        }
    }

    public function syncEstoque()
    {
        $this->load->helper('bling');

        file_put_contents("falhas.txt", "");

        $rows = [];

        $query = $this->db->query("SELECT p.code, p.name, p.price, v.* FROM tec_products_variants v, tec_products p WHERE p.id=v.id_produto order by code asc");

        foreach ($query->result() as $row) {

            if (empty($row->sku)) {
                echo "$row->code:$row->color:$row->size sem sku\n";
                continue;
            }

            $row->quantity = intval($row->quantity);

            $rows[$row->code][$row->sku] = $row;
        }


        foreach ($rows as $code => $map) {

            $resp = bling_produto($code);

            if (!isset($resp['retorno']['produtos'])) {
                echo "$code: nao encontrado\n";
                continue;
            }

            echo "$code : " . count($map) . " variacoes\n";

            $variacoes = $resp['retorno']['produtos'][0]['produto']['variacoes'];

            foreach ($variacoes as $variacao) {

                $variacao = $variacao['variacao'];

                if (!isset($map[$variacao['codigo']])) {

                    file_put_contents("falhas.txt", $variacao['codigo'] . " => $code\n", FILE_APPEND);

                    continue;
                }

                $row = $map[$variacao['codigo']];

                $estoque = intval($variacao['depositos'][0]['deposito']['saldo']);

                if ($row->quantity < 0) {
                    $row->quantity = 0;
                }

                if ($estoque != $row->quantity) {

                    echo "$row->sku : estoque : $estoque => $row->quantity\n";

                    bling_update_variacao($row, $row->price);
                }
            }
        }
    }
}
