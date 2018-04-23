<?php
//ini_set('display_errors',1);
//ini_set('display_startup_erros',1);
//error_reporting(E_ALL);

//Database settings
$hostname = "localhost";
$username = "your_user";
$password = "your_pass";
$database = "dhcpwebadmin";
$conecta = mysqli_connect("$hostname","$username","$password") or die("Erro ao conectar ao MySQL!");
mysqli_select_db($conecta,"$database") or die("Não foi possivel conectar ao banco DHCPWebAdmin.");


$data = date("Y-m-d H:i");
$data_token = date("Y-m-d");
// pega o endereco IP do local
$ip_token = $_SERVER["REMOTE_ADDR"];

$status_servico = "";


// recebe os dados para autenticacao
if (isset($_POST['login']) AND isset($_POST['senha'])){
  $usuario = strtolower(trim($_POST['usuario']));
  $senha = md5($_POST['senha']);
  //MUDE AQUI A SENHA EM FORMATO MD5
  //DEFAULT USER: admin PASS: admin
  if (($usuario == 'admin') AND ($senha == '21232f297a57a5a743894a0e4a801fc3')){
  	session_start();
  	$_SESSION['logado'] = $usuario;
  	header("Location: index.php");
  }

}

//INICIALIZA A SESSÃO
session_start();
//SE NÃO TIVER VARIÁVEIS REGISTRADAS RETORNA PARA A TELA DE LOGIN
if (!isset($_SESSION['logado'])){
    header("Location: login.php");
} 

//faz logout ao clicar no menu logout
if (isset($_GET['logout']) AND ( $_GET['logout'] == 'ok')){ 
  $login = $_SESSION['logado'];
  unset($_SESSION['logado']);
  session_destroy($_SESSION['logado']);
  header("Location: login.php");
}



//rodape padrao de todas as paginas
$rodape = '<a class="navbar-brand" >DHCPWebAdmin - Versão 0.8 - Desenvolvido por: diego.lopes@gmail.com.</a>';


//menu de todas as paginas
$menu = '<nav class="navbar navbar-inverse">
      <div class="container-fluid">
      <div class="navbar-header">
           <a class="navbar-brand" href="index.php">DHCPWebAdmin</a>
      </div>
  <ul class="nav navbar-nav">
        <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Pools<span class="caret"></span></a>
    <ul class="dropdown-menu">
              <li><a href="adicionar_subnet.php">Adicionar Pool</a></li> 
              <li><a href="listar_subnets.php">Listar Pools</a></li>
              
        </ul>
    </li>
      <li><a href="leases.php">Leases</a></li>
          <li><a href="logs.php">LOGs do Serviço</a></li>
          <li><a href="gerar_arquivo.php">Gerar dhcpd.conf</a></li>
          <li><a href="settings.php?logout=ok">Logout</a></li>
    </ul>
    </div>
    </nav> ';


function verifica_status(){
$saida_shell =  shell_exec('ps aux | grep /usr/sbin/dhcpd | wc -l');
$pid =  shell_exec('ps aux | grep /usr/sbin/dhcpd | grep -v grep | awk \'{print $2}\' ');
//echo $saida_shell;
if ( $saida_shell > 2 ){
    return  '<div><b>Status do Serviço DHCPd: <font color="green">OK - PID: '.$pid.'</font></b></div>';
    } else{
    return  '<div><b>Status do Serviço DHCPd: <font color="red">PARADO, FAVOR VERIFICAR LOG <a href="logs.php">AQUI.</a></font></b></div>'; 
  }

}//final function verifica_status






function valida_ip($ip){
    return inet_pton($ip);
}
function valida_mac($mac){
	return preg_match('/^[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}:[a-f0-9]{2}$/i',$mac);
}

function backup_e_restart(){
  // grava uma copia de backup do arquivo antes
  echo 'Fazendo backup e restartando o serviço...<br>';
  shell_exec('sudo cp /etc/dhcp/dhcpd.conf /etc/dhcp/dhcpd.conf.bkp');
  gerar_arquivo();
  shell_exec('sudo service dhcpd restart');
  sleep(1);
  $status_servico = verifica_status();
  
  //verifica o status do servico se esta rodando normalmente depois do restart
  if ($status_servico == '<div><b>Status do Serviço DHCPd: <font color="red">PARADO, FAVOR VERIFICAR LOG <a href="logs.php">AQUI.</a></font></b></div>' ){
      echo '<font color="red">Oooops! Há algum erro no novo arquivo dhcpd.conf que não foi possível reiniciar o serviço de dhcp. Voltando o arquivo de backup!</font><br>';
      echo '<font color="red">Verifique os campos abaixo pois há algum parâmetro errado.</font><br>';
      echo '<font color="red">Caso precise descobrir qual parâmetro, olhe a página de LOGs para tentar descobrir o erro <a href="logs.php">AQUI</a>.</font><br>';
      shell_exec('sudo cp /etc/dhcp/dhcpd.conf.bkp /etc/dhcp/dhcpd.conf');
      shell_exec('sudo service dhcpd restart');
      return "erro";
  } else{
      header('location: listar_subnets.php?sucesso=1');
      
  }

}//final function backup_e_restart

