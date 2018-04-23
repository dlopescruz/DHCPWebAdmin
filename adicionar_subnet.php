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



if (isset($_POST['adicionar'])){
  //recebe os dados dos campos do formulario
  $nome_pool = mysqli_real_escape_string($conecta,$_POST['nome_pool']);
  $subnet = mysqli_real_escape_string($conecta,$_POST['subnet']);
  $mascara = mysqli_real_escape_string($conecta,$_POST['mascara']);
  $ip_inicial = mysqli_real_escape_string($conecta,$_POST['ip_inicial']);
  $ip_final = mysqli_real_escape_string($conecta,$_POST['ip_final']);
  $gateway = mysqli_real_escape_string($conecta,$_POST['gateway']);
  $broadcast = mysqli_real_escape_string($conecta,$_POST['broadcast']);
  $dns = mysqli_real_escape_string($conecta,preg_replace('/([^\-0-9\.,])/i', '',$_POST['dns']));
  $wins = mysqli_real_escape_string($conecta,preg_replace('/([^\-0-9\.,])/i', '',$_POST['wins']));
  $lease_time = mysqli_real_escape_string($conecta,preg_replace('/([^-0-9])/i', '',$_POST['lease_time']));
  $dominio = mysqli_real_escape_string($conecta,$_POST['dominio']);
  $wpad = mysqli_real_escape_string($conecta,$_POST['wpad']);

  // trata e verifica os campos
  if (strlen($nome_pool) < 3 ){
    $erro1 = "has-error has-feedback";
    $msg_campo1 = "<b>* 3 a 20 caracteres</b>";
  }
  if (!inet_pton($subnet)){
    $erro2 = "has-error has-feedback";
    $msg_campo2 = "<b>* formato IP inválido</b>";
  }
  if (!inet_pton($mascara)){
    $erro3 = "has-error has-feedback";
    $msg_campo3 = "<b>* formato IP inválido</b>";
  }
  if (!inet_pton($ip_inicial)){
    $erro4 = "has-error has-feedback";
    $msg_campo4 = "<b>* formato IP inválido</b>";
  }
  if (!inet_pton($ip_final)){
    $erro5 = "has-error has-feedback";
    $msg_campo5 = "<b>* formato IP inválido</b>";
  }
  if (!inet_pton($gateway)){
    $erro6 = "has-error has-feedback";
    $msg_campo6 = "<b>* formato IP inválido</b>";
  }
  if (!inet_pton($broadcast)){
    $erro7 = "has-error has-feedback";
    $msg_campo7 = "<b>* formato IP inválido</b>";
  }
  if (strlen($dns) < 7 ){
    $erro8 = "has-error has-feedback";
    $msg_campo8 = "<b>* preenchimento incorreto</b>";
  }
  if ( ($lease_time == null ) || ($lease_time < -1 ) || ($lease_time > 10368000) ){
    $erro9 = "has-error has-feedback";
    $msg_campo9 = "<b>* valores entre -1 (infinito) e 10368000</b>";
  }
  if ( ( strlen($wins) > 0 ) && ( strlen($wins) < 7 )){
    $erro10 = "has-error has-feedback";
    $msg_campo10 = "<b>* preenchimento incorreto</b>";
  }
  if ( ( strlen($dominio) > 0 ) && ( strlen($dominio) < 5 )){
    $erro11 = "has-error has-feedback";
    $msg_campo11 = "<b>* minimo 5 caracteres.</b>";
  }
  if ( ( strlen($wpad) > 0 ) && ( strlen($wpad) < 5 )){
    $erro12 = "has-error has-feedback";
    $msg_campo12 = "<b>* minimo 5 caracteres.</b>";
  }
// se não tiver nenhum erro, grava no banco
if ( $erro1 != "" || $erro2 != "" || $erro3 != "" || $erro4 != "" || $erro5 != "" || $erro6 != "" || $erro7 != "" || $erro8 != "" || $erro9 != "" || $erro10 != "" || $erro11 != "" || $erro12 != ""   ){
    echo '<font color="red"><b>Erro! Corrija os campos em vermelho. </b></font>';

} else {

    // tudo certo insere no banco
    $sql = "INSERT INTO pools VALUES('0','$nome_pool','$subnet','$mascara','$broadcast','$gateway','$lease_time','$data','$wins','$dns','$wpad','$dominio','sim','','');";
    // grava no banco, se possivel, senao mostra mensagem de erro
    $resultado = mysqli_query($conecta,$sql) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysqli_error().'</b></font>');
   
    
    if ($resultado){
      // tudo certo insere no banco o range de ips do pool
      $consulta = "SELECT id, nome FROM pools WHERE nome='$nome_pool';";
      $resultado_consulta = mysqli_query($conecta,$consulta) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysqli_error().'</b></font>');
      while($row = mysqli_fetch_array($resultado_consulta)){
          $id_pool = $row['id'];
      }

      $sql_range = "INSERT INTO range_ips VALUES('0','$id_pool','$ip_inicial','$ip_final');";
      $resultado_range = mysqli_query($conecta,$sql_range) or die ('<font color="red"><b>Erro ao inserir o Range de IPs no banco: '.mysqli_error().'</b></font>');
      
      //if ($resultado_range){
        //header('location: listar_subnets.php?sucesso=1');
      //}
    }
     
     $erro = backup_e_restart();
     //em caso de erro apaga o registro recem adicionado
     if ($erro == "erro"){
        $sql_back = "DELETE FROM pools WHERE id='$id_pool'";
        $resultado_back = mysqli_query($conecta,$sql_back) or die ('<font color="red"><b>Erro ao apagar o pool do mysql: '.mysqli_error().'</b></font>');
        $sql_back_range = "DELETE FROM range_ips WHERE id_pool='$id_pool'";
        $resultado_back_range = mysqli_query($conecta,$sql_back_range) or die ('<font color="red"><b>Erro ao apagar o range do mysql: '.mysqli_error().'</b></font>');

     }
     mysqli_close($conecta);

}  
    
}


