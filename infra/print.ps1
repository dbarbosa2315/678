$cupom = $args[0]

$pdf_1 = $cupom + ".pdf"
$pdf_2 = $cupom + "_copy.pdf"

Copy-Item -Path $pdf_1 -Destination $pdf_2

Start-Process -FilePath $pdf_1 -WindowStyle Minimized -Verb Print

Start-Process -FilePath $pdf_2 -WindowStyle Minimized -Verb Print