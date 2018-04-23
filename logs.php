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


$log = shell_exec('cat /var/log/syslog | grep dhcpd | grep -v DHCP | tail -500');

if (isset($_POST['filtrar']) AND (strlen($_POST['campo_consulta']) > 4) ){
    $consulta=$_POST['campo_consulta'];
    $log_leases = shell_exec('cat /var/log/syslog | grep dhcpd | grep '.$consulta.' | tail -1000 ');

} else{
    $log_leases = shell_exec('cat /var/log/syslog | grep dhcpd | tail -6000');
}
     
$status = '<span class="list-group-item list-group-item-action ">Log do serviço dhcpd em /var/log/syslog:<br>
<textarea class="form-control" rows="20" disabled=disabled >'.$log.'</textarea></span>';


$status_leases = '
<div>Log de leases dhcpd em /var/log/syslog:</div>
<span class="list-group-item list-group-item-action " align="left">
<form name="filtrar" role="form" action="logs.php" method="POST">
    <div class="form-group  '.$erro.'">
    <div class="col-sm-4">
        <label class="control-label">Pesquisar no log de Leases:</label>
        <input class="form-control" name="campo_consulta" type="text" value="'.$consulta.'"> IP, MAC ou HOSTNAME (case sensitive)
        <button type="submit" class="btn btn-success" id="filtrar" name="filtrar">Filtrar</button>
      </div>
    </div>
</form><br>
<textarea class="form-control" rows="15" disabled=disabled>'.$log_leases.'</textarea></span>
';




?>
<div align="center">
  <h2>LOGs do Serviço</h2>
</div>
<div class="panel panel-default">
    <div class="panel-body">
    <div class="list-group">
    <?php echo $status_leases;?>
    <?php echo $status;?>
    </div>
    </div>
</div>

<nav class="navbar navbar-inverse" >
  <div class="container-fluid">
    <div class="navbar-topper">
      <?php echo $rodape;?>
    </div>
  </div>
</nav>
</body>
</hmtl>
