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
?>
<div align="center">
<img src="images/logo.png">

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
