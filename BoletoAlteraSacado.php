<?php
###########
## Módulo de pesquisa para BOLETOS
## Criado: - 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//Monta a query para pegar os dados
$sql = "SELECT * FROM boleto WHERE sacado = 'Dados do formando não informados' order by data_vencimento DESC";

//Executa a query
$query = mysql_query($sql);

//Conta o numero de registros da query
$registros = mysql_num_rows($query);

//Caso não houver registros
if ($registros == 0) 
{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'><span class='TituloModulo'>Boletos: </span><span class='style1'>Não há boletos disponíveis para alteraçao !</span></td>
			</tr>
		</table>";

} 

else 

{
	
	echo "<table width='100%' align='left' border='0' cellspacing='0' cellpadding='0'>
			<tr>
				<td valign='middle'>
					<span class='TituloModulo'>Boletos: </span><span class='style1'>A pesquisa retornou $registros resultado(s)</br>
				</td>
			</tr>
			<tr>
				<td>		  
					<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>		  
						<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
							<td width='116' align='center'>Nosso Número</td>
							<td>Dados do Sacado/Evento/Formando</td>
							<td width='60' align='center'>Emissão</td>
							<td width='60' align='center'>Vencto</td>
							<td width='80' align='right'>Valor</td>
							<td width='65' align='center'>Situação</td>       		
						</tr>";						

	//efetua o loop na pesquisa
	while ($dados_rec = mysql_fetch_array($query))
	{		
		
		//Verifica a situação do boleto
		switch ($dados_rec["boleto_recebido"]) 
		{
  	  
			case 0: $desc_situacao = "<span style='color: #990000'>Em Aberto</span>"; break;		  
			case 1: $desc_situacao = "<span style='color: #6666CC'>Recebido</span>"; break;
      
		}
		
		?>

		<tr height="16">
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo substr($dados_rec["nosso_numero"], 0,7) ?></span><span style="color: #990000"><?php echo substr($dados_rec["nosso_numero"], 7,3) ?></span><span style="color: #59AA08"><?php echo substr($dados_rec["nosso_numero"], 10,5) ?></span><?php echo substr($dados_rec["nosso_numero"], 15,2) ?>				
			</td>      			
			<td style="border-bottom: 1px solid" height="20">
				<font color="#CC3300" size="2" face="Tahoma">
				  <a title="Clique para exibir este boleto" href="#" onclick="wdCarregarFormulario('BoletoAlteraSacadoAltera.php?Id=<?php echo $dados_rec[id] ?>&headers=1','dados_boleto')"><?php echo $dados_rec["sacado"]; ?></a>
				</font>
				<br/>
				<?php echo "<span style='color: #990000'><b>$dados_rec[demonstrativo2]</b></span><br/>$dados_rec[demonstrativo3]" ?>
				</span>      
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo DataMySQLRetornar($dados_rec["data_documento"]) ?>				
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<span style="color: #6666CC"><?php echo DataMySQLRetornar($dados_rec["data_vencimento"]) ?></span>			
			</td>			
			<td style="border-bottom: 1px solid" align="right">
				<?php 
					echo "R$ " . number_format($dados_rec["valor_boleto"], 2, ",", ".");
					$total_receber = $total_receber + $dados_rec["valor_boleto"]; 
				?>
			</td>
			<td style="border-bottom: 1px solid" align="center">
				<?php echo $desc_situacao ?>				
			</td>
		</tr>

		<?php 
			
			//Fecha o while
			}
			
			echo "</table><br />";
			}

		?> 
		</td>
	</tr>
	<tr>
		<td>
			<div id='dados_boleto'></div>
		</td>
	</tr>	
</table>
<br/>