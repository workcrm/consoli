<?php
###########
## Módulo para Exibição do planejamento orçamentário
## Criado: 03/10/2012 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{
	
  //Inclui o arquivo para manipulação de datas
  include "./include/ManipulaDatas.php";

}

$ano = $_GET["cmbAno"];

$regional = $_GET["cmbRegional"];

if ($regional > 0)
{ 

  $filtra_regiao = "AND orc.regional = '$regional'";
  $filtra_regiao_cpag = "AND pag.regiao_id = '$regional'";
	
}

$centro_custo = $_GET["cmbCCU"];

if ($centro_custo > 0) $filtra_centro_custo = "AND orc.centro_custo = '$centro_custo'";

$conta_caixa = $_GET["cmbCCX"];

if ($conta_caixa > 0) $filtra_conta_caixa = "AND orc.conta_caixa = '$conta_caixa'";

$consulta = "SELECT 
            orc.id,
            orc.tipo,
            orc.ano,
            orc.regional,
            orc.centro_custo,
            orc.conta_caixa,
            SUM(orc.valor) AS valor,
            reg.nome AS regional_nome,
            ccu.nome AS centro_custo_nome,
            ccx.nome AS conta_caixa_nome
            FROM orcamentario orc
            LEFT OUTER JOIN regioes reg ON reg.id = orc.regional
            LEFT OUTER JOIN grupo_conta ccu ON ccu.id = orc.centro_custo
            LEFT OUTER JOIN subgrupo_conta ccx ON ccx.id = orc.conta_caixa
            WHERE 1 = 1
            AND ano = '$ano'
            $filtra_regiao
            $filtra_centro_custo
            $filtra_conta_caixa
            GROUP BY orc.ano, ccu.nome, ccx.nome
            ORDER BY orc.ano, ccu.nome, ccx.nome";

//debug
//echo $consulta;
			
//Executa a query
$listagem = mysql_query($consulta);

//Conta o numero de contas que a query retornou
$registros = mysql_num_rows($listagem);

