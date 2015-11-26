<?php session_start(); ?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8" />
        <title>Confirmar Borrado</title>
    </head>
    <body><?php
        require '../comunes/auxiliar.php';
        
        comprobar_es_socio_admi();
        
        conectar();
        
        if(isset($_GET['id'])):
            $id = trim($_GET['id']);
            $res = pg_query_params("select * from usuarios where id = $1", array($id));
            
            if(pg_num_rows($res) > 0):
                extract(pg_fetch_assoc($res, 0)); ?>
                <h3>¿Está usted seguro de que quiere borrar el socio?</h3>
                <p>Numero => <?= $numero ?></p>
                <p>Nombre => <?= $nick ?></p>
                <form action="borrar.php" method="post">
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="submit" value="Sí" /><?php
                    volver(); ?>
                </form><?php
            else: ?>
                <h3>El usuario con el id <?= $id ?> no existe</h3><?php
                volver();
            endif;
        elseif(isset($_POST['id'])):
            $id = trim($_POST['id']);
            $res = pg_query_params("delete from usuarios where id = $1", array($id));
            
            if(pg_affected_rows($res) == 1): ?>
                <h3>Usuario borrado correctamente</h3><?php
            else: ?>
                <h3>No ha sido posible borrar al usuario</h3><?php
            endif; ?>
            <a href="index.php"><input type="button" value="Volver" /></a><?php
        else:
            header("Location: index.php");
            return;
        endif; ?>
    </body>
</html>
