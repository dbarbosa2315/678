<?php
executeContingencias();
?>

<div class="modal fade bd-example-modal-lg" id="mdlTransferirEstoque">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Transferência de Estoque</h4>
            </div>
            <?= form_open_multipart("products/transferirestoque", ['id' => 'formTransferirEstoque', 'class' => 'validation']); ?>
            <div class="modal-body">


                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Inserir Produto para transferência</h3>
                        <div class="box-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input id="pesquisaInserirProdutoTrasnferencia" type="text" class="form-control pull-right" placeholder="Código ou Nome do produto">

                                <div class="input-group-btn">
                                    <button id="btnPesquisaInserirProdutoTrasnferencia" type="button" class="btn btn-default"><i id="iconPesquisaInserirProdutoTrasnferencia" class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body" id="resultadoPesquisaInserirProdutoTrasnferencia">
                        <p><i>Faça a busca pelo produto acima</i></p>
                    </div>
                </div>

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Produtos Inseridos</h3>

                    </div>
                    <div class="box-body">
                        <p id="produtosNInseridos"><i>Nenhum produto inserido</i></p>
                        <div id="dvProdutos">
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Estoque atual</th>
                                        <th>Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="form-group" id="selectLojaDestino">
                    <label for="status">Loja destino</label>
                    <select name="cod_loja_destino" id="cod_loja_destino" data-placeholder="Selecione a Loja" class="form-control select2 select2-hidden-accessible input-tip selectLojaDestino" style="width:100%;" tabindex="-1" onchange="validaTransferencia()" aria-hidden="true">
                        <option value="" selected="selected"></option>

                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button id="btnConfirmarTransferencia" type="submit" class="btn btn-success btn-flat disabled"><i class="far fa-trash-alt"></i>&nbsp; CONFIRMAR</button>
            </div>
            <?= form_close(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    var id = 1;

    function validaQtd(elemento, validarTransf) {

        var idx = $(elemento).data('id');
        var qtdAtual = parseFloat($(elemento).data('qtd-atual'));
        var qtdTrasferir = parseFloat($(elemento).val());

        if (qtdTrasferir < 1 || (qtdTrasferir > qtdAtual) || isNaN(qtdTrasferir)) {

            $('#erro_' + idx).html('&bull; Quantidade inválida! Estoque Atual: ' + qtdAtual);
            $('#erro_' + idx).show();
            $('#dvproduto_' + idx).removeClass('box-success');
            $('#dvproduto_' + idx).addClass('box-danger has-error');

        } else {

            $('#erro_' + idx).hide();
            $('#dvproduto_' + idx).addClass('box-success');
            $('#dvproduto_' + idx).removeClass('box-danger has-error');

        }

        if (validarTransf) {
            validaTransferencia();
        }


    }

    function validaTransferencia() {

        var valido = true;

        if ($('.productTransf').length) {

            $('#produtosNInseridos').hide();
            $('.productTransf').each(function() {

                var idx = $(this).data('id');
                if ($(this).hasClass('has-error') || $(this).hasClass('box-danger') || $('#qtd_transferir_' + idx).val() == "") {
                    valido = false;
                }


            });
        } else {
            $('#produtosNInseridos').show();
            valido = false;
            excluirProdutosSessao();
        }

        if (!$('#cod_loja_destino').val()) {

            valido = false;
        }



        if (!valido) {
            $('#btnConfirmarTransferencia').addClass('disabled');
        } else {           
            $('#btnConfirmarTransferencia').removeClass('disabled');
        }

        salvaProdutosSessao();

    }

    function remover(id) {

        $('#dvproduto_' + id).remove();
        validaTransferencia();
    }

    function openMdlTransferirEstoque() {
        getMdlSolicitarTransferencia(true, true, false, false, false, false, false);

    }

    function buscaProdutoByCodONome() {

        $.ajax({

            url: '<?= site_url('products/buscaProdutoByCodONome') ?>',
            method: 'POST',
            async: true,
            data: {

                pesquisa: $('#pesquisaInserirProdutoTrasnferencia').val()

            },
            beforeSend: function() {

                $('#iconPesquisaInserirProdutoTrasnferencia').removeClass('fa-search');
                $('#iconPesquisaInserirProdutoTrasnferencia').addClass('fa-spinner fa-spin');
            }

        }).done(function(json) {

            if (json != []) {

                var obj = $.parseJSON(json);

                if (obj.dados) {

                    html = '<table class="table table-bordered">';
                    html += '<tbody>';
                    html += '<tr>';
                    html += '<th>Código</th>';
                    html += '<th>Nome</th>';
                    html += '<th>Estoque</th>';
                    html += '<th>QTD Transferir</th>';
                    html += '<th>Inserir</th>';
                    html += '</tr>';

                    $.each(obj.dados, function(idx, arr) {

                        html += '<tr>';
                        html += '<td>' + arr.code + '</td>';
                        html += '<td>' + arr.name + '</td>';
                        html += '<td>' + arr.quantity + '</td>';
                        html += '<td><input type="number" id="qtd_transferir_' + arr.code + '"></td>';
                        html += '<td><a href="javascript:void(0);" title="" class="tip btn btn-success btn-xs" onclick="insereProdutoPesquisado(false,false,' + arr.id + ',\'' + arr.code + '\',\'' + arr.name + '\',\'' + arr.quantity + '\', document.getElementById(\'qtd_transferir_' + arr.code + '\').value)" data-original-title="Transferir Estoque"><i class="fa fa-long-arrow-right"></i></a></td>';
                        html += '</tr>';

                    });

                    html += '</tbody>';
                    html += '</table>';

                    $('#resultadoPesquisaInserirProdutoTrasnferencia').html(html);

                } else {

                    $('#resultadoPesquisaInserirProdutoTrasnferencia').html('<p><i>Nenhum produto encontrado!</i></p>');
                }
            }

            $('#iconPesquisaInserirProdutoTrasnferencia').removeClass('fa-spinner fa-spin');
            $('#iconPesquisaInserirProdutoTrasnferencia').addClass('fa-search');

        });
    }

    function salvaProdutosSessao() {

        var arrProdutos = [];

        $('.productTransf').each(function() {

            var idx = $(this).data('id');

            var arr = {
                idx: $(this).data('id'),
                id_produto: $('#id_produto_' + idx).val(),
                cod_produto: $('#cod_produto_' + idx).val(),
                nome_produto: $('#nome_produto_' + idx).val(),
                qtd_transferir: $('#qtd_transferir_' + idx).val(),
                qtd_atual: $('#qtd_transferir_' + idx).data('qtd-atual')
            };

            arrProdutos.push(arr);

        });

        //console.log('salvaProdutosSessao');
        //console.log(arrProdutos);

        $.ajax({

            url: '<?= site_url('products/salvaProdutosSessao') ?>',
            method: 'POST',
            async: true,
            data: {

                produtos: arrProdutos,
                id: id

            }

        });


    }

    function excluirProdutosSessao() {

        $.ajax({

            url: '<?= site_url('products/excluirProdutosSessao') ?>',
            method: 'POST',
            async: true,

        });

    }

    function insereProdutoPesquisado(buscaSessao, abreModal, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir) {

        getMdlSolicitarTransferencia(buscaSessao, abreModal, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir);
        $('#resultadoPesquisaInserirProdutoTrasnferencia').html('<p><i>Faça a busca pelo produto acima</i></p>');
        var idx = id - 1;
        validaQtd($('#qtd_transferir_' + idx), true);

    }

    function getMdlSolicitarTransferencia(buscaSessao, abreModal, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir) {

        if (buscaSessao) {


            $.ajax({

                url: '<?= site_url('products/getProdutosSessao') ?>',
                method: 'POST',
                async: true,

                beforeSend: function() {

                    $("#ajaxCall").show();

                }

            }).done(function(json) {

                if (json != 'null') {

                    var obj = $.parseJSON(json);
                    id = parseInt(obj.id);

                    if (obj.dados) {

                        $.each(obj.dados, function(idx, arr) {

                            $('#dvProdutos table tbody').append(getHtmlInserirItem(idx, arr.id_produto, arr.cod_produto, arr.nome_produto, arr.qtd_atual, arr.qtd_transferir));
                            validaQtd($('#qtd_transferir_' + arr.idx), false);


                        });
                    }

                }

                if (idProduto) {

                    var codRepetido = false;
                    var idRepetido = false;

                    $('.productTransf').each(function() {


                        if ($(this).data('cod-produto') == codProduto) {

                            codRepetido = true;
                            idRepetido = this.id;

                        }

                    });


                    if (!codRepetido) {

                        var html = getHtmlInserirItem(id, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir);

                    } else {

                        var produtoRepetido = $('#' + idRepetido);
                        $('#' + idRepetido).remove();
                        html = produtoRepetido;
                        produtoRepetido.removeClass('box-danger');
                        produtoRepetido.removeClass('box-success');
                        produtoRepetido.addClass('box-warning');
                    }

                    $('#dvProdutos table tbody').prepend(html);
                    id++;
                }

                validaTransferencia();

                if (abreModal) {
                    $("#mdlTransferirEstoque").modal('show');

                }

                $("#ajaxCall").hide();

            });
        } else {

            $("#ajaxCall").show();

            if (idProduto) {
                var codRepetido = false;
                var idRepetido = false;

                $('.productTransf').each(function() {


                    if ($(this).data('cod-produto') == codProduto) {

                        codRepetido = true;
                        idRepetido = this.id;

                    }

                });


                if (!codRepetido) {

                    var html = getHtmlInserirItem(id, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir);

                } else {

                    var produtoRepetido = $('#' + idRepetido);
                    $('#' + idRepetido).remove();
                    html = produtoRepetido;
                    produtoRepetido.removeClass('box-danger');
                    produtoRepetido.removeClass('box-success');
                    produtoRepetido.addClass('box-warning');
                }

                $('#dvProdutos table tbody').prepend(html);
                id++;
            }

            validaTransferencia();

            if (abreModal) {
                $("#mdlTransferirEstoque").modal('show');

            }

            $("#ajaxCall").hide();
        }
    }

    function getHtmlInserirItem(idx, idProduto, codProduto, nomeProduto, qtdAtual, qtdTransferir) {

        var html = '<tr data-cod-produto="' + codProduto + '" data-id="' + idx + '" class="productTransf" id="dvproduto_' + idx + '">';
    
        html += '<td>\n\
        <input type="hidden" id="id_produto_' + idx + '">\n\
        <input id="cod_produto_' + idx + '" name="arrProdutos[' + idx + '][cod_produto]" type="text" class="form-control" value="' + codProduto + '" readonly>\n\
        </td>';
        
        html += '<td>\n\
        <input id="nome_produto_' + idx + '" type="text" class="form-control" value="' + nomeProduto + '" readonly>\n\
        </td>';
        
        html += '<td>\n\
            <input id="estoque_atual_' + idx + '" type="text" class="form-control" value="' + qtdAtual + '" readonly>\n\
        </td>';
                
        html += '<td>\n\
            <div class="input-group">\n\
                <input data-qtd="" id="qtd_transferir_' + idx + '" onkeyup="validaQtd(this, true)" onchange="validaQtd(this, true)" data-qtd-atual="' + qtdAtual + '" data-id="' + idx + '" name="arrProdutos[' + idx + '][qtd_transferir]" type="number" class="form-control" value="' + qtdTransferir + '" required="required">\n\
                <div class="input-group-addon">\n\
                    <a href="javascript:void(0);"  onclick="remover(' + idx + ')" title="Excluir Produto"><i class="fa fa-trash-o"></i></a>\n\
                </div>\n\
           </div>\n\
    <span id="erro_' + idx + '" class="help-block" hidden></span>\n\
            </td>\n\
        </tr>';

        return html;
    }

    $('#mdlTransferirEstoque').on('hidden.bs.modal', function(e) {
        validaTransferencia();
        $('#dvProdutos table tbody').html('');
    })

    $('#btnPesquisaInserirProdutoTrasnferencia').click(function() {
        buscaProdutoByCodONome();

    });

    var objLOJAS;
    getTotalTrasferenciasEstoqueRecebidasPendentes();
    corrigeEstoqueComErro();
    getLojas();
    //enviaRelatorioEstoque();
    getSolicitacaoEdicaoProduto();
    syncCardsTax();
    syncSellers();
    syncSales();
    syncSalesCanceled();
    syncProducts();
    ajusteEstoque();

    setInterval(function() {
        getTotalTrasferenciasEstoqueRecebidasPendentes();
        ajusteEstoque();
        getSolicitacaoEdicaoProduto();
        syncSales();
        syncSalesCanceled();
    }, <?= TEMPO_VERIFICACAO_TRANSFERENCIAS_PENDENTES ?> * 60 * 1000);

    function getTotalTrasferenciasEstoqueRecebidasPendentes() {

        if (navigator.onLine) {

            $.ajax({

                url: '<?= site_url('products/getTotalTrasferenciasEstoqueRecebidasPendentes') ?>',
                method: 'POST',
                async: true,

            }).done(function(json) {

                if (json != []) {

                    var obj = $.parseJSON(json);

                    if (typeof obj.dados[0] !== 'undefined') {

                        var totalPendentes = parseInt(obj.dados);

                        $('#qtdTransferenciaEstoqueRecebidasPendentes').html(totalPendentes);
                        $('#BqtdTransferenciaEstoqueRecebidasPendentes').html(totalPendentes);
                        if (totalPendentes > 0) {
                            $('#qtdTransferenciaEstoqueRecebidasPendentes').show();
                        } else {
                            $('#qtdTransferenciaEstoqueRecebidasPendentes').hide();
                        }


                    }
                }

            });

        }


    }

    function corrigeEstoqueComErro() {

        if (navigator.onLine) {

            $.ajax({

                url: '<?= site_url('products/corrigeEstoqueComErro') ?>',
                method: 'POST',
                async: true,

            });
        }
    }

    function enviaRelatorioEstoque() {

        if (navigator.onLine) {

            $.ajax({

                url: '<?= site_url('products/enviaRelatorioEstoque') ?>',
                method: 'POST',
                async: true,

            });
        }
    }

    function getSolicitacaoEdicaoProduto() {

        if (navigator.onLine) {

            $.ajax({

                url: '<?= site_url('products/getSolicitacaoEdicaoProduto') ?>',
                method: 'POST',
                async: true,

            });
        }
    }

    function getLojas() {


        $.ajax({

            url: '<?= site_url('products/getLojas') ?>',
            method: 'POST',
            async: true,

        }).done(function(json) {

            if (json != []) {

                var obj = $.parseJSON(json);

                objLOJAS = obj

                $.each(obj.dados, function(idx, arr) {

                    if (arr.cod != '<?= CODIGO_LOJA ?>') {
                        $('#cod_loja_destino').append($('<option>', {
                            value: arr.cod,
                            text: arr.cod + ' - ' + arr.tipo + ' ' + arr.nome
                        }));
                    }

                });



            }

        });

    }

    function getContingencias() {
        if (navigator.onLine) {

            $.ajax({

                url: '<?= site_url('products/getContingencias') ?>',
                method: 'POST',
                async: true,

            });
        }

    }
    
    function syncCardsTax() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('cards/syncTax') ?>',
                method: 'POST',
                async: true
            });
        }

    }
    
    function syncSellers() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('sellers/sync') ?>',
                method: 'POST',
                async: true
            });
        }

    }
    
    function syncSales() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('sales/sync') ?>',
                method: 'POST',
                async: true
            });
        }

    }

    function syncSalesCanceled() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('sales/syncCanceled') ?>',
                method: 'POST',
                async: true
            });
        }
    }
    
    function syncProducts() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('products/sync') ?>',
                method: 'POST',
                async: true
            });
        }

    }
    
    function ajusteEstoque() {
        if (navigator.onLine) {
            $.ajax({
                url: '<?= site_url('products/ajusteEstoque') ?>',
                method: 'POST',
                async: true
            });
        }

    }

    function dateTIMEToBR(date, segundos) {
        var arrDate = date.split(' ');

        var data = arrDate[0];
        var hora = arrDate[1];

        if (!segundos) {

            arrHora = hora.split(':');
            hora = arrHora[0] + ':' + arrHora[1];
        }

        return data.split('-').reverse().join('/') + ' ' + hora;
    }

    $("#pesquisaInserirProdutoTrasnferencia").keypress(function(event) {
        if (event.keyCode === 13) {
            $("#btnPesquisaInserirProdutoTrasnferencia").click();
        }
    });

    $(document).on("keypress", 'form', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });
</script>