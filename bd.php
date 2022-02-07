<?php

// sin codigo
function leer_config($nombre, $esquema){
    $config = new DOMDocument();
    $config->load($nombre);
    $res = $config->schemaValidate($esquema);
    if($res===FALSE){
        throw new InvalidArgumentException("Revise fichero de configuración.");
    }
    $datos = simplexml_load_file($nombre);
    $ip = $datos->xpath("//ip");
    $nombre = $datos->xpath("//nombre");
    $usu = $datos->xpath("//usuario");
    $clave = $datos->xpath("//clave");
    $cad = sprintf("mysql:dbname=%s;host=%s", $nombre[0], $ip[0]);
    $resul = [];
    $resul[] = $cad;
    $resul[] = $usu[0];
    $resul[] = $clave[0];
    return $resul;
};

// sin codigo
function comprobar_usuario($nombre, $clave){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $ins = "select codRes, correo from restaurantes where correo = '$nombre' and clave = '$clave'";
    $resul = $bd->query($ins);
    if($resul->rowCount() === 1){
        return $resul->fetch();
    } else {
        return FALSE;
    }
}

function cargar_categorias(){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $ins = "select codCat, nombre from categorias";
    $resul = $bd->query($ins);
    if(!$resul){
        return FALSE;
    }
    if($resul->rowCount() === 0){
        return FALSE;
    }
    // si hay 1 o más
    return $resul;
}

// sin codigo
function cargar_categoria($codCat){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $ins = "select codCat, nombre, descripcion from categorias where codcat = $codCat";
    $resul = $bd->query($ins);
    if(!$resul){
        return FALSE;
    }
    if($resul->rowCount() === 0){
        return FALSE;
    }
    // si hay 1 o más
    return $resul->fetch();
}

// sin codigo
function cargar_productos_categoria($codCat){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $sql = "select * from productos where categoria = $codCat";
    $resul = $bd->query($sql);
    if(!$resul){
        return FALSE;
    }
    if($resul->rowCount() === 0){
        return FALSE;
    }
    // si hay 1 o más
    return $resul;
}

// recibe un array de códigos de productos
// devuelve un cursor con los datos de esos productos
function cargar_productos($codigosProductos){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $texto_in = implode(",", $codigosProductos);
    $ins = "select * from productos where codProd in($texto_in)";
    $resul = $bd->query($ins);
    if(!$resul){
        return FALSE;
    }
    return $resul;
}

function insertar_pedido($carrito, $codRes){
    $res = leer_config(dirname(__FILE__)."/configuracion.xml", dirname(__FILE__)."/configuracion.xsd");
    $bd = new PDO($res[0], $res[1], $res[2]);
    $bd->beginTransaction();
    $hora = date("Y-m-d H:i:s", time());

    // insertar el pedido
    $sql = "insert into pedidos (fecha, enviado, restaurante) values ('$hora', '0', '$codRes')";
    $resul = $bd->query($sql);
    if(!$resul){
        return FALSE;
    }

    // coger el id del nuevo pedido para las filas detalle
    $pedido = $bd->lastInsertId();
    // insertar las filas en pedidoproductos
    foreach ($carrito as $codProd=>$unidades){
        $sql = "insert into pedidosproductos(Pedido, Producto, Unidades) values ($pedido, $codProd, $unidades)";
        echo $sql;
        $resul = $bd->query($sql);
        if(!$resul){
            $bd->rollBack;
            return FALSE;
        }
        $sql = "update productos set stock = stock - $unidades where codProd = $codProd";
        $resul = $bd->query($sql);
        if(!$resul){
            $bd->rollBack;
            return FALSE;
        }
    }
    $bd->commit();
    return $pedido;
}

?>
