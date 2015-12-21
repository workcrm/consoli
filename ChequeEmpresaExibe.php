<?php 
###########
## Módulo para Exibição dos dados do cheque da empresa
## Criado: 25/08/2011 - Maycon Edinger
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

//Monta o sql para recuperar os dados do cheque
$sql  = "SELECT
  		che.id,
		che.conta_corrente_id,
		che.numero_cheque,
		che.data_emissao,
		che.pre_datado,
		che.bom_para,
		che.valor,
		che.status,
		che.conta_pagar_id,
		che.data_compensacao,
		cont.nome AS conta_corrente_nome,
		cont.agencia,
		cont.conta,
		cpag.descricao AS conta_pagar_nome
		FROM cheques_empresa che
		LEFT OUTER JOIN conta_corrente cont ON cont.id = che.conta_corrente_id
		LEFT OUTER JOIN contas_pagar cpag ON cpag.id = che.conta_pagar_id
  		WHERE che.id = $ChequeId";	
							   
//Executa a query
$resultado = mysql_query($sql);

$registros = mysql_num_rows($resultado);

//Monta o array dos dados
$dados = mysql_fetch_array($resultado);
	
$BomPara = DataMySQLRetornar($dados["bom_para"]);

$Emissao = DataMySQLRetornar($dados["data_emissao"]);

$valor = number_format($dados[valor],2,",",".");
	
if ($dados["pre_datado"] == 1)
{
		
  $marca_chk = "Sim";
		
} 
  
else 
  
{
		
  $marca_chk = "Não";
		
}

//Verifica a situação do boleto
switch ($dados["status"]) 
{
  
	case 1: 
		$desc_status = "<span style='color: #990000'>Emitido</span>"; 
	break;		  
	case 2: 
		$desc_status = "<span style='color: #6666CC'>Compensado</span>"; 
	break;
  
}
  		
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Detalhamento do Cheque da Empresa</span></td>
				</tr>
				<tr>
					<td colspan="5">
						<img src="image/bt_espacohoriz.gif" width="100%" height="12">
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php

echo "<br/><span style='font-family: courier; font-size: 10px'>
		<span style='color: #990000'>--------------------------------------[ <b>DADOS DO CHEQUE</b> ]---------------------------------------</span><br/> 
		<b>Número:</b> $dados[numero_cheque] <br/>
		<b>Valor:</b> R$ $valor <br/>
		<b>Conta-Corrente:</b> $dados[conta_corrente_nome] <br/>
		<b>Bom para:</b> $BomPara <br/>
		<b>Emitido em:</b> $Emissao <br/>
		<b>Pré-datado:</b> $marca_chk <br/>
		<b>Status:</b> $desc_status
		</span>";
  
