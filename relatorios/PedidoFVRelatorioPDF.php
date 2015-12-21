<?php
###########
## Módulo de relatório geral de pedidos de foto e video
## Criado: 15/10/2010 - Maycon Edinger
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
$dataIni = DataMySQLInserir($_GET[DataIni]);
$dataFim = DataMySQLInserir($_GET[DataFim]);



//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) 
{
	
  $TextoFiltraData = "E com data de emissão entre: $_GET[DataIni] a $_GET[DataFim]";
	$TextoSQLData = "	 AND ped.data >= '$dataIni' AND ped.data <= '$dataFim' ";

}

//Recebe os valores vindos do formulário
//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) 
{
  //Se for 1 então é visualização por data
	case 1: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Data de Emissão'; 

		//Monta o sql
		$sql = "SELECT 
							ped.id,
							ped.data,
              ped.evento_id,
							ped.formando_id,
              ped.data_entrega,
              ped.fornecedor_id,
              ped.observacoes,
							eve.nome AS evento_nome,
							form.nome AS formando_nome,
							forn.nome AS fornecedor_nome
							FROM pedido_fv ped
							LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
              LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
              LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
							WHERE ped.data >= '$dataIni' AND ped.data <= '$dataFim' 
							ORDER BY ped.data, eve.nome";

		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos emitidos entre as datas informadas';
	break;

	//Se for 2 então é visualização por evento
	case 2: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento'; 		

    $eventoId = $_GET[EventoId];
    
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							ped.id,
							ped.data,
              ped.evento_id,
							ped.formando_id,
              ped.data_entrega,
              ped.fornecedor_id,
              ped.observacoes,
							eve.nome AS evento_nome,
							form.nome AS formando_nome,
							forn.nome AS fornecedor_nome
							FROM pedido_fv ped
							LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
              LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
              LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
							WHERE ped.evento_id = '$eventoId' $TextoSQLData 
							ORDER BY ped.data";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = "Não há pedidos para o evento $dados_evento[nome]";
	break;
	
  
 	//Se for 3 então é visualização por evento e formando
	case 3: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento e Formando'; 		

    $eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
    
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.evento_id = '$eventoId' AND ped.formando_id = '$formandoId' $TextoSQLData
						ORDER BY ped.data";
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este evento e formando';
	break;
  
  //Se for 4 então é visualização por evento, formando e fornecedor
	case 4: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Evento, Formando e Fornecedor'; 		

    $eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
    $fornecedorId = $_GET[FornecedorId];
    
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.evento_id = '$eventoId' AND ped.formando_id = '$formandoId' AND ped.fornecedor_id = '$fornecedorId' $TextoSQLData
						ORDER BY ped.data";            
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este evento, formando e fornecedor';
	break;
  
  //Se for 5 então é visualização por fornecedor
	case 5: 
		//Monta o título da página
		$titulo = 'Relação de Pedidos do Foto e Vídeo por Fornecedor';         

    $fornecedorId = $_GET[FornecedorId];

		//Monta o sql de filtragem das contas
		$sql = "SELECT 
						ped.id,
						ped.data,
            ped.evento_id,
						ped.formando_id,
            ped.data_entrega,
            ped.fornecedor_id,
            ped.observacoes,
						eve.nome AS evento_nome,
						form.nome AS formando_nome,
						forn.nome AS fornecedor_nome
						FROM pedido_fv ped
						LEFT OUTER JOIN eventos eve ON eve.id = ped.evento_id
            LEFT OUTER JOIN eventos_formando form ON form.id = ped.formando_id
            LEFT OUTER JOIN fornecedores forn ON forn.id = ped.fornecedor_id
						WHERE ped.fornecedor_id = '$fornecedorId' $TextoSQLData
						ORDER BY ped.data";            
		
		//Monta o texto para caso não houver registros
		$texto_vazio = 'Não há pedidos para este fornecedor';
	break;  	
}
			
//Executa a Query
$query = mysql_query($sql);		  	  

//verifica o número total de registros
$registros = mysql_num_rows($query);	      
 
