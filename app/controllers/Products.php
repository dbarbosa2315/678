<?php defined('BASEPATH') or exit('No direct script access allowed');

class Products extends MY_Controller
{

    function __construct()
    {
        parent::__construct();


        if (!$this->loggedIn) {
            redirect('login');
        }

        $this->load->library('form_validation');
        $this->load->model('products_model');
        $this->load->model('lojas_model');
    }

    function index()
    {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['page_title'] = lang('products');

        $bc = array(array('link' => '#', 'page' => lang('products')));
        $meta = array('page_title' => lang('products'), 'bc' => $bc);
        $this->page_construct('products/index', $this->data, $meta);
    }

    function get_products()
    {

        $this->load->library('datatables');
        
        $prod = $this->db->dbprefix('products');
        $cat = $this->db->dbprefix('categories');
        
            $this->datatables->select("$prod.id as pid, $prod.image as image, $prod.code as code,"
                   . "$prod.name as pname, $prod.ean as ean,"
                   . "$cat.name as cname, model, quantity, price, barcode_symbology", FALSE);

            if (empty($_POST['sSearch'])) {
                $this->datatables->where('quantity != 0');
            }
            
            if (empty($_POST['sSearch']) && CODIGO_LOJA === 'ONLINE') {
                $this->datatables->where('variants > 0');
            }
             
        $this->datatables->join('categories', 'categories.id=products.category_id', 'LEFT')
            ->from('products')
            ->group_by('products.id');

        $actions = "<div class='text-center'>
                <div class='btn-group actions'>
                <a href='#' class='tip btn btn-primary btn-xs' onclick='variants(event, this, $1)' title='Variações'>
                    <i class='fa fa-search'></i>
                </a>
                <a href='" . site_url('products/view/$1') . "' title='" . lang("view") . "' class='tip btn btn-primary btn-xs' data-toggle='ajax'>
                  <i class='fa fa-file-text-o'></i>
                </a>";
                /* <a onclick=\"window.open('" . site_url('products/single_barcode/$1') . "', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='" . lang('print_barcodes') . "' class='tip btn btn-default btn-xs'>"
                . "<i class='fa fa-print'>"
                . "</i></a> "
                . "<a onclick=\"window.open('" . site_url('products/single_label/$1') . "', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;\" href='#' title='" . lang('print_labels') . "' class='tip btn btn-default btn-xs'>"
                . "<i class='fa fa-print'></i>"
                . "</a>"
                . "<a id='$4 ($3)' href='" . site_url('products/gen_barcode/$3/$5') . "' title='" . lang("view_barcode") . "' class='barcode tip btn btn-primary btn-xs'><i class='fa fa-barcode'></i>
                 * </a>"
        
                $actions .= "<a class='tip image btn btn-primary btn-xs' id='$4 ($3)' href='" . base_url('uploads/$2') . "' title='" . lang("view_image") . "'>"
                . "<i class='fa fa-picture-o'></i>"
                . "</a>";
                 */
                
                if ($this->Admin || CODIGO_LOJA === 'ONLINE') {
                $actions .= "<a href='" . site_url('products/edit/$1') . "' title='" . lang("edit_product") . "' class='tip btn btn-warning btn-xs'>
                <i class='fa fa-edit'></i>
                </a>";
                }
                
                $actions .= "<a href='javascript:void(0);' title='Transferir Estoque' class='tip btn btn-primary btn-xs' onclick='getMdlSolicitarTransferencia(true, true,$1,\"$3\",\"$4\",\"$6\", \"\")'>"
                . "<i class='fa fa-exchange'></i>"
                . "</a>"
                /*
                . "<a href='" . site_url('products/delete/$1') . "' onClick=\"return confirm('" . lang('alert_x_product') . "')\" title='" . lang("delete_product") . "' class='tip btn btn-danger btn-xs'>"
                . "<i class='fa fa-trash-o'></i>"
                . "</a>"
                 */
                . "</div>"
                . "</div>";

        $this->datatables->add_column('Actions', $actions, "pid, image, code, pname, barcode_symbology, quantity");
        
        $this->datatables->unset_column('pid')->unset_column('barcode_symbology');
        echo $this->datatables->generate();
    }

    function view($id = NULL)
    {
        $data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        
        $product = $this->site->getProductByID($id);
        
        $this->data['product'] = $product;
        
        $this->data['category'] = $this->site->getCategoryByID($product->category_id);
        
        $this->data['combo_items'] = $product->type == 'combo' ? $this->products_model->getComboItemsByPID($id) : NULL;
        
        $this->data['variants'] = $this->products_model->getVariants($id);
        
        $this->load->view($this->theme . 'products/view', $this->data);
    }

    function barcode($product_code = NULL)
    {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }

