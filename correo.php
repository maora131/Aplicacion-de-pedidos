<?php

use PHPMailer\PHPMailer\PHPMailer;
require dirname(__FILE__). "../vendor/autoload.php";

// crear correo
function crear_correo($carrito, $pedido, $correo){
    $texto = "<h1>Pedido nº $pedido</h1><h2>Restaurante: $correo </h2>";
    $texto.= "Detalle del pedido:";
    $productos = cargar_productos(array_keys($carrito));

    $texto.= "<table>"; //abrir la tabla
    $texto.= "<tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Peso</th>
                <th>Unidades</th>
                <th>Eliminar</th>
            </tr>";
    foreach ($productos as $producto){
        $cod = $producto['CodProd'];
        $nom = $producto['Nombre'];
        $des = $producto['Descripcion'];
        $peso = $producto['Peso'];
        $unidades = $_SESSION['carrito'][$cod];
        $texto.= "<tr>
                    <td>$nom</td>
                    <td>$des</td>
                    <td>$peso</td>
                    <td>$unidades</td>
                </tr>";
    }
    $texto.= "</table>";
    return $texto;
}

// sin codigo
function enviar_correo_multiples($lista_correos, $cuerpo, $asunto=""){
    $mail = new PHPMailer();
    $mail->IsSMTP();
    // pa no ver mensajes de error se pone a 0
    $mail->SMPTDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Host = "smtp-gmail.com";
    $mail->Port = 587;
    // introducir usuario de google
    $mail->Username = "";
    // introducir clave
    $mail->Password = "";
    $mail->setFrom('user@gmail.com');
    // asunto
    $mail->Subject = $asunto; // ("Correo de prueba")
    // cuerpo
    $mail->msgHTML($cuerpo);

    $correos = explode(",", $lista_correos);
    foreach ($correos as $correo){
        $mail->addAddress($correo, $correo);
    }
    if(!$mail->Send()){
        return $mail->ErrorInfo;
    } else {
        return TRUE;
    }

    // adjuntos
    // $mail->addAttachment("empleado.xsd");
    // destinatario
    // $address = "destino@servidor.com";
    // $mail->addAddress($address, "Test");
    // enviar
    // $resul = $mail->Send();
    // if($resul) {
    //     echo "Error". $mail->ErrorInfo;
    // } else {
    //     echo "Enviado";
    // }
}

// sin codigo
function enviar_correos($carrito, $pedido, $correo){
    $cuerpo = crear_correo($carrito, $pedido, $correo);
    return enviar_correo_multiples("$correo pedidos@empresa.com", $cuerpo, "Pedido $pedido confirmado");
}

?>