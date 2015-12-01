<?php session_start(); ?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>VideoClub</title>
    </head>
    <body><?php 
        require 'auxiliar.php';
        require 'modificar_auxiliar.php';
        require '../comunes/auxiliar.php';

        mostrar_dato_inicial();
        comprobar_usuario_admin();
        
        $con = conectar();
        
        $cols = array('id', 'numero', 'nick', 'password', 'admin');
        $vals = array();
        
        for($i = 0; $i < count($cols); $i++):
            $vals[] = "";
        endfor;
        
        $variables = array_combine($cols, $vals);
        extract($variables);
        
        if(isset($_GET['id'])):
            $id = trim($_GET['id']);
            $res = pg_query_params("select id, numero, nick, admin from usuarios where id = $1", array($id));
            $fila = pg_fetch_assoc($res, 0);
            extract($fila);
            
            $variables = $fila;
        endif;
        
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
                comprobar_nick_modificar($error, $nick, $id);
                comprobar_password($password, $error);
                comprobar_errores($error);
                
                $res = pg_query($con, "begin");
                bloquear_tabla_usuarios($con);
                comprobar_existe_usuario($error, $numero, $id);
                $password = md5($password);
                $res = modificar_usuario(compact($cols));
                
                comprobar_modificacion($res, $error);
                $res = pg_query($con, "commit"); ?>
                <h3>Se ha modificado correctamente el usuario</h3><?php
                $exito = true;
            } catch(Exception $e) {
                foreach($error as $err): ?>
                    <h3>Error: <?= $err ?></h3><?php
                endforeach;
            }
        endif;
        
        
        if(!isset($exito)):
            formulario_modificar($variables);
        endif;
        volver(); ?>
    </body>
</html>
