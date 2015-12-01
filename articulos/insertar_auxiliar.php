<?php

function formulario_insertar($variables)
{   extract($variables); ?>
<form action="insertar.php" method="post">
        <label for="codigo">Código *:</label>
        <input type="text" name="codigo" value="<?= $codigo ?>" /><br/>
        <label for="nombre">Nombre *:</label>
        <input type="text" name="nombre" value="<?= $nombre ?>" /><br/>
        <label for="precio">Precio *:</label>
        <input type="text" name="precio" value="<?= $precio ?>" /><br/>
        <label for="existencias">Existencias: </label>
        <input type="text" name="existencias" value="<?= $existencias ?>" /><br/>
        <label for="descripcion">Descripción:</label><br/>
        <textarea name="descripcion"><?= $descripcion ?></textarea><br/>
        <input type="submit" value="Insertar" />
    </form><?php
}

function insertar($valores)
{
    $columnas = implode(",", array_keys($valores));
    $valores = array_values($valores);
    $comodines = array();
    for ($i = 1; $i <= count($valores); $i++) {
        $comodines[] = "\$$i";
    }
    $comodines = implode(",", $comodines);
    $res = pg_query_params("insert into articulos ($columnas)
                            values ($comodines)", $valores);
    return $res;
}