<?php
session_start();
include('db.php');

// Verifica se o usuário está autenticado como admin
if ($_SESSION['tipo_usuario'] != 'admin') {
    die("Acesso negado. Apenas administradores podem acessar essa página.");
}

// Função para inserir meta
function inserirMeta($cidade, $meta, $mesAno, $filial_id, $usuario_id) {
    global $conn;

    if (preg_match("/^\d{2}\/\d{4}$/", $mesAno)) {
        $dataFormatada = DateTime::createFromFormat('m/Y', $mesAno);
        if ($dataFormatada !== false) {
            $dataFormatada = $dataFormatada->format("Y-m-01");
        } else {
            echo "<div class='alert alert-danger'>Erro ao formatar a data. Use MM/YYYY.</div>";
            return;
        }
    } else {
        echo "<div class='alert alert-danger'>Formato de data inválido. Use MM/YYYY.</div>";
        return;
    }

    $sql = "INSERT INTO metas_mensais (cidade, meta, mes_ano, filial_id, usuario_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisis", $cidade, $meta, $dataFormatada, $filial_id, $usuario_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Meta inserida com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao inserir meta: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Deletar meta
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $sql = "DELETE FROM metas_mensais WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    inserirMeta($_POST['cidade'], $_POST['meta'], $_POST['mes_ano'], $_SESSION['filial_id'], $_SESSION['usuario_id']);
}

// Obter todas as metas cadastradas
function obterMetas() {
    global $conn;
    $sql = "SELECT m.*, u.nome AS usuario_nome FROM metas_mensais m JOIN usuarios u ON m.usuario_id = u.id ORDER BY m.mes_ano DESC";
    return $conn->query($sql);
}
$metas = obterMetas();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Metas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a href="adicionar_usuario.php" class="btn btn-success me-2">Cadastrar Novo Usuário</a>
            <a href="index.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="text-center mb-4">Cadastrar Nova Meta</h1>
        <div class="card shadow-lg">
            <div class="card-body">
                <form action="admin_dashboard.php" method="POST">
                    <div class="mb-3">
                        <label for="cidade" class="form-label">Cidade</label>
                        <input type="text" name="cidade" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="meta" class="form-label">Meta</label>
                        <input type="number" name="meta" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="mes_ano" class="form-label">Mês/Ano (MM/YYYY)</label>
                        <input type="text" name="mes_ano" class="form-control" placeholder="MM/YYYY" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cadastrar Meta</button>
                </form>
            </div>
        </div>

        <h2 class="text-center my-5">Metas Cadastradas</h2>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Cidade</th>
                            <th>Meta</th>
                            <th>Mês/Ano</th>
                            <th>Data de Adição</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $metas->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['usuario_nome'] ?></td>
                                <td><?= $row['cidade'] ?></td>
                                <td><?= $row['meta'] ?></td>
                                <td><?= date("m/Y", strtotime($row['mes_ano'])) ?></td>
                                <td><?= $row['created_at'] != '0000-00-00 00:00:00' ? date("d/m/Y H:i:s", strtotime($row['created_at'])) : 'Data não disponível' ?></td>
                                <td>
                                    <a href="admin_dashboard.php?deletar=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir esta meta?')">Deletar</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
