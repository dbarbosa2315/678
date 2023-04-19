<?php
define('BLING_API_KEY', '4e714f48dbc09c61030619e4e138e818e975ae4a7beb1f4adbadf47878e610b4992e1016');

define('BLING_API_URL', 'https://bling.com.br/Api/v2');

function bling_get($url)
{
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($curl);

    curl_close($curl);

    return json_decode($json, true);
}

function bling_post($url, $data)
{
    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_POST, count($data));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($curl);

    curl_close($curl);

    return json_decode($json, true);
}

function bling_produto($codigo)
{
    $url = BLING_API_URL . "/produto/$codigo/json&estoque=S&apikey=" . BLING_API_KEY;

    return bling_get($url);
}

function bling_add_produto($code, $name, $price, $variants)
{
    $xml_vars = '';

    foreach ($variants as $variant) {
        $xml_vars .= '
            <variacao>
                <codigo>' . $variant->sku . '</codigo>
                <nome>COR:' . $variant->color . ';TAMANHO:' . $variant->size . '</nome>
                <vlr_unit>' . $price . '</vlr_unit>
                <estoque>' . $variant->quantity . '</estoque>
            </variacao>';
    }

    $xml = '
        <?xml version="1.0" encoding="UTF-8"?>
        <produto>
           <codigo>' . $code . '</codigo>
           <descricao>' . $name . '</descricao>
           <situacao>Ativo</situacao>
           <un>Pc</un>
           <variacoes>
            ' . $xml_vars . '
           </variacoes>
        </produto>';

    $post = [
        'apikey' => BLING_API_KEY,
        'xml' => rawurlencode($xml)
    ];

    $url = BLING_API_URL . "/produto/json";

    return bling_post($url, $post);
}

function bling_update_variacao($variant, $price)
{
    $xml = '
        <?xml version="1.0" encoding="UTF-8"?>
        <produto>
           <codigo>' . $variant->sku . '</codigo>
           <nome>COR:' . $variant->color . ';TAMANHO:' . $variant->size . '</nome>
           <situacao>Ativo</situacao>
           <un>Pc</un>
           <vlr_unit>' . $price . '</vlr_unit>
           <estoque>' . $variant->quantity . '</estoque>
           <estrutura>
            <lancarEstoque>P</lancarEstoque>
           </estrutura>
        </produto>';

    $post = [
        'apikey' => BLING_API_KEY,
        'xml' => rawurlencode($xml)
    ];

    $url = BLING_API_URL . "/produto/$variant->sku/json";

    return bling_post($url, $post);
}

function bling_pedidos($dataIni, $dataFim)
{
    $url = BLING_API_URL . '/pedidos/json&apikey=' . BLING_API_KEY . "&filters=dataEmissao[$dataIni TO $dataFim]";

    return bling_get($url);
}

function bling_confirma_pedido($numero)
{
    $url = $url = BLING_API_URL . "/pedido/$numero/json";

    $xml = '
        <?xml version="1.0" encoding="UTF-8"?>
        <pedido>
            <idSituacao>9</idSituacao>
        </pedido>';

    $post = [
        'apikey' => BLING_API_KEY,
        'xml' => rawurlencode($xml)
    ];

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_POST, count($post));
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $json = curl_exec($curl);

    curl_close($curl);

    return json_decode($json, true);
}
