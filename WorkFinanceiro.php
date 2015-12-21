<?php
###########
## Módulo para Listagem dos boletos de um formando de forma online
## Criado: 15/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Expires" content="3" />

<title>Work Eventos - Sistema de Gestão de Eventos</title>

<script src="include/workFuncoes.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<?php

//Inclui o arquivo para manipulação de datas
//include "ManipulaDatas.php";

$edtFormandoCpf = $_POST["cpf"];
$edtFormandoSenha = $_POST["user_senha"];

//Captura os 5 ultimos numeros do CPF
$edtFormandoCpf = substr($edtFormandoCpf,(strlen($edtFormandoCpf)-5),strlen($edtFormandoCpf));        

$edtFormandoCpf = str_replace("-","", $edtFormandoCpf); 
  
//Dados do servidor remoto
$Server_atu = "mysql.fotusfotografia.com.br";

//$Login_atu = "consolieventos";
$Login_atu = "fotusfotografi";

//$Senha_atu = "";
$Senha_atu = "cavalosgalopantes";

//$DB_atu = "workeventos";
$DB_atu = "fotusfotografi";
  
//Conecta ao banco de dados online
//Define a sting de conexão
$conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');
  
//Conecta ao banco de dados principal
$base = @mysql_select_db($DB_atu) or die("Nao foi possivel selecionar a base: $DB_atu no servidor de destino !");

 //Processa o login do formando
