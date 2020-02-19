<?php

namespace App\Models;
class ResponseModel
{
    public $message;
    public $data;
    public $code;
    public $errors;

    public function __construct($message,$data,$code,$errors) {
        $this->message = $message;
        $this->data = $data;
        $this->code = $code;
        $this->errors = $errors;
    }
    
}