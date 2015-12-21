<?php

//Determina a origem da exibi��o do boleto
$Origem = $_GET["OID"];

//Determina o tipo de conex�o
if ($Origem == "OLN")
{
  
  //Carrega os dados da conex�o ONline
  //Dados do servidor remoto
  $Server_atu = "myadmin.softhouse.com.br";
  //$Server_atu = "localhost";
  $Login_atu = "consolieventos";
  //$Login_atu = "root";
  $Senha_atu = "consoli2010";
  //$Senha_atu = "";
  $DB_atu = "consolieventos";
  //$DB_atu = "workeventos";
  
  //Conecta ao banco de dados online
  //Define a sting de conex�o
  $conexao = @mysql_connect($Server_atu,$Login_atu,$Senha_atu) or die('Nao foi possivel se conectar com o banco de dados do servidor de destino !');
  
  //Conecta ao banco de dados principal
  $base = @mysql_select_db($DB_atu) or die("Nao foi possivel selecionar a base: $DB_atu no servidor de destino !");

} 
else 
{
  
  //Carrega os dados do servidor local da consoli
  
  //Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
  //Processa as diretivas de seguran�a 
  require("../Diretivas.php");
  
  //Estabelece a conex�o com o banco de dados
  include "../conexao/ConexaoMySQL.php";

}

//Inclui o arquivo para manipula��o de datas
include "../include/ManipulaDatas.php";

//Captura a vari�vel com o tipo do boleto
$TipoBol = $_GET["TipoBol"];

//Verifica o tipo de boleto
//Caso for visualiza��o normal com id simples
if ($TipoBol == 1)
{
  
  //Pega o id normal do boleto
  $BoletoId = $_GET["BoletoId"];

//Caso for com o id criptografado  
} 
else if ($TipoBol == 2)
{
  
  //Pega o id do boleto pelo hash
  $BoletoId = $_GET["BID"];
  
}

//Busca os dados dos parametros do boleto nas preferencias
$sql_parametros = mysql_query("SELECT * FROM parametros_sistema");
		
//Monta o array com os dados
$dados_parametros = mysql_fetch_array($sql_parametros);


//Verifica o tipo de boleto
//Caso for visualiza��o normal com id simples
if ($TipoBol == 1)
{

  //Busca os dados dos parametros do boleto nas preferencias usando um id simples
  $sql_boleto = mysql_query("SELECT * FROM boleto WHERE id = $BoletoId");
  
//Caso for com o id criptografado  
} 
else if ($TipoBol == 2)
{
  
  //Busca os dados dos parametros do boleto nas preferencias usando um id hash
  $sql_boleto = mysql_query("SELECT * FROM WORK_boleto WHERE id_hash = '$BoletoId'");  
}
  		
//Monta o array com os dados do boleto
$dados_boleto = mysql_fetch_array($sql_boleto);


//***********************************
//DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 0;
//$taxa_boleto = $dados_parametros["boleto_taxa"];
//$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = $dados_boleto['valor_boleto']; // Valor - REGRA: Sem pontos na milhar e tanto faz com ".' ou ',' ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(',', '.',$valor_cobrado);
//$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');
$valor_boleto = number_format($valor_cobrado, 2, ',', '');
$valor_boleto_formata = number_format($valor_cobrado, 2, ',', '.');

$dadosboleto['nosso_numero'] = substr($dados_boleto['nosso_numero'],7,10);
$dadosboleto['numero_documento'] = substr($dados_boleto['numero_documento'],7,10);	// Num do pedido ou do documento
$dadosboleto['data_vencimento'] = DataMySQLRetornar($dados_boleto['data_vencimento']); // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto['data_documento'] = DataMySQLRetornar($dados_boleto['data_documento']); // Data de emiss�o do Boleto
$dadosboleto['data_processamento'] = DataMySQLRetornar($dados_boleto['data_processamento']); // Data de processamento do boleto (opcional)
$dadosboleto['valor_boleto'] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula
$dadosboleto['valor_boleto_formata'] = $valor_boleto_formata; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto['sacado'] = $dados_boleto['sacado'];
$dadosboleto['endereco1'] = $dados_boleto['endereco1'];
$dadosboleto['endereco2'] = $dados_boleto['endereco2'];

