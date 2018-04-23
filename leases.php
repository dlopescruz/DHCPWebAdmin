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

if (isset($_POST['pesquisar'])){
  $ip = $_POST['ip'];

  if (strlen($ip) < 5){
    $erro1 = "has-error has-feedback";
    $msg_campo = "<b>* Pelo menos 5 caracteres...</b>";
  } 

}// final if isset POST pesquisar

if (isset($_POST['apagar'])){
  $ip = trim($_POST['apagar']);

  if (inet_pton($ip)){
  echo '<div align="center">
          <form name="confirma" method="post" action="leases.php">
          <b>Apagar realmente todos os leases do IP: <font color="red">'.$ip.'</font>?</b><br> Isto fará com que o serviço DHCPD seja reiniciado. Deseja continuar?
          <input type="hidden" name="ip" value="'.$ip.'">
          <button type="submit" class="btn btn-success" value="sim" name="confirma">Sim</button><button type="submit" class="btn btn-danger" value="nao" name="confirma">Não</button>
          </form>
        </div>';

    
  }
}//final post apagar

if (isset($_POST['confirma'])){
    if ($_POST['confirma'] == "sim"){
      $ip = $_POST['ip'];
      $count = 0;
      $arquivo_novo = "";
      //echo $ip.'bla';

      //$arquivo = file_get_contents("dhcpd.leases");
        //$arquivo = shell_exec('sudo cat /var/lib/dhcpd/dhcpd.leases | grep -A 9 "'.$ip.' "');
        $arquivo = shell_exec('sudo cat /var/lib/dhcpd/dhcpd.leases | grep -v "#"');
        $blocos         = explode("}\n", $arquivo);
        $linhas         = [];
            

            foreach($blocos as $id_pool => $pool){
                $linhas_do_bloco  = array_values(array_filter(explode("\n", $pool))); // remove linhas em branco e reordena keys
                $linhas[$id_pool] = $linhas_do_bloco;
                //$cont=1;
                $lease = "";
                foreach($linhas_do_bloco as $id_linha => $linha_do_pool){
                       
                                    
                            if (preg_match('/^\s*lease/i', $linha_do_pool)){
                                $lease = trim(explode('{',substr($linha_do_pool, 6))[0]);
                               
                            } 
                            
                          
                            if (preg_match('/^\s*starts/i', $linha_do_pool)){
                                $starts = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                $starts .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];
                                        
                            }                            
                        
                            if (preg_match('/^\s*ends/i', $linha_do_pool)){
                                $ends = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                $ends .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];

                            }
                            if (preg_match('/^\s*client-hostname/i', $linha_do_pool)){
                                $hostname2 = explode(';',explode(' ',$linha_do_pool)[3])[0];
                                        
                            } else {
                              $hostname2 = "";
                            }
                            if (preg_match('/^\s*uid/i', $linha_do_pool)){
                                $uid = explode('"',$linha_do_pool)[1];
                                        
                            }
                            if (preg_match('/^\s*hardware/i', $linha_do_pool)){
                                $mac = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                        
                            } 
                            
                            /*if (!preg_match('/^\s*lease/i', $linha_do_pool) AND $id_linha == 0){
                                //$uid .= $linha_do_pool;
                                //$linha_do_pool = $uid;
                                echo $linha_do_pool."<br>";
                               
                            }*/    
                          

                            /// escreve na variavel que vai escrever no arquivo novo
                            if ($lease != $ip){
                                  //verifica se e a primeira linha de cada bloco para colocar o }           
                                  if ($id_linha == 0 AND $id_pool != 0){
                                      $count++;
                                      $arquivo_novo .= "}\n";
                                  }
                                  $arquivo_novo .= $linha_do_pool."\n";

                            } 
                            
                                               
                }

                
               

         }
        //fecha o arquivo com }       
        $arquivo_novo .= "}\n";
        //para o servico dhcpd
        shell_exec('sudo service dhcpd stop');
        // grava num arquivo temporario e depois move ppro arquivo original
        shell_exec('sudo cp /var/lib/dhcpd/dhcpd.leases /var/lib/dhcpd/dhcpd.leases.bkp-`date +%Y-%m-%d:%H:%M:%S`');
        $nome_arquivo = '/var/www/html/dhcpwebadmin/dhcpd.leases';
        unlink($nome_arquivo);
        $file = fopen($nome_arquivo, 'a');
        fwrite($file, $arquivo_novo);
        fclose($file);
        shell_exec('sudo mv /var/www/html/dhcpwebadmin/dhcpd.leases /var/lib/dhcpd/');
        shell_exec('sudo service dhcpd start');
        echo '<div align="center"><b><font color="blue"> Lease removido e serviço reiniciado! </font></b></div>';

    }//final if post confirma == sim
    else {
      $ip = $_POST['ip'];
    }


}//final isset post confirma


