<?php
###########
## Módulo para montagem do relatório de Planilha de Foto e Vídeo de Formandos sem compra
## Criado: 29/06/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
##
###########

//Acesso as rotinas do PDF
require('../fpdf/fpdf.php');

//Inclui o arquivo de conexão com o servidor
include '../conexao/ConexaoMySQL.php';

//Inclui o arquivo para manipulação de datas
include '../include/ManipulaDatas.php';

//Recupera os valores para filtragem
$EventoId = $_GET['EventoId'];
$EmpresaId = $_GET['EmpresaId'];

//Recupera dos dados do evento
$sql_evento = "SELECT id, nome FROM eventos WHERE id = $EventoId";

//Executa a query de consulta
$query_evento = mysql_query($sql_evento);

//Monta a matriz com os dados
$dados_evento = mysql_fetch_array($query_evento);

//Chama a classe para gerar o PDF
class PDF extends FPDF
{

  //Cabeçalho do relatório
  function Header()
  {    

    //Recupera o nome da empresa
    $empresaNome = $_GET['EmpresaNome'];
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    $this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
    $this->Ln();
    $this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');
    $this->Ln();	    
    $this->SetFont('Arial','B',15);
    $this->Cell(0,6,'Gerenciamento de Foto e Vídeo',0,0,'R');
    $this->SetFont('Arial','',9);

    //Imprime o logotipo da empresa
    $this->Image('../image/logo_consoli2.jpg',10,10,42);
    //$this->Image('../image/logo_consoli2.jpg',10,10,59);
    $this->SetY(25);
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(178,178,178);

    //Line break
    $this->Ln(14);
  }

  //Rodapé do Relatório
  function Footer()
  {

    $usuarioNome = $_GET['UsuarioNome'];
    //Posiciona a 1.5 cm do final
    $this->SetY(-15);    
    //Arial italico 8
    $this->SetFont('Arial','I',7);
    $this->Line(10,281,200,281);
    $this->Cell(100,3,'Emitido por: ' . $usuarioNome);
    $this->SetFont('Arial','',7);
    $this->Cell(0,3,'[Powered by work | eventos]',0,0,'R');
    
  }
  
}

//Instancia a classe gerador de pdf
$pdf=new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Work Labs Tecnologia - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Planilha Controle de Foto e Vídeo do Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage();

$pdf->ln();
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(178,178,178);
$pdf->Cell(0,6, 'Gerenciamento do Foto e Video - Formandos Sem Aquisições',1,0,'L',1);
$pdf->ln(8);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0,6, 'Evento: '  . $dados_evento["id"] ,0,1);
$pdf->Cell(0,6, $dados_evento["nome"] ,0,0,'L');
$pdf->SetFont('Arial', 'B', 10);
$pdf->ln();

	
//verifica os formandos já cadastrados para este evento
$sql_formando = mysql_query("SELECT 
                            form.id, 
                            form.nome,
                            form.contato,
                            form.telefone_comercial,
                            form.telefone_residencial,
                            form.endereco,
                            form.complemento,
                            form.bairro,
                            form.cep,
                            form.cpf,
                            form.uf,
                            form.email,
                            form.observacoes,
                            form.obs_compra,
                            cid.nome AS cidade_nome
                            FROM eventos_formando form
                            LEFT OUTER JOIN cidades cid ON cid.id = form.cidade_id
                            WHERE form.evento_id = $EventoId 
                            AND status != 3
                            AND form.status_fotovideo = 1
                            ORDER by form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);
	
//Verifica a quantidade de registros
if ($registros == 0 ) 
{
  
  //Exibe a mensagem que não foram encontrados registros
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(0,6, 'Não há formandos cadastrados para este evento !',1,0,'L');

} 

else 

{
	
  //Percorre o array 
  while ($dados_formando = mysql_fetch_array($sql_formando))
  {

    $FormandoId = $dados_formando["id"];

    //verifica se o formando comprou algo de foto e video antigo
    $sql_compra = mysql_query("SELECT 
                              item_id 
                              FROM eventos_fotovideo 
                              WHERE evento_id = $EventoId
                              AND formando_id = $FormandoId");

    //Verifica o numero de registros retornados
    $registros_antigo = mysql_num_rows($sql_compra);

   	//verifica se o formando comprou algo de foto e video NOVO
    $sql_compra = mysql_query("SELECT 
                              id 
                              FROM fotovideo_pedido
                              WHERE formando_id = $FormandoId");

    //Verifica o numero de registros retornados
    $registros_novo = mysql_num_rows($sql_compra);

    $total_registros = $registros_antigo + $registros_novo;
			
    //Verifica a quantidade de registros
    if ($total_registros == 0 ) 
    {

      //Imprime os dados do formando	  
      $pdf->ln(8);
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,6, $dados_formando['nome'], "T");
      $pdf->ln();
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(0,5, "CPF: " . $dados_formando['cpf']);
      $pdf->ln();
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(0,5, 'Contato: ' . $dados_formando['contato'] . ' - Residencial: ' . $dados_formando['telefone_residencial'] . ' - Comercial: ' . $dados_formando['telefone_comercial']);
      $pdf->ln();
      $pdf->Cell(0,5, $dados_formando['email'] );
      $pdf->ln();
      $pdf->Cell(0,5, $dados_formando['endereco'] . " - " . $dados_formando['complemento'] . " - " . $dados_formando['bairro'] );
      $pdf->ln();
      $pdf->Cell(0,5, $dados_formando['cep'] . " - " . $dados_formando['cidade_nome'] . " - " . $dados_formando['uf'] );
      
      if ($dados_formando['observacoes'] != '')
      {
        
        $pdf->ln();
        $pdf->Multicell(0,5, $dados_formando['observacoes'] );
        
      }
      
      if ($dados_formando['obs_compra'] != '')
      {
        
        $pdf->ln();
        $pdf->Multicell(0,5, $dados_formando['obs_compra'] );
        
      }

      $pendente++;

    }

  }

  //Imprime os dados do formando	  
  $pdf->ln(10);
  $pdf->SetFont('Arial','',10);
  $pdf->Cell(0,5, "Total de Formandos Pendentes: " . $pendente, "T");
	
}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      