<?php 
###########
## Módulo para Processamento de Multa e Juros de boletos
## Criado: 12/07/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
## 
###########

header('Content-Type: text/html;  charset=ISO-8859-1',true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

$hoje = date('Y-m-d', mktime());

//Rotina para verificar o número de dias que precisa ser calculado os juros sobre o valor
//Captura a data do ultimo processamento

$data_processa = $_GET['DataProcessa'];

$data_processa_normal = DataMySQLRetornar($data_processa);

$data_processa_1 = som_data($data_processa_normal, 1);

$data_processa_1_banco = DataMySQLInserir($data_processa_1);


//echo "Data Processa: " . $data_processa;

//defino data 1 
$ano1 = substr($data_processa,0,4); 
$mes1 = substr($data_processa,5,2); 
$dia1 = substr($data_processa,8,2); 

//defino data 2 
$ano2 = date("Y", mktime()); 
$mes2 = date("m", mktime()); 
$dia2 = date("d", mktime()); 

//calculo timestam das duas datas 
$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); 
$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2); 

//diminuo a uma data a outra 
$segundos_diferenca = $timestamp1 - $timestamp2; 
//echo $segundos_diferenca; 

//converto segundos em dias 
$dias_diferenca = $segundos_diferenca / (60 * 60 * 24); 

//obtenho o valor absoluto dos dias (tiro o possível sinal negativo) 
$dias_diferenca = abs($dias_diferenca); 

//tiro os decimais aos dias de diferenca 
$dias_diferenca = floor($dias_diferenca); 

//echo "<br/>--------<br/>Dias a calcular: " . $dias_diferenca . "<br/>"; 

//Consulta as contas que devem ser processadas na parte das multas
//Cria a SQL
$consulta_multa = "SELECT
                  id,
                  valor,
                  valor_original,
                  valor_boleto,
                  valor_multa,
                  valor_juros,
                  valor_multa_juros,
                  taxa_multa,
                  taxa_juros                  
                  FROM contas_receber WHERE empresa_id = $empresaId 
                  AND data_vencimento < '$hoje'
                  AND situacao = 1
                  AND multa_aplicada = 0
                  ";
                  
//Executa a query
$listagem_multa = mysql_query($consulta_multa);

$numero_registros_multa = mysql_num_rows($listagem_multa);

