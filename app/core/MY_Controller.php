<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    private $group_acl = [
        'cancela-venda' => [
            'sales',
            'sales/*',
            'pos/view/*',
            'users/profile/*',
            'auth/edit_user/*',
            'logout'
        ]
    ];
    
    function __construct() {
        parent::__construct();
        define("DEMO", 0);
        
        $this->check_group_access();
        
        $this->Settings = $this->site->getSettings();
        $this->lang->load('app', $this->Settings->language);
        $this->Settings->pin_code = $this->Settings->pin_code ? md5($this->Settings->pin_code) : NULL;
        $this->theme = $this->Settings->theme.'/views/';
        $this->data['assets'] = base_url() . 'themes/default/assets/';
        $this->data['Settings'] = $this->Settings;
        $this->loggedIn = $this->tec->logged_in();
        $this->data['loggedIn'] = $this->loggedIn;
        $this->data['categories'] = $this->site->getAllCategories();
        
        $this->data['user_group'] = $this->session->userdata('group_name');
        
        $this->Admin = ($this->data['user_group'] === 'admin');

        $this->data['Admin'] = $this->Admin;
        
        $this->m = strtolower($this->router->fetch_class());
        $this->v = strtolower($this->router->fetch_method());
        $this->data['m']= $this->m;
        $this->data['v'] = $this->v;

    }

    function page_construct($page, $data = array(), $meta = array()) {
        if(empty($meta)) { $meta['page_title'] = $data['page_title']; }
        $meta['message'] = isset($data['message']) ? $data['message'] : $this->session->flashdata('message');
        $meta['error'] = isset($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $meta['warning'] = isset($data['warning']) ? $data['warning'] : $this->session->flashdata('warning');
        $meta['ip_address'] = $this->input->ip_address();
        $meta['Admin'] = $data['Admin'];
        $meta['user_group'] = $data['user_group'];
        $meta['loggedIn'] = $data['loggedIn'];
        $meta['Settings'] = $data['Settings'];
        $meta['assets'] = $data['assets'];
        $meta['suspended_sales'] = $this->site->getUserSuspenedSales();
        $meta['qty_alert_num'] = $this->site->getQtyAlerts();
        $this->load->view($this->theme . 'header', $meta);
        $this->load->view($this->theme . $page, $data);
        $this->load->view($this->theme . 'footer');
    }
    
    private function check_group_access() {

        $group_name = $this->session->userdata('group_name');

        if (!isset($this->group_acl[$group_name])) {
            return;
        }

        $acl = $this->group_acl[$group_name];

        $uri = $this->router->uri->uri_string;

        if (empty($uri)) {
            redirect($acl[0]);
        }

        if (in_array($uri, $acl)) {
            return;
        }

        foreach ($acl as $path) {
            if (preg_match("%^$path%", $uri)) {
                return;
            }
        }
        
        //$this->session->set_flashdata('error', lang('access_denied'));

        redirect($acl[0]);
    }

}
