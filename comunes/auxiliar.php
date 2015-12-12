<?php
    
    define('APP_ROOT', '/');
    define('FPP', 5);
    
    function mostrar_dato_inicial() { ?>
        <a href="/index.php">
            <img src="../imagenes/juguetoon.jpg" alt="Juguetoon" height="15%" width="15%" style="text-align: left;" />
        </a><?php       
        salir(); ?>
        <hr style="clear:both;" /><?php
    }

    function salir() { 
        if (isset($_SESSION['usuario_id'])) {
            $id = $_SESSION['usuario_id'];
            $res = pg_query_params("select * from usuarios where id = $1", array($id));
            $usuario = pg_fetch_assoc($res, 0); ?>
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
            </div><?php
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
            paginacion($href, $npag, $npags); ?>
            <br /><?php
        else: ?>
            <h3>La búsqueda no ha dado ningún resultado.</h3><?php
        endif;
    }

    function paginacion($href, $npag, $npags) { 
        if ($npags != 1) { ?>
            <div style="text-align: center;"><?php
                if ($npag-1 >= 1) {
                    $next = $npag - 1; ?>
                    <a href=<?= "${href}npag=$next" ?> >Anterior</a>&nbsp;&nbsp;<?php
                }
            
                for ($i = 1; $i <= $npags; $i++) {
                    if ($npag == $i) { ?>
                        <span><?= $i ?></span><?php
                    } else { ?>
                        <a href=<?= "${href}npag=$i" ?> ><?= $i ?></a><?php
                    }
                    
                    if ($i != $npags) { ?>
                        <span>,</span><?php
                    }
                }
            
                if ($npag+1 <= $npags) { 
                    $next = $npag + 1; ?>
                    &nbsp;&nbsp;<a href=<?= "${href}npag=$next" ?> >Siguiente</a><?php
                } ?>
            </div><?php
        }
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
        <a href="<?= APP_ROOT ?>"><input type="button" value="Volver" /></a><?php
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
    
    function recoger_parametros($columnas, $modulo, $npags) {
        
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
            
            $_SESSION[$modulo]['orden'] = $orden;
        else:
            if(isset($_SESSION[$modulo]['orden'])):
                $orden = $_SESSION[$modulo]['orden'];
            else:
                foreach($columnas as $k => $v) break;
                $orden = $k; // array_keys($columnas)[0]
                $_SESSION[$modulo]['orden'] = $orden;
            endif;
            
            $_SESSION['orden'] = $orden;
        endif;
        
        if(isset($_GET['sentido'])):
            $sentido = trim($_GET['sentido']);
            
            if($sentido != "asc" && $sentido != "desc"):
                header("Location: index.php");
                return array();
            endif;
            $_SESSION[$modulo]['sentido'] = $sentido;
        else:
            if(isset($_SESSION[$modulo]['sentido'])):
                $sentido = $_SESSION[$modulo]['sentido'];
            else:
                $sentido = "asc";
            endif;
            $_SESSION[$modulo]['sentido'] = $sentido;
        endif;
        
        if(isset($_GET['columna'])):
            $columna = trim($_GET['columna']);
            $_SESSION[$modulo]['columna'] = $columna;
        else:
            if(isset($_SESSION[$modulo]['columna'])):
                $columna = $_SESSION[$modulo]['columna'];
            else:
                $columna = "";
                $_SESSION[$modulo]['columna'] = $columna; 
            endif;
        endif;
        
        if(isset($_GET['criterio'])):
            $criterio = trim($_GET['criterio']);
            $_SESSION[$modulo]['criterio'] = $criterio;
        else:
            if(isset($_SESSION[$modulo]['criterio'])):
                $criterio = $_SESSION[$modulo]['criterio'];
            else:
                $criterio = "";
                $_SESSION[$modulo]['criterio'] = $criterio;
            endif;
        endif;
        
        if (isset($_GET['npag'])):
            $npag = trim($_GET['npag']);
            if($npag > $npags || $npag < 1):
                header("Location: index.php");
                return array();
            endif;
        
            $_SESSION[$modulo]['npag'] = $npag;
        else:
            if (isset($_SESSION[$modulo]['npag'])):
                $npag = $_SESSION[$modulo]['npag'];
            else:
                $npag = 1;
                $_SESSION[$modulo]['npag'] = $npag;
            endif;
        endif;

        return compact('criterio', 'columna', 'orden', 'sentido', 'npag');
    }
    
    function index($columnas, $vista, $modulo, $bol = false) {
        $res = pg_query("select * from $vista");
        $nfilas = pg_num_rows($res);
        $npags  = ceil($nfilas/FPP);
        
        extract(recoger_parametros($columnas, $modulo, $npags));
        
        $params = compact('columnas', 'columna', 'criterio', 'orden', 'sentido', 'npag');
        
        formulario_busqueda($params);
        
        list($where, $pqp) = filtro($columnas, $columna, $criterio);
        
        $res = pg_query_params("select * from $vista
                                 where $where
                              order by $orden $sentido
                              limit " . FPP . "
                              offset " . FPP . "*($npag-1)", $pqp);
        
        
        $params['npags']  = $npags;
        $params['res'] = $res;
        generar_resultado($params, $bol); 
        if (!$bol) { ?>
            <a href="insertar.php"><input type="button" value="Insertar" /></a><?php
        }
        volver();
    }
