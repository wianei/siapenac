<?php
/**
 * Created by PhpStorm.
 * User: geova
 * Date: 23/10/2018
 * Time: 13:39
 */

namespace App\Classes;


class Util
{

    public static function formata($cpf) {
		
        
        $newCpf = "";
        
            // Formata o CPF ###.###.###-##
            $newCpf  = substr( $cpf, 0, 3 ) . '.';
            $newCpf .= substr( $cpf, 3, 3 ) . '.';
            $newCpf .= substr( $cpf, 6, 3 ) . '-';
            $newCpf .= substr( $cpf, 9, 2 ) . '';
            
        
            return $newCpf;
    }
}