<html>
    <head>
        <meta charset="utf-8" />
        <title>Juguetes</title>
    </head>
    <body><?php

        function conectar() {
            return pg_pconnect("host=localhost dbname=datos user=usuario
                        password=usuario");
        }

        $columnas = array(
            'codigo' => array(
                'bonito' => 'Código',
                'criterio' => 'codigo',
                'exacto' => TRUE,
                'align' => 'center'
            ),
            'nombre' => array(
                'bonito' => 'Nombre',
                'criterio' => 'nombre'
            ),
            'descripcion' => array(
                'bonito' => 'Descripcion'
            ),
            'precio' => array(
                'bonito' => 'Precio',
                'criterio' => 'precio',
                'exacto' => TRUE,
                'align' => 'center'
            )
        );
        ?>
        <p>
            <form action="index.php" method="get">
                <label for="criterio">Buscar:</label>
                <select name="columna"><?php
                    foreach ($columnas as $v):
                        if (isset($v['criterio'])):
                            ?>
                            <option value="<?= $v['criterio'] ?>" >
                                <?= $v['bonito'] ?>
                            </option><?php
                        endif;
                    endforeach;
                    
                    ?>
                </select>
                <input type="text" name="criterio" />
                <input type="submit" value="Buscar">
            </form>
        </p>
    <?php
    $con = conectar();
    ?>
        <p><table border="1" style="margin:auto">
            <thead><?php
        $href = "index.php?";
        foreach ($columnas as $k => $v):?>
                <th>
                    <?= $v['bonito']?>
                </th><?php endforeach; ?>
            <th colspan="2">Operaciones</th>
        </thead>
        </table></p>
</body>
</html>
