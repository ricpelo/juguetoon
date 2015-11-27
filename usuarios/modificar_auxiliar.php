<?php    
    function modificar_usuario($valores) {
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
        $res = pg_query_params("update usuarios
                               set $asignaciones
                               where id = \$$k", $pqp);
        return $res;
    }
    
    function comprobar_existe_usuario(&$error, $numero, $id) {
        $res = pg_query_params("select id from usuarios where numero = $1 and id != $2", array($numero, $id));
        
        if(pg_num_rows($res) > 0):
            $res = pg_query("rollback");
            $error[] = "ya existe un usuario con ese número.";
            throw new Exception();
        endif;
    }

    function comprobar_nick_modificar(&$error, $nick, $id) {
        $res = pg_query_params("select * from usuarios where nick = $1 and id != $2", array($nick, $id));

        if(pg_num_rows($res) > 0):
            $error[] = "nick cogido. Escoja otro.";
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
        $password = (isset($password)) ? $password : "";
        $admin = $admin == "t"; ?>
        
        <form action="modificar.php" method="post">
            <fieldset style="width: 3em;">
                <legend>Datos del Usuario</legend>
                <input type="hidden" name="id" value="<?= $id ?>" />
                <label for="numero">Número *</label> <br />
                <input type="text" name="numero" value="<?= $numero ?>" /> <br />
                <label for="nick">Nick *</label> <br />
                <input type="text" name="nick" value="<?= $nick ?>"/> <br />
                <label for="password">Password *</label> <br />
                <input type="text" name="password" value="<?= $password ?>"/> <br />
                
                <label for="admin">Admin</label>
                <select name="admin">
                <option value="<?= (int) $admin ?>"><?= ($admin) ? "Sí" : "No" ?></option>
                <option value="<?= (int) !$admin ?>"><?= (!$admin) ? "Sí" : "No" ?></option>
                </select>
                <br />
                <br />
                <input type="submit" value="Modificar" />
            </fieldset>
        </form><?php
    }
