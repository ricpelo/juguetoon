<?php

define('PATRON', '/^(-?\d*)(,\d{2})?(\s*€)?$/');

function comprobar_nombre($nombre, &$error)
{
    if (strlen($nombre) > 50) {
        $error[] = "el nombre no puede tener más de 50 caracteres";
    }
}

function comprobar_precio(&$precio, &$error)
{
    if ($precio != "") {
        $c = array();
        preg_match(PATRON, $precio, $c);
        if (empty($c)) {
            $error[] = "el precio no es correcto";
        } else {
            $precio = normalizar_precio($precio);
            $patron = '/^(-?\d*),(\d*)\s€$/';
            $c = array();
            preg_match($patron, $precio, $c);
            $precio = "${c[1]}.${c[2]}";
            $valor = (float) $precio;
            if ($valor < 0 || $valor >= 10000) {
                $error[] = "el precio debe estar entre 0 y 9999,99 €";
            } else {
                $precio;
            }
        }
    }
}

function comprobar_codigo($codigo, &$error)
{
    if ($codigo == "") {
        $error[] = "el código es obligatorio";
    } else {
        if (!ctype_digit($codigo)) {
            $error[] = "el código sólo puede tener dígitos";
        }
        if (strlen($codigo) > 13) {
            $error[] = "el código no puede tener más de 13 dígitos";
        }
    }
}

function comprobar_operacion($res, &$error) {
    if (pg_affected_rows($res) == 0) {
        $error[] = "no se ha podido modificar el artículo.";
        throw new Exception();
    }
}

function bloquear_tabla_articulos()
{
    $res = pg_query("lock table articulos in share mode");
}

function comparar_precios($precio_usuario, $precio_tabla)
{
    if ($precio_usuario == $precio_tabla) {
        return TRUE;
    }

    $patron = '/^(\d){1,2}(,\d{2})?(\s*€)?$/';

    $c = array();
    preg_match($patron, $precio_usuario, $c);

    if (empty($c)) {
        return FALSE;
    }

    if ($c[2] == "") {
        $precio_usuario = preg_replace($patron, '\1,00\3', $precio_usuario);
    }

    $precio_usuario = preg_replace($patron, '\1\2', $precio_usuario);
    $precio_tabla = preg_replace($patron, '\1\2', $precio_tabla);

    return $precio_usuario == $precio_tabla;
}

function normalizar_precio($precio)
{
    $c = array();
    preg_match(PATRON, $precio, $c);

    if (empty($c)) {
        return $precio;
    }

    if (!isset($c[2]) || $c[2] == "") {
        $precio = preg_replace(PATRON, '\1,00\3', $precio);
    }

    $precio = preg_replace(PATRON, '\1\2 €', $precio);

    return $precio;
}


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