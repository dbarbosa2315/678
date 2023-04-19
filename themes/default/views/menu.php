<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
            <!-- <li class="header"><?= lang('mian_navigation'); ?></li> -->


            <li class="mm_pos"><a href="<?= site_url('pos'); ?>"><i class="fa fa-th"></i> <span><?= lang('pos'); ?></span></a></li>

            <?php if ($Admin) { ?>
                <li class="mm_welcome"><a href="<?= site_url(); ?>"><i class="fa fa-dashboard"></i> <span><?= lang('dashboard'); ?></span></a></li>
                <li class="treeview mm_products">
                    <a href="#">
                        <i class="fa fa-barcode"></i>
                        <span><?= lang('products'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="products_index"><a href="<?= site_url('products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_products'); ?></a></li>
                        <!--
                        <li id="products_add"><a href="<?= site_url('products/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_product'); ?></a></li>
                        <li id="products_import"><a href="<?= site_url('products/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_products'); ?></a></li>
                        -->
                        <li id="products_print_barcodes"><a onclick="window.open('<?= site_url('products/print_barcodes'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;" href="#"><i class="fa fa-circle-o"></i> <?= lang('print_barcodes'); ?></a></li>
                        <li id="products_print_labels"><a onclick="window.open('<?= site_url('products/print_labels'); ?>', 'pos_popup', 'width=900,height=600,menubar=yes,scrollbars=yes,status=no,resizable=yes,screenx=0,screeny=0'); return false;" href="#"><i class="fa fa-circle-o"></i> <?= lang('print_labels'); ?></a></li>
                        <li id="products_transferenciaestoque"><a href="<?= site_url('products/transferenciaestoque'); ?>"><i class="fa fa-circle-o"></i>Transferências de Estoque</a></li>
                    </ul>
                </li>
                <li class="treeview mm_categories">
                    <a href="#">
                        <i class="fa fa-folder"></i>
                        <span><?= lang('categories'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="categories_index"><a href="<?= site_url('categories'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_categories'); ?></a></li>
                        <li id="categories_add"><a href="<?= site_url('categories/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_category'); ?></a></li>
                        <li id="categories_import"><a href="<?= site_url('categories/import'); ?>"><i class="fa fa-circle-o"></i> <?= lang('import_categories'); ?></a></li>
                    </ul>
                </li>
                <li class="treeview mm_sales">
                    <a href="#">
                        <i class="fa fa-shopping-cart"></i>
                        <span><?= lang('sales'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
                        <li id="sales_bling"><a href="<?= site_url('sales/bling'); ?>"><i class="fa fa-circle-o"></i> Bling </a></li>
                    </ul>
                </li>
                <li class="treeview mm_purchases">
                    <a href="#">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('purchases'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="purchases_index"><a href="<?= site_url('purchases'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_purchases'); ?></a></li>
                        <li id="purchases_add"><a href="<?= site_url('purchases/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_purchase'); ?></a></li>
                        <li class="divider"></li>
                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                    </ul>
                </li>


                <li class="treeview mm_auth mm_customers mm_suppliers">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span><?= lang('people'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="auth_users"><a href="<?= site_url('users'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_users'); ?></a></li>
                        <li id="auth_add"><a href="<?= site_url('users/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_user'); ?></a></li>
                        <li class="divider"></li>
                        <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>
                        <li id="customers_add"><a href="<?= site_url('customers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_customer'); ?></a></li>
                        <li class="divider"></li>
                        <li id="suppliers_index"><a href="<?= site_url('suppliers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_suppliers'); ?></a></li>
                        <li id="suppliers_add"><a href="<?= site_url('suppliers/add'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_supplier'); ?></a></li>
                    </ul>
                </li>

                <li class="mm_sellers">
                    <a href="<?= site_url('sellers'); ?>">
                        <i class="fa fa-briefcase"></i>
                        <span>Vendedores</span>
                    </a>
                </li>

                <li class="treeview mm_settings">
                    <a href="#">
                        <i class="fa fa-cogs"></i>
                        <span><?= lang('settings'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="settings_index"><a href="<?= site_url('settings'); ?>"><i class="fa fa-circle-o"></i> <?= lang('settings'); ?></a></li>
                        <li id="settings_backups"><a href="<?= site_url('settings/backups'); ?>"><i class="fa fa-circle-o"></i> <?= lang('backups'); ?></a></li>
                        <li id="settings_updates"><a href="<?= site_url('settings/updates'); ?>"><i class="fa fa-circle-o"></i> <?= lang('updates'); ?></a></li>
                    </ul>
                </li>
                <li class="treeview mm_reports">
                    <a href="#">
                        <i class="fa fa-bar-chart-o"></i>
                        <span><?= lang('reports'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="reports_daily_sales"><a href="<?= site_url('reports/daily_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('daily_sales'); ?></a></li>
                        <li id="reports_monthly_sales"><a href="<?= site_url('reports/monthly_sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('monthly_sales'); ?></a></li>
                        <li id="reports_index"><a href="<?= site_url('reports'); ?>"><i class="fa fa-circle-o"></i> <?= lang('sales_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_payments"><a href="<?= site_url('reports/payments'); ?>"><i class="fa fa-circle-o"></i> <?= lang('payments_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_registers"><a href="<?= site_url('reports/registers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('registers_report'); ?></a></li>
                        <li class="divider"></li>
                        <li id="reports_top_products"><a href="<?= site_url('reports/top_products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('top_products'); ?></a></li>
                        <li id="reports_products"><a href="<?= site_url('reports/products'); ?>"><i class="fa fa-circle-o"></i> <?= lang('products_report'); ?></a></li>
                        <li id="reports_estoque"><a href="<?= site_url('reports/estoque'); ?>"><i class="fa fa-circle-o"></i> Relatório de Estoque</a></li>
                    </ul>
                </li>
            <?php } else { ?>
                <li class="mm_products"><a href="<?= site_url('products'); ?>"><i class="fa fa-barcode"></i> <span><?= lang('products'); ?></span></a></li>
                <li class="mm_categories"><a href="<?= site_url('categories'); ?>"><i class="fa fa-folder-open"></i> <span><?= lang('categories'); ?></span></a></li>
                <li class="treeview mm_sales">
                    <a href="#">
                        <i class="fa fa-shopping-cart"></i>
                        <span><?= lang('sales'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="sales_index"><a href="<?= site_url('sales'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_sales'); ?></a></li>
                        <li id="sales_opened"><a href="<?= site_url('sales/opened'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_opened_bills'); ?></a></li>
                    </ul>
                </li>
                <li class="treeview mm_purchases">
                    <a href="#">
                        <i class="fa fa-plus"></i>
                        <span><?= lang('expenses'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="purchases_expenses"><a href="<?= site_url('purchases/expenses'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_expenses'); ?></a></li>
                        <li id="purchases_add_expense"><a href="<?= site_url('purchases/add_expense'); ?>"><i class="fa fa-circle-o"></i> <?= lang('add_expense'); ?></a></li>
                    </ul>

                <li class="treeview mm_customers">
                    <a href="#">
                        <i class="fa fa-users"></i>
                        <span><?= lang('customers'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="customers_index"><a href="<?= site_url('customers'); ?>"><i class="fa fa-circle-o"></i> <?= lang('list_customers'); ?></a></li>

                    </ul>
                </li>

                <li class="mm_sellers">
                    <a href="<?= site_url('sellers'); ?>">
                        <i class="fa fa-briefcase"></i>
                        <span>Vendedores</span>
                    </a>
                </li>
                <li class="treeview mm_reports">
                    <a href="#">
                        <i class="fa fa-bar-chart-o"></i>
                        <span><?= lang('reports'); ?></span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li id="reports_estoque"><a href="<?= site_url('reports/estoque'); ?>"><i class="fa fa-circle-o"></i> Relatório de Estoque</a></li>
                    </ul>
                <?php } ?>
        </ul>
    </section>
</aside>