        $data['product_details'] = $this->products_model->getProductByCode($product_code);
        $data['img'] = "<img src='" . base_url() . "index.php?products/gen_barcode&code={$product_code}' alt='{$product_code}' />";
        $this->load->view('barcode', $data);
    }

    function product_barcode($product_code = NULL, $bcs = 'code39', $height = 60)
    {
        if ($this->input->get('code')) {
            $product_code = $this->input->get('code');
        }
        return "<img src='" . base_url() . "products/gen_barcode/{$product_code}/{$bcs}/{$height}' alt='{$product_code}' />";
    }

    function gen_barcode($product_code = NULL, $bcs = 'code39', $height = 60, $text = 1)
    {
        $drawText = ($text != 1) ? FALSE : TRUE;
        $this->load->library('zend');
        $this->zend->load('Zend/Barcode');
        $barcodeOptions = array('text' => $product_code, 'barHeight' => $height, 'drawText' => $drawText);
        $rendererOptions = array('imageType' => 'png', 'horizontalPosition' => 'center', 'verticalPosition' => 'middle');
        $imageResource = Zend_Barcode::render($bcs, 'image', $barcodeOptions, $rendererOptions);
        return $imageResource;
    }


    function print_barcodes()
    {
        $this->load->library('pagination');

        $per_page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;

        $config['base_url'] = site_url('products/print_barcodes');
        $config['total_rows'] = $this->products_model->products_count();
        $config['per_page'] = 16;
        $config['num_links'] = 5;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);

        $products = $this->products_model->fetch_products($config['per_page'], $per_page);
        $r = 1;
        $html = "";
        $html .= '<table class="table table-bordered">
        <tbody><tr>';
        foreach ($products as $pr) {
            if ($r != 1) {
                $rw = (bool)($r & 1);
                $html .= $rw ? '</tr><tr>' : '';
            }
            $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 60) . '<br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $pr->price . '</span></td>';
            $r++;
        }
        $html .= '</tr></tbody>
        </table>';

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes");
        $this->load->view($this->theme . 'products/print_barcodes', $this->data);
    }

    function print_labels()
    {
        $this->load->library('pagination');

        $per_page = $this->input->get('per_page') ? $this->input->get('per_page') : 0;

        $config['base_url'] = site_url('products/print_labels');
        $config['total_rows'] = $this->products_model->products_count();
        $config['per_page'] = 10;
        $config['num_links'] = 5;

        $config['full_tag_open'] = '<ul class="pagination pagination-sm">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a>';
        $config['cur_tag_close'] = '</a></li>';

        $this->pagination->initialize($config);

        $products = $this->products_model->fetch_products($config['per_page'], $per_page);

        $html = "";

        foreach ($products as $pr) {
            $html .= '<div class="labels"><strong>' . $pr->name . '</strong><br>' . $this->product_barcode($pr->code, $pr->barcode_symbology, 25) . '<br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $pr->price . '</span></div>';
        }

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_labels");
        $this->load->view($this->theme . 'products/print_labels', $this->data);
    }

    function single_barcode($product_id = NULL)
    {

        $product = $this->site->getProductByID($product_id);

        $html = "";
        $html .= '<table class="table table-bordered">
        <tbody><tr>';
        if ($product->quantity > 0) {
            for ($r = 1; $r <= $product->quantity; $r++) {
                if ($r != 1) {
                    $rw = (bool)($r & 1);
                    $html .= $rw ? '</tr><tr>' : '';
                }
                $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $product->price . '</span></td>';
            }
        } else {
            for ($r = 1; $r <= 16; $r++) {
                if ($r != 1) {
                    $rw = (bool)($r & 1);
                    $html .= $rw ? '</tr><tr>' : '';
                }
                $html .= '<td><h4>' . $this->Settings->site_name . '</h4><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 60) . ' <br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $product->price . '</span></td>';
            }
        }
        $html .= '</tr></tbody>
        </table>';

        $this->data['html'] = $html;
        $this->data['page_title'] = lang("print_barcodes");
        $this->load->view($this->theme . 'products/single_barcode', $this->data);
    }

    function single_label($product_id = NULL, $warehouse_id = NULL)
    {

        $product = $this->site->getProductByID($product_id);
        $html = "";
        if ($product->quantity > 0) {
            for ($r = 1; $r <= $product->quantity; $r++) {
                $html .= '<div class="labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $product->price . '</span></div>';
            }
        } else {
            for ($r = 1; $r <= 10; $r++) {
                $html .= '<div class="labels"><strong>' . $product->name . '</strong><br>' . $this->product_barcode($product->code, $product->barcode_symbology, 25) . ' <br><span class="price">' . lang('price') . ': ' . $this->Settings->currency_prefix . ' ' . $product->price . '</span></div>';
            }
        }
        $this->data['html'] = $html;
        $this->data['page_title'] = lang("barcode_label");
        $this->load->view($this->theme . 'products/single_label', $this->data);
    }

