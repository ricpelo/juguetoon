<?php    
    function modificar_socio($valores) {
        $id = $valores['id'];
        unset($valores['id']);
        $pqp = array_values($valores);
        $pqp[] = $id;
        
        $asignaciones = array();
                
        foreach(array_keys($valores) as $k => $v):
            $asignaciones[] = "$v = \$" . ($k + 1);
        endforeach;
        $k += 2;
        
        $asignaciones = implode(",", $asignaciones);
        
        $res = pg_query_params("update socios
                               set $asignaciones
                               where id = \$$k", $pqp);
        return $res;
    }
    
    function comprobar_existe_socio(&$error, $numero, $id) {
        $res = pg_query_params("select id from socios where numero = $1 and id != $2", array($numero, $id));
        
        if(pg_num_rows($res) > 0):
            $res = pg_query("rollback");
            $error[] = "ya existe un socio con ese número.";
            throw new Exception();
        endif;
    }
    
    function comprobar_modificacion($res, &$error) {
        if(!$res && pg_affected_rows($res) != 1):
            
            $error[] = "no se ha podido llevar a cabo la operación.";
            $res = pg_query("rollback");
            throw new Exception();
        endif;
    }
    
    function formulario_modificar($variables) {
        extract($variables);
        
        // Al pg_query() no le hace falta pasarle la conexion, ya que, el mismo coje la ultima abierta por defecto
        $res = pg_query("select id, nombre from poblaciones order by nombre");
        ?>
        
        <form action="modificar.php" method="post">
            <fieldset style="width: 3em;">
                <legend>Datos del Socio</legend>
                <input type="hidden" name="id" value="<?= $id ?>" />
                <label for="numero">Número *</label> <br />
                <input type="text" name="numero" value="<?= $numero ?>" /> <br />
                <label for="dni">DNI</label> <br />
                <input type="text" name="dni" value="<?= $dni ?>"/> <br />
                <label for="nombre">Nombre *</label> <br />
                <input type="text" name="nombre" value="<?= $nombre ?>"/> <br />
                <label for="direccion">Dirección</label> <br />
                <input type="text" name="direccion" value="<?= $direccion ?>" /> <br />
                <label for="codpostal">Código Postal </label> <br />
                <input type="text" name="codpostal" value="<?= $codpostal ?>"/> <br /> <br />
                
                <label for="poblacion_id">Población *</label>
                <select name="poblacion_id"><?php
                    for($i = 0; $i < pg_num_rows($res); $i++):
                        extract(pg_fetch_assoc($res, $i));?>
                            <option value="<?= $id ?>" <?= selected($id, $poblacion_id) ?>><?= $nombre ?></option><?php
                    endfor;?>
                </select>
                <label for="Telefono">Telefono</label><br />
                <input type="text" name="telefono" value="<?= $telefono ?>" /><br />
                <br />
                <br />
                <input type="submit" value="Modificar" />
            </fieldset>
        </form><?php
    }