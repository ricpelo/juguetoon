<?php session_start(); ?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>Insertar Socio</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';
        require 'insertar_auxiliar.php';
        require 'auxiliar.php';

        conectar();

        mostrar_dato_inicial();
        comprobar_usuario_admin();

        $cols = array('numero', 'nick', 'password', 'admin');
        $vals = array();
        
        for($i = 0; $i < count($cols); $i++):
            $vals[] = "";
        endfor;
        
        $variables = array_combine($cols, $vals);
        extract($variables);
        
        $existe = TRUE;
        foreach($cols as $col):
            $existe = $existe && isset($_POST[$col]);
        endforeach;
        
        if($existe):
            
            foreach($variables as $k => $v):
                $variables[$k] = trim($_POST[$k]);
            endforeach;
            
            extract($variables);
            
            $error = array();
            
            try {
                comprobar_numero($numero, $error);
                comprobar_nick($nick, $error);
                comprobar_password($password, $error);
                //comprobar_admin($admin, $error);
                comprobar_errores($error);
                
                $res = pg_query("begin");
                bloquear_tabla_usuarios();                
                comprobar_existe_usuario($error, $numero);
                
                $res = insertar_usuario(compact($cols));
                
                comprobar_insercion($con, $res, $error);
                $res = pg_query($con, "commit"); ?>
                <h3>Se ha insertado correctamente el usuario</h3><?php
                $exito = true;
            } catch(Exception $e) {
                foreach($error as $err): ?>
                    <h3>Error: <?= $err ?></h3><?php
                endforeach;
            }
        endif;
        
        
        if(!isset($exito)):
            formulario_insertar($variables);
        endif;
        
        volver(); ?>
    </body>
</html>
