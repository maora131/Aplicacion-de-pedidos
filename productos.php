<?php
    /*comprueba que el usuario haya abierto sesión o redirige*/
    require 'sesiones.php';
    require_once 'bd.php';
    comprobar_sesion();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Tabla de productos por categoría</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <link rel="stylesheet" href="css/estilosproducto.css">
</head>
<body>
    <?php
        require 'cabecera.php';
        $cat = cargar_categoria($_GET['categoria']);
        $productos = cargar_productos_categoria($_GET['categoria']);
        if($cat=== FALSE or $productos === FALSE){
            echo "<p class='error'>Error al conectar con la base datos</p>";
            exit;
        }
        echo "<h1>". $cat['nombre']. "</h1>";
        echo "<p>". $cat['descripcion']."</p>";
        echo "<table>"; //abrir la tabla
        echo "<tr><th>Nombre</th><th>Descripción</th>
        <th>Peso</th><th>Stock</th><th<Comprar</th></tr>";
        foreach($productos as $producto){
            $cod = $producto['CodProd'];
            $nom = $producto['Nombre'];
            $des = $producto['Descripcion'];
            $peso = $producto['Peso'];
            $stock = $producto['Stock'];
            echo "<tr><td>$nom</td><td>$des</td><td>$peso</td><td>$stock</td>
            <td>
            
            <form action = 'anadir.php' method = 'POST'>
            
            <input name = 'unidades' type='number' min = '1'value = '1'>
            
            <input type = 'submit' value='Comprar'>
            
            <input name = 'cod' type='hidden' value = '$cod'>
            </form>
            </td>
            </tr>";
        }
        echo "</table>"
        ?>

</body>
</html>