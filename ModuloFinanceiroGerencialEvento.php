<?php 
###########
## Módulo para relatório dos analise gerencial por evento
## Criado: 21/05/2013 - Maycon Edinger
## Alterado: 
## Alterações: 
###########

//Rotina para verificar se necessita ou não montar o header para o ajax
header("Content-Type: text/html; charset=ISO-8859-1",true);

//Com a utilização do AJAX, deve-se efetuar nova conexão e novo processamento de diretivas
// Processa as diretivas de segurança 
require("Diretivas.php");

//Estabelece a conexão com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Verifica se a funcão já foi declarada
if (function_exists("DataMySQLRetornar") == false) 
{

  //Inclui o arquivo para manipulação de datas
  include "./include/ManipulaDatas.php";

}

//Efetua o lookup na tabela de eventos
//Monta o sql de pesquisa
$lista_eventos = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId ORDER BY nome";
//Executa a query
$dados_eventos = mysql_query($lista_eventos);

?>
<script language="JavaScript">

//Função que alterna a visibilidade do painel especificado.
function oculta(id)
{
  ID = document.getElementById(id);
  ID.style.display = "none";
}


function ExecutaConsulta() 
{
	
  var Form;
  Form = document.consulta_data;

  //Monta url que do relatório que será carregado	
  url = "./relatorios/EventoGerencialRelatorioDataPDF.php?UsuarioNome=<?php echo $usuarioNome .  ' ' . $usuarioSobrenome ?>&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>&EventoId=" + document.getElementById('cmbEventoId').value;

  //Executa o relatório selecionado
  abreJanela(url);
	
}
</script>
<form id="consulta_data" name="consulta_data" method="post">

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td>
            <img src="image/lat_cadastro.gif" />&nbsp;<span class="TituloModulo">Relatório de Análise Gerencial por Evento</span>
          </td>
        </tr>
        <tr>
          <td>
            <img src="image/bt_espacohoriz.gif" width="100%" height="12">		
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td style="padding-bottom: 2px">
      <span >
        <input name="Button" type="button" class="button" id="consulta" title="Emite o relatório" value='Emitir Relatório' onclick="ExecutaConsulta()" />
      </span>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="middle"> 
            <table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="listViewPaginationTdS1" style="padding-right: 0px; padding-left: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
                  <table cellspacing="0" cellpadding="0" width="100%" border="0">
                    <tr>
                      <td class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Informe o per&iacute;odo a pesquisar</td>
                    </tr>
                  </table>
                </td>
              </tr>			  
              <tr>
                <td class="dataLabel">Evento:</td>
                <td colspan="3" class="tabDetailViewDF">
                  <select name="cmbEventoId" id="cmbEventoId" style="width: 400px">                  
                    <?php
                    
                      //Cria o componente de lookup de eventos
                      while ($lookup_eventos = mysql_fetch_object($dados_eventos))
                      {
                        
                        ?>
                        <option value="<?php echo $lookup_eventos->id ?>"><?php echo $lookup_eventos->id . " - " . $lookup_eventos->nome ?></option>
                        <?php
                        
                      //Fecha o while
                      }
                      
                    ?>
                  </select>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <div id="resultado_consulta">

      </div>
    </td>
  </tr>
</table>
</form>