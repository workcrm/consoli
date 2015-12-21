<?php
###########
## M�dulo para montagem do formul�rio de AR
## Criado: 13/09/2008 - Maycon Edinger
## Alterado: 
## Altera��es: 
##
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conex�o com o servidor
include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipula��o de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$TipoPessoa = $_GET['TipoPessoa'];
$PessoaId = $_GET['PessoaId']; 

//Chama a classe para gerar o PDF
class PDF extends FPDF
{
//Cabe�alho do relat�rio
function Header()
{    	

  //Ajusta a fonte
  $this->SetFont('Arial','',9);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();	    
	$this->SetFont('Arial','',10);
  $this->Cell(0,4,'Emiss�o do Formul�rio de AR',0,0,'L');	  
	
  //Imprime o logotipo da empresa
	$this->Image('../image/logo_correios.jpg',9,70,40);
  
  //Line break
  $this->Ln(6);
}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Work Labs - maycon@worklabs.com.br');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Formul�rio de AR');
$pdf->SetSubject('Relat�rio gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a p�gina do relat�rio
$pdf->AddPage();

//Busca os dados da pessoa conforme o tipo de pessoa informado
//Verifica se � formando

if ($TipoPessoa == 1)
{
  
  //verifica os formandos j� cadastrados para este evento
  $sql_formando = mysql_query("SELECT 
  														form.nome,
  														form.cpf,
  														form.endereco,
  														form.complemento,
  														form.bairro,
  														form.uf,
  														form.cep,
  														form.observacoes,
  														cid.nome as cidade_nome
  														FROM eventos_formando form
  														LEFT OUTER JOIN cidades cid ON cid.id = form.cidade_id
  														WHERE form.id = $PessoaId");
  
  //Verifica o numero de registros retornados
  $registros = mysql_num_rows($sql_formando);

  //Verifica a quantidade de registros
  if ($registros == 0 ) 
  {
  	
    //Exibe a mensagem que n�o foram encontrados registros
  	$pdf->SetFont('Arial', 'B', 9);
  	$pdf->Cell(0,6, 'N�o h� formandos cadastrados para este evento !',1,0,'L');
  
  //Caso tiver
  } 
  
  else 
  
  {
	
  	$dados_formando = mysql_fetch_array($sql_formando);
  
  	$total_formando = 0;
  		
  	//Imprime os dados do formando	  
  	$pdf->SetFont('Arial','B',12);
    $pdf->Cell(110,6, $dados_formando['nome'],'LTR',0,'L');
    $pdf->ln();
    $pdf->SetFont('Arial','B',9);        
    $pdf->Cell(110,5, 'CPF: ' . $dados_formando['cpf'],'LR',0,'L');
    $pdf->ln();
    $pdf->Cell(110,5, $dados_formando['endereco'],'LR',0,'L');
    $pdf->ln();
    $pdf->Cell(110,5, $dados_formando['bairro'],'LR',0,'L');
    $pdf->ln();
    $pdf->Cell(110,5, $dados_formando['cidade_nome'] . ' - ' . $dados_formando['uf'] ,'LR',0,'L');
    $pdf->ln();
    $pdf->Cell(110,5, 'CEP: ' . $dados_formando['cep'],'LBR',0,'L');                		 		  	
	 
    $pdf->ln(25);
    
    $pdf->SetX(50);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(76,8, 'AVISO DE RECEBIMENTO',1,0,'C');
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(14,8, 'AR',1,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,8, ' DATA DA POSTAGEM:','LTR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFillColor(178,178,178);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'DESTINAT�RIO:',1,0,'L',1);
    $pdf->Cell(0,5, ' ','LBR',0,'L');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'NOME:','LTR',0,'L');
    $pdf->Cell(0,5, 'UNIDADE DE POSTAGEM:','LTR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5, $dados_formando['nome'],'LBR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'ENDERE�O:','LTR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5, $dados_formando['endereco'],'LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, $dados_formando['complemento'],'LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, $dados_formando['bairro'],'LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, $dados_formando['cidade_nome'] . ' - ' . $dados_formando['uf'],'LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'CPF: ' . $dados_formando['cpf'],'LBR',0,'L');
    $pdf->Cell(0,5, ' ','LBR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,6, 'CEP: ','LB',0,'L');
    $pdf->SetX(20);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,6, $dados_formando['cep'],'BR',0,'L');
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,6, 'CIDADE/UF: ','LB',0,'L');
    $pdf->SetX(62);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(78,6, $dados_formando['cidade_nome'] . ' - ' . $dados_formando['uf'],'BR',0,'L');
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,6, 'UNIDADE DE ENTREGA:','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFillColor(178,178,178);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'ENDERE�O PARA DEVOLU��O DO AR:',1,0,'L',1);
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'NOME:','LTR',0,'L');
    $pdf->Cell(0,5, '','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5, 'CONSOLI EVENTOS LTDA','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'ENDERE�O:','LTR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(130,5, 'Rua Josephina Ferrari','LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'N�mero 81','LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'Santa Rita','LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,6, 'CEP: ','LTB',0,'L');
    $pdf->SetX(20);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(20,6, '89.162-408','TBR',0,'L');
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(40,6, 'CIDADE/UF: ','LTB',0,'L');
    $pdf->SetX(62);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(78,6, 'RIO DO SUL - SC','TBR',0,'L');
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,6, 'R�BRICA E MAT. DO CARTEIRO:','LTR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, 'DECLARA��O DO CONTE�DO (SUJEITO A VERIFICA��O):','LR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, ' ','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFillColor(178,178,178);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(65,5, 'TENTATIVAS DE ENTREGA:',1,0,'C',1);
    $pdf->Cell(65,5, 'MOTIVO DE DEVOLU��O:',1,0,'C',1);
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',12); 
    $pdf->Cell(65,8, '1a:       /      /            ____:____ h','LR',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'L');
    $pdf->SetFont('Arial','',9); 
    $pdf->Cell(26,5, 'Mudou-se','LBR',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'L');
    $pdf->Cell(27,5, 'Recusado','LBR',0,'L');
    
    $pdf->Cell(0,5, ' ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(65,13, '2a:       /      /            ____:____ h','L',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'C');
    $pdf->SetFont('Arial','',9); 
    $pdf->Cell(26,5, 'End. Insuficiente','LBR',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'L');
    $pdf->Cell(27,5, 'N�o Procurado','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','',12);
    $pdf->Cell(65,18, '3a:       /      /            ____:____ h','L',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'C');
    $pdf->SetFont('Arial','',9); 
    $pdf->Cell(26,5, 'N�o Existe Nro','LBR',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'L');
    $pdf->Cell(27,5, 'Ausente','LBR',0,'L'); 
    $pdf->Cell(0,5, ' ','LR',0,'L');
    
    $pdf->ln();
    $pdf->SetX(75);
    $pdf->Cell(6,5, ' ','LBR',0,'C');
    $pdf->SetFont('Arial','',9); 
    $pdf->Cell(26,5, 'Desconhecido','LBR',0,'L');
    $pdf->Cell(6,5, ' ','LBR',0,'L');
    $pdf->Cell(27,5, 'Falecido','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LR',0,'L'); 
    
    $pdf->ln();
    $pdf->SetX(75);
    $pdf->Cell(6,5, ' ','LBR',0,'C');
    $pdf->SetFont('Arial','',9); 
    $pdf->Cell(59,5, 'Outro:','LBR',0,'L');  
    $pdf->Cell(0,5, ' ','LBR',0,'L'); 
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'ASSINATURA DO RECEBEDOR:','LTR',0,'L');
    $pdf->Cell(0,5, 'DATA DE ENTREGA: ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, ' ','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LBR',0,'C');
    
    $pdf->ln();
    
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(130,5, 'NOME LEG�VEL DO RECEBEDOR:','LR',0,'L');
    $pdf->Cell(0,5, 'NRO DOC. IDENTIDADE: ','LR',0,'C');
    
    $pdf->ln();
    
    $pdf->Cell(130,5, ' ','LBR',0,'L');
    $pdf->Cell(0,5, ' ','LBR',0,'C'); 
    

  //Fecha o If de se h� registros
  }

}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      