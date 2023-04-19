<?php

//CODIGO DA LOJA
$codigo = '190';

//TOKEN DA LOJA
$token = 'fe87917ff61a9acc93092b0883523a28';

//URL API BELAPLUS
$urlApi = 'https://pdv.belaplusoficial.com.br/api';

//TEMPO VERIFICAÇÃO DE TRANSFERENCIA PENDENTE (EM MINUTOS)
$minVerificacaoTransferenciaPendente = 5;

$local = __DIR__ . '/config-local.php';

if (file_exists($local)) {
    require $local;
}