class PDF extends FPDF
{
  //Cabeçalho do relatório
  function Header()
  {
    
    //Pega a data inicial pra ver se veio vazia
    $dataIni = DataMySQLInserir($_GET[DataIni]);
  	  
  	$empresaNome = $_GET["EmpresaNome"];
   	
     //Monta o switch para criar o texto da filtragem
  	switch ($_GET[TipoListagem]) {
    //Se for 1 então é visualização por data
  	case 1: 
  		//Monta a descrição a exibir
  		$desc_filtragem = "Data de emissão entre: $_GET[DataIni] a $_GET[DataFim]";
  	break;
  
    //Se for 2 então é visualização por evento
  	case 2: 
  		$eventoId = $_GET[EventoId];
  		
  		//Recupera o nome da categoria selecionada
  		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
  		
  		//Monta o array com os dados
  		$dados_evento = mysql_fetch_array($sql_evento);
  		
  		//Monta a descrição a exibir
  		$desc_filtragem = "Evento: $dados_evento[nome]" . $TextoFiltraData;
      
  		//Se tem filtragem acrescenta as datas ao texto
  		if ($dataIni != 0) {
  			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
  		}
  	break;
  
    //Se for 3 então é visualização por evento
  	case 3: 
  		$eventoId = $_GET[EventoId];
      $formandoId = $_GET[FormandoId];
  		
  		//Recupera o nome da categoria selecionada
  		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
  		
  		//Monta o array com os dados
  		$dados_evento = mysql_fetch_array($sql_evento);
      
      //Recupera o nome do formando
  		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
  		
  		//Monta o array com os dados
  		$dados_formando = mysql_fetch_array($sql_formando);
  		
  		//Monta a descrição a exibir
  		$desc_filtragem = "Evento: $dados_evento[nome], formando: $dados_formando[nome]" . $TextoFiltraData;
      
  		//Se tem filtragem acrescenta as datas ao texto
  		if ($dataIni != 0) {
  			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
  		}
  	break;	
  
    //Se for 4 então é visualização por evento, formando e fornecedor
  	case 4:
  		$eventoId = $_GET[EventoId];
      $formandoId = $_GET[FormandoId];
      $fornecedorId = $_GET[FornecedorId];
  		
  		//Recupera o nome da categoria selecionada
  		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
  		
  		//Monta o array com os dados
  		$dados_evento = mysql_fetch_array($sql_evento);
      
      //Recupera o nome do formando
  		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
  		
  		//Monta o array com os dados
  		$dados_formando = mysql_fetch_array($sql_formando);
      
      //Recupera o nome do fornecedor
  		$sql_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$fornecedorId'");
  		
  		//Monta o array com os dados
  		$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
  		
  		//Monta a descrição a exibir
  		$desc_filtragem = "Evento: $dados_evento[nome], formando: $dados_formando[nome], fornecedor: $dados_fornecedor[nome]" . $TextoFiltraData;
      
  		//Se tem filtragem acrescenta as datas ao texto
  		if ($dataIni != 0) {
  			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
  		}
  	break;	
  	
    //Se for 5 então é visualização por fornecedor
  	case 5:
  		$fornecedorId = $_GET[FornecedorId];
      
      //Recupera o nome do fornecedor
  		$sql_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$fornecedorId'");
  		
  		//Monta o array com os dados
  		$dados_fornecedor = mysql_fetch_array($sql_fornecedor);
  		
  		//Monta a descrição a exibir
  		$desc_filtragem = "Fornecedor: $dados_fornecedor[nome]" . $TextoFiltraData;
      
  		//Se tem filtragem acrescenta as datas ao texto
  		if ($dataIni != 0) 
      {
  			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim] $texto_situacao";
  		}
  	
    break;	
  	
  }
  
  	
    //Ajusta a fonte
    $this->SetFont('Arial','',9);
    //Titulo do relatório
  	$this->Cell(0,4, $empresaNome);
  	$this->Cell(0,4, date('d/m/Y', mktime()),0,0,'R');
  	$this->Ln();
  	$this->SetFont('Arial','B',15);
    $this->Cell(0,6,'Relação Geral de Pedidos do Foto e Vídeo');
    $this->SetFont('Arial','',9);
  	$this->Cell(0,4, 'Pagina: '.$this->PageNo(),0,0,'R');    
    $this->Ln(5);
    $this->SetFont('Arial', 'B', 10);
    $this->Cell(19,6,'Filtragem:');
    $this->SetFont('Arial', '', 10);
    $this->Multicell(0,6, $desc_filtragem);
  	//Line break
    $this->Ln(6);
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
$pdf->SetTitle('Relação Geral de Pedidos do Foto e Vídeo');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage();

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(15,6, 'Pedido',1,0,'C',1);
$pdf->Cell(20,6, 'Emissão',1,0,'C',1);
$pdf->Cell(130,6, 'Fornecedor/Formando/Evento',1,0,'L',1);
$pdf->Cell(0,6, 'Entrega',1,0,'C',1);

//Caso houverem registros
if ($registros > 0) 
{
    
  //Percorre o array dos dados
  while ($dados = mysql_fetch_array($query))
  {
    
    $pdf->ln();
    $pdf->SetFont('Arial','',9);
  	$pdf->Cell(15,4, ' ','LTR',0,'C');
    $pdf->Cell(20,4, ' ','LTR');
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(130,4, $dados['fornecedor_nome'] . " (" . $dados['fornecedor_id'] . ")",'LTR');
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(0,4, ' ','LTR');
                 
    $pdf->ln();
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(15,4, $dados['id'],'LR',0,'C');
    $pdf->Cell(20,4, DataMySQLRetornar($dados['data']),'LR');
    $pdf->Cell(130,4, $dados['evento_nome'] . " (" . $dados['evento_id'] . ")",'LR');
    
    if ($dados['data_entrega'] != "0000-00-00")
    {
      
      $pdf->Cell(0,4, DataMySQLRetornar($dados['data_entrega']),'LR',0,'C'); 
      
    }
     
    else
     
    {
      
      $pdf->Cell(0,4, " ",'LR');
             
    }
    
    $pdf->ln();
    $pdf->SetFont('Arial','',9);
  	$pdf->Cell(15,4, ' ','LBR',0,'C');
    $pdf->Cell(20,4, ' ','LBR');
    $pdf->Cell(130,4, $dados['formando_nome'] . " (" . $dados['formando_id'] . ")",'LBR');
    $pdf->Cell(0,4, ' ','LBR');
     
  }

}

else

{
  
  //Exibe uma linha dizendo que nao há registros
  $pdf->ln(10);
  $pdf->Cell(0,6, $texto_vazio,1,0);
  
}

//Gera o PDF
$pdf->Output();
?>