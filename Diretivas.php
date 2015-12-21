<?php 
###########
## Módulo para Controle das Diretivas de Segurança do Sistema
## Criado: - Maycon Edinger
## Alterado: 27/02/2007
## Alterações: 
## 27/02/2007 - Implementado a inclusão do campo email ao session e o armazenamento do IP do usuário
###########

//Inicia a sessão
session_start();

//Caso exista a sessão existente no servidor
if (isset($_SESSION['WorkCrmLogin'])) 
{
    
  //Monta as variáveis buscando os valores dentro do arquivo de sessão
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

  //Monta a semente para gerar a chave que irá controlar a sessão do usuário
  $chave = "1a2cf8gk68gj67gf784kh69fo6";
    
  //Monta a variável do IP do usuario
  $ip = $_SESSION['REMOTE_ADDR'];

  //Verifica se a sessão ainda é válida e pode ser ativa
  if ($_SESSION['WorkCrmLogin']['chave'] != md5($login . $chave . $ip . $horaAcesso)) 
  {

    //Caso não estiver mais ativa, volta e informa o erro ao usuário
    header("Location: login.php?Erro=Sessão Expirada !&Solucao=Efetue um novo login no sistema.");

  }
                                     
} 

else 
{
    
  header("location: login.php?Erro=Acesso não autorizado !&Solucao=A sessão pode ter expirado. Efetue um novo login.");

}
?>