if ($dados["conta_pagar_id"] > 0)
{
    
    //Monta o sql para recuperar os dados da conta
    $sql="SELECT 
		con.id,
		con.data,
		con.tipo_pessoa,
		con.pessoa_id,
		con.grupo_conta_id,
		con.subgrupo_conta_id,
		con.evento_id,
		con.descricao,
		con.nro_documento,
		con.condicao_pgto_id,
		con.valor,
		con.data_vencimento,
		con.situacao,
		con.data_pagamento,
		con.tipo_pagamento,
		con.cheque_id,
		con.valor_pago,
		con.observacoes,
		con.cadastro_timestamp,
		con.cadastro_operador_id,
		con.alteracao_timestamp,
		con.alteracao_operador_id,
		usu_cad.nome as operador_cadastro_nome, 
		usu_cad.sobrenome as operador_cadastro_sobrenome,
		usu_alt.nome as operador_alteracao_nome, 
		usu_alt.sobrenome as operador_alteracao_sobrenome,
		cat.nome as categoria_nome,
		gru.nome as grupo_nome,
		sub.nome as subgrupo_nome,
		cond.nome as condicao_pgto_nome,
		evento.nome as evento_nome

		FROM contas_pagar con
		LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
		LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
		LEFT OUTER JOIN categoria_conta cat ON con.categoria_id = cat.id 
		LEFT OUTER JOIN grupo_conta gru ON con.grupo_conta_id = gru.id 							 
		LEFT OUTER JOIN subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 							 
		LEFT OUTER JOIN condicao_pgto cond ON con.condicao_pgto_id = cond.id 							 
		LEFT OUTER JOIN eventos evento ON con.evento_id = evento.id 							 
					
		WHERE con.id = $dados[conta_pagar_id]";			
    			   
	//Executa a query
	$resultado = mysql_query($sql);
  
	//Monta o array dos dados
	$campos = mysql_fetch_array($resultado);
  
	//Efetua o switch para o campo de situacao
	switch ($campos[situacao]) 
	{
		case 1: 
			$desc_situacao = "<span style='color: #990000'><strong>Em aberto</strong></span>"; 
			$mostra_pagar = 1;
		break;
		case 2: 
			$desc_situacao = "<span style='color: blue'><strong>Pago</strong></span>"; 
			$mostra_pagar = 0;   
		break;
	}    
  
	//Caso a conta já tenha um valor recebido mas ainda está em aberta, então ela possui um recebimento parcial
	if ($campos[valor_pago] > 0 AND $campos[situacao] == 1)
	{
   
		$desc_situacao = "<span style='color: #018B0F'><strong>Pagamento Parcial</strong></span>";
		$mostra_pagar = 1; 
	
	}
  
	//Efetua o switch para o campo tipo de pessoa
	switch ($campos[tipo_pessoa]) 
	{
		case 1: 
			$desc_pessoa = "Cliente:"; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM clientes WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];
		break;
		case 2: 
			$desc_pessoa = "Fornecedor:"; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM fornecedores WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];	
		break;
		case 3: 
			$desc_pessoa = "Colaborador:"; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM colaboradores WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];	
		break;	
	}    
  
	//Efetua o switch para o campo tipo de pagamento
	switch ($campos[tipo_pagamento]) 
	{
		case 1: $desc_pago = "Dinheiro"; break;
		case 2: $desc_pago = "Cheque - Nº: " . $campos[cheque_numero]; break;
	}
  
	$data_pagar = DataMySQLRetornar($campos['data']);
	$valor_pagar = number_format($campos['valor'], 2, ',', '.');
	$valor_pago = number_format($campos['valor_pago'], 2, ',', '.');
	$saldo_pagar = number_format($campos[valor] - $campos[valor_pago], 2, ",", ".");
	$vencimento = DataMySQLRetornar($campos[data_vencimento]);

	echo "<br/><br/><p align='center'><img src='./image/bt_setas.png'></p>
	<br><span style='font-family: courier; font-size: 10px'>
	<span style='color: #990000'>--------------------------[ <b>CHEQUE JÁ UTILIZADO PARA CONTA A PAGAR</b> ]-------------------------</span><br/>
	<b>Data: </b>$data_pagar<br/>
	<b>Descrição: </b>$campos[descricao]<br/>
	<b>Conta-Caixa: </b>$campos[subgrupo_nome]<br/>
	<b>Centro de Custo: </b>$campos[grupo_nome]<br/>
	<b>Tipo de Pessoa/Sacado: </b> $desc_pessoa $nome_pessoa<br/>
	<b>Evento: </b> $campos[evento_nome]<br/>
	<b>Nº do Documento: </b>$campos[nro_documento]<br/>
	<b>Valor: </b>R$ $valor_pagar<br/> 
	<b>Data Vencimento: </b>$vencimento<br/>
	<b>Situação: </b>$desc_situacao<br/>
	<b>Valor Pago: </b>R$ $valor_pago<br/>
	<b>Saldo a Pagar: </b>R$ $saldo_pagar<br/>
	<b>Observações: </b>" . nl2br($campos[observacoes]) . "<br/> 
	"; 

}

?>