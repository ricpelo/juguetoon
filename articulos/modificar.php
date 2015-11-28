<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Modificar un socio</title>
        <meta charset="utf-8" />
    </head>
    <body><?php
        require 'auxiliar.php';
        require 'modificar_auxiliar.php';
        require '../comunes/auxiliar.php';
        // Comentado a la espera de compañeros. "FUNCIONAL"
        // comprobar_admin_conectado();
        conectar();

        $cols = array('id', 'codigo', 'nombre', 'descripcion', 'precio', 'precio_format', 'existencias');

        $vals = array();
        for ($i = 0; $i < count($cols); $i++) {
            $vals[] = "";
        }

        $variables = array_combine($cols, $vals);
        extract($variables);

        if (isset($_GET['id'])) {
            $id = trim($_GET['id']);
            $res = pg_query_params("select *
                                      from v_articulos
                                     where id = $1", array($id));
            $fila = pg_fetch_assoc($res, 0);
            extract($fila);
            $variables = $fila;
        }

        $existe = TRUE;
        foreach ($cols as $col) {
            $existe = $existe && isset($_POST[$col]);
        }

        if ($existe) {

            foreach ($variables as $k => $v) {
                $variables[$k] = trim($_POST[$k]);
            }

            extract($variables);

            $error = array();

            try {
                comprobar_nombre_obligatorio($nombre, $error);
                comprobar_nombre($nombre, $error);
                comprobar_precio_obligatorio($precio, $error);
                comprobar_precio($precio, $error);
                comprobar_codigo($codigo, $error);
                comprobar_descripcion($descripcion, $error);
                comprobar_errores($error);

                $res = pg_query("begin");
                bloquear_tabla_articulos();
                comprobar_existe_articulo($codigo, $id, $error);
                $valores = compact('id', 'codigo', 'nombre', 'descripcion', 'precio', 'existencias');
                $res = modificar_articulo($valores);
                comprobar_operacion($res, $error);
                $res = pg_query("commit"); ?>
                <h3>Se ha modificado la película correctamente</h3><?php
                $exito = TRUE;
            } catch (Exception $e) {
                foreach ($error as $err): ?>
                    <h3>Error: <?= $err ?></h3><?php
                endforeach;
            }
        }

        if (!isset($exito)) {
            formulario_modificar($variables);
        } ?>
        <a href="index.php"><input type="button" value="Volver" /></a>
    </body>
</html>
