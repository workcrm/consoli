<?php

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require('Diretivas.php');

//Estabelece a conexão com o banco de dados
include './conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include './include/ManipulaDatas.php';

//pesquisa as diretivas do usuário
$sql = "SELECT id, nome FROM eventos_formando WHERE evento_id = 1027 order by nome";													  													  
							  
//Executa a query
$resultado = mysql_query($sql);

while ($dados_formando = mysql_fetch_array($resultado))
{

	echo "<br/><br/>ID: <b>" . $dados_formando[id] . "</b> - Nome: <b>" . $dados_formando[nome] . "</b>";
	
	$pessoaId = $dados_formando[id];
	
	//pesquisa as diretivas do usuário
	$sql_contas = "SELECT * FROM contas_receber WHERE pessoa_id = $pessoaId";													  													  
								  
	//Executa a query
	$resultado_contas = mysql_query($sql_contas);

	while ($dados_contas = mysql_fetch_array($resultado_contas))
	{
	
		$ContaId = $dados_contas[id];
		
		switch ($dados_contas[situacao]) 
		{
			case 1: $desc_situacao = "<b>Em Aberto</b>"; break;
			case 2: $desc_situacao = "<b>Pago</b>"; break;
		}
		
		echo "<br/><span style='margin-left:30px; font-family: courier'>Conta Receber: <b>" . $dados_contas[id] . "</b> - Docto: " . $dados_contas[nro_documento] . "</b> - Vencto: <b>" . DataMySQLRetornar($dados_contas[data_vencimento]) . "</b> - Sit: $desc_situacao</span>";
	
		//pesquisa as diretivas do usuário
		$sql_boleto = "SELECT * FROM boleto WHERE conta_receber_id = $ContaId";													  													  
									  
		//Executa a query
		$resultado_boleto = mysql_query($sql_boleto);

		while ($dados_boleto = mysql_fetch_array($resultado_boleto))
		{
		
			echo "<br/><span style='margin-left:166px; font-family: courier'>Boleto: <b>" . $dados_boleto[id] . "</b> - Nosso Numero: <b>" . $dados_boleto[nosso_numero] . "</b>";
		
			//Geracao do novo codigo dos boletos
			$semente = rand(100,999);
			
			$novo_nosso_numero = substr($dados_boleto[nosso_numero], 0, 7) . $semente . $dados_formando[id] . substr($dados_boleto[nosso_numero], 15, 2);
			$novo_numero_documento = substr($dados_boleto[nosso_numero], 0, 7) . $semente . $dados_formando[id];
			
			echo "<br/><span style='margin-left: 166px'>Novo Numero: <b>" . $novo_nosso_numero.  "</span></b>   - Novo Numero: <b>" . $novo_numero_documento . "</span></b>";
			
			//Atualiza o novo numero do boleto
			mysql_query("UPDATE BOLETO SET nosso_numero = '$novo_nosso_numero', numero_documento = '$novo_numero_documento' where id = $dados_boleto[id]");
		
		}
	}

}
?>