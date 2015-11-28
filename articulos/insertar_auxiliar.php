<?php

function formulario_insertar($variables)
{?>
    <form action="modificar.php" method="post">
        <input type="hidden" name="id"  />
        <label for="nombre">Nombre *:</label>
        <input type="text" name="nombre"  /><br/>
        <label for="precio">Precio *:</label>
        <input type="text" name="precio"  /><br/>
        <label for="codigo">Código *:</label>
        <input type="text" name="codigo"  /><br/>
        <label for="existencias">Existencias: </label>
        <input type="text" name="existencias"  /><br/>
        <label for="descripcion">Descripción:</label><br/>
        <textarea name="descripcion"></textarea><br/>
        <input type="submit" value="Agregar" /> 
        <input type="reset" value="Borrar" />
    </form><?php
}

