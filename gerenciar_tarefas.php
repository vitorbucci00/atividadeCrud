<?php include("config.php"); ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gerenciar Tarefas</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>Kanban - Gerenciamento de Tarefas</h1>
  <nav>
    <a href="cadastro_usuario.php">Cadastrar Usuário</a>
    <a href="cadastro_tarefa.php">Cadastrar Tarefa</a>
    <a href="gerenciar_tarefas.php">Gerenciar Tarefas</a>
  </nav>
</header>

<div class="kanban">
  <?php
  $colunas = ["A Fazer", "Fazendo", "Pronto"];
  foreach ($colunas as $status) {
      echo "<div class='coluna'><h2>$status</h2>";

      $result = $conn->query("SELECT t.*, u.nome FROM tarefas t 
                              JOIN usuarios u ON t.id_usuario = u.id 
                              WHERE t.status='$status'");
      while ($row = $result->fetch_assoc()) {
          echo "<div class='tarefa'>
                <p><b>{$row['descricao']}</b></p>
                <p>Setor: {$row['setor']}</p>
                <p>Prioridade: {$row['prioridade']}</p>
                <p>Usuário: {$row['nome']}</p>
                <form method='post' class='inline'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <select name='status'>
                        <option>A Fazer</option>
                        <option>Fazendo</option>
                        <option>Pronto</option>
                    </select>
                    <button type='submit' name='atualizar'>Atualizar</button>
                </form>
                <form method='post' class='inline'>
                    <input type='hidden' name='id' value='{$row['id']}'>
                    <button type='submit' name='excluir' onclick='return confirm(\"Deseja excluir?\")'>Excluir</button>
                </form>
                </div>";
      }

      echo "</div>";
  }

  // Atualizar status
  if (isset($_POST['atualizar'])) {
      $id = $_POST['id'];
      $status = $_POST['status'];
      $conn->query("UPDATE tarefas SET status='$status' WHERE id=$id");
      header("Location: gerenciar_tarefas.php");
  }

  // Excluir tarefa
  if (isset($_POST['excluir'])) {
      $id = $_POST['id'];
      $conn->query("DELETE FROM tarefas WHERE id=$id");
      header("Location: gerenciar_tarefas.php");
  }
  ?>
</div>
</body>
</html>
