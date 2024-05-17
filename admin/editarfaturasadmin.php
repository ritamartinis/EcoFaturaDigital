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

// Obtém os dados dos clientes, para o dropdown. !!!!menos o admin!!!!
$consulta_clientes = $ligacao->query("SELECT id_cliente, nome_cliente FROM gestaoFaturasUtilizadores WHERE id_cliente != 1 ORDER BY id_cliente ASC");
$clientes = $consulta_clientes->fetchAll(PDO::FETCH_ASSOC);

//Obtém os tipos
$tipos_fatura = ["Água", "Luz", "Gás", "Internet"];

//Estados
$estados = ["Em processamento", "Aguarda pagamento", "Pago"];

// Verifica se o ID da fatura a ser editada foi passado via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_fatura = $_GET['id'];

    // Consulta os dados da fatura
    $consulta_fatura = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura = ?");
    $consulta_fatura->execute([$id_fatura]);
    $fatura = $consulta_fatura->fetch(PDO::FETCH_ASSOC);

    // Verifica se a fatura existe
    if (!$fatura) {
        header("location: verfaturasadmin.php");
        exit;
    }
} else {
    // Se não foi passado um ID válido via GET, redireciona à lista de faturas
    header("location: verfaturasadmin.php");
    exit;
}

// POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente = $_POST["id_cliente"];
    $tipo_fatura = $_POST["tipo_fatura"];
    $valor = $_POST["valor"];
    $data_emissao = $_POST["data_emissao"];
    $estado = $_POST["estado"];


    // Faz uma query para ir procurar o nome do cliente e compará-lo com o id_cliente já selecionado
    $procura_nome = $ligacao->prepare("SELECT nome_cliente FROM gestaoFaturasUtilizadores WHERE id_cliente = '$id_cliente'");
    $procura_nome->execute();
    $nome_cliente = $procura_nome->fetchColumn();

    // Consulta os dados da fatura
    $consulta_fatura = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura = ?");
    $consulta_fatura->execute([$id_fatura]);
    $fatura = $consulta_fatura->fetch(PDO::FETCH_ASSOC);

    // Atualiza a fatura na BD
    $consulta = $ligacao->prepare("UPDATE gestaoFaturas SET id_cliente = ?, nome_cliente = ?, tipo_fatura = ?, valor = ?, data_emissao = ?, estado = ? WHERE id_fatura = ?");
    $consulta->execute([$id_cliente, $nome_cliente, $tipo_fatura, $valor, $data_emissao, $estado, $id_fatura]);

    // Redireciona para a página de lista de faturas após a atualização
    header("location: verfaturasadmin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Fatura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Editar dados da Fatura</h1>
                </div>

                <form method="POST" class="w-50 mx-auto">
                    <div class="mb-3">
                        <label for="id_cliente" class="form-label">Cliente</label>
                        <select class="form-select" id="id_cliente" name="id_cliente">
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente['id_cliente'] ?>"
                                    <?= ($cliente['id_cliente'] == $fatura['id_cliente']) ? 'selected' : '' ?>>
                                    <?= $cliente['nome_cliente'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_fatura" class="form-label">Tipo de Fatura</label>
                        <select class="form-select" id="tipo_fatura" name="tipo_fatura">
                            <?php foreach ($tipos_fatura as $tipo): ?>
                                <option value="<?= $tipo ?>" <?= ($tipo == $fatura['tipo_fatura']) ? 'selected' : '' ?>>
                                    <?= $tipo ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" value="<?= $fatura['valor'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="data_emissao" class="form-label">Data de Emissão</label>
                        <input type="date" class="form-control" id="data_emissao" name="data_emissao"
                            value="<?= $fatura['data_emissao'] ?>">
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <?php foreach ($estados as $estado): ?>
                                <option value="<?= $estado ?>" <?= ($estado == $fatura['estado']) ? 'selected' : '' ?>>
                                    <?= $estado ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="offset-sm-3 col-sm-3 d-grid">
                            <button type="submit" class="btn btn-success btn-sm">Editar</button>
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