<?php
###########
## Módulo para processamento dos boletos vindos do arquivo de retorno
## Criado: 20/01/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1) 
{
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Processa as diretivas de segurança 
require('Diretivas.php');
	
//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Inclui o arquivo para manipulação de valor monetário
include "./include/ManipulaMoney.php";
	
$edtTotalChk = $_POST['edtTotalChk'];

echo "linhas:  " . $edtTotalChk . "<br/><br/>";

$dataProcessa = date("Y-m-d", mktime());

$TemMensagem = 0;
		
//Define o valor inicial para efetuar o FOR
for ($contador_for = 1; $contador_for <= $edtTotalChk; $contador_for++)
{						
	
	//Enquanto não chegar ao final do contador total de itens
	if ($_POST[$contador_for] != 0) 
	{
		
		$numero_boleto = $_POST[$contador_for];
		$valor_boleto = MoneyMySQLInserir($_POST["edtValor$contador_for"]);
		$valor_boleto_formata = $_POST["edtValor$contador_for"];
		
		//echo "Titulo: " . $numero_boleto . " Valor: " . $valor_boleto . "<br/><br/>";
		
		$titulos_processados .= "<tr><td align='center'><b>" . $numero_boleto . "</b></td><td align='right'>R$ " . $valor_boleto . "</td></tr>";
		
		$total_recebido = $total_recebido + $valor_boleto;
		
		//Busca no banco de dados se existe o boleto
		//Monta a query para pegar os dados
		$sql_busca_boleto = "SELECT conta_receber_id FROM boleto WHERE nosso_numero = '$numero_boleto' LIMIT 1";
	
		//Executa a query
		$query_busca_boleto = mysql_query($sql_busca_boleto);
	  
		$dados_busca_boleto = mysql_fetch_array($query_busca_boleto);
		
		$contaId = $dados_busca_boleto["conta_receber_id"];
		
		//Marca o boleto como recebido
		$sql = "UPDATE boleto SET 
                boleto_recebido = 1,
                data_recebimento = '$dataProcessa',
                valor_recebido = '$valor_boleto',
                obs_recebimento = 'Recebemos via boleto na data  o valor de R$ $valor_boleto_formata',
                usuario_recebimento_id = $usuarioId
                WHERE nosso_numero = '$numero_boleto'";
               
		//echo $sql . "<br/><br/>";
               	   
        mysql_query($sql); 
                
		//Gera um lançamento de recebimento para a conta a receber vinculada
		$sql = "INSERT INTO contas_receber_recebimento (
				conta_receber_id,
				data_recebimento, 
				tipo_recebimento,
				total_recebido,
				obs 
				) values (				
				$contaId,
				'$dataProcessa',
				'4',
				'$valor_boleto',
				'Recebemos via boleto na data  o valor de R$ $valor_boleto_formata'
				);";
              
        //echo $sql . "<br/><br/>";
        		
        mysql_query($sql);            
                              
            
        //Efetua a baixa da conta a receber vinculada
		$sql = "UPDATE contas_receber SET situacao = 2, valor_recebido = '$valor_boleto' WHERE id = $contaId";
                      
        //echo $sql . "<br/><br/>";
        
        mysql_query($sql);
               
        //Gera um lançamento na base de retornos processados com o status de processado (1)
        $sql = "INSERT INTO retornos (
				data_processamento,
				titulo, 
				valor,
				status
				) values (
				'$dataProcessa',
				'$numero_boleto',
				'$valor_boleto',
				'1'
				);";
              
        //echo $sql . "<br/><br/>";
		
		mysql_query($sql);	
		
	}
		
}

?>
<link rel="stylesheet" type="text/css" href="include/workStyle.css">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Arquivos de Retorno</span>
		</td>
	</tr>
	<tr>
		<td>
			<img src="image/bt_espacohoriz.gif" width="100%" height="12">
		</td>
	</tr>
	<tr>
		<td>
			<span style="font-size: 12px"><b>RESULTADO DO PROCESSAMENTO DOS BOLETOS DO ARQUIVO DE RETORNO:</b></span><br/><br/>
		</td>
	</tr>
	<tr>
		<td>
			<table width="200" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width='120' align='center'>
						Boleto
					</td>
					<td align="right">
						Valor
					</td>
				</tr>
				<?php echo $titulos_processados ?>
				<tr>
					<td align='right'>
						TOTAL:&nbsp;
					</td>
					<td align='right'>
						<?php echo "R$ " . number_format($total_recebido,2,',','.') ?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
