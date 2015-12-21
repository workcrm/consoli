<?php
###########
## Módulo de relatório geral de financeiro por formando
## Criado: 05/04/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

require('../fpdf/fpdf.php');

include "../conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
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

//Verifica o tipo da consulta
if ($edtTipoConsulta == 1)
{	
                
  $desc_tipo_consulta = "POR BOLETOS DO FORMANDO";

} 
else 
{

  $desc_tipo_consulta = "POR POSIÇÃO FINANCEIRA DO FORMANDO";

}

class PDF extends FPDF
{
//Cabeçalho do relatório
function Header()
{
	    
	$empresaNome = $_GET["EmpresaNome"];
  global $desc_tipo_consulta;
  global $desc_evento;

  //Ajusta a fonte
  $this->SetFont('Arial','',9);
  //Titulo do relatório
	$this->Cell(0,4, $empresaNome);
	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
	$this->Ln();
	$this->SetFont('Arial','B',15);
  $this->Cell(0,6,'Relatório de Posição Financeira');
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

//Rodapé do Relatório
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
$pdf->SetTitle('Relação Geral de Contas a Receber');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');


//Caso seja relatório por boletos
if ($edtTipoConsulta == 1)
{		
  
  //Busca os boletos emitidos para o evento
  $sql_topo = mysql_query("SELECT 
                      bol.id,
                      bol.nosso_numero,
                      bol.sacado,
                      bol.demonstrativo2,
                      bol.demonstrativo3,
                      bol.valor_boleto,
                      bol.data_documento,
                      bol.data_vencimento,
                      bol.boleto_recebido,
                      form.nome as formando_nome
                      FROM boleto bol 
                      LEFT OUTER JOIN eventos_formando form ON form.id = bol.formando_id
                      WHERE bol.evento_id = $edtEventoId
                      ORDER BY formando_nome
                      LIMIT 0,1");
                      
  //verifica o número total de registros
  $registros = mysql_num_rows($sql_topo);                      
                      
  //Busca os boletos emitidos para o evento
  $sql = mysql_query("SELECT 
                      bol.id,
                      bol.nosso_numero,
                      bol.sacado,
                      bol.demonstrativo2,
                      bol.demonstrativo3,
                      bol.valor_boleto,
                      bol.data_documento,
                      bol.data_vencimento,
                      bol.boleto_recebido,
                      form.nome as formando_nome
                      FROM boleto bol 
                      LEFT OUTER JOIN eventos_formando form ON form.id = bol.formando_id
                      WHERE bol.evento_id = $edtEventoId
                      ORDER BY formando_nome"); 
  
  //Verifica se há formandos cadastrados para o evento
  if ($registros == 0)
  {
  
    $pdf->Cell(0,7, "Não há formandos para o evento selecionado !",1,0);
  
  }
  else 
  {
      
    $dados_topo = mysql_fetch_array($sql_topo);
    
    $formando = $dados_topo["formando_nome"];    
    
    //Títulos das colunas
    $pdf->SetFont("Arial", "B", 12);
    $pdf->Cell(0,6, $dados_topo["formando_nome"]);

    $pdf->ln();

    //Títulos das colunas
    $pdf->SetFont("Arial", "B", 10);
    //Define a cor RGB do fundo da celula
    $pdf->SetFillColor(178,178,178);
    //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
    $pdf->Cell(0,6, "Nosso Número",1,0,"L",1);
    $pdf->SetX(45);
    $pdf->Cell(0,6, "Dados do Sacado/Evento/Formando",1,0,"L",1);
    $pdf->SetX(185);
    $pdf->Cell(0,6, "Emissão");
    $pdf->SetX(205);
    $pdf->Cell(0,6, "Vencto");
    $pdf->SetX(225);
    $pdf->Cell(30,6, "Valor",0,0,"R");
    $pdf->SetX(268);
    $pdf->Cell(0,6, "Situação");
    
    //Cria as variáveis zeradas
    $total_formando = 0;
    $total_geral = 0;
    
    //Seta a variável que controla o numero da linha a ser impresso
    $numero_linha = 1; 
    
    while ($dados = mysql_fetch_array($sql))
    {
          
  		//Verifica a situação do boleto
  		switch ($dados["boleto_recebido"]) 
      {
  		  
        case 0: $desc_situacao = "Em Aberto"; break;		  
        case 1: $desc_situacao = "Recebido"; break;
        
      }
      
      
      if($formando != $dados["formando_nome"])
      {
    
        //Monta o subtotal por formando
        $pdf->ln();
        $pdf->Cell(0,6, " ","T");
        $pdf->SetFont("Arial", "B", 10);
        $pdf->SetX(192);
        $pdf->Cell(0,6, "Total do Formando:");
        $pdf->SetX(225);
        $pdf->Cell(30,6, "R$ " . number_format($total_formando, 2, ",", "."),0,0,"R");
        
        $pdf->ln(8);
        
        //Títulos das colunas
        $pdf->SetFont("Arial", "B", 12);
        $pdf->Cell(0,6, $dados["formando_nome"]);
        
        $pdf->ln();
        
        //Títulos das colunas
        $pdf->SetFont("Arial", "B", 10);
        //Define a cor RGB do fundo da celula
        $pdf->SetFillColor(178,178,178);
        //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
        $pdf->Cell(0,6, "Nosso Número",1,0,"L",1);
        $pdf->SetX(45);        
        $pdf->Cell(0,6, "Dados do Sacado/Evento/Formando");
        $pdf->SetX(185);
        $pdf->Cell(0,6, "Emissão");
        $pdf->SetX(205);
        $pdf->Cell(0,6, "Vencto");
        $pdf->SetX(225);
        $pdf->Cell(30,6, "Valor",0,0,"R");
        $pdf->SetX(268);
        $pdf->Cell(0,6, "Situação");
        
        //Zera o totalizador do formando
        $total_formando = 0;
        
      }
      
      //Faz o operador de módulo para verificar se a linha é par ou impar
      $linha_modulo = $numero_linha % 2;
          
      //Verifica se é par
      if ($linha_modulo == 0) 
      {
      	
      	//Se é par, seta o preenchimento
      	$pdf->SetFillColor(220,220,220);
      	
      } 
      else 
      {
      	
      	//Se for impar, seta como fundo branco
      	$pdf->SetFillColor(255,255,255);
      
    	}      

      $pdf->ln();
      $pdf->SetFont("Arial","B",9);
    	$pdf->Cell(0,6, $dados["nosso_numero"],0,0,"L",1); 
    	$pdf->SetX(45);      
      $pdf->Cell(0,6, $dados["sacado"]);
    	$pdf->SetX(185);
      $pdf->Cell(0,6, DataMySQLRetornar($dados["data_documento"]));
      $pdf->SetX(205);
      $pdf->Cell(0,6, DataMySQLRetornar($dados["data_vencimento"]));
      $pdf->SetX(225);
      $pdf->Cell(30,6, "R$ " . number_format($dados["valor_boleto"], 2, ",", "."),0,0,"R");      
      $pdf->SetX(268);
      $pdf->Cell(0,6, $desc_situacao); 
    	
      //Acumula os valores
      $total_formando = $total_formando + $dados["valor_boleto"]; 
      $total_geral = $total_geral + $dados["valor_boleto"];
      
      //Incrementa o contado de linhas
    	$numero_linha++; 
      
      $formando = $dados["formando_nome"];   
    
    }
    
    //Monta o subtotal por formando
    $pdf->ln();
    $pdf->Cell(0,6, " ","T");
    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetX(192);
    $pdf->Cell(0,6, "Total do Formando:");
    $pdf->SetX(225);
    $pdf->Cell(30,6, "R$ " . number_format($total_formando, 2, ",", "."),0,0,"R");
    
    $pdf->ln();
    $pdf->Cell(0,6, " ","T");
    $pdf->SetFont("Arial", "B", 10);
    $pdf->SetX(192);
    $pdf->Cell(0,6, "Total Geral:");
    $pdf->SetX(225);
    $pdf->Cell(30,6, "R$ " . number_format($total_geral, 2, ",", "."),0,0,"R");
       
  }
  
}
else
{
 
  //verifica os formandos já cadastrados para este evento e exibe na tela
  $sql_consulta = mysql_query("SELECT * FROM eventos_formando
														 WHERE evento_id = $edtEventoId
														 ORDER by nome
														 ");

  $registros = mysql_num_rows($sql_consulta); 
  
  //Verifica se há formandos cadastrados para o evento
  if ($registros == 0)
  {
  
    $pdf->Cell(0,7, "Não há formandos para o evento selecionado !",1,0);
  
  }
  else 
  { 
    
    $pdf->ln();
        
    //Títulos das colunas
    $pdf->SetFont("Arial", "B", 10);
    //Define a cor RGB do fundo da celula
    $pdf->SetFillColor(178,178,178);
    //Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
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
    $pdf->Cell(0,6, "Observações");
    
    //Cria o array e o percorre para montar a listagem dinamicamente
    while ($dados_consulta = mysql_fetch_array($sql_consulta)){
    	
			//Efetua o switch para o campo de status
			switch ($dados_consulta[status]) {
			  case 1: $desc_status = "A se formar"; break;
				case 2: $desc_status = "Formado"; break;
				case 3: $desc_status = "Desistente"; break;
			} 
      
      //Efetua o switch para o campo de situação financeira
			switch ($dados_consulta[situacao]) {
			  case 0: $desc_fin = " "; break;
				case 1: $desc_fin = "Pendente"; break;
				case 2: $desc_fin = "Quitado"; break;
			}
      
      //Faz o operador de módulo para verificar se a linha é par ou impar
      $linha_modulo = $numero_linha % 2;
          
      //Verifica se é par
      if ($linha_modulo == 0) 
      {
      	
      	//Se é par, seta o preenchimento
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