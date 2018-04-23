<?php

//inclui as configuracaoes padroes
include 'settings.php';

$arquivo = file_get_contents("dhcpd.conf");
$blocos         = explode("}", $arquivo);
$linhas         = [];

foreach($blocos as $id_pool => $pool){
    $linhas_do_bloco  = array_values(array_filter(explode("\n", $pool))); // remove linhas em branco e reordena keys
    $linhas[$id_pool] = $linhas_do_bloco;
    $cont=1;

    foreach($linhas_do_bloco as $id_linha => $linha_do_pool){
        	//echo "Cliente: {$id_pool}" . $linha_do_pool . "<br>";
    		
        	//guarda o nome do pool na variavel
        	if ($id_linha == 0){
        		$nome = trim(preg_replace('/#/','',$linha_do_pool));
        		//echo $nome.'<br>';
        	}
        	// ignora as linhas comentadas do arquivo
        	if (!preg_match('/^#/', $linha_do_pool)){
        		//echo $linha_do_pool;
        		if (preg_match('/^subnet/', $linha_do_pool)){
        			//echo $linha_do_pool;
        			$linha = explode(' ',$linha_do_pool);
        			$network = $linha[1];
        			$mascara = $linha[3];
        			//echo $network;
        		}
        		if (preg_match('/^\s*default/i', $linha_do_pool)){
        			 $lease_time = explode(';',substr($linha_do_pool, 23))[0];
        			       			        			
        		}
        		if (preg_match('/^\s*range/i', $linha_do_pool)){
        			//echo $linha_do_pool;
        			 $linha = explode(' ',$linha_do_pool);
        			 if ($cont == 1){
        			 	$ip_inicio1 = explode(' ',$linha[5])[0];
        			 	$ip_final1 = explode(';',$linha[6])[0]; 
        			 }
        			 if ($cont == 2){
        			 	$ip_inicio2 = explode(' ',$linha[5])[0];
        			 	$ip_final2 = explode(';',$linha[6])[0];
        			 }
        			 if ($cont == 3){
        			 	$ip_inicio3 = explode(' ',$linha[5])[0];
        			 	$ip_final3 = explode(';',$linha[6])[0];
        			 }
        			 if ($cont == 4){
        			 	$ip_inicio4 = explode(' ',$linha[5])[0];
        			 	$ip_final4 = explode(';',$linha[6])[0];
        			 }
        			 if ($cont == 5){
        			 	$ip_inicio5 = explode(' ',$linha[5])[0];
        			 	$ip_final5 = explode(';',$linha[6])[0];
        			 }
        			 if ($cont == 6){
        			 	$ip_inicio6 = explode(' ',$linha[5])[0];
        			 	$ip_final6 = explode(';',$linha[6])[0];
        			 }
        			 $cont++;


        		}
        		if (preg_match('/\s*broadcast/i', $linha_do_pool)){
        			$broadcast = explode(';',explode(' ',$linha_do_pool)[6])[0];
        			//echo $broadcast;

        		}
        		if (preg_match('/\s*router/i', $linha_do_pool)){
        			$gateway = explode(';',explode(' ',$linha_do_pool)[6])[0];
        			//echo $gateway;
        		}
        		if (preg_match('/\s*domain-name-server/i', $linha_do_pool)){
        			$dns = explode(';',explode(' ',$linha_do_pool)[6])[0];
        			//echo $dns;
        		}
        		if (preg_match('/\s*netbios/i', $linha_do_pool)){
        			$wins = explode(';',explode(' ',$linha_do_pool)[6])[0];
        			//echo $wins;
        		}
        		if (preg_match('/\s*wpad/i', $linha_do_pool)){
        			$wpad = preg_replace('/"/','',explode(';',explode(' ',$linha_do_pool)[6])[0]);
        			//echo $wpad;
        		}
        		if (preg_match('/\s*domain-name /i', $linha_do_pool)){
        			$dominio = preg_replace('/"/','',explode(';',explode(' ',$linha_do_pool)[6])[0]);
        			//echo $dominio;
        		}

        	}

    }
    echo "<br>";
    echo '		Nome: '.$nome.'<br>
        		Network: '.$network.'<br>
        		Mascara: '.$mascara.'<br>
        		Gateway: '.$gateway.'<br>
        		Broadcast: '.$broadcast.'<br>
        		DNS: '.$dns.'<br>
        		Lease time: '.$lease_time.'<br>
        		WINS: '.$wins.'<br>
        		Dominio: '.$dominio.'<br>
        		WPAD: '.$wpad.'<br>
        		';
        		$wpad2 = $wpad;
        		$wpad3 = preg_replace('/.dat/','.dat\\\\\n',$wpad2);

        		//insere no banco
        		$insert_pools = "INSERT INTO pools VALUES('0','$nome','$network','$mascara','$broadcast','$gateway','$lease_time','$data','$wins','$dns','$wpad3','$dominio','sim');";
        		$resultado = mysql_query($insert_pools) or die ('<font color="red"><b>Erro ao inserir no banco o POOL '.$nome.': '.mysql_error().'</b></font>');
        		$pega_id = "SELECT id FROM pools WHERE nome='$nome';";
        		$resultado_pega = mysql_query($pega_id) or die ('<font color="red"><b>Erro ao pegar o ID: '.mysql_error().'</b></font>');
        		while($row = mysql_fetch_array($resultado_pega)){    
                    $id = $row['id'];

                 }


        		if (strlen($ip_inicio1) > 3){
        			echo 'Ip inicio1: '.$ip_inicio1;
        			echo ' Ip final1: '.$ip_final1.'<br>';
        			$insert_range1 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio1','$ip_final1');";
        			$resultado = mysql_query($insert_range1) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}
        		if (strlen($ip_inicio2) > 3){
        			echo 'Ip inicio2: '.$ip_inicio2;
        			echo ' Ip final2: '.$ip_final2.'<br>';
        			$insert_range2 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio2','$ip_final2');";
        			$resultado = mysql_query($insert_range2) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}
        		if (strlen($ip_inicio3) > 3){
        			echo 'Ip inicio3: '.$ip_inicio3;
        			echo ' Ip final3: '.$ip_final3.'<br>';
        			$insert_range3 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio3','$ip_final3');";
        			$resultado = mysql_query($insert_range3) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}
        		if (strlen($ip_inicio4) > 3){
        			echo 'Ip inicio4: '.$ip_inicio4;
        			echo ' Ip final4: '.$ip_final4.'<br>';
        			$insert_range4 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio4','$ip_final4');";
        			$resultado = mysql_query($insert_range4) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}
        		if (strlen($ip_inicio5) > 3){
        			echo 'Ip inicio5: '.$ip_inicio5;
        			echo ' Ip final5: '.$ip_final5.'<br>';
        			$insert_range5 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio5','$ip_final5');";
        			$resultado = mysql_query($insert_range5) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}
        		if (strlen($ip_inicio6) > 3){
        			echo 'Ip inicio6: '.$ip_inicio6;
        			echo ' Ip final6: '.$ip_final6.'<br>';
        			$insert_range6 = "INSERT INTO range_ips VALUES('0','$id','$ip_inicio6','$ip_final6');";
        			$resultado = mysql_query($insert_range6) or die ('<font color="red"><b>Erro ao inserir no banco: '.mysql_error().'</b></font>');
        		}

        		

        		$ip_inicio1 ="";
        		$ip_inicio2 ="";
        		$ip_inicio3 ="";
        		$ip_inicio4 ="";
        		$ip_inicio5 ="";
        		$ip_inicio6 ="";
        		$ip_final1 = "";
        		$ip_final2 = "";
        		$ip_final3 = "";
        		$ip_final4 = "";
        		$ip_final5 = "";
        		$ip_final6 = "";
        		
    
       
     echo '<br>';
}

mysqli_close($conecta);


?>