<?php session_start() ?>

<!DOCTYPE html>
<html>
	<head>
		<title>JUGUETOON</title>
		<meta charset="utf-8"/>
	</head>
    <body><?php
            require 'comunes/auxiliar.php';
            conectar();
            comprobar_usuario_admin(); 
            
            unset($_SESSION['orden']);
        
        ?>
		<div align="center">
			<img src="imagenes/juguetoon.jpg" style="margin:auto;">
		</div>
		<div align="center">
			<a href="usuarios" style="padding:30px"><button>Gestión de Usuarios</button></a>
			<a href="articulos" style="padding:30px"><button>Gestión de Artículos</button></a>
			<a href="pedidos" style="padding:30px"><button>Gestión de Pedidos</button></a>
		</div>
	</body>
</html>
