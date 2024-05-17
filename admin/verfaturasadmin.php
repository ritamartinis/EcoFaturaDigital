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

// Consulta para buscar nomes dos clientes
$consultaClientes = $ligacao->query("SELECT DISTINCT nome_cliente FROM gestaoFaturas ORDER BY nome_cliente");
$clientes = $consultaClientes->fetchAll(PDO::FETCH_ASSOC);

// Inicializa os parâmetros do FILTRO
$nome_cliente = $_GET['nome_cliente'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$estado = $_GET['estado'] ?? '';
$dataInicio = $_GET['dataInicio'] ?? '';
$dataFim = $_GET['dataFim'] ?? '';

// Consulta com base nos filtros introduzidos
$sql = "SELECT * FROM gestaoFaturas WHERE 1 = 1";
$params = [];

if (!empty($nome_cliente)) {
    $sql .= " AND nome_cliente LIKE ?";
    $params[] = "%$nome_cliente%";
}

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

// Consulta todos os dados de todas as faturas
$consulta = $ligacao->prepare($sql);
$consulta->execute($params);
$faturas_todas = $consulta->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Faturas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbaradmin.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Lista de Faturas</h1>
                    <hr class="my-4">
                </div>

                <!-- Formulário de Filtros -->
                <div class="container mt-5">
                    <div class="filter-icon" onclick="toggleFilter()">
                        <i class="fas fa-filter"> Filtrar</i>
                    </div>
                    <div class="filter-popup" id="myFilter">
                        <form class="row g-3 mb-4" method="get">
                            <div class="col-md-2">
                                <label for="nome_cliente" class="form-label">Nome do Cliente</label>
                                <select class="form-select" id="nome_cliente" name="nome_cliente">
                                    <option value="">Todos</option>
                                    <?php foreach ($clientes as $cliente): ?>
                                        <option value="<?= htmlspecialchars($cliente['nome_cliente']) ?>"
                                            <?= $nome_cliente == $cliente['nome_cliente'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cliente['nome_cliente']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="tipo" class="form-label">Tipo de Fatura</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option selected value="">Todos</option>
                                    <option value="Agua" <?= $tipo == 'Agua' ? 'selected' : '' ?>>Água</option>
                                    <option value="Luz" <?= $tipo == 'Luz' ? 'selected' : '' ?>>Luz</option>
                                    <option value="Gas" <?= $tipo == 'Gas' ? 'selected' : '' ?>>Gás</option>
                                    <option value="Internet" <?= $tipo == 'Internet' ? 'selected' : '' ?>>Internet</option>
                                </select>
                            </div>
                            <div class="col-md-2">
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
                                <input type="date" class="form-control" id="dataInicio" name="dataInicio"
                                    value="<?= $dataInicio ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="dataFim" class="form-label">Até</label>
                                <input type="date" class="form-control" id="dataFim" name="dataFim"
                                    value="<?= $dataFim ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                            </div>
                        </form>
                    </div>
                </div>
                <p>
                <div class="d-flex justify-content-start">
                    <a class="btn btn-success" href="adicionarfaturaadmin.php" role="button">Adicionar Nova
                        Fatura</a>
                </div>
                <p>
                    <?php if (count($faturas_todas) > 0): ?>
                    <!-- Lista das Faturas em CARDS-->
                    <div class="d-flex flex-wrap">
                        <?php foreach ($faturas_todas as $fatura): ?>
                            <div class="card m-2" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $fatura['nome_cliente']; ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted">Fatura #<?php echo $fatura['id_fatura']; ?></h6>
                                    <p class="card-text">
                                        <strong>Tipo: </strong><?php echo $fatura['tipo_fatura']; ?><br>
                                        <strong>Valor: </strong><?php echo $fatura['valor']; ?><br>
                                        <strong>Emissão: </strong><?php echo $fatura['data_emissao']; ?><br>
                                        <strong>Estado: </strong><span
                                            class="estado <?= strtolower(str_replace(' ', '-', $fatura['estado'])); ?>">
                                            <?= $fatura['estado']; ?>
                                        </span>
                                    </p>
                                    <div class="separator"></div>
                                    <div class="d-flex justify-content-center">
                                        <a href="editarfaturasadmin.php?id=<?= $fatura['id_fatura']; ?>"
                                            class="btn btn-primary m-1">Editar</a>
                                        <a href="eliminarfaturasadmin.php?id=<?= $fatura['id_fatura']; ?>"
                                            class="btn btn-danger m-1">Eliminar</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- Se não houver faturas de todo ou se não voltar nada dos filtros -->
                    <?php else: ?>
                        <div class="text-center">
                            <img src="../fotosdiversas/erro.png" alt="No results">
                        </div>
                        <div class="text-center error-box">
                            <p>Ups, não foram encontradas faturas registadas.</p>
                        </div>

                    <?php endif; ?>
                </div>
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