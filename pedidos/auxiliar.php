<?php     


function index1($columnas, $vista) {
                
        extract(recoger_parametros($columnas));
        
        $params = compact('columnas', 'columna', 'criterio', 'orden', 'sentido');
        
        formulario_busqueda($params);
        
        $con = conectar();
        
        list($where, $pqp) = filtro($columnas, $columna, $criterio);
        
        $res = pg_query_params($con, "select * from $vista
                                      where $where
                                      order by $orden $sentido", $pqp);
 
        $params['res'] = $res;
        
        generar_resultado1($params); ?>
        <a href="insertar.php"><input type="button" value="Insertar" /></a><?php
    }

function generar_resultado1($params) {
        
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
                        $fila = pg_fetch_assoc($res, $i);
                    
                    ?>
                        
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
                            endforeach;?>
                            <td colspan="2">
                                <form action="index.php" method="get">
                                    <input type="hidden" name="codigo" value="<?= trim($fila['codigo']) ?>" />
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="cantidad" value="0" min="0" max="<?= $fila['existencias']?>"/> 
                                    <input type="submit" value="Añadir al carrito" />
                                </form>
                            </td>
                        </tr><?php
                    endfor; ?>
                </tbody>
            </table><?php
        else: ?>
            <h3>La búsqueda no ha dado ningún resultado.</h3><?php
        endif;
    }
    /***********************El carrito*************************************************/
?><hr><?php
 $carrito = $_SESSION['carrito'];
 
        if(sizeof($carrito) > 0): ?>
            <table border="1" style="margin: auto;">
            <th>El Carrito</th><?php

            foreach ($carrito as $k => $v):

                ?><tr>
                   <td>
                   <?=$carrito[$k]?></td>
                </tr>
              <?php
              
            endforeach; 
            endif;
            ?>
            </table>  
            }