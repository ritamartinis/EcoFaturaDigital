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



try {
    // Conexão à BD  
    $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturasUtilizadores WHERE id_cliente = '$id_cliente'");
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
    <title>Dados Pessoais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' href='../estilos.css'>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <?php include_once 'navbarcliente.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="jumbotron text-center">
                    <h1 class="display-4">Dados Pessoais</h1>
                    <hr class="my-4">
                </div>
                <!-- card dos dados pessoais -->    
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="profile-image mb-3">
                                        <!-- Se tiver foto -->
                                    <?php if (!empty($dados['foto_perfil'])): ?>
                                        <img src="<?= $dados['foto_perfil'] ?>" alt="Foto de Perfil"
                                            class="rounded-circle img-thumbnail" style="width: 150px; height: 150px;">
                                    <?php else: ?>
                                        <!-- Senão é atribuida a default -->
                                        <img src="../fotosdiversas/default.jpg" alt="Foto de Perfil Padrão"
                                            class="rounded-circle img-thumbnail" style="width: 150px; height: 150px;">
                                    <?php endif; ?>
                                </div>
                                    
                                <h4 class="card-title mb-3"><?= $dados['nome_cliente'] ?></h4>
                                <p class="card-text mb-2"><strong>Morada:</strong> <?= $dados['endereco'] ?></p>
                                <p class="card-text mb-2"><strong>Contacto:</strong> <?= $dados['contacto'] ?></p>
                                <p class="card-text mb-2"><strong>Nome de Utilizador:</strong> <?= $dados['user'] ?></p>
                                <p class="card-text mb-2"><strong>Palavra-Passe:</strong>
                                    <?= str_repeat('*', strlen($dados['pass'])) ?></p>
                                <div class="separator"></div>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                    data-bs-target="#editarDadosModal">
                                    Editar Dados Pessoais
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
        
    <!-- Modal de Edição -->
    <div class="modal fade" id="editarDadosModal" tabindex="-1" aria-labelledby="editarDadosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarDadosModalLabel">Editar Dados Pessoais</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="editardadospessoais.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_cliente" value="<?= $dados['id_cliente'] ?>">
                        <!-- Campo para upload de foto -->
                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label">Foto de Perfil:</label>
                            <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
                            <!-- Mostra a foto atual -->
                            <img src="<?= $dados['foto_perfil'] ?>" alt="Foto de Perfil Atual"
                                class="img-thumbnail mt-2" style="width: 100px; height: auto; ">
                        </div>
                        <div class="mb-3">
                            <label for="nome_cliente" class="form-label">Nome de Cliente:</label>
                            <input type="text" class="form-control" id="nome_cliente" name="nome_cliente"
                                value="<?= $dados['nome_cliente'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="endereco" class="form-label">Endereço:</label>
                            <input type="text" class="form-control" id="endereco" name="endereco"
                                value="<?= $dados['endereco'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="contacto" class="form-label">Contacto:</label>
                            <input type="text" class="form-control" id="contacto" name="contacto"
                                value="<?= $dados['contacto'] ?>">
                        </div>
                        <div class="mb-3">
                            <label for="user" class="form-label">Nome de Utilizador:</label>
                            <input type="text" class="form-control" id="user" name="user" value="<?= $dados['user'] ?>"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="pass" class="form-label">Senha:</label>
                            <input type="password" class="form-control" id="pass" name="pass"
                                value="<?= $dados['user'] ?>" required>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-success btn-sm">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>