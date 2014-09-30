<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class core_exception extends Exception
{
    public function __construct($message, $type = E_NOTICE)
    {
        parent::__construct($message, $type);
        exit;
    }
}