<?php
session_start();
// Se o utilizador já tiver feito o login, redireciona para a área admin ou cliente
if (isset($_SESSION["utilizador"])) {
    if ($_SESSION["utilizador"] === "admin") {
        header("location: area_admin.php");
        exit;
    } elseif ($_SESSION["utilizador"] === "cliente") {
        header("location: area_cliente.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifica se todos os campos foram preenchidos
    if (
        isset($_POST['nome']) &&
        isset($_POST['user']) &&
        isset($_POST['endereco']) &&
        isset($_POST['contacto']) &&
        isset($_POST['password']) &&
        isset($_POST['passwordrep'])
    ) {
        // Grava
        $nome = $_POST['nome'];
        $user = $_POST['user']; 
        $endereco = $_POST['endereco'];
        $contacto = $_POST['contacto'];
        $password = $_POST['password'];
        $passwordrep = $_POST['passwordrep'];

        // Verifica se as passes coincidem
        if ($password !== $passwordrep) {
            $erro = "As senhas não coincidem. Por favor, tente novamente.";
        } else {
            // BD
            $maquina = "fdb1031.runhosting.com";
            $userbd = "4433528_ritans";
            $passbd = "RitaM#93";
            $bd = "4433528_ritans";

            try {
                // Estabelecer a conexão
                $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
                $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativar a exibição de erros

                // Verifica se já existe um cliente com o mesmo nome 
                $verificacao = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE nome_cliente = '$nome'");
                $verificacao->execute();

                if ($verificacao->rowCount() > 0) {
                    $erro = "Nome já utilizado, por favor escolha outro.";
                } else {
                    //Insere o novo cliente na bd
                    $inserir_cliente = $ligacao->prepare("INSERT INTO gestaoFaturasUtilizadores (nome_cliente, endereco, contacto, user, pass, tipo) VALUES (:nome, :endereco, :contacto, :user, :password, 'cliente')");
                    $inserir_cliente->bindParam(':nome', $nome);
                    $inserir_cliente->bindParam(':endereco', $endereco);
                    $inserir_cliente->bindParam(':contacto', $contacto);
                    $inserir_cliente->bindParam(':user', $user);
                    $inserir_cliente->bindParam(':password', $password);
                    $inserir_cliente->execute();

                    // Manda o utilizador, se ele for bem-sucedido, para o index para, lá, fazer login
                    header("Location: index.php");
                    exit;
                }
            } catch (PDOException $e) {
                $erro = "Ocorreu um erro ao criar a nova conta: " . $e->getMessage();
            }
        }
    } else {
        // Se algum campo estiver em falta, mostra uma mensagem de erro
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body class="flex-body">
    <div class="login-container">
        <div class="login-logo">
            <img src="fotosdiversas/logo.png" alt="Logo da Empresa">
        </div>
        <div class="login-form">
            <h3 class="text-center">Criar Conta</h3>
            <form action="" method="post">
                <div class="input-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" class="form-control" required>
                </div>
                <div class="input-group">
                    <label for="user">Nome de Utilizador:</label>
                    <input type="text" id="user" name="user" class="form-control" required>
                </div>
                <div class="input-group">
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" class="form-control" required>
                </div>
                <div class="input-group">
                    <label for="contacto">Contacto:</label>
                    <input type="text" id="contacto" name="contacto" class="form-control" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="input-group">
                    <label for="passwordrep">Repetir a password:</label>
                    <input type="password" id="passwordrep" name="passwordrep" class="form-control" required>
                </div>
                <p>
                    <input type="submit" value="Criar Conta" class="login-button">
            </form>
            <p class="text-center">Já tem uma conta?→<a href="index.php">Iniciar Sessão</a></p>
        </div>
        <!-- Caso haja erros -->
        <?php if (isset($erro)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>