if (isset($_POST['nome_pool'])){
  $id=$_POST['nome_pool'];
  if ($id != "outro" ){
      $sql_id = "SELECT id, nome, network, mascara, broadcast FROM pools WHERE id='$id';";
      // faz a consulta
      $resultado_id = mysql_query($sql_id) or die ('<font color="red"><b>Erro ao consultar o banco: '.mysql_error().'</b></font>');
       while ($row = mysql_fetch_array($resultado_id)){
          $nome = $row['nome'];
          $network = $row['network'];
          $mascara = $row['mascara'];
          $broadcast = $row['broadcast'];   
      }//final while
      $range = calcIPrange($network,$mascara);
      $ip_inicio =  $range['start_ip'];
      $ip_final = $range['end_ip'];


  } else{
  header('Location: leases.php');
  }//final if !$id == "outro" ){

}


?>

<div align="center">
  <h2>Lista de leases do arquivo /var/lib/dhcpd/dhcpd.leases</h2>
</div> 


<div class="panel panel-default">
    <div class="panel-body" align="center">
    <b>Lista por Pool:</b><br><br><br>
    <form class="form-horizontal" role="form" name="form_pools" action="leases.php" method="post">
    <div class="form-group <?php echo $erro; ?>">
      <label class="col-sm-4 control-label">Pool:</label>
      <div class="col-sm-4">
        <select ng-model="" class="form-control" name="nome_pool">
          <option value="outro">Selecione</option>
<?php

if (!isset($_POST['nome_pool'])){
  // carrega comando sql
  $sql = "SELECT id, nome FROM pools WHERE ativo='sim' ORDER BY nome;";
  // faz a consulta
  $resultado = mysql_query($sql) or die ('<font color="red"><b>Erro ao consultar o banco: '.mysql_error().'</b></font>');

  while ($row = mysql_fetch_array($resultado)){
      $id = $row['id'];
      $nome = $row['nome'];
      echo '<option value="'.$id.'">'.$nome.'</option>';
  }//final while 
} else{
   echo '<option value="'.$id.'" selected>'.$nome.'</option>';
   echo '<option value="outro">Escolher outro...</option>';
 }// final if !isset

?>

      </select>
      <br>
      <button type="submit" class="btn btn-success" id="selecionar" name="selecionar"> Selecionar</button>
      <br>
      <br>

<?php


