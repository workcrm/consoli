<?php
###########
## Módulo para Processar a exclusão de um vale ao colaborador
## Criado: 04/03/2010 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

header("Content-Type: text/html;  charset=ISO-8859-1",true);

//Estabelece a conexão com o banco de dados  
include("./conexao/ConexaoMySQL.php");

//Recebe os parâmetros para montar a exclusão
$ValeId = $_GET["ValeId"];
$ColaboradorId = $_GET["ColaboradorId"];

//Captura o id do lancamento no caixa que está vinculado
$lista_caixa = "SELECT lancamento_caixa_id FROM vales WHERE id = $ValeId";
//Executa a query
$dados_caixa = mysql_query($lista_caixa); 
//Monta o array dos dados
$campos_caixa = mysql_fetch_array($dados_caixa);

$LancamentoCaixaId = $campos_caixa["lancamento_caixa_id"];

//Exclui o vale
mysql_query("DELETE FROM vales WHERE id = $ValeId");

//Exclui o lançamento no caixa
mysql_query("DELETE FROM caixa WHERE id = $LancamentoCaixaId");

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
			    <td>
            <img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Manutenção de Vales para Colaboradores</span>
          </td>
			  </tr>
			  <tr>
			    <td>
				    <img src="image/bt_espacohoriz.gif" width="100%" height="12">
				  </td>
			  </tr>
			</table>
      <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0" class="text">
        <tr>
          <td height="22" width="20" valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; padding-top: 1px; border-right: 0px">
            <img src="./image/bt_informacao.gif" border="0" />
          </td>
          <td valign="middle" bgcolor="#FFFFCD" style="border: solid 1px; padding-left: 4px; border-left: 0px">
            <strong>Vale excluído com sucesso !</strong>
          </td>
        </tr>
      </table>      
    </td>
  </tr>
</table>
<br/>
<input class="button" title="Retorna ao Módulo de Colaboradores" name="btnVoltar" type="button" id="btnVoltar" value="Retornar ao Módulo de Colaboradores" onclick="wdCarregarFormulario('ColaboradorExibe.php?ColaboradorId=<?php echo $ColaboradorId ?>','conteudo')" />