<?php 
###########
## Mѓdulo para Autenticaчуo de Usuсrio e Controle de sessѕes
## Criado: 02/04/2007 - Maycon Edinger
## Alterado: Removido limite de execuчуo do sistema (consoli pagou)
## Alteraчѕes: 
###########

//Efetua a conexуo com o banco
include("./conexao/ConexaoMySQL.php");

//Inicia a sessуo
session_start();

//Pega os dados do formulсrio de login
$user_login = str_replace("'","",$_POST["user_login"]);
$user_senha = $_POST["user_senha"];

//Encripta a senha para comparaчуo
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
	//Monta o array com os dados do usuсrio
	$dados = mysql_fetch_array($query); 
  
	//Alimenta as variсveis com o valor do array  
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
  
	//Monta a variсvel a data atual
	$data_login = date('Y-m-d'); 
	//Monta a variсvel a hora atual
	$hora_login = date('H:i');
	//Monta a variсvel do IP do usuario
	$ip_login = getenv("REMOTE_ADDR");
	
	//Grava na tabela de usuсrios os novos valores de data, hora e IP de acessso
	$grava_acesso = mysql_query("UPDATE 
								usuarios 
								SET  
								data_acesso = '$data_login',
								hora_acesso = '$hora_login',
								ip_acesso = '$ip_login'
								WHERE usuario_id = '$usuarioId'");
    
	//Monta e executa a query para pegar os dados da empresa que o usuсrio pertence
	$busca_empresa = mysql_query("SELECT razao_social, email FROM empresas WHERE id = '$empresaId'");
  
	if (!$query) 
	{
		
		die("Erro ao acessar a tabela de empresas." . mysql_error());
  
	}

	//Monta o array com os dados da empresa
	$dados_empresa = mysql_fetch_array($busca_empresa);

	//Alimenta as variсveis com os dados vindos do array
	$empresaNome = $dados_empresa['razao_social'];
	$empresaEmail = $dados_empresa['email'];
    
	//Monta a semente para gerar a chave que irс controlar a sessуo do usuсrio
	$chave = "1a2cf8gk68gj67gf784kh69fo6";
	
	//Encripta a chave
	$chave = md5($login . $chave . $ip . $horaAcesso);

	//Verifica a data para expiraчуo
	$data_expira = date('d/m/Y',mktime());
	
	//Caso a data de expiraчуo alcanчar...
	//if ($data_expira == '08/12/2007'){
		//Faz o update na base de dados
		//$trava = mysql_query("UPDATE parametros_sistema SET grupo_id = '1'");
	//}
	
	//Procura entуo se o sistema nуo estс travado
	$busca_trava = mysql_query("SELECT grupo_id FROM parametros_sistema");
	
	$dados_trava = mysql_fetch_array($busca_trava);
	
	//Verifica se estс
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

	//Verifica se o usuсrio nao estс bloqueado
	if ($dados[travado] == 1 OR $dados[ativo] == 0) 
	{
	  
	  header("location: login.php?Erro=Usuсrio com acesso bloqueado !&Solucao=Entre em contato com o administrador do sistema.");
	
	} 
	
	else 
	
	{
    
		//Executa a rotina de compensaчуo de cheques do dia  
		//include "ChequeProcessaCompensacao.php";
		
		// redirecionando para o acesso do sistema
		header("location: sistema.php");
	
	}

} 

else 

{

    // redirecionando para o formulario de login com o erro.
    header("location: login.php?Erro=Usuсrio ou senha Invсlida !&Solucao=Verifique seus dados e tente novamente.");

}
?>