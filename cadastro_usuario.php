<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Usuários</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>Kanban - Cadastro de Usuários</h1>
  <nav>
    <a href="cadastro_usuario.php">Cadastrar Usuário</a>
    <a href="cadastro_tarefa.php">Cadastrar Tarefa</a>
    <a href="gerenciar_tarefas.php">Gerenciar Tarefas</a>
  </nav>
</header>

<div class="container">
  <h2>Novo Usuário</h2>
  <form method="post">
    <label>Nome:</label>
    <input type="text" name="nome" required>

    <label>E-mail:</label>
    <input type="email" name="email" required>

    <button type="submit" name="salvar">Cadastrar</button>
  </form>

  <?php
  if (isset($_POST['salvar'])) {
      $nome = $_POST['nome'];
      $email = $_POST['email'];

      $sql = "INSERT INTO usuarios (nome, email) VALUES ('$nome', '$email')";
      if ($conn->query($sql) === TRUE) {
          echo "<p class='success'>Cadastro concluído com sucesso!</p>";
      } else {
          echo "<p class='error'>Erro: " . $conn->error . "</p>";
      }
  }
  ?>
</div>
</body>
</html>
