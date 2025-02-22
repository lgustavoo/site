<?php
session_start();
include('db.php');

// Verifica se o usuário está autenticado como admin
if ($_SESSION['tipo_usuario'] != 'admin') {
    die("Acesso negado. Apenas administradores podem acessar essa página.");
}

// Busca todas as metas do banco de dados
$sql = "SELECT m.cidade, m.meta, m.mes_ano, m.data_adicao, u.nome AS usuario_nome 
        FROM metas_mensais m 
        JOIN usuarios u ON m.usuario_id = u.id 
        ORDER BY m.data_adicao DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Metas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Metas Adicionadas</h1>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cidade</th>
                            <th>Meta</th>
                            <th>Mês/Ano</th>
                            <th>Adicionada Por</th>
                            <th>Data de Adição</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['cidade'] ?></td>
                                <td><?= $row['meta'] ?></td>
                                <!-- Exibe o Mês/Ano formatado -->
                                <td><?= date("m/Y", strtotime($row['mes_ano'])) ?></td>
                                <td><?= $row['usuario_nome'] ?></td>
                                <!-- Exibe a data e hora corretamente -->
                                <td><?= date("d/m/Y H:i:s", strtotime($row['data_adicao'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <!-- Botão para voltar ao painel de administração -->
                <a href="admin_dashboard.php" class="btn btn-secondary w-100 mt-3">Voltar para o Painel de Administração</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
