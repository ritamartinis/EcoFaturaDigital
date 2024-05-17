<?php
session_start();

// BD
$maquina = "fdb1031.runhosting.com";
$userbd = "4433528_ritans";
$passbd = "RitaM#93";
$bd = "4433528_ritans";

// Verifica o preenchimento
if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        // Estabelecer a conexão
        $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
        $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //Ativa a exibição de erros

        $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE user='$username' AND pass='$password'");
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            // Verifica o tipo de utilizador
            $utilizador = $consulta->fetch(PDO::FETCH_ASSOC);
            $_SESSION["utilizador"] = $utilizador["tipo"];

            //Se for administrador, vai para a página: area_admin
            if ($_SESSION["utilizador"] === "admin") {
                header("location: admin/area_admin.php");
                exit;
            }

            // Se for cliente, redireciona para a página: area_cliente
            if ($_SESSION["utilizador"] === "cliente") {
                // Verifica se o ID do cliente está definido
                if (isset($utilizador["id_cliente"])) {
                    // Obtem o ID do cliente - para mostrar as faturas DESSE cliente
                    $id_cliente = $utilizador["id_cliente"];
                    // Armazena o ID do cliente na sessão
                    $_SESSION["id_cliente"] = $id_cliente;
                    // Redirecionar para a página do cliente
                    header("location: cliente/area_cliente.php");
                    exit;
                } else {
                    $erro = "Erro no ID do Cliente.";
                }
            }
        }
        // Não é um cliente válido
        else {
            $erro = "Dados de acesso incorretos. Por favor, tente novamente.";
        }
    } catch (PDOException $e) {
        $erro = "Ocorreu um erro no login: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticação</title>
    <link rel='stylesheet' href='estilos.css'>
</head>

<body class="flex-body">
    <div class="login-container">
        <div class="login-logo">
            <img src="fotosdiversas/logo.png" alt="Logo da Empresa">
        </div>
        <div class="login-form">
            <h3 class="text-center">Autenticação do Utilizador</h3>
            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                </div>
                <p></p>
                <div class="input-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                </div>
                <p></p>
                <input type="submit" name="login" id="login" value="LOGIN" class="login-button">
            </form>
            <p class="text-center"><a href="criar_conta.php">Criar uma conta →</a></p>
        </div>
        <!-- Caso haja erros -->
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>