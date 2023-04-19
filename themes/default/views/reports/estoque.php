<link href="<?= $assets ?>plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

<style>
    #spData_wrapper .col-xs-12 {
        padding: 0;
    }
    table.dataTable thead > tr > th {
        font-size: 13px;
        text-align: center;
        padding: 8px 0;
        border-bottom: 1px solid #666;
    }
    table.dataTable > tbody > tr > td {
        text-align: center;
        padding: 8px 0;
        border: none;
        border-bottom: 1px solid #666;
        border-right: 1px solid #666;
    }

    table.dataTable > tbody > tr > td:nth-child(2) {
        text-align: left;
        padding: 8px;
    }

    table.dataTable thead > tr > th > span {
        margin-right: 16px;
    }
    .td-bold {
        font-weight: bold;
    }
    .td-bg-low {
        background-color: #ff0000;
    }
    .td-bg-med {
        background-color: #ffff00;
    }
    .td-bg-high {
        background-color: #a4c2f4;
    }
    .td-bg-est {
        background-color: #fff2cc;
    }
    .td-bg-sale {
        background-color: #b7e1cd;
    }
    .table-hover>tbody>tr:hover {
        background-color: #cababa;
    }
</style>
<script src="<?= $assets ?>plugins/daterangepicker/moment.min.js"></script>
<script src="<?= $assets ?>plugins/daterangepicker/daterangepicker.js"></script>
<script>
    var dataTable;

    function load_data() {
        $('#spData_processing').css('visibility', 'visible');

        var url = '<?= site_url('reports/get_relatorios_estoque') ?>';
        
        url += "?start=" + $('#date_start').val();
        url += "&end=" + $('#date_end').val();
        
        $.get(url, function (d) {
            load_table(d);
            $('#spData_processing').css('visibility', 'hidden');
        });
    }

    function column_data(data, type, full) {
      return data[0];
    }

    var thead_th = [];

    function load_table(data) {
        if (dataTable) {
            dataTable.fnDestroy();
        }

        dataTable = $('#spData').dataTable({
            sDom: 'ftr',
            "aLengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, '<?= lang('all'); ?>']
            ],
            "aaSorting": [
                [data[0] ? data[0].length - 2 : 0, "desc"]
            ],
            "iDisplayLength": -1,
            'bResponsive': true,
            'bProcessing': true,
            'sScrollY': (window.innerHeight - 280) + 'px',
            'aaData': data,
            'aoColumnDefs': [
                     <?php
                     foreach ($thead as $i => $col) {
                        echo "{aTargets: [$i], mRender: column_data},";
                     }
                    ?>
            ],
            fnRowCallback: function(tr, data, index) {
                $.each(data, function (i, d) {
                    var td = $('td:eq('+i+')', tr);

                    td.text(d[0]);

                    if (d[1]) {
                        td.addClass(d[1]);
                    }
                });

                $('td:eq(3)', tr).text('R$ ' + data[3]);
            }
        });

        $('#spData').css('width', '100%');
    }
    
    $(document).ready(function () {
        load_table([]);
        load_data();
    });

</script>


<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header" style="padding: 20px 10px">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                    <div class="box-tools">
                        <div style="float: left; margin-right: 5px">
                            <a href="#" class="btn btn-success" onclick="load_data();">
                                <i class="fa fa-refresh"></i>
                                Atualizar
                            </a>
                        </div>
                        <div style="width: 210px; float: right;">
                            <div class="input-group">
                                <input type="text" class="form-control pull-right" id="data_range" name="data_range">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="hidden" id="date_start" value="<?=$date_start?>">
                                <input type="hidden" id="date_end" value="<?=$date_end?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="spData" class="table table-bordered table-hover table-striped">
                            <thead>
                                <tr>
                                    <?php
                                    foreach ($thead as $col) {
                                        echo "<th><span>$col</span></th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="<?= count($thead) ?>" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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

<script>
    $(document).ready(function () {
        $('#data_range').daterangepicker({
            locale: daterange_locale,
            startDate: moment(<?= $date_start ?>),
            endDate: moment(<?= $date_end ?>)
        }, function (start, end, label) {
            $('#date_start').val(start.format('x'));
            $('#date_end').val(end.format('x'));
            load_data();
        });
    });
</script>
