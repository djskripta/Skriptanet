<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function ob_get_response($callback, $params = array())
{
    ob_start();
    call_user_func_array($callback, $params);
    $res = ob_get_contents();
    ob_end_clean();
    
    return $res;
}

function curl_get($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    
    return $output;
}

function curl_post($url, $params){
    $post_data = '';
    //create name value pairs seperated by &
    foreach($params as $k => $v) $post_data .= $k . '='.$v.'&'; 
    rtrim($post_data, '&');
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HEADER, false); 
    curl_setopt($ch, CURLOPT_POST, count($post_data));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
    
    return $output;
}