<?php
require_once "config.php";
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['atualizar'])) {
        $id = intval($_POST['id']);
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE tarefas SET status = ? WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("sii", $status, $id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: gerenciar_tarefas.php");
        exit;
    } elseif (isset($_POST['excluir'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: gerenciar_tarefas.php");
        exit;
    }
}

$colunas = ["A Fazer", "Fazendo", "Pronto"];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Gerenciar Tarefas</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="topbar">
    <div><h1>Kanban - Minhas Tarefas</h1><div class="small">Usuário: <?php echo htmlspecialchars($_SESSION['user_name']); ?></div></div>
    <div>
      <a href="cadastro_tarefa.php" style="color:white; text-decoration:none; background:#28a745; padding:8px; border-radius:5px;">Nova Tarefa</a>
      <a href="logout.php" style="color:white; text-decoration:none; background:#dc3545; padding:8px; border-radius:5px; margin-left:8px;">Logout</a>
    </div>
  </div>
</header>

<div class="kanban">
<?php foreach ($colunas as $status) : ?>
  <div class="coluna">
    <h2><?php echo $status; ?></h2>
    <?php
      $stmt = $conn->prepare("SELECT id, descricao, setor, prioridade, data_cadastro FROM tarefas WHERE id_usuario = ? AND status = ? ORDER BY data_cadastro DESC");
      $stmt->bind_param("is", $user_id, $status);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($row = $res->fetch_assoc()) {
        echo "<div class='tarefa'>";
        echo "<p><b>".htmlspecialchars($row['descricao'])."</b></p>";
        echo "<p>Setor: ".htmlspecialchars($row['setor'])."</p>";
        echo "<p>Prioridade: ".htmlspecialchars($row['prioridade'])."</p>";
        echo "<p class='small'>Criado: ".htmlspecialchars($row['data_cadastro'])."</p>";
        
        echo "<form method='post' style='display:flex; gap:5px; margin-top:8px;'>";
        echo "<input type='hidden' name='id' value='".intval($row['id'])."'>";
        echo "<select name='status'>";
        foreach ($colunas as $opt) {
          $sel = ($opt === $status) ? "selected" : "";
          echo "<option $sel>$opt</option>";
        }
        echo "</select>";
        echo "<button type='submit' name='atualizar'>Atualizar</button>";
        echo "</form>";

        
        echo "<div style='margin-top:8px; display:flex; gap:8px;'>";
        echo "<a href='cadastro_tarefa.php?id=".intval($row['id'])."' style='background:#ffc107; padding:6px 8px; border-radius:5px; color:#000; text-decoration:none;'>Editar</a>";
        echo "<form method='post' onsubmit='return confirm(\"Deseja excluir?\")' style='display:inline;'>";
        echo "<input type='hidden' name='id' value='".intval($row['id'])."'>";
        echo "<button type='submit' name='excluir' style='background:#dc3545; color:white; border:none; padding:6px 8px; border-radius:5px;'>Excluir</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>";
      }
      $stmt->close();
    ?>
  </div>
<?php endforeach; ?>
</div>
</body>
</html>
