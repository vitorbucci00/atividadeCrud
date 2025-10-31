<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Tarefas</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>Kanban - Cadastro de Tarefas</h1>
  <nav>
    <a href="cadastro_usuario.php">Cadastrar Usuário</a>
    <a href="cadastro_tarefa.php">Cadastrar Tarefa</a>
    <a href="gerenciar_tarefas.php">Gerenciar Tarefas</a>
  </nav>
</header>

<div class="container">
  <h2>Nova Tarefa</h2>
  <form method="post">
    <label>Usuário:</label>
    <select name="id_usuario" required>
      <option value="">Selecione</option>
      <?php
      $usuarios = $conn->query("SELECT * FROM usuarios");
      while ($u = $usuarios->fetch_assoc()) {
          echo "<option value='{$u['id']}'>{$u['nome']} ({$u['email']})</option>";
      }
      ?>
    </select>

    <label>Descrição:</label>
    <textarea name="descricao" required></textarea>

    <label>Setor:</label>
    <input type="text" name="setor" required>

    <label>Prioridade:</label>
    <select name="prioridade" required>
      <option>Baixa</option>
      <option>Média</option>
      <option>Alta</option>
    </select>

    <button type="submit" name="salvar">Cadastrar Tarefa</button>
  </form>

  <?php
  if (isset($_POST['salvar'])) {
      $id_usuario = $_POST['id_usuario'];
      $descricao = $_POST['descricao'];
      $setor = $_POST['setor'];
      $prioridade = $_POST['prioridade'];

      $sql = "INSERT INTO tarefas (id_usuario, descricao, setor, prioridade) 
              VALUES ('$id_usuario', '$descricao', '$setor', '$prioridade')";
      if ($conn->query($sql) === TRUE) {
          echo "<p class='success'>Tarefa cadastrada com sucesso!</p>";
      } else {
          echo "<p class='error'>Erro: " . $conn->error . "</p>";
      }
  }
  ?>
</div>
</body>
</html>
