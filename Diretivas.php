<?php 
###########
## M�dulo para Controle das Diretivas de Seguran�a do Sistema
## Criado: - Maycon Edinger
## Alterado: 27/02/2007
## Altera��es: 
## 27/02/2007 - Implementado a inclus�o do campo email ao session e o armazenamento do IP do usu�rio
###########

//Inicia a sess�o
session_start();

//Caso exista a sess�o existente no servidor
if (isset($_SESSION['WorkCrmLogin'])) 
{
    
  //Monta as vari�veis buscando os valores dentro do arquivo de sess�o
  $login = $_SESSION['WorkCrmLogin']['login'];
  $usuarioId = $_SESSION['WorkCrmLogin']['usuarioId'];
  $usuarioNome = $_SESSION['WorkCrmLogin']['usuarioNome'];
  $usuarioSobrenome = $_SESSION['WorkCrmLogin']['usuarioSobrenome'];
  $nomeBanco = $_SESSION['WorkCrmLogin']['nomeBanco'];
  $empresaId = $_SESSION['WorkCrmLogin']['empresaId'];
  $empresaNome = $_SESSION['WorkCrmLogin']['empresaNome'];
  $empresaEmail = $_SESSION['WorkCrmLogin']['empresaEmail'];
  $dataAcesso = $_SESSION['WorkCrmLogin']['dataAcesso'];
  $horaAcesso = $_SESSION['WorkCrmLogin']['horaAcesso']; 
  $nivelAcesso = $_SESSION['WorkCrmLogin']['nivelAcesso'];
  $planoAcesso = $_SESSION['WorkCrmLogin']['planoAcesso'];   
  $usuarioDataCadastro = $_SESSION['WorkCrmLogin']['usuarioDataCadastro'];   
  $bloqueio = $_SESSION['WorkCrmLogin']['bloqueio'];

  //Monta a semente para gerar a chave que ir� controlar a sess�o do usu�rio
  $chave = "1a2cf8gk68gj67gf784kh69fo6";
    
  //Monta a vari�vel do IP do usuario
  $ip = $_SESSION['REMOTE_ADDR'];

  //Verifica se a sess�o ainda � v�lida e pode ser ativa
  if ($_SESSION['WorkCrmLogin']['chave'] != md5($login . $chave . $ip . $horaAcesso)) 
  {

    //Caso n�o estiver mais ativa, volta e informa o erro ao usu�rio
    header("Location: login.php?Erro=Sess�o Expirada !&Solucao=Efetue um novo login no sistema.");

  }
                                     
} 

else 
{
    
  header("location: login.php?Erro=Acesso n�o autorizado !&Solucao=A sess�o pode ter expirado. Efetue um novo login.");

}
?>
