<?php

    function comprobar_numero($numero, &$error) {
        if($numero == ""):
            $error[] = "el número es obligatorio.";
        else:
            if(!ctype_digit($numero)):
                $error[] = "el número solo puede contener dígitos.";
            endif;
            if(strlen($numero) > 13):
                $error[] = "el número debe contener como máximo 13 dígitos.";
            endif;
        endif;
    }
    
    function comprobar_nick($nick, &$error) {
        $res = pg_query_params("select * from usuarios where nick = $1", array($nick));
        
        if($nick == ""):
            $error[] = "el nombre es obligatorio.";
        elseif(pg_num_rows($res) > 0):
            $error[] = "el nombre de usuario ya esta cogido. Vuelva a intentarlo.";
        endif;
    }
    
    function comprobar_password($password, &$error) {
        if($password == ""):
            $error[] = "La constraseña no puede estar vacia.";
        endif;
    };
    
    function bloquear_tabla_usuarios($con) {            
        $res = pg_query($con, "lock table usuarios in share mode;");
    }
