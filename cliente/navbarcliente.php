<?php
session_start();

$id_cliente = $_SESSION['id_cliente'];

// BD
$maquina = "fdb1031.runhosting.com";
$userbd = "4433528_ritans";
$passbd = "RitaM#93";
$bd = "4433528_ritans";

try {
    // Ligação à BD   
    $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE id_cliente = '$id_cliente'");
    $consulta->execute();
    $dados = $consulta->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

<link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">

<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar vh-100">
    <div class="position-sticky pt-3 d-flex flex-column h-100">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active d-flex align-items-center" aria-current="page" href="area_cliente.php">
                    <img src="../fotosdiversas/pin.png" alt="Logo">
                    <span class="ms-2">EcoFatura Digital</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="area_cliente.php">
                    <i data-feather="home"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="dadospessoais.php">
                    <i data-feather="file-text"></i>
                    Os Meus Dados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="faturasclientes.php">
                    <i data-feather="list"></i>
                    As Minhas Faturas
                </a>
            </li>
        </ul>
        <div class="mt-auto">
            <ul class="nav flex-column mb-2">
                <li class="nav-item">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <a class="nav-link d-flex align-items-center p-0" href="#">
                                
                            <!-- Verifica se o utilizador já tem foto, senão atribui a default -->
                            <?php if (!empty($dados['foto_perfil'])): ?>
                                <img src="<?= $dados['foto_perfil'] ?>" alt="Foto de Perfil" class="img-thumbnail user-img">
                            <?php else: ?>
                                <img src="../default.jpg" alt="Foto de Perfil Padrão" class="img-thumbnail user-img">
                            <?php endif; ?>
                            <!-- vai buscar o nome do cliente à bd -->    
                            <span class="ms-2 username"><?= $dados['nome_cliente'] ?></span>
                        </a>
                        <a href="../logout.php" class="nav-link d-flex align-items-center p-0">
                            <i data-feather="power" class="logout-icon"></i>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>