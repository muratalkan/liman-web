<?php
namespace App\Helpers;


class InputValidations
{
    public static function isNameValid($str){
        if (!preg_match('/^[a-zA-Z0-9-.]+$/', $str)) {
            return false;
        }
        return true;
    }

}