/*
    function add()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        $this->form_validation->set_rules('code', lang("product_code"), 'trim|is_unique[products.code]|min_length[2]|max_length[50]|required|alpha_numeric');
        $this->form_validation->set_rules('name', lang("product_name"), 'required');
        $this->form_validation->set_rules('category', lang("category"), 'required');
        $this->form_validation->set_rules('price', lang("product_price"), 'required|is_numeric');
        $this->form_validation->set_rules('cost', lang("product_cost"), 'required|is_numeric');
        $this->form_validation->set_rules('product_tax', lang("product_tax"), 'required|is_numeric');
        $this->form_validation->set_rules('quantity', lang("quantity"), 'is_numeric');
        $this->form_validation->set_rules('alert_quantity', lang("alert_quantity"), 'is_numeric');

        if ($this->form_validation->run() == true) {

            $data = array(
                'type' => $this->input->post('type'),
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'category_id' => $this->input->post('category'),
                'price' => $this->input->post('price'),
                'cost' => $this->input->post('cost'),
                'tax' => $this->input->post('product_tax'),
                'tax_method' => $this->input->post('tax_method'),
                'quantity' => $this->input->post('quantity'),
                'alert_quantity' => $this->input->post('alert_quantity'),
                'details' => $this->input->post('details'),
            );

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/add", 'refresh');
                }

                $photo = $this->upload->file_name;
                $data['image'] = $photo;

                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->session->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/add");
                }
            }
            // $this->tec->print_arrays($data, $items);
        }

        if ($this->form_validation->run() == true && $this->products_model->addProduct($data, $items)) {

            $this->session->set_flashdata('message', lang("product_added"));
            redirect('products');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('add_product');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('add_product')));
            $meta = array('page_title' => lang('add_product'), 'bc' => $bc);
            $this->page_construct('products/add', $this->data, $meta);
        }
    }
*/
    function edit($id = NULL)
    {
        if (!$this->Admin && CODIGO_LOJA !== 'ONLINE') {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $pr_details = $this->site->getProductByID($id);
        
        if ($this->input->post('code') != $pr_details->code) {
            $this->form_validation->set_rules('code', lang("product_code"), 'is_unique[products.code]');
        }
        
        $this->form_validation->set_rules('category', lang("category"), 'required');
        $this->form_validation->set_rules('model', lang("Modelo"), 'required');
        //$this->form_validation->set_rules('material', lang("Material"), 'required');
        $this->form_validation->set_rules('season', lang("Estação"), 'required');
        
        if ($this->form_validation->run() == true) {

            $data = array(
                'category_id' => $this->input->post('category'),
                'model' => $this->input->post('model'),
                'material' => $this->input->post('material'),
                'stamp' => $this->input->post('stamp'),
                'manga' => $this->input->post('manga'),
                'season' => $this->input->post('season'),
            );

            if ($this->input->post('type') == 'combo') {
                $c = sizeof($_POST['combo_item_code']) - 1;
                for ($r = 0; $r <= $c; $r++) {
                    if (isset($_POST['combo_item_code'][$r]) && isset($_POST['combo_item_quantity'][$r])) {
                        $items[] = array(
                            'item_code' => $_POST['combo_item_code'][$r],
                            'quantity' => $_POST['combo_item_quantity'][$r]
                        );
                    }
                }
            } else {
                $items = array();
            }

            $quantity = 0;
            
            $variants = [];
            
            $vars = $this->input->post('variants');
            
            if ($vars) {
                foreach ($vars as $prop => $rows) {
                    foreach($rows as $i => $val) {
                        $variants[$i][$prop] = $val;
                        
                        if ($prop === 'quantity') {
                            $quantity += intval($val);
                        }
                    }
                }
            }
            
            $data['quantity'] = $quantity;
            
            if ($_FILES['userfile']['size'] > 0) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '500';
                $config['max_width'] = '800';
                $config['max_height'] = '800';
                $config['overwrite'] = FALSE;
                $config['encrypt_name'] = TRUE;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->upload->set_flashdata('error', $error);
                    redirect("products/edit/" . $id);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library'] = 'gd2';
                $config['source_image'] = 'uploads/' . $photo;
                $config['new_image'] = 'uploads/thumbs/' . $photo;
                $config['maintain_ratio'] = TRUE;
                $config['width'] = 110;
                $config['height'] = 110;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    $this->upload->set_flashdata('error', $this->image_lib->display_errors());
                    redirect("products/edit/" . $id);
                }
            } else {
                $photo = NULL;
            }
            
            $data['variants'] = count($variants);
        }
            
        if ($this->form_validation->run() == true && $this->products_model->updateProduct($id, $data, $items, $photo)) {
            $this->products_model->addVariants($id, $variants);
            $this->session->set_flashdata('message', lang("product_updated"));
            redirect("products");
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $product = $this->site->getProductByID($id);
            if ($product->type == 'combo') {
                $combo_items = $this->products_model->getComboItemsByPID($id);
                foreach ($combo_items as $combo_item) {
                    $cpr = $this->site->getProductByID($combo_item->id);
                    $cpr->qty = $combo_item->qty;
                    $items[] = array('id' => $cpr->id, 'row' => $cpr);
                }
                $this->data['items'] = $items;
            }
            $this->data['product'] = $product;
            
            $this->data['categories'] = $this->site->getAllCategories();
            
            $this->data['variants'] = $this->products_model->getVariants($id);
            
            $this->data['page_title'] = lang('edit_product');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('edit_product')));
            $meta = array('page_title' => lang('edit_product'), 'bc' => $bc);
            $this->page_construct('products/edit', $this->data, $meta);
        }
    }

    function import()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/import");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                //$keys = array('id', 'code', 'name', 'cost', 'tax', 'price', 'category');

                $keys = [
                    'id',
                    'code',
                    'name',
                    'category_code',
                    'price',
                    'image',
                    'tax',
                    'cost',
                    'tax_method',
                    'quantity',
                    'barcode_symbology',
                    'type',
                    'details',
                    'alert_quantity',
                ];

                $final = array();
                $erroFormato = false;
                foreach ($arrResult as $key => $value) {
                    if (count($keys) == count($value)) {

                        $final[] = array_combine($keys, $value);
                    } else {

                        $erroFormato = true;
                        break;
                    }
                }

                //var_export(sizeof($final));exit;

                if (sizeof($final) > 1500) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("products/import");
                }

                $arrCodes = [];

                if (!$erroFormato) {
                    foreach ($final as $csv_pr) {

                        if (in_array($csv_pr['code'], $arrCodes)) {
                            $this->session->set_flashdata('error', 'Código repetido: '.$csv_pr['code']);
                            redirect("products/import");
                            break;
                        } else {
                            array_push($arrCodes, $csv_pr['code']);
                        }

                        $csv_pr['id'] = (int)$csv_pr['id'];
                        $csv_pr['price'] = (float)$csv_pr['price'];
                        $csv_pr['category_code'] = (strlen($csv_pr['category_code']) == 1) ? '0' . $csv_pr['category_code'] : $csv_pr['category_code'];
                        $csv_pr['image'] = ($csv_pr['image']) ? $csv_pr['image'] : 'no_image.png';
                        $csv_pr['tax'] = ($csv_pr['tax']) ?  (float)$csv_pr['tax'] : 0;
                        $csv_pr['cost'] = ($csv_pr['cost']) ? (float)$csv_pr['cost'] : 0;
                        $csv_pr['tax_method'] = ($csv_pr['tax_method']) ? (int)$csv_pr['tax_method'] : 1;
                        $csv_pr['quantity'] = ($csv_pr['quantity']) ? (float)$csv_pr['quantity'] : 0.00;
                        $csv_pr['barcode_symbology'] = ($csv_pr['barcode_symbology']) ? $csv_pr['barcode_symbology'] : 'code39';
                        $csv_pr['type'] = ($csv_pr['type']) ? $csv_pr['type'] : 'standard';
                        $csv_pr['details'] = ($csv_pr['details']) ?  $csv_pr['details'] : NULL;
                        $csv_pr['alert_quantity'] = ($csv_pr['alert_quantity']) ?  (float)$csv_pr['alert_quantity'] : 0.00;

                        if ($this->products_model->getProductByCode($csv_pr['code'])) {
                            $this->session->set_flashdata('error', 'CÓDIGO JÁ CADASTRADO');
                            redirect("products/import");
                        }
                        if (!is_numeric($csv_pr['tax']) && $csv_pr['tax'] != NULL) {

                            $this->session->set_flashdata('error', lang("check_product_tax") . " (" . $csv_pr['tax'] . "). " . lang("tax_not_numeric"));
                            redirect("products/import");
                        }


                        if (!($category = $this->site->getCategoryByCode($csv_pr['category_code']))) {                          
                            $this->session->set_flashdata('error', 'CATEGORIA NÃO ENCONTRADA');
                            redirect("products/import");
                        }


                        $data[] = [
                            'id' => $csv_pr['id'],
                            'code' => $csv_pr['code'],
                            'name' => $csv_pr['name'],
                            'category_id' => $category->id,
                            'price' => $csv_pr['price'],
                            'image' => $csv_pr['image'],
                            'tax' => $csv_pr['tax'],
                            'cost' => $csv_pr['cost'],
                            'tax_method' => $csv_pr['tax_method'],
                            'quantity' => $csv_pr['quantity'],
                            'barcode_symbology' => $csv_pr['barcode_symbology'],
                            'type' => $csv_pr['type'],
                            'details' => $csv_pr['details'],
                            'alert_quantity' => $csv_pr['alert_quantity'],

                        ];
                    }
                } else {

                    $this->session->set_flashdata('error', 'Formato inválido');
                    redirect("products/import");
                }
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->add_products($data)) {

            $this->session->set_flashdata('message', lang("products_added"));
            redirect('products');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('import_products');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products')));
            $meta = array('page_title' => lang('import_products'), 'bc' => $bc);
            $this->page_construct('products/import', $this->data, $meta);
        }
    }

    function import_original()
    {
        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }
        $this->load->helper('security');
        $this->form_validation->set_rules('userfile', lang("upload_file"), 'xss_clean');

        if ($this->form_validation->run() == true) {
            if (DEMO) {
                $this->session->set_flashdata('warning', lang("disabled_in_demo"));
                redirect('pos');
            }

            if (isset($_FILES["userfile"])) {

                $this->load->library('upload');

                $config['upload_path'] = 'uploads/';
                $config['allowed_types'] = 'csv';
                $config['max_size'] = '500';
                $config['overwrite'] = TRUE;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect("products/import");
                }


                $csv = $this->upload->file_name;

                $arrResult = array();
                $handle = fopen("uploads/" . $csv, "r");
                if ($handle) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $arrResult[] = $row;
                    }
                    fclose($handle);
                }
                array_shift($arrResult);

                $keys = array('code', 'name', 'cost', 'tax', 'price', 'category');

                $final = array();
                foreach ($arrResult as $key => $value) {
                    $final[] = array_combine($keys, $value);
                }

                if (sizeof($final) > 1001) {
                    $this->session->set_flashdata('error', lang("more_than_allowed"));
                    redirect("products/import");
                }

                foreach ($final as $csv_pr) {
                    if ($this->products_model->getProductByCode($csv_pr['code'])) {
                        $this->session->set_flashdata('error', lang("check_product_code") . " (" . $csv_pr['code'] . "). " . lang("code_already_exist"));
                        redirect("products/import");
                    }
                    if (!is_numeric($csv_pr['tax'])) {
                        $this->session->set_flashdata('error', lang("check_product_tax") . " (" . $csv_pr['tax'] . "). " . lang("tax_not_numeric"));
                        redirect("products/import");
                    }
                    if (!($category = $this->site->getCategoryByCode($csv_pr['category']))) {
                        $this->session->set_flashdata('error', lang("check_category") . " (" . $csv_pr['category'] . "). " . lang("category_x_exist"));
                        redirect("products/import");
                    }
                    $data[] = array(
                        'type' => 'standard',
                        'code' => $csv_pr['code'],
                        'name' => $csv_pr['name'],
                        'cost' => $csv_pr['cost'],
                        'tax' => $csv_pr['tax'],
                        'price' => $csv_pr['price'],
                        'category_id' => $category->id
                    );
                }
                //print_r($data); die();
            }
        }

        if ($this->form_validation->run() == true && $this->products_model->add_products($data)) {

            $this->session->set_flashdata('message', lang("products_added"));
            redirect('products');
        } else {

            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['categories'] = $this->site->getAllCategories();
            $this->data['page_title'] = lang('import_products');
            $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => lang('import_products')));
            $meta = array('page_title' => lang('import_products'), 'bc' => $bc);
            $this->page_construct('products/import', $this->data, $meta);
        }
    }

    function delete($id = NULL)
    {
        if (DEMO) {
            $this->session->set_flashdata('error', lang('disabled_in_demo'));
            redirect(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : 'welcome');
        }

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        if (!$this->Admin) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect('pos');
        }

        if ($this->products_model->deleteProduct($id)) {
            $this->session->set_flashdata('message', lang("product_deleted"));
            redirect('products');
        }
    }

    function suggestions()
    {
        $term = $this->input->get('term', TRUE);

        $rows = $this->products_model->getProductNames($term);
        if ($rows) {
            foreach ($rows as $row) {
                $row->qty = 1;
                $pr[] = array('id' => str_replace(".", "", microtime(true)), 'item_id' => $row->id, 'label' => $row->name . " (" . $row->code . ")", 'row' => $row);
            }
            echo json_encode($pr);
        } else {
            echo json_encode(array(array('id' => 0, 'label' => lang('no_match_found'), 'value' => $term)));
        }
    }

    // tRANSFERENCIAS DE ESTOQUE
    function getLojas()
    {

        $arrLojas = $this->lojas_model->getAllLojas();
        $arr = [];

        foreach ($arrLojas as $arrL) {

            $arr[$arrL->cod] = $arrL;
        }

        $this->session->set_userdata('lojasSessao', $arr);
        echo json_encode(['dados' => $arr]);
    }

    function transferirestoque2()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $post = $this->input->post();
            $erro = false;
            $arrErros = "";
            $arrDados = [];

            foreach ($post['arrProdutos'] as $arrProduto) {
                
                $arrProtudoTransferir = $this->products_model->getByCode($arrProduto['cod_produto']);

                if ($arrProtudoTransferir) {

                    $qtd_atual_loja_origem = $arrProtudoTransferir->quantity;

                    if ($qtd_atual_loja_origem && ($qtd_atual_loja_origem > 0) && $arrProduto['qtd_transferir'] > 0) {

                        if ($arrProduto['qtd_transferir'] <= $qtd_atual_loja_origem) {

                            if ($post['cod_loja_destino'] != CODIGO_LOJA) {

                                $arrDados[] = [
                                    'id_produto' => $arrProtudoTransferir->id,
                                    'cod_produto' => $arrProduto['cod_produto'],
                                    'cod_loja_origem' => CODIGO_LOJA,
                                    'cod_loja_destino' => $post['cod_loja_destino'],
                                    'qtd_atual_loja_origem' => $qtd_atual_loja_origem,
                                    'qtd_transferir' => $arrProduto['qtd_transferir'],
                                    'nome_usuario_solicitante' => $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name')
                                ];
                            } else {

                                $erro = true;
                                $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Loja destino inválida";
                                break;
                            }
                        } else {

                            $erro = true;
                            $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Quantidade a transferir inválida'<br>";
                        }
                    } else {

                        $erro = true;
                        $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Estoque ou quantidade zerado deste produto'<br>";
                    }
                } else {
                    $erro = true;
                    $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - Produto inválido ou não existente'<br>";
                }
            }

            if (!$erro) {
                $resposta = json_decode(consumirApi('transferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrTransferencias' => $arrDados]));
                $msgFinal = "";

                if ($resposta->sucesso) {

                    foreach ($resposta->dados as $arrTransferencias) {

                        $qtd_apos_transferencia = (float)$arrTransferencias->dados->qtd_atual_loja_origem - (float)$arrTransferencias->dados->qtd_transferir;

                        if ($this->products_model->updateQuantity($arrTransferencias->dados->id_produto, $qtd_apos_transferencia)) {

                            $msgFinal .= "&bull; <b>SUCESSO!</b> - " . $arrTransferencias->msg;
                        } else {

                            $dados = [
                                'token' => TOKEN,
                                'cod_loja_origem' => CODIGO_LOJA,
                                'id_transferencia' => $resposta->dados->id_transferencia
                            ];

                            consumirApi('excluirTransferenciaEstoque', $dados);
                            $msgFinal .= "<font color='red'>&bull; <b>ERRO!</b> - " . $arrTransferencias->msg . '</font>';
                        }
                    }

                    $this->session->set_flashdata('message', "<b>VERIFIQUE A BAIXO SE A TRANSFERÊNCIA FOI BEM SUCEDIDA:</b><br><br>" . $msgFinal . "<br><br>" . "Para acompanhar o histórico de transferência acesse <a href='" . site_url('products/transferenciaestoque') . "'>PRODUTOS > TRANSFERÊNCIAS DE ESTOQUE</a>");
                    $this->excluirProdutosSessao();
                    redirect('products');
                } else {

                    $this->session->set_flashdata('error', $resposta->mensagem);
                    redirect('products');
                }
            } else {

                $this->session->set_flashdata('error', $arrErros);
                redirect('products');
            }
        }
    }

    function transferirestoque()
    {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $post = $this->input->post();
            $erro = false;
            $arrErros = "";
            $arrDados = [];

            foreach ($post['arrProdutos'] as $arrProduto) {
                
                $arrProtudoTransferir = $this->products_model->getByCode($arrProduto['cod_produto']);

                if ($arrProtudoTransferir) {
                
                    $qtd_atual_loja_origem = $arrProtudoTransferir->quantity;

                    if ($qtd_atual_loja_origem && ($qtd_atual_loja_origem > 0) && $arrProduto['qtd_transferir'] > 0) {

                        if ($arrProduto['qtd_transferir'] <= $qtd_atual_loja_origem) {

                            if ($post['cod_loja_destino'] != CODIGO_LOJA) {

                                $arrDados[] = [
                                    'id_produto' => $arrProtudoTransferir->id,
                                    'cod_produto' => $arrProtudoTransferir->ean,
                                    'code' => $arrProduto['cod_produto'],
                                    'cod_loja_origem' => CODIGO_LOJA,
                                    'cod_loja_destino' => $post['cod_loja_destino'],
                                    'qtd_atual_loja_origem' => $qtd_atual_loja_origem,
                                    'qtd_transferir' => $arrProduto['qtd_transferir'],
                                    'nome_usuario_solicitante' => $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name')
                                ];
                            } else {

                                $erro = true;
                                $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Loja destino inválida";
                                break;
                            }
                        } else {

                            $erro = true;
                            $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Quantidade a transferir inválida'<br>";
                        }
                    } else {

                        $erro = true;
                        $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - {$arrProtudoTransferir->name} QTD ENVIADA : {$arrProduto['qtd_transferir']} ESTOQUE ATUAL: {$arrProtudoTransferir->name} - Estoque ou quantidade zerado deste produto'<br>";
                    }
                } else {
                    $erro = true;
                    $arrErros .= "&bull; PRODUTO: {$arrProduto['cod']} - Produto inválido ou não existente'<br>";
                }
            }

            if (!$erro) {              
                $msgFinal = "<b>VERIFIQUE A BAIXO SE A TRANSFERÊNCIA FOI BEM SUCEDIDA:</b><br><br>";
                $arrTransferenciasEnviar = [];
                foreach ($arrDados as $arrTransferencia) {

                    $qtd_apos_transferencia = (float)$arrTransferencia['qtd_atual_loja_origem'] - (float)$arrTransferencia['qtd_transferir'];
                    $msg = "Origem: " . getTipoLD($arrTransferencia['cod_loja_origem']) . " {$arrTransferencia['cod_loja_origem']} &rarr; Destino: " . getTipoLD($arrTransferencia['cod_loja_destino']) . " {$arrTransferencia['cod_loja_destino']} - PRODUTO: {$arrTransferencia['cod_produto']} - QTD TRANSFERIDA: {$arrTransferencia['qtd_transferir']} - QTD FINAL EM ESTOQUE: {$qtd_apos_transferencia} <br>";

                    if ($this->products_model->updateQuantity($arrTransferencia['id_produto'], $qtd_apos_transferencia)) {

                        $arrTransferenciasEnviar[] = $arrTransferencia;
                        $msgFinal .= "&bull; <b>SUCESSO!</b> - " . $msg;
                    } else {

                        $msgFinal .= "&bull; <b>ERRO!</b> - " . $msg;
                    }
                }
                $this->excluirProdutosSessao();
                $resposta = json_decode(consumirApi('transferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrTransferencias' => $arrTransferenciasEnviar], ['msgContingencia' =>  $msgFinal]));
                
                if ($resposta->sucesso) {

                    $this->session->set_flashdata('message', "<b>VERIFIQUE A BAIXO SE A TRANSFERÊNCIA FOI BEM SUCEDIDA:</b><br><br>" . $resposta->mensagem . "<br><br>" . "Para acompanhar o histórico de transferência acesse <a href='" . site_url('products/transferenciaestoque') . "'>PRODUTOS > TRANSFERÊNCIAS DE ESTOQUE</a>");
                    $this->excluirProdutosSessao();
                    redirect('products/transferenciaestoque');
                } else {

                    $this->session->set_flashdata('error', $resposta->mensagem);
                    redirect('products/transferenciaestoque');
                }
            } else {

                $this->session->set_flashdata('error', $arrErros);
                redirect('products/transferenciaestoque');
            }
        }
    }

    function transferenciaestoque()
    {
        $this->data['page_title'] = 'Transferências de Estoque';
        $this->data['arrTransferencias'] = json_decode(consumirApi('listaTransferenciasEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'qtd_por_pg' => TRANSFERENCIAS_POR_PG]))->dados;

        $bc = array(array('link' => site_url('products'), 'page' => lang('products')), array('link' => '#', 'page' => 'Transferências de Estoque'));
        $meta = array('page_title' => 'Transferências de Estoque', 'bc' => $bc);
        $this->page_construct('products/transferenciaestoque', $this->data, $meta);
    }

    function cancelartransferenciaestoque($id = NULL)
    {

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }

        $arrTransferencia = json_decode(consumirApi('getRegistroTransferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'id_transferencia' => $id]));
        if ($arrTransferencia->sucesso) {
            $resposta = json_decode(consumirApi('cancelarTransferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'id_transferencia' => $id]));

            if ($resposta->sucesso) {
                $cod_produto = $arrTransferencia->dados->cod_produto;
                $qtd_transferir = $arrTransferencia->dados->qtd_transferir;

                $arrProduto = $this->products_model->getProductByCode($cod_produto);
                if ($arrProduto) {
                    $qtd_atualizada = (int) $arrProduto->quantity + (int)$qtd_transferir;

                    if ($this->products_model->updateProduct($arrProduto->id, ['quantity' => $qtd_atualizada])) {
                        $this->session->set_flashdata('message', $resposta->mensagem);
                    } else {
                        $this->session->set_flashdata('error', 'Não foi possível cancelar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde (Erro 001)');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Não foi possível cancelar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde (Erro 002)');
                }
            } else {
                $this->session->set_flashdata('error', 'Não foi possível cancelar a solicitação de transferência de estoque. Verifique se esssa solicitação já foi confirmada e por favor, tente novamente mais tarde (Erro 003)');
            }
        } else {
            $this->session->set_flashdata('error', 'Não foi possível cancelar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde (Erro 004)');
        }
        redirect('products/transferenciaestoque');
    }

    function gettransferenciaspendentes()
    {

        echo consumirApi('getTrasferenciasEstoquePendentes', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA]);
    }

    function confirmartransferenciaspendentes()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $post = $this->input->post();

            $arrTranferenciasPendentes = json_decode(consumirApi('getTrasferenciasEstoquePendentes', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrTransferencias' => json_decode($post['arrTransferenciasPendentes'])]));

            if ($arrTranferenciasPendentes->sucesso) {

                $arrDados = [];

                foreach ($arrTranferenciasPendentes->dados as $arr) {

                    $arrProduto = $this->products_model->getProductByCode($arr->cod_produto);

                    if ($arrProduto) {

                        $arrDados[] = [

                            'arrTransferenciaPendente' => $arr,
                            'id_produto' => $arrProduto->id,
                            'arrDadosUpdate' => [

                                'qtd_atual_loja_destino' => $arrProduto->quantity,
                                'nome_usuario_confirmacao' => $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'),
                                'status' => STATUS_TRANSFERENCIA_CONFIRMADA,
                                'data_confirmacao' => date('Ymdhis')

                            ]
                        ];
                    }
                }

                $arrResposta = json_decode(consumirApi('aprovarTransferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrTransferencias' => $arrDados]));

                if ($arrResposta->sucesso) {

                    $erro = false;

                    foreach ($arrResposta->dados as $arr) {

                        $arrProduto = $this->products_model->getProductByCode($arr->arrTransferenciaPendente->cod_produto);

                        if ($arrProduto) {
                            $quantity = (int)$arrProduto->quantity + (int)$arr->arrTransferenciaPendente->qtd_transferir;

                            $dadosUpdade = [
                                'quantity' => $quantity
                            ];

                            if (!$this->products_model->updateProduct($arrProduto->id, $dadosUpdade)) {

                                $erro = true;
                                break;
                            }
                        }
                    }

                    if (!$erro) {
                        $this->session->set_flashdata('message', 'Transferência(s) de estoque confirmada(s) com sucesso!');
                    } else {

                        $this->session->set_flashdata('error', 'Não foi possível confirmar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde (Erro 001)');
                    }
                }
            }
        }

        redirect('products/transferenciaestoque');
    }

    function getTotalTrasferenciasEstoqueRecebidasPendentes()
    {
        echo consumirApi('getTotalTrasferenciasEstoqueRecebidasPendentes', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA]);
    }

    function editarEnvioTransferenciaEstoque()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $post = $this->input->post();
            //var_export($post);exit;

            $arrProduto = $this->products_model->getProductByCode($post['cod_produto']);

            if ($arrProduto) {

                $qtd_trasnferir = (int)$post['qtd_transferir'];
                $qtd_atual = (int)$post['qtd_atual'];
                $id_transferencia = (int)$post['id_transferencia'];
                $qtd_atual_loja_origem = (int)$arrProduto->quantity + $qtd_atual;

                if ($arrProduto->quantity && ($qtd_atual_loja_origem > 0) && $qtd_trasnferir > 0) {

                    if ($qtd_trasnferir <= $qtd_atual_loja_origem) {

                        $arrResposta = json_decode(
                            consumirApi(
                                'editarQtdTransferenciaEstoque',
                                [
                                    'token' => TOKEN,
                                    'cod_loja_origem' => CODIGO_LOJA,
                                    'id_transferencia' => $id_transferencia,
                                    'qtd_transferir' =>  $qtd_trasnferir,
                                    'qtd_atual_loja_origem' => $qtd_atual_loja_origem
                                ]
                            )
                        );

                        if ($arrResposta->sucesso) {

                            $qtd_final = ((int)$arrProduto->quantity + $qtd_atual) - $qtd_trasnferir;

                            if ($this->products_model->updateProduct($arrProduto->id, ['quantity' => $qtd_final])) {

                                $this->session->set_flashdata('message', $arrResposta->mensagem);
                            } else {

                                $this->session->set_flashdata('error', 'Não foi possível editar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde');
                            }
                        } else {

                            $this->session->set_flashdata('error', 'Não foi possível editar a solicitação de transferência de estoque. Verifique se ela não foi confirmada e por favor, tente novamente mais tarde' . $arrResposta->mensagem);
                        }
                    } else {
                        $this->session->set_flashdata('error', 'Quantidade inválida');
                    }
                } else {

                    $this->session->set_flashdata('error', 'Quantidade inválida');
                }
            } else {

                $this->session->set_flashdata('error', 'Produto inválido');
            }
        }

        redirect('products/transferenciaestoque');
    }

    function confirmarTransferenciaEstoqueComErro()
    {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $post = $this->input->post();
            //var_export($post);exit;

            $arrProduto = $this->products_model->getProductByCode($post['cod_produto']);

            if ($arrProduto) {

                $qtd_corrigida = (int)$post['qtd_transferir'];
                $qtd_atual = (int)$post['qtd_atual'];
                $id_transferencia = (int)$post['id_transferencia'];

                $arrResposta = json_decode(
                    consumirApi(
                        'confirmarTransferenciaEstoqueComErro',
                        [
                            'token' => TOKEN,
                            'cod_loja_origem' => CODIGO_LOJA,
                            'id_transferencia' => $id_transferencia,
                            'dadosUpdate' => [
                                'qtd_erro' =>  $qtd_atual,
                                'qtd_transferir' =>  $qtd_corrigida,
                                'qtd_atual_loja_destino' => $arrProduto->quantity,
                                'nome_usuario_confirmacao' => $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'),
                                'status' => STATUS_TRANSFERENCIA_CONFIRMADA,
                                'data_confirmacao' => date('Ymdhis')
                            ]
                        ]
                    )
                );

                if ($arrResposta->sucesso) {

                    $qtd_final = (int)$arrProduto->quantity + $qtd_corrigida;

                    if ($this->products_model->updateProduct($arrProduto->id, ['quantity' => $qtd_final])) {

                        $this->session->set_flashdata('message', $arrResposta->mensagem);
                    } else {

                        $this->session->set_flashdata('error', 'Não foi possível editar a solicitação de transferência de estoque. Por favor, tente novamente mais tarde');
                    }
                } else {

                    $this->session->set_flashdata('error', 'Não foi possível editar a solicitação de transferência de estoque. Verifique se ela não foi confirmada e por favor, tente novamente mais tarde. ' . $arrResposta->mensagem);
                }
            } else {

                $this->session->set_flashdata('error', 'Produto inválido');
            }
        }

        redirect('products/transferenciaestoque');
    }

    function corrigeEstoqueComErro()
    {

        $arrTransferenciasEstoqueErro = json_decode(
            consumirApi(
                'getTransferenciaEstoqueComErro',
                [
                    'token' => TOKEN,
                    'cod_loja_origem' => CODIGO_LOJA,

                ]
            )
        );

        if ($arrTransferenciasEstoqueErro->dados) {
            $arr = [];
            foreach ($arrTransferenciasEstoqueErro->dados as $arrTransderencia) {

                $arrProduto = $this->products_model->getProductByCode($arrTransderencia->cod_produto);

                if ($arrProduto) {
                    $qtd_atual_loja_origem = (int)$arrProduto->quantity + $arrTransderencia->qtd_erro;
                    $qtd_final = $qtd_atual_loja_origem - $arrTransderencia->qtd_transferir;

                    if ($this->products_model->updateProduct($arrProduto->id, ['quantity' => $qtd_final])) {


                        $arrTransderencia->dadosUpdate = [

                            'qtd_atual_loja_origem' => $qtd_atual_loja_origem,
                            'qtd_erro' => NULL

                        ];

                        $arr[] = $arrTransderencia;
                    }
                }
            }

            consumirApi(
                'corrigirTransferenciaEstoqueComErro',
                [
                    'token' => TOKEN,
                    'cod_loja_origem' => CODIGO_LOJA,
                    'arrTransferencias' => $arr

                ]
            );
        }
    }

    function listaTransferenciasEstoque_maisRecebidas()
    {

        $arrPesquisa = [
            'start' => (int)$this->input->post('start', TRUE),
            'qtd_por_pg' =>  (int)$this->input->post('qtd_por_pg', TRUE),
            'pesquisa' => ($this->input->post('pesquisa', TRUE)) ? $this->input->post('pesquisa', TRUE) : null
        ];

        echo consumirApi('listaTransferenciasEstoque_maisRecebidas', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrPesquisa' => $arrPesquisa]);
    }

    function listaTransferenciasEstoque_maisEnviadas()
    {

        $arrPesquisa = [
            'start' => (int)$this->input->post('start', TRUE),
            'qtd_por_pg' =>  (int)$this->input->post('qtd_por_pg', TRUE),
            'pesquisa' => ($this->input->post('pesquisa', TRUE)) ? $this->input->post('pesquisa', TRUE) : null
        ];

        echo consumirApi('listaTransferenciasEstoque_maisEnviadas', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrPesquisa' => $arrPesquisa]);
    }

    function buscaProdutoByCodONome()
    {

        $termo = ($this->input->post('pesquisa', TRUE)) ? $this->input->post('pesquisa', TRUE) : false;

        if ($termo != '') {


            echo json_encode(['dados' => $this->products_model->getProductNames($termo)]);
        } else {

            echo json_encode(['dados' => false]);
        }
    }

    function salvaProdutosSessao()
    {

        $this->session->set_userdata('solicitacaoTransferencia', ['dados' =>  $this->input->post('produtos'), 'id' => (int)$this->input->post('id')]);
    }

    function getProdutosSessao()
    {

        echo json_encode($this->session->solicitacaoTransferencia);
    }

    function excluirProdutosSessao()
    {

        $this->session->unset_userdata('solicitacaoTransferencia');
    }

    function enviaRelatorioEstoque()
    {

        $arrResposta = json_decode(consumirApi('getSolicitacaoRelatorioEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA]));

        if ($arrResposta->sucesso) {

            if ($arrResposta->dados) {

                foreach ($arrResposta->dados as $arr) {

                    $arrRVs = $this->products_model->getRelatorioVendas($arr->data_i, $arr->data_f);
                    $arrRelatorioEstoque = [];

                    foreach ($arrRVs as $arrRV) {

                        $arrRelatorioEstoque[$arrRV->name]['qtd_vendas'] = $arrRV->qtd_total;
                    }

                    $arrREs = $this->products_model->getBy('tec_products.name, tec_products.quantity', 'quantity IS NOT NULL');


                    foreach ($arrREs as $arrRE) {

                        $arrRelatorioEstoque[$arrRE->name]['qtd_estoque'] = $arrRE->quantity;
                    }

                    $post = [
                        'token' => TOKEN,
                        'cod_loja_origem' => CODIGO_LOJA,
                        'id_relatorio' => $arr->id,
                        'json_envio' => json_encode($arrRelatorioEstoque)
                    ];


                    consumirApi('enviaRelatorioEstoque', $post);
                }
            }
        }
    }

    function getSolicitacaoEdicaoProduto() {
        
        $json = consumirApi('getSolicitacaoEdicaoProduto', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA]);

        echo $json;
        
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
                $ok = $this->products_model->addProduct($dados);
            }

            if ($ok) {
                $post = [
                    'token' => TOKEN,
                    'cod_loja_origem' => CODIGO_LOJA,
                    'id_produtos_edicoes' => $arr->id,
                ];
                
                consumirApi('enviaConfirmacaoEdicaoProduto', $post);
            }
        }
    }
    
    public function variants($id) {
        $rows = $this->products_model->getVariants($id);
        
        header('Content-Type: application/json');
        
        echo json_encode($rows);
    }

    public function sync() {
        $rows = $this->products_model->getForSync();

        foreach ($rows as $prod) {
            $post = [
                'token' => TOKEN,
                'cod_loja_origem' => CODIGO_LOJA,
                'code' => $prod->code,
                'ean' => $prod->ean,
                'quantity' => $prod->quantity
            ];

            echo consumirApi("syncEstoque", $post) . "\n";
        }
    }
    
    public function ajusteEstoque() {

        $post = [
            'token' => TOKEN,
            'cod_loja_origem' => CODIGO_LOJA
        ];

        $json = consumirApi("ajusteEstoque", $post);

        $resp = json_decode($json);

        if (!$resp->sucesso) {
            exit;
        }

        foreach ($resp->dados as $row) {
            $this->products_model->ajusteEstoque($row->cod_produto, $row->loja_ajuste);
            
            $post['id'] = $row->id;
            
            echo consumirApi("confirmaAjusteEstoque", $post);
        }
    }

    public function transferencia_variants()
    {

        $post = $this->input->post();

        if (empty($post['id_produto'])) {
            exit;
        }

        $quantity = 0;

        $id_produto = $post['id_produto'];

        $variants = [];

        $rows = $this->products_model->getVariants($id_produto);

        if ($rows) {
            foreach ($rows as $row) {
                $key            = md5($row->size.$row->color);
                $variants[$key] = [
                    'id' => $row->id,
                    'size' => $row->size,
                    'color' => $row->color,
                    'quantity' => intval($row->quantity)
                ];

                $quantity += intval($row->quantity);
            }
        }

        $post_variants = [];

        foreach ($post['variants'] as $prop => $rows) {
            foreach ($rows as $i => $val) {
                $post_variants[$i][$prop] = $val;
            }
        }

        foreach ($post_variants as $item) {

            $key = md5($item['size'].$item['color']);

            $quantity += intval($item['quantity']);

            if (isset($variants[$key])) {
                $variants[$key]['quantity'] += intval($item['quantity']);
                continue;
            }

            $variants[$key] = $item;
        }

        $this->products_model->addVariants($id_produto, $variants);

        $this->products_model->updateQuantity($id_produto, $quantity);
        
        $arrDados[] = [
            'arrTransferenciaPendente' => [
                'id' => $post['id']
            ],
            'id_produto' => $id_produto,
            'arrDadosUpdate' => [
                'qtd_atual_loja_destino' => $quantity,
                'nome_usuario_confirmacao' => $this->session->userdata('first_name').' '.$this->session->userdata('last_name'),
                'status' => STATUS_TRANSFERENCIA_CONFIRMADA,
                'data_confirmacao' => date('Ymdhis')
            ]
        ];

        consumirApi('aprovarTransferenciaEstoque', ['token' => TOKEN, 'cod_loja_origem' => CODIGO_LOJA, 'arrTransferencias' => $arrDados]);
    }
}