?>
<h2 align="center">Adicionar Pool:</h2>
<br>
<form class="form-horizontal" role="form" id="form_add" method="post" action="adicionar_subnet.php">
    <div class="form-group  <?php echo $erro1; ?>">
      <label class="col-sm-4 control-label">Nome do Pool:</label>
      <div class="col-sm-4">
        <input class="form-control" name="nome_pool" type="text" value="<?php echo $nome_pool; ?>"><?php echo $msg_campo1; ?> Padrão: REDE_CCTO_COMPLEMENTO Ex: REDE_112456_FLORIANOPOLIS
      </div>
    </div>
    <div class="form-group <?php echo $erro2; ?>">
      <label class="col-sm-4 control-label">Subnet:</label>
      <div class="col-sm-2">
        <input class="form-control" name="subnet" type="text" value="<?php echo $subnet; ?>"><?php echo $msg_campo2; ?> Ex: 192.168.5.0
      </div>
    </div>
    <div class="form-group <?php echo $erro3; ?>">
      <label class="col-sm-4 control-label">Máscara:</label>
      <div class="col-sm-2">
        <input class="form-control" name="mascara" type="text" value="<?php echo $mascara; ?>"><?php echo $msg_campo3; ?> Ex: 255.255.255.0
      </div>
    </div>
    <div class="form-group <?php echo $erro4; ?>"> 
      <label class="col-sm-4 control-label">IP inicial:</label>
      <div class="col-sm-2">
        <input class="form-control" name="ip_inicial" type="text" value="<?php echo $ip_inicial; ?>"><?php echo $msg_campo4; ?> Ex: 192.168.5.10
      </div>
    </div>
     <div class="form-group <?php echo $erro5; ?>"> 
      <label class="col-sm-4 control-label">IP final:</label>
      <div class="col-sm-2">
        <input class="form-control" name="ip_final" type="text" value="<?php echo $ip_final; ?>"><?php echo $msg_campo5; ?> Ex: 192.168.5.200
      </div>
    </div>
     <div class="form-group <?php echo $erro6; ?>"> 
      <label class="col-sm-4 control-label">Gateway:</label>
      <div class="col-sm-2">
        <input class="form-control" name="gateway" type="text" value="<?php echo $gateway; ?>"><?php echo $msg_campo6 ?> Ex: 192.168.5.254
      </div>
     </div>
     <div class="form-group <?php echo $erro7; ?>"> 
      <label class="col-sm-4 control-label">Broadcast:</label>
      <div class="col-sm-2">
        <input class="form-control" name="broadcast" type="text" value="<?php echo $broadcast; ?>"><?php echo $msg_campo7; ?> Ex: 192.168.5.255
      </div>
     </div>
       <div class="form-group <?php echo $erro8; ?>"> 
      <label class="col-sm-4 control-label">Servidores DNS:</label>
      <div class="col-sm-2">
        <input class="form-control" name="dns" type="text" value="<?php echo $dns; ?>"><?php echo $msg_campo8; ?> Ex: 200.19.215.1,200.19.215.2 (separe por vírgula)
      </div>
    </div>
    <div class="form-group <?php echo $erro9; ?>"> 
      <label class="col-sm-4 control-label">Default Lease Time:</label>
      <div class="col-sm-2">
        <input class="form-control" name="lease_time" type="text" value="<?php echo $lease_time; ?>"><?php echo $msg_campo9; ?> Em segundos. Ex: 3600
      </div>
    </div>
     <div class="form-group <?php echo $erro10; ?>"> 
      <label class="col-sm-4 control-label">Servidores WINS (opcional):</label> 
      <div class="col-sm-2">
        <input class="form-control" name="wins" type="text" value="<?php echo $wins; ?>"><?php echo $msg_campo10; ?> Ex: 172.19.213.1,172.19.216.1 (separe por vírgula)
      </div>
    </div>
    <div class="form-group <?php echo $erro11; ?>"> 
      <label class="col-sm-4 control-label">Domínio (opcional):</label>
      <div class="col-sm-2">
        <input class="form-control" name="dominio" type="text" value="<?php echo $dominio; ?>"><?php echo $msg_campo11; ?> Ex: sc.gov.br
      </div>
    </div>
    <div class="form-group <?php echo $erro12; ?>"> 
      <label class="col-sm-4 control-label">WPAD link (opcional):</label>
      <div class="col-sm-2">
        <input class="form-control" name="wpad" type="text" value="<?php echo $wpad; ?>"><?php echo $msg_campo12; ?> Ex: http://proxy.seudominio.com/wpad.dat\n
      </div>
    </div>
    <div class="form-group" align="center">
     <button type="submit" class="btn btn-success" id="adicionar" name="adicionar">Adicionar</button>
     <button type="button" class="btn btn-danger" id="cancelar" onclick="location.href='index.php';">Cancelar</button>
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
