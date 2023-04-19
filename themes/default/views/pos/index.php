<?php (defined('BASEPATH')) or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

    <head>
        <meta charset="UTF-8">
        <title><?= $page_title . ' | ' . $Settings->site_name; ?></title>
        <link rel="shortcut icon" href="<?= $assets ?>img/icon.png" />
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/iCheck/square/green.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>plugins/redactor/redactor.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>dist/css/jquery-ui.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
        <link href="<?= $assets ?>dist/css/custom.css" rel="stylesheet" type="text/css" />

        <style>
            .sname {
                font-size: 16px;
                color: #333;
            }
            .ui-autocomplete {
                 max-height: 400px;
                 overflow-x: hidden;
                 overflow-y: auto;
            } 
            .pos .contents #item-list .btn-both span {
                white-space: nowrap;
            }
            #posTable tbody td {
                border-bottom: 1px solid #d2d6de;
            }
            .pos .input-qty {
                width: 50px;
                margin-left: 20px;
            }
            .info td {
                font-weight: bold;
            }
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }

            /* Firefox */
            input[type=number] {
              -moz-appearance: textfield;
            }
        </style>
        <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>

    </head>

    <?php include(getcwd() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_comunicacao.php') ?>

    <body class="skin-green sidebar-collapse sidebar-mini pos">

        <?= form_open('pos', 'id="pos-sale-form"'); ?>
        <div class="wrapper">

            <header class="main-header">

                <nav class="navbar navbar-static-top" role="navigation">
                    <ul class="nav navbar-nav pull-left">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->language; ?>.png" alt="<?= $Settings->language; ?>"></a>
                            <ul class="dropdown-menu">
                                <?php
                                $scanned_lang_dir = array_map(function ($path) {
                                    return basename($path);
                                }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                                foreach ($scanned_lang_dir as $entry) {
                                    ?>
                                    <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img src="<?= $assets; ?>images/<?= $entry; ?>.png" class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                    <div class="navbar-custom-menu">
                        <ul class="nav navbar-nav">
                            <li><a href="#" class="clock"></a></li>


                            <li class="dropdown notifications-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    <i class="fa fa-exchange"></i>
                                    <span class="label label-danger" id="qtdTransferenciaEstoqueRecebidasPendentes" style="display:none;"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="header"></li>
                                    <li>
                                        <!-- inner menu: contains the actual data -->
                                        <ul class="menu">
                                            <li>
                                                <a href="<?= site_url('products/transferenciaestoque') ?>">
                                                    <i class="fa fa-exchange text-danger"></i><b id="BqtdTransferenciaEstoqueRecebidasPendentes">0</b> TRANSFERENCIAS PENDENTE
                                                </a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" onclick="openMdlTransferirEstoque()">
                                                    <i class="fa fa-exchange text-success"></i>TRANSFERIR ESTOQUE
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="footer"><a href="<?= site_url('products/transferenciaestoque') ?>">Ver todas as transferências</a></li>
                                </ul>
                            </li>

                            <?php if ($Admin) { ?>
                                <li><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
                            <?php } ?>

                            <li><a href="<?= site_url('pos/shortcuts'); ?>" data-toggle="ajax"><i class="fa fa-key"></i></a></li>

                            <?php if ($Admin) { ?>
                                <li><a href="<?= site_url('pos/register_details'); ?>" data-toggle="ajax"><?= lang('register_details'); ?></a></li>
                                <li><a href="<?= site_url('pos/today_sale'); ?>" data-toggle="ajax"><?= lang('today_sale'); ?></a></li>
                            <?php } ?>
                            <li><a href="<?= site_url('pos/close_register'); ?>" data-toggle="ajax"><?= lang('close_register'); ?></a></li>
                            <?php if ($suspended_sales) { ?>
                                <li class="dropdown notifications-menu">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        <i class="fa fa-bell-o"></i>
                                        <span class="label label-warning"><?= sizeof($suspended_sales); ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="header"><?= lang('recent_suspended_sales'); ?></li>
                                        <li>
                                            <ul class="menu">
                                                <li>
                                                    <?php
                                                    foreach ($suspended_sales as $ss) {
                                                        echo '<a href="' . site_url('pos/?hold=' . $ss->id) . '" class="load_suspended">' . $this->tec->hrld($ss->date) . ' (' . $ss->customer_name . ')<br><strong>' . $ss->hold_ref . '</strong></a>';
                                                    }
                                                    ?>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="footer"><a href="<?= site_url('sales/opened'); ?>"><?= lang('view_all'); ?></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="dropdown user user-menu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="<?= base_url('uploads/avatars/thumbs/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>" class="user-image" alt="Avatar" />
                                    <span><?= $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'); ?></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="user-header">
                                        <img src="<?= base_url('uploads/avatars/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>" class="img-circle" alt="Avatar" />
                                        <p>
                                            <?= $this->session->userdata('email'); ?>
                                            <small><?= lang('member_since') . ' ' . $this->session->userdata('created_on'); ?></small>
                                        </p>
                                    </li>
                                    <li class="user-footer">
                                        <div class="pull-left">
                                            <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat"><?= lang('profile'); ?></a>
                                        </div>
                                        <div class="pull-right">
                                            <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat"><?= lang('sing_out'); ?></a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <!--
                            <li>
                                <a href="#" data-toggle="control-sidebar" class="sidebar-icon"><i class="fa fa-folder sidebar-icon"></i></a>
                            </li>
                            -->
                        </ul>
                    </div>
                </nav>
            </header>

            <?php include(getcwd() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'menu.php') ?>

            <div class="content-wrapper">

                <div class="col-lg-12 alerts">
                    <?php if ($error) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                            <?= $error; ?>
                        </div>
                        <?php
                    }
                    if ($message) {
                        ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                            <?= $message; ?>
                        </div>
                    <?php } ?>
                </div>

                <table style="width:100%;" class="layout-table">
                    <tr>
                        <td style="width: 700px;">

                            <div id="pos">

                                <div class="well well-sm" id="leftdiv">
                                    <div id="lefttop" style="margin-bottom:5px;">
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <label>Cliente</label>
                                            <div class="input-group">
                                                <?php
                                                $cust = ["0" => "Selecione"];
                                                foreach ($customers as $customer) {
                                                    $cust[$customer->id] = $customer->name;
                                                }
                                                ?>
                                                <?= form_dropdown('customer_id', $cust, set_value('customer_id', '0'), 'id="spos_customer" required="required" class="form-control select2" style="width:100%;"'); ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;">
                                                    <a href="#" id="add-customer" class="external" data-toggle="modal" data-target="#myModal"><i class="fa fa-2x fa-plus-circle" id="addIcon"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-bottom:20px;">
                                            <label>Vendedor</label>
                                            <?php
                                            $op_sellers = ["0" => "Selecione"];
                                            foreach ($sellers as $seller) {
                                                $op_sellers[$seller->id] = $seller->name;
                                            }
                                            ?>
                                            <?= form_dropdown('seller_id', $op_sellers, set_value('seller_id', '0'), 'id="seller_id" required="required" class="form-control select2" style="width:100%;"'); ?>
                                        </div>
                                        <div class="form-group" style="margin-bottom:5px;">
                                            <input type="text" name="code" id="add_item" class="form-control" placeholder="<?= lang('search__scan') ?>" />
                                        </div>
                                    </div>
                                    <div id="printhead" class="print">
                                        <?= $Settings->header; ?>
                                        <p>Date: <?= date($Settings->dateformat) ?></p>
                                    </div>
                                    <div id="print">
                                        <div id="list-table-div" style="max-height: 530px;overflow: auto !important">
                                            <table id="posTable" class="table table-striped table-condensed table-hover list-table" style="margin:0;">
                                                <thead>
                                                    <tr class="success">
                                                        <th style="text-align: left"><?= lang('product') ?></th>
                                                        <th style="width: 15%;text-align:right;"><?= lang('price') ?></th>
                                                        <th style="width: 15%;text-align:center;"><?= lang('qty') ?></th>
                                                        <th style="width: 20%;text-align:right;"><?= lang('subtotal') ?></th>
                                                        <th style="width: 20px;" class="satu"><i class="fa fa-trash-o pointer delete-all"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <div style="clear:both;"></div>
                                        <div id="totaldiv">
                                            <table id="totaltbl" class="table table-condensed totals" style="margin-bottom:10px;">
                                                <tbody>
                                                    <tr class="info">
                                                        <td width="25%"><?= lang('total_items') ?></td>
                                                        <td class="text-center" style="padding-right:10px;"><span id="count">0</span></td>
                                                        <td width="25%"><?= lang('total') ?></td>
                                                        <td class="text-right" colspan="2"><span id="total">0</span></td>
                                                    </tr>
                                                    <!--
                                                    <tr class="info">
                                                        <td width="25%">
                                                            Taxa Débito
                                                        </td>
                                                        <td class="text-center" style="padding-right:10px;">
                                                            <?= $tax_debit; ?>%
                                                        </td>
                                                        <td width="25%">
                                                            Taxa Crédito
                                                        </td>
                                                        <td class="text-right">
                                                            <?= $tax_credit; ?>%
                                                        </td>
                                                    </tr>
                                                    -->
                                                    <tr class="success">
                                                        <td colspan="2" style="font-family:Arial;font-weight:bold;font-size:30px"><?= lang('total_payable') ?></td>
                                                        <td class="text-right" colspan="2" style="font-family:Arial;font-weight:bold;font-size:30px"><span id="total-payable">0</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="botbuttons" class="col-xs-12 text-center">
                                        <div class="row">
                                            <div class="col-xs-4" style="padding: 0;">
                                                <div class="btn-group-vertical btn-block">
                                                    <button type="button" class="btn btn-warning btn-block btn-flat" id="suspend"><?= lang('hold'); ?></button>
                                                    <button type="button" class="btn btn-danger btn-block btn-flat" id="reset"><?= lang('cancel'); ?></button>
                                                </div>

                                            </div>
                                            <div class="col-xs-4" style="padding: 0;">
                                                <div class="btn-group-vertical btn-block">


                                                    <button type="button" class="btn bg-navy btn-block btn-flat" style="height:67px;" onclick="openMdlTransferirEstoque()">Transferir Estoque </button> 
                                                </div>
                                            </div>
                                            <div class="col-xs-4" style="padding: 0;">
                                                <button type="button" class="btn btn-success btn-block btn-flat" id="<?= $eid ? 'submit-sale' : 'payment'; ?>" style="height:67px;"><?= $eid ? lang('submit') : lang('payment'); ?></button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <span id="hidesuspend"></span>
                                    <input type="hidden" name="spos_note" value="" id="spos_note">

                                    <div id="payment-con">
                                        <input type="hidden" name="amount" id="amount_val" value="<?= $eid ? $sale->paid : ''; ?>" />
                                        <input type="hidden" name="balance_amount" id="balance_val" value="" />
                                        <input type="hidden" name="paid_by" id="paid_by_val" value="cash" />
                                        <input type="hidden" name="cc_no" id="cc_no_val" value="" />
                                        <input type="hidden" name="paying_gift_card_no" id="paying_gift_card_no_val" value="" />
                                        <input type="hidden" name="cc_holder" id="cc_holder_val" value="" />
                                        <input type="hidden" name="cheque_no" id="cheque_no_val" value="" />
                                        <input type="hidden" name="cc_month" id="cc_month_val" value="" />
                                        <input type="hidden" name="cc_year" id="cc_year_val" value="" />
                                        <input type="hidden" name="cc_type" id="cc_type_val" value="" />
                                        <input type="hidden" name="cc_cvv2" id="cc_cvv2_val" value="" />
                                        <input type="hidden" name="balance" id="balance_val" value="" />
                                        <input type="hidden" name="payment_note" id="payment_note_val" value="" />
                                    </div>
                                    <input type="hidden" name="customer" id="customer" value="<?= $Settings->default_customer ?>" />
                                    <input type="hidden" name="order_tax" id="tax_val" value="" />
                                    <input type="hidden" name="order_discount" id="discount_val" value="" />
                                    <input type="hidden" name="count" id="total_item" value="" />
                                    <input type="hidden" name="did" id="is_delete" value="<?= $sid; ?>" />
                                    <input type="hidden" name="eid" id="is_delete" value="<?= $eid; ?>" />
                                    <input type="hidden" name="hold_ref" id="hold_ref" value="" />
                                    <input type="hidden" name="total_items" id="total_items" value="0" />
                                    <input type="hidden" name="total_quantity" id="total_quantity" value="0" />
                                    <input type="submit" id="submit" value="Submit Sale" style="display: none;" />
                                </div>
                                <?= form_close(); ?>
                            </div>

                        </td>
                        <td>
                            <div class="contents" id="right-col">
                                <div id="item-list">
                                    <div class="items">
                                    </div>
                                </div>
                                <div class="product-nav">
                                </div>
                            </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
<!--
        <aside class="control-sidebar control-sidebar-dark" id="categories-list">
            <div class="tab-content">
                <div class="tab-pane active" id="control-sidebar-home-tab">
                    <ul class="control-sidebar-menu">
                        <?php
                        foreach ($categories as $category) {
                            echo '<li><a href="#" class="category' . ($category->id == $Settings->default_category ? ' active' : '') . '" id="' . $category->id . '">';
                            if ($category->image) {
                                echo '<div class="menu-icon"><img src="' . base_url('uploads/thumbs/' . $category->image) . '" alt="" class="img-thumbnail img-circle img-responsive"></div>';
                            } else {
                                echo '<i class="menu-icon fa fa-folder-open bg-red"></i>';
                            }
                            echo '<div class="menu-info"><h4 class="control-sidebar-subheading">' . $category->code . '</h4><p>' . $category->name . '</p></div>
							</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </aside>
        <div class="control-sidebar-bg"></div>
-->
<div id="order_tbl" style="display:none;"><span id="order_span"></span>
    <table id="order-table" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>
<div id="bill_tbl" style="display:none;"><span id="bill_span"></span>
    <table id="bill-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
    <table id="bill-total-table" width="100%" class="prT table table-striped table-condensed" style="width:100%;margin-bottom:0;"></table>
</div>

<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="posModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true"></div>
<div id="ajaxCall"><i class="fa fa-spinner fa-pulse"></i></div>

<div class="modal" data-easein="flipYIn" id="gcModal" tabindex="-1" role="dialog" aria-labelledby="mModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="myModalLabel"><?= lang('sell_gift_card'); ?></h4>
            </div>
            <div class="modal-body">
                <p><?= lang('enter_info'); ?></p>

                <div class="alert alert-danger gcerror-con" style="display: none;">
                    <button data-dismiss="alert" class="close" type="button">×</button>
                    <span id="gcerror"></span>
                </div>
                <div class="form-group">
                    <?= lang("card_no", "gccard_no"); ?> *
                    <div class="input-group">
                        <?php echo form_input('gccard_no', '', 'class="form-control" id="gccard_no"'); ?>
                        <div class="input-group-addon" style="padding-left: 10px; padding-right: 10px;"><a href="#" id="genNo"><i class="fa fa-cogs"></i></a></div>
                    </div>
                </div>
                <input type="hidden" name="gcname" value="<?= lang('gift_card') ?>" id="gcname" />
                <div class="form-group">
                    <?= lang("value", "gcvalue"); ?> *
                    <?php echo form_input('gcvalue', '', 'class="form-control" id="gcvalue"'); ?>
                </div>
                <div class="form-group">
                    <?= lang("price", "gcprice"); ?> *
                    <?php echo form_input('gcprice', '', 'class="form-control" id="gcprice"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="button" id="addGiftCard" class="btn btn-primary"><?= lang('sell_gift_card') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="dsModal" tabindex="-1" role="dialog" aria-labelledby="dsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="dsModalLabel"><?= lang('discount_title'); ?></h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ds' onClick='this.select();' value=''>

                <label class="checkbox" for="apply_to_order">
                    <input type="radio" name="apply_to" value="order" id="apply_to_order" checked="checked" />
                    <?= lang('apply_to_order') ?>
                </label>
                <label class="checkbox" for="apply_to_products">
                    <input type="radio" name="apply_to" value="products" id="apply_to_products" />
                    <?= lang('apply_to_products') ?>
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="button" id="updateDiscount" class="btn btn-primary btn-sm"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="tsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="tsModalLabel"><?= lang('tax_title'); ?></h4>
            </div>
            <div class="modal-body">
                <input type='text' class='form-control input-sm kb-pad' id='get_ts' onClick='this.select();' value=''>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm pull-left" data-dismiss="modal"><?= lang('close') ?></button>
                <button type="button" id="updateTax" class="btn btn-primary btn-sm"><?= lang('apply_to_order') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="proModal" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="proModalLabel">
                    <?= lang('payment') ?>
                </h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="row_id" />
                <input type="hidden" id="item_id" />
                <div class="box-group" id="accordion">
                    <div class="panel box" style="border-top:0">
                        <div class="box-header">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    Desconto
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Valor</label>
                                            <input type="hidden" id="nPrice">
                                            <input type="hidden" id="nQuantity">
                                            <input type="text" class="form-control input-sm kb-pad" id="nDiscount" onClick="this.select();" placeholder="<?= lang('discount') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label style="padding: 0">
                                                <input type="checkbox" id="split-item">
                                                Desagrupar item
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel box" style="border-top:0">
                        <div class="box-header">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                    Troca
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="panel-collapse collapse">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Valor pago</label>
                                            <input type="text" class="form-control" id="item_sale_paid">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #fff">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close') ?></button>
                <button class="btn btn-success" id="editItem"><?= lang('update') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="susModal" tabindex="-1" role="dialog" aria-labelledby="susModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="susModalLabel"><?= lang('suspend_sale'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Observação</label>
                    <?php echo form_input('reference_note', $reference_note, 'class="form-control kb-text" id="reference_note"'); ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?= lang('close') ?> </button>
                <button type="button" id="suspend_sale" class="btn btn-primary"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>



<div class="modal" data-easein="flipYIn" id="saleModal" tabindex="-1" role="dialog" aria-labelledby="saleModalLabel" aria-hidden="true"></div>
<div class="modal" data-easein="flipYIn" id="opModal" tabindex="-1" role="dialog" aria-labelledby="opModalLabel" aria-hidden="true"></div>

<div class="modal" data-easein="flipYIn" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-success">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title" id="payModalLabel">
                    <?= lang('payment') ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="col-xs-10 font16" style="padding-left: 0;padding-right: 5px;">
                            <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                <tbody>
                                    <tr>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_items"); ?></td>
                                        <td width="25%" class="text-right"><span id="item_count">0.00</span></td>
                                        <td width="25%" style="border-right-color: #FFF !important;"><?= lang("total_payable"); ?></td>
                                        <td width="25%" class="text-right"><span id="twt">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td style="border-right-color: #FFF !important;"><?= lang("total_paying"); ?></td>
                                        <td class="text-right"><span id="total_paying">0.00</span></td>
                                        <td style="border-right-color: #FFF !important;"><?= lang("balance"); ?></td>
                                        <td class="text-right"><span id="balance">0.00</span></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="clearfix"></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-10">
                                <div class="form-group">
                                    <?= lang('note', 'note'); ?>
                                    <textarea name="note" id="note" class="pa form-control kb-text"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-4 box-pay-0">
                                <div class="form-group">
                                    <?= lang("amount", "amount"); ?>
                                    <input name="amount_row[]" type="number" id="amount" onchange="paying_change(this, 'box-pay-0')" class="pa form-control kb-pad amount" data-box="box-pay-0" step="0.01"/>
                                </div>
                            </div>
                            <div class="col-xs-6 box-pay-0">
                                <div class="form-group">
                                    <?= lang("paying_by", "paid_by"); ?>
                                    <select name="method[]" id="method" class="form-control method" onchange="method_change(this, 'box-pay-0')" style="width:100%;">
                                        <option value="cash"><?= lang("cash"); ?></option>
                                        <option value="credit"><?= lang("cc"); ?></option>
                                        <option value="debit"><?= lang("stripe"); ?></option>
                                        <option value="transfer">Depósito</option>
                                    </select>
                                </div>
                                <input type="hidden" class="tax" value="0">
                                <input type="hidden" class="balance_ori" id="balance_ori">
                            </div>
                            <div class="col-xs-2 box-pay-0 instalments" style="display: none">
                                <div class="form-group">
                                    <label>Parcelas</label>
                                    <select name="instalments[]" class="form-control instalment" onchange="instalment_change(this, 'box-pay-0')" style="width:100%;">
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="box-payments">

                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group gc" style="display: none;">
                                    <?= lang("gift_card_no", "gift_card_no"); ?>
                                    <input type="text" id="gift_card_no" class="pa form-control kb-pad gift_card_no gift_card_input" />

                                    <div id="gc_details"></div>
                                </div>
                                <div class="pcheque" style="display:none;">
                                    <div class="form-group"><?= lang("cheque_no", "cheque_no"); ?>
                                        <input type="text" id="cheque_no" class="form-control cheque_no  kb-text" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" id="abort-sale" data-dismiss="modal"> <?= lang('close') ?> </button>
                <button class="btn btn-primary" id="<?= $eid ? '' : 'submit-sale'; ?>"><?= lang('submit') ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal" data-easein="flipYIn" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="cModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
                <h4 class="modal-title" id="cModalLabel">
                    <?= lang('add_customer') ?>
                </h4>
            </div>
            <?= form_open('pos/add_customer', 'id="customer-form"'); ?>
            <div class="modal-body">
                <div id="c-alert" class="alert alert-danger" style="display:none;"></div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-group">
                            <label class="control-label" for="code">
                                <?= lang("name"); ?>
                            </label>
                            <?= form_input('name', '', 'class="form-control input-sm kb-text" id="cname"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cemail">
                                <?= lang("email_address"); ?>
                            </label>
                            <?= form_input('email', '', 'class="form-control input-sm kb-text" id="cemail"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="phone">
                                <?= lang("phone"); ?>
                            </label>
                            <?= form_input('phone', '', 'class="form-control input-sm kb-pad" id="cphone"'); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf1">
                                <?= lang("cf1"); ?>
                            </label>
                            <?= form_input('cf1', '', 'class="form-control input-sm kb-text" id="cf1"'); ?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="control-label" for="cf2">
                                <?= lang("cf2"); ?>
                            </label>
                            <?= form_input('cf2', '', 'class="form-control input-sm kb-text" id="cf2"'); ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer" style="margin-top:0;">
                <input type="hidden" name="seller_id" id="cust-seller_id"
                       <button type="button" class="btn btn-default pull-left" data-dismiss="modal"> <?= lang('close') ?> </button>
                <button type="submit" class="btn btn-primary" id="add_customer"> <?= lang('add_customer') ?> </button>
            </div>
        </div>
    </div>
</div>
<?= form_close(); ?>

<script src="<?= $assets ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/redactor/redactor.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/select2/select2.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/formvalidation/js/formValidation.popular.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/formvalidation/js/framework/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/common-libs.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/app.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/pages/all.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/custom.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/jquery-mask/jquery.mask.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/input-mask/jquery.inputmask.date.extensions.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/velocity/velocity.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>plugins/velocity/velocity.ui.min.js" type="text/javascript"></script>
<script src="<?= $assets ?>dist/js/parse-track-data.js" type="text/javascript"></script>
<?php if ($Settings->java_applet) { ?>
    <script type="text/javascript" src="<?= $assets ?>plugins/qz/js/deployJava.js"></script>
    <script type="text/javascript" src="<?= $assets ?>plugins/qz/qz-functions.js"></script>
    <script type="text/javascript">
                                        deployQZ('themes/<?= $Settings->theme ?>/assets/plugins/qz/qz-print.jar', '<?= $assets ?>plugins/qz/qz-print_jnlp.jnlp');

                                        function printBill(bill) {
                                            usePrinter("<?= $Settings->receipt_printer; ?>");
                                            printData(bill);
                                        }
    <?php
    $printers = json_encode(explode('|', $Settings->pos_printers));
    echo 'var printer = ' . $printers . ';
';
    ?>

                                        function printOrder(order) {
                                            for (index = 0; index < printers.length; index++) {
                                                usePrinter(printers[index]);
                                                printData(order);
                                            }
                                        }
    </script>
<?php } ?>
<script src="<?= $assets ?>dist/js/pos.js?v=2021-05-10-1" type="text/javascript">
</script>
<script type="text/javascript">
    var base_url = '<?= base_url(); ?>',
            assets = '<?= $assets ?>';
    var dateformat = '<?= $Settings->dateformat; ?>',
            timeformat = '<?= $Settings->timeformat ?>';
<?php unset($Settings->protocol, $Settings->smtp_host, $Settings->smtp_user, $Settings->smtp_pass, $Settings->smtp_port, $Settings->smtp_crypto, $Settings->mailpath, $Settings->timezone, $Settings->setting_id, $Settings->default_email, $Settings->version, $Settings->stripe, $Settings->stripe_secret_key, $Settings->stripe_publishable_key); ?>
    var Settings = <?= json_encode($Settings); ?>;
    var sid = false,
            username = '<?= $this->session->userdata('username'); ?>',
            spositems = {};
    $(window).load(function () {
        $('#mm_<?= $m ?>').addClass('active');
        $('#<?= $m ?>_<?= $v ?>').addClass('active');
    });
    var pro_limit = <?= $Settings->pro_limit ?>,
            java_applet = 0,
            count = 1,
            total = 0,
            an = 1,
            p_page = 0,
            page = 0,
            cat_id = <?= $Settings->default_category ?>,
            tcp = <?= $tcp ?>;
    var gtotal = 0,
            order_discount = 0,
            order_tax = 0,
            protect_delete = false;
    //<?= ($Admin) ? 0 : ($Settings->pin_code ? 1 : 0); ?>;
    var order_data = '',
            bill_data = '';
    var lang = new Array();
    lang['code_error'] = '<?= lang('code_error'); ?>';
    lang['r_u_sure'] = '<?= lang('r_u_sure'); ?>';
    lang['please_add_product'] = '<?= lang('please_add_product'); ?>';
    lang['paid_less_than_amount'] = '<?= lang('paid_less_than_amount'); ?>';
    lang['x_suspend'] = '<?= lang('x_suspend'); ?>';
    lang['discount_title'] = '<?= lang('discount_title'); ?>';
    lang['update'] = '<?= lang('update'); ?>';
    lang['tax_title'] = '<?= lang('tax_title'); ?>';
    lang['leave_alert'] = '<?= lang('leave_alert'); ?>';
    lang['close'] = '<?= lang('close'); ?>';
    lang['delete'] = '<?= lang('delete'); ?>';
    lang['no_match_found'] = '<?= lang('no_match_found'); ?>';
    lang['wrong_pin'] = '<?= lang('wrong_pin'); ?>';
    lang['file_required_fields'] = '<?= lang('file_required_fields'); ?>';
    lang['enter_pin_code'] = '<?= lang('enter_pin_code'); ?>';
    lang['incorrect_gift_card'] = '<?= lang('incorrect_gift_card'); ?>';
    lang['card_no'] = '<?= lang('card_no'); ?>';
    lang['value'] = '<?= lang('value'); ?>';
    lang['balance'] = '<?= lang('balance'); ?>';
    lang['unexpected_value'] = '<?= lang('unexpected_value'); ?>';
    lang['inclusive'] = '<?= lang('inclusive'); ?>';
    lang['exclusive'] = '<?= lang('exclusive'); ?>';
    lang['total'] = '<?= lang('total'); ?>';
    lang['total_items'] = '<?= lang('total_items'); ?>';
    lang['order_tax'] = '<?= lang('order_tax'); ?>';
    lang['order_discount'] = '<?= lang('order_discount'); ?>';
    lang['total_payable'] = '<?= lang('total_payable'); ?>';
    lang['rounding'] = '<?= lang('rounding'); ?>';
    lang['grand_total'] = '<?= lang('grand_total'); ?>';

    $(document).ready(function () {
        posScreen();
<?php if ($this->session->userdata('rmspos')) { ?>
            if (get('spositems')) {
                remove('spositems');
            }
            if (get('spos_discount')) {
                remove('spos_discount');
            }
            if (get('spos_tax')) {
                remove('spos_tax');
            }
            if (get('spos_note')) {
                remove('spos_note');
            }
            if (get('spos_customer')) {
                remove('spos_customer');
            }
            if (get('amount')) {
                remove('amount');
            }
    <?php
    $this->tec->unset_data('rmspos');
}
?>

        if (get('rmspos')) {
            if (get('spositems')) {
                remove('spositems');
            }
            if (get('spos_discount')) {
                remove('spos_discount');
            }
            if (get('spos_tax')) {
                remove('spos_tax');
            }
            if (get('spos_note')) {
                remove('spos_note');
            }
            if (get('spos_customer')) {
                remove('spos_customer');
            }
            if (get('amount')) {
                remove('amount');
            }
            remove('rmspos');
        }
<?php if ($sid) { ?>

            store('spositems', JSON.stringify(<?= $items; ?>));
            store('spos_discount', '<?= $suspend_sale->order_discount_id; ?>');
            store('spos_tax', '<?= $suspend_sale->order_tax_id; ?>');
            store('spos_customer', '<?= $suspend_sale->customer_id; ?>');
            $('#spos_customer').select2('val', '<?= $suspend_sale->customer_id; ?>');
            store('rmspos', '1');
            $('#tax_val').val('<?= $suspend_sale->order_tax_id; ?>');
            $('#discount_val').val('<?= $suspend_sale->order_discount_id; ?>');
<?php } elseif ($eid) { ?>

            store('spositems', JSON.stringify(<?= $items; ?>));
            store('spos_discount', '<?= $sale->order_discount_id; ?>');
            store('spos_tax', '<?= $sale->order_tax_id; ?>');
            store('spos_customer', '<?= $sale->customer_id; ?>');
            $('#spos_customer').select2('val', '<?= $sale->customer_id; ?>');
            store('rmspos', '1');
            $('#tax_val').val('<?= $sale->order_tax_id; ?>');
            $('#discount_val').val('<?= $sale->order_discount_id; ?>');
<?php } else { ?>
            if (!get('spos_discount')) {
                store('spos_discount', '<?= $Settings->default_discount; ?>');
                $('#discount_val').val('<?= $Settings->default_discount; ?>');
            }
            if (!get('spos_tax')) {
                store('spos_tax', '<?= $Settings->default_tax_rate; ?>');
                $('#tax_val').val('<?= $Settings->default_tax_rate; ?>');
            }
<?php } ?>

        if (ots = get('spos_tax')) {
            $('#tax_val').val(ots);
        }
        if (ods = get('spos_discount')) {
            $('#discount_val').val(ods);
        }
        if (Settings.display_kb == 1) {
            display_keyboards();
        }
        nav_pointer();
        loadItems();
        read_card();
        bootbox.addLocale('bl', {
            OK: '<?= lang('ok'); ?>',
            CANCEL: '<?= lang('no'); ?>',
            CONFIRM: '<?= lang('yes'); ?>'
        });
        bootbox.setDefaults({
            closeButton: false,
            locale: "bl"
        });
<?php if ($eid) { ?>
            $('#suspend').attr('disabled', true);
            $('#print_order').attr('disabled', true);
<?php } ?>
    });

    function payment_row_add() {
        if (balance_val >= 0) {
            return;
        }

        var id = String(Math.random()).replace('.', '_');

        var diff = '';

        if (balance_val < 0) {
            diff = balance_val * -1;
        }

        var tpl = '\n\
<div class="col-xs-4 box-pay-' + id + '">\n\
    <div class="form-group">\n\
        <input name="amount_row[]" type="number" value="' + diff + '" onchange="paying_change(this, \'box-pay-' + id + '\')" data-box="box-pay-' + id + '" class="pa form-control kb-pad amount" step="0.01" />\n\
    </div>\n\
</div>\n\
<div class="col-xs-6 box-pay-' + id + '">\n\
    <div class="form-group">\n\
        <div class="input-group">\n\
            <select id="method-' + id + '" name="method[]" onchange="method_change(this, \'box-pay-' + id + '\')" class="form-control paid_by select2 method" style="width:100%;">\n\
                <option value="cash"><?= lang("cash"); ?></option>\n\
                <option value="credit"><?= lang("cc"); ?></option>\n\
                <option value="debit"><?= lang("stripe"); ?></option>\n\
                <option value="transfer">Depósito</option>\n\
            </select>\n\
            <span class="input-group-addon">\n\
                <i class="fa fa-trash-o tip pointer" data-ref="' + id + '" onclick="payment_row_remove(this)" title="Remover"></i>\n\
            </span>\n\
        </div>\n\
        <input type="hidden" class="tax" value="0">\n\
        <input type="hidden" class="balance_ori" value="'+diff+'">\n\
    </div>\n\
</div>\n\
<div class="col-xs-2 box-pay-' + id + ' instalments" style="display: none">\n\
    <div class="form-group">\n\
        <select name="instalments[]" class="form-control instalment" onchange="instalment_change(this, \'box-pay-' + id + '\')" style="width:100%">\n\
            <option value="1">1x</option>\n\
            <option value="2">2x</option>\n\
            <option value="3">3x</option>\n\
        </select>\n\
    </div>\n\
</div>';

        $('#box-payments').append(tpl);

        calcTotal();
    }

    function payment_row_remove(elm) {
        var ref = elm.dataset.ref;

        $('.box-pay-' + ref).each(function (i, e) {
            e.remove();
        });

        calcTotal();
    }

    var total_paying = 0;

    var balance_val = -1;

    $(document).on('keypress', 'input.amount', function (e) {
        if (e.which === 13) {
            $(e.target).change();
        }
    });

    function paying_change(elm, box) {
        $('.' + box + ' input.balance_ori').val(elm.value);
        
        calcTotal();
        payment_row_add();
    }

    function method_change(elm, box) {
        if (elm.value === 'credit') {
            $('.' + box + '.instalments').show();
        } else {
            $('.' + box + '.instalments').hide();
        }

        update_tax(box, elm.value);
    }

    function instalment_change(elm, box) {
        var method = $('.' + box + ' select.method').val();

        var di = $('.' + box + ' select.instalment').val();

        if (method === 'credit' && di > 1) {
            method += '_' + di + 'x';
        }

        update_tax(box, method);
    }

    function update_tax(box, method) {
        var input = $('.' + box + ' input.amount');

        var val = parseFloat($('#total-payable').text().trim().replace(',', ''));

        if ($('input.amount').length > 1) {
            var ori = $('.' + box + ' input.balance_ori').val();
            val = parseFloat(ori);
        }

        var tax = calcTax(val, method);
        
        $('.' + box + ' input.tax').val(tax);
        
        input.val(Number(val + tax).toFixed(2));
        
        calcTotal(input);
    }

    function calcTotal(input_balance) {
        total_paying = 0;

        var tax_val = 0;

        $(".amount").each(function (i, e) {
            
            if (e.value === "") {
                return;
            }

            var box = e.dataset.box;

            var tx = $('.' + box + ' input.tax').val();

            tax_val += parseFloat(tx);

            total_paying += parseFloat(e.value);
        });

        total_paying = total_paying.toFixed(2);

        var subtotal = parseFloat($('#total-payable').text().trim().replace(',', ''));

        gtotal = parseFloat(Number(subtotal + tax_val).toFixed(2));
        balance_val = parseFloat(Number(total_paying - gtotal).toFixed(2));

        $('#balance').text(formatMoney(balance_val));

        $('#total_paying').text(formatMoney(total_paying));

        if (input_balance && balance_val < 0) {
            var v = Number(parseFloat(input_balance.val()) + (balance_val * -1)).toFixed(2);
            input_balance.val(v);
            $('#balance').text(formatMoney(0));
        }
    
        $('#twt').text(gtotal);
    }

    function calcTax(amount, method) {
        var tax = JSON.parse('<?= $tax_cards; ?>');

        var tax_val = 0;

        if (tax[method]) {
            var tr = parseFloat(amount);
            tax_val = tr * tax[method];
        }

        return tax_val;
    }
    
    $.get('<?= site_url('sales/topItems')?>', function(rows) {
        html = '<div>';
        
        $.each(rows, function (i, row) {
            html += '<button type="button" data-name="'+row.name+'" id="product-'+row.code+'" value="'+row.code+'" class="btn btn-both btn-flat product">\n\
                    <span class="bg-img">\n\
                        <img src="<?= URL_IMAGES . "/uploads/thumbs/" ?>'+row.image+'" alt="" style="width: 100px; height: 100px;">\n\
                    </span>\n\
                    <span>\n\
                        <span>'+row.code+'</span>\n\
                    </span>\n\
                </button>';
        });
        
        html += '</div>';
        
        $('#item-list .ps-container').html(html);
    });
    
    $('.delete-all').click(function () {
        localStorage.setItem('spositems', '[]');
        loadItems();
    });
    
    function update_quantity(ev) {
        if (ev.charCode === 13) {
            $(ev.target).trigger('change');
        }
    }
    
    var SPMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    };
    
    $(document).ready(function () {
        $('#list-table-div').css('height', (window.innerHeight - 260 - 170) + 'px');
        
        $('#seller_id').val('0').change();
        $('#spos_customer').val('0').change();
        
        var spOptions = {
            onKeyPress: function(val, e, field, options) {
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            }
        };

        $('#cphone').mask(SPMaskBehavior, spOptions);

    });
        
    $(document).on('click', '#editItem', function () {
        var item_id = $('#item_id').val();
        
        if(!is_valid_discount($('#nDiscount').val())) {
            bootbox.alert(lang.unexpected_value);
            return false;
        }
                
        var discount = $('#nDiscount').val() ? $('#nDiscount').val() : 0;
        
        //troca
        var item_sale_paid = parseInt($('#item_sale_paid').val());
        
        spositems[item_id].row.exchange_val = 0;
        
        if (item_sale_paid > 0) {
            discount = $('#item_sale_paid').val();
            spositems[item_id].row.real_unit_price = 0;
            spositems[item_id].row.exchange_val = item_sale_paid;
        }
        
        spositems[item_id].row.discount = discount;
        
        var qty = spositems[item_id].row.qty;
        
        var cod_loja = '<?= CODIGO_LOJA?>';
        
        var spl = $('#split-item').is(':checked'); 
        
        if (cod_loja !== 'ONLINE' && spl === true && qty > 1) {
            spositems[item_id].row.qty = 1;
            
            for (var i=2; i<=qty; i++) {
                var key = item_id + "_" + i;
                var row = JSON.parse(JSON.stringify(spositems[item_id]));
                row.row.discount = 0;
                spositems[key] = row;
            }
        }
                
        localStorage.setItem('spositems', JSON.stringify(spositems));
        
        $('#proModal').modal('hide');

        loadItems();
        
        return false;
    });

</script>
</body>
</html>