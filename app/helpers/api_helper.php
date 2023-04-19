<?php

function consumirApi($endpoint, $post, $arrContingencia = false)
{
    if (!empty($_SESSION) && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        session_write_close();
    }

    set_time_limit(60);

    $CI = get_instance();
    $url = URL_API . '/' . $endpoint;

    $online = true;

    ini_set('default_socket_timeout', 5);

    $options = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
    ));

    $test = file_get_contents(str_replace('https:', 'http:', $url));

    if (!$test) {
        $online = false;
    }

    if ($online) {

        $fields_string = http_build_query($post);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $result = curl_exec($ch);
    } else {

        if ($arrContingencia !== false) {

            adicionarContingencia($url, json_encode($post));

            $result = json_encode(
                array(
                    'sucesso' => false,
                    'mensagem' =>  "Você está sem conexão com a internet no momento ou o servidor Bela Plus está fora do ar. A solicitação abaixo foi realizada porém será confirmada quando houver internet.<br>" . $arrContingencia['msgContingencia'],
                    'dados' => []
                )
            );
          
            return $result;
        } else {

            $result = json_encode(
                array(
                    'sucesso' => false,
                    'mensagem' => 'Sem internet no momento',
                    'dados' => []
                )
            );

            $CI->session->set_flashdata('warning', "Você está sem conexão com a internet no momento ou o servidor Bela Plus está fora do ar. Algumas funções não estarão disponíveis no momento. Por favor, tente novamente mais tarde");
            return $result;
        }
    }

    return $result;
}

function adicionarContingencia($url, $post)
{

    $dados = [

        'url' => $url,
        'post' => $post,
        'data' => date('Ymdhis')

    ];

    addContingencia($dados);
}

function addContingencia($dados)
{
    $CI = get_instance();
    $CI->load->model('contingencia_model');
    $CI->contingencia_model->add($dados);
}

function executeContingencias(){

    $CI = get_instance();
    $CI->load->model('contingencia_model');

    $arrContingencias = $CI->contingencia_model->getSimple(['*'], 'status = 1');    
   
    foreach($arrContingencias as $arr){

        $arrUrl = explode("/",$arr->url);
        $endpoint = $arrUrl[count($arrUrl)-1];

        $resposta = json_decode(consumirApi($endpoint, json_decode($arr->post,true)));
        
        if($resposta->sucesso){

            $CI->contingencia_model->del($arr->id);

        }

    }
   
}