if (isset($_POST['nome_pool']) AND !isset($_POST['apagar'])){
    echo "<b>Network:</b> ".$network."    <b>Mascara:</b>".$mascara."    <b>Broadcast: </b>".$broadcast."<br>";
    echo "<b>Range de IPs:</b> ".$ip_inicio."    <b>até:</b>".$ip_final."<br>";
    $pre_inicio = explode(".",$network);
    $pre_final = explode(".",$broadcast);
    $prefixo_inicio = $pre_inicio[0].".".$pre_inicio[1].".".$pre_inicio[2].".";
    $prefixo_final = $pre_final[0].".".$pre_final[1].".".$pre_final[2].".";
    $contador_inicio = $pre_inicio[2];
    $contador_final = $pre_final[2];
    //echo "bla";

     echo '
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>IP</th>
            <th>Starts</th>
            <th>Ends</th>
            <th>Hostname</th>
            <th>MAC</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>';


//$arquivo = file_get_contents("dhcpd.leases");
$arquivo = shell_exec('sudo cat /var/lib/dhcpd/dhcpd.leases | grep -A 9 "'.$ip.' "');
$total = 0;

// mostar todo os leases dentro dos ranges da subrede
for ($contador = $pre_inicio[2]; $contador <= $pre_final[2]; $contador++){

    $prefixo_atual =  $pre_inicio[0].".".$pre_inicio[1].".".$contador.".";

    $blocos         = explode("}\n", $arquivo);
    $linhas         = [];
    

    foreach($blocos as $id_pool => $pool){
        $linhas_do_bloco  = array_values(array_filter(explode("\n", $pool))); // remove linhas em branco e reordena keys
        $linhas[$id_pool] = $linhas_do_bloco;
        //$cont=1;
        $lease = "";
        foreach($linhas_do_bloco as $id_linha => $linha_do_pool){
               
                            
                    if (preg_match('/^\s*lease/i', $linha_do_pool)){
                        $lease = trim(explode('{',substr($linha_do_pool, 6))[0]);
                    
                    }
                    if (preg_match('/^\s*starts/i', $linha_do_pool)){
                        $starts = explode(';',explode(' ',$linha_do_pool)[4])[0];
                        $starts .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];
                                
                    }
                    if (preg_match('/^\s*ends/i', $linha_do_pool)){
                        $ends = explode(';',explode(' ',$linha_do_pool)[4])[0];
                        $ends .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];
                                
                    }
                    if (preg_match('/^\s*client-hostname/i', $linha_do_pool)){
                        $hostname2 = explode(';',explode(' ',$linha_do_pool)[3])[0];
                                
                    } else {
                        $hostname2 = "";
                    }
                    if (preg_match('/^\s*hardware/i', $linha_do_pool)){
                        $mac = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                
                    }

                    
                
        }

        $pre_lease = explode(".", $lease);
        $prefixo_lease = $pre_lease[0].".".$pre_lease[1].".".$pre_lease[2].".";


        /// aqui faz a comparacao de IP do leases com os IPs do POOL
        if ($prefixo_lease == $prefixo_atual){           
                          
                          echo '<tr>
                          <td>'.$id_pool.'</td>
                          <td>'.$lease.'</td>
                          <td>'.$starts.'</td>
                          <td>'.$ends.'</td>
                          <td>'.$hostname2.'</td>
                          <td>'.$mac.'</td>
                          <td><button type="submit" class="btn btn-danger" value="'.$lease.'" name="apagar">Apagar</button></td>
                          ';
                          $total++;

       }
       

 }
       
}

echo   '</tbody>
      </table>
      ';

echo "<b>Total de leases: ".$total."</b>";

}//final isset post[nome_pools]

?>

      </div>
    </div>
    </form>
    <div class="list-group">
    
  
<div class="panel panel-default">
    <div class="panel-body" align="center">
    <b>Pesquisa por IP:</b><br><br><br>
    <form class="form-horizontal" role="form" name="pesquisa" action="leases.php" method="post">
    <div class="form-group <?php echo $erro1; ?>">
      <label class="col-sm-4 control-label">IP no lease (ou parte do IP):</label>
      <div class="col-sm-4">
        <input class="form-control" name="ip" type="text" value="<?php echo $ip; ?>" placeholder="Ex: 172.20.10.22 ou apenas 172.20.10."><?php echo $msg_campo;?><br><button type="submit" class="btn btn-success" id="pesquisar" name="pesquisar"> Pesquisar</button>
      </div>
    </div>
    </form>
    <div class="list-group">

<?php


if (isset($_POST['apagar'])){
  $ip = trim($_POST['ip']);

  if (inet_pton($ip)){

    
  }
}

