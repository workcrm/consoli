<?php 
###########
## M�dulo para Autentica��o de Usu�rio e Controle de sess�es
## Criado: 02/04/2007 - Maycon Edinger
## Alterado: Removido limite de execu��o do sistema (consoli pagou)
## Altera��es: 
###########

//Efetua a conex�o com o banco
include("./conexao/ConexaoMySQL.php");

//Inicia a sess�o
session_start();

//Pega os dados do formul�rio de login
$user_login = str_replace("'","",$_POST["user_login"]);
$user_senha = $_POST["user_senha"];

//Encripta a senha para compara��o
$senha = md5($user_senha);

// verificado login no banco de dados
$query = mysql_query("SELECT * FROM usuarios WHERE login = '$user_login' AND senha = '$senha'");

if (!$query) 
{

    die("Erro ao acessar a tabela de usuarios." . mysql_error());

}

// verificando se encontrou registros do login e senha no banco de dados.
if (mysql_num_rows($query) > 0) 
{
	//Monta o array com os dados do usu�rio
	$dados = mysql_fetch_array($query); 
  
	//Alimenta as vari�veis com o valor do array  
	$login = $dados['login'];
	$usuarioId = $dados['usuario_id'];
	$usuarioNome = $dados['nome'];
	$usuarioSobrenome = $dados['sobrenome'];
	$empresaId = $dados['empresa_id'];
	$dataAcesso = $dados['data_acesso'];
	$horaAcesso = $dados['hora_acesso'];
	$ipAcesso = $dados['ip_acesso'];
	$nivelAcesso = $dados['nivel_acesso'];
	$planoAcesso = $dados['plano_acesso'];	
	$usuarioDataCadastro = $dados['data_cadastro'];
  
	//Monta a vari�vel a data atual
	$data_login = date('Y-m-d'); 
	//Monta a vari�vel a hora atual
	$hora_login = date('H:i');
	//Monta a vari�vel do IP do usuario
	$ip_login = getenv("REMOTE_ADDR");
	
	//Grava na tabela de usu�rios os novos valores de data, hora e IP de acessso
	$grava_acesso = mysql_query("UPDATE 
								usuarios 
								SET  
								data_acesso = '$data_login',
								hora_acesso = '$hora_login',
								ip_acesso = '$ip_login'
								WHERE usuario_id = '$usuarioId'");
    
	//Monta e executa a query para pegar os dados da empresa que o usu�rio pertence
	$busca_empresa = mysql_query("SELECT razao_social, email FROM empresas WHERE id = '$empresaId'");
  
	if (!$query) 
	{
		
		die("Erro ao acessar a tabela de empresas." . mysql_error());
  
	}

	//Monta o array com os dados da empresa
	$dados_empresa = mysql_fetch_array($busca_empresa);

	//Alimenta as vari�veis com os dados vindos do array
	$empresaNome = $dados_empresa['razao_social'];
	$empresaEmail = $dados_empresa['email'];
    
	//Monta a semente para gerar a chave que ir� controlar a sess�o do usu�rio
	$chave = "1a2cf8gk68gj67gf784kh69fo6";
	
	//Encripta a chave
	$chave = md5($login . $chave . $ip . $horaAcesso);

	//Verifica a data para expira��o
	$data_expira = date('d/m/Y',mktime());
	
	//Caso a data de expira��o alcan�ar...
	//if ($data_expira == '08/12/2007'){
		//Faz o update na base de dados
		//$trava = mysql_query("UPDATE parametros_sistema SET grupo_id = '1'");
	//}
	
	//Procura ent�o se o sistema n�o est� travado
	$busca_trava = mysql_query("SELECT grupo_id FROM parametros_sistema");
	
	$dados_trava = mysql_fetch_array($busca_trava);
	
	//Verifica se est�
	if ($dados_trava["grupo_id"] == '1') 
	{
	
		$bloqueio = "1";

	} 
	
	else 
	
	{
	
		$bloqueio = "0";
	
	}

	//Registrando a session com um array com os dados recolhidos
	$_SESSION['WorkCrmLogin'] = array("usuarioId" => $usuarioId,
									"usuarioNome" => $usuarioNome,
									"usuarioSobrenome" => $usuarioSobrenome,
									"empresaId" => $empresaId,
									"empresaNome" => $empresaNome,
									"empresaEmail" => $empresaEmail,
									"nomeBanco" => $nomeBanco,
									"login" => $login,
									"chave" => $chave,
									"dataAcesso" => $dataAcesso,
									"horaAcesso" => $horaAcesso,
									"nivelAcesso" => $nivelAcesso,
									"planoAcesso" => $planoAcesso,
									"usuarioDataCadastro" => $usuarioDataCadastro,
									"bloqueio" => $bloqueio);

	//Verifica se o usu�rio nao est� bloqueado
	if ($dados[travado] == 1 OR $dados[ativo] == 0) 
	{
	  
	  header("location: login.php?Erro=Usu�rio com acesso bloqueado !&Solucao=Entre em contato com o administrador do sistema.");
	
	} 
	
	else 
	
	{
    
		//Executa a rotina de compensa��o de cheques do dia  
		//include "ChequeProcessaCompensacao.php";
		
		// redirecionando para o acesso do sistema
		header("location: sistema.php");
	
	}

} 

else 

{

    // redirecionando para o formulario de login com o erro.
    header("location: login.php?Erro=Usu�rio ou senha Inv�lida !&Solucao=Verifique seus dados e tente novamente.");

}
?>