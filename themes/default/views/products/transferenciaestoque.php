<style>
    #varTable tr td:nth-child(1) {
        width: 180px;
    } 
    #varTable tr td:nth-child(2) {
        width: 240px;
    } 
    #varTable tr td:nth-child(3) {
        width: 140px;
    } 
</style>
<section class="content">
    <div class="row">
        <?php if (!empty($arrTransferencias)) : ?>
            <?php if ($arrTransferencias->enviadas ||  $arrTransferencias->recebidas->pendentes || $arrTransferencias->recebidas->outras) : ?>

                <?php

                $arrStatus = [
                    STATUS_TRANSFERENCIA_PENDENTE => ['lbl' => 'warning', 'desc' => 'PENDENTE'],
                    STATUS_TRANSFERENCIA_CONFIRMADA => ['lbl' => 'success', 'desc' => 'CONFIRMADA'],
                    STATUS_TRANSFERENCIA_CANCELADA => ['lbl' => 'danger', 'desc' => 'CANCELADA'],
                ];

                ?>

                <script>
                    var arrStatus = $.parseJSON('<?= json_encode($arrStatus) ?>');
                </script>

                <?php if ($arrTransferencias->recebidas->pendentes) : ?>

                    <div class="col-xs-12">
                        <div class="box box-danger">
                            <div class="box-header">
                                <h3 class="box-title">RECEBIDAS (<b>PENDENTES</b>)</h3>

                            </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Data</th>
                                            <th>Produto</th>
                                            <th>Transação</th>
                                            <th>QTD Anterior</th>
                                            <th>QTD Transferida</th>
                                            <th>QTD Final</th>
                                            <th>Status</th>
                                            <?php if(CODIGO_LOJA === 'ONLINE'): ?>
                                            <th>Ações</th>
                                            <?php else:?>
                                            <th>Confirmar</th>
                                            <?php endif;?>
                                        </tr>

                                        <?php foreach ($arrTransferencias->recebidas->pendentes as $arrRecebidas) : ?>

                                            <?php $d = new DateTime($arrRecebidas->data_solicitacao); ?>

                                            <tr class="tr_confirmar_transferencia_erro_<?= $arrRecebidas->id ?>">
                                                <td><?= $d->format('d/m/Y H:i:s') ?></td>
                                                <td><?= $arrRecebidas->produto_completo ?></td>
                                                <td><?= getTipoLD($arrRecebidas->cod_loja_origem) ?> <?= $arrRecebidas->cod_loja_origem ?> &rarr; <?= getTipoLD($arrRecebidas->cod_loja_destino) ?> <?= $arrRecebidas->cod_loja_destino ?></td>
                                                <td class="ignore ignoreTransferenciaErro"><?= ($arrRecebidas->qtd_atual_loja_destino) ? $arrRecebidas->qtd_atual_loja_destino : '-' ?></td>
                                                <td class="ignoreTransferenciaErro"><?= $arrRecebidas->qtd_transferir ?></td>
                                                <td class="ignore ignoreTransferenciaErro"><?= ($arrRecebidas->qtd_atual_loja_destino) ? (int)$arrRecebidas->qtd_atual_loja_destino + (int)$arrRecebidas->qtd_transferir : '-' ?></td>
                                                <td><span class="label label-<?= $arrStatus[$arrRecebidas->status]['lbl'] ?>"><?= $arrStatus[$arrRecebidas->status]['desc'] ?></span></td>
                                                <?php if(CODIGO_LOJA === 'ONLINE'): ?>
                                                <td class="ignore ignoreTransferenciaErro">
                                                    <div class="text-center">
                                                        <div class="btn-group actions">
                                                            <a href="javascript:void(0);" onclick="confirmarTransferenciaOnline(<?= $arrRecebidas->id ?>, <?= $arrRecebidas->id_produto ?>, <?= $arrRecebidas->qtd_transferir ?>)" title="Confirmar Transferência" class="tip btn btn-success btn-xs" data-original-title="Confirmar">
                                                                <i class="fa fa-check-square-o"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <?php else:?>
                                                <td class="ignore ignoreTransferenciaErro">
                                                    <input data-id-transferencia="<?= $arrRecebidas->id ?>" type="checkbox" class="chkConfirmar" />
                                                </td>
                                                <?php endif;?>
                                               
                                            </tr>

                                            <script>
                                                $('#btnConfirmarTransferenciaErro_<?= $arrRecebidas->id ?>').click(function() {

                                                    var idTransferencia = $(this).data('id-transferencia');
                                                    var qtd_atual = $(this).data('qtd_transferir');
                                                    var cod_produto = $(this).data('cod-produto');

                                                    var htmlTransferencias = "<tr>" + $(".tr_confirmar_transferencia_erro_" + idTransferencia).clone().find('.ignoreTransferenciaErro').remove().end().html();
                                                    htmlTransferencias += "<td><input type='number' name='qtd_transferir' value='" + qtd_atual + "'></td></tr>";

                                                    $('#confirmar_transferencia_com_erro_id_transferencia').val(idTransferencia);
                                                    $('#confirmar_transferencia_com_erro_cod_produto').val(cod_produto);
                                                    $('#confirmar_transferencia_com_erro_qtd_atual').val(qtd_atual);
                                                    $('#mdlConfirmarTransferenciaEstoqueComErro_body').html(htmlTransferencias);
                                                    $("#mdlConfirmarTransferenciaEstoqueComErro").modal("show");

                                                });
                                            </script>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <?php if ($arrTransferencias->arrTotais->recebidas->pendentes) : ?>
                                    <button id="btnConfirmar" type="button" onclick='mdlConfirmarTransferenciasPendentes();' class="btn btn-success btn-flat pull-right disabled">CONFIRMAR SELECIONADAS</button>
                                <?php endif; ?>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

                    <script>
                        $('.chkConfirmar').on('ifChanged ', function() {

                            var btnConfirmarDisable = true;
                            $('.chkConfirmar').each(function() {
                                if (this.checked) {

                                    btnConfirmarDisable = false;
                                }
                            });

                            if (btnConfirmarDisable) {
                                $('#btnConfirmar').addClass('disabled');
                            } else {

                                $('#btnConfirmar').removeClass('disabled');
                            }
                        });

                        function mdlConfirmarTransferenciasPendentes() {

                            var arr = []
                            var htmlTransferencias = "";

                            $('.chkConfirmar').each(function() {
                                if (this.checked) {

                                    var idTransferencia = $(this).data("id-transferencia");
                                    arr.push(idTransferencia);
                                    htmlTransferencias += "<tr>" + $(".tr_confirmar_transferencia_erro_" + idTransferencia).clone().find('.ignore').remove().end().html() + "</tr>";
                                }
                            });

                            $('#arrTransferenciasPendentes').val(JSON.stringify(arr));
                            $('#mdlConfirmarTransferenciasPendentes_body').html(htmlTransferencias);
                            $("#mdlConfirmarTransferenciasPendentes").modal("show");

                        }
                    </script>

                    <div class="modal fade bd-example-modal-lg" id="mdlConfirmarTransferenciasPendentes">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Confirmar Transferência de Estoque</h4>
                                </div>

                                <div class="modal-body">

                                    <div class="col-xs-12">
                                        <div class="box box-success">
                                            <div class="box-header">
                                                <h3 class="box-title">Lista de transferências pendentes</h3>
                                            </div>
                                            <!-- /.box-header -->
                                            <div class="box-body table-responsive no-padding">
                                                <table class="table table-hover">
                                                    <thead>

                                                        <th>Data</th>
                                                        <th>Produto</th>
                                                        <th>Transação</th>
                                                        <th>Quantidade à transferir</th>
                                                        <th>Status</th>

                                                    </thead>
                                                    <tbody id="mdlConfirmarTransferenciasPendentes_body">

                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>
                                        <!-- /.box -->
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <?= form_open_multipart("products/confirmartransferenciaspendentes",  array('id' => 'formConfirmarTransferenciasPendentes', 'class' => 'validation')); ?>
                                    <input type="hidden" id="arrTransferenciasPendentes" name="arrTransferenciasPendentes">
                                    <button type="submit" class="btn btn-secondary btn-flat" data-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-success btn-flat">CONFIRMAR</button>
                                    <?= form_close(); ?>
                                </div>

                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal fade bd-example-modal-lg" id="mdlConfirmarTransferenciaEstoqueComErro">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Confirmar Transferência de Estoque com Erro</h4>
                                </div>
                                <?= form_open_multipart("products/confirmarTransferenciaEstoqueComErro",  array('id' => 'formEditarEnvioTransfer', 'class' => 'validation')); ?>
                                <div class="modal-body">

                                    <div class="col-xs-12">
                                        <div class="box box-danger">
                                            <!-- /.box-header -->
                                            <div class="box-body table-responsive no-padding">
                                                <table class="table table-hover">
                                                    <thead>

                                                        <th>Data</th>
                                                        <th>Produto</th>
                                                        <th>Transação</th>
                                                        <th>Status</th>
                                                        <th>Nova quantidade</th>

                                                    </thead>
                                                    <tbody id="mdlConfirmarTransferenciaEstoqueComErro_body">


                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>
                                        <!-- /.box -->
                                    </div>

                                </div>
                                <div class="modal-footer">

                                    <input type="hidden" id="confirmar_transferencia_com_erro_id_transferencia" name="id_transferencia">
                                    <input type="hidden" id="confirmar_transferencia_com_erro_cod_produto" name="cod_produto">
                                    <input type="hidden" id="confirmar_transferencia_com_erro_qtd_atual" name="qtd_atual">
                                    <button type="submit" class="btn btn-secondary btn-flat" data-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-danger btn-flat">CONFIRMAR </button>

                                </div>
                                <?= form_close(); ?>

                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                <?php endif; ?>

                <?php if ($arrTransferencias->recebidas->outras) : ?>

                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">RECEBIDAS</h3>
                                <div class="box-tools">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 250px;">
                                        <input id="pesquisaRecebidas" type="text" name="table_search" class="form-control pull-right" placeholder="Código ou Nome do produto">

                                        <div class="input-group-btn">
                                            <button id="btnPesquisaRecebidas" type="button" class="btn btn-default"><i id="iconPesquisaRecebidas" class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Produto</th>
                                            <th>Transação</th>
                                            <th>QTD Anterior</th>
                                            <th>QTD Transferida</th>
                                            <th>QTD Final</th>
                                            <th>OBS</th>
                                            <th>Status</th>
                                        </tr>

                                    </thead>
                                    <tbody id="tbodyArrTransferenciasRecebidas">

                                        <?php foreach ($arrTransferencias->recebidas->outras as $arrRecebidas) : ?>

                                            <?php $d = new DateTime($arrRecebidas->data_solicitacao); ?>

                                            <tr>
                                                <td><?= $d->format('d/m/Y H:i:s') ?></td>
                                                <td><?= $arrRecebidas->produto_completo ?></td>
                                                <td><?= getTipoLD($arrRecebidas->cod_loja_origem) ?> <?= $arrRecebidas->cod_loja_origem ?> &rarr; <?= getTipoLD($arrRecebidas->cod_loja_destino) ?> <?= $arrRecebidas->cod_loja_destino ?></td>
                                                <td><?= ($arrRecebidas->qtd_atual_loja_destino) ? $arrRecebidas->qtd_atual_loja_destino : '-' ?></td>
                                                <td><?= $arrRecebidas->qtd_transferir ?></td>
                                                <td><?= ($arrRecebidas->qtd_atual_loja_destino) ? (int)$arrRecebidas->qtd_atual_loja_destino + (int)$arrRecebidas->qtd_transferir : '-' ?></td>
                                                <td style="max-width: 250px;"><?= ($arrRecebidas->obs) ? $arrRecebidas->obs : '-' ?></td>
                                                <td><span class="label label-<?= $arrStatus[$arrRecebidas->status]['lbl'] ?>"><?= $arrStatus[$arrRecebidas->status]['desc'] ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <button id="btnMaisRecebidas" type="button" class="btn btn-default btn-block btn-sm">Mais</button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

                    <script>
                        var start = <?= TRANSFERENCIAS_POR_PG ?>;
                        var qtd_por_pg = <?= TRANSFERENCIAS_POR_PG ?>;


                        function zeraBuscas() {
                            start = 0;
                            qtd_por_pg = <?= TRANSFERENCIAS_POR_PG ?>;
                        }

                        function pesquisaRecebidas() {
                            zeraBuscas();
                            $.ajax({

                                url: '<?= site_url('products/listaTransferenciasEstoque_maisRecebidas') ?>',
                                method: 'POST',
                                async: true,
                                data: {

                                    start: start,
                                    qtd_por_pg: qtd_por_pg,
                                    pesquisa: $('#pesquisaRecebidas').val()

                                },
                                beforeSend: function() {

                                    $("#btnMaisRecebidas").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Buscando ...');
                                    $("#btnMaisRecebidas").addClass('disabled');
                                    $('#iconPesquisaRecebidas').removeClass('fa-search');
                                    $('#iconPesquisaRecebidas').addClass('fa-spinner fa-spin');
                                }
                            }).done(function(json) {


                                if (json != '[]') {

                                    var arrResposta = $.parseJSON(json);
                                    var html = ""

                                    if (arrResposta.dados.length > 0) {


                                        $.each(arrResposta.dados, function(idx, arr) {

                                            var qtdAnterior = (arr.qtd_atual_loja_destino) ? arr.qtd_atual_loja_destino : '-';
                                            var qtdFinal = (arr.qtd_atual_loja_destino) ? parseFloat(arr.qtd_atual_loja_destino) + parseFloat(arr.qtd_transferir) : '-';
                                            var obs = (arr.obs) ? arr.obs : '-';

                                            html += '<tr>';
                                            html += '<td>' + dateTIMEToBR(arr.data_solicitacao) + '</td>';
                                            html += '<td>' + arr.produto_completo + '</td>';
                                            html += '<td>ORIGEM: ' + objLOJAS.dados[arr.cod_loja_origem].tipo + ' ' + arr.cod_loja_origem + ' &rarr; DESTINO: ' + objLOJAS.dados[arr.cod_loja_destino].tipo + ' ' + arr.cod_loja_destino + '</td>';
                                            html += '<td>' + qtdAnterior + '</td>';
                                            html += '<td>' + arr.qtd_transferir + '</td>';
                                            html += '<td>' + qtdFinal + '</td>';
                                            html += '<td style="max-width: 250px;">' + obs + '</td>';
                                            html += '<td><span class="label label-' + arrStatus[arr.status]['lbl'] + '">' + arrStatus[arr.status]['desc'] + '</span></td>';
                                            html += '</tr>';

                                        });


                                        start += qtd_por_pg;
                                        $("#btnMaisRecebidas").html('Mais');
                                        $("#btnMaisRecebidas").removeClass('disabled');

                                    } else {

                                        $("#btnMaisRecebidas").html('Sem mais resultados');
                                        $("#btnMaisRecebidas").addClass('disabled');
                                    }

                                    $('#iconPesquisaRecebidas').removeClass('fa-spinner fa-spin');
                                    $('#iconPesquisaRecebidas').addClass('fa-search');
                                    $('#tbodyArrTransferenciasRecebidas').html(html);

                                }

                            });
                        }

                        function buscaMaisRecebidas() {
                            $.ajax({

                                url: '<?= site_url('products/listaTransferenciasEstoque_maisRecebidas') ?>',
                                method: 'POST',
                                async: true,
                                data: {

                                    start: start,
                                    qtd_por_pg: qtd_por_pg,
                                    pesquisa: $('#pesquisaRecebidas').val()

                                },
                                beforeSend: function() {

                                    $("#btnMaisRecebidas").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Buscando ...');
                                    $("#btnMaisRecebidas").addClass('disabled');
                                }
                            }).done(function(json) {


                                if (json != '[]') {

                                    var arrResposta = $.parseJSON(json);

                                    if (arrResposta.dados.length > 0) {

                                        var html = ""

                                        $.each(arrResposta.dados, function(idx, arr) {

                                            var qtdAnterior = (arr.qtd_atual_loja_destino) ? arr.qtd_atual_loja_destino : '-';
                                            var qtdFinal = (arr.qtd_atual_loja_destino) ? parseFloat(arr.qtd_atual_loja_destino) + parseFloat(arr.qtd_transferir) : '-';
                                            var obs = (arr.obs) ? arr.obs : '-';

                                            html += '<tr>';
                                            html += '<td>' + dateTIMEToBR(arr.data_solicitacao) + '</td>';
                                            html += '<td>' + arr.produto_completo + '</td>';
                                            html += '<td>ORIGEM: ' + objLOJAS.dados[arr.cod_loja_origem].tipo + ' ' + arr.cod_loja_origem + ' &rarr; DESTINO: ' + objLOJAS.dados[arr.cod_loja_destino].tipo + ' ' + arr.cod_loja_destino + '</td>';
                                            html += '<td>' + qtdAnterior + '</td>';
                                            html += '<td>' + arr.qtd_transferir + '</td>';
                                            html += '<td>' + qtdFinal + '</td>';
                                            html += '<td style="max-width: 250px;">' + obs + '</td>';
                                            html += '<td><span class="label label-' + arrStatus[arr.status]['lbl'] + '">' + arrStatus[arr.status]['desc'] + '</span></td>';
                                            html += '</tr>';

                                        });

                                        $('#tbodyArrTransferenciasRecebidas').append(html);
                                        start += qtd_por_pg;
                                        $("#btnMaisRecebidas").html('Mais');
                                        $("#btnMaisRecebidas").removeClass('disabled');
                                    } else {

                                        $("#btnMaisRecebidas").html('Sem mais resultados');
                                        $("#btnMaisRecebidas").addClass('disabled');
                                    }

                                }

                            });
                        }

                        $('#btnMaisRecebidas').click(function() {

                            buscaMaisRecebidas()

                        });

                        $('#btnPesquisaRecebidas').click(function() {

                            pesquisaRecebidas();

                        });
                    </script>

                <?php endif; ?>


                <?php if ($arrTransferencias->enviadas->pendentes) : ?>

                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title"> ENVIADAS (<b>PENDENTES</b>)</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover">
                                    <tbody>
                                        <tr>
                                            <th>Data</th>
                                            <th>Produto</th>
                                            <th>Transação</th>
                                            <th>QTD Anterior</th>
                                            <th>QTD Transferida</th>
                                            <th>QTD Final</th>
                                            <th>Status</th>
                                            <th>OBS</th>
                                            <th>Ações</th>
                                        </tr>

                                        <?php foreach ($arrTransferencias->enviadas->pendentes as $arrEnviadas) : ?>

                                            <?php $d = new DateTime($arrEnviadas->data_solicitacao);  ?>

                                            <tr class="tr_edt_transferencia_<?= $arrEnviadas->id ?>">
                                                <td><?= $d->format('d/m/Y H:i:s') ?></td>
                                                <td><?= $arrEnviadas->produto_completo ?></td>
                                                <td><?= getTipoLD($arrEnviadas->cod_loja_origem) ?> <?= $arrEnviadas->cod_loja_origem ?> &rarr; <?= getTipoLD($arrEnviadas->cod_loja_destino) ?> <?= $arrEnviadas->cod_loja_destino ?></td>
                                                <td class="ignore"><?= ($arrEnviadas->qtd_atual_loja_origem) ? $arrEnviadas->qtd_atual_loja_origem : '-' ?></td>
                                                <td class="ignore"><?= $arrEnviadas->qtd_transferir ?></td>
                                                <td class="ignore"><?= ($arrEnviadas->qtd_atual_loja_origem) ? (int)$arrEnviadas->qtd_atual_loja_origem - (int)$arrEnviadas->qtd_transferir : '-' ?></td>
                                                <td><span class="label label-<?= $arrStatus[$arrEnviadas->status]['lbl'] ?>"><?= $arrStatus[$arrEnviadas->status]['desc'] ?></span></td>
                                                <td><?= $arrEnviadas->obs ?></td>
                                                <td class="ignore">
                                                    <div class='text-center'>

                                                        <?php if ($arrEnviadas->status == STATUS_TRANSFERENCIA_PENDENTE) : ?>
                                                            <div class='btn-group actions'>
                                                                <a id="btnEditarTransferencia_<?= $arrEnviadas->id ?>" data-id-transferencia="<?= $arrEnviadas->id ?>" data-qtd_transferir="<?= $arrEnviadas->qtd_transferir ?>" data-cod-produto="<?= $arrEnviadas->cod_produto ?>" href="javascript:void(0);" title="Editar" class="tip btn btn-warning btn-xs" data-original-title="Editar">
                                                                    <i class="fa fa-edit"></i>
                                                                </a>

                                                                <a href='<?= site_url('products/cancelartransferenciaestoque/' . $arrEnviadas->id) ?>' title='Cancelar Solicitação' onclick="return confirm('Deseja cancelar essa solicitação de transferência de estoque? Clique em OK para confirmar')" class='tip btn btn-danger btn-xs'>
                                                                    <i class='fa fa-times-circle'></i>
                                                                </a>
                                                            </div>

                                                        <?php else : ?>
                                                            <p>Nenhuma ação</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <script>
                                                $('#btnEditarTransferencia_<?= $arrEnviadas->id ?>').click(function() {

                                                    var idTransferencia = $(this).data('id-transferencia');
                                                    var qtd_atual = $(this).data('qtd_transferir');
                                                    var cod_produto = $(this).data('cod-produto');

                                                    var htmlTransferencias = "<tr>" + $(".tr_edt_transferencia_" + idTransferencia).clone().find('.ignore').remove().end().html();
                                                    htmlTransferencias += "<td><input type='number' name='qtd_transferir' value='" + qtd_atual + "'></td></tr>";

                                                    $('#edt_transferencia_id_transferencia').val(idTransferencia);
                                                    $('#edt_transferencia_cod_produto').val(cod_produto);
                                                    $('#edt_transferencia_qtd_atual').val(qtd_atual);
                                                    $('#mdlEditarEnvioTransferenciaEstoque_body').html(htmlTransferencias);
                                                    $("#mdlEditarEnvioTransferenciaEstoque").modal("show");

                                                });
                                            </script>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

                    <div class="modal fade bd-example-modal-lg" id="mdlEditarEnvioTransferenciaEstoque">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <h4 class="modal-title">Editar envio de Transferência de Estoque</h4>
                                </div>
                                <?= form_open_multipart("products/editarEnvioTransferenciaEstoque",  array('id' => 'formEditarEnvioTransferenciaEstoque', 'class' => 'validation')); ?>
                                <div class="modal-body">

                                    <div class="col-xs-12">
                                        <div class="box box-warning">
                                            <!-- /.box-header -->
                                            <div class="box-body table-responsive no-padding">
                                                <table class="table table-hover">
                                                    <thead>

                                                        <th>Data</th>
                                                        <th>Produto</th>
                                                        <th>Transação</th>
                                                        <th>Status</th>
                                                        <th>Nova quantidade</th>

                                                    </thead>
                                                    <tbody id="mdlEditarEnvioTransferenciaEstoque_body">


                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>
                                        <!-- /.box -->
                                    </div>

                                </div>
                                <div class="modal-footer">

                                    <input type="hidden" id="edt_transferencia_id_transferencia" name="id_transferencia">
                                    <input type="hidden" id="edt_transferencia_cod_produto" name="cod_produto">
                                    <input type="hidden" id="edt_transferencia_qtd_atual" name="qtd_atual">
                                    <button type="submit" class="btn btn-secondary btn-flat" data-dismiss="modal">CANCELAR</button>
                                    <button type="submit" class="btn btn-warning btn-flat">EDITAR </button>

                                </div>
                                <?= form_close(); ?>

                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                <?php endif; ?>

                <?php if ($arrTransferencias->enviadas->outras) : ?>

                    <div class="col-xs-12">
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title"> ENVIADAS</h3>
                                <div class="box-tools">
                                    <div class="input-group input-group-sm hidden-xs" style="width: 250px;">
                                        <input id="pesquisaEnviadas" type="text" name="table_search" class="form-control pull-right" placeholder="Código ou Nome do produto">

                                        <div class="input-group-btn">
                                            <button id="btnPesquisaEnviadas" type="button" class="btn btn-default"><i id="iconPesquisaEnviadas" class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Produto</th>
                                            <th>Transação</th>
                                            <th>QTD Anterior</th>
                                            <th>QTD Transferida</th>
                                            <th>QTD Final</th>
                                            <th>Status</th>
                                            <th>OBS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyArrTransferenciasEnviadas">

                                        <?php foreach ($arrTransferencias->enviadas->outras as $arrEnviadas) : ?>

                                            <?php $d = new DateTime($arrEnviadas->data_solicitacao);  ?>

                                            <tr class="tr_edt_transferencia_<?= $arrEnviadas->id ?>">
                                                <td><?= $d->format('d/m/Y H:i:s') ?></td>
                                                <td><?= $arrEnviadas->produto_completo ?></td>
                                                <td><?= getTipoLD($arrEnviadas->cod_loja_origem) ?> <?= $arrEnviadas->cod_loja_origem ?> &rarr; <?= getTipoLD($arrEnviadas->cod_loja_destino) ?> <?= $arrEnviadas->cod_loja_destino ?></td>
                                                <td class="ignore"><?= ($arrEnviadas->qtd_atual_loja_origem) ? $arrEnviadas->qtd_atual_loja_origem : '-' ?></td>
                                                <td class="ignore"><?= $arrEnviadas->qtd_transferir ?></td>
                                                <td class="ignore"><?= ($arrEnviadas->qtd_atual_loja_origem) ? (int)$arrEnviadas->qtd_atual_loja_origem - (int)$arrEnviadas->qtd_transferir : '-' ?></td>
                                                <td><span class="label label-<?= $arrStatus[$arrEnviadas->status]['lbl'] ?>"><?= $arrStatus[$arrEnviadas->status]['desc'] ?></span></td>
                                                <td><?= $arrEnviadas->obs ?></td>
                                            </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="box-footer">
                                <button id="btnMaisEnviadas" type="button" class="btn btn-default btn-block btn-sm">Mais</button>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

                    <script>
                        var start_enviadas = <?= TRANSFERENCIAS_POR_PG ?>;
                        var qtd_por_pg_enviadas = <?= TRANSFERENCIAS_POR_PG ?>;

                        function zeraBuscasEnviadas() {
                            start_enviadas = 0;
                            qtd_por_pg_enviadas = <?= TRANSFERENCIAS_POR_PG ?>;
                        }

                        function pesquisaEnviadas() {
                            zeraBuscasEnviadas();
                            $.ajax({

                                url: '<?= site_url('products/listaTransferenciasEstoque_maisEnviadas') ?>',
                                method: 'POST',
                                async: true,
                                data: {

                                    start: start_enviadas,
                                    qtd_por_pg: qtd_por_pg_enviadas,
                                    pesquisa: $('#pesquisaEnviadas').val()

                                },
                                beforeSend: function() {

                                    $("#btnMaisEnviadas").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Buscando ...');
                                    $("#btnMaisEnviadas").addClass('disabled');
                                    $('#iconPesquisaEnviadas').removeClass('fa-search');
                                    $('#iconPesquisaEnviadas').addClass('fa-spinner fa-spin');
                                }
                            }).done(function(json) {


                                if (json != '[]') {

                                    var arrResposta = $.parseJSON(json);
                                    var html = ""

                                    if (arrResposta.dados.length > 0) {


                                        $.each(arrResposta.dados, function(idx, arr) {

                                            var qtdAnterior = (arr.qtd_atual_loja_origem) ? arr.qtd_atual_loja_origem : '-';
                                            var qtdFinal = (arr.qtd_atual_loja_origem) ? parseFloat(arr.qtd_atual_loja_origem) - parseFloat(arr.qtd_transferir) : '-';
                                            var obs = (arr.obs) ? arr.obs : '-';

                                            html += '<tr>';
                                            html += '<td>' + dateTIMEToBR(arr.data_solicitacao) + '</td>';
                                            html += '<td>' + arr.produto_completo + '</td>';
                                            html += '<td>ORIGEM: ' + objLOJAS.dados[arr.cod_loja_origem].tipo + ' ' + arr.cod_loja_origem + ' &rarr; DESTINO: ' + objLOJAS.dados[arr.cod_loja_destino].tipo + ' ' + arr.cod_loja_destino + '</td>';
                                            html += '<td>' + qtdAnterior + '</td>';
                                            html += '<td>' + arr.qtd_transferir + '</td>';
                                            html += '<td>' + qtdFinal + '</td>';
                                            html += '<td><span class="label label-' + arrStatus[arr.status]['lbl'] + '">' + arrStatus[arr.status]['desc'] + '</span></td>';
                                            html += '<td style="max-width: 250px;">' + obs + '</td>';
                                            html += '</tr>';

                                        });


                                        start_enviadas += qtd_por_pg_enviadas;
                                        $("#btnMaisEnviadas").html('Mais');
                                        $("#btnMaisEnviadas").removeClass('disabled');

                                    } else {

                                        $("#btnMaisEnviadas").html('Sem mais resultados');
                                        $("#btnMaisEnviadas").addClass('disabled');
                                    }

                                    $('#iconPesquisaEnviadas').removeClass('fa-spinner fa-spin');
                                    $('#iconPesquisaEnviadas').addClass('fa-search');
                                    $('#tbodyArrTransferenciasEnviadas').html(html);

                                }

                            });
                        }

                        function buscaMaisEnviadas() {
                            $.ajax({

                                url: '<?= site_url('products/listaTransferenciasEstoque_maisEnviadas') ?>',
                                method: 'POST',
                                async: true,
                                data: {

                                    start: start_enviadas,
                                    qtd_por_pg: qtd_por_pg_enviadas,
                                    pesquisa: $('#pesquisaEnviadas').val()

                                },
                                beforeSend: function() {

                                    $("#btnMaisEnviadas").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Buscando ...');
                                    $("#btnMaisEnviadas").addClass('disabled');
                                }
                            }).done(function(json) {


                                if (json != '[]') {

                                    var arrResposta = $.parseJSON(json);

                                    if (arrResposta.dados.length > 0) {

                                        var html = ""

                                        $.each(arrResposta.dados, function(idx, arr) {

                                            var qtdAnterior = (arr.qtd_atual_loja_origem) ? arr.qtd_atual_loja_origem : '-';
                                            var qtdFinal = (arr.qtd_atual_loja_origem) ? parseFloat(arr.qtd_atual_loja_origem) - parseFloat(arr.qtd_transferir) : '-';
                                            var obs = (arr.obs) ? arr.obs : '-';

                                            html += '<tr>';
                                            html += '<td>' + dateTIMEToBR(arr.data_solicitacao) + '</td>';
                                            html += '<td>' + arr.produto_completo + '</td>';
                                            html += '<td>ORIGEM: ' + objLOJAS.dados[arr.cod_loja_origem].tipo + ' ' + arr.cod_loja_origem + ' &rarr; DESTINO: ' + objLOJAS.dados[arr.cod_loja_destino].tipo + ' ' + arr.cod_loja_destino + '</td>';
                                            html += '<td>' + qtdAnterior + '</td>';
                                            html += '<td>' + arr.qtd_transferir + '</td>';
                                            html += '<td>' + qtdFinal + '</td>';
                                            html += '<td><span class="label label-' + arrStatus[arr.status]['lbl'] + '">' + arrStatus[arr.status]['desc'] + '</span></td>';
                                            html += '<td style="max-width: 250px;">' + obs + '</td>';
                                            html += '</tr>';

                                        });

                                        $('#tbodyArrTransferenciasEnviadas').append(html);
                                        start_enviadas += qtd_por_pg_enviadas;
                                        $("#btnMaisEnviadas").html('Mais');
                                        $("#btnMaisEnviadas").removeClass('disabled');
                                    } else {

                                        $("#btnMaisEnviadas").html('Sem mais resultados');
                                        $("#btnMaisEnviadas").addClass('disabled');
                                    }

                                }

                            });
                        }

                        $('#btnMaisEnviadas').click(function() {

                            buscaMaisEnviadas()

                        });

                        $('#btnPesquisaEnviadas').click(function() {

                            pesquisaEnviadas();

                        });
                    </script>

                <?php endif; ?>

            <?php else : ?>
                <div class="col-xs-12">

                    <h4>Nenhuma informação encontrada</h4>

                </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="col-xs-12">

                <h4>Nenhuma informação encontrada</h4>

            </div>
        <?php endif; ?>

    </div>
    <script>
        function buscaProduto(termo) {

            $.ajax({

                url: '<?= site_url('products/listaTransferenciasEstoque_maisEnviadas') ?>',
                method: 'POST',
                async: true,
                data: {

                    start: start_enviadas,
                    qtd_por_pg: qtd_por_pg_enviadas,
                    pesquisa: $('#pesquisaEnviadas').val()

                },
                beforeSend: function() {

                    $("#btnMaisEnviadas").html('<i class="fa fa-spinner fa-spin"></i>&nbsp;&nbsp;Buscando ...');
                    $("#btnMaisEnviadas").addClass('disabled');
                    $('#iconPesquisaEnviadas').removeClass('fa-search');
                    $('#iconPesquisaEnviadas').addClass('fa-spinner fa-spin');
                }
            }).done(function(json) {


                if (json != '[]') {

                    var arrResposta = $.parseJSON(json);
                    var html = ""

                    if (arrResposta.dados.length > 0) {


                        $.each(arrResposta.dados, function(idx, arr) {

                            var qtdAnterior = (arr.qtd_atual_loja_origem) ? arr.qtd_atual_loja_origem : '-';
                            var qtdFinal = (arr.qtd_atual_loja_origem) ? parseFloat(arr.qtd_atual_loja_origem) - parseFloat(arr.qtd_transferir) : '-';
                            var obs = (arr.obs) ? arr.obs : '-';

                            html += '<tr>';
                            html += '<td>' + dateTIMEToBR(arr.data_solicitacao) + '</td>';
                            html += '<td>' + arr.produto_completo + '</td>';
                            html += '<td>ORIGEM: ' + objLOJAS.dados[arr.cod_loja_origem].tipo + ' ' + arr.cod_loja_origem + ' &rarr; DESTINO: ' + objLOJAS.dados[arr.cod_loja_destino].tipo + ' ' + arr.cod_loja_destino + '</td>';
                            html += '<td>' + qtdAnterior + '</td>';
                            html += '<td>' + arr.qtd_transferir + '</td>';
                            html += '<td>' + qtdFinal + '</td>';
                            html += '<td><span class="label label-' + arrStatus[arr.status]['lbl'] + '">' + arrStatus[arr.status]['desc'] + '</span></td>';
                            html += '<td style="max-width: 250px;">' + obs + '</td>';
                            html += '</tr>';

                        });


                        start_enviadas += qtd_por_pg_enviadas;
                        $("#btnMaisEnviadas").html('Mais');
                        $("#btnMaisEnviadas").removeClass('disabled');

                    } else {

                        $("#btnMaisEnviadas").html('Sem mais resultados');
                        $("#btnMaisEnviadas").addClass('disabled');
                    }

                    $('#iconPesquisaEnviadas').removeClass('fa-spinner fa-spin');
                    $('#iconPesquisaEnviadas').addClass('fa-search');
                    $('#tbodyArrTransferenciasEnviadas').html(html);

                }

            });
        }
        
        function confirmarTransferenciaOnline (id, id_produto, val) {
            $('#varTable tbody').html('');
            $('#tsid').val(id);
            $('#tspid').val(id_produto);
            $('#tstotal').val(val);
            $('#tsModal .submit').attr('disabled', true);
            $('#tsModal').modal();
        }
        
        $(document.body).on('keypress', '#varTable select, #varTable input', function(ev) {
            if (ev.charCode === 13) {
                $(ev.target).trigger('change');
            }
        });
        
        $(document.body).on('change', '#varTable select, #varTable input', function() {
            var total = 0;
            
            $('#varTable tbody input[type="number"]').each(function (i, e) {
                if (e.value) {
                    total += parseInt(e.value);
                } 
            });
                        
            var tstotal = $('#tstotal').val();
            
            if (total === parseInt(tstotal)) {
                $('#tsModal .submit').attr('disabled', false);
            } else {
                $('#tsModal .submit').attr('disabled', true);
            }
        });
        
        $(document).ready(function () {
            select2_variant_parent = $('#tsModal');
            
            $('#tsModal .submit').click(function () {
                $('#tsModal .submit').attr('disabled', true);
                
                var post = $('#ts-form').serialize();
                
                post += "&<?= $this->security->get_csrf_token_name() ?>=<?= $this->security->get_csrf_hash() ?>";
                
                $.post('<?= site_url('products/transferencia_variants') ?>', post, function () {
                    location.reload();
                });
            });
        }); 
    </script>
    <?php 
    include(getcwd() . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'form_script.php');
    ?>
