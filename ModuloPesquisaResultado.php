<?php 
###########
## Módulo para Exibição do Resultado da pesquisa
## Criado: 23/04/2007 - Maycon Edinger
## Alterado: 20/05/200
## Alterações: 
## 20/05/2007 - Corrigido problema com o include errado dos arquivos das pesquisas
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Recebendo os valores da pesquisa
$chavePesquisa = $_GET["ChavePesquisa"];

//pesquisa as diretivas do usuário
$sql_usuario = "SELECT * FROM usuarios WHERE usuario_id = $usuarioId";													  													  
							  
//Executa a query
$resultado_usuario = mysql_query($sql_usuario);

//Monta o array dos campos
$dados_usuario = mysql_fetch_array($resultado_usuario);

?>

<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td class="text" valign="top">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Resultado da Pesquisa  </span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>

			<table id="2" width="626" align="left" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td valign="middle">Pesquisando por <span class="style1">"<?php echo $chavePesquisa ?>"</span>&nbsp;em <span class="style1"><?php echo $Modulo ?></span></td>
				</tr>
			</table>
	 
			<br/>
			<br/>

			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de compromissos
							include "PesquisaCompromissos.php";
							echo "<br /><br />"; 
						 ?>
					 </td>	 
				</tr>	
				<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de eventos
							include "PesquisaEventos.php";
							echo "<br /><br />"; 
						?>
					</td>	 
				</tr>
				<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de formandos
							include "PesquisaFormandos.php";
							echo "<br /><br />"; 
						
						?>
					</td>	 
				</tr>
				<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de boletos
							include "PesquisaBoletos.php";
							echo "<br /><br />"; 
						?>
					</td>	 
				</tr>
			 	<tr>
					<td>
						<?php 
						   
							//Inclui a pesquisa de clientes
							include "PesquisaClientes.php";
			 				echo "<br /><br />"; 		 
						?>
					</td>
		 		</tr>
		 		<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de fornecedores
							include "PesquisaFornecedores.php";
			 				echo "<br /><br />"; 		 
						?>
					</td>
		 		</tr>		 
		 		<tr>
					<td>
						<?php 
						 
							//Inclui a pesquisa de colaboradores
							include "PesquisaColaboradores.php";
							echo "<br /><br />"; 		 
						?>
					</td>
		 		</tr>
				<?php
					 
					//verifica se o usuário pode ver este menu
					if ($dados_usuario["menu_financeiro"] == 1)
					{
				?>
		 		<tr>
					<td>
						<?php 
						
							//Inclui a pesquisa de cheques de terceiro
							include "PesquisaChequesTerceiro.php";
							echo "<br /><br />"; 		 
						?>
					</td>
		 		</tr>
				<?php
					 
					//verifica se o usuário pode ver este menu
					if ($usuarioNome == "Maycon" OR $usuarioNome == "Josiane" OR $usuarioNome == "Joni")
					{
				?>
				<tr>
					<td>
						<?php 
						
							//Inclui a pesquisa de cheques da empresa
							include "PesquisaChequesEmpresa.php";
							echo "<br /><br />"; 		 
						?>
					</td>
		 		</tr>
				<?php
				
					}
					
					}
					
					//verifica se o usuário pode ver este menu
					if ($dados_usuario["menu_financeiro"] == 1 OR $usuarioNome == "Luana" )
					{
					
				?>
				<tr>
					<td>
						<?php 
							
							//Inclui a pesquisa de contas a pagar
							include "PesquisaContasReceber.php";
							echo "<br /><br />";							
							
						?>
					</td>
		 		</tr>
				<tr>
					<td>
						<?php 
						 
							//Inclui a pesquisa de contas a receber de evento
							include "PesquisaContasReceberEvento.php";
							echo "<br /><br /><br/><br/>"; 		 
						?>
					</td>
		 		</tr>
				<?php
				
				}
				
				if ($dados_usuario["menu_financeiro"] == 1)
				{
				
				?>
				<tr>
					<td>
						<?php 
						
							//Inclui a pesquisa de contas a receber
							include "PesquisaContasPagar.php";
							echo "<br /><br /><br/>"; 		 
						?>
					</td>
		 		</tr>
				<?php
				
				}
				
				?>
				<tr>
					<td>
						<?php 
						   
							//Inclui a pesquisa de recados
							include "PesquisaRecados.php";
			 				echo "<br /><br />"; 		 
						?>
					</td>
				</tr>
			</table>	  
		</td>   
	</tr> 
</table>  	 


