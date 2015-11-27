<html>
    <head>
        <meta charset="utf-8" />
        <title>Juguetes</title>
    </head>
    <body>
        <?php
        require 'auxiliar.php';
        require '../comunes/auxiliar.php';

        $columnas = array(
            'codigo' => array(
                'bonito'   => 'Código',
                'criterio' => 'codigo',
                'exacto'   => TRUE,
                'mostrar' => TRUE
            ),
            'nombre' => array(
                'bonito'   => 'Nombre',
                'criterio' => 'nombre',
                'mostrar' => TRUE
            ),
            'descripcion' => array(
                'bonito'   => 'Descripcion',
                'criterio' => 'descripcion',
                'mostrar' => TRUE
            ),
            'precio' => array(
                'bonito'   => 'Precio',
                'criterio' => 'precio',
                'exacto'   => TRUE,
                'mostrar' => TRUE
            ),
            'existencias' => array(
                'bonito'   => 'Existencias',
                'criterio' => 'existencias',
                'exacto'   => TRUE,
                'mostrar' => TRUE
            )
        );

        index($columnas, 'v_articulos'); ?>
    </body>
</html>
