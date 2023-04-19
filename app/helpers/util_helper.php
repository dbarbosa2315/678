<?php
function getTipoLD($cod){

    $CI = get_instance();
    return (isset($CI->session->lojasSessao[$cod]->tipo)) ? $CI->session->lojasSessao[$cod]->tipo : '';

}