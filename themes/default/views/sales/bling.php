<link href="<?= $assets ?>plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css"/>
<style>
    .table-hover>tbody>tr:hover {
        background-color: #cababa;
    }
</style>
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
                                    <th>Origem</th>
                                    <th>Peças</th>
                                    <th>Valor</th>
                                    <th>Situação</th>
                                    <th style="text-align:center;">
                                        <?php echo $this->lang->line("actions"); ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal" data-easein="flipYIn" id="posModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>

<script src="<?= $assets ?>plugins/daterangepicker/moment.min.js"></script>
<script src="<?= $assets ?>plugins/daterangepicker/daterangepicker.js"></script>

<script>
     function sale_valor(valor, type, row) {
        if(type === 'display') {
            return 'R$ ' + valor.replace('.', ',');
        }
     
        return valor;
    }
    
    function dt_actions(data, type, row) {

        var actions = '\n\
            <a href="#" onclick="verPedido(' + data + ')" title="Visualizar" class="tip btn btn-primary btn-xs" data-original-title="Visualizar">\n\
                <i class="fa fa-file-text-o"></i>\n\
            </a>';

        if (row[6] === "Em aberto") {
            actions += '\
                <a href="javascript:void(0);" onclick="confirmarPedido(' + data + ')" title="Confirmar pedido" class="tip btn btn-success btn-xs" data-original-title="Confirmar pedido">\n\
                    <i class="fa fa-check-square-o"></i>\n\
                </a>';
        }

        return '\n\
            <div class="text-center">\n\
                <div class="btn-group actions">' + actions + '</div>\n\
            </div>';
    }
    
    function verPedido(id) {
        
        $('#posModal').modal();
        
        $.get('<?= site_url('sales/biling_view') ?>' + '/' + id, function (html) {
            $('#posModal').html(html);
        });
    }

    function confirmarPedido(id) {
        var confirma = window.confirm('Tem certeza que deseja confirmar o pedido?');

        if (!confirma) {
            return;
        }

        var post = {
            id: id,
            '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
        };

        $.post('<?= site_url('sales/biling_confirma') ?>', post, function () {
            dataTable._fnAjaxUpdate();
        });
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
            "aaSorting": [[1, "desc"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true,
            'bServerSide': true,
            'sAjaxSource': '<?= site_url('sales/get_sales_bling') ?>',
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
                    'success': function (json) {
                        dt_data = json;
                        fnCallback(json);
                    }
                });
            },
            "aoColumns": [
                {"sClass": "text-center"},
                {"sClass": "text-center"},
                {"sClass": "text-center"},
                {"sClass": "text-center"},
                {"sClass": "text-center"},
                {"sClass": "text-center", mRender: sale_valor},
                {"sClass": "text-center"},
                {"sClass": "text-center", bSortable: false, bSearchable: false, mRender: dt_actions}
            ]
        });
    });
</script>