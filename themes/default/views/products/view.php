<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?= $product->name; ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-4">
                    <img id="pr-image" src="<?= base_url() ?>uploads/<?= $product->image ?>"
                    alt="<?= $product->name ?>" class="img-responsive img-thumbnail"/>
                </div>
                <div class="col-xs-8">
                    <div class="table-responsive">
                        <table class="table table-borderless table-striped dfTable table-right-left">
                            <tbody>
                                <tr>
                                    <td class="col-xs-5"><?= lang("product_type"); ?></td>
                                    <td class="col-xs-7"><?= lang($product->type); ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("product_name"); ?></td>
                                    <td><?= $product->name; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("product_code"); ?></td>
                                    <td><?= $product->code; ?></td>
                                </tr>
                                <tr>
                                    <td><?= lang("category"); ?></td>
                                    <td><?= $category->name.' ('.$category->code.')'; ?></td>
                                </tr>
                                <tr>
                                    <td>Tecido</td>
                                    <td><?= $product->material; ?></td>
                                </tr>
                                <tr>
                                    <td>Modelo</td>
                                    <td><?= $product->model; ?></td>
                                </tr>
                                <tr>
                                    <td>Estação</td>
                                    <td><?= $product->season; ?></td>
                                </tr>
       
                                <tr>
                                    <td><?= lang("price"); ?></td>
                                    <td><?= $this->tec->formatMoney($product->price) ?></td>
                                </tr>
                                <?php if ($product->type == 'standard') { ?>
                                <tr>
                                    <td><?= lang("quantity"); ?></td>
                                    <td><?= $product->quantity ?></td>
                                </tr>
                                <?php } ?>
                                <tr>
                    <td colspan="2" style="padding-left: 0">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding-left: 0">Tamanho</th>
                                <th style="text-align: left">Cor</th>
                                <th class="text-center">Qtd</th>
                            </tr>
                        </thead>
                        <tbody>
                           <?php foreach ($variants as $row) {
                                echo '<tr>'
                                        . '<td style="text-align: left">'.$row->size.'</td>'
                                        . '<td style="text-align: left">'.$row->color.'</td>'
                                        . '<td class="text-center">'.$row->quantity.'</td>'
                                . '</tr>';
                           }?>
                        </tbody>
                    </table>
                </td>
            </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($product->type == 'combo') { ?>
                    <h4 class="bold"><?= lang('combo_items') ?></h4>
                    <div class="table-responsive">
                        <table
                        class="table table-bordered table-striped table-condensed dfTable two-columns">
                        <thead>
                            <tr>
                                <th><?= lang('product_name') ?></th>
                                <th><?= lang('quantity') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($combo_items as $combo_item) {
                                echo '<tr><td>' . $combo_item->name . ' (' . $combo_item->code . ') </td><td>' . $this->tec->formatNumber($combo_item->qty) . '</td></tr>';
                            } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </div>

    </div>
</div>
</div>