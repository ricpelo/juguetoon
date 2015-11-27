<?php session_start() ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Borrar</title>
        <meta charset="utf-8" />
    </head>
    <body><?php
        require 'auxiliar.php';
        require '../comunes/auxiliar.php';
        // Comentado a la espera de compañeros. "Funcional"
        // comprobar_admin_conectado();
        
        //if (isset($_GET['id'])):
            //$id = trim($_GET['id']);
            // id fijo para que sea "funcional". Borrar cuando se añada index
            $id = 1;
            conectar();
            $res = pg_query_params("select codigo, nombre
                                      from v_articulos
                                     where id = $1", array($id));
            if (pg_num_rows($res) != 1): ?>
                <h3>El artículo indicado no existe</h3><?php
                volver();
            else:
                $fila = pg_fetch_assoc($res, 0);
                extract($fila); ?>
                <h4><?= $codigo ?> : <?= $nombre ?></h4>
                <h3>¿Está seguro de querer borrar el artículo?</h3>
                <form action="borrar.php" method="post">
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="submit" value="Sí" />
                    <a href="index.php"><input type="button" value="No" /></a>
                </form><?php
            endif;
        // elseif cambiado para que sea "funcional"
        if (isset($_POST['id'])):
            $id = trim($_POST['id']);
            conectar();
            $res = pg_query_params("update articulos
                                       set borrado = true
                                     where id = $1", array($id));
            if (pg_affected_rows($res) == 1): ?>
                <h3>Película borrada correctamente</h3><?php
            else: ?>
                <h3>No ha sido posible borrar la película</h3><?php
            endif;
            volver();
        //else:
            // header("Location: index.php");
        endif; ?>
    </body>
</html>
