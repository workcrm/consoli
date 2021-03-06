<?php 
###########
## M�dulo para Exibi��o dos dados do cheque de terceiro
## Criado: 25/08/2011 - Maycon Edinger
## Alterado: 
## Altera��es:
###########

//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "./include/ManipulaDatas.php";

//Recupera o id do cheque
$ChequeId = $_GET["ChequeId"];

//Monta o sql para recuperar os dados do cheque
$sql  = "SELECT
  		che.id,
        che.numero_cheque,
  		che.bom_para,
  		che.data_recebimento,
  		che.favorecido,
  		che.agencia,
  		che.pre_datado,
  		che.valor,
  		che.conta,
        che.status,
		che.disposicao,
        che.conta_receber_id,
        che.conta_pagar_id,
  		ban.nome as banco_nome			
  		FROM cheques che 
  		LEFT OUTER JOIN bancos ban ON ban.id = che.banco_id
  		WHERE che.id = $ChequeId";	
							   
//Executa a query
$resultado = mysql_query($sql);

$registros = mysql_num_rows($resultado);

//Monta o array dos dados
$dados = mysql_fetch_array($resultado);
	
$BomPara = DataMySQLRetornar($dados["bom_para"]);

$RecebidoEm = DataMySQLRetornar($dados["data_recebimento"]);

$valor = number_format($dados[valor],2,",",".");
	
if ($dados["pre_datado"] == 1)
{
		
  $marca_chk = "Sim";
		
} 
  
else 
  
{
		
  $marca_chk = "N�o";
		
}

//Verifica a situa��o do boleto
switch ($dados["status"]) 
{
  
	case 1: 
		$desc_status = "<span style='color: #990000'>Recebido</span>"; 
	break;		  
	case 2: 
		$desc_status = "<span style='color: #6666CC'>Compensado</span>"; 
	break;
	case 3: 
		$desc_status = "<span style='color: #6666CC'>Devolvido</span>"; 
		
		//Verifica a situa��o do boleto
		switch ($dados["disposicao"]) 
		{
		  
			//Se for 1 
			case 1: $desc_status .= " - Primeiro Contato"; break;		
			//Se for 2
			case 2: $desc_status .= " - Em Negocia��o"; break;	
			//Se for 3
			case 3: $desc_status .= " - Reapresentado"; break;		
			//Se for 4
			case 4: $desc_status .= " - Pago"; break;
			//Se for 5
			case 5: $desc_status .= " - Para Registrar"; break;
			//Se for 6 
			case 6: $desc_status .= " - No SPC"; break;
			//Se for 7 
			case 7: $desc_status .= " - N�o Pode SPC"; break;
			//Se for 8 
			case 8: $desc_status .= " - SPC Pago"; break;
			//Se for 9 
			case 9: $desc_status .= " - Devolvido ao Titular"; break;
			//Se for 10
			case 10: $desc_status .= " - Cobran�a Judicial"; break;
		  
		}
		
	break;
  
}
  		
$ValorContaReceber =  $dados["conta_receber_id"];
$ValorContaPagar = $dados["conta_pagar_id"];

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td width="440"><img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Detalhamento do Cheque de Terceiro</span></td>
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
<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td width="100" style="PADDING-BOTTOM: 2px">
			<form name="frmContaExibe" action="#">
				<input name="btnEditarConta" type="button" class="button" title="Edita este Cheque" value="Editar Cheque" onclick="wdCarregarFormulario('ChequeAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
			</form>
		</td>
		<td style="PADDING-BOTTOM: 2px">
			<?php
				
				//Verifica o n�vel de acesso do usu�rio
			
				//Exibe o bot�o de excluir
				echo "<form id='exclui' name='exclui' action='ProcessaExclusao.php' method='post'><input class=button title='Exclui este Cheque [Alt+X]' accessKey='X' onClick='return confirm(\"Confirma a exclus�o deste Cheque ?\")' type='submit' value='Excluir' name='Delete'><input name='Id' type='hidden' value=$dados[id] /><input name='Modulo' type='hidden' value='cheques' /></form>";
			
			?>
		</td>
	</tr>
</table>
<?php

echo "<br/><span style='font-family: courier; font-size: 10px'>
		<span style='color: #990000'>--------------------------------------[ <b>DADOS DO CHEQUE</b> ]---------------------------------------</span><br/> 
		<b>N�mero:</b> $dados[numero_cheque] <br/>
		<b>Valor:</b> R$ $valor <br/>
		<b>Titular:</b> $dados[favorecido] <br/>
		<b>Bom para:</b> $BomPara <br/>
		<b>Banco:</b> $dados[banco_nome] <br/>
		<b>Ag�ncia:</b> $dados[agencia] <br/>
		<b>Conta:</b> $dados[conta] <br/>
		<b>Recebido em:</b> $RecebidoEm <br/>
		<b>Pr�-datado:</b> $marca_chk <br/>
		<b>Status:</b> $desc_status
		</span>";


