<?php

require_once 'bd.php';

// formulario de login habitual
// si va bien abre sesión, guarda el usuario y redirige a principal.php
// si va mal da mensaje de error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usu = comprobar_usuario($_POST['usuario'], $_POST['clave']);
    if ($usu == FALSE) {
        $err = TRUE;
        $usuario = $_POST['usuario'];
    } else {
        session_start();
        $_SESSION['usuario'] = $usu;
        $_SESSION['carrito'] = [];
        header("Location:categorias.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Formulario de login</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
	<link href="../css/bootstrap-theme.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estiloslogin.css">
</head>

<body>
    <?php if (isset($_GET["redirigido"])) {
        echo "<p>Haga login para continuar <p>";
    } ?>
    <?php if (isset($err) and $err == TRUE) {
        echo "<p>Revise usuario y contraseña <p>";
    } ?>
    <div class="login">
    <div class="login-triangle"></div>
    <h2 class="login-header">Log in</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <label for="usuario">Usuario:</label><br>
            <input value="<?php if (isset($usuario)) echo $usuario; ?>" id="usuario" name="usuario" type="text"><br><br>
            <label for="clave">Clave:</label><br>
            <input id="clave" name="clave" type="password"><br><br>
            <input type="submit">
        </form>
    </div>
</body>

</html>