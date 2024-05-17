<?php
header("Access-Control-Allow-Origin: *");

// BD
$maquina = "fdb1031.runhosting.com";
$userbd = "4433528_ritans";
$passbd = "RitaM#93";
$bd = "4433528_ritans";

try {
    // Estabelece a conexão
    $ligacao = new PDO("mysql:host=$maquina;dbname=$bd", $userbd, $passbd);
    $ligacao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $ligacao->query("SET NAMES utf8");
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}

// Verifica o user e a pass
function validacao($user, $pass)
{
    global $ligacao;
    $consulta = $ligacao->prepare("SELECT tipo FROM gestaoFaturasUtilizadores WHERE user = :user AND pass = :pass");
    $consulta->execute(['user' => $user, 'pass' => $pass]);

    if ($consulta->rowCount() == 0) {
        header("Content-type: application/json");
        echo json_encode(['Info' => "ACESSO NEGADO! Credenciais Inválidas!"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $data = $consulta->fetch(PDO::FETCH_ASSOC);
    return $data['tipo'];  // Faz return o tipo de utilizador (admin ou cliente)
}

// 1 - Listar 1 registo e enviar dados em XML
function umafaturaxml($id_fatura, $tipo)
{
    global $ligacao;

    // Consulta
    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura = :id_fatura");
    $consulta->execute(['id_fatura' => $id_fatura]);

    header("Content-type: text/xml");
    header("Access-Control-Allow-Origin: *");
    print "<?xml version='1.0' encoding='UTF-8'?>";
    print "<Faturas>";

    if ($consulta->rowCount() > 0) {
        while ($row = $consulta->fetch(PDO::FETCH_OBJ)) {
            print "<Fatura>";
            print "<id_fatura>$row->id_fatura</id_fatura>";
            print "<id_cliente>$row->id_cliente</id_cliente>";
            print "<tipo_fatura>$row->tipo_fatura</tipo_fatura>";
            print "<valor>$row->valor</valor>";
            print "<data_emissao>$row->data_emissao</data_emissao>";
            print "<estado>$row->estado</estado>";
            print "<nome_cliente>$row->nome_cliente</nome_cliente>";
            print "</Fatura>";
        }
    } else {
        print "<Mensagem>";
        print "<Info>A fatura não existe na nossa base de dados.</Info>";
        print "</Mensagem>";
    }

    print "</Faturas>";
}

// 2 - Listar todos os registos e enviar dados em XML
function todasfaturasxml($tipo)
{
    global $ligacao;
    $consulta = $ligacao->query("SELECT * FROM gestaoFaturas");

    header("Content-type: text/xml");
    print "<?xml version='1.0' encoding='UTF-8'?>";
    print "<Faturas>";

    while ($row = $consulta->fetch(PDO::FETCH_OBJ)) {
        print "<Fatura>";
        print "<id_fatura>$row->id_fatura</id_fatura>";
        print "<id_cliente>$row->id_cliente</id_cliente>";
        print "<tipo_fatura>$row->tipo_fatura</tipo_fatura>";
        print "<valor>$row->valor</valor>";
        print "<data_emissao>$row->data_emissao</data_emissao>";
        print "<estado>$row->estado</estado>";
        print "<nome_cliente>$row->nome_cliente</nome_cliente>";
        print "</Fatura>";
    }

    print "</Faturas>";
}

// 3 - Listar 1 registo e enviar dados em JSON
function umafaturajson($id_fatura, $tipo)
{
    global $ligacao;
    $consulta = $ligacao->prepare("SELECT * FROM gestaoFaturas WHERE id_fatura = :id_fatura");
    $consulta->execute(['id_fatura' => $id_fatura]);
    $fatura = $consulta->fetch(PDO::FETCH_ASSOC);

    header("Content-type: application/json");
    if ($fatura) {
        echo json_encode($fatura, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['Mensagem' => "A fatura não consta da nossa base de dados."], JSON_UNESCAPED_UNICODE);
    }
}

// 4 - Listar todos os registos e enviar dados em JSON
function todasfaturasjson($tipo)
{
    global $ligacao;
    $consulta = $ligacao->query("SELECT * FROM gestaoFaturas");
    $faturas = $consulta->fetchAll(PDO::FETCH_ASSOC);

    header("Content-type: application/json");
    echo json_encode(['Faturas' => $faturas], JSON_UNESCAPED_UNICODE);
}

// 5 - Criar 1 novo registo e enviar uma mensagem personalizada em XML
function adicionarfaturaxml($dados, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: text/xml");
        print "<?xml version='1.0' encoding='UTF-8'?>";
        print "<Mensagem>";
        print "<Info>Acesso negado! Apenas o admin pode adicionar faturas. Obrigada.</Info>";
        print "</Mensagem>";
        exit;
    }

    global $ligacao;
    $sql = "INSERT INTO gestaoFaturas (id_cliente, tipo_fatura, valor, data_emissao, estado, nome_cliente) VALUES (?, ?, ?, ?, ?, ?)";
    $consulta = $ligacao->prepare($sql);
    $consulta->execute([$dados['id_cliente'], $dados['tipo_fatura'], $dados['valor'], $dados['data_emissao'], $dados['estado'], $dados['nome_cliente']]);

    header("Content-type: text/xml");
    print "<?xml version='1.0' encoding='UTF-8'?>";
    print "<Mensagem>";

    if ($consulta->rowCount() > 0) {
        print "<Info>Fatura adicionada com sucesso!</Info>";
    } else {
        print "<Info>Erro ao adicionar a Fatura!</Info>";
    }
    print "</Mensagem>";
}

// 6 - Eliminar 1 registo e enviar uma mensagem personalizada em XML
function eliminarfaturaxml($id_fatura, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: text/xml");
        print "<?xml version='1.0' encoding='UTF-8'?>";
        print "<Mensagem>";
        print "<Info>Acesso negado! Apenas o admin pode eliminar faturas. Obrigada.</Info>";
        print "</Mensagem>";
        exit;
    }

    global $ligacao;
    $consulta = $ligacao->prepare("DELETE FROM gestaoFaturas WHERE id_fatura = :id_fatura");
    $consulta->execute(['id_fatura' => $id_fatura]);

    header("Content-type: text/xml");
    print "<?xml version='1.0' encoding='UTF-8'?>";
    print "<Mensagem>";
    if ($consulta->rowCount() > 0) {
        print "<Info>Fatura eliminada com sucesso!</Info>";
    } else {
        print "<Info>Erro ao eliminar a Fatura!</Info>";
    }
    print "</Mensagem>";
}

// 7 - Criar 1 novo registo e enviar uma mensagem personalizada em JSON
function adicionarfaturajson($dados, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: application/json");
        echo json_encode(['Mensagem' => "Acesso negado! Apenas o admin pode adicionar faturas. Obrigada."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    global $ligacao;
    $sql = "INSERT INTO gestaoFaturas (id_cliente, tipo_fatura, valor, data_emissao, estado, nome_cliente) VALUES (?, ?, ?, ?, ?, ?)";
    $consulta = $ligacao->prepare($sql);
    $consulta->execute([$dados['id_cliente'], $dados['tipo_fatura'], $dados['valor'], $dados['data_emissao'], $dados['estado'], $dados['nome_cliente']]);
    header("Content-type: application/json");

    if ($consulta->rowCount() > 0) {
        echo json_encode(['Mensagem' => "Fatura adicionada com sucesso!"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['Mensagem' => "Erro ao adicionar a Fatura!"], JSON_UNESCAPED_UNICODE);
    }
}

// 8 - Eliminar 1 registo e enviar uma mensagem personalizada em JSON
function eliminarfaturajson($id_fatura, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: application/json");
        echo json_encode(['Mensagem' => "Acesso negado! Apenas o admin pode eliminar faturas. Obrigada."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    global $ligacao;
    $consulta = $ligacao->prepare("DELETE FROM gestaoFaturas WHERE id_fatura = :id_fatura");
    $consulta->execute(['id_fatura' => $id_fatura]);

    header("Content-type: application/json");
    if ($consulta->rowCount() > 0) {
        echo json_encode(['Mensagem' => "Fatura eliminada com sucesso!"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['Mensagem' => "Erro ao eliminar a Fatura!"], JSON_UNESCAPED_UNICODE);
    }
}

// 9 - Editar uma fatura em XML
function editarfaturaxml($id_fatura, $dados, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: text/xml");
        print "<?xml version='1.0' encoding='UTF-8'?>";
        print "<Mensagem>";
        print "<Info>Acesso negado! Apenas o admin pode editar faturas. Obrigada.</Info>";
        print "</Mensagem>";
        exit;
    }

    global $ligacao;
    $sql = "UPDATE gestaoFaturas SET id_cliente = ?, tipo_fatura = ?, valor = ?, data_emissao = ?, estado = ?, nome_cliente = ? WHERE id_fatura = ?";
    $consulta = $ligacao->prepare($sql);
    $consulta->execute([$dados['id_cliente'], $dados['tipo_fatura'], $dados['valor'], $dados['data_emissao'], $dados['estado'], $dados['nome_cliente'], $id_fatura]);

    header("Content-type: text/xml");
    print "<?xml version='1.0' encoding='UTF-8'?>";
    print "<Mensagem>";
    if ($consulta->rowCount() > 0) {
        print "<Info>Fatura editada com sucesso!</Info>";
    } else {
        print "<Info>Erro ao editar a Fatura!</Info>";
    }
    print "</Mensagem>";
}

// 10 - Editar uma fatura em JSON
function editarfaturajson($id_fatura, $dados, $tipo)
{
    if ($tipo != 'admin') {
        header("Content-type: application/json");
        echo json_encode(['Mensagem' => "Acesso negado! Apenas o admin pode editar faturas. Obrigada."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    global $ligacao;
    $sql = "UPDATE gestaoFaturas SET id_cliente = ?, tipo_fatura = ?, valor = ?, data_emissao = ?, estado = ?, nome_cliente = ? WHERE id_fatura = ?";
    $consulta = $ligacao->prepare($sql);
    $consulta->execute([$dados['id_cliente'], $dados['tipo_fatura'], $dados['valor'], $dados['data_emissao'], $dados['estado'], $dados['nome_cliente'], $id_fatura]);

    header("Content-type: application/json");
    if ($consulta->rowCount() > 0) {
        echo json_encode(['Mensagem' => "Fatura editada com sucesso!"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['Mensagem' => "Erro ao editar a Fatura!"], JSON_UNESCAPED_UNICODE);
    }
}

// Verificação do tipo utilizador 
$tipo = validacao($_GET['user'], $_GET['pass']);
$id_fatura = $_GET['id_fatura'] ?? null;

// GET
$acao = $_GET['acao'] ?? '';
switch ($acao) {

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=umafaturaxml&user=USER&pass=PASS&id_fatura=ID            
    case 'umafaturaxml':
        umafaturaxml($id_fatura, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=todasfaturasxml&user=USER&pass=PASS          
    case 'todasfaturasxml':
        todasfaturasxml($tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=umafaturajson&user=USER&pass=PASS&id_fatura=ID            
    case 'umafaturajson':
        umafaturajson($id_fatura, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=todasfaturasjson&user=USER&pass=PASS            
    case 'todasfaturasjson':
        todasfaturasjson($tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=adicionarfaturaxml&user=admin&pass=admin&id_cliente=ID&tipo_fatura=TIPO&valor=VALOR&data_emissao=2024-03-16&estado=ESTADO&nome_cliente=NOMECLIENTE           
    case 'adicionarfaturaxml':
        $dados = [
            'id_cliente' => $_GET['id_cliente'],
            'tipo_fatura' => $_GET['tipo_fatura'],
            'valor' => $_GET['valor'],
            'data_emissao' => $_GET['data_emissao'],
            'estado' => $_GET['estado'],
            'nome_cliente' => $_GET['nome_cliente']
        ];
        adicionarfaturaxml($dados, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=eliminarfaturaxml&user=admin&pass=admin&id_fatura=ID
    case 'eliminarfaturaxml':
        eliminarfaturaxml($id_fatura, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=adicionarfaturaxml&user=admin&pass=admin&id_cliente=IDCLIENTE&tipo_fatura=TIPO&valor=VALOR&data_emissao=2024-03-16&estado=ESTADO&nome_cliente=NOMECLIENTE          
    case 'adicionarfaturajson':
        $dados = [
            'id_cliente' => $_GET['id_cliente'],
            'tipo_fatura' => $_GET['tipo_fatura'],
            'valor' => $_GET['valor'],
            'data_emissao' => $_GET['data_emissao'],
            'estado' => $_GET['estado'],
            'nome_cliente' => $_GET['nome_cliente']
        ];
        adicionarfaturajson($dados, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=eliminarfaturajson&user=admin&pass=admin&id_fatura=ID           
    case 'eliminarfaturajson':
        eliminarfaturajson($id_fatura, $tipo);
        break;

    //http://ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=editarfaturaxml&user=admin&pass=admin&id_fatura=ID&id_cliente=IDCLIENTE&tipo_fatura=TIPO&valor=VALOR&data_emissao=2024-03-16&estado=ESTADO&nome_cliente=NOMECLIENTE            
    case 'editarfaturaxml':
        $dados = [
            'id_cliente' => $_GET['id_cliente'],
            'tipo_fatura' => $_GET['tipo_fatura'],
            'valor' => $_GET['valor'],
            'data_emissao' => $_GET['data_emissao'],
            'estado' => $_GET['estado'],
            'nome_cliente' => $_GET['nome_cliente']
        ];
        editarfaturaxml($id_fatura, $dados, $tipo);
        break;

    //http:///ritansmartinis.getenjoyment.net/5421/Projeto/webservice.php/?acao=editarfaturajson&user=admin&pass=admin&id_fatura=ID&id_cliente=IDCLIENTE&tipo_fatura=TIPO&valor=VALOR&data_emissao=2024-03-16&estado=ESTADO&nome_cliente=NOMECLIENTE
    case 'editarfaturajson':
        $dados = [
            'id_cliente' => $_GET['id_cliente'],
            'tipo_fatura' => $_GET['tipo_fatura'],
            'valor' => $_GET['valor'],
            'data_emissao' => $_GET['data_emissao'],
            'estado' => $_GET['estado'],
            'nome_cliente' => $_GET['nome_cliente']
        ];
        editarfaturajson($id_fatura, $dados, $tipo);
        break;

    default:
        header("Content-type: application/json");
        echo json_encode(['Mensagem' => "Ação não reconhecida"]);
        break;
}