</section>
<div class="modal" data-easein="flipYIn" id="tsModal" tabindex="-1" role="dialog" aria-labelledby="proModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
                <h4 class="modal-title">
                    Transferência de estoque
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <form id="ts-form" method="post">
                            <table class="table items table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;background-color: #f5f5f5; width: 180px">Tamanho</th>
                                        <th style="background-color: #f5f5f5; width: 240px">Cor</th>
                                        <th style="background-color: #f5f5f5; width: 140px">Qtd.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3">
                                            <div style="max-height: 500px; overflow: auto">
                                                <table  id="varTable" class="table">
                                                    <tbody>
                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3">
                                            <div style="float: left; height: 30px; align-items: center; display: flex">
                                                <a href="#" onclick="input_variant(event)">
                                                    <i class="fa fa-plus"></i>
                                                    Adicionar
                                                </a>
                                            </div>
                                            <div style="float: left; width: 60px; margin-left: 10px">
                                                <input type="number" name="varc" class="form-control input-sm not-validate" id="variant_count" step="1" min="1" value="1">
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <input type="hidden" name="id" id="tsid">
                            <input type="hidden" name="id_produto" id="tspid">
                            <input type="hidden" id="tstotal">
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background-color: #fff">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= lang('close') ?></button>
                <button class="btn btn-success submit" disabled>Salvar</button>
            </div>
        </div>
    </div>
</div>