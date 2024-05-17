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
// Verifica se o ID é válido
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Consulta os dados da fatura
    $id_fatura = $_GET['id'];
    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura ='$id_fatura'");
    $consulta->execute();
    $fatura = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verifica se a fatura existe
    if (!$fatura) {
        header("location: verfaturasadmin.php");
        exit;
    }
} else {
    // Se não for passado um ID válido via GET, redireciona à lista de faturas
    header("location: verfaturasadmin.php");
    exit;
}

// POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id_fatura"])) {
    $id_fatura = $_POST["id_fatura"];

    // Elimina da BD
    $stmt = $ligacao->prepare("DELETE FROM gestaoFaturas WHERE id_fatura = '$id_fatura'");
    $stmt->execute();

    // Depois de eliminar, volta à lista
    header("location: verfaturasadmin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Fatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Eliminar Fatura</h1>
                </div>

                <form action="" method="POST" class="w-50 mx-auto">
                    <input type="hidden" name="id_fatura" value="<?php echo $fatura['id_fatura']; ?>">
                    <div class="mb-3">
                        <label for="id_cliente" class="form-label">ID do Cliente</label>
                        <input type="text" class="form-control" id="id_cliente" name="id_cliente"
                            value="<?php echo $fatura['id_cliente']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="nome_cliente" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" id="nome_cliente" name="nome_cliente"
                            value="<?php echo $fatura['nome_cliente']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_fatura" class="form-label">Tipo de Fatura</label>
                        <input type="text" class="form-control" id="tipo_fatura" name="tipo_fatura"
                            value="<?php echo $fatura['tipo_fatura']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor"
                            value="<?php echo $fatura['valor']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="data_emissao" class="form-label">Data de Emissão</label>
                        <input type="text" class="form-control" id="data_emissao" name="data_emissao"
                            value="<?php echo $fatura['data_emissao']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" id="estado" name="estado"
                            value="<?php echo $fatura['estado']; ?>" disabled>
                    </div>
                    <div class="row mb-3">
                        <div class="offset-sm-3 col-sm-3 d-grid">
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </div>
                        <div class="col-sm-3 d-grid">
                            <a class="btn btn-secondary" href="verfaturasadmin.php" role="button">Cancelar</a>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>