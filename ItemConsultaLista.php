<?php 
###########
## Módulo para Consulta de Itens do evento
## Criado: 06/12/2011 - Maycon Edinger
## Alterado: 
## Alterações:
###########

//Seta o header do retorno para efetuar a acentuação correta usando o AJAX
if ($_GET["headers"] == 1)
{

  header("Content-Type: text/html;  charset=ISO-8859-1",true);

}
	
//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
//Processa as diretivas de segurança 
@require_once("Diretivas.php");

//Estabelece a conexão com o banco de dados
@require_once "./conexao/ConexaoMySQL.php";

//Inclui o arquivo para manipulação de valor monetário
@require_once "./include/ManipulaMoney.php";

$edtPesquisa = $_GET[edtPesquisa];
$edtCategoria = $_GET[edtCategoria];
$edtDepartamento = $_GET[edtDepartamento];
$edtLetra = $_GET[ChaveFiltragem];

//Caso tenha informado um nome ou codigo a pesquisar
if($edtPesquisa != '')
{
	
  $pesquisa_nome = "AND ite.nome LIKE '%$edtPesquisa%' OR ite.id = '$edtPesquisa'";
	
}

//Caso tenha informado uma categoria
if($edtCategoria > 0)
{
	
  $pesquisa_nome = "AND ite.categoria_id = '$edtCategoria'";
		
}


//Caso tenha informado um departamento
if($edtDepartamento > 0)
{
	
  $pesquisa_nome = "AND ite.departamento_id = '$edtDepartamento'";
		
}

//Caso tenha informado uma letra a pesquisar
if($edtLetra != '')
{
	
  $pesquisa_nome = "AND ite.nome LIKE '$edtLetra%'";
	
}

//Caso tenha informado uma letra a pesquisar
if($edtLetra == 'todos')
{
	
  $pesquisa_nome = "";
	
}

?>

<link rel="stylesheet" type="text/css" href="include/workStyle.css">


