<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales extends MY_Controller {

    function __construct() {
        parent::__construct();

        if (!$this->loggedIn) {
            redirect('login');
        }
        $this->load->library('form_validation');
        $this->load->model('sales_model');

        $this->digital_file_types = 'zip|pdf|doc|docx|xls|xlsx|jpg|png|gif';
    }

    function index() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('sales');
        $bc = array(array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
        
        $today = time() * 1000;
                
        $this->data['date_start'] = $today;
        $this->data['date_end'] = $today;
        
        $this->page_construct('sales/index', $this->data, $meta);
    }

    function get_sales() {
        $this->load->library('datatables');

        $sales = $this->db->dbprefix('sales');
        $cust = $this->db->dbprefix('customers');
        $sel = $this->db->dbprefix('sellers');

        $this->datatables->select("$sales.id as sid, seq_id, $sales.date, $cust.name as cname, $sel.name as sname, grand_total, $sales.status");
        $this->datatables->from($sales);

        $date_start = date('Y-m-d', $this->input->post('start') / 1000);
        $date_end = date('Y-m-d', $this->input->post('end') / 1000);
        
        $this->datatables->where("$sales.date >=", "$date_start 00:00:00");
        $this->datatables->where("$sales.date <=", "$date_end 23:59:59");
        
        $this->datatables->join($cust, "$cust.id=$sales.customer_id", 'LEFT');

        $this->datatables->join($sel, "$sel.id=$sales.seller_id");

        $actions = "<div class='text-center'>"
                . "<div class='btn-group actions'>"
                . "<a href='#' onClick=\"MyWindow=window.open('" . site_url('pos/view/$1/1') . "', 'MyWindow','toolbar=no,location=no,directories=no,status=no,menubar=yes,scrollbars=yes,resizable=yes,width=600,height=800'); return false;\" title='" . lang("view_invoice") . "' class='tip btn btn-primary btn-xs'>"
                . "<i class='fa fa-list'></i>"
                . "</a>";


        $actions .= "|<a href='" . site_url('sales/cancel/$1') . "' onClick=\"return confirm('Tem certeza que deseja cancelar a venda $1?')\" title='Cancelar' class='tip btn btn-danger btn-xs'><i class='fa fa-ban'></i>"
                    . "</a>";

        $actions .= "</div></div>";

        $this->datatables->add_column("Actions", $actions, "sid");

        $this->datatables->unset_column('sid');
                
        $data = json_decode($this->datatables->generate(), true);
        
        $data['footer'] = $this->sales_model->valorTotal($date_start, $date_end);
        
        echo json_encode($data);
    }

    function bling() {
        
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('sales');
        $bc = array(array('link' => '#', 'page' => lang('sales')));
        $meta = array('page_title' => lang('sales'), 'bc' => $bc);
                        
        $this->data['date_start'] = strtotime("-7 days") * 1000;
        $this->data['date_end'] = time() * 1000;
        
        $this->page_construct('sales/bling', $this->data, $meta);
    }
    
    function get_sales_bling() {

        if (!$this->Admin) {
            exit;
        }

        $this->load->library('datatables');

        $pedidos = $this->db->dbprefix('bling_pedidos');
        $clientes = $this->db->dbprefix('bling_clientes');

        $this->datatables->select("$pedidos.numeroPedidoLoja, $pedidos.data, $clientes.nome as cliente, "
                . "$pedidos.tipoIntegracao, $pedidos.totalItens, $pedidos.totalvenda, $pedidos.situacao, $pedidos.numero");

        $this->datatables->from($pedidos);

        $date_start = date('Y-m-d', $this->input->post('start') / 1000);
        $date_end = date('Y-m-d', $this->input->post('end') / 1000);

        $this->datatables->where("$pedidos.data >=", "$date_start");
        $this->datatables->where("$pedidos.data <=", "$date_end");

        $this->datatables->join($clientes, "$clientes.id=$pedidos.id_cliente");

        echo $this->datatables->generate();
    }

    function biling_view($id) {
        
        $this->load->model('bling_model');
        
        $pedido = $this->bling_model->getPedido($id);
        
        $this->data['pedido'] = $pedido;
        
        $this->data['itens'] = $this->bling_model->getItens($id);
        
        $this->data['cliente'] = $this->bling_model->getCliente($pedido->id_cliente);
        
        $this->load->view($this->theme . 'sales/bling_view', $this->data);
    }
    
    function biling_confirma() {

        if (!$this->Admin) {
            exit;
        }

        $id = $this->input->post('id');

        $this->load->helper('bling');

        $response = bling_confirma_pedido($id);
        
        if (!isset($response['retorno']['pedidos'][0]['pedido']['numero'])) {
            exit;
        }
        
        $this->load->model('bling_model');
        
        $this->bling_model->confirmaPedido($id);
    }

    function opened() {
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('opened_bills');
        $bc = array(array('link' => '#', 'page' => lang('opened_bills')));
        $meta = array('page_title' => lang('opened_bills'), 'bc' => $bc);
        $this->page_construct('sales/opened', $this->data, $meta);
    }

    function get_opened_list() {

        $this->load->library('datatables');
        $this->datatables
                ->select("id, date, customer_name, hold_ref, CONCAT(total_items, ' (', total_quantity, ')') as items, grand_total", FALSE)
                ->from('suspended_sales');
        if (!$this->Admin) {
            $user_id = $this->session->userdata('user_id');
            $this->datatables->where('created_by', $user_id);
        }
        $this->datatables->add_column("Actions", "<div class='text-center'><div class='btn-group actions'><a href='" . site_url('pos/?hold=$1') . "' title='" . lang("click_to_add") . "' class='tip btn btn-info btn-xs'><i class='fa fa-th-large'></i></a>
			<a href='" . site_url('sales/delete_holded/$1') . "' onClick=\"return confirm('Tem certeza que deseja excluir?')\" title='" . lang("delete_sale") . "' class='tip btn btn-danger btn-xs'><i class='fa fa-trash-o'></i></a></div></div>", "id")
                ->unset_column('id');

        echo $this->datatables->generate();
    }

    function cancel($id = NULL) {
        if (DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->sales_model->setStatus($id, 'pend-cancel');
                
        $this->session->set_flashdata('message', 'Venda enviada para cancelamento');
        
        redirect('sales');
    }

    function delete_holded($id = NULL) {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if ($this->sales_model->deleteOpenedSale($id)) {
            $this->session->set_flashdata('message', 'Conta excluída');
            redirect('sales/opened');
        }
    }

    /* -------------------------------------------------------------------------------- */

    function payments($id = NULL) {
        $this->data['payments'] = $this->sales_model->getSalePayments($id);
        $this->load->view($this->theme . 'sales/payments', $this->data);
    }

    function payment_note($id = NULL) {
        $payment = $this->sales_model->getPaymentByID($id);
        $inv = $this->sales_model->getSaleByID($payment->sale_id);
        $this->data['customer'] = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv'] = $inv;
        $this->data['payment'] = $payment;
        $this->data['page_title'] = $this->lang->line("payment_note");

        $this->load->view($this->theme . 'sales/payment_note', $this->data);
    }

    function add_payment($id = NULL, $cid = NULL) {
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->Admin) {
                $date = $this->input->post('date');
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = array(
                'date' => $date,
                'sale_id' => $id,
                'customer_id' => $cid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'gc_no' => $this->input->post('gift_card_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'created_by' => $this->session->userdata('user_id'),
            );

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }


        if ($this->form_validation->run() == true && $this->sales_model->addPayment($payment)) {
            $this->session->set_flashdata('message', lang("payment_added"));
            redirect($_SERVER["HTTP_REFERER"]);
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $sale = $this->sales_model->getSaleByID($id);
            $this->data['inv'] = $sale;

            $this->load->view($this->theme . 'sales/add_payment', $this->data);
        }
    }

    function edit_payment($id = NULL, $sid = NULL) {

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $this->form_validation->set_rules('amount-paid', lang("amount"), 'required');
        $this->form_validation->set_rules('paid_by', lang("paid_by"), 'required');
        $this->form_validation->set_rules('userfile', lang("attachment"), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $payment = array(
                'sale_id' => $sid,
                'reference' => $this->input->post('reference'),
                'amount' => $this->input->post('amount-paid'),
                'paid_by' => $this->input->post('paid_by'),
                'cheque_no' => $this->input->post('cheque_no'),
                'gc_no' => $this->input->post('gift_card_no'),
                'cc_no' => $this->input->post('pcc_no'),
                'cc_holder' => $this->input->post('pcc_holder'),
                'cc_month' => $this->input->post('pcc_month'),
                'cc_year' => $this->input->post('pcc_year'),
                'cc_type' => $this->input->post('pcc_type'),
                'note' => $this->input->post('note'),
                'updated_by' => $this->session->userdata('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            );

            if ($this->Admin) {
                $payment['date'] = $this->input->post('date');
            }

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path'] = 'files/';
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size'] = 2048;
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER["HTTP_REFERER"]);
                }
                $photo = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            $this->tec->dd();
        }


        if ($this->form_validation->run() == true && $this->sales_model->updatePayment($id, $payment)) {
            $this->session->set_flashdata('message', lang("payment_updated"));
            redirect("sales");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $payment = $this->sales_model->getPaymentByID($id);
            if ($payment->paid_by != 'cash') {
                $this->session->set_flashdata('error', lang('only_cash_can_be_edited'));
                $this->tec->dd();
            }
            $this->data['payment'] = $payment;
            $this->load->view($this->theme . 'sales/edit_payment', $this->data);
        }
    }

    function delete_payment($id = NULL) {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang("access_denied"));
            redirect($_SERVER["HTTP_REFERER"]);
        }

        if ($this->sales_model->deletePayment($id)) {
            $this->session->set_flashdata('message', lang("payment_deleted"));
            redirect('sales');
        }
    }

    public function sync() {
        $post = [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA,
        ];

        $this->load->model('customers_model');

        $this->load->model('products_model');

        $rows = $this->sales_model->getForSync();

        foreach ($rows as $sale) {

            $items = $this->sales_model->getItems($sale->id);
            
            $post['sale'] = $sale;

            $post['items'] = $items;

            $customer = $this->customers_model->getCustomerByID($sale->customer_id);

            $post['customer'] = $customer;

            $post['estoque'] = [];

            foreach ($items as $item) {
                $prod = $this->products_model->getProductById($item->product_id);
                $estoque = [
                    'code' => $prod->code,
                    'ean' => $prod->ean,
                    'quantity' => $prod->quantity
                ];

                consumirApi("syncEstoque", $post + $estoque);
            }

            $json = consumirApi("syncSale", $post);
            $resp = json_decode($json);

            if ($resp->sucesso) {
                $this->sales_model->confirmSync($sale->id);
            }
        }
    }
    
    public function syncCanceled() {

        $post = [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA,
        ];

        $rows = $this->sales_model->getPendCancel();

        foreach ($rows as $sale) {
            
            $post['id'] = $sale->id;
            
            $json = consumirApi("syncSaleCanceled", $post);

            $resp = json_decode($json);

            if($resp->sucesso && $resp->dados->status === 'canceled') {
                $this->sales_model->setStatus($sale->id, 'canceled');
            }
        }
    }

    public function topItems() {
        $rows = $this->sales_model->topItems();

        header('Content-Type: application/json');

        echo json_encode($rows);
    }

    /* --------------------------------------------------------------------------------------------- */
}