//Verifica se deve processar alguma conta
if ($numero_registros_multa > 0)
{
  
  //Monta e percorre o array com os dados da consulta
  while ($dados_multa = mysql_fetch_array($listagem_multa))
  {
  
    $conta_id = $dados_multa['id'];
    
    //Calcula o valor em R$ da multa
    $valor_multa = $dados_multa['valor'] * ($dados_multa['taxa_multa'] / 100);
    
    $valor_corrigido_multa = $dados_multa['valor'] + $valor_multa;
    
    //echo "Valor a Pagar: " . $dados_multa["valor"] . "<br/>";
    //echo "Valor da Multa: " . $valor_multa . "<br/>";
    //echo "Valor do Juros: " . $valor_juros . "<br/>";
    //echo "Valor Corrigido: " . $valor_corrigido . "<br/><br/>";
    
    
    //Atualiza o registro com os dados da multa
    $atualiza_multa = mysql_query("UPDATE contas_receber SET
                                  valor = '$valor_corrigido_multa',
                                  valor_multa = '$valor_multa',
                                  valor_multa_juros = '$valor_multa',
                                  multa_aplicada = 1
                                  WHERE id = $conta_id");  

  }

}


// ****ATUALIZAÇÃO DOS JUROS ****
//Cria a SQL
$consulta_juros = "SELECT
                  id,
                  valor,
                  valor_original,
                  valor_boleto,
                  valor_multa,
                  valor_juros,
                  valor_multa_juros,
                  taxa_multa,
                  taxa_juros,
                  boleto_id                  
                  FROM contas_receber WHERE empresa_id = $empresaId 
                  AND data_vencimento < '$hoje'
                  AND situacao = 1
                  ";
                  
//Executa a query
$listagem_juros = mysql_query($consulta_juros);

$numero_registros_juros = mysql_num_rows($listagem_juros);

$data_atualizacao = date("Y-m-d", mktime());

//Verifica se deve processar alguma conta
if ($numero_registros_juros > 0)
{
  
	//Monta e percorre o array com os dados da consulta
	while ($dados_juros = mysql_fetch_array($listagem_juros))
	{
  
		$conta_id = $dados_juros['id'];
    
		//Calcula o valor em R$ dos juros
		$valor_juros = $dados_juros['valor'] * (($dados_juros['taxa_juros'] / 100)/30);
    
		$valor_corrigido_juros = $dados_juros['valor'] + $valor_juros;
    
		$valor_corrigido_multa_juros = $dados_juros['valor_multa_juros'] + $valor_juros;
    
		//monta o FOR
		for($i = 1; $i <= $dias_diferenca; $i++)
		{
      
			//echo "Dia " . $i . "<br>";
			//echo "Valor Juros do dia " . $valor_juros . "<br/>";
			//echo "Valor Corrigido a Pagar " . $valor_corrigido_juros . "<br/>";
			//echo "Valor Corrigido Multa e Juros " . $valor_corrigido_multa_juros . "<br/><br/>";                     
    
           
			$valor_corrigido_juros = $valor_corrigido_juros + $valor_juros;
			$valor_corrigido_multa_juros = $valor_corrigido_multa_juros + $valor_juros; 
    
		}
    
		//Atualiza o registro com os dados dos juros
		$atualiza_juros = mysql_query("UPDATE contas_receber SET
									  valor = '$valor_corrigido_juros',
									  valor_juros = '$valor_juros',
									  valor_multa_juros = '$valor_corrigido_multa_juros'
									  WHERE id = $conta_id");
    
		if ($dados_juros['boleto_id'] > 0)
		{
		
			//20/04/2011 - Modificado para receber o valor do dia atual como data do vencimento (bruna)
			//Se precisar voltar, coplocar data_vencimento = '$data_processa'
			//Atualiza o valor do boleto                                  
			$atualiza_boleto = mysql_query("UPDATE boleto SET
											valor_boleto = '$valor_corrigido_juros',
											data_vencimento = '$data_processa_1_banco',
											reajustado = 1,
											data_reajuste = '$data_processa_1_banco',
											data_atualizacao = '$data_atualizacao'
											WHERE conta_receber_id = $conta_id");                                                           
		}
  
	} 

}

//Atualiza o preço de venda com base no preço de custo
mysql_query("UPDATE parametros_sistema SET data_atualizacao_multa = '$hoje'");	

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Processamento de Juros e Multa das contas a receber</span>
		</td>
	</tr>
	<tr>
		<td>
			<img src="image/bt_espacohoriz.gif" width="100%" height="12" />
		</td>
	</tr>
</table>
<table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
	<tr>
		<td height="22" width="20" valign="top" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 4px; border-right: 0px">
			<img src="./image/bt_informacao.gif" border="0" />
		</td>
		<td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px; padding-top: 4px; padding-bottom: 4px">
			<strong>Processo de cálculo de juros e multa das contas a receber efetuado com sucesso !</strong><br/><br/>Contas a receber com multas aplicadas hoje: <span style="color: #990000;"><b><?php echo $numero_registros_multa ?></b></span><br/>Contas a receber com juros aplicados hoje: <span style="color: #990000;"><b><?php echo $numero_registros_juros ?></b></span><br/>Os juros foram calculados e aplicados sobre <span style="color: #990000;"><b><?php echo $dias_diferenca ?></b></span> dia(s) desde o último processamento em <span style="color: #990000;"><b><?php echo DataMySQLRetornar($data_processa) ?></b></span>. 
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
</table>
</td></tr><tr><td>
<br/>
<br/>
<input class="button" value="Voltar" type="button" name="btVoltar" onclick="wdCarregarFormulario('MeuPortal.php','conteudo')" style="width: 100px" />
<script>
  
  document.getElementById('atualiza').style.display = "none";
  
</script>