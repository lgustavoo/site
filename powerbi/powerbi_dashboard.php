<?php
session_start();
include('db.php');

// Verifica se o usuário é Power BI
if ($_SESSION['tipo_usuario'] != 'powerbi') {
    die("Acesso negado. Apenas usuários Power BI podem acessar essa página.");
}

$filial_id = $_SESSION['filial_id'];
$sql = "SELECT * FROM metas_mensais WHERE filial_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $filial_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Power BI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h1 class="text-center mb-4">Metas de Filial</h1>
        <div class="card shadow-lg">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Cidade</th>
                            <th>Meta</th>
                            <th>Mês/Ano</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['cidade'] ?></td>
                                <td><?= $row['meta'] ?></td>
                                <td><?= date("m/Y", strtotime($row['mes_ano'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
