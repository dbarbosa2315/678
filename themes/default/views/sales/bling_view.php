<div class="modal-dialog modal-md">
    <div class="modal-content" style="border-radius: 3px;">
        <div class="modal-header" style="background-color: #00a65a; color: #fff; padding: 5px 10px;">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="margin-top: 5px;">
                <i class="fa fa-times"></i>
            </button>
            <h3 class="modal-title" id="myModalLabel"><?= $cliente->nome; ?></h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary box-solid">
                        <div class="box-header">
                            <h3 class="box-title">Endereço</h3>
                        </div>
                        <div class="box-body">
                            <?= $cliente->endereco; ?>, <?= $cliente->numero; ?> <?= $cliente->complemento; ?>
                            <br>
                            <?= $cliente->bairro; ?>
                            <br>
                            <?= $cliente->cidade; ?> - <?= $cliente->uf; ?>
                            <br>
                            <?= $cliente->cep; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary box-solid">
                        <div class="box-header">
                            <h3 class="box-title">Contato</h3>
                        </div>
                        <div class="box-body">
                            Email: <?= $cliente->email; ?>
                            <br>
                            Telefone: <?= preg_replace('/^55/', '', $cliente->celular); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary box-solid">
                        <div class="box-header">
                            <h3 class="box-title">Itens</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-borderless table-striped dfTable table-right-left">
                                <thead>
                                    <tr>
                                        <th style="text-align: left">Código</th>
                                        <th style="text-align: center">Cor</th>
                                        <th style="text-align: center">Tamanho</th>
                                        <th style="text-align: center">Quantidade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itens as $item): ?>
                                        <?php list($code, $color, $size) = explode("-", $item->codigo); ?>
                                        <tr>
                                            <td style="text-align: left"><?= $code; ?></td>
                                            <td style="text-align: center"><?= $color; ?></td>
                                            <td style="text-align: center"><?= $size; ?></td>
                                            <td style="text-align: center"><?= intval($item->quantidade); ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>