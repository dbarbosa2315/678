<?php (defined('BASEPATH')) or exit('No direct script access allowed'); ?>

<script type="text/javascript">
    $(document).ready(function () {
        function image(n) {
            
            if (n !== null) {
                return '<div style="text-align: center; margin: 0 5px;">\n\
                    <a href="<?= URL_IMAGES ?>/uploads/' + n + '" class="open-image">\n\
                        <img src="<?= URL_IMAGES ?>/uploads/thumbs/' + n + '" alt="" style="width:32px;max-width:32px">\n\
                    </a>\n\
                </div>';
            }
            
            return '';
        }

        function method(n) {
            return (n == 0) ? '<span class="label label-primary"><?= lang('inclusive'); ?></span>' : '<span class="label label-warning"><?= lang('exclusive'); ?></span>';
        }
        $('#fileData').dataTable({
            "aLengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, '<?= lang('all'); ?>']
            ],
            "aaSorting": [
                [1, "asc"]
            ],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true,
            'bServerSide': true,
            'sAjaxSource': '<?= site_url('products/get_products') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({
                    'dataType': 'json',
                    'type': 'POST',
                    'url': sSource,
                    'data': aoData,
                    'success': fnCallback
                });
            },
            "aoColumns": [{
                    "mRender": image,
                    "bSortable": false
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    sClass: "text-center"
                }, {
                    "bSortable": false,
                    "bSearchable": false
                }]
        });
        //{"data":"tax_method","render":method},
        $('#fileData').on('click', '.image', function () {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src', a_href);
            $('#picModal').modal();
            return false;
        });
        $('#fileData').on('click', '.barcode', function () {
            var a_href = $(this).attr('href');
            var code = $(this).attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src', a_href);
            $('#picModal').modal();
            return false;
        });
        $('#fileData').on('click', '.open-image', function () {
            var a_href = $(this).attr('href');
            var code = $(this).closest('tr').find('.image').attr('id');
            $('#myModalLabel').text(code);
            $('#product_image').attr('src', a_href);
            $('#picModal').modal();
            return false;
        });


    });
</script>
<style type="text/css">
    #dvProdutos table th, #dvProdutos table td {
        padding: 5px;
    }

    #dvProdutos table td {
        height: 70px;
        vertical-align: top;
    }

    .input-group .input-group-addon {
        background-color: #dd4b39;
        border-color: #dd4b39;
    }
    .input-group .input-group-addon a {
        color: #fff;
    }

</style>
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><?= lang('list_results'); ?></h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="fileData" class="table table-striped table-bordered table-hover" style="margin-bottom:5px;">
                            <thead>
                                <tr class="active">
                                    <th style="width: 80px"><?= lang("image"); ?></th>
                                    <th style="width: 80px"><?= lang("code"); ?></th>
                                    <th><?= lang("name"); ?></th>
                                    <th>EAN</th>
                                    <th style="width: 60px"><?= lang("category"); ?></th>
                                    <th>Modelo</th>
                                    <th><?= lang("quantity"); ?></th>
                                    <th><?= lang("price"); ?></th>
                                    <th><?= lang("actions"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="10" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="modal fade" id="picModal" tabindex="-1" role="dialog" aria-labelledby="picModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                                    <h4 class="modal-title" id="myModalLabel">title</h4>
                                </div>
                                <div class="modal-body text-center">
                                    <img id="product_image" src="" alt="" style="max-width: 500px"/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function variants(ev, elm, id) {        
        ev.preventDefault();
        
        if ($('#variants-' + id).length > 0) {
            $('#variants-' + id).remove();
            return;
        }

        var tr = $(elm).parent().parent().parent().parent();

        $.get('<?= site_url('products/variants'); ?>/'+id, function (rows) {

            var row = '<tr id="variants-' + id + '">\n\
                <td colspan="3">\n\
                    <table class="table">\n\
                        <thead>\n\
                            <tr>\n\
                                <th style="text-align: left">Tamanho</th>\n\
                                <th style="text-align: left">Cor</th>\n\
                                <th class="text-center">Qtd</th>\n\
                            </tr>\n\
                        </thead>\n\
                        <tbody>';
                            $.each(rows, function (i, d) {
                                row += '<tr>';
                                    row += '<td style="text-align: left">'+d.size+'</td>';
                                    row += '<td style="text-align: left">'+d.color+'</td>';
                                    row += '<td class="text-center">'+d.quantity+'</td>';
                                row += '</tr>';
                            });
                    row += '</tbody>\n\
                    </table>\n\
                </td>\n\
            </tr>';

            $(row).insertAfter(tr);
        });
    }
</script>