<?php session_start() ?>
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

        

        if (isset($_GET['codigo']) && isset($_GET['cantidad'])) {
            $producto = trim($_GET['codigo']);
            $cantidad = trim($_GET['cantidad']);
            anadir_producto($producto, $cantidad);
        }
        
        mostrar_dato_inicial();
        mostrar_carrito();
        $columnas = array(
            'codigo' => array(
                'bonito'   => 'Código',
                'criterio' => 'codigo',
                'exacto'   => TRUE,
                'mostrar'  => TRUE
            ),
            'nombre' => array(
                'bonito'   => 'Nombre',
                'criterio' => 'nombre',
                'mostrar'  => TRUE
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
                'mostrar'  => TRUE
            )
        );

        index($columnas, 'v_articulos', 'pedidos', true); ?>

    </body>
</html>