<table width="100%" cellpadding="0" cellspacing="0" border="0" class="listView">
  <tr>
    <td colspan="8" align="right">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td height="20" nowrap align="left"  class="listViewPaginationTdS1"><span class="pageNumbers">Produtos Cadastrados</span></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr height="20">
    <td width="50" class="listViewThS1">
      <div align="center">A&ccedil;&atilde;o</div>
    </td>
    <td align="center" width="50" class="listViewThS1" style="padding-left: 0px">Cód.</td>
    <td align="center" width="20" class="listViewThS1" title="Classificação" style="padding-left: 0px">C</td>
    <td class="listViewThS1">&nbsp;&nbsp;Descrição do Produto</td>
    <td align="center" width="20" class="listViewThS1">Un</td>
    <td align="right" width="60" class="listViewThS1">Custo</td>
    <td align="right" width="60" class="listViewThS1">Minimo</td>				        
    <td align="right" width="60" class="listViewThS1" style="padding-right: 4px">Atual</td>
  </tr>
  <?php
  
    //Monta a tabela de consulta dos itens acadastrados
    //Cria a SQL
    $consulta = "SELECT 
                ite.id,
                ite.nome,
                ite.classificacao,
                ite.valor_custo,
                ite.estoque_atual,
                ite.estoque_minimo,
                ite.unidade,
                ite.ativo,
                ite.categoria_id,
                cat.nome as categoria_nome 
                FROM item_evento ite 
                LEFT OUTER JOIN categoria_item cat ON cat.id = ite.categoria_id
                WHERE ite.empresa_id = $empresaId 
                AND ite.tipo_produto = '1'
                $pesquisa_nome					
                ORDER BY cat.nome, ite.nome";

    //Executa a query
    $listagem = mysql_query($consulta);

    $registros = mysql_num_rows($listagem);

    $categoria_lista = 0;

    $linha = 1;

    //Monta e percorre o array com os dados da consulta
    while ($dados = mysql_fetch_array($listagem))
    {

      if ($linha < $registros)
      {

        $borda = "border-bottom: 1px #aaa dashed;";

      }

      else
      {

        $borda = '';

      }

      //Efetua o switch para exibir a imagem para quando o cadastro estiver ativo
      switch ($dados["classificacao"])
      {

        case 1: $texto_class = "<span title='Produto'>P</span>"; break;
        case 2: $texto_class = "<span title='Serviço'>S</span>"; break;
        
      }

      //Efetua o switch do campo de unidade de medida
      switch ($dados[unidade])
      {

        case "PC": $texto_unidade = "PC - Peça"; break;
        case "UN": $texto_unidade = "UN - Unidade"; break;
        case "GR": $texto_unidade = "GR - Grama"; break;
        case "KG": $texto_unidade = "KG - Kilo"; break;
        case "LT": $texto_unidade = "LT - Litro"; break;
        case "PT": $texto_unidade = "PT - Pacote"; break;
        case "VD": $texto_unidade = "VD - Vidro"; break;
        case "LT": $texto_unidade = "LT - Lata"; break;
        case "BD": $texto_unidade = "BD - Balde"; break;
        case "CX": $texto_unidade = "CX - Caixa"; break;
        case "GL": $texto_unidade = "GL - Galão"; break;
        case "MT": $texto_unidade = "MT - Metro"; break;
        case "M2": $texto_unidade = "M2 - Metro Quadrado"; break;
        case "M3": $texto_unidade = "M3 - Metro Cúbico"; break;
      }

      //Caso seja uma outra categoria de produtos.
      if ($categoria_lista != $dados["categoria_id"])
      {

        echo "<tr height='16'>
                <td colspan='8' style='padding-left: 6px; padding-top: 5px; padding-bottom: 5px'><span class='TituloModulo'>$dados[categoria_nome]</span></td>
              </tr>";
      }

    //Fecha o php, mas o while continua
    ?>
    <tr height="16">
      <td height="24" style="<?php echo $borda ?>">
        <div align="center">
          <?php
          /*
            <img src="image/grid_exclui.gif" alt="Excluir Registro" width="12" height="12" border="0" onclick="if(confirm('Confirma a exclusão do registro ?\nA exclusão de registros desta tabela não é recomendada.\nRecomendamos a utilização da caixa [Cadastro Ativo] caso desejar desativar um registro.')) {wdCarregarFormulario('ProcessaExclusaoGet.php?Id=<?php echo $dados[id] ?>&Modulo=item_evento&Retorno=ItemCadastra','conteudo')}" style="cursor: pointer"></a>
           */
          ?>
          <img src="image/grid_edita.gif" alt="Editar Registro" width="12" height="12" border="0" onclick="wdCarregarFormulario('ItemAltera.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">      
          <img src="image/grid_composicao.gif" alt="Gerenciar a composição dos materiais do produto" width="12" height="12" border="0" onclick="wdCarregarFormulario('ItemComposicaoCadastra.php?ItemId=<?php echo $dados[id] ?>&headers=1','conteudo')" style="cursor: pointer">    
        </div>
      </td>
      <td align="center" style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="currentTabList">
        <?php echo $dados["id"] ?>
      </td>
      <td align="center" style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="currentTabList">
        <?php echo $texto_class ?>
      </td>
      <td style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="oddListRowS1" onclick="wdCarregarFormulario('ItemExibe.php?Id=<?php echo $dados[id] ?>&headers=1','conteudo')">
        <a title="Clique para exibir este registro" href="#"><?php echo $dados[nome] ?></a>
      </td>
      <td style="<?php echo $borda ?>" valign="middle" bgcolor="#fdfdfd" class="currentTabList">
        &nbsp;<span title="<?php echo $texto_unidade ?>"><?php echo $dados["unidade"] ?></span>
      </td>
      <td style="<?php echo $borda ?>" align="right" bgcolor="#fdfdfd" class="currentTabList">
        <?php echo number_format($dados["valor_custo"], 2, ",", ".") ?>
      </td>
      <td style="<?php echo $borda ?>" align="right" bgcolor="#fdfdfd" class="currentTabList">
        <?php echo number_format($dados["estoque_minimmo"], 3, ".", ",") ?>
      </td>
      <td style="padding-right: 4px;<?php echo $borda ?>" align="right" bgcolor="#fdfdfd" class="currentTabList">
        <?php echo number_format($dados["estoque_atual"], 3, ".", ",") ?>
      </td>
      <?php

        $categoria_lista = $dados["categoria_id"];

        $linha++;

      //Fecha o while
      }
      
    ?>
  </tr>
</table>