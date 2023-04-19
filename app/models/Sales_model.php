<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getSaleByID($id) {
        $q = $this->db->get_where('sales', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function deleteInvoice($id) {
        if ($this->db->delete('sale_items', array('sale_id' => $id)) && $this->db->delete('sales', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function deleteOpenedSale($id) {
        if ($this->db->delete('suspended_items', array('suspend_id' => $id)) && $this->db->delete('suspended_sales', array('id' => $id))) {
            return true;
        }
        return FALSE;
    }

    public function getSalePayments($sale_id) {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', array('sale_id' => $sale_id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id) {
        $q = $this->db->get_where('payments', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return FALSE;
    }

    public function addPayment($data = array()) {
        if ($this->db->insert('payments', $data)) {
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['gc_no']);
                $this->db->update('gift_cards', array('balance' => ($gc->balance - $data['amount'])), array('card_no' => $data['gc_no']));
            }
            $this->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = array()) {
        if ($this->db->update('payments', $data, array('id' => $id))) {
            $this->syncSalePayments($data['sale_id']);
            return true;
        }
        return false;
    }

    public function deletePayment($id) {
        $opay = $this->getPaymentByID($id);
        if ($this->db->delete('payments', array('id' => $id))) {
            $this->syncSalePayments($opay->sale_id);
            return true;
        }
        return FALSE;
    }

    public function syncSalePayments($id) {
        $sale = $this->getSaleByID($id);
        $payments = $this->getSalePayments($id);
        $paid = 0;
        if ($payments) {
            foreach ($payments as $payment) {
                $paid += $payment->amount;
            }
        }
        $status = $paid <= 0 ? 'due' : $sale->status;
        if ($this->tec->formatDecimal($sale->grand_total) > $this->tec->formatDecimal($paid) && $paid > 0) {
            $status = 'partial';
        } elseif ($this->tec->formatDecimal($sale->grand_total) <= $this->tec->formatDecimal($paid)) {
            $status = 'paid';
        }

        if ($this->db->update('sales', array('paid' => $paid, 'status' => $status), array('id' => $id))) {
            return true;
        }

        return FALSE;
    }

    public function getForSync() {
        $query = $this->db->get_where('sales', array('sync_time' => 0), 10);
        return $query->result();
    }

    public function getItems($id) {
        $query = $this->db->get_where('sale_items', array('sale_id' => $id));
        return $query->result();
    }

    public function confirmSync($id) {
        $this->db->update('sales', array('sync_time' => time()), array('id' => $id));
    }

    public function topItems() {
        $date = date('Y-m-d H:i:s', strtotime("-30 days"));
        
        $this->db->select('tec_products.code, tec_products.name, tec_products.image, SUM(tec_sale_items.quantity) AS total');
        $this->db->from('tec_sale_items');
        $this->db->join('tec_sales', 'tec_sales.id = tec_sale_items.sale_id');
        $this->db->join('tec_products', 'tec_products.id = tec_sale_items.product_id');
        $this->db->where('tec_sales.date > ', $date);
        $this->db->group_by('tec_sale_items.product_id');
        $this->db->order_by('total', 'DESC');
        $this->db->limit(100);

        $q = $this->db->get();
        
        return $q->result();
    }
    
    public function update($id, $data = array()) {
        if ($this->db->update('sales', $data, array('id' => $id))) {
            return true;
        }

        return false;
    }
    
    public function setStatus($id, $status)
    {
        $items = $this->getItems($id);

        if (!$items) {
            return;
        }

        $this->db->reset_query();
        
        if ($status !== 'canceled') {
            $this->update($id, ['status' => $status, 'sync_time' => 0]);
            return;
        }
        
        $this->update($id, ['status' => $status]);
        
        foreach ($items as $item) {
            $this->db->where(['id' => $item->product_id]);
            $this->db->set('quantity', 'quantity + ' . $item->quantity, false);
            $this->db->update('products');
            $this->db->reset_query();
        }
    }
    
    public function getPendCancel() {
        $query = $this->db->get_where('sales', array('status' => 'pend-cancel'), 5);
        return $query->result();
    }
    
    public function valorTotal($date_start, $date_end) {

        $sql = "SELECT SUM(grand_total) as total "
                . "FROM tec_sales "
                . "WHERE date >= '$date_start 00:00:00' "
                . "AND date <= '$date_end 23:59:50' "
                . "AND status != 'canceled'";

        $query = $this->db->query($sql);

        return $query->row();
    }
}