$sql_login = mysql_query("SELECT 
                          form.id,
                          form.nome,
                          form.cpf,
                          form.senha,
                          form.evento_id,
                          eve.nome as evento_nome
                          FROM WORK_formandos form
                          LEFT OUTER JOIN WORK_eventos eve ON eve.id = form.evento_id
                          WHERE form.cpf = '$edtFormandoCpf'
                          AND form.senha = '$edtFormandoSenha'");
                          
$dados_formando = mysql_fetch_array($sql_login); 
    
//Verifica se a senha informada está correta
if ($registros_login > 0) 
{
  
?>

<table cellspacing="0" cellpadding="0" width="1002" border="0">
  <tr>
    <td>
      <table cellspacing="0" cellpadding="0" width="1002" border="0" >
        <tr>
          <td height="70" valign="middle" background="image/topo_sistema_online.jpg">&nbsp;</td>
        </tr>
        <tr>
          <td style="padding-bottom: 4px; padding-left: 6px; padding-top: 4px">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Olá, <span style="color: #990000;"><?php echo $dados_formando["nome"] ?></span></span>&nbsp;&nbsp;<a title="Sair do sistema" href="WorkLogin.php" style="font-size: 13px;">[ Sair ]</a>          			  	
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br/>
<table width="996" border="0" cellpadding="0" cellspacing="0" class="text" style="margin-left: 6px;">
    <tr>
      <td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 3px; border-right: 0px"></td>
      <td align="center" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 3px; padding-bottom: 4px">
        <strong>Caro formando:<br/><br/>Nossa rotina de atualização dos boletos ocorre diariamente das <span style='color: #990000'>08:00</span> as <span style='color: #990000'>10:00</span>, período este em que seus títulos podem não estar visíveis.<br/><br/>Caso <u>após as 10:00</u> encontre algum problema referente aos boletos, entre em contato com nosso departamento financeiro através do e-mail:<br/></strong>&nbsp;<a title="Enviar e-mail para dúvidas sobre boletos" href="mailto:boletos@consolieventos.com.br">boletos@consolieventos.com.br</a>
      </td>
    </tr>
</table>
<br/>
  
<?php

//Cria o array e o percorre para montar a listagem dinamicamente
while ($dados_login = mysql_fetch_array($sql_login))
{
  
?>

<br/>
  
<table width="996" border="0" cellpadding="0" cellspacing="0" class="text" style="margin-left: 6px;">
    <tr>
      <td align="center" style="padding-bottom: 6px;">
        <span class="TituloModulo">Relação de boletos emitidos a seu favor pela participação no evento:<br/><span style="color: #990000;"><?php echo $dados_login["evento_nome"] ?></span></span>
      </td>
    </tr>
</table>
    
<?php	
  
	$EventoId = $dados_login["evento_id"];
	$FormandoId = $dados_login["id"];
    
	//Busca os boletos emitidos para o evento
	$sql = mysql_query("SELECT 
					  id,
					  id_hash,
					  nosso_numero,
					  sacado,
					  demonstrativo2,
					  demonstrativo3,
					  valor_boleto,
					  data_documento,
					  data_vencimento,
					  boleto_recebido
					  FROM WORK_boleto bol 
					  WHERE evento_id = $EventoId AND formando_id = $FormandoId
					  ORDER BY id");
                      
                      
	//verifica o número total de registros
	$registros = mysql_num_rows($sql);
  
?>
    
<table width="996" border="0" cellpadding="0" cellspacing="0" style="margin-left: 6px;">
    <?php 
    
		//Verifica se há formandos cadastrados para o evento
		if ($registros == 0)
		{
    
	?>
    <tr>
		<td height="20" style="border: 1px #444444 solid; padding-left: 8px">
			<span style="font-size: 12px;"><b>Não há boletos emitidos !</b></span>
		</td>
    </tr>
    <?php 
      
        } 
		
		else 
		
		{    
          
        ?>
          
        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
					<tr class="listViewThS1" background="image/fundo_consulta.gif">                
						<td height="26" width="85" align="center">Situação</td>
						<td height="26">Dados do Sacado/Evento/Formando</td>
						<td height="26" width="75" align="center">Emissão</td>
						<td height="26" width="75" align="center">Vencto</td>
						<td height="26" width="80" align="right">Valor</td>                  
						<td height="26" width="130" align="center" style="padding-right: 0px">Ação</td>          
					</tr>
     
          <?php
          
          while ($dados = mysql_fetch_array($sql))
          {
            
        		//Verifica a situação do boleto
        		switch ($dados["boleto_recebido"]) 
				{
        		  
					case 0: $desc_situacao = "<span style='color: #990000'><strong>EM ABERTO</strong></span>"; break;		  
					case 1: $desc_situacao = "<span style='color: #6666CC'><strong>QUITADO</strong></span>"; break;
              
				}
                     
           
				?>
                <tr height="16">
        			<td style="border-bottom: 1px solid" align="center">
            			<?php echo $desc_situacao ?>				
            		</td>      			
					<td style="border-bottom: 1px solid" height="20">
						<font color="#444444" size="2" face="Tahoma">
						<strong><?php echo $dados["sacado"]; ?></strong>
            			</font>
						<br/>
            				<?php echo "<span style='color: #990000'><b>$dados[demonstrativo2]</b></span><br/>$dados[demonstrativo3]" ?>
            				</span>      
            			</td>
            			<td style="border-bottom: 1px solid" align="center">
            				<?php echo DataMySQLRetornar($dados["data_documento"]) ?>				
            			</td>
            			<td style="border-bottom: 1px solid" align="center">
            				<span style="color: #6666CC"><strong><?php echo DataMySQLRetornar($dados["data_vencimento"]) ?></strong></span>			
            			</td>			
                  <td style="border-bottom: 1px solid" align="right">
                    <?php 
            					echo "R$ " . number_format($dados["valor_boleto"], 2, ",", ".");
            					$total_receber = $total_receber + $dados["valor_boleto"]; 
            				?>
            			</td>
                  <td style="border-bottom: 1px solid" align="center">
                  <?php
                  
                    //Verifica se o boleto ainda está em aberto
                    if ($dados["boleto_recebido"] == 0)
                    {
                      
                      //HAbilita o link para exibir o boleto
                      ?>
                 	    <img src="image/bt_boleto.png" title="Visualizar Boleto" onclick="abreJanelaBoleto('./boletos/boleto_bb.php?TipoBol=2&BID=<?php echo $dados[id_hash] ?>&OID=OLN')" style="cursor: pointer" /><br/>Visualizar Boleto
                     <?php
                     
                     //Fecha o if de se o boleto está em aberto
                     }
                     else
                     {
                      
                      echo "&nbsp;";
                      
                      }
                     ?>			
            			</td>
                </tr>
           <?php
            
          }
          
          ?>
          
            </table>
          	  </td>
            </tr>
            
            <?php
          
        }
        
    ?>
    </table>
    <?php
        }
        
       ?>  
  <br/>
  <table width="996" border="0" align="left" cellpadding="0" cellspacing="0" style="margin-left: 6px;">
    <tr>
      <td width="150" valign="top">
        <a href="http://www.worklabs.com.br" target="_blank"><img src="image/logo_work_pq.jpg" border="0" /></a>
      </td>
      <td align="right">
  			Tecnologia: <a href="http://www.worklabs.com.br" target="_blank">work | eventos</a> - © 2007 : 2011 - Todos os direitos reservados - Desenvolvido por Work Labs Tecnologia e Sistemas Ltda
  			<br />
      </td>
    </tr>
  </table>
<?php
  
    
  //Caso o usuário tenha informado o login incorreto
  } else {
    
    include("WorkLogin.php");
    
    echo "<script language='javascript'>alert('Usuário ou senha inválido !');</script>";
    
  }

?>
