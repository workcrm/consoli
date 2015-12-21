<?php
###########
## M�dulo de relat�rio geral de financeiro por formando - SINTETICO
## Criado: 05/04/2010 - Maycon Edinger
## Alterado: 
## Altera��es: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipula��o de datas
include "../include/ManipulaDatas.php";

//Recupera os valores para filtragem
$empresaId = $_GET["EmpresaId"];
$empresaNome = $_GET["EmpresaNome"];
$usuarioNome = $_GET["UsuarioNome"];
$edtEventoId = $_GET["EventoId"];
$edtTipoConsulta = $_GET["TipoConsulta"];

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

$desc_evento = $dados_evento["nome"];


class PDF extends FPDF
{

	//Cabe�alho do relat�rio
	function Header()
	{
	    
		$empresaNome = $_GET["EmpresaNome"];
		global $desc_tipo_consulta;
		global $desc_evento;

		//Ajusta a fonte
		$this->SetFont('Arial','',9);
		//Titulo do relat�rio
		$this->Cell(0,4, $empresaNome);
		$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
		$this->Ln();
		$this->SetFont('Arial','B',15);
		$this->Cell(0,6,'Relat�rio de Posi��o Financeira');
		$this->SetFont('Arial','',9);
		$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
		$this->Ln(6);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,5,'Evento:');
		$this->SetFont('Arial', '', 10);
		$this->Multicell(0,5, $desc_evento);
		$this->SetFont('Arial', 'B', 10);
		$this->Cell(19,5,'Filtragem:');
		$this->SetFont('Arial', '', 10);
		$this->Multicell(0,5, $desc_tipo_consulta);
		//Line break
		$this->Ln(4);

	}

	//Rodap� do Relat�rio
	function Footer()
	{
		
		$usuarioNome = $_GET["UsuarioNome"];
		//Position at 1.5 cm from bottom
		$this->SetY(-15);    
		//Arial italic 8
		$this->SetFont('Arial','I',7);
		//Page number
		$this->Line(10,281,200,281);
		$this->Cell(0,3,'Emitido por: ' . $usuarioNome);

	}

}

//Instancia a classe gerador de pdf
$pdf=new PDF();
//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos');
$pdf->SetAuthor($usuarioNome . " - " . $empresaNome);
$pdf->SetTitle('Rela��o Geral de Contas a Receber');
$pdf->SetSubject('Relat�rio gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');
 
//verifica os formandos j� cadastrados para este evento e exibe na tela
$sql_consulta = mysql_query("SELECT * FROM eventos_formando WHERE evento_id = $edtEventoId ORDER by nome");

$registros = mysql_num_rows($sql_consulta); 
  
//Verifica se h� formandos cadastrados para o evento
if ($registros == 0)
{
  
	$pdf->Cell(0,7, "N�o h� formandos para o evento selecionado !",1,0);
  
}

else 

{ 
    
	$pdf->ln();
		
	//T�tulos das colunas
	$pdf->SetFont("Arial", "B", 10);
	//Define a cor RGB do fundo da celula
	$pdf->SetFillColor(178,178,178);
	//Faz a c�lula ser preenchida. Isso � feito setando 1 ap�s a expressao de alinhamento
	$pdf->Cell(0,6, "Status",1,0,"L",1);
	$pdf->SetX(32);        
	$pdf->Cell(0,6, "Financeiro");
	$pdf->SetX(55);
	$pdf->Cell(0,6, "Formando");
	$pdf->SetX(135);
	$pdf->Cell(0,6, "Telefone");
	$pdf->SetX(160);
	$pdf->Cell(30,6, "Email");
	$pdf->SetX(215);
	$pdf->Cell(0,6, "Observa��es");
    
    //Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta))
	{
    	
		//Efetua o switch para o campo de status
		switch ($dados_consulta[status]) 
		{
			case 1: $desc_status = "A se formar"; break;
			case 2: $desc_status = "Formado"; break;
			case 3: $desc_status = "Desistente"; break;
		} 
      
		//Efetua o switch para o campo de situa��o financeira
		switch ($dados_consulta[situacao]) 
		{
			case 0: $desc_fin = " "; break;
			case 1: $desc_fin = "Pendente"; break;
			case 2: $desc_fin = "Quitado"; break;
		}
      
		//Faz o operador de m�dulo para verificar se a linha � par ou impar
		$linha_modulo = $numero_linha % 2;
		  
		//Verifica se � par
		if ($linha_modulo == 0) 
		{

			//Se � par, seta o preenchimento
			$pdf->SetFillColor(220,220,220);

		} 
		
		else 
		
		{

			//Se for impar, seta como fundo branco
			$pdf->SetFillColor(255,255,255);

		}
      
		$pdf->ln();
		$pdf->SetFont("Arial","",9);
		$pdf->Cell(0,5, $desc_status,"T",0,"L",1); 
		$pdf->SetX(32);      
		$pdf->Cell(0,5, $desc_fin);
		$pdf->SetX(55);
		$pdf->Cell(0,5, $dados_consulta["nome"]);
		$pdf->SetX(135);
		$pdf->Cell(0,5, $dados_consulta["contato"]);
		$pdf->SetX(160);
		$pdf->Cell(30,5, $dados_consulta["email"]);      
		$pdf->SetX(215);
		$pdf->Cell(0,5, $dados_consulta["observacoes"]); 
      
		$pdf->ln();
		$pdf->SetFont("Arial","",8);
		$pdf->Cell(0,4, "Obs. Financeiras:" ,0,0,"L",1); 
		$pdf->SetX(55);
		$pdf->Cell(0,4, $dados_consulta["obs_financeiro"]);
      
		//Incrementa o contado de linhas
    	$numero_linha++;
      
    } 
    
  }
  
}

//Gera o PDF
$pdf->Output();
?>