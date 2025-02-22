<?php
session_start();
include('db.php');

// Verifica se o usuário está logado e se é um admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != 'admin') {
    // Caso não tenha permissão ou não esteja logado, redireciona para a página de login
    header("Location: index.php"); // Redireciona para a página de login (index.php)
    exit();
}

// Função para cadastrar um novo usuário no banco de dados
function cadastrarUsuario($nome, $email, $senha, $tipo_usuario, $filial_id) {
    global $conn;

    // Criptografando a senha do usuário antes de salvar no banco de dados
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Comando SQL para inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, tipo_usuario, filial_id) 
            VALUES (?, ?, ?, ?, ?)";

    // Preparando e executando a consulta SQL
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        // Exibe erro caso a preparação da consulta falhe
        echo "Erro na preparação da consulta: " . $conn->error;
        return false;
    }
    
    $stmt->bind_param("ssssi", $nome, $email, $senhaHash, $tipo_usuario, $filial_id);

    if ($stmt->execute()) {
        return true;
    } else {
        // Exibe erro caso a execução falhe
        echo "Erro ao executar a consulta: " . $stmt->error;
        return false;
    }

    $stmt->close();
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $filial_id = $_POST['filial_id'];

    // Chama a função para cadastrar o usuário
    if (cadastrarUsuario($nome, $email, $senha, $tipo_usuario, $filial_id)) {
        // Caso o cadastro seja bem-sucedido, redireciona para a página de login (index.php)
        $_SESSION['success'] = "Usuário cadastrado com sucesso!";
        header("Location: index.php"); // Redireciona para o login
        exit();
    } else {
        // Caso haja algum erro no cadastro, exibe uma mensagem de erro
        $erro = "Erro ao cadastrar usuário. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Usuário</title>
    <style>
        /* Estilo simples para o formulário */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        .success {
            color: green;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Adicionar Novo Usuário</h2>
        
        <!-- Exibe a mensagem de erro caso haja algum -->
        <?php if (isset($erro)): ?>
            <div class="error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <!-- Exibe a mensagem de sucesso após o cadastro -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Formulário para adicionar usuário -->
        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="senha">Senha:</label>
            <input type="password" name="senha" required>

            <label for="tipo_usuario">Tipo de Usuário:</label>
            <select name="tipo_usuario" required>
                <option value="admin">Admin</option>
                <option value="powerbi">Power BI</option>
            </select>

            <label for="filial_id">Filial ID:</label>
            <select name="filial_id" required>
                <option value="1">Filial Palmas</option>
                <option value="2">Filial São Francisco</option>
                <option value="3">Filial Pinhão</option>
                <option value="4">Filial Guarapuava</option>
                <option value="5">Filial Francisco Beltrão</option>
                <option value="6">Filial Pato Branco</option>
            </select>

            <button type="submit">Cadastrar Usuário</button>
        </form>
    </div>

</body>
</html>
