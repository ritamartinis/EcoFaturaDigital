<?php
session_start();

// Verifica se o utilizador é cliente ou não
if (!isset($_SESSION["utilizador"]) || $_SESSION["utilizador"] !== "cliente") {
    header("location: ../index.php");
    exit;
}

// Verifica se o ID do cliente está definido na sessão
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

// Consulta as faturas DESTE cliente 
$consulta = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_cliente = '$id_cliente'");
$consulta->execute();
$faturas_cliente = $consulta->fetchAll(PDO::FETCH_ASSOC);


require '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Função para gerar o PDF da fatura
function gerarPDF($dadosFatura, $ligacao)
{
    // Crie uma nova instância do Dompdf    
    $dompdf = new Dompdf();

    // Construção do HTML da fatura
    $html = '<h1></strong>Fatura</strong></h1>';
    $html .= '<p>Tipo de Fatura: ' . $dadosFatura['tipo_fatura'] . '</p>';
    $html .= '<p>Valor: ' . $dadosFatura['valor'] . '</p>';
    $html .= '<p>Data de Emissão: ' . $dadosFatura['data_emissao'] . '</p>';
    $html .= '<p>Estado: ' . $dadosFatura['estado'] . '</p>';

    // Carrega o HTML no Dompdf
    $dompdf->loadHtml($html);

    // Configura o tamanho do papel e a orientação
    $dompdf->setPaper('A4', 'landscape');

    // "Renderiza" o PDF
    $dompdf->render();

    // Envia o PDF para o browser
    $dompdf->stream();
}

// Verifica o ID da fatura
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idFatura = $_GET['id'];

    // Consulta a fatura na base de dados com o id do cliente
    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura = :id_fatura AND id_cliente = :id_cliente");
    $consulta->bindParam(':id_fatura', $idFatura, PDO::PARAM_INT);
    $consulta->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
    $consulta->execute();
    $fatura = $consulta->fetch(PDO::FETCH_ASSOC);

    // Verifica se a fatura foi encontrada
    if ($fatura) {
        // Gera o PDF da fatura
        gerarPDF($fatura, $ligacao);
        exit;
    }
}
?>