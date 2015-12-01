<?php



function modificar_articulo($valores)
{
    $id = $valores['id'];
    unset($valores['id']);
    $pqp = array_values($valores);
    $pqp[] = $id;
    $asignaciones = array();
    $i = 1;
    foreach ($valores as $k => $v) {
        $asignaciones[] = "$k = \$$i";
        $i++;
    }
    $asignaciones = implode(",", $asignaciones);
    $res = pg_query_params("update articulos
                               set $asignaciones
                             where id = \$$i", $pqp);

    return $res;
}



function formulario_modificar($variables)
{
    extract($variables); ?>

    <form action="modificar.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <input type="hidden" name="precio_format" />
        <label for="codigo">Código *:</label>
        <input type="text" name="codigo" value="<?= $codigo ?>" /><br/>
        <label for="nombre">Nombre *:</label>
        <input type="text" name="nombre" value="<?= $nombre ?>" /><br/>
        <label for="precio">Precio *:</label>
        <input type="text" name="precio" value="<?= $precio_format ?>" /><br/>
        <label for="existencias">Existencias: </label>
        <input type="text" name="existencias" value="<?= $existencias ?>" /><br/>
        <label for="descripcion">Descripción:</label><br/>
        <textarea name="descripcion"><?= $descripcion ?></textarea><br/>
        <input type="submit" value="Modificar" />
    </form><?php
}

