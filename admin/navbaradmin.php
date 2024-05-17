<link href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" rel="stylesheet">

<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar vh-100">
    <div class="position-sticky pt-3 d-flex flex-column h-100">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active d-flex align-items-center" aria-current="page" href="area_admin.php">
                    <img src="../fotosdiversas/pin.png" alt="Logo">
                    <span class="ms-2">EcoFatura Digital</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="area_admin.php">
                    <i data-feather="home"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="verlistaclientes.php">
                    <i data-feather="file-text"></i>
                    Ver Lista de Clientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="verfaturasadmin.php">
                    <i data-feather="list"></i>
                    Ver Lista de Faturas
                </a>
            </li>
        </ul>
        <div class="mt-auto">
            <ul class="nav flex-column mb-2">
                <li class="nav-item d-flex align-items-center justify-content-between">
                    <a class="nav-link d-flex align-items-center p-0" href="#">
                        <img src="../fotosdiversas/_admin.png" alt="Foto de Perfil do Admin"
                            class="img-thumbnail user-img">
                        <span class="ms-2 username">Administrador</span>
                    </a>
                    <a href="../logout.php" class="nav-link d-flex align-items-center p-0">
                        <i data-feather="power" class="logout-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>