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
$CidadeId = $_GET['CidadeId'];
$UFId = $_GET['UFId'];
$EmpresaId = $_GET['EmpresaId'];
$DataIni = DataMySQLInserir($_GET["DataIni"]);
$DataFim = DataMySQLInserir($_GET["DataFim"]);

//Caso tenha Informado uma cidade
if ($CidadeId != 0)
{

  //Recupera dos dados do evento
  $sql_cidade = "SELECT nome FROM cidades WHERE id = $CidadeId";

  //Executa a query de consulta
  $query_cidade = mysql_query($sql_cidade);

  //Monta a matriz com os dados
  $dados_cidade = mysql_fetch_array($query_cidade);

  //Caria a descricao da cidade
  $desc_cidade = "Cidade: " . $dados_cidade['nome'];

  //Cria a filtragem
  $where_cidade = "AND form.cidade_id = $CidadeId";

}

//Caso tenha Informado uma UF
if (!empty($UFId))
{
	
  //Caria a descricao da uf
  $desc_uf = "UF: " . $UFId;

  //Cria a filtragem
  $where_uf = "AND cid.uf = '$UFId'";

}

//Caso tenha uma data
if ($DataFim != '0-0-0')
{
	
  //Caria a descricao da uf
  $desc_datas = "Data de Realização do Evento: " . DataMySQLRetornar($DataIni) . ' a ' . DataMySQLRetornar($DataFim);

  //Cria a filtragem
  $where_datas = "AND eve.data_realizacao BETWEEN '$DataIni' AND '$DataFim'";
  
}
	
//Chama a classe para gerar o PDF
class PDF extends FPDF
{

  //Cabeçalho do relatório
  function Header()
  {    

    global $desc_cidade, $desc_uf, $desc_datas;

    //Recupera o nome da empresa
    $empresaNome = $_GET['EmpresaNome'];
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    $this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
    $this->Ln();
    $this->Cell(0,4, 'Pagina: ' . $this->PageNo(),0,0,'R');
    $this->Ln();	    
    $this->SetFont('Arial','B',16);
    $this->Cell(0,6,'Gerenciamento de Foto e Vídeo',0,0,'R');
    $this->Ln();
    $this->SetFont('Arial','',12);
    $this->Cell(0,5,"Relação Formandos Sem Aquisições",0,0,'R');
	
    $this->Ln();
    $this->SetFont('Arial','B',12);
    $this->Cell(0,5,$desc_cidade . '     ' . $desc_uf . '     ' . $desc_datas,0,0,'R');

    $this->SetFont('Arial','',9);

    //Imprime o logotipo da empresa
    $this->Image('../image/logo_consoli2.jpg',10,10,35);
    //$this->Image('../image/logo_consoli2.jpg',10,10,59);
    $this->SetY(25);
    $this->SetFont('Arial', 'B', 10);
    $this->SetFillColor(178,178,178);
	
    //Line break
    $this->Ln(8);
    
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

  }
  
}

//Instancia a classe gerador de pdf
$pdf = new PDF();

//Define os atributos de propriedade do arquivo PDF
$pdf->SetCreator('work | eventos - CopyRight(c) - Desenvolvido por Work Labs Tecnologia - www.worklabs.com.br');
$pdf->SetAuthor($usuarioNome . ' - ' . $empresaNome);
$pdf->SetTitle('Planilha Controle de Foto e Vídeo do Evento');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();

//Cria a página do relatório
$pdf->AddPage('L');
	