?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <?php

        if ($registros == 0) 
        { 

          echo "<table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>
                  <tr class='listViewThS1' height='20' background='image/fundo_consulta.gif'>
                    <td  scope='col'>&nbsp;&nbsp;Não há planejamento orçado com o filtro de consulta informado !</td>
                  </tr>
                </table>";

        } 

        else 

        {

          $ano_base = 0;
          $ccu_base = '';
					
          echo "<br/>
                <table width='100%' cellpadding='0' cellspacing='0' border='0' class='listView'>";
				
          //Monta e percorre o array dos dados
          while ($dados = mysql_fetch_array($listagem))
          {

            $valor = number_format($dados['valor'],2,',','.');				

            if ($ano_base != $dados['ano'])
            {

              echo "<tr height='20'>
                      <td colspan='8' class='listViewThS1' align='center'>
                        <font size='3'><span style='color: #990000'>[$dados[ano]]</span></font>
                      </td>
                    </tr>
                    <tr height='20'>
                      <td class='listViewThS1' style='padding-left: 25px'>
                        Conta-Caixa
                      </td>
                      <td class='listViewThS1' align='right' style='padding-right: 5px'>
                        Orçado
                      </td>
                      <td class='listViewThS1' align='right' style='padding-right: 5px'>
                        Executado
                      </td>
                      <td class='listViewThS1' align='center'>
                        % Exec
                      </td>
                      <td class='listViewThS1' align='right' style='padding-right: 5px'>
                        Média/Mês
                      </td>
                      <td class='listViewThS1' align='right' style='padding-right: 5px'>
                        Média/Dia
                      </td>
                      <td class='listViewThS1' align='right' style='padding-right: 5px'>
                        Projeção
                      </td>
                      <td class='listViewThS1' align='center' style='padding-right: 10px'>
                        % Proj
                      </td>
                    </tr>";

            }

            if ($ccu_base != $dados['centro_custo_nome'])
            {
						
              //Caso nao seja o primeiro registro imprime o totalizador
              if ($ccu_base != '')
              {

                $total_centro_custo_orcado_formata = number_format($total_centro_custo_orcado, 2,',','.');
                $total_centro_custo_executado_formata = number_format($total_centro_custo_executado, 2,',','.');

                //Cacula o percentual projetado
                $percentual_total_centro_custo = number_format(($total_centro_custo_executado / $total_centro_custo_orcado) * 100, 2);

                if ($total_centro_custo_executado > $total_centro_custo_orcado) $formata_total_centro_custo_executado = 'color: #990000';

                //Calcula a média diaria dos gastos executados
                @$media_dia_centro_custo = $total_centro_custo_executado / $dias_passado;
                @$media_dia_centro_custo_formata = number_format($media_dia_centro_custo,2,',','.');

                @$media_mes_centro_custo = number_format(($total_centro_custo_executado) / ($m3 - 1),2,',','.');

                $projecao_centro_custo = ($media_dia_centro_custo * $dias_restante) + $total_centro_custo_executado;
                $projecao_centro_custo_formata = number_format($projecao_centro_custo,2,',','.');

                @$percentual_projetado_centro_custo = number_format(($projecao_centro_custo / $total_centro_custo_orcado) * 100, 2,'.','.');

                $formata_centro_custo_projecao = '';
                if ($projecao_centro_custo > $total_centro_custo_orcado) $formata_centro_custo_projecao = 'color: #990000';
				
                echo "<tr height='20'>
                        <td valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted; padding-right: 5x' align='right'>
                          <span class='oddListRowS1' style='padding: 5px'><b>TOTAL CENTRO DE CUSTO:</b></span>
                        </td>
                        <td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span class='oddListRowS1' style='padding: 5px'>$total_centro_custo_orcado_formata</span>
                        </td>
                        <td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span class='oddListRowS1' style='padding: 5px; $formata_total_centro_custo_executado'>$total_centro_custo_executado_formata</span>
                        </td>
                        <td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span style='padding: 5px'>$percentual_total_centro_custo%</span>
                        </td>
                        <td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span style='padding: 5px'>$media_mes_centro_custo</span>
                        </td>
                        <td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span style='padding: 5px'>$media_dia_centro_custo_formata</span>
                        </td>
                        <td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
                          <span style='padding: 5px; $formata_centro_custo_projecao'>$projecao_centro_custo_formata</span>
                        </td>
                        <td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; padding-right: 5px' align='right'>
                          <span style='padding: 5px'>$percentual_projetado_centro_custo%</span>
                        </td>
                      </tr>";

                      $total_centro_custo_orcado = 0;
                      $total_centro_custo_executado = 0;

                      //Cacula o percentual projetado
                      $percentual_total_centro_custo = 0;

                      $media_dia_centro_custo = 0;
                      $media_mes_centro_custo = 0;

                      $projecao_centro_custo = 0;
                      $percentual_projetado_centro_custo = 0;

                      $formata_centro_custo_projecao = '';

                    }

                    echo "
                    <tr height='22'>
                      <td colspan='8' valign='middle' bgcolor='#fdfdfd' class='oddListRowS1' style='padding: 0px; padding-top:6px' >
                        <span class='oddListRowS1' style='padding: 5px'><strong>Centro de Custo: $dados[centro_custo_nome]</strong></span>
                      </td>
                    </tr>";

                  }

                  //Verifica o total de contas a pagar do filtro
                  $grupo_pesquisa = $dados['centro_custo'];
                  $subgrupo_pesquisa = $dados['conta_caixa'];

                  $busca_contas = "SELECT sum(sai.total_pago) AS total, 
                                  pag.grupo_conta_id, 
                                  pag.subgrupo_conta_id
                                  FROM contas_pagar_pagamento sai 
                                  LEFT OUTER JOIN contas_pagar pag ON pag.id = sai.conta_pagar_id 
                                  WHERE sai.data_pagamento between '$ano-01-01' AND '$ano-12-31'
                                  $filtra_regiao_cpag
                                  AND pag.grupo_conta_id = $grupo_pesquisa
                                  AND pag.subgrupo_conta_id = $subgrupo_pesquisa
                                  GROUP BY pag.subgrupo_conta_id";
										
                                  //Executa a query
                                  $lista_contas = mysql_query($busca_contas);

                                  //Conta o numero de contas que a query retornou
                                  $registros_contas = mysql_num_rows($lista_contas);

                                  if ($registros_contas > 0)
                                  {
						
                                    //Limpa as formatacoes
                                    $formata_executado = '';
                                    $formata_projecao = '';

                                    $dados_conta = mysql_fetch_array($lista_contas);

                                    $total_executado = number_format($dados_conta['total'],2,',','.');

                                    //Cacula o percentual executado em relacao ao orcado
                                    $percentual = number_format(($dados_conta['total'] / $dados['valor']) * 100, 2);

                                    //Funcao para calcular o numero de meses decorridos do ano
                                    //Sempre retornara o inteiro do mes ate ultimo dia do mes anterior
                                    $data1 = "01/01/" . $ano; 

                                    $arr = explode('/',$data1); 

                                    $dia_mes_atual = date("t", mktime());
                                    $mes_atual = date("m", mktime());

                                    $data2 = $dia_mes_atual . "/" . $mes_atual . "/" . $ano;

                                    $arr2 = explode('/',$data2); 

                                    $dia1 = $arr[0]; 
                                    $mes1 = $arr[1]; 
                                    $ano1 = $arr[2]; 

                                    $dia2 = $arr2[0]; 
                                    $mes2 = $arr2[1]; 
                                    $ano2 = $arr2[2]; 

                                    $a1 = ($ano2 - $ano1)*12;
                                    $m1 = ($mes2 - $mes1)+1;
                                    $m3 = ($m1 + $a1);

                                    $m3;

                                    //Calcula a média do mes
                                    @$media_mes = number_format(($dados_conta['total']) / ($m3 - 1),2,',','.');

                                    $hoje = date('Y-m-d', mktime());
                                    $fim_ano = $ano . "-12-31"; 
                                    $ini_ano = $ano . "-01-01";

                                    $dias_restante = diffDate($hoje, $fim_ano);
                                    $dias_passado = diffDate($ini_ano, $hoje);

                                    //Calcula a média diaria dos gastos executados
                                    $media_dia = $dados_conta['total'] / $dias_passado;
                                    $media_dia_formata = number_format($media_dia,2,',','.');

                                    //Calcula a projecao dos gastos baseado na media por dia X dias restantes no ano
                                    $projecao = ($media_dia * $dias_restante) + $dados_conta['total'];
                                    $projecao_formata = number_format($projecao,2,',','.');

                                    //Cacula o percentual projetado
                                    $percentual_projetado = number_format(($projecao / $dados['valor']) * 100, 2);

                                    //Ajusta as formatacoes caso passe do previsto
                                    if ($dados_conta['total'] > $dados['valor']) $formata_executado = 'color: #990000';
                                    if ($projecao > $dados['valor']) $formata_projecao = 'color: #990000';
							
                                    //Efetua as totalizacoes
                                    $total_geral_orcado = $total_geral_orcado + $dados['valor'];
                                    $total_centro_custo_orcado = $total_centro_custo_orcado + $dados['valor'];

                                    $total_geral_executado = $total_geral_executado + $dados_conta['total'];
                                    $total_centro_custo_executado = $total_centro_custo_executado + $dados_conta['total'];

                                    $conta_caixa = substr($dados[conta_caixa_nome],0,30);

                                    /*
                                    echo '<br/>Dias Restante:' . $dias_restante;
                                    echo '<br/>Dias Passado:' . $dias_passado;
                                    echo '<br/>Media Diária:' . $media_dia;
                                    */

                                  }

                                  else

                                  {
						
                                    //Limpa as formatacoes
                                    $formata_executado = '';
                                    $formata_projecao = '';

                                    //Calula a soma do previsto mesmo assim
                                    $total_geral_orcado = $total_geral_orcado + $dados['valor'];
                                    $total_centro_custo_orcado = $total_centro_custo_orcado + $dados['valor'];

                                    //Zera as variaveis
                                    $conta_caixa = substr($dados[conta_caixa_nome],0,30);
                                    $total_executado = '0,00';
                                    $percentual = '0.00';
                                    $media_mes = '0,00';
                                    $media_dia_formata = '0,00';
                                    $projecao_formata = '0,00';
                                    $percentual_projetado = '0.00';

                                  }
						
                                  //Cria o link do relatorio a exibir
                                  //Monta url que do relatório que será carregado	
                                  $conta_caixa_id = $dados['conta_caixa'];
                                  $centro_custo_id = $dados['centro_custo'];
                                  $abre_conta = "abreJanela(\"./relatorios/CaixaAnaliticoOrcamentarioRelatorioPDF.php?&TipoLancamento=2&Regiao=$regional&GrupoId=$centro_custo_id&SubGrupoId=$conta_caixa_id&DataIni=01/01/$ano&DataFim=31/12/$ano&ContaCaixaId=$conta_caixa_id&UsuarioNome=$usuarioNome&EmpresaId=$empresaId&EmpresaNome=$empresaNome&EmpresaId=$empresaId\")";
                                  $abre_grafico = "abreJanela(\"./graficos/AbreGraficoOrcamentario.php?&TipoLancamento=2&Regiao=$regional&GrupoId=$centro_custo_id&SubGrupoId=$conta_caixa_id&DataIni=01/01/$ano&DataFim=31/12/$ano&ContaCaixaId=$conta_caixa_id&EmpresaId=$empresaId\")";

                                  echo "<tr height='22' valign='middle'>
                                          <td valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted; padding-left: 4px'>
                                            <img src='image/bt_relatorio.gif' width='13' height='16' onclick='$abre_conta' style='cursor: pointer' title='Clique para exibir o detalhamento do executado da conta-caixa'>&nbsp;<img src='image/bt_grafico.png' width='16' height='16' onclick='$abre_grafico' style='cursor: pointer' title='Clique para exibir o gráfico executado da conta-caixa'><span class='oddListRowS1' style='padding: 5px'>$conta_caixa</span>
                                          </td>
                                          <td width='90' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span class='oddListRowS1' style='padding: 5px'>$valor</span>
                                          </td>
                                          <td width='90' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span class='oddListRowS1' style='padding: 5px; $formata_executado'>$total_executado</span>
                                          </td>
                                          <td width='60' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span style='padding: 5px'>$percentual%</span>
                                          </td>
                                          <td width='70' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span style='padding: 4px'>$media_mes</span>
                                          </td>
                                          <td width='60' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span style='padding: 4px'>$media_dia_formata</span>
                                          </td>
                                          <td width='80' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; border-right: 1px dotted' align='right'>
                                            <span style='padding: 5px; $formata_projecao'>$projecao_formata</span>
                                          </td>
                                          <td width='60' valign='middle' bgcolor='#fdfdfd' class='currentTabList' style='border-top: 1px dotted; padding-right: 5px' align='right'>
                                            <span style='padding: 5px;'>$percentual_projetado%</span>
                                          </td>
                                        </tr>";
						
                                  //Define as variaveis das datas decorridas do ano e o numero de dias restantes
                                  $ano_base = $dados['ano'];
                                  $ccu_base = $dados['centro_custo_nome'];

                                //Fecha o while
                                }
				
				$total_geral_orcado_formata = number_format($total_geral_orcado, 2,',','.');
				$total_centro_custo_orcado_formata = number_format($total_centro_custo_orcado, 2,',','.');
				$total_geral_executado_formata = number_format($total_geral_executado, 2,',','.');
				$total_centro_custo_executado_formata = number_format($total_centro_custo_executado, 2,',','.');
				
				//Cacula o percentual projetado
				@$percentual_total_geral = number_format(($total_geral_executado / $total_geral_orcado) * 100, 2);
				@$percentual_total_centro_custo = number_format(($total_centro_custo_executado / $total_centro_custo_orcado) * 100, 2);
				
				if ($total_geral_executado > $total_geral_orcado) $formata_total_geral_executado = 'color: #990000';
				if ($total_centro_custo_executado > $total_centro_custo_orcado) $formata_total_centro_custo_executado = 'color: #990000';
				
				//Calcula a média diaria dos gastos executados
				@$media_dia_centro_custo = $total_centro_custo_executado / $dias_passado;
				$media_dia_centro_custo_formata = number_format($media_dia_centro_custo,2,',','.');
				
				@$media_dia_geral = $total_geral_executado / $dias_passado;
				$media_dia_geral_formata = number_format($media_dia_geral,2,',','.');
				
				@$media_mes_centro_custo = number_format(($total_centro_custo_executado) / ($m3 - 1),2,',','.');
				@$media_mes_geral = number_format(($total_geral_executado) / ($m3 - 1),2,',','.');
				
				$projecao_centro_custo = ($media_dia_centro_custo * $dias_restante) + $total_centro_custo_executado;
				$projecao_centro_custo_formata = number_format($projecao_centro_custo,2,',','.');
				
				$projecao_geral = ($media_dia_geral * $dias_restante) + $total_geral_executado;
				$projecao_geral_formata = number_format($projecao_geral,2,',','.');
				
				@$percentual_projetado_centro_custo = number_format(($projecao_centro_custo / $total_centro_custo_orcado) * 100, 2);
				@$percentual_projetado_geral = number_format(($projecao_geral / $total_geral_orcado) * 100, 2);
				
				$formata_centro_custo_projecao = '';
				if ($projecao_centro_custo > $total_centro_custo_orcado) $formata_centro_custo_projecao = 'color: #990000';
				
				$formata_geral_projecao = '';
				if ($projecao_geral > $total_geral_orcado) $formata_geral_projecao = 'color: #990000';
				
				
				echo "
						<tr height='20'>
							<td valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted; padding-right: 5x' align='right'>
								<span class='oddListRowS1' style='padding: 5px'><b>TOTAL CENTRO DE CUSTO:</b></span>
							</td>
							<td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span class='oddListRowS1' style='padding: 5px'>$total_centro_custo_orcado_formata</span>
							</td>
							<td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span class='oddListRowS1' style='padding: 5px; $formata_total_centro_custo_executado'>$total_centro_custo_executado_formata</span>
							</td>
							<td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$percentual_total_centro_custo%</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$media_mes_centro_custo</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$media_dia_centro_custo_formata</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px; $formata_centro_custo_projecao'>$projecao_centro_custo_formata</span>
							</td>
							<td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; padding-right: 5px' align='right'>
								<span style='padding: 5px'>$percentual_projetado_centro_custo%</span>
							</td>
						</tr>
						<tr height='20'>
							<td valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted; padding-right: 5x' align='right'>
								<span class='oddListRowS1' style='padding: 5px'><b>TOTAL GERAL:</b></span>
							</td>
							<td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span class='oddListRowS1' style='padding: 5px'>$total_geral_orcado_formata</span>
							</td>
							<td width='90' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span class='oddListRowS1' style='padding: 5px; $formata_total_geral_executado'>$total_geral_executado_formata</span>
							</td>
							<td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$percentual_total_geral%</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$media_mes_geral</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px'>$media_dia_geral_formata</span>
							</td>
							<td width='80' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; border-right: 1px dotted' align='right'>
								<span style='padding: 5px; $formata_geral_projecao'>$projecao_geral_formata</span>
							</td>
							<td width='60' valign='middle' bgcolor='lightgray' class='currentTabList' style='border-top: 1px solid; padding-right: 5px' align='right'>
								<span style='padding: 5px'>$percentual_projetado_geral%</span>
							</td>
						</tr>";
						
				echo "</table>";
				
				echo "<br/><b>Dias decorridos:</b> " . $dias_passado . '<b> - Dias Restantes:</b> ' . $dias_restante;
			 
				//Fecha o if de se conter registros na consulta
				}
		   
			?>
		
		</td>
	</tr>
</table>	
