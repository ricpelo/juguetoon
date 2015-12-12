<?php     

function existe_producto($codigo) {
    $res = pg_query_params("select *
                              from v_articulos
                             where codigo = $1", array($codigo,));
    return pg_num_rows($res) > 0;
}

function anadir_producto($producto, $cantidad) {
    if (!existe_producto($producto)) {
        header("Location: index.php");
        return;
    }
    if (isset($_SESSION['carrito'][$producto])) {
        $_SESSION['carrito'][$producto] += $cantidad;
    } else {
        $carrito = array (
            $producto => $cantidad
        );
        $_SESSION['carrito'][$producto] = $cantidad;
    }
}

function mostrar_carrito() {
    if (isset($_SESSION['carrito'])) {
        $carrito = $_SESSION['carrito'];
    } else {
        $carrito = array();
    } ?>
    <table border="1" style="margin: auto;">
        <caption><b>Carrito</b></caption><?php
        if (empty($carrito)) { ?>
            <tr>
                <th>No hay elementos</th>
            </tr><?php
        } else { ?>
            <tr>
                <th>Código</th>
                <th>Cantidad</th>
            </tr><?php
            foreach ($carrito as $k => $v): ?>
                <tr>
                    <td><?= $k ?></td>
                    <td><?= $v ?></td>
                </tr><?php
            endforeach;    
        } ?>
    </table><?php      
}      
