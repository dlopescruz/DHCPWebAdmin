<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <!--
    Modified from the Debian original for Ubuntu
    Last updated: 2014-03-19
    See: https://launchpad.net/bugs/1288690
  -->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>DHCPWebAdmin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
  </head>
<body>


<?php
//inclui as configuracaoes padroes
include 'settings.php';

echo $menu;


$status_servico = verifica_status();
echo $status_servico;

if (!isset($_POST['salvar'])){
    // tudo certo insere no banco
    $sql = "SELECT * FROM config_global;";
    // grava no banco, se possivel, senao mostra mensagem de erro
    $resultado = mysql_query($sql) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysql_error().'</b></font>');
    $quantidade = mysql_num_rows($resultado);
    mysqli_close($conecta);

    while($row = mysql_fetch_array($resultado)){
          $config_global = $row['config_global'];


    }
} else {
    $config_global = $_POST['config_h'];
    echo '<div align="center">
          <form name="confirma" method="post" action="config_global.php">
          <b>Salvar as informações abaixo?</b><br> Isto fará com que o serviço DHCPD seja reiniciado. Deseja continuar?
          <button type="submit" class="btn btn-success" value="sim" name="confirma">Sim</button><button type="submit" class="btn btn-danger" value="nao" name="confirma">Não</button>
          </form>
        </div>';


}

$config = '<span class="list-group-item list-group-item-action ">Cabeçalho do arquivo /etc/dhcp/dhcpd.conf. <br> <b>Não esqueça do ; ao final de cada linha!</b><br>
<textarea class="form-control" rows="20" name="config">'.$config_global.'</textarea></span>';


?>

<div align="center">
  <h2>Configuração global do dhcpd.conf</h2>
</div>
<div class="panel panel-default">
    <div class="panel-body">
    <div class="list-group">
    <?php echo $config;?>
    </div>
    </div>
</div>
<form name="salva" method="post" action="config_global.php">
<div class="form-group" align="center">
     <input type="hidden" name="config_h" value="<?php echo $config_global; ?>">
     <button type="submit" class="btn btn-success" id="salvar" name="salvar">Salvar</button>
</div>
</form>
<nav class="navbar navbar-inverse" >
  <div class="container-fluid">
    <div class="navbar-topper">
    <?php echo $rodape;?>
    </div>
  </div>
</nav>

</body>
</html>