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

if (isset($_GET['sucesso'])){
  if ($_GET['sucesso'] == 1 ){
    echo '<div align="center"><font color="blue"><b>Dados gravados com sucesso!</b></font><br>';
    echo '<font color="green">O serviço foi restartado com sucesso!</font><br></div>';
  }
}



//se foi confimado sim ou nao para apagar o registro no banco
if (isset($_POST['confirma'])){
    $nome_pool = $_POST['nome'];
    $confimacao = $_POST['confirma'];
    if ($confimacao == "sim"){
      $consulta = "SELECT id, nome FROM pools WHERE nome='$nome_pool'";
      $resultado= mysqli_query($conecta,$consulta) or die ('<font color="red"><b>Erro ao consultar o pool no banco: '.mysqli_error().'</b></font>');
      while($row = mysqli_fetch_array($resultado)){
        $id_pool = $row['id'];
      }
      $apaga_pool = "DELETE FROM pools WHERE nome='$nome_pool';";
      $resultado_pool = mysqli_query($conecta,$apaga_pool) or die ('<font color="red"><b>Erro ao apagar o pool no banco: '.mysqli_error().'</b></font>');
      $apaga_range = "DELETE FROM range_ips WHERE id_pool='$id_pool';";
      $resultado_range = mysqli_query($conecta,$apaga_range) or die ('<font color="red"><b>Erro ao apagar o range no banco: '.mysqli_error().'</b></font>');
      
      mysqli_close($conecta);
      backup_e_restart();
    }
  }

//se for clicado no botao apagar, pede a confirmação
if (isset($_POST['apagar'])){
  $nome_pool = $_POST['apagar'];
  echo '<div align="center">
      <form name="confirma" method="post" action="listar_subnets.php">
      Apagar realmente o pool '.$nome_pool.'?
      <input type="hidden" name="nome" value="'.$nome_pool.'">
      <button type="submit" class="btn btn-success" value="sim" name="confirma">Sim</button><button type="submit" class="btn btn-danger" value="nao" name="confirma">Não</button>
      </form>
  </div>';
}

//se for clicado no botao editar, pede a confirmação
if (isset($_POST['editar'])){
  $id = $_POST['editar'];
  header('Location: editar_subnet.php?id='.$id);

}

// tudo certo insere no banco
$sql = "SELECT id, ativo, nome, network, mascara FROM pools ORDER BY nome;";
// grava no banco, se possivel, senao mostra mensagem de erro
$resultado = mysqli_query($conecta,$sql) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysqli_error().'</b></font>');
$quantidade = mysqli_num_rows($resultado);
mysqli_close($conecta);
echo '<h2 align="center">Pools:</h2>
      <div class="panel panel-default">
        <div class="panel-body">
        <b>Quantidade de Pools: '.$quantidade.' </b> 
        </div>
      </div>';


     echo '
     <form name="form1" method="post" action="listar_subnets.php">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Nome do Pool</th>
            <th>Subnet</th>
            <th>Máscara</th>
            <th>Ativo</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>';

while($row = mysqli_fetch_array($resultado)){
      $id = $row['id'];
      $nome_pool = $row['nome'];
      $subnet = $row['network'];
      $mascara = $row['mascara'];
      $ativo = $row['ativo'];
      echo '
          <tr>
            <td>'.$nome_pool.'</td>
            <td>'.$subnet.'</td>
            <td>'.$mascara.'</td>
            <td>'.$ativo.'</td>
            <td><button type="submit" class="btn btn-primary" value="'.$id.'" name="editar">Editar</button><button type="submit" class="btn btn-danger" value="'.$nome_pool.'" name="apagar">Apagar</button></td>
          ';
} //final while

echo   '</tbody>
      </table>
      </form>
      ';


?>

<nav class="navbar navbar-inverse" >
  <div class="container-fluid">
    <div class="navbar-topper">
      <?php echo $rodape;?>
    </div>
  </div>
</nav>
</body>
</html>
