<?php

  const dn = "DC=tre-ms,DC=jus,DC=br";
  const domain ="tre-ms.jus.br";
  const server = "ldaps://msdc10.tre-ms.jus.br";

  $username = $_POST['username'];
  $oldPassword = $_POST['oldPassword'];
  $newPassword = $_POST['newPassword'];
  $newPasswordConf = $_POST['newPasswordConf'];

  verify($username, $oldPassword, $newPassword, $newPasswordConf);

  function verify($username, $oldPassword, $newPassword, $newPasswordConf){

    if($username=="" or $oldPassword=="" or $newPassword=="" or $newPasswordConf==""){
      $msg = "Por favor, preencha todos os campos.";
      $error = http_build_query(array('id'=>0, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else if(strlen($newPassword) < 8){
      $msg = "A nova senha deve ter no minimo 8 digitos.";
      $error = http_build_query(array('id'=>0, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else if($newPassword != $newPasswordConf){
      $msg = "Senhas não compativeis.";
      $error = http_build_query(array('id'=>0, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else{
    changePassword($username, $oldPassword, $newPassword);
    }

  }

  function conecction (){
    $con = ldap_connect(server);

    if(!$con){
      $msg = returnMessage(1);
      $error = http_build_query(array('id'=>1, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else{
      return $con;
    }

  }

  function bind($con, $domain, $oldPassword, $username){

    if (!@ldap_bind($con, $username."@".$domain, $oldPassword)){
      $log = logs($username, ldap_error($con));
      $msg = returnMessage(2);
      $error = http_build_query(array('id'=>1, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else{
      return 0;
    }

  }

  function searchDNByUsername($con, $dn ,$username){
    $busca = ldap_search($con, dn, "(userPrincipalName=$username@".domain.")");
    $resultado = ldap_first_entry($con, $busca);

    if(!$dn = ldap_get_dn($con, $resultado)){
      $log = logs($username, ldap_error($con));
      $msg = returnMessage(0);
      $error = http_build_query(array('id'=>1, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else{
      return $dn;
    }

  }

  function changePassword($username, $oldPassword, $newPassword){
    $con = conecction();

    ldap_set_option($con, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
    ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($con, LDAP_OPT_REFERRALS, 0);

    $bind = bind($con, domain, $oldPassword, $username);

    $dnUser = searchDNByUsername($con, dn, $username);

    $replace = [
      [
        "attrib" => "unicodePwd",
        "modtype" => LDAP_MODIFY_BATCH_REMOVE,
        "values" => [iconv("UTF-8", "UTF-16LE", '"' . $oldPassword . '"')],
      ],
      [
        "attrib" => "unicodePwd",
        "modtype" => LDAP_MODIFY_BATCH_ADD,
        "values" => [iconv("UTF-8", "UTF-16LE", '"' . $newPassword . '"')],
      ],
    ];
    
    if(ldap_modify_batch($con, $dnUser, $replace)){
      $log = logs($username, ldap_error($con));
      $msg = returnMessage(5);
      $error = http_build_query(array('id'=>2, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }else{
      $log = logs($username, ldap_error($con));
      $msg = returnMessage(4);
      $error = http_build_query(array('id'=>1, 'msg'=>$msg));
      header('Location:index.php?'.$error);
    }
  }

  function returnMessage($id){

    switch($id){
      case 0:
        $msg = "Um erro inesperado ocorreu. Por favor, entre em contato com a SGI.";
        break;
      case 1:
        $msg = "Não foi possível estabelecer a conexão. Por favor, entre em contato com a SGI.";
        break;
      case 2:
        $msg = "Nome de usuário ou senha incorretos. Verifique os dados e tente novamente.";
        break;
      case 4:
        $msg = "Não foi possível alterar a senha. Por favor, entre em contato com a SGI.";
        break;
      case 5:
        $msg = "Senha alterada com sucesso!";
        break;
    }
    return $msg;

  }

  function logs($username, $error){
    date_default_timezone_set('America/Sao_Paulo');
    $date = date('Y-m-d H:i:s');

    $ip = $_SERVER['REMOTE_ADDR'];

    $arquivo = "logChpasswd.txt";
    $mensagem = "$date: $ip: $username: $error\n";

    /*$open = fopen($arquivo, 'a');
    fwrite($open, $mensagem);
    fclose($open);*/

    file_put_contents("php://stderr", "$mensagem\n");

    return 0;
  }
?>
