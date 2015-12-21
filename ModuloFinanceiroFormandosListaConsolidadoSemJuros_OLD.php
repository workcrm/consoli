<?php
###########
## Módulo para Listagem da posição financeira dos formandos de um evento consolidado por turma SEM JUROS
## Criado: 02/09/2011 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Desativar o CSS redundante
//<link rel="stylesheet" type="text/css" href="include/workStyle.css">

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de datas
include "./include/ManipulaDatas.php";

//Captura o evento informado
$edtEventoId = $_GET["EventoId"];
$edtContaCaixaId = $_GET["ContaCaixaId"];
$edtContaCaixaId2 = $_GET["ContaCaixaId2"];

//Busca o nome do evento
//Monta o sql
$sql_evento = mysql_query("SELECT 
														nome,
														valor_geral_evento,
														valor_desconto_evento
													FROM 
														eventos 
													WHERE 
														id = $edtEventoId");

//Monta o array com os dados
$dados_evento = mysql_fetch_array($sql_evento);

if ($edtContaCaixaId > 0)
{

  $sql_conta_caixa = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = $edtContaCaixaId");

  //Monta o array com os dados
  $dados_conta_caixa = mysql_fetch_array($sql_conta_caixa);

  $TextoContaCaixa = "<br/>Conta-caixa: <b>($edtContaCaixaId) - " . $dados_conta_caixa["nome"] . "</b><br/>";
  $where_conta_caixa = "AND (rec.subgrupo_conta_id = $edtContaCaixaId)";

  if ($edtContaCaixaId2 > 0)
  {

    $sql_conta_caixa2 = mysql_query("SELECT nome FROM subgrupo_conta WHERE id = $edtContaCaixaId2");

    //Monta o array com os dados
    $dados_conta_caixa2 = mysql_fetch_array($sql_conta_caixa2);

    $TextoContaCaixa2 = "Conta-caixa 2: <b>($edtContaCaixaId2) - " . $dados_conta_caixa2["nome"] . "</b><br/>";
		
    $where_conta_caixa = str_replace(")","", $where_conta_caixa);

    $where_conta_caixa .= " OR rec.subgrupo_conta_id = $edtContaCaixaId2)";

  }
  
}

//Monta a query de filtragem dos itens
$filtra_produtos = "SELECT 
		                	sum(quantidade * valor_venda) AS TOTAL_PRODUTOS
		                FROM 
		                	eventos_item
		                WHERE 
		                	evento_id = $edtEventoId";

//Executa a query
$lista_produtos = mysql_query($filtra_produtos);

//Percorre o array
while ($dados_produtos = mysql_fetch_array($lista_produtos))
{

	$edtTotalProdutos = $dados_produtos['TOTAL_PRODUTOS'];

}

//Monta a query de filtragem dos servicos
$filtra_servicos = "SELECT 
		                	sum(quantidade * valor_venda) AS TOTAL_SERVICOS
		                FROM 
		                	eventos_servico
		                WHERE 
		                	evento_id = $edtEventoId";

//Executa a query
$lista_servicos = mysql_query($filtra_servicos);

//Percorre o array
while ($dados_servicos = mysql_fetch_array($lista_servicos))
{

	$edtTotalServicos = $dados_servicos['TOTAL_SERVICOS'];

}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css" />

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="2" valign="top">
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td style="padding-bottom: 4px;">
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Evento: <span style="color: #990000;"><?php echo $dados_evento["nome"] ?></span></span>			  	
          </td>
        </tr>
        <tr>
          <td style="padding-bottom: 10px;">
            Consulta: <span style=1color: #990000;><strong>POR POSIÇÃO FINANCEIRA DOS FORMANDOS</strong></span>
            <?php echo $TextoContaCaixa ?>
            <?php echo $TextoContaCaixa2 ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
  
<?php


//Monta o sql de filtragem das contas
$sql = "SELECT 
	        rec.id,			
	        rec.pessoa_id,			
	        eve.nome AS evento_nome,
	        form.nome AS formando_nome,
	        form.chk_culto,
	        form.chk_colacao,
	        form.chk_jantar,
	        form.chk_baile
        FROM 
        	contas_receber rec
        LEFT OUTER JOIN 
        	eventos eve ON eve.id = rec.evento_id
        LEFT OUTER JOIN 
        	eventos_formando form ON form.id = rec.pessoa_id
        WHERE 
        	rec.empresa_id = $empresaId 
        AND 
        	rec.evento_id = $edtEventoId
        AND 
        	rec.formando_id > 0
        	$where_conta_caixa
        GROUP BY 
        	formando_nome
        ORDER BY 
        	formando_nome"; 	
		
$query = mysql_query($sql);

$registros = mysql_num_rows($query);

//Caso não encontrar contas
if ($registros == 0)
{
  
  ?>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td>
        Nenhuma conta a receber encontrada !
      </td>
    </tr>
  </table>
  <?php
	
}
	
else
	
{
	
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">	
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
        <tr class="listViewThS1" height="20" background="image/fundo_consulta.gif">                
        <td style="padding-left: 4px">Dados do Sacado/Evento/Formando</td>
        <td width="36" align="center" style="border-left: #aaa 1px dotted">Part.</td>
        <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Contrato</td>
        <td width="36" align="center" style="border-left: #aaa 1px dotted">Parc.</td>
        <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Receber</td>
        <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Vencido</td>
        <td width="80" align="right" style="border-left: #aaa 1px dotted; padding-right: 2px">Vl Recebido</td>        
      </tr>
      <?php

        //Percorre as contas
        while ($dados = mysql_fetch_array($query))
        {

          $desc_participante = '&nbsp;';

          if ($dados["chk_culto"] == 1)
          {

            $desc_participante .= "<span title='Formando Participa do Culto'>M</span>&nbsp;";

          }

          if ($dados["chk_colacao"] == 1)
          {

            $desc_participante .= "<span title='Formando Participa da Colação'>C</span>&nbsp;";

          }

          if ($dados["chk_jantar"] == 1)
          {

            $desc_participante .= "<span title='Formando Participa do Jantar'>J</span>&nbsp;";

          }
							
          if ($dados["chk_baile"] == 1)
          {

            $desc_participante .= "<span title='Formando Participa do Baile'>B</span>";

          }	

          $FormandoId = $dados["pessoa_id"];

          //Pega o numero de parcelas e o valor
          //Monta o sql de filtragem das contas
          $sql = "SELECT 
                  	count(1) AS total_parcelas,
                  	valor_original AS valor_parcela
                  FROM 
                  	contas_receber rec
                  WHERE 
                  	empresa_id = $empresaId AND pessoa_id = $FormandoId
                  	$where_conta_caixa
                  GROUP BY 
                  	pessoa_id";								
								
	        $query_parcela = mysql_query($sql);

	        $registros_parcela = mysql_num_rows($query_parcela);

	        //Verifica se possuem registros
	        if ($registros_parcela > 0)
	        {

	          //Percorre as contas
	          while ($dados_parcela = mysql_fetch_array($query_parcela))
	          {

	            $numero_parcelas = $dados_parcela["total_parcelas"];
	            $valor_parcela = $dados_parcela["valor_parcela"];

	          }
	          
	        }
		
	        $hoje = date('Y-m-d', mktime());

	        $TextoSituacao = " ";

	        //Pega o numero contas recebidas
	        //Monta o sql de filtragem das contas
	        $sql = "SELECT 
	                	SUM(valor_original) AS valor_recebida						
	                FROM 
	                	contas_receber rec
	                WHERE 
	                	empresa_id = $empresaId 
	                AND 
	                	pessoa_id = $FormandoId
	                AND 
	                	situacao = 2
	                	$where_conta_caixa"; 
				
				
	        $query_recebida = mysql_query($sql);

	        $registros_recebida = mysql_num_rows($query_recebida);

	        //Verifica se possuem registros
	        if ($registros_recebida > 0)
	        {

	          //Percorre as contas
	          while ($dados_recebida = mysql_fetch_array($query_recebida))
	          {

	            $valor_recebida = $dados_recebida["valor_recebida"];

	            $geral_recebida = $geral_recebida + $dados_recebida["valor_recebida"];

	          }

	        }

	        //Pega o numero contas a receber
	        //Monta o sql de filtragem das contas
	        $sql = "SELECT 
	                	SUM(valor_original) AS valor_receber							
	                FROM 
	                	contas_receber rec
	                WHERE 
	                	empresa_id = $empresaId 
	                AND 
	                	pessoa_id = $FormandoId
	                AND 
	                	situacao = 1
	                	$where_conta_caixa";  


	        $query_receber = mysql_query($sql);

	        $registros_receber = mysql_num_rows($query_receber);

	        //Verifica se possuem registros
	        if ($registros_receber > 0)
	        {

	          //Percorre as contas
	          while ($dados_receber = mysql_fetch_array($query_receber))
	          {

	            $valor_receber = $dados_receber["valor_receber"];

	            $geral_receber = $geral_receber + $dados_receber["valor_receber"];

	          }

	        }

	        //Pega o numero contas a receber vencidas
	        $data_base_vencida = date("Y-m-d" , mktime());

	        //Monta o sql de filtragem das contas
	        $sql = "SELECT 
	                	SUM(valor_original) AS valor_vencida							
	                FROM 
	                	contas_receber rec
	                WHERE 
	                	empresa_id = $empresaId 
	                AND 
	                	data_vencimento < '$data_base_vencida'
	                AND 
	                	pessoa_id = $FormandoId
	                AND 
	                	situacao = 1
	                	$where_conta_caixa";   

	        $query_vencida = mysql_query($sql);

	        $registros_vencida = mysql_num_rows($query_vencida);

	        //Verifica se possuem registros
	        if ($registros_vencida > 0)
	        {

	          //Percorre as contas
	          while ($dados_vencida = mysql_fetch_array($query_vencida))
	          {

	            $valor_vencida = $dados_vencida["valor_vencida"];

	            $geral_vencida = $geral_vencida + $dados_vencida["valor_vencida"];

	          }

	        }

	        //Monta o sql de filtragem das contas
	        $sql = "SELECT 
	                  rec.id,	
	                  SUM(valor_original) AS valor_contrato								
	                FROM 
	                	contas_receber rec
	                WHERE 
	                	rec.empresa_id = $empresaId 
	                AND 
	                	rec.pessoa_id = $FormandoId
	                	$where_conta_caixa
	                GROUP BY 
	                	rec.pessoa_id";						

	        $query_formando = mysql_query($sql);

	        $registros_formando = mysql_num_rows($query_formando);

	        //Verifica se possuem registros
	        if ($registros_formando > 0)
	        {

	          //Percorre as contas
	          while ($dados_formando = mysql_fetch_array($query_formando))
	          {

	            $valor_contrato = $dados_formando["valor_contrato"];
	            $geral_contrato = $geral_contrato + $valor_contrato;

	            ?>
	            <tr height="22">                
	              <td style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b><?php echo $dados["formando_nome"] ?></b></td>
	              <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $desc_participante ?></td>
	              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_contrato, 2,",",".") ?></td>
	              <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted"><?php echo $numero_parcelas ?></td>
	              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_receber ,2,",","."); ?></td>
	              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_vencida ,2,",","."); ?></td>
	              <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><?php echo number_format($valor_recebida, 2,",",".") ?></td>							
	            </tr>
	            <?php

	          }

	        }					

	      }

	    //Imprime os totais		
	    ?>
	    <tr height="22">                
	      <td colspan="2" align="right" style="padding-left: 4px; border-bottom: #aaa 1px dotted"><b>TOTAL:&nbsp;</b></td>
	      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_contrato,2,",",".") ?></b></td>
	      <td align="center" style="border-left: #aaa 1px dotted; border-bottom: #aaa 1px dotted">&nbsp;</td>
	      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_receber,2,",",".") ?></b></td>
	      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_vencida,2,",",".") ?></b></td>
	      <td align="right" style="border-left: #aaa 1px dotted; padding-right: 2px; border-bottom: #aaa 1px dotted"><b><?php echo number_format($geral_recebida,2,",",".") ?></b></td>					
	    </tr>			
	  </table>
	  <b>* Este relatório é meramente ilustrativo, podendo sofrendo alterações conforme a variação de produtos e alunos.</b>
	  <br/>
	  <br/>
    <table width="350" border="0" cellpadding="0" cellspacing="0" class="listView">	
      <tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
          Valor Orçado R$: 
        </td>
        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
          <b><?php echo number_format($dados_evento["valor_geral_evento"], 2, ",", ".") ?></b>
        </td>
      </tr>
      <tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px; border-bottom: 1px solid">
          Valor Orçamento Atual R$: 
        </td>
        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
          <b>
        	<?php 

    				$edtTotalValorOrcamento = ($edtTotalProdutos + $edtTotalServicos) - $dados_evento['valor_desconto_evento'];
        		echo number_format($edtTotalValorOrcamento, 2, ",", ".");

    		 ?>
        	</b>
        </td>
      </tr>
      <tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
          Valor em Contratos Individuais R$: 
        </td>
        <td style="border-left: 1px solid; border-bottom: 1px solid; padding-right: 8px" align="right">
          <b><?php echo number_format($geral_contrato, 2, ",", ".") ?></b>
        </td>
      </tr>
      <tr>
        <td width="200" class="listViewThS1" height="20" background="image/fundo_consulta.gif" style="padding-left: 10px;">
          <?php

            $diferenca_total = $geral_contrato - $edtTotalValorOrcamento;
            @$diferenca_percentual = ($diferenca_total / $edtTotalValorOrcamento) * 100;

          ?>
          Diferença R$: 
        </td>
        <td style="border-left: 1px solid; padding-right: 8px" align="right">
          <span style="color: #990000"><b><?php echo number_format($diferenca_total, 2, ",", ".") . ' (' .  number_format($diferenca_percentual, 0, ",", ".") . '%)' ?></b></span>
        </td>
      </tr>
    </table>
	</td>
	</tr>
	</table>
		<?php
	
  }
  
?>