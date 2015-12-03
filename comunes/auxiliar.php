<?php
    
    define('APP_ROOT', '/juguetoon/');
    
    function mostrar_dato_inicial() {
        if (isset($_SESSION['usuario_id'])) {
            $id = $_SESSION['usuario_id'];
            $res = pg_query_params("select * from usuarios where id = $1", array($id));
            $usuario = pg_fetch_assoc($res, 0); ?>
            <img src="../imagenes/juguetoon.jpg" alt="Juguetoon" height="10%" width="10%" style="text-align: left;" />
            <div style="float: right;">
                <fieldset>
                    <p style="text-align: right;">Usuario: <?= $usuario['nick'] ?></p>
                    <p style="text-align: right;">Número: <?= $usuario['numero'] ?></p>
                    <p style="text-align: right;">
                        <a href="../comunes/logout.php">
                            <button>Salir</button>
                        </a>
                    </p>
                </fieldset>
            </div>
            <hr /><?php
        }
    }
    
    function comprobar_usuario_admin() {
        if(isset($_SESSION['usuario_id'])) {
            $res = pg_query_params("select admin from usuarios where id = $1", array($_SESSION['usuario_id']));
            $fila = pg_fetch_assoc($res, 0);
            $admin = $fila['admin'] == "t";
            
            if(!$admin) {
                header("Location: " . APP_ROOT . "no_admin.php");
            }
        }
        else {
            header("Location: " . APP_ROOT . "comunes/login.php");
        }
    }
    
    function comprobar_usuario_conectado() {
        if(!usuario_conectado()):
            header("Location: comunes/login.php");
            return;
        else:
            $usuario_id = $_SESSION['usuario_id'];
            $res = pg_query_params("select nick from usuarios where id = $1", array($socio_id));
            
            if(pg_num_rows($res) == 0):
                header("Location: ../socios/login.php");
                return;
            else:
                $fila = pg_fetch_assoc($res, 0);
                $nombre = $fila['nombre']; ?>
                <p align="right">Usuario: <?= $nombre ?>
                    <a href="../socios/logout.php">
                        <input type="button" value="Salir" />
                    </a>
                </p>
                <hr /><?php
            endif;
        endif;
    }

    function usuario_conectado() {
        return isset($_SESSION['socio_id']);    
    }

    function comprobar_errores($error) {
        if($error):
            throw new Exception();
        endif;
    }
    
    function alinear($v) {
        return isset($v['align']) ? "align=\"${v['align']}\"" : '';
    }

    function filtro($columnas, $columna, $criterio) {
        if($criterio != ""):
            $where = "$columna::text = $1";
            if(isset($columnas[$columna]['post'])):
                $func = $columnas[$columna]['post'];
                $criterio = call_user_func($func, $criterio);
            elseif(!isset($columnas[$columna]['exacto'])):
                $where = "formato($columna::text) like formato('%' || $1 || '%')";
            endif;
            $pqp = array($criterio);
        else:
            $where = "true";
            $pqp = array();
        endif;
        
        return array($where, $pqp);
    }

    function generar_resultado($params, $bol = false) {
        
        extract($params);
        
        if(pg_num_rows($res) > 0): ?>
            <table border="1" style="margin: auto;">
                <thead><?php
                    $href = "index.php?";
                                      
                    foreach($columnas as $k => $v):
                        if(isset($v['mostrar'])):
                            $sufijo = sentido($k, $orden, $sentido); ?>
                            <th>
                                <a href="<?= "${href}orden=$k&sentido=$sufijo" ?>">
                                    <input type="button" value="<?= $v['bonito'] ?>" <?= resaltar($k, $orden) ?> >
                                </a>
                                <?= ($k==$orden) ? icono_sentido($sentido) : ""; ?>
                            </th><?php
                        endif;
                    endforeach;?>
                    <th colspan="2" >Operaciones</th>
                </thead>
                <tbody><?php
                    
                    for($i = 0; $i < pg_num_rows($res); $i++):
                        $fila = pg_fetch_assoc($res, $i);?>
                        <tr><?php
                            foreach($columnas as $k => $v):
                                if(isset($v['mostrar'])):
                                    if(isset($v['formato'])) {
                                        $valor = $fila[$v['formato']];
                                    }
                                    else {
                                        $valor = $fila[$k];
                                    } ?>
                                    <td <?= alinear($v) ?>><?= $valor ?></td><?php
                                endif;
                            endforeach;
                            if ($bol){?>
                            <td colspan="2">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="codigo" value="<?= trim($fila['codigo']) ?>" />
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="cantidad" value="0" min="0" max="<?= $fila['existencias']?>"/> 
                                    <input type="submit" value="Añadir al carrito" />
                                </form>
                            </td>
                            
                            <?php } else {?>
                            <td>
                                <form action="modificar.php" method="get">
                                    <input type="hidden" name="id" value="<?= $fila['id'] ?>" />
                                    <input type="submit" value="Modificar" />
                                </form>
                            </td>
                            <td>
                                <form action="borrar.php" method="get">
                                    <input type="hidden" name="id" value="<?= $fila['id'] ?>" />
                                    <input type="submit" value="Borrar" />
                                </form>
                            </td>
                        </tr><?php
                            }
                    endfor; ?>
                </tbody>
            </table><?php
        else: ?>
            <h3>La búsqueda no ha dado ningún resultado.</h3><?php
        endif;
    }

    function selected($value, $col) {
        return ($value == $col) ? 'selected="on"' : '';
    }
    
    function resaltar($columna, $orden) {
        return $columna == $orden ? 'style="font-weight: bold;"': '';
    }
    
    function sentido($columna, $orden, $sentido) {
        if($columna == $orden):
            return $sentido == "asc" ? "desc" : "asc";
        else:
            return "asc";
        endif;
    }
    
    function icono_sentido($sentido) {
        return $sentido == "asc" ? "▲" : "▼";
    }
    
    function conectar() {
        return pg_connect("host=localhost dbname=datos user=usuario password=usuario");
    }
        
    function volver() { ?>
        <a href="index.php"><input type="button" value="Volver" /></a><?php
    }
    
    function formulario_busqueda($params) {
        
        extract($params); ?>
        
        <form action="index.php" method="get">
            <label for="criterio">Buscar => </label>
            <select name="columna"><?php
                foreach($columnas as $v):
                    if(isset($v['criterio'])): ?>
                        <option value="<?= $v['criterio'] ?>" 
                            <?= selected($v['criterio'], $columna) ?>>
                            <?= $v['bonito'] ?>
                        </option><?php
                    endif;
                endforeach;?>
            </select>
            <input type="text" name="criterio" value="<?= $criterio ?>" />
            <input type="hidden" name="orden" value="<?= $orden ?>" />
            <input type="hidden" name="sentido" value="<?= $sentido ?>" />
            <input type="submit" value="Buscar" />
        </form><?php
    }
    
    function recoger_parametros($columnas) {
        
        // Esta es la cadena a buscar
       // $criterio = isset($_GET['criterio']) ? trim($_GET['criterio']) : "";
        // Esta es la columna por la que deseas buscar, es decir, numero, nombre, dni, ...etc.
       // $columna = isset($_GET['columna']) ? trim($_GET['columna']) : ""; // array_keys($columnas)[0]
        
        if(isset($_GET['orden'])):
            $orden = trim($_GET['orden']);
            
            if(!isset($columnas[$orden])):
                header("Location: index.php");
                return array();
            endif;
            
            $_SESSION['orden'] = $orden;
        else:
            if(isset($_SESSION['orden'])):
                $orden = $_SESSION['orden'];
            else:
                foreach($columnas as $k => $v) break;
                $orden = $k; // array_keys($columnas)[0]
                $_SESSION['orden'] = $orden;
            endif;
            
            $_SESSION['orden'] = $orden;
        endif;
        
        if(isset($_GET['sentido'])):
            $sentido = trim($_GET['sentido']);
            
            if($sentido != "asc" && $sentido != "desc"):
                header("Location: index.php");
                return array();
            endif;
            $_SESSION['sentido'] = $sentido;
        else:
            if(isset($_SESSION['sentido'])):
                $sentido = $_SESSION['sentido'];
            else:
                $sentido = "asc";
            endif;
            $_SESSION['sentido'] = $sentido;
        endif;
        
        if(isset($_GET['columna'])):
            $columna = trim($_GET['columna']);
            $_SESSION['columna'] = $columna;
        else:
            if(isset($_SESSION['columna'])):
                $columna = $_SESSION['columna'];
            else:
                $columna = "";
                $_SESSION['columna'] = $columna; 
            endif;
        endif;
        
        if(isset($_GET['criterio'])):
            $criterio = trim($_GET['criterio']);
            $_SESSION['criterio'] = $criterio;
        else:
            if(isset($_SESSION['criterio'])):
                $criterio = $_SESSION['criterio'];
            else:
                $criterio = "";
                $_SESSION['criterio'] = $criterio;
            endif;
        endif;
        
        return compact('criterio', 'columna', 'orden', 'sentido');
    }
    
    function index($columnas, $vista, $bol = false) {
                
        extract(recoger_parametros($columnas));
        
        $params = compact('columnas', 'columna', 'criterio', 'orden', 'sentido');
        
        formulario_busqueda($params);
        
        $con = conectar();
        
        list($where, $pqp) = filtro($columnas, $columna, $criterio);
        
        $res = pg_query_params($con, "select * from $vista
                                      where $where
                                      order by $orden $sentido", $pqp);
 
        $params['res'] = $res;
        generar_resultado($params, $bol); ?>
        <a href="insertar.php"><input type="button" value="Insertar" /></a><?php
    }
