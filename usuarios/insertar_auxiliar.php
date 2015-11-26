<?php
    function comprobar_existe_socio(&$error, $numero) {
        $res = pg_query_params("select id from socios where numero = $1", array($numero));
        
        if(pg_num_rows($res) > 0):
            $res = pg_query("rollback");
            $error[] = "ya existe un socio con ese número.";
            throw new Exception();
        endif;
    }

    function comprobar_insercion($con, $res, &$error) {
        if(!$res && pg_affected_rows($res) != 1):
            
            $error[] = "no se ha podido llevar a cabo la operación.";
            $res = pg_query($con, "rollback");
            throw new Exception();
        endif;
    }
    
    function insertar_socio($con, $valores) {
        
        // El metodo array_keys devuelve de un array asociativo un array con solo las claves
        $columnas = implode(",", array_keys($valores));

        // El metodo array_values devuelve de un array asociativo un array indexado por numero
        // con solo los valores
        $valores = array_values($valores);
        
        $comodines = array();
        for($i = 1; $i <= count($valores); $i++):
            $comodines[] = "\$$i";
        endfor;
        $comodines = implode(",", array_values($comodines));
        
        $res = pg_query_params($con, "insert into socios($columnas)
                                      values ($comodines)", $valores);
        return $res;
    }
    
    function formulario_insertar($variables) {
        extract($variables);
        
        $res = pg_query("select id, nombre from poblaciones order by nombre");
        ?>
        
        <form action="insertar.php" method="post">
            <fieldset style="width: 3em;">
                <legend>Datos del Socio</legend>
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
                <input type="submit" value="Insertar" />
            </fieldset>
        </form><?php
    }
