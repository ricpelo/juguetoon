<?php session_start() ?>

<!DOCTYPE html>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Gestion de Usuarios</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';
        mostrar_dato_inicial();
        comprobar_es_socio_admin();
        
        $columnas = array(
            'numero'           => array(
                                        'bonito' => 'Número',
                                        'criterio' => 'numero',
                                        'exacto' => TRUE,
                                        'mostrar' => TRUE
            ),
            'nick'           => array(
                                        'bonito' => 'Nick',
                                        'criterio' => 'nick',
                                        'mostrar' => TRUE
            )
        );
        index($columnas, 'usuarios');  ?> 
    </body>
</html>
