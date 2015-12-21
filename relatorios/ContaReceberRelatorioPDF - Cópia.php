<?php
###########
## Módulo de relatório geral de contas a receber
## Criado: 07/06/2007 - Maycon Edinger
## Alterado: 14/08/2007 - Maycon Edinger
## Alterações: 
## 20/06/2007 - Invertido a ordem de exibição do sacado e descrição da conta
## 26/06/2007 - Incluído a filtragem opcional por datas
## 16/07/2007 - Incluído a filtragem por sacado
## 19/07/2007 - Incluído a filtragem por subgrupo
## 14/08/2007 - Incluído a filtragem agrupada grupo, subgrupo, categoria
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

$TipoSituacao = $_GET["TipoSituacao"];

//Verifica a situação informada
if ($_GET["TipoSituacao"] > 0 AND $_GET["TipoSituacao"] != 4){
	
		//Efetua o switch da situacao informada
		switch ($_GET["TipoSituacao"]) 
    {
    	//Se for 1 então é visualização em aberto
  		case 1:
  			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Em aberto</span>";
  		break;		
    	//Se for 2 então é visualização das recebidas
  		case 2:
  			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Recebidas</span>";
  		break;
    	//Se for 3 então é visualização das vencidas
  		case 3:
  			$texto_situacao = " <b>e com Situação:</b> <span style='color: #990000'>Vencidas</span>";
  		break;
		}	
		
	$TipoSituacao = $_GET["TipoSituacao"];
	$TextoSituacao = " AND rec.situacao = '$TipoSituacao'";
	
} 

//Verifica se foi informado alguma data para filtrar junto
if ($dataIni != 0) {
	$TextoFiltraData = "E com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
	$TextoSQLData = "	 AND con.data_vencimento >= '$dataIni' AND con.data_vencimento <= '$dataFim' ";
}

//Efetua o switch para o campo de tipo de listagem
switch ($_GET[TipoListagem]) {
  //Se for 1 então é visualização por data
	case 1: 

		//Monta o sql
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.data_vencimento >= '$dataIni' AND rec.data_vencimento <= '$dataFim' $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
	break;

  //Se for 2 então é visualização por centro de custo
	case 2: 
		//Monta as variáveis
		$grupoId = $_GET[GrupoId];
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";		
	break;
	
  //Se for 3 então é visualização por evento
	case 3: 
		//Monta as variáveis
		$eventoId = $_GET[EventoId];
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";		
	break;		

  //Se for 4 então é visualização por situacao
	case 4:
		//Monta as variáveis
		$TipoSituacao = $_GET[TipoSituacao];
		
    //Efetua o switch da situacao informada
		switch ($TipoSituacao) 
    {
    	//Se for 1 então é visualização em aberto
  		case 1:
  			$texto_situacao = "Em aberto";
  			$where_situacao = "rec.situacao = '$TipoSituacao'";
  		break;		
    	//Se for 2 então é visualização das recebidas
  		case 2:
  			$texto_situacao = "Pagas";
  			$where_situacao = "rec.situacao = '$TipoSituacao'";
  		break;
    	//Se for 3 então é visualização das vencidas
  		case 3:
  			$texto_situacao = "Vencidas";
  			$data_base_vencimento = date("Y-m-d", mktime());
  			$where_situacao = "rec.situacao = '1' AND rec.data_vencimento < '$data_base_vencimento'";
  		break;
		}	
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND $where_situacao $TextoSQLData 
							ORDER BY rec.data_vencimento, rec.descricao";
	break;

	//Se for 5 então é visualização por sacado
	case 5:
		//Monta as variáveis
		$TipoPessoa = $_GET[TipoPessoa];
		$PessoaId = $_GET[PessoaId];
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
              rec.boleto_id,
							rec.valor_recebido,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.tipo_pessoa = '$TipoPessoa' AND rec.pessoa_id = '$PessoaId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";
	break;	
	
	//Se for 6 então é visualização por conta-caixa
	case 6: 
		//Monta as variáveis
		$subgrupoId = $_GET[SubgrupoId];
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";		
	break;	

	//Se for 7 então é visualização agrupada
	case 7: 
		//Monta as variáveis
		$grupoId = $_GET[GrupoId];
		$subgrupoId = $_GET[SubgrupoId];
		$categoriaId = $_GET[CategoriaId];
		
    //Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = $empresaId AND rec.grupo_conta_id = '$grupoId' AND rec.subgrupo_conta_id = '$subgrupoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";    				
	break;
 
  //Se for 8 então é visualização por evento e formando
	case 8: 
		//Monta as variáveis
		$eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
		
		//Monta o sql de filtragem das contas
		$sql = "SELECT 
							rec.id,
							rec.data,
							rec.valor,
							rec.tipo_pessoa,
							rec.pessoa_id,
							rec.data_vencimento,
							rec.descricao,
							rec.situacao,
							rec.origem_conta,
							rec.valor_recebido,
              rec.boleto_id,
							eve.nome as evento_nome
							FROM contas_receber rec
							LEFT OUTER JOIN eventos eve ON eve.id = rec.evento_id
							WHERE rec.empresa_id = '$empresaId' AND rec.evento_id = '$eventoId' AND rec.formando_id = '$formandoId' $TextoSQLData $TextoSituacao 
							ORDER BY rec.data_vencimento, rec.descricao";		
	break;		
}

