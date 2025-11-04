<?php
require_once "config.php";

if (!empty($_SESSION['user_id'])) {
    header("Location: gerenciar_tarefas.php");
    exit;
}

$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $err = "Preencha e-mail e senha.";
    } else {
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $nome, $hash);
        if ($stmt->fetch()) {
            if (password_verify($senha, $hash)) {
                
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $nome;
                header("Location: gerenciar_tarefas.php");
                exit;
            } else {
                $err = "Credenciais inválidas.";
            }
        } else {
            $err = "Credenciais inválidas.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>Kanban - Login</h1>
  <nav>
    <a href="cadastro_usuario.php">Cadastrar</a>
  </nav>
</header>

<div class="container">
  <h2>Entrar</h2>
  <?php if ($err) echo "<p class='error'>{$err}</p>"; ?>

  <form method="post">
    <label>E-mail</label>
    <input type="email" name="email" required>
    <label>Senha</label>
    <input type="password" name="senha" required>
    <button type="submit">Entrar</button>
  </form>
</div>
</body>
</html>

