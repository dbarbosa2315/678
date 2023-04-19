<?php

class PrintCupom extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->load->library("gearmanworker");

        $this->gearmanworker->init("print_cupom", [$this, "process"]);

        while ($this->gearmanworker->work()) {
            
        }
    }

    public function process($job) {
        $data = json_decode($job->getWorkload());

        if (!$data) {
            return;
        }

        $start = time();

        $script = FCPATH . "infra/phantomjs/screenshot.js";

        $url = base64_encode(site_url("pos/view/$data->id/1"));

        $file = base64_encode(FCPATH . "infra/tmp/cupom_$data->id.pdf");

        exec("infra/phantomjs/bin/phantomjs.exe $script $url $file");

        exec("powershell.exe -InputFormat none -File print.ps1 -Pdf " . FCPATH . "infra/tmp/cupom_$data->id.pdf");
        
        $end = time() - $start;

        echo "Time: $end\n";
    }

}