$query = mysql_query($sql);
	  	
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
		$desc_filtragem = "Contas a receber com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
	break;

  //Se for 2 então é visualização por centro de custo
	case 2: 
		//Monta as variáveis
		$grupoId = $_GET[GrupoId];
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber do centro de custo: $dados_grupo[nome]";
		//Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;

  //Se for 3 então é visualização por evento
	case 3: 
		//Monta as variáveis
		$eventoId = $_GET[EventoId];
		
		//Recupera o nome do evento selecionado
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]";
		
    //Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) 
    {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;	

  //Se for 4 então é visualização por situacao
	case 4:
		//Monta as variáveis
		$TipoSituacao = $_GET[TipoSituacao];
		
		//Efetua o switch da situacao informada
		switch ($TipoSituacao) {
  	//Se for 1 então é visualização em aberto
		case 1:
			$texto_situacao = "Em aberto";
		break;		
  	//Se for 2 então é visualização das pagas
		case 2:
			$texto_situacao = "Recebidas";
		break;
  	//Se for 3 então é visualização das vencidas
		case 3:
			$texto_situacao = "Vencidas";
		break;
		}
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber com situacao: $texto_situacao";
		//Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;
	
  //Se for 5 então é visualização por sacado
	case 5:
		//Monta as variáveis
		$TipoPessoa = $_GET[TipoPessoa];
		$PessoaId = $_GET[PessoaId];		
		
		//Efetua o switch da pessoa informada
		switch ($TipoPessoa) {
  	//Se for 1 então é cliente
		case 1:			
			//Recupera o nome do cliente
			$query_cliente = mysql_query("SELECT nome FROM clientes WHERE id = '$PessoaId'");
			$nome_cliente = mysql_fetch_array($query_cliente);
			$texto_pessoa = "Cliente $nome_cliente[nome]";
		break;		
  	//Se for 2 então é visualização das pagas
		case 2:
			//Recupera o nome do fornecedor
			$query_fornecedor = mysql_query("SELECT nome FROM fornecedores WHERE id = '$PessoaId'");
			$nome_fornecedor = mysql_fetch_array($query_fornecedor);
			$texto_pessoa = "Fornecedor $nome_fornecedor[nome]";
		break;
  	//Se for 3 então é visualização das vencidas
		case 3:
			//Recupera o nome do colaborador
			$query_colaborador = mysql_query("SELECT nome FROM colaboradores WHERE id = '$PessoaId'");
			$nome_colaborador = mysql_fetch_array($query_colaborador);
			$texto_pessoa = "Colaborador $nome_colaborador[nome]";
		break;
		}
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber do $texto_pessoa";
		//Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;		
	
	
	//Se for 6 então é visualização por conta-caixa
	case 6: 
		//Monta as variáveis
		$subgrupoId = $_GET[SubgrupoId];
			
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber da conta-caixa: $dados_subgrupo[nome]";
		
    //Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) 
    {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;
	
	//Se for 7 então é visualização agrupada
	case 7: 
		$grupoId = $_GET[GrupoId];
		$subgrupoId = $_GET[SubgrupoId];		
		$categoriaId = $_GET[CategoriaId];
		
		//Recupera o nome do grupo selecionado
		$sql_grupo = mysql_query("SELECT nome FROM grupo_conta WHERE id = '$grupoId'");
		
		//Monta o array com os dados
		$dados_grupo = mysql_fetch_array($sql_grupo);
		
		//Recupera o nome do subgrupo selecionado
		$sql_subgrupo = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = '$subgrupoId'");
		
		//Monta o array com os dados
		$dados_subgrupo = mysql_fetch_array($sql_subgrupo);
				
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber do centro de custo: $dados_grupo[nome]\nE da conta-caixa: $dados_subgrupo[nome]";
		//Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) {
			$desc_filtragem .= "\ne com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
		}
	break;
  
   //Se for 8 então é visualização por evento
	case 8: 
		//Monta as variáveis
		$eventoId = $_GET[EventoId];
    $formandoId = $_GET[FormandoId];
		
		//Recupera o nome do evento selecionado
		$sql_evento = mysql_query("SELECT nome FROM eventos WHERE id = '$eventoId'");
		
		//Monta o array com os dados
		$dados_evento = mysql_fetch_array($sql_evento);
    
    //Recupera o nome do formando selecionado
		$sql_formando = mysql_query("SELECT nome FROM eventos_formando WHERE id = '$formandoId'");
		
		//Monta o array com os dados
		$dados_formando = mysql_fetch_array($sql_formando);
		
		//Monta a descrição a exibir
		$desc_filtragem = "Contas a receber do evento: $dados_evento[nome]\nE do formando: $dados_formando[nome]";
		
    //Se tem filtragem acrescenta as datas ao texto
		if ($dataIni != 0) 
    {
			$desc_filtragem .= ", e com data de vencimento entre: $_GET[DataIni] a $_GET[DataFim]";
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
  $this->Cell(0,6,'Relação Geral de Contas a Receber');
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
$pdf->SetTitle('Relação Geral de Contas a Receber');
$pdf->SetSubject('Relatório gerado automaticamente pelo sistema');
$pdf->AliasNbPages();
$pdf->AddPage('L');

//Títulos das colunas
$pdf->SetFont('Arial', 'B', 10);
//Define a cor RGB do fundo da celula
$pdf->SetFillColor(178,178,178);
//Faz a célula ser preenchida. Isso é feito setando 1 após a expressao de alinhamento
$pdf->Cell(0,6, 'Dados do Sacado/Descrição da Conta a Receber',1,0,'L',1);
$pdf->SetX(152);
$pdf->Cell(0,6, 'Tipo Sacado');
$pdf->SetX(185);
$pdf->Cell(0,6, 'Emissão');
$pdf->SetX(205);
$pdf->Cell(0,6, 'Vencto');
$pdf->SetX(220);
$pdf->Cell(24,6, 'Valor',0,0,'R');
$pdf->SetX(240);
$pdf->Cell(28,6, 'A Receber',0,0,'R');
$pdf->SetX(270);
$pdf->Cell(0,6, 'Situação');

//Seta a variável do total a receber zerado
$total_receber = 0;
$saldo_receber = 0;

//Seta a variável que controla o numero da linha a ser impresso
$numero_linha = 1;

//Percorre o array dos dados
while ($dados = mysql_fetch_array($query)){
	//Efetua o switch para recuperar o nome do sacado
	switch ($dados[tipo_pessoa]) {
	  case 1: //Se for cliente
			$sql_pessoa = "SELECT nome FROM clientes WHERE id = '$dados[pessoa_id]'";
			$desc_pessoa = "Cliente";				
		break;
	  case 2: //Se for fornecedor
			$sql_pessoa = "SELECT nome FROM fornecedores WHERE id = '$dados[pessoa_id]'";
			$desc_pessoa = "Fornecedor";				
		break;
	  case 3: //Se for colaborador
			$sql_pessoa = "SELECT nome FROM colaboradores WHERE id = '$dados[pessoa_id]'";
			$desc_pessoa = "Colaborador";			
		break;
    case 4: //Se for formando
			$sql_pessoa = "SELECT nome FROM eventos_formando WHERE id = '$dados[pessoa_id]'";
			$desc_pessoa = "Formando";				
		break;	
    case 5: //Se for por evento
			$sql_pessoa = "SELECT nome FROM eventos WHERE id = '$dados[pessoa_id]'";
			$desc_pessoa = "Evento";							
		break;		
	}		
	
  //Executa a query de pesquisa de pessoas
  $query_pessoa = mysql_query($sql_pessoa);
  $dados_pessoa = mysql_fetch_array($query_pessoa);
  
	//Efetua o switch para o campo de situação
  switch ($dados[situacao]) 
  {
  	case 1: $texto_situacao = "Em aberto";	break;
		case 2: $texto_situacao = "Recebido";	break;     	
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
  $pdf->SetFont("Arial","B",10);
	$pdf->Cell(0,5, $dados_pessoa["nome"],0,0,"L",1); 
	$pdf->SetFont('Arial','',8);
	$pdf->SetX(152);
  $pdf->Cell(0,5, $desc_pessoa);
	$pdf->SetX(185);
  $pdf->Cell(0,5, DataMySQLRetornar($dados["data"]));
  $pdf->SetX(205);
  $pdf->Cell(0,5, DataMySQLRetornar($dados["data_vencimento"]));
  $pdf->SetX(220);
  $pdf->Cell(24,5, "R$ " . number_format($dados["valor"], 2, ",", "."),0,0,"R");
  $pdf->SetX(244);
  $pdf->Cell(24,5, "R$ " . number_format($dados[valor] - $dados[valor_recebido], 2, ",", "."),0,0,"R");
  $pdf->SetX(270);
  $pdf->Cell(0,5, $texto_situacao);
  $pdf->ln();
  $pdf->SetFont("Arial","",8);
	$pdf->Cell(0,3, $dados["descricao"],0,0,"L",1); 
	
  //Acumula o valor a receber
	$total_receber = $total_receber + $dados["valor"];
  $saldo_receber = $saldo_receber + ($dados["valor"] - $dados["valor_recebido"]);
  
  //Incrementa o contado de linhas
	$numero_linha++;	  
}

$pdf->SetFont('Arial', 'B', 8);
$pdf->ln();
$pdf->Cell(0,6, 'Total de registros listados: ' . $registros,'T');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetX(200);
$pdf->Cell(20,6, 'Total:',0,0,'R');
$pdf->SetX(220);
$pdf->Cell(24,6, "R$ " . number_format($total_receber, 2, ",", "."),0,0,'R');
$pdf->SetX(244);
$pdf->Cell(24,6, "R$ " . number_format($saldo_receber, 2, ",", "."),0,0,'R');

//Gera o PDF
$pdf->Output();
?>