//verifica os formandos já cadastrados para este evento
$sql_formando = mysql_query("SELECT 
	                            form.id, 
	                            form.evento_id,
	                            form.nome,
	                            form.contato,
	                            form.operadora,
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
	                            cid.nome AS cidade_nome,
	                            cid.uf AS cidade_uf,
	                            eve.nome AS evento_nome
                            FROM 
                            	eventos_formando form
                            LEFT OUTER JOIN 
                            	eventos eve ON eve.id = form.evento_id
                            LEFT OUTER JOIN 
                            	cidades cid ON cid.id = form.cidade_id
                            WHERE 
                            	form.status = 2 
	                            $where_cidade
	                            $where_uf
	                            $where_datas
                            AND 
                            	form.status_fotovideo = 1
                            AND
                            	eve.foto_video_liberado = 1
                            ORDER BY 
                            	form.nome");

//Verifica o numero de registros retornados
$registros = mysql_num_rows($sql_formando);
	
//Verifica a quantidade de registros
if ($registros == 0 ) 
{
  //Exibe a mensagem que não foram encontrados registros
  $pdf->ln(12);
  $pdf->SetFont('Arial', 'B', 9);
  $pdf->Cell(0,6, 'Não há Formandos Cadastrados para esta Cidade/UF !',1,0,'L');

//Caso tiver
} 

else 

{
	
  //Percorre o array 
  while ($dados_formando = mysql_fetch_array($sql_formando))
  {

    $FormandoId = $dados_formando["id"];

    $desc_operadora = '';

    //Efetua o switch para o campo de operadora
    switch ($dados_formando[operadora]) 
    {
      case 1: $desc_operadora = " - (VIVO)"; break;
      case 2: $desc_operadora = " - (TIM)"; break;
      case 3: $desc_operadora = " - (Claro)"; break;
      case 4: $desc_operadora = " - (Oi)"; break;
    }
		
    //verifica se o formando comprou algo de foto e video
    $sql_compra = mysql_query("SELECT 
                              	item_id 
                              FROM 
                              	eventos_fotovideo 
                              WHERE 
                              	formando_id = $FormandoId");

    //Verifica o numero de registros retornados
    $registros_antigo = mysql_num_rows($sql_compra);

   	//verifica se o formando comprou algo de foto e video NOVO
    $sql_compra = mysql_query("SELECT 
                              	id 
                              FROM 
                              	fotovideo_pedido
                              WHERE 
                              	formando_id = $FormandoId");

    //Verifica o numero de registros retornados
    $registros_novo = mysql_num_rows($sql_compra);

    $total_registros = $registros_antigo + $registros_novo;
			
    //Verifica a quantidade de registros
    if ($total_registros == 0 )
    {

      $TelRes = '';
      $TelCom = '';

      if ($dados_formando['telefone_residencial'] != '') $TelRes = "   - Tel Res: " . $dados_formando['telefone_residencial'];
      if ($dados_formando['telefone_comercial'] != '') $TelCom = "   - Tel Com: " . $dados_formando['telefone_comercial'];

      //Imprime os dados do formando	  
      $pdf->ln();
      $pdf->SetFont('Arial','B',12);
      $pdf->Cell(0,5, $dados_formando['nome'] . '   [CPF: ' . $dados_formando['cpf'] . ']', "T");
      $pdf->ln();
      $pdf->SetFont('Arial','B',10);
      $pdf->Cell(0,4, '(' . $dados_formando['evento_id'] . ') - ' . $dados_formando['evento_nome']);
      $pdf->ln();
      $pdf->SetFont('Arial','',9);
      $pdf->Cell(0,4, 'Contato: ' . $dados_formando['contato'] . $desc_operadora . $TelRex . $TelCom . ' - ' . $dados_formando['endereco'] . " - " . $dados_formando['complemento'] . " - " . $dados_formando['bairro'] . ' - ' . $dados_formando['cep'] . " - " . $dados_formando['cidade_nome'] . " - " . $dados_formando['cidade_uf'] );
      $pdf->ln();
      $pdf->Cell(0,4, $dados_formando['email'] );

      if ($dados_formando['observacoes'] != '')
      {

        $pdf->ln();
        $pdf->SetFont('Arial','I',9);
        $pdf->Multicell(0,4, $dados_formando['observacoes'] );
        $pdf->SetFont('Arial','',9);

      }

      else

      {

        $pdf->ln(2);

      }

      $pendente++;

    }

  }

  //Imprime os dados do formando	  
  $pdf->ln();
  $pdf->SetFont('Arial','',10);
  $pdf->SetX(15);
  $pdf->Cell(0,5, "Total de Formandos a Atender: " . $pendente, "T");
	
}

$pdf->ln();	

//Gera o PDF
$pdf->Output();

?>                                                      