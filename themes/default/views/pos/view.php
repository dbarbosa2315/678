<?php


function product_name($name)
{
    return character_limiter($name, (isset($Settings->char_per_line) ? ($Settings->char_per_line-8) : 35));
}

if ($modal) {
    echo '<div class="modal-dialog no-modal-header"><div class="modal-content"><div class="modal-body"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i></button>';
} else { ?>
    <!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= $page_title . " " . lang("no") . " " . $inv->id; ?></title>
        <base href="<?= base_url() ?>"/>
        <meta http-equiv="cache-control" content="max-age=0"/>
        <meta http-equiv="cache-control" content="no-cache"/>
        <meta http-equiv="expires" content="0"/>
        <meta http-equiv="pragma" content="no-cache"/>
        <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
        <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
        <style type="text/css" media="all">
            body { color: #000;}
            #wrapper { max-width: 480px; margin: 0 auto; padding-top: 20px; }
            .btn { border-radius: 0; margin-bottom: 5px; }
            .table { border-radius: 3px; }
            .table th { background: #f5f5f5; }
            .table th, .table td { vertical-align: middle !important;}
            h3 { margin: 5px 0; }
            .table>thead>tr>th, .table>tfoot>tr>td  {
                border: none;
            }
            .table>tbody>tr>td {
                border: none;
            }
            #items thead th{
                border-bottom: 1px solid #666;
            }
            
            td.soma_total {font-weight: bold}
            
            @media print {
                .no-print { display: none; }
            }
                         
            @media (max-width: 400px) {
                .no-print { display: none; }
                #wrapper {  
                    margin: 0 0 0 0; 
                    padding: 0 0 0 0; 
                    width: 79.95mm;
                }
                #receipt-data .text-header {font-size: 10px;}
                #receipt-data .text-header h2 {font-size: 20px;}
                #receipt-data .text-header p {font-size: 10px;}
                .well.well-sm {font-size: 10px;}
                table.print {font-size: 9px; }
                td.soma_total {font-size: 12px; }
            
                body {
                    width: 80mm;
                    height: 297mm;
                    margin: 0; 
                }
            }
            
            @page {
                size: 80mm 297mm;
                margin: 0; 
             }
        </style>
    </head>

    <body>

<?php } ?>
<div id="wrapper">
    <div id="receiptData">
    <div class="no-print">
        <?php if ($message) { ?>
            <div class="alert alert-success">
                <button data-dismiss="alert" class="close" type="button">×</button>
                <?= is_array($message) ? print_r($message, true) : $message; ?>
            </div>
        <?php } ?>
    </div>
    <div id="receipt-data">
        <div class="text-center text-header">
                <?= $Settings->header; ?>
                <p>
                    Venda N&ordm;: <?= $inv->seq_id ?>
                    <br/>
                    <?php   
                        echo lang("customer") . ': ';
                        if ($customer) {
                            echo $customer->name; 
                        }
                    ?>
                    <br/>
                    Vendedor(a): <?= $seller->name ?>
                    <br/>
                    <?= lang("date").': '.$this->tec->hrld($inv->date); ?>
                </p>
            <div style="clear:both;"></div>
            <table id="items" class="table table-condensed print">
                <thead>
                    <tr>
                        <th class="text-left">Cod.</th>
                        <th class="text-center"><?=lang('quantity');?></th>
                        <?php if(CODIGO_LOJA === 'ONLINE'): ?>
                            <th class="text-center">Tam.</th>
                            <th class="text-center">Cor</th>
                        <?php endif;?>
                        <th class="text-right"><?=lang('price');?></th>
                        <th style="width: 70px"></th>
                        <th class="text-right"><?=lang('subtotal');?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $tax_summary = array();
                $total_items = 0;
                
                $subtotal = 0;
                
                foreach ($rows as $row) {
                    $total_items += $row->quantity;
                    
                    if ($row->discount < 0) {
                        $row->real_unit_price -= $row->discount;
                    }
                    
                    $subtotal += $row->net_unit_price * $row->quantity;
                    
                    $color = substr($row->vcolor, 0, 4);
                    
                    $parts = explode(' ', $row->vcolor);
                    
                    if (count($parts) >= 2) {
                        $color = substr(array_shift($parts), 0, 3) . '. ' . substr(array_pop($parts), 0, 2);
                    }
                    
                    echo '<tr>'
                        . '<td class="text-left">' .$row->product_code . '</td>'
                        . '<td class="text-center">' . intval($row->quantity) . '</td>';

                    if (CODIGO_LOJA === 'ONLINE') {
                        echo '<td class="text-center">' . $row->vsize . '</td>'
                            . '<td class="text-center">' . $color . '.</td>';
                    }

                    echo '<td class="text-right"  style="white-space: nowrap;">';
                    
                    echo $this->tec->formatMoneyBR($row->real_unit_price);
                    
                    echo '</td>';
                    
                    $ld = '<td></td>';
                    
                    if ($row->discount > 0) {
                        $ld = '<td>-' . $this->tec->formatMoneyBR($row->discount) . '</td>';
                    }
                    
                    if ($row->exchange_val > 0) {
                        $ld = '<td>Troca</td>';
                    }
                    
                    echo $ld;
                    
                    echo '<td class="text-right" style="white-space: nowrap;">' . $this->tec->formatMoneyBR($row->net_unit_price * $row->quantity) . '</td>'
                    . '</tr>';
                }
                ?>
                     <tr>
                        <td style="border-top: 1px solid #666"></td>
                        <td style="border-top: 1px solid #666"></td>
                        <?php if(CODIGO_LOJA === 'ONLINE'): ?>
                            <td colspan="5" style="text-align: right; border-top: 1px solid #666">
                                <?= $this->tec->formatMoneyBR($subtotal); ?>
                            </td>
                        <?php else: ?>
                            <td colspan="3" style="text-align: right; border-top: 1px solid #666">
                                <?= $this->tec->formatMoneyBR($subtotal); ?>
                            </td>
                        <?php endif;?>
                    </tr>
                    
                </tbody>
                <tfoot>
                <?php
                                
            if ($inv->grand_total > $inv->total) {
                    echo '<tr>'
                    . '<td colspan="2" style="text-align: left; border-top: 1px solid #666">' . lang("order_tax") . '</td>';

                    if (CODIGO_LOJA === 'ONLINE') {
                        echo '<td colspan="5" style="text-align: right; border-top: 1px solid #666">' . $this->tec->formatMoneyBR($inv->grand_total - $inv->total) . '</td>';
                    } else {
                        echo '<td colspan="3" style="text-align: right; border-top: 1px solid #666">' . $this->tec->formatMoneyBR($inv->grand_total - $inv->total) . '</td>';
                    }

                    echo ' </tr>';
                }

                echo '<tr style="background-color: #f9f9f9;">
                        <td class="text-left soma_total">Total</td>
                        <td class="text-center soma_total">'.intval($total_items) .'</td>';
                if (CODIGO_LOJA === 'ONLINE') {
                    echo '<td colspan="5" class="text-right soma_total">'.$this->tec->formatMoneyBR($inv->grand_total).'</td>';
                } else {
                    echo '<td colspan="3" class="text-right soma_total">'.$this->tec->formatMoneyBR($inv->grand_total).'</td>';
                }
                
                echo '</tr>';
                ?>
                </tfoot>
            </table>
            <?php
           // if ($payments) {
                echo '<table class="table table-condensed print"><tbody>';
                //foreach ($payments as $payment) {
                    
                    if ($inv->total_cash > 0) {
                        echo '<tr>';
                        echo '<td align="left">' . lang("cash") . '</td>';
                        echo '<td align="right">' . $this->tec->formatMoneyBR($inv->total_cash) . '</td>';
                        echo '</tr>';
                    }
                    
                    if ($inv->total_credit > 0) {
                        $instalments = $inv->instalments_credit . "x";
                        
                        echo '<tr>';
                        echo '<td align="left">' . lang("cc") . ' ' . $instalments . '</td>';
                        echo '<td align="right">' . $this->tec->formatMoneyBR($inv->total_credit) . '</td>';
                        echo '</tr>';
                    }
                    
                    if ($inv->total_debit > 0) {
                        echo '<tr>';
                        echo '<td align="left">' . lang("stripe") . '</td>';
                        echo '<td align="right">' . $this->tec->formatMoneyBR($inv->total_debit) . '</td>';
                        echo '</tr>';
                    }
                    
                    if ($inv->total_transfer > 0) {
                        echo '<tr>';
                        echo '<td align="left">Depósito</td>';
                        echo '<td align="right">' . $this->tec->formatMoneyBR($inv->total_transfer) . '</td>';
                        echo '</tr>';
                    }
                     
                    /*
                    if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': ' . $this->tec->formatMoneyBR($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("no") . ': ' . 'xxxx xxxx xxxx ' . substr($payment->cc_no, -4) . '</td>';
                        echo '<td>' . lang("name") . ': ' . $payment->cc_holder . '</td>';
                    }
                    if ($payment->paid_by == 'Cheque' && $payment->cheque_no) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': ' . $this->tec->formatMoneyBR($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("cheque_no") . ': ' . $payment->cheque_no . '</td>';
                    }
                    if ($payment->paid_by == 'gift_card' && $payment->pos_paid) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("no") . ': ' . $payment->gc_no . '</td>';
                        echo '<td>' . lang("amount") . ': ' . $this->tec->formatMoneyBR($payment->pos_paid) . '</td>';
                        echo '<td>' . lang("balance") . ': ' . ($payment->pos_balance > 0 ? $this->tec->formatMoneyBR($payment->pos_balance) : 0) . '</td>';
                    }
                    if ($payment->paid_by == 'other' && $payment->amount) {
                        echo '<td>' . lang("paid_by") . ': ' . lang($payment->paid_by) . '</td>';
                        echo '<td>' . lang("amount") . ': ' . $this->tec->formatMoneyBR($payment->pos_paid == 0 ? $payment->amount : $payment->pos_paid) . '</td>';
                        echo $payment->note ? '</tr><td colspan="2">' . lang("payment_note") . ': ' . $payment->note . '</td>' : '';
                    }*/
                    
                    
                //}
                echo '</tbody></table>';                
            //}

            ?>

            <?= $inv->note ? '<p class="text-center">' . $this->tec->decode_html($inv->note) . '</p>' : ''; ?>
            <div class="well well-sm">
                <?= $Settings->footer; ?>
            </div>
        </div>
        <div style="clear:both;"></div>
    </div>
