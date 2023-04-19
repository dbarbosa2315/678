<?php

class Tasks extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function syncEstoqueFull()
    {
        $this->load->model('products_model');

        $rows = $this->products_model->getAllProducts();

        $ago = time() - (10 * 3600);

        foreach ($rows as $prod) {

            if ($prod->sync_time > $ago) {
                continue;
            }

            $post = [
                'token' => TOKEN,
                'cod_loja_origem' => CODIGO_LOJA,
                'code' => $prod->code,
                'ean' => $prod->ean,
                'quantity' => $prod->quantity
            ];

            $json = consumirApi("syncEstoque", $post);

            $resp = json_decode($json);

            if ($resp->sucesso) {
                $this->products_model->updateProduct($prod->id, ['sync_time' => time()]);
                echo "$prod->code : Ok\n";
            } else {
                echo "$prod->code : Erro\n";
            }
        }
    }

    public function updateDatabase()
    {

        $query = $this->db->query("SELECT * FROM tec_products_variants");
        if (!$query) {
            $this->db->query("TRUNCATE TABLE tec_customers;");
            $this->db->query("TRUNCATE TABLE tec_sales;");
            $this->db->query("TRUNCATE TABLE tec_sale_items;");
        }

        $files = glob("update_db/*/*.sql");

        foreach ($files as $file) {

            echo "$file\n";

            $sql = file_get_contents($file);

            $sqls = explode(';', $sql);
            array_pop($sqls);

            foreach ($sqls as $statement) {
                $stm = $statement . ";";
                $this->db->query($stm);
            }

            unlink($file);
        }
    }

    public function importaEstoque()
    {
        $this->load->model('products_model');

        $fh = fopen("estoque.csv", "r");

        $header = fgetcsv($fh, 1024, ",");

        //$this->products_model->db->update('products', ['quantity' => 0]);

        while (($row = fgetcsv($fh, 1024, ",")) !== false) {

            if (count($row) != 2) {
                echo "Falha\n";
                print_r($row);
                exit;
            }

            $code = trim($row[0]);

            $total = intval($row[1]);

            $prod = $this->products_model->getByCode($code);

            if (!$prod) {
                exit("\nNão achou $code\n\n");
            }

            $this->products_model->updateProduct($prod->id, ['quantity' => $total]);

            echo "$code : $total\n";
        }
    }

    public function syncEdicaoProduto()
    {
        $this->load->model('products_model');

        $json = consumirApi('getSolicitacaoEdicaoProduto', [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA,
            'limit' => 500
        ]);

        $arrResposta = json_decode($json);

        if (!$arrResposta->sucesso) {
            return;
        }

        if (!$arrResposta->dados) {
            return;
        }

        foreach ($arrResposta->dados as $arr) {
            $ok = false;

            $dados = json_decode($arr->dados, true);

            unset($dados['color']);
            unset($dados['size']);

            if ($arr->operation === 'update') {
                foreach ($dados as $prop => $val) {
                    if (empty($val)) {
                        unset($dados[$prop]);
                    }
                }

                $ok = $this->products_model->updateProduct($arr->id_produto, $dados);
            }


            if ($arr->operation === 'insert') {
                $dados['id'] = $arr->id_produto;

                $prod = $this->products_model->getByCode($dados['code']);

                if ($prod) {
                    $ok = true;
                } else {
                    $ok = $this->products_model->addProduct($dados);
                }
            }

            if ($ok) {
                $post = [
                    'token' => TOKEN,
                    'cod_loja_origem' => CODIGO_LOJA,
                    'id_produtos_edicoes' => $arr->id,
                ];

                consumirApi('enviaConfirmacaoEdicaoProduto', $post);

                echo "{$dados['code']} : $arr->operation : ok\n";
            } else {
                echo "{$dados['code']} : $arr->operation : falha\n";
            }
        }
    }

    public function updateSkus()
    {
        $this->load->model('products_model');

        $query = $this->db->query("SELECT p.code, v.id, v.color, v.size FROM tec_products_variants v, tec_products p WHERE v.sku IS NULL AND p.id=v.id_produto");

        foreach ($query->result() as $row) {

            $sku = $this->products_model->getSku($row->code, $row->color, $row->size);

            $this->db->query("UPDATE tec_products_variants set sku='$sku' WHERE id=$row->id");

            echo "$sku\n";
        }
    }
}
