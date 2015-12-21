<?php 
###########
## Módulo para Exibição dos dados do cheque cadastrado
## Criado: 01/03/2009 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Recupera o id do cheque
$ChequeId = $_GET["ChequeId"];
$ContaPagarId = $_GET["ContaPagarId"];

//echo "ContaId: ". $ContaPagarId;

//Monta o sql para recuperar os dados do cheque
$sql="SELECT
	che.id,
	che.numero_cheque,
	che.bom_para,
	che.data_recebimento,
	che.favorecido,
	che.agencia,
	che.pre_datado,
	che.valor,
	che.valor_utilizado,
	che.conta,
	che.status,
	ban.nome as banco_nome			
	FROM cheques che 
	LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
	WHERE che.numero_cheque LIKE '$ChequeId%'";	
							   
//Executa a query
$resultado = mysql_query($sql);

$registros = mysql_num_rows($resultado);

//Verifica se encontrou o cheque
if ($registros == 0)
{

	echo "<script>alert('Cheque não encontrado !!!');</script>";
	echo "<span style='color: #990000'><b>Cheque não encontrado !!!</b></span>";	

} 

else 

{

?>

 <div style="background-color: #FFFFCD; border: #aaa solid 1px; padding: 5px;">
    <strong>Selecione o cheque que deseja utilizar:</strong>
    <br/>
    <br/>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView" style="border-bottom: 0px">
		<tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
			<td style='border-right: 1px dotted' width="80" align="center">Ação</td>
			<td style='border-right: 1px dotted' width='66' align='center'>Nº Cheque</td>
			<td style='border-right: 1px dotted' width="76" align="center">Valor</td>
			<td style='border-right: 1px dotted' width="76" align="center">Valor Disp.</td>
			<td style='border-right: 1px dotted' width='76' align='center'>Banco</td>          
			<td style='border-right: 1px dotted' width='55' align='center'>Agencia</td>
			<td style='border-right: 1px dotted' width='66' align='center'>C/C</td>
			<td>&nbsp;Titular</td>			
		</tr>
		<?php
		
			$linha = 1;
		
			//Cria o array e o percorre para montar a listagem dinamicamente
			while ($dados = mysql_fetch_array($resultado))
			{
			  
				if ($linha == 1)
				{
				  
				  $checa = "checked='checked'";
				  
				}
				
				else
				
				{
				  
				  $checa = "";
				  
				}
					   
				//Verifica se o status do cheque é 1 - disponível
				if ($dados["status"] == 1)
				{
				  
					?>
					
					<tr height="16">      
						<td bgcolor="#ffffff" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26" align="center">
							<input type="radio" name="edtCheque" value="<?php echo $dados[id] ?>" title="<?php echo $dados[id] ?>" <?php echo $checa ?> />&nbsp;<input name="btnDetalhes" type="button" class="button" id="btnPegaCheque" title="Exibe os detalhes deste cheque" value="Detalhes" style="width: 50px" onclick="wdCarregarFormulario('ChequeDetalheJanela.php?ChequeId=<?php echo $dados[id] ?>&headers=1','cheque_detalhe',2)" />
						</td>
						<td bgcolor="#ffffff" align="center" style="border-bottom: 1px solid; border-right: 1px dotted" height="26">
							<span style='color: #990000'><b><?php echo $dados[numero_cheque] ?></b></span>
						</td>
						<td bgcolor="#ffffff" align="right" style="border-bottom: 1px solid; border-right: 1px dotted; padding-right: 4px" height="26">
							<?php echo number_format($dados[valor],2,",",".") ?>
						</td>
						<td bgcolor="#ffffff" align="right" style="border-bottom: 1px solid; border-right: 1px dotted; padding-right: 4px" height="26">
							<?php echo number_format($dados[valor] - $dados[valor_utilizado],2,",",".") ?>
						</td>
						<td bgcolor="#ffffff" align="center" style="border-bottom: 1px solid; border-right: 1px dotted;" height="26">
							<?php echo $dados[banco_nome] ?>
						</td>
						<td bgcolor="#ffffff" align="center" style="border-bottom: 1px solid; border-right: 1px dotted;" height="26">
							<?php echo $dados[agencia] ?>
						</td>
						<td bgcolor="#ffffff" align="center" style="border-bottom: 1px solid; border-right: 1px dotted;" height="26">
							<?php echo $dados[conta] ?>
						</td>
						<td bgcolor="#ffffff" style="border-bottom: 1px solid;" height="26">
							<?php echo "&nbsp;" . $dados[favorecido] ?>
						</td>
					</tr>
					
					<?php
										
					$botao_pega = 1;
				
				}
			
				else
			
				{
			  
					?>
					
					<tr height="16">      
						<td bgcolor="#F0D9D9" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26" align="center">
							<input name="btnDetalhes" type="button" class="button" id="btnPegaCheque" title="Exibe os detalhes deste cheque" value="Detalhes" style="width: 50px" onclick="wdCarregarFormulario('ChequeDetalheJanela.php?ChequeId=<?php echo $dados[id] ?>&headers=1','cheque_detalhe',2)" />
						</td>
						<td bgcolor="#F0D9D9" align="center" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26">
							<span style='color: #990000'><b><?php echo $ChequeId ?></b></span>
						</td>
						<td bgcolor="#F0D9D9" align="right" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px; padding-right: 4px" height="26">
							<s><?php echo number_format($dados[valor],2,",",".") ?></s>
						</td>
						<td bgcolor="#F0D9D9" align="right" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px; padding-right: 4px" height="26">
							<s><?php echo number_format($dados[valor] - $dados[valor_utilizado],2,",",".") ?></s>
						</td>
						<td bgcolor="#F0D9D9" align="center" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26">
							<s><?php echo $dados[banco_nome] ?></s>
						</td>
						<td bgcolor="#F0D9D9" align="center" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26">
							<s><?php echo $dados[agencia] ?></s>
						</td>
						<td bgcolor="#F0D9D9" align="center" style="border-bottom: 1px solid; border-right: 1px dotted; padding-bottom: 2px" height="26">
							<s><?php echo $dados[conta] ?></s>
						</td>
						<td bgcolor="#F0D9D9" style="border-bottom: 1px solid;" height="26">
							<s><?php echo "&nbsp;" . $dados[favorecido] ?></s>
						</td>
					</tr>
					
					<?php
					 
				}
			
				$linha++;
		  
			//Fecha o while
			}
			
			echo "</table>";
	
			//Verifica se deve exibir o botão de captura de cheque
			if ($botao_pega == 1)
			{
		
		?>
		
		<br/>
		<input name="btnPegaCheque" type="button" class="button" id="btnPegaCheque" title="Utiliza o cheque selecionado para o pagamento da conta" value="Utilizar Cheque" style="width: 120px" onclick="var edtCheque = document.getElementsByName('edtCheque'); for (var i=0; i < edtCheque.length; i++) {if (edtCheque[i].checked == true) {edtChequeValor = edtCheque[i].value; break;}} ;wdCarregarFormulario('PegaCheque.php?ChequeId=' + edtChequeValor + '&headers=1&ContaPagarId=<?php echo $ContaPagarId ?>','pega_cheque',2)" /><div id="pega_cheque" style="display: inline"></div>
    
		<?php
		
		}
	?>
	<br/>
    <div id="cheque_detalhe"></div>
</div>
<br/>
  
<?php

}

?>