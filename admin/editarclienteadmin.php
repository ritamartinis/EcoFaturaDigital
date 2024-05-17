<?php
session_start();

// Verifica se o utilizador é admin
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

//GET
// Verifica o ID é válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Consulta os dados do cliente
    $id_cliente = $_GET['id'];
    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE id_cliente = '$id_cliente'");
    $consulta->execute();
    $cliente = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verifica se o cliente existe
    if (!$cliente) {
        header("location: verlistaclientes.php");
        exit;
    }
} else {
    // Se não for passado um ID de cliente, volta para lista de clientes
    header("location: verlistaclientes.php");
    exit;
}

// POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_cliente"])) {
    $id_cliente = $_POST["id_cliente"];
    $nome_cliente = $_POST["nome_cliente"];
    $endereco = $_POST["endereco"];
    $contacto = $_POST["contacto"];

    // Atualiza a BD
    $consulta = $ligacao->prepare("UPDATE gestaoFaturasUtilizadores SET nome_cliente = '$nome_cliente', endereco = '$endereco', contacto = '$contacto' WHERE id_cliente = '$id_cliente'");
    $consulta->execute();

    // Depois de atualizar, volta à lista
    header("location: verlistaclientes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Editar dados do Cliente</h1>
                </div>
                <form action="" method="POST" class="w-50 mx-auto">
                    <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                    <div class="mb-3">
                        <label for="nome_cliente" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" id="nome_cliente" name="nome_cliente"
                            value="<?php echo $cliente['nome_cliente']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco"
                            value="<?php echo $cliente['endereco']; ?>">
                    </div>
                    <div class="mb-3">
                        <label for="contacto" class="form-label">Contacto</label>
                        <input type="text" class="form-control" id="contacto" name="contacto"
                            value="<?php echo $cliente['contacto']; ?>">
                    </div>
                    <div class="row mb-3">
                        <div class="offset-sm-3 col-sm-3 d-grid">
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                        <div class="col-sm-3 d-grid">
                            <a class="btn btn-secondary" href="verlistaclientes.php" role="button">Cancelar</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>