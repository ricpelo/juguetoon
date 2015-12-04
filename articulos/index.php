<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Juguetes</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        require '../comunes/auxiliar.php';
        conectar();
        comprobar_usuario_admin();
        mostrar_dato_inicial();
        
        $columnas = array(
            'codigo' => array(
                'bonito'   => 'Código',
                'criterio' => 'codigo',
                'exacto'   => TRUE,
                'mostrar'  => TRUE,
                'align'     => 'center'
            ),
            'nombre' => array(
                'bonito'   => 'Nombre',
                'criterio' => 'nombre',
                'mostrar'  => TRUE,
                'align'     => 'center'
            ),
            'precio_format' => array(
                'bonito'    => 'Precio',
                'criterio'  => 'precio_format',
                'exacto'    => TRUE,
                'mostrar'   => TRUE,
                'align'     => 'right'
            ),
            'existencias' => array(
                'bonito'   => 'Existencias',
                'criterio' => 'existencias',
                'exacto'   => TRUE,
                'mostrar'  => TRUE,
                'align'     => 'center'
            )
        );
        
        index($columnas, 'v_articulos', 'articulos'); ?>
    </body>
</html>