function gerar_arquivo(){


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
    $resultado = mysql_query($conecta,$sql) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysql_error().'</b></font>');
    

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
                    $resultado2 = mysql_query($conecta,$sql2) or die ('<font color="red"><b>Erro ao consultar no banco: '.mysql_error().'</b></font>');
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
    shell_exec('sudo rm -f /var/www/html/dhcpwebadmin/dhcpd.conf');      
    $nome_arquivo = '/var/www/html/dhcpwebadmin/dhcpd.conf';
    unlink($nome_arquivo);
    $file = fopen($nome_arquivo, 'a');
    fwrite($file, $arquivo);
    fclose($file);
    shell_exec('sudo mv /var/www/html/dhcpwebadmin/dhcpd.conf /etc/dhcp/');


}






/*function cidr2NetmaskAddr($cidr) {
	$ta = substr($cidr, strpos($cidr, '/') + 1) * 1;
	$netmask = str_split(str_pad(str_pad('', $ta, '1'), 32, '0'), 8);
	foreach ($netmask as &$element) $element = bindec($element);
	return join('.', $netmask);
}*/
function calcIPrange($ip_addr,$subnet_mask) {
	$ip = ip2long($ip_addr);
	$nm = ip2long($subnet_mask);
	$nw = ($ip & $nm);
	$bc = $nw | (~$nm);
	return 	array(	"network" => long2ip($nw),
                	"broadcast" => long2ip($bc),
                	"gateway" => long2ip($bc - 1),
                	"start_ip" => long2ip($nw + 1),
                	"end_ip" => long2ip($bc - 2)
	             );
}


// separa os octetos do ip
function ip2octetos($ip){
  $octetos = explode(".", $ip);
  return array( "octeto1" => $octetos[0],
                "octeto2" => $octetos[1],
                "octeto3" => $octetos[2],
                "octeto4" => $octetos[3]
              );
}



/*
function net_match($network, $ip) {
      // determines if a network in the form of 192.168.17.1/16 or
      // 127.0.0.1/255.255.255.255 or 10.0.0.1 matches a given ip
      $ip_arr = explode('/', $network);
      $network_long = ip2long($ip_arr[0]);
      $x = ip2long($ip_arr[1]);
      $mask =  long2ip($x) == $ip_arr[1] ? $x : 0xffffffff << (32 - $ip_arr[1]);
      $ip_long = ip2long($ip);
      // echo ">".$ip_arr[1]."> ".decbin($mask)."\n";
      return ($ip_long & $mask) == ($network_long & $mask);
}

function gera_conf() {
        $cmd = "SELECT * FROM pools WHERE ativo='sim' ORDER BY network;";
        $consulta = mysql_query($cmd);
        $resultado=mysql_fetch_array($consulta);
        $arquivo= "";
        foreach ($resultado as $tempArray) {
                $arquivo .= "# ".$tempArray['nome']."\n";
                $arquivo .= "subnet ".$tempArray['network']." netmask ".$tempArray['mascara']." {\n";
                $arquivo .= "   option domain-name-servers ip-to-dns1, ip-to-dns2;\n";
                $arquivo .= "   option broadcast-address ".$tempArray['broadcast'].";\n";
                $arquivo .= "   option domain-name ".$tempArray['dominio'].";\n";
                $arquivo .= "   option ntp-servers ip-to-ntp-server;\n";
                $arquivo .= "   next-server ip-to-next-server;\n";
                $arquivo .= "   server-name \"your_url\";\n";
                $arquivo .= "   option routers ".$tempArray['gateway'].";\n";
                $arquivo .= "   pool {\n";
                $arquivo .= "           max-lease-time 3600;\n";
                $arquivo .= "           default-lease-time 3600;\n";
                $arquivo .= "           range ".$tempArray['start_ip']." ".$tempArray['end_ip'].";\n";
                $arquivo .= generateStaticHosts($tempArray['pool']);
                $arquivo .= "   }\n";
                $arquivo .= "}\n\n";

        }
        return $arquivo;
}*/


?>
