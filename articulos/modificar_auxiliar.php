<?php

function comprobar_nombre_obligatorio($nombre, &$error)
{
    if ($nombre == "") {
        $error[] = "el nombre es obligatorio";
    }
}

function comprobar_precio_obligatorio($precio, &$error)
{
    if ($precio == "") {
        $error[] = "el precio es obligatorio";
    }
}

function comprobar_descripcion($descripcion, &$error) {
    if (strlen($descripcion) > 150) {
        $error[] = "la descripción no puede tener más de 150 caracteres";
    }
}

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

function comprobar_existe_articulo($codigo, $id, &$error)
{
    $res = pg_query_params("select id
                              from articulos
                             where codigo = $1 and
                                   id != $2", array($codigo, $id));
    if (pg_num_rows($res) > 0) {
        $res = pg_query("rollback");
        $error[] = "ya existe un articulo con ese código";
        throw new Exception();
    }
}

function formulario_modificar($variables)
{
    extract($variables); ?>

    <form action="modificar.php" method="post">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <label for="nombre">Nombre *:</label>
        <input type="text" name="nombre" value="<?= $nombre ?>" /><br/>
        <label for="precio">Precio *:</label>
        <input type="text" name="precio" value="<?= $precio ?>" /><br/>
        <label for="codigo">Código *:</label>
        <input type="text" name="codigo" value="<?= $codigo ?>" /><br/>
        <label for="existencias">Existencias: </label>
        <input type="text" name="existencias" value="<?= $existencias ?>" /><br/>
        <label for="descripcion">Descripción:</label><br/>
        <textarea name="descripcion"><?= $descripcion ?></textarea><br/>
        <input type="submit" value="Modificar" />
    </form><?php
}