//Verifica os dados da conta a receber originadora do cheque
if ($ValorContaReceber > 0)
{
  
	//Monta o sql para recuperar os dados da conta
	$sql = "SELECT 
			con.id,
			con.data,
			con.tipo_pessoa,
			con.pessoa_id,
			con.grupo_conta_id,
			con.subgrupo_conta_id,
			con.evento_id,
			con.formando_id,
			con.descricao,
			con.nro_documento,
			con.condicao_pgto_id,
			con.valor_original,
			con.valor,
			con.valor_boleto,
			con.taxa_multa,
			con.taxa_juros,
			con.data_vencimento,
			con.situacao,
			con.data_recebimento,
			con.tipo_recebimento,
			con.cheque_id,
			con.valor_recebido,
			con.observacoes,
			con.cadastro_timestamp,
			con.cadastro_operador_id,
			con.alteracao_timestamp,
			con.alteracao_operador_id,
			con.boleto_id,
			usu_cad.nome as operador_cadastro_nome, 
			usu_cad.sobrenome as operador_cadastro_sobrenome,
			usu_alt.nome as operador_alteracao_nome, 
			usu_alt.sobrenome as operador_alteracao_sobrenome,
  			cat.nome as categoria_nome,
  			gru.nome as grupo_nome,
  			sub.nome as subgrupo_nome,
  			cond.nome as condicao_pgto_nome,
  			evento.nome as evento_nome,
			formando.nome as formando_nome
			FROM contas_receber con
			LEFT OUTER JOIN usuarios usu_cad ON con.cadastro_operador_id = usu_cad.usuario_id 
			LEFT OUTER JOIN usuarios usu_alt ON con.alteracao_operador_id = usu_alt.usuario_id
  			LEFT OUTER JOIN categoria_conta cat ON con.categoria_id = cat.id 
  			LEFT OUTER JOIN grupo_conta gru ON con.grupo_conta_id = gru.id 							 
  			LEFT OUTER JOIN subgrupo_conta sub ON con.subgrupo_conta_id = sub.id 							 
  			LEFT OUTER JOIN condicao_pgto cond ON con.condicao_pgto_id = cond.id 							 
  			LEFT OUTER JOIN eventos evento ON con.evento_id = evento.id 
			LEFT OUTER JOIN eventos_formando formando ON con.formando_id = formando.id							 					
			WHERE con.id = $dados[conta_receber_id]";	
  						   
	//Executa a query
	$resultado = mysql_query($sql);
  
	//Monta o array dos dados
	$campos = mysql_fetch_array($resultado);

	//Efetua o switch para o campo de situacao
	switch ($campos['situacao']) 
	{
		case 1: $desc_situacao = "<span style='color: #990000'><strong>Em aberto</strong></span>"; break;
		case 2: $desc_situacao = "<span style='color: blue'><strong>Recebida</strong></span>"; break;
	}


	//Caso a conta j� tenha um valor recebido mas ainda est� em aberta, ent�o ela possui um recebimento parcial
	if ($campos['valor_recebido'] > 0 AND $campos['situacao'] == 1)
	{
   
		$desc_situacao = "<span style='color: #018B0F'><strong>Recebimento Parcial</strong></span>"; 
    
	}

	//Efetua o switch para o campo tipo de pessoa
	switch ($campos[tipo_pessoa]) 
	{
		case 1: 
			$desc_pessoa = 'Cliente:'; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM clientes WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];
		break;
		case 2: 
			$desc_pessoa = 'Fornecedor:'; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM fornecedores WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];	
		break;
		case 3: 
			$desc_pessoa = 'Colaborador:'; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM colaboradores WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];	
		break;
		case 4: 
			$desc_pessoa = 'Formando:'; 
			$busca_pessoa = mysql_query("SELECT id, nome FROM eventos_formando WHERE id = '$campos[pessoa_id]'");
			$dados_pessoa = mysql_fetch_array($busca_pessoa);
			$id_pessoa = $dados_pessoa[id];
			$nome_pessoa = $dados_pessoa[nome];	
		break;	
	} 
  
	$data_receber = DataMySQLRetornar($campos['data']);
	$valor_receber = number_format($campos['valor_original'], 2, ',', '.');
	$valor_boleto_receber = number_format($campos['valor_boleto'], 2, ',', '.');
	$valor_total = number_format($campos['valor'], 2, ',', '.');
	$vencimento = DataMySQLRetornar($campos[data_vencimento]);
  
	echo "<br/><br/><span style='font-family: courier; font-size: 10px'>
	<span style='color: #990000'>----------------------------[ <b>CHEQUE ORIGINADO DE CONTA A RECEBER</b> ]--------------------------</span><br/>
	<b>Data: </b>$data_receber<br/>
	<b>Descri��o: </b>$campos[descricao]<br/>
	<b>Conta-Caixa: </b>$campos[subgrupo_nome]<br/>
	<b>Centro de Custo: </b>$campos[grupo_nome]<br/>
	<b>Tipo de Pessoa/Sacado: </b> $desc_pessoa $nome_pessoa<br/>
	<b>Evento: </b>";
  
	//Verifica se h� algum formando associado a conta
	if ($campos['evento_id'] != 0)
	{
    
		//Imprime os dados do evento
		echo $campos['evento_nome']; 
    
	} 
   
	else 
   
	{
    
		echo 'Nenhum evento associado a esta conta';
    
	}
   
	echo "<br/>
	<b>Formando: </b>";
   
	//Verifica se h� algum formando associado a conta
	if ($campos['formando_id'] != 0)
	{
    
		//Imprime os dados do formando
		echo $campos['formando_nome']; 
    
	} 
   
	else 
   
	{
    
		echo 'Nenhum formando associado a esta conta';
    
	}
   
	echo "<br/>
	<b>N� do Documento: </b>$campos[nro_documento]<br/>
	<b>Valor: </b>R$ $valor_receber<br/>
	<b>Custo do Boleto: </b>R$ $valor_boleto_receber<br/>
	<b>Taxa Multa Atraso: </b>$campos[taxa_multa] %<br/>
	<b>Taxa Juros M�s: </b>$campos[taxa_juros] %<br/>
	<b>Total a Receber: </b>R$ $valor_total<br/>
	<b>Data Vencimento: </b>$vencimento<br/>
	<b>Situa��o: </b>$desc_situacao<br/>
	<b>Observa��es: </b>" . nl2br($campos[observacoes]) . "<br/>
	</span>";
  
 }
 
