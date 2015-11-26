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

        comprobar_es_socio_admi();
        
        $con = conectar();
        
        $cols = array('id', 'numero', 'dni', 'nombre', 'direccion', 'codpostal', 'poblacion_id', 'telefono');
        $vals = array();
        
        for($i = 0; $i < count($cols); $i++):
            $vals[] = "";
        endfor;
        
        $variables = array_combine($cols, $vals);
        extract($variables);
        
        if(isset($_GET['id'])):
            $id = trim($_GET['id']);
            $res = pg_query_params("select * from socios where id = $1", array($id));
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
                comprobar_nombre($nombre, $error);
                comprobar_numero($numero, $error);
                comprobar_dni($dni, $error);
                comprobar_direccion($direccion, $error);
                comprobar_poblacion_id($poblacion_id, $error);
                comprobar_codpostal($codpostal, $error);
                comprobar_telefono($telefono, $error);
                comprobar_errores($error);
                
                $res = pg_query($con, "begin");
                bloquear_tabla_socios($con);
                comprobar_existe_socio($error, $numero, $id);
                
                $res = modificar_socio(compact($cols));
                
                comprobar_modificacion($res, $error);
                $res = pg_query($con, "commit"); ?>
                <h3>Se ha modificado correctamente el socio</h3><?php
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