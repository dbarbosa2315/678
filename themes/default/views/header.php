<?php (defined('BASEPATH')) or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= $page_title . ' | ' . $Settings->site_name; ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="<?= $assets ?>bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/iCheck/square/green.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/select2/select2.min.css?v=1706" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>plugins/redactor/redactor.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/jquery-ui.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= $assets ?>dist/css/custom.css" rel="stylesheet" type="text/css" />
    
    <style>
            .slimScrollDiv {
                overflow: auto !important;
            }
            <?php if(!isset($menu_fixed)): ?>
            .main-sidebar .sidebar {
                overflow: auto !important;
                height: auto !important;
            }
            <?php endif;?>
            .table td .btn-group.actions .btn,
            .table td:last-child .btn-group .btn:hover,
            .table td .btn-group.actions .btn:hover {
                padding: 0px 5px;
                margin-left: 0px;
                margin-right: 5px;
                border: 1px solid transparent;
                border-radius: 2px !important;
                font-size: 16px;
                line-height: 1.4;
            }
        </style>
        
    <script src="<?= $assets ?>plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>

</head>

<?php include(getcwd() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_comunicacao.php') ?>

<body class="<?php if(isset($menu_fixed)){echo 'skin-green sidebar-collapse sidebar-mini pos';}else{echo 'skin-green fixed sidebar-mini'; } ?>">
    <div class="wrapper">

        <header class="main-header">
            <a href="<?= site_url(); ?>" class="logo">
                <span class="logo-mini">PDV</span>
                <span class="logo-lg" style="text-align: left; font-weight: bold; color: #f6f6f6;">
                    Bela Plus
                </span>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Navegação</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <ul class="nav navbar-nav pull-left">
                    <li class="dropdown hidden-xs">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><img src="<?= $assets; ?>images/<?= $Settings->language; ?>.png" alt="<?= $Settings->language; ?>"></a>
                        <ul class="dropdown-menu">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { ?>
                                <li><a href="<?= site_url('pos/language/' . $entry); ?>"><img src="<?= $assets; ?>images/<?= $entry; ?>.png" class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                            <?php } ?>
                        </ul>
                    </li>
                </ul>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li class="hidden-xs hidden-sm"><a href="#" class="clock"></a></li>
                        <li class="hidden-xs"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i></a></li>

                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-exchange"></i>
                                <span class="label label-danger" id="qtdTransferenciaEstoqueRecebidasPendentes" style="display:none;"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">Transferências de Estoque</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <li>
                                            <a href="<?= site_url('products/transferenciaestoque') ?>">
                                                <i class="fa fa-exchange text-danger"></i><b id="BqtdTransferenciaEstoqueRecebidasPendentes">0</b> transferências pendentes
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" onclick="openMdlTransferirEstoque()">
                                                <i class="fa fa-exchange text-success"></i>Solicitar transferência de estoque
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="<?= site_url('products/transferenciaestoque') ?>">Ver todas as transferências</a></li>
                            </ul>
                        </li>

                        <?php if ($Admin) { ?>
                            <li class="hidden-xs"><a href="<?= site_url('settings'); ?>"><i class="fa fa-cogs"></i></a></li>
                        <?php } ?>
                        <li><a href="<?= site_url('pos/view_bill'); ?>" target="_blank"><i class="fa fa-file-text-o"></i></a></li>
                        <li><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i></a></li>
                        <?php if ($Admin && $qty_alert_num) { ?>
                            <li>
                                <a href="<?= site_url('reports/alerts'); ?>">
                                    <i class="fa fa-bullhorn"></i>
                                    <span class="label label-warning"><?= $qty_alert_num; ?></span>
                                </a>
                            </li>
                        <?php } ?>
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
                        <li class="dropdown user user-menu" style="padding-right:5px;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <img src="<?= base_url('uploads/avatars/thumbs/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>" class="user-image" alt="Avatar" />
                                <span class="hidden-xs"><?= $this->session->userdata('first_name') . ' ' . $this->session->userdata('last_name'); ?></span>
                            </a>
                            <ul class="dropdown-menu" style="padding-right:3px;">
                                <li class="user-header">
                                    <img src="<?= base_url('uploads/avatars/' . ($this->session->userdata('avatar') ? $this->session->userdata('avatar') : $this->session->userdata('gender') . '.png')) ?>" class="img-circle" alt="Avatar" />
                                    <p>
                                        <?= $this->session->userdata('email'); ?>
                                        <small><?= lang('member_since') . ' ' . $this->session->userdata('created_on'); ?></small>
                                    </p>
                                </li>
                                <li class="user-footer">
                                    <div class="pull-left">
                                        <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn btn-default btn-flat">Perfil</a>
                                    </div>
                                    <div class="pull-right">
                                        <a href="<?= site_url('logout'); ?>" class="btn btn-default btn-flat">Sair</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        
        <?php 
            $root_theme = getcwd() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
            
            $group_menu = $root_theme . "group_$user_group" . DIRECTORY_SEPARATOR . 'menu.php';
            
            if (file_exists($group_menu)) {
                include($group_menu);
            } else {
                include($root_theme . 'menu.php');
            }
        ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <h1><?= $page_title; ?></h1>
                <ol class="breadcrumb">
                    <li><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <?= lang('home'); ?></a></li>
                    <?php
                    foreach ($bc as $b) {
                        if ($b['link'] === '#') {
                            echo '<li class="active">' . $b['page'] . '</li>';
                        } else {
                            echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                        }
                    }
                    ?>
                </ol>
            </section>

            <div class="col-lg-12 alerts">
                <div id="custom-alerts" style="display:none;">
                    <div class="alert alert-dismissable">
                        <div class="custom-msg"></div>
                    </div>
                </div>
                <?php if ($error) { ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-ban"></i> <?= lang('error'); ?></h4>
                        <?= $error; ?>
                    </div>
                <?php }
                if ($warning) { ?>
                    <div class="alert alert-warning alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4><i class="icon fa fa-warning"></i> <?= lang('warning'); ?></h4>
                        <?= $warning; ?>
                    </div>
                <?php }
                if ($message) { ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <h4> <i class="icon fa fa-check"></i> <?= lang('Success'); ?></h4>
                        <?= $message; ?>
                    </div>
                <?php } ?>
            </div>