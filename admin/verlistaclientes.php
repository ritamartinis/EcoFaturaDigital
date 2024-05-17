<?php
session_start();

// Verifica se o utilizador é admin ou não
if (!isset($_SESSION["utilizador"]) || $_SESSION["utilizador"] !== "admin") {
    // Se não for, volta ao index
    header("location: ../index.php");
    exit;
}

// BD
$maquina = "fdb1031.runhosting.com";
$userbd = "4433528_ritans";
$passbd = "RitaM#93";
$bd = "4433528_ritans";

// Estabelece a conexão
$ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
$ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa a exibição de erros

// Consultar todos os dados de todos os clientes !!!!excepto do admin!!!!
$consulta = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE id_cliente != 1");
$consulta->execute();
$clientes_todos = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Lista de Clientes</h1>
                    <hr class="my-4">
                </div>
                <!-- em cards -->
                <div class="d-flex flex-wrap">
                    <?php foreach ($clientes_todos as $cliente): ?>
                        <div class="card m-2" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $cliente['nome_cliente']; ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">Cliente #<?php echo $cliente['id_cliente']; ?>
                                </h6>
                                <p class="card-text">
                                    <strong>Username: </strong><?php echo $cliente['user']; ?><br>
                                    <strong>Morada: </strong><?php echo $cliente['endereco']; ?><br>
                                    <strong>Contato: </strong><?php echo $cliente['contacto']; ?>
                                </p>
                                <div class="separator"></div>
                                <div class="d-flex justify-content-center">
                                    <a href="editarclienteadmin.php?id=<?php echo $cliente['id_cliente']; ?>"
                                        class="btn btn-primary m-1">Editar</a>
                                    <a href="eliminarclienteadmin.php?id=<?php echo $cliente['id_cliente']; ?>"
                                        class="btn btn-danger m-1">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>