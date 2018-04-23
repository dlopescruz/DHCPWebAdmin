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


//clicando no botao gerar
if (isset($_POST['gerar'])){

    //evita o flood de consultas
    sleep(2);
    $arquivo = "";

    //cabeçalho do arquivo, opções globais para o serviço dhcp
    $arquivo .= "##### NAO EDITE ESTE ARQUIVO NA MAO!!! ######
    ##### UTILIZE A FERRAMENTA DHCPWebAdmin, CASO CONTRARIO A BASE MYSQL FICARA INCONSISTENTE!!! ######
    authoritative;
    ddns-update-style none;
    #log-facility local7;
    local-address 200.19.219.79;
    local-port 67;
    option wpad code 252 = text;
    ######### SERVIDOR\n
    subnet 200.19.219.79 netmask 255.255.255.255 {
    }
    ";


    // tudo certo insere no banco
    $sql = "SELECT * FROM pools WHERE ativo='sim';";
    // grava no banco, se possivel, senao mostra mensagem de erro
    $resultado = mysql_query($sql) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysql_error().'</b></font>');
    

    while($row = mysql_fetch_array($resultado)){    
                    $id = $row['id'];
                    $arquivo .= "### ".$row['nome']."\n";
                    $arquivo .= "subnet ".$row['network']." netmask ".$row['mascara']." {\n";
                    $arquivo .= "   ddns-updates off;\n";
                    $arquivo .= "   default-lease-time ".$row['lease_time'].";\n";
                    $arquivo .= "   option subnet-mask ".$row['mascara'].";\n";
                    $arquivo .= "   option domain-name-servers ".$row['dns'].";\n";
                    $arquivo .= "   option broadcast-address ".$row['broadcast'].";\n";
                    //faz consulta na tabela de range_ips
                    $sql2 = "SELECT * FROM range_ips WHERE id_pool='$id';";
                    $resultado2 = mysql_query($sql2) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysql_error().'</b></font>');
                    while($row2 = mysql_fetch_array($resultado2)){ 
                        $arquivo .= "   range ".$row2['ip_inicio']." ".$row2['ip_final'].";\n";
                    }
                    $arquivo .= "   option routers ".$row['gateway'].";\n";
                    if (strlen($row['dominio']) > 3 ){
                        $arquivo .= "   option domain-name \"".$row['dominio']."\";\n";
                    }
                    if (strlen($row['wins']) > 3 ){
                        $arquivo .= "   option netbios-name-servers ".$row['wins'].";\n";
                    }
                    if (strlen($row['wpad']) > 3 ){
                        $arquivo .= "   option wpad \"".trim($row['wpad'])."\";\n";
                    }
                    $arquivo .= "}\n\n";
    }
    mysqli_close($conecta);        


    echo '<div align="center"><font color="blue"><b>Arquivo gerado com sucesso!</b></font></div>';
    $campo_visualizar = '<span class="list-group-item list-group-item-action ">dhcpd.conf<br>
    <textarea class="form-control" rows="25" disabled=disabled>'.$arquivo.'</textarea></span>';

}//final isset confirmar gerar

?>

<h2 align="center">Gerar arquivo dhcpd.conf</h2>
<br>
<form class="form-horizontal" role="form" method="post" action="gerar_arquivo.php">
<div class="panel panel-default">
    <div class="panel-body">
    <b>Atenção:</b><br>
    1. O arquivo APENAS será gerado no campo texto abaixo, e não no arquivo /etc/dhcpd/dhcpd.conf!<br>
    2. Será gerado a partir dos pools cadastrados no banco de dados Mysql com status ativo.
    
    </div>
</div>
<div>
  <?php echo $campo_visualizar;?>
</div>

    <div class="form-group" align="center">
     <button type="submit" class="btn btn-success" id="gerar" name="gerar">Gerar</button>
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