<?php if ($modal) {
    echo '</div></div></div></div>';
} else { ?>
<div id="buttons" style="padding-top:10px; text-transform:uppercase;" class="no-print">
    <hr>
    <?php if ($message) { ?>
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <?= is_array($message) ? print_r($message, true) : $message; ?>
    </div>
<?php } ?>

    <?php if ($Settings->java_applet) { ?>
        <span class="col-xs-12"><a class="btn btn-block btn-primary" onClick="printReceipt()"><?= lang("print"); ?></a></span>
        <span class="col-xs-12"><a class="btn btn-block btn-info" type="button" onClick="openCashDrawer()"><?= lang('open_cash_drawer'); ?></a></span>
        <div style="clear:both;"></div>
    <?php } else { ?>
        <span class="pull-right col-xs-12">
        <a href="javascript:window.print()" id="web_print" class="btn btn-block btn-primary"
           onClick="window.print();return false;"><?= lang("web_print"); ?></a>
    </span>
    <?php } ?>
    <span class="pull-left col-xs-12"><a class="btn btn-block btn-success" href="#" id="email"><?= lang("email"); ?></a></span>

    <span class="col-xs-12">
        <a class="btn btn-block btn-warning" href="<?= site_url('pos'); ?>"><?= lang("back_to_pos"); ?></a>
    </span>
    <?php if (!$Settings->java_applet) { ?>
        <div style="clear:both;"></div>
        <div class="col-xs-12" style="background:#F5F5F5; padding:10px;">
            <font size="-2">
            <p style="font-weight:bold;">Favor alterar as configurações de impressão de seu browser</p>
            <p style="text-transform: capitalize;"><strong>FireFox:</strong> Arquivo &gt; Configurar impressora &gt; Margem &amp;Cabeçalho/Rodapé --Nenhum--</p>
            <p style="text-transform: capitalize;"><strong>Chrome:</strong> Menu &gt; Impressora &gt; Disabilitar Cabeçalho/Rodapé Opções &amp; Setar margem em branco</p></div>
            <font>
    <?php } ?>
    <div style="clear:both;"></div>

</div>

</div>
<canvas id="hidden_screenshot" style="display:none;">

</canvas>
<div class="canvas_con" style="display:none;"></div>
<script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
<?php if ($Settings->java_applet) {

        function drawLine($Settings)
        {
            $size = $Settings->char_per_line;
            $new = '';
            for ($i = 1; $i < $size; $i++) {
                $new .= '-';
            }
            $new .= ' ';
            return $new;
        }

        function printLine($str, $Settings, $sep = ":", $space = NULL)
        {
            $size = $space ? $space : $Settings->char_per_line;
            $lenght = strlen($str);
            list($first, $second) = explode(":", $str, 2);
            $new = $first . ($sep == ":" ? $sep : '');
            for ($i = 1; $i < ($size - $lenght); $i++) {
                $new .= ' ';
            }
            $new .= ($sep != ":" ? $sep : '') . $second;
            return $new;
        }

        function printText($text, $Settings)
        {
            $size = $Settings->char_per_line;
            $new = wordwrap($text, $size, "\\n");
            return $new;
        }

        function taxLine($name, $code, $qty, $amt, $tax)
        {
            return printLine(printLine(printLine(printLine($name . ':' . $code, '', 18) . ':' . $qty, '', 25) . ':' . $amt, '', 35) . ':' . $tax, ' ');
        }

        ?>

        <script type="text/javascript" src="<?= $assets ?>plugins/qz/js/deployJava.js"></script>
        <script type="text/javascript" src="<?= $assets ?>plugins/qz/qz-functions.js"></script>
        <script type="text/javascript">
            deployQZ('themes/<?=$Settings->theme?>/assets/plugins/qz/qz-print.jar', '<?= $assets ?>plugins/qz/qz-print_jnlp.jnlp');
            usePrinter("<?= $Settings->receipt_printer; ?>");
            <?php /*$image = $this->tec->save_barcode($inv->reference_no);*/ ?>
            function printReceipt() {
                //var barcode = 'data:image/png;base64,<?php /*echo $image;*/ ?>';
                receipt = "";
                receipt += chr(27) + chr(69) + "\r" + chr(27) + "\x61" + "\x31\r";
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $Settings->header)), $Settings); ?>" + "\n";
                receipt += " \x1B\x45\x0A\r ";
                receipt += "<?=drawLine($Settings);?>\r\n";
                //receipt += "<?php // if($Settings->invoice_view == 1) { echo lang('tax_invoice'); } ?>\r\n";
                //receipt += "<?php // if($Settings->invoice_view == 1) { echo drawLine(); } ?>\r\n";
                receipt += "\x1B\x61\x30";
                receipt += "<?= printLine(lang("sale_no") . ": " . $inv->id, $Settings) ?>" + "\n";
                receipt += "<?= printLine(lang("sales_person") . ": " . $created_by->first_name." ".$created_by->last_name, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("customer") . ": " . $inv->customer_name, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("date") . ": " . $this->tec->hrld($inv->date), $Settings); ?>" + "\n\n";
                receipt += "<?php $r = 1;
            foreach ($rows as $row): ?>";
                receipt += "<?= "#" . $r ." "; ?>";
                receipt += "<?= product_name(addslashes($row->product_name)); ?>" + "\n";
                receipt += "<?= printLine($this->tec->formatNumber($row->quantity)."x".$this->tec->formatMoneyBR($row->net_unit_price+($row->item_tax/$row->quantity)) . ":  ". $this->tec->formatMoneyBR($row->subtotal), $Settings, ' ') . ""; ?>" + "\n";
                receipt += "<?php $r++;
            endforeach; ?>";
                receipt += "\x1B\x61\x31";
                receipt += "<?=drawLine($Settings);?>\r\n";
                receipt += "\x1B\x61\x30";
                receipt += "<?= printLine(lang("total") . ": " . $this->tec->formatMoneyBR($inv->total+$inv->product_tax), $Settings); ?>" + "\n";
                <?php if ($inv->order_tax != 0) { ?>
                receipt += "<?= printLine(lang("tax") . ": " . $this->tec->formatMoneyBR($inv->order_tax), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if ($inv->total_discount != 0) { ?>
                receipt += "<?= printLine(lang("discount") . ": " . $this->tec->formatMoneyBR($inv->total_discount), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if($Settings->rounding) { ?>
                receipt += "<?= printLine(lang("rounding") . ": " . $rounding, $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoneyBR($inv->grand_total + $rounding), $Settings); ?>" + "\n";
                <?php } else { ?>
                receipt += "<?= printLine(lang("grand_total") . ": " . $this->tec->formatMoneyBR($inv->grand_total), $Settings); ?>" + "\n";
                <?php } ?>
                <?php if($inv->paid < $inv->grand_total) { ?>
                receipt += "<?= printLine(lang("paid_amount") . ": " . $this->tec->formatMoneyBR($inv->paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("due_amount") . ": " . $this->tec->formatMoneyBR($inv->grand_total-$inv->paid), $Settings); ?>" + "\n\n";
                <?php } ?>
                <?php
                if($payments) {
                    foreach($payments as $payment) {
                        if ($payment->paid_by == 'cash' && $payment->pos_paid) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoneyBR($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("change") . ": " . ($payment->pos_balance > 0 ? $this->tec->formatMoneyBR($payment->pos_balance) : 0), $Settings); ?>" + "\n";
                <?php } if (($payment->paid_by == 'CC' || $payment->paid_by == 'ppp' || $payment->paid_by == 'stripe') && $payment->cc_no) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoneyBR($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("card_no") . ": xxxx xxxx xxxx " . substr($payment->cc_no, -4), $Settings); ?>" + "\n";
                <?php  } if ($payment->paid_by == 'gift_card') { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoneyBR($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("card_no") . ": " . $payment->gc_no, $Settings); ?>" + "\n";
                <?php } if ($payment->paid_by == 'Cheque' && $payment->cheque_no) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoneyBR($payment->pos_paid), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("cheque_no") . ": " . $payment->cheque_no, $Settings); ?>" + "\n";
                <?php if ($payment->paid_by == 'other' && $payment->amount) { ?>
                receipt += "<?= printLine(lang("paid_by") . ": " . lang($payment->paid_by), $Settings); ?>" + "\n";
                receipt += "<?= printLine(lang("amount") . ": " . $this->tec->formatMoneyBR($payment->amount), $Settings); ?>" + "\n";
                receipt += "<?= printText(lang("payment_note") . ": " . $payment->note, $Settings); ?>" + "\n";
                <?php }
            }

        }
    }

    /* if($Settings->invoice_view == 1) {
        if(!empty($tax_summary)) {
    ?>
                receipt += "\n" + "<?= lang('tax_summary'); ?>" + "\n";
                receipt += "<?= taxLine(lang('name'),lang('code'),lang('qty'),lang('tax_excl'),lang('tax_amt')); ?>" + "\n";
                receipt += "<?php foreach ($tax_summary as $summary): ?>";
                receipt += "<?= taxLine($summary['name'],$summary['code'],$this->tec->formatNumber($summary['items']),$this->tec->formatMoneyBR($summary['amt']),$this->tec->formatMoneyBR($summary['tax'])); ?>" + "\n";
                receipt += "<?php endforeach; ?>";
                receipt += "<?= printLine(lang("total_tax_amount") . ":" . $this->tec->formatMoneyBR($inv->product_tax)); ?>" + "\n";
                <?php
                    }
                } */
                ?>
                receipt += "\x1B\x61\x31";
                <?php if ($inv->note) { ?>
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $this->tec->decode_html($inv->note))), $Settings); ?>" + "\n";
                <?php } ?>
                receipt += "<?= printText(strip_tags(preg_replace('/\s+/',' ', $Settings->footer)), $Settings); ?>" + "\n";
                receipt += "\x1B\x61\x30";
                <?php if(isset($Settings->cash_drawer_cose)) { ?>
                print(receipt, '', '<?=$Settings->cash_drawer_cose;?>');
                <?php } else { ?>
                print(receipt, '', '');
                <?php } ?>

            }

        </script>
    <?php } ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#email').click(function () {
                      
                    });
                });

            </script>
            
            <?php if($noprint === null):?>
            <script type="text/javascript" src="<?= $assets ?>plugins/sweetalert2/sweetalert2.js"></script>
            <script>
                        Swal.fire({
                            icon: 'info',
                            title: 'Imprimindo cupom',
                            didOpen: function() {
                                Swal.showLoading();
                            }
                        });
                        
                        $.get("<?= site_url('pos/printCupom/' . $sid) ?>", function (code) {
                                    if (code === "0") {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Finalizado',
                                            showConfirmButton: false,
                                            timer: 2000
                                        }).then(function(r) {
                                            location.href = "<?= site_url('pos') ?>";
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Falha na impressão'
                                        }).then(function(r) {
                                            setTimeout(function () {
                                                window.print();
                                            }, 500);
                                        });
                                    }
                        }); 
                                
                        localStorage.clear();
                    </script>
            <?php endif;?>
</body>
</html>
<?php } ?>
