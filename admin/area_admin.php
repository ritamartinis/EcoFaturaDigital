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



try {
    // Ligação à BD   
    $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $consulta = $ligacao->prepare("SELECT * FROM `gestaoFaturasUtilizadores` WHERE id_cliente = 1");
    $consulta->execute();
    $dados = $consulta->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Administrador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Área de Administrador</h1>
                    <hr class="my-4">
                </div>
                <!-- Gráfico com os Serviços que nós dispomos -->    
                <div class="card services-card mb-3">
                    <div class="card-header">
                        <h4>Serviços Disponíveis:</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col">
                                <i class="fas fa-water fa-2x"></i>
                                <p>Água</p>
                            </div>
                            <div class="col">
                                <i class="fas fa-bolt fa-2x"></i>
                                <p>Luz</p>
                            </div>
                            <div class="col">
                                <i class="fas fa-burn fa-2x"></i>
                                <p>Gás</p>
                            </div>
                            <div class="col">
                                <i class="fas fa-wifi fa-2x"></i>
                                <p>Internet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>