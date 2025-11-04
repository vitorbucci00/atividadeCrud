<?php
require_once "config.php";


if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

$id_editar = $_GET['id'] ?? null;
$editing = false;
$descricao = "";
$setor = "";
$prioridade = "Baixa";
$status = "A Fazer";


if ($id_editar) {
    $stmt = $conn->prepare("SELECT descricao, setor, prioridade, status FROM tarefas WHERE id = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_editar, $user_id);
    $stmt->execute();
    $stmt->bind_result($d, $s, $p, $st);
    if ($stmt->fetch()) {
        $editing = true;
        $descricao = $d;
        $setor = $s;
        $prioridade = $p;
        $status = $st;
    } else {
        $errors[] = "Tarefa não encontrada ou sem permissão.";
    }
    $stmt->close();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['descricao'] ?? '');
    $setor = trim($_POST['setor'] ?? '');
    $prioridade = $_POST['prioridade'] ?? 'Baixa';
    $status = $_POST['status'] ?? 'A Fazer';
    $id_form = $_POST['id'] ?? null;

    if ($descricao === '' || $setor === '' || $prioridade === '') {
        $errors[] = "Preencha todos os campos obrigatórios.";
    } else {
        if ($id_form) {
            
            $upd = $conn->prepare("UPDATE tarefas SET descricao=?, setor=?, prioridade=?, status=? WHERE id=? AND id_usuario=?");
            $upd->bind_param("ssssii", $descricao, $setor, $prioridade, $status, $id_form, $user_id);
            if ($upd->execute()) {
                $success = "Tarefa atualizada com sucesso.";
                header("Location: gerenciar_tarefas.php");
                exit;
            } else {
                $errors[] = "Erro ao atualizar: " . $conn->error;
            }
            $upd->close();
        } else {
            
            $ins = $conn->prepare("INSERT INTO tarefas (id_usuario, descricao, setor, prioridade, status) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("issss", $user_id, $descricao, $setor, $prioridade, $status);
            if ($ins->execute()) {
                $success = "Tarefa criada com sucesso.";
                header("Location: gerenciar_tarefas.php");
                exit;
            } else {
                $errors[] = "Erro ao salvar: " . $conn->error;
            }
            $ins->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Cadastro Tarefa</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="topbar">
    <div><h1>Kanban - Nova Tarefa</h1><div class="small">Usuário: <?php echo htmlspecialchars($_SESSION['user_name']); ?></div></div>
    <div>
      <a href="gerenciar_tarefas.php" style="color:white; text-decoration:none; background:#28a745; padding:8px; border-radius:5px;">Voltar</a>
      <a href="logout.php" style="color:white; text-decoration:none; background:#dc3545; padding:8px; border-radius:5px; margin-left:8px;">Logout</a>
    </div>
  </div>
</header>

<div class="container">
  <h2><?php echo $editing ? "Editar Tarefa" : "Nova Tarefa"; ?></h2>
  <?php foreach($errors as $e) echo "<p class='error'>{$e}</p>"; ?>
  <?php if ($success) echo "<p class='success'>{$success}</p>"; ?>

  <form method="post">
    <input type="hidden" name="id" value="<?php echo $editing ? htmlspecialchars($id_editar) : ''; ?>">

    <label>Descrição</label>
    <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($descricao); ?></textarea>
    <button type="button" id="sugerirBtn" style="width:auto; margin-top:8px;">Sugerir descrição (API)</button>

    <label>Setor</label>
    <input type="text" name="setor" value="<?php echo htmlspecialchars($setor); ?>" required>

    <label>Prioridade</label>
    <select name="prioridade" required>
      <option <?php if($prioridade=='Baixa') echo 'selected'; ?>>Baixa</option>
      <option <?php if($prioridade=='Média') echo 'selected'; ?>>Média</option>
      <option <?php if($prioridade=='Alta') echo 'selected'; ?>>Alta</option>
    </select>

    <label>Status</label>
    <select name="status" required>
      <option <?php if($status=='A Fazer') echo 'selected'; ?>>A Fazer</option>
      <option <?php if($status=='Fazendo') echo 'selected'; ?>>Fazendo</option>
      <option <?php if($status=='Pronto') echo 'selected'; ?>>Pronto</option>
    </select>

    <button type="submit"><?php echo $editing ? "Atualizar" : "Cadastrar"; ?></button>
  </form>
</div>

<script>
document.getElementById('sugerirBtn').addEventListener('click', function() {
  this.disabled = true;
  this.textContent = 'Buscando...';
  fetch('https://www.boredapi.com/api/activity')
    .then(resp => resp.json())
    .then(data => {
      document.getElementById('descricao').value = data.activity || '';
      document.getElementById('sugerirBtn').disabled = false;
      document.getElementById('sugerirBtn').textContent = 'Sugerir descrição (API)';
    })
    .catch(err => {
      alert('Erro ao buscar sugestão: ' + err);
      this.disabled = false;
      this.textContent = 'Sugerir descrição (API)';
    });
});
</script>
</body>
</html>
