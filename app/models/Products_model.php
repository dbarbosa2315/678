<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Products_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getAllProducts() {
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function products_count($category_id = NULL) {
        if ($category_id) {
            $this->db->where('category_id', $category_id);
            return $this->db->count_all_results("products");
        } else {
            return $this->db->count_all("products");
        }
    }

    public function fetch_products($limit, $start, $category_id = NULL) {
        $this->db->select('name, code, barcode_symbology, price')
                ->limit($limit, $start)->order_by("code", "asc");
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        $q = $this->db->get("products");

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getProductByCode($code) {
        $q = $this->db->get_where('products', array('ean' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getByCode($code) {
        $q = $this->db->get_where('products', array('code' => $code), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function getProductById($id) {
        $q = $this->db->get_where('products', array('id' => $id));

        if ($q->num_rows() > 0) {
            return $q->row();
        }

        return FALSE;
    }

    public function addProduct($data, $items = array()) {
        if ($this->db->insert('products', $data)) {
            $product_id = $this->db->insert_id();
            if (!empty($items)) {
                foreach ($items as $item) {
                    $item['product_id'] = $product_id;
                    $this->db->insert('combo_items', $item);
                }
            }
            return true;
        }
        return false;
    }

    public function add_products($data = array()) {
        if ($this->db->insert_batch('products', $data)) {
            return true;
        }
        return false;
    }

    public function updatePrice($data = array()) {
        if ($this->db->update_batch('products', $data, 'code')) {
            return true;
        }
        return false;
    }

    public function updateQuantity($id, $newQuantity) {
        $this->db->update('products', ['quantity' => $newQuantity], array('id' => $id));
        return ($this->db->affected_rows() == 1);
    }

    public function updateProduct($id, $data = array(), $items = array(), $photo = NULL) {
        if ($photo) {
            $data['image'] = $photo;
        }
        if ($this->db->update('products', $data, array('id' => $id))) {
            if (!empty($items)) {
                $this->db->delete('combo_items', array('product_id' => $id));
                foreach ($items as $item) {
                    $item['product_id'] = $id;
                    $this->db->insert('combo_items', $item);
                }
            }
            return true;
        }
        return false;
    }

    public function getComboItemsByPID($product_id) {
        $this->db->select($this->db->dbprefix('products') . '.id as id, ' . $this->db->dbprefix('products') . '.code as code, ' . $this->db->dbprefix('combo_items') . '.quantity as qty, ' . $this->db->dbprefix('products') . '.name as name')
                ->join('products', 'products.code=combo_items.item_code', 'left')
                ->group_by('combo_items.id');
        $q = $this->db->get_where('combo_items', array('product_id' => $product_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function deleteProduct($id) {
        if ($this->db->delete('products', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getProductNames($term, $limit = 10) {
        $this->db->where("type != 'combo' AND code='$term'");
        $this->db->limit($limit);
        $q = $this->db->get('products');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return FALSE;
    }

    public function getBy($colunas, $cond, $limit = false, $start = 0, $order = false) {

        $this->db->select($colunas);
        $this->db->from('tec_products');
        $this->db->where($cond);

        if ($limit) {
            $this->db->limit($limit, $start);
        }

        if ($order) {

            $this->db->order_by($order);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function getRelatorioVendas($dataI, $dataF) {
        $this->db->select('tec_products.name, SUM(DISTINCT tec_sale_items.quantity) as qtd_total');
        $this->db->from('tec_sale_items');
        $this->db->join('tec_sales', 'tec_sales.id = tec_sale_items.sale_id', 'INNER');
        $this->db->join('tec_products', 'tec_products.id = tec_sale_items.product_id', 'INNER');
        $this->db->where('tec_sales.date BETWEEN "' . $dataI . ' 00:00:00" and "' . $dataF . ' 23:59:59"');
        $this->db->group_by('tec_sale_items.product_id');

        $q = $this->db->get();

        return $q->result();
    }

    public function getVariants($id_produto) {
        $this->db->order_by('size', 'ASC');

        $query = $this->db->get_where('tec_products_variants', ['id_produto' => $id_produto]);

        return $query->result();
    }

    public function addVariants($id_produto, $items) {

        $query = $this->db->get_where('products', array('id' => $id_produto), 1);
        
        if ($query->num_rows() != 1) {
            return;
        }
        
        $prod = $query->row();
        
        //marca para remover 
        $this->db->update('tec_products_variants', ['quantity' => -101], ['id_produto' => $id_produto]);

        foreach ($items as $item) {

            $id = $item['id'];
            unset($item['id']);

            $item['sku'] = $this->getSku($prod->code, $item['color'], $item['size']);
            
            if ($id > 0) {
                $this->db->update('tec_products_variants', $item, ['id' => $id]);
            } else {
                $item['id_produto'] = $id_produto;
                $this->db->insert('tec_products_variants', $item);
            }
        }

        $this->db->delete('tec_products_variants', ['id_produto' => $id_produto, 'quantity' => -101]);
    }

    public function getForSync() {
        $ago = time() - 300;

        $date = date('Y-m-d H:i:s', $ago);

        $where = array(
            'updatedAt > ' => $date,
            'sync_time < ' => $ago
        );

        $this->db->where($where);

        $this->db->order_by('sync_time', 'ASC');

        $this->db->limit(10);

        $query = $this->db->get('products');

        return $query->result();
    }

    public function ajusteEstoque($code, $ajuste) {
        $this->db->where('code', $code);
        $this->db->set('quantity', 'quantity + ' . $ajuste, false);
        $this->db->update('products');
    }

    public function getSku($code, $color, $size) {

        setlocale(LC_ALL, 'en_US.utf8');

        $clean = str_replace(" e ", "", $color);

        $clean = preg_replace("/[^a-zA-Z]/", "", iconv('UTF-8', 'ASCII//TRANSLIT', $clean));

        $col = strtoupper($clean);

        return "$code-$col-$size";
    }

}
