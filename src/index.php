<!DOCTYPE html>
<html lang="en">
<head>
  <title>Alterar Senha</title>
  <link rel="stylesheet" href="../media/styles/style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<script>
  function alerta(){
    alert("ATENÇÃO\nEsse formulário irá alterar a senha de acesso dos seguintes sistemas:\n•SEI\n•Pandion\n•EAD\n•Logon do windows (apenas secretaria)\n•E-mail\n•Navegar na internet (Micros com SIS)\n•Outros sistemas como: Infodip");
  }
</script>
<body>
  <div class="msg">
    <?php 
      if(!empty($_GET)){
        $msg = $_GET['msg'];
        $id = $_GET['id'];
        switch($id){
          case 0:
            echo "<div class=\"alert alert-info\" role=\"alert\">$msg</div>";
            break;
          case 1:
            echo "<div class=\"alert alert-danger\" role=\"alert\">$msg</div>";
            break;
          case 2:
            echo "<div class=\"alert alert-success\" role=\"alert\">$msg</div>";
            break;
        }
      }
      ?>
  </div>
  <br>
  <div id="container" class="card shadow-2-strong card-body p-5 text-center">
    <h2>Alterar Senha</h2>
    <ul></ul>
    <form action="chpasswd.php" name="passwordChange" method="post">
      <table style="width: 400px; margin: 0 auto;">
        <tr><th>Nome de usuário:</th><td><input name="username" id="username" type="text" size="20" autocomplete="off" /></td></tr>
        <tr><th>Senha antiga:</th><td><input name="oldPassword" id="oldPassword" size="20" type="password" /></td></tr>
        <tr><th>Nova senha:</th><td><input name="newPassword" id="newPassword" size="20" type="password" /></td></tr>
        <tr><th>Confirmar nova senha:</th><td><input name="newPasswordConf" id="newPasswordConf" size="20" type="password" /></td></tr>
        <tr><td colspan="2" id="botao" >
        <button type="submit" onclick="alerta()" class="btn btn-primary" id="botao" data-toggle="modal" data-target="#exampleModalCenter">Alterar Senha</button>
      </table>
    </form>
    <br>
  </div>
  <div class="alert alert-primary" role="alert">
    <b>Atenção</b>
    <br/>
    A nova senha deve:
    <ul>
      <li>Ter no mínimo, 8 (oito) caracteres;</li>
      <li>Ser diferente das 3 (três) últimas senhas que foram utilizadas.</li>
    </ul>
  </div>
</body>
</html>