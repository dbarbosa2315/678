<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bling_model extends CI_Model
{

    private $pedidos = 'bling_pedidos';
    private $clientes = 'bling_clientes';
    private $pedido_itens = 'bling_pedido_itens';
    private $map_situacao = [
        6 => 'Em aberto',
        9 => 'Atendido',
        12 => 'Cancelado',
        15 => 'Em andamento'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function getPedido($numero)
    {
        $query = $this->db->get_where($this->pedidos, ['numero' => $numero]);

        return $query->row();
    }

    public function getItens($id_pedido)
    {
        $query = $this->db->get_where($this->pedido_itens, ['id_pedido' => $id_pedido]);

        return $query->result();
    }

    public function getCliente($id)
    {
        $query = $this->db->get_where($this->clientes, ['id' => $id]);

        return $query->row();
    }

    public function addPedido(array $pedido)
    {
        if (!$this->savePedido($pedido, $pedido['itens'])) {
            return;
        }

        $this->saveCliente($pedido['cliente']);

        $this->saveItens($pedido['numero'], $pedido['itens']);
    }

    public function confirmaPedido($numero)
    {
        $query = $this->db->get_where($this->pedidos, ['numero' => $numero, 'idSituacao' => 6]);

        if ($query->num_rows() < 1) {
            return false;
        }

        $itens = $this->db->get_where($this->pedido_itens, ['id_pedido' => $numero]);

        foreach ($itens->result() as $item) {
            $this->baixaEstoque($item->codigo, $item->quantidade);
        }

        $this->db->update($this->pedidos, ['situacao' => $this->map_situacao[9], 'idSituacao' => 9], ['numero' => $numero]);
    }

    private function savePedido($pedido, $itens)
    {
        $query = $this->db->get_where($this->pedidos, ['numero' => $pedido['numero']]);

        if ($query->num_rows() > 0) {

            $row = $query->row();

            if ($row->situacao != $pedido['situacao']) {

                echo "{$pedido['numero']} : $row->situacao => {$pedido['situacao']}\n";

                $this->pedidoAlterado($pedido, $itens);
            }

            return false;
        }

        $map = ['numero', 'data',
            'totalprodutos',
            'totalvenda',
            'situacao',
            'idSituacao',
            'numeroPedidoLoja',
            'tipoIntegracao',
            'valorfrete'];

        $raw = $pedido;

        foreach ($raw as $key => $value) {

            if (!in_array($key, $map)) {
                unset($pedido[$key]);
            }
        }

        $pedido['id_cliente'] = $raw['cliente']['id'];

        $idSituacao = array_search($pedido['situacao'], $this->map_situacao);

        if (!$idSituacao) {
            $idSituacao = 0;
        }

        $pedido['idSituacao'] = $idSituacao;

        $save = $this->db->insert($this->pedidos, $pedido);

        if ($save && $idSituacao > 6 && $idSituacao != 12) {

            foreach ($itens as $item) {

                $item = $item['item'];

                $this->baixaEstoque($item['codigo'], $item['quantidade']);
            }
        }

        return $save;
    }

    private function saveCliente($cliente)
    {
        $query = $this->db->get_where($this->clientes, ['id' => $cliente['id']]);

        if ($query->num_rows() > 0) {
            return false;
        }

        $cliente['complemento'] = substr($cliente['complemento'], 0, 15);

        return $this->db->insert($this->clientes, $cliente);
    }

    private function saveItens($id_pedido, $itens)
    {
        $query = $this->db->get_where($this->pedido_itens, ['id_pedido' => $id_pedido], 1);

        if ($query->num_rows() > 0) {
            return false;
        }

        $map = [
            'id_pedido',
            'codigo',
            'descricao',
            'quantidade',
            'valorunidade'
        ];

        $total_itens = 0;

        foreach ($itens as $item) {

            $item = $item['item'];

            if (empty($item['codigo'])) {
                $item['codigo'] = '';
                echo "$id_pedido: sem SKU\n";
            }

            foreach ($item as $key => $value) {

                if (!in_array($key, $map)) {
                    unset($item[$key]);
                }
            }

            $item['id_pedido'] = $id_pedido;

            $this->db->insert($this->pedido_itens, $item);

            $total_itens += $item['quantidade'];
        }

        $this->db->update($this->pedidos, ['totalItens' => $total_itens], ['numero' => $id_pedido]);

        return true;
    }

    private function pedidoAlterado($pedido, $itens)
    {
        $idSituacao = array_search($pedido['situacao'], $this->map_situacao);

        if (!$idSituacao) {
            $idSituacao = 0;
        }

        $this->db->update($this->pedidos, ['situacao' => $pedido['situacao'], 'idSituacao' => $idSituacao], ['numero' => $pedido['numero']]);

        if ($idSituacao > 6 && $idSituacao != 12) {

            foreach ($itens as $item) {

                $item = $item['item'];

                $this->baixaEstoque($item['codigo'], $item['quantidade']);
            }
        }
    }

    private function baixaEstoque($sku, $quantity)
    {
        if (empty($sku)) {
            return;
        }

        /*

          $this->db->reset_query();

          $this->db->where(['sku' => $sku]);
          $this->db->set('quantity', 'quantity - ' . $quantity, false);
          $this->db->update('tec_products_variants');

          $this->db->reset_query();

         */
    }
}
