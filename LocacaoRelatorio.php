<?php
###########
## Módulo para opções do relatório da locacao
## Criado: 30/08/2007 - Maycon Edinger
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

//Recupera o id do evento a imprimir
$LocacaoId = $_GET[LocacaoId];

//Recupera o nome do evento
$query_evento = mysql_query("SELECT descricao FROM locacao WHERE id = $LocacaoId");

//Monta o array
$dados_locacao = mysql_fetch_array($query_evento);
?>

<script language="javascript">
function wdCarregarRelatorio() {
   
	var Form;
  Form = document.cadastro;
   
	
	//Verifica se o checkbox de valores está ativo
	if (Form.chkValores.checked) {
	 var chkValoresValor = 1;
	} else {
	 var chkValoresValor = 0;
	}
   
	
	//Verifica se o checkbox de declaracao está ativo
	if (Form.chkDeclaracao.checked) {
	 var chkDeclaracaoValor = 1;
	} else {
	 var chkDeclaracaoValor = 0;
	}

	//Monta url que do relatório que será carregado	
	url = "./relatorios/LocacaoDetalheRelatorioPDF.php?LocacaoId=<?php echo $LocacaoId ?>&UsuarioNome=<?php echo $usuarioNome . " " . $usuarioSobrenome ?>&Iv=" + chkValoresValor + "&Id=" + chkDeclaracaoValor + "&EmpresaId=<?php echo $empresaId ?>&EmpresaNome=<?php echo $empresaNome ?>&EmpresaId=<?php echo $empresaId ?>";
		 
  //Executa o relatório selecionado
	abreJanela(url);	 	 
}
</script>

<form id="form" name="cadastro" method="post">

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top">
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
		      <td>
			    	<img src="image/lat_cadastro.gif"/>&nbsp;<span class="TituloModulo">Emissão dos Relatórios da Locação</span>			  	
					</td>
		    </tr>
		    <tr>
		      <td>
			    	<img src="image/bt_espacohoriz.gif" width="100%" height="12">
		  	  </td>
		    </tr>
      	<tr>
    			<td>
						<input class="button" title="Retorna a exibição do detalhamento da locação" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar a Locação" style="width:120px" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $LocacaoId ?>&headers=1','conteudo')"/>
						<input class="button" title="Emite o relatório da locação" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()" />
		      	<br />
		      	<br />	   	   		   		
 					</td>   
  			</tr> 
		  </table>
    </td>
  </tr>
 </table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
          <td colspan="2">
          	<span class="TituloModulo"><span style="color: #990000;"><?php echo $dados_locacao[descricao] ?></span></span>
          	<br/>
						<b>Selecione o Tipo de Relatório desejado:</b>
						<br/>
						<br/>				    
		        <span style="color: #990000;"><b>Relatório de Detalhamento da Locação</b></span>
					</td>
        </tr>
        <tr>
          <td width="20">			    
		        &nbsp;	    
					</td>          
					<td width="440">
						Opções de emissão do relatório:
						<br/>
				    <table width="100%" cellpadding="0" cellspacing="0">
		          <tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input name="chkValores" type="checkbox" id="chkValores" value="1" title="Marque esta caixa caso desejar incluir os valores financeiros dos itens da locação no relatório" style="border: 0px" checked>
									Incluir valores financeiros dos itens da locação.
								</td>
		          </tr>	
							<tr valign="middle" style="padding: 1px">
		            <td height="20">
		              <input name="chkDeclaracao" type="checkbox" id="chkDeclaracao" value="1" title="Marque esta caixa caso desejar incluir a declaração de compromisso no relatório" style="border: 0px" checked>
									Incluir declaração de compromisso de devolução do material locado.
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
      <br/>
				<input class="button" title="Retorna a exibição do detalhamento da locação" name="btnVoltar" type="button" id="btnRelatorio" value="Retornar a Locação" style="width:120px" onclick="wdCarregarFormulario('LocacaoExibe.php?LocacaoId=<?php echo $LocacaoId ?>&headers=1','conteudo')"/>
				<input class="button" title="Emite o relatório da locação" name="btnRelatorio" type="button" id="btnRelatorio" value="Emitir Relatório" style="width:100px" onclick="wdCarregarRelatorio()"/>
      	<br />
      	<br />	   	   		   		
 		</td>   
  </tr>  
</table>
</form>