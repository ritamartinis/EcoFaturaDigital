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

// POST
$nome_cliente = $_POST['nome_cliente'];
$endereco = $_POST['endereco'];
$contacto = $_POST['contacto'];
$user = $_POST['user'];
$pass = $_POST['pass'];

// Upload da nova foto
if ($_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
    $nome_arquivo = $_FILES['foto_perfil']['name'];
    $caminho_temporario = $_FILES['foto_perfil']['tmp_name'];
    $diretorio_destino = "uploads/";

    // Cria uma pasta chamada "uploads" se ela não existir já
    if (!is_dir($diretorio_destino)) {
        mkdir($diretorio_destino, 0777, true);
    }

    // Cria um nome único para o ficheiro
    $nome_arquivo_unico = $id_cliente . '_' . $nome_arquivo;
    $caminho_destino = $diretorio_destino . $nome_arquivo_unico;

    // Coloca o ficheiro na pasta
    if (move_uploaded_file($caminho_temporario, $caminho_destino)) {
        // Coloca o path da foto na bd
        $caminho_foto = $caminho_destino;
    } else {
        echo "Erro ao enviar o arquivo.";
        exit;
    }
} else {
    // Se nenhum ficheiro foi enviado, mantem-se a que estava antes
    $caminho_foto = $_POST['foto_atual'];
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

    // UPDATE dos dadospessoais
    $consulta = $ligacao->prepare("UPDATE gestaoFaturasUtilizadores SET nome_cliente = :nome_cliente, endereco = :endereco, contacto = :contacto, user = :user, pass = :pass, foto_perfil = :foto_perfil WHERE id_cliente = :id_cliente");
    $consulta->bindParam(':nome_cliente', $nome_cliente);
    $consulta->bindParam(':endereco', $endereco);
    $consulta->bindParam(':contacto', $contacto);
    $consulta->bindParam(':user', $user);
    $consulta->bindParam(':pass', $pass);
    $consulta->bindParam(':foto_perfil', $caminho_foto);
    $consulta->bindParam(':id_cliente', $id_cliente);
    $consulta->execute();

    // Depois do update, volta à página dos dados
    header("location: dadospessoais.php");
    exit;

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>