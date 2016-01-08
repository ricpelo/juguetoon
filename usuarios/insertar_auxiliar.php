<?php
    function comprobar_existe_usuario(&$error, $numero) {
        $res = pg_query_params("select id from usuarios where numero = $1", array($numero));
        
        if(pg_num_rows($res) > 0):
            $res = pg_query("rollback");
            $error[] = "ya existe un usuario con ese número.";
            throw new Exception();
        endif;
    }

    function comprobar_insercion($res, &$error) {
        if(!$res && pg_affected_rows($res) != 1):
            
            $error[] = "no se ha podido llevar a cabo la operación.";
            $res = pg_query("rollback");
            throw new Exception();
        endif;
    }
    
    function insertar_usuario($valores) {
        unset($valores['repassword']);
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
        
        $res = pg_query_params("insert into usuarios($columnas)
                                      values ($comodines)", $valores);
        return $res;
    }
    
    function formulario_insertar($variables) {
        extract($variables); ?>
        
        <form action="insertar.php" method="post">
            <fieldset style="width: 3em;">
                <legend>Datos del Usuario</legend>
                <label for="numero">Número *</label> <br />
                <input type="text" name="numero" value="<?= $numero ?>" /> <br />
                <label for="nick">Nick *</label> <br />
                <input type="text" name="nick" value="<?= $nick ?>"/> <br />
                <label for="password">Password *</label> <br />
                <input type="password" name="password" value="<?= $password ?>"/> <br />
                <label for="repassword">Re Password *</label> <br />
                <input type="password" name="repassword" value="<?= $repassword ?>"/> <br />
                <br> <br>
                <label for="admin">Administrador</label>
                <select name="admin">
                    <option value="false">No</option>
                    <option value="true">Sí</option>
                </select>
                <br />
                <br />
                <input type="submit" value="Insertar" />
            </fieldset>
        </form><?php
    }