if (isset($_POST['pesquisar'])){
  $ip = trim($_POST['ip']);

  if (strlen($ip) > 4){

    echo '
     <form name="form1" method="post" action="leases.php">
     <input type="hidden" name="ip" value="'.$ip.'">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Id</th>
            <th>IP</th>
            <th>Starts</th>
            <th>Ends</th>
            <th>Hostname</th>
            <th>MAC</th>
            <th>Ação</th>
          </tr>
        </thead>
        <tbody>';


        //$arquivo = file_get_contents("dhcpd.leases");
        //$arquivo = shell_exec('sudo cat /var/lib/dhcpd/dhcpd.leases | grep -A 9 "'.$ip.' "');
        $arquivo = shell_exec('sudo cat /var/lib/dhcpd/dhcpd.leases | grep -v "#"');
        $blocos         = explode("}\n", $arquivo);
        $linhas         = [];
            

            foreach($blocos as $id_pool => $pool){
                $linhas_do_bloco  = array_values(array_filter(explode("\n", $pool))); // remove linhas em branco e reordena keys
                $linhas[$id_pool] = $linhas_do_bloco;
                //$cont=1;
                $lease = "";
                foreach($linhas_do_bloco as $id_linha => $linha_do_pool){
                       
                                    
                            if (preg_match('/^\s*lease/i', $linha_do_pool)){
                                $lease = trim(explode('{',substr($linha_do_pool, 6))[0]);
                               
                            }                             
                          
                            if (preg_match('/^\s*starts/i', $linha_do_pool)){
                                $starts = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                $starts .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];
                                        
                            }                            
                        
                            if (preg_match('/^\s*ends/i', $linha_do_pool)){
                                $ends = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                $ends .= '  '.explode(';',explode(' ',$linha_do_pool)[5])[0];

                            }
                            if (preg_match('/^\s*client-hostname/i', $linha_do_pool)){
                                $hostname2 = explode(';',explode(' ',$linha_do_pool)[3])[0];
                                        
                            } else {
                                $hostname2 = "";
                            }

                            if (preg_match('/^\s*uid/i', $linha_do_pool)){
                                $uid = explode('"',$linha_do_pool)[1];
                                        
                            }
                            if (preg_match('/^\s*hardware/i', $linha_do_pool)){
                                $mac = explode(';',explode(' ',$linha_do_pool)[4])[0];
                                        
                            }                             
                
          

              }

                    $pre_lease = explode(".", $lease);
                    $pre_ip = explode(".", $ip);
                    $prefixo_lease = $pre_lease[0];
                    $prefixo_ip = $pre_ip[0];

                    if ($pre_ip[1] != ""){
                        $prefixo_lease .= ".".$pre_lease[1];
                        $prefixo_ip .= ".".$pre_ip[1];
                    }
                    if ($pre_ip[2] != ""){
                        $prefixo_lease .= ".".$pre_lease[2];
                        $prefixo_ip .= ".".$pre_ip[2];
                    }
                    if ($pre_ip[3] != ""){
                        $prefixo_lease .= ".".$pre_lease[3];
                        $prefixo_ip .= ".".$pre_ip[3];
                    }

                   

                    /// aqui faz a comparacao de IP do leases com os IPs do POOL
                    if ($prefixo_lease == $prefixo_ip){           
                                          
                                          echo '<tr>
                                          <td>'.$id_pool.'</td>
                                          <td>'.$lease.'</td>
                                          <td>'.$starts.'</td>
                                          <td>'.$ends.'</td>
                                          <td>'.$hostname2.'</td>
                                          <td>'.$mac.'</td>
                                          <td><button type="submit" class="btn btn-danger" value="'.$lease.'" name="apagar">Apagar</button></td>
                                          ';
                                          //$total++;

                    
      }
     }

echo   '</tbody>
      </table>
      </form>
      ';

  }
}

?>


    

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
</html>
