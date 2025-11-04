<?php
require_once "config.php";

if (!empty($_SESSION['user_id'])) {
    header("Location: gerenciar_tarefas.php");
    exit;
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if ($nome === '' || $email === '' || $senha === '' || $senha2 === '') {
        $errors[] = "Todos os campos são obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail inválido.";
    } elseif ($senha !== $senha2) {
        $errors[] = "As senhas não coincidem.";
    } else {
        // verifica email único
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "E-mail já cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $nome, $email, $hash);
            if ($ins->execute()) {
                $success = "Cadastro concluído com sucesso. Faça login.";
            } else {
                $errors[] = "Erro ao salvar: " . $conn->error;
            }
            $ins->close();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Cadastro de Usuário</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>Kanban - Cadastro</h1>
  <nav>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="container">
  <h2>Cadastrar novo usuário</h2>

  <?php foreach($errors as $e) echo "<p class='error'>{$e}</p>"; ?>
  <?php if ($success) echo "<p class='success'>{$success}</p>"; ?>

  <form method="post">
    <label>Nome</label>
    <input type="text" name="nome" required>

    <label>E-mail</label>
    <input type="email" name="email" required>

    <label>Senha</label>
    <input type="password" name="senha" required>

    <label>Confirmar senha</label>
    <input type="password" name="senha2" required>

    <button type="submit">Cadastrar</button>
  </form>
</div>
</body>
</html>
