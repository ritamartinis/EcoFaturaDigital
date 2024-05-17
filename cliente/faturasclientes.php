<?php
session_start();

// Verificar se o utilizador é cliente ou não
if (!isset($_SESSION["utilizador"]) || $_SESSION["utilizador"] !== "cliente") {
    // Se não for, volta ao index
    header("location: ../index.php");
    exit;
}

// Verificar se o ID do cliente está definido na sessão
if (!isset($_SESSION['id_cliente'])) {
    // Se não estiver, volta ao index
    header("location: ../index.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// BD
$maquina = "fdb1031.runhosting.com";
$userbd = "4433528_ritans";
$passbd = "RitaM#93";
$bd = "4433528_ritans";

// Estabelece a conexão
$ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
$ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa a exibição de erros

// Inicializa os parâmetros de filtro
$tipo = $_GET['tipo'] ?? '';
$estado = $_GET['estado'] ?? '';
$dataInicio = $_GET['dataInicio'] ?? '';
$dataFim = $_GET['dataFim'] ?? '';

// Construir a consulta com base nos filtros
$sql = "SELECT * FROM gestaoFaturas WHERE id_cliente = ?";
$params = [$id_cliente];

if (!empty($tipo)) {
    $sql .= " AND tipo_fatura = ?";
    $params[] = $tipo;
}

if (!empty($estado)) {
    $sql .= " AND estado = ?";
    $params[] = $estado;
}

if (!empty($dataInicio) && !empty($dataFim)) {
    $sql .= " AND data_emissao BETWEEN ? AND ?";
    $params[] = $dataInicio;
    $params[] = $dataFim;
}

// Consultar as faturas DESTE cliente 
$consulta = $ligacao->prepare($sql);
$consulta->execute($params);
$faturas_cliente = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>As Minhas Faturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbarcliente.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="jumbotron text-center">
                    <h1 class="display-4">As Minhas Faturas</h1>
                    <hr class="my-4">
            </div>
               <!-- Formulário de Filtros -->
            <div class="container mt-5">
                <div class="filter-icon" onclick="toggleFilter()">
                    <i class="fas fa-filter"> Filtrar</i>
                </div>
                <div class="filter-popup" id="myFilter">
                    <form class="row g-3 mb-4" method="get">
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo de Fatura</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option selected value="">Todos</option>
                                <option value="Agua" <?= $tipo == 'Agua' ? 'selected' : '' ?>>Água</option>
                                <option value="Luz" <?= $tipo == 'Luz' ? 'selected' : '' ?>>Luz</option>
                                <option value="Gas" <?= $tipo == 'Gas' ? 'selected' : '' ?>>Gás</option>
                                <option value="Internet" <?= $tipo == 'Internet' ? 'selected' : '' ?>>Internet</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option selected value="">Todos</option>
                                <option value="Em processamento" <?= $estado == 'Em processamento' ? 'selected' : '' ?>>Em processamento</option>
                                <option value="Aguarda Pagamento" <?= $estado == 'Aguarda Pagamento' ? 'selected' : '' ?>>Aguarda Pagamento</option>
                                <option value="Pago" <?= $estado == 'Pago' ? 'selected' : '' ?>>Pago</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="dataInicio" class="form-label">De</label>
                            <input type="date" class="form-control" id="dataInicio" name="dataInicio" value="<?= $dataInicio ?>">
                        </div>
                        <div class="col-md-2">
                            <label for="dataFim" class="form-label">Até</label>
                            <input type="date" class="form-control" id="dataFim" name="dataFim" value="<?= $dataFim ?>">
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            </div>
                    </form>
                </div>
            </div>
            <?php if (count($faturas_cliente) > 0): ?>
                <div class="d-flex flex-wrap">
                    <?php foreach ($faturas_cliente as $fatura): ?>
                        <div class="card m-2" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title">Fatura #<?= $fatura['id_fatura']; ?></h5>
                                <p class="card-text">
                                    <strong>Tipo: </strong><?= $fatura['tipo_fatura']; ?><br>
                                    <strong>Valor: </strong><?= $fatura['valor']; ?><br>
                                    <strong>Emissão: </strong><?= $fatura['data_emissao']; ?><br>
                                    <strong>Estado: </strong><span class="estado <?= strtolower(str_replace(' ', '-', $fatura['estado'])); ?>">
                                        <?= $fatura['estado']; ?>
                                    </span>
                                </p>
                                <div class="separator"></div>
                                <a href="descarregarfaturacliente.php?id=<?= $fatura['id_fatura']; ?>" class="btn btn-success d-flex justify-content-center">Descarregar Fatura</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
    				<img src="../fotosdiversas/erro.png" alt="No results">
				</div>
				<div class="text-center error-box">
   					 <p>Ups, não foram encontradas faturas registadas.</p>
				</div>
            <?php endif; ?>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleFilter() {
            var filterPopup = document.getElementById("myFilter");
            if (filterPopup.style.display === "none") {
                filterPopup.style.display = "block";
            } else {
                filterPopup.style.display = "none";
            }
        }
    </script>
</body>
</html>