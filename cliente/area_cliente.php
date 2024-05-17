<?php
session_start();

// Verifica se o utilizador é cliente ou não
if (!isset($_SESSION["utilizador"]) || $_SESSION["utilizador"] !== "cliente") {
    header("location: ../index.php");
    exit;
}

// Verifica se o ID do cliente está definido na sessão - feito no index antes de direccionar para aqui
if (!isset($_SESSION['id_cliente'])) {
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

// Consulta as faturas de luz DESTE cliente
$consultaLuz = $ligacao->prepare("SELECT data_emissao, valor FROM gestaoFaturas WHERE id_cliente = '$id_cliente' AND tipo_fatura = 'Luz'");
$consultaLuz->execute();
$faturasLuz = $consultaLuz->fetchAll(PDO::FETCH_ASSOC);

// Consulta as faturas de água DESTE cliente
$consultaAgua = $ligacao->prepare("SELECT data_emissao, valor FROM gestaoFaturas WHERE id_cliente = '$id_cliente' AND tipo_fatura = 'Água'");
$consultaAgua->execute();
$faturasAgua = $consultaAgua->fetchAll(PDO::FETCH_ASSOC);

// Consulta as faturas de gás DESTE cliente
$consultaGas = $ligacao->prepare("SELECT data_emissao, valor FROM gestaoFaturas WHERE id_cliente = '$id_cliente' AND tipo_fatura = 'Gás'");
$consultaGas->execute();
$faturasGas = $consultaGas->fetchAll(PDO::FETCH_ASSOC);

// Consulta as faturas de internet DESTE cliente
$consultaInternet = $ligacao->prepare("SELECT data_emissao, valor FROM gestaoFaturas WHERE id_cliente = '$id_cliente' AND tipo_fatura = 'Internet'");
$consultaInternet->execute();
$faturasInternet = $consultaInternet->fetchAll(PDO::FETCH_ASSOC);

// Criar a estrutura dos Gráficos
function estruturarDados($faturas, $tipo)
{
    $dados = array(
        'labels' => array(),
        'datasets' => array(
            array(
                'label' => 'Consumo de ' . $tipo,
                'data' => array(),
                'fill' => false,
                'borderColor' => ($tipo === 'Luz') ? 'orange' : (($tipo === 'Água') ? 'blue' : (($tipo === 'Gás') ? 'green' : 'purple')),
                'tension' => 0.1
            )
        )
    );

    foreach ($faturas as $fatura) {
        // Adiciona a data de emissão para aparecer em baixo
        $dados['labels'][] = $fatura['data_emissao'];

        // Adicionar o valor pago do lado esquerdo
        $dados['datasets'][0]['data'][] = $fatura['valor'];
    }

    return $dados;
}

//Estrutura dos dados
$dadosLuz = estruturarDados($faturasLuz, 'Luz');
$dadosAgua = estruturarDados($faturasAgua, 'Água');
$dadosGas = estruturarDados($faturasGas, 'Gás');
$dadosInternet = estruturarDados($faturasInternet, 'Internet');
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbarcliente.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">A Minha Área de Cliente</h1>
                    <hr class="my-4">
                </div>
                <!-- Serviços Disponíveis num card + os gráficos -->    
                <div class="card services-card mb-3">
                    <div class="card-header">
                        <h4>Serviços Disponíveis:</h4>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col">
                                <i class="fas fa-water fa-2x"></i>
                                <p>Água</p>
                                <canvas id="graficoAgua"></canvas>
                            </div>
                            <div class="col">
                                <i class="fas fa-bolt fa-2x"></i>
                                <p>Luz</p>
                                <canvas id="graficoLuz"></canvas>
                            </div>
                            <div class="col">
                                <i class="fas fa-burn fa-2x"></i>
                                <p>Gás</p>
                                <canvas id="graficoGas"></canvas>
                            </div>
                            <div class="col">
                                <i class="fas fa-wifi fa-2x"></i>
                                <p>Internet</p>
                                <canvas id="graficoInternet"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
            
        // Função para criar um gráfico com os dados das faturas
        function criarGrafico(idCanvas, tipo, dados) {
            var ctx = document.getElementById(idCanvas).getContext('2d');
            new Chart(ctx, {
                type: tipo,
                data: dados,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Chama a função para criar os gráficos
        criarGrafico('graficoAgua', 'line', <?php echo json_encode($dadosAgua); ?>);
        criarGrafico('graficoLuz', 'line', <?php echo json_encode($dadosLuz); ?>);
        criarGrafico('graficoGas', 'line', <?php echo json_encode($dadosGas); ?>);
        criarGrafico('graficoInternet', 'line', <?php echo json_encode($dadosInternet); ?>);
    </script>
</body>

</html>