// INFORMACOES PARA O CLIENTE
$dadosboleto['demonstrativo1'] = $dados_boleto['demonstrativo1'];
$dadosboleto['demonstrativo2'] = $dados_boleto['demonstrativo2'];
$dadosboleto['demonstrativo3'] = $dados_boleto['demonstrativo3'];

// INSTRU��ES PARA O CAIXA
$dadosboleto['instrucoes1'] = $dados_boleto['instrucoes1'];
$dadosboleto['instrucoes2'] = $dados_boleto['instrucoes2'];
$dadosboleto['instrucoes3'] = $dados_boleto['instrucoes3'];
$dadosboleto['instrucoes4'] = $dados_boleto['instrucoes4'];

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto['quantidade'] = $dados_boleto['quantidade'];
$dadosboleto['valor_unitario'] = $dados_boleto['valor_unitario'];;
$dadosboleto['aceite'] = $dados_boleto['aceite'];		
$dadosboleto['especie'] = $dados_boleto['especie'];
$dadosboleto['especie_doc'] = $dados_boleto['especie_doc'];


// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - BANCO DO BRASIL
$dadosboleto['agencia'] = $dados_parametros['boleto_agencia']; // Num da agencia, sem digito
$dadosboleto['conta'] = $dados_parametros['boleto_conta']; 	// Num da conta, sem digito

// DADOS PERSONALIZADOS - BANCO DO BRASIL
$dadosboleto['convenio'] = $dados_parametros['boleto_convenio'];  // Num do conv�nio - REGRA: 6 ou 7 ou 8 d�gitos
$dadosboleto['contrato'] = $dados_parametros['boleto_contrato']; // Num do seu contrato
$dadosboleto['carteira'] = $dados_parametros['boleto_carteira'];
$dadosboleto['variacao_carteira'] = $dados_parametros['boleto_var_carteira'];  // Varia��o da Carteira, com tra�o (opcional)

// TIPO DO BOLETO
$dadosboleto['formatacao_convenio'] = $dados_parametros['boleto_formato_convenio']; // REGRA: 8 p/ Conv�nio c/ 8 d�gitos, 7 p/ Conv�nio c/ 7 d�gitos, ou 6 se Conv�nio c/ 6 d�gitos
$dadosboleto['formatacao_nosso_numero'] = $dados_parametros['boleto_formato_nosso_numero']; // REGRA: Usado apenas p/ Conv�nio c/ 6 d�gitos: informe 1 se for NossoN�mero de at� 5 d�gitos ou 2 para op��o de at� 17 d�gitos

/*
#################################################
DESENVOLVIDO PARA CARTEIRA 18

- Carteira 18 com Convenio de 8 digitos
  Nosso n�mero: pode ser at� 9 d�gitos

- Carteira 18 com Convenio de 7 digitos
  Nosso n�mero: pode ser at� 10 d�gitos

- Carteira 18 com Convenio de 6 digitos
  Nosso n�mero:
  de 1 a 99999 para op��o de at� 5 d�gitos
  de 1 a 99999999999999999 para op��o de at� 17 d�gitos

#################################################
*/


// SEUS DADOS
$dadosboleto['identificacao'] = 'Consoli Eventos';
$dadosboleto['cpf_cnpj'] = '';
$dadosboleto['endereco'] = 'Rua Sao Bento, 289 - Bairro Progresso';
$dadosboleto['cidade_uf'] = '89.163-656 - Rio do Sul - SC';
$dadosboleto['telefone'] = '(47) 3522-1336 / 3521-4024 - boletos@consolieventos.com.br';
$dadosboleto['cedente'] = 'Consoli Eventos';

//Verifica se o boleto foi reajustado
if ($dados_boleto['reajustado'] == 1)
{
  
  //Mensagem que o boleto est� reajustado
  $dadosboleto['imprime_reajuste'] = 1;
  $dadosboleto['data_reajuste'] = DataMySQLRetornar($dados_boleto['data_reajuste']);
  
}

// N�O ALTERAR!
include('include/funcoes_bb.php'); 
include('include/layout_bb.php');

//Determina o tipo de conex�o
if ($Origem == 'OLN')
{
?>
  <script language="javascript">
  		window.print();
  </script>
<?php
}
?>