if ($ValorContaPagar > 0)
{

	//Monta o sql para recuperar os dados da conta
	$sql = "SELECT 
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
  
	//Caso a conta j� tenha um valor recebido mas ainda est� em aberta, ent�o ela possui um recebimento parcial
	if ($campos[valor_pago] > 0 AND $campos[situacao] == 1)
	{
   
		$desc_situacao = "<span style='color: #018B0F'><strong>Pagamento Parcial</strong></span>";
		$mostra_pagar = 1; 
	
	}
  
  //Efetua o switch para o campo tipo de pessoa
  switch ($campos[tipo_pessoa]) {
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
	case 2: $desc_pago = "Cheque - N�: " . $campos[cheque_numero]; break;
  }
  
$data_pagar = DataMySQLRetornar($campos['data']);
$valor_pagar = number_format($campos['valor'], 2, ',', '.');
$valor_pago = number_format($campos['valor_pago'], 2, ',', '.');
$saldo_pagar = number_format($campos[valor] - $campos[valor_pago], 2, ",", ".");
$vencimento = DataMySQLRetornar($campos[data_vencimento]);

echo "<br/><br/><p align='center'><img src='./image/bt_setas.png'></p>
<br><span style='font-family: courier; font-size: 10px'>
<span style='color: #990000'>--------------------------[ <b>CHEQUE J� UTILIZADO PARA CONTA A PAGAR</b> ]-------------------------</span><br/>";
?>

<input class="button" title="Exibe os detalhes desta conta a pagar" name="btnVoltar" type="button" id="btnVoltar" value="Exibir Conta a Pagar" onclick="wdCarregarFormulario('ContaPagarExibe.php?ContaId=<?php echo $ValorContaPagar ?>&headers=1','conteudo')" />

<?php

echo "<br/><br/>
<b>Data: </b>$data_pagar<br/>
<b>Descri��o: </b>$campos[descricao]<br/>
<b>Conta-Caixa: </b>$campos[subgrupo_nome]<br/>
<b>Centro de Custo: </b>$campos[grupo_nome]<br/>
<b>Tipo de Pessoa/Sacado: </b> $desc_pessoa $nome_pessoa<br/>
<b>Evento: </b> $campos[evento_nome]<br/>
<b>N� do Documento: </b>$campos[nro_documento]<br/>
<b>Valor: </b>R$ $valor_pagar<br/> 
<b>Data Vencimento: </b>$vencimento<br/>
<b>Situa��o: </b>$desc_situacao<br/>
<b>Valor Pago: </b>R$ $valor_pago<br/>
<b>Saldo a Pagar: </b>R$ $saldo_pagar<br/>
<b>Observa��es: </b><br/>" . nl2br($campos[observacoes]) . "<br/> 
"; 

}

?>