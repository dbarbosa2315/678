<link href="<?= $assets ?>plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                    <div class="box-tools">
                        <div style="width: 210px; float: right;">
                            <div class="input-group">
                                <input type="text" class="form-control pull-right" id="data_range" name="data_range">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="hidden" id="date_start" value="<?= $date_start ?>">
                                <input type="hidden" id="date_end" value="<?= $date_end ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="SLData" class="table table-striped table-bordered table-condensed table-hover">
                            <thead>
                                <tr class="active">
                                    <th>Id</th>
                                    <th><?php echo $this->lang->line("date"); ?></th>
                                    <th><?php echo $this->lang->line("customer"); ?></th>
                                    <th>Vendedor</th>
                                    <th style="text-align: right"><?php echo $this->lang->line("total"); ?></th>
                                    <th><?php echo $this->lang->line("status"); ?></th>
                                    <th style="text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="<?= $assets ?>plugins/daterangepicker/moment.min.js"></script>
<script src="<?= $assets ?>plugins/daterangepicker/daterangepicker.js"></script>

<script>
    function sale_valor(valor, type, row) {
        if(type === 'display') {
            return 'R$ ' + valor.replace('.', ',');
        }
     
        return valor;
    }
    
    function sale_status(status, type, row) {
        if (status === 'canceled') {
            return 'Cancelada';
        }
        
        if (status === 'pend-cancel') {
            return 'Pend. Cancela';
        }

        return 'Ativa';
    }

    function dt_actions(data, type, row) {
        var parts = data.split('|');

        if (row[5] === "canceled" || row[5] === "pend-cancel") {
            return parts[0];
        }

        return parts.join('');
    }

    var dataTable;
    
    $(document).ready(function () {
        $('#data_range').daterangepicker({
            locale: daterange_locale,
            startDate: moment(<?= $date_start ?>),
            endDate: moment(<?= $date_end ?>)
        }, function (start, end, label) {
            $('#date_start').val(start.format('x'));
            $('#date_end').val(end.format('x'));
            
            dataTable._fnAjaxUpdate();
        });

        var dt_data = {};
        
        dataTable = $('#SLData').dataTable({
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, '<?= lang('all'); ?>']],
            "aaSorting": [[0, "desc"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true,
            'bServerSide': true,
            'sAjaxSource': '<?= site_url('sales/get_sales') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                
                aoData.push({name: "start", value: $('#date_start').val()});
                
                aoData.push({name: "end", value: $('#date_end').val()});
                
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': function(json) {
                        dt_data = json;
                        fnCallback(json);
                    }
                });
            },
            "aoColumns": [
                {"sClass": "text-center"},
                {"mRender": hrld, "sClass": "text-center"},
                {"sClass": "text-center"},
                {"sClass": "text-center"},
                {"mRender": sale_valor, "sClass": "text-right"},
                {"sClass": "text-center", "mRender": sale_status},
                {"bSortable": false, "bSearchable": false, "mRender": dt_actions}
            ],
            "fnFooterCallback": function(nFoot, aData, iStart, iEnd, aiDisplay) {
            
                if ($('#date_start').val() === $('#date_end').val()) {
                    nFoot.getElementsByTagName('th')[4].innerHTML = 'R$ ' + Number(dt_data.footer.total).toLocaleString();
                } else {
                    nFoot.getElementsByTagName('th')[4].innerHTML = '';
                }
            }
        });
    });
</script>