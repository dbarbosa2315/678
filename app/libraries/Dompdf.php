<?php

require __DIR__ . '/dompdf/autoload.inc.php';

class Dompdf {

    private $dompdf;

    public function __construct() {
        $this->dompdf = new Dompdf\Dompdf();
    }

    public function save($html, $pdf) {
        
        $this->dompdf->loadHtml($html);
                
        $this->dompdf->set_option('defaultMediaType', 'print');
        $this->dompdf->set_option('isFontSubsettingEnabled', true);

        $this->dompdf->render();
        
        $output = $this->dompdf->output();
        
        file_put_contents($pdf, $output);
    }

}
