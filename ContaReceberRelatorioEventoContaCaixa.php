<?php 
###########
## M�dulo para exibi��o da filtragem do relat�rio das contas - por evento e conta-caixa
## Criado: 18/08/2010 - Maycon Edinger
## Alterado: 
## Altera��es:
##  
###########

if ($_GET['headers'] == 1) 
{
	//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
	header('Content-Type: text/html;  charset=ISO-8859-1',true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require('Diretivas.php');

//Estabelece a conex�o com o banco de dados
include './conexao/ConexaoMySQL.php';

//Adiciona o acesso a entidade de cria��o do componente data
include('CalendarioPopUp.php');  
//Cria um objeto do componente data
$objData = new tipData();
//Define que n�o deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Efetua o lookup na tabela de Origens
//Monta o SQL de pesquisa
$lista_origem = "SELECT id, nome FROM eventos WHERE empresa_id = $empresaId AND ativo = 1 ORDER BY nome";

//Executa a query
$dados_origem = mysql_query($lista_origem);

//Efetua o lookup na tabela de contas-caixa
//Monta o SQL de pesquisa
$lista_contacaixa = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = 1 AND tipo = 1 ORDER BY nome";
	
//Executa a query
$dados_contacaixa = mysql_query($lista_contacaixa);

?>

<input name="TipoObjeto" type="hidden" value="combobox" />

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Evento e Conta-Caixa</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 
			<table class="tabDetailView" cellspacing="0" cellpadding="0" width="100%" border="0">
        <tr>
          <td class="listViewPaginationTdS1" style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px" colspan="21">
            <table cellspacing="0" cellpadding="0" width="100%" border="0">
              <tr>
                <td class="tabDetailViewDL" style="TEXT-ALIGN: left"><img src="image/bt_cadastro.gif" width="16" height="15" /> Selecione o Evento e o Formando desejado para filtragem das contas:</td>
              </tr>
            </table>
          </td>
        </tr>
  			<tr>
  				<td width="100" class="dataLabel">
  				Evento
  				</td>
  				<td colspan="3" class="tabDetailViewDF">
				    <select name="cmbEventoId" id="cmbEventoId" style="width: 450px">    
              <?php 
								while ($lookup_origem = mysql_fetch_object($dados_origem)) { 
							?>
			  			<option value="<?php echo $lookup_origem->id ?>"><?php echo $lookup_origem->id . " - " . $lookup_origem->nome ?></option>
				    	<?php 
								} 
							?>
				    </select>		  				
  				</td>
  			</tr>                    
        <tr>
          <td class="dataLabel">Conta-Caixa:</td>
          <td colspan="3" width="450" class="tabDetailViewDF">
						<select name="cmbContaCaixaId" id="cmbContaCaixaId" style="width: 450px">    
              <?php 
								while ($lookup_contacaixa = mysql_fetch_object($dados_contacaixa)) { 
							?>
			  			<option value="<?php echo $lookup_contacaixa->id ?>"><?php echo $lookup_contacaixa->id . " - " . $lookup_contacaixa->nome ?></option>
				    	<?php 
								} 
							?>
				    </select>	
          </td>
        </tr>
				<tr>
          <td class="dataLabel">
            In&iacute;cio:
          </td>
          <td width="107" class="tabDetailViewDF">
						<?php
						    //Define a data do formul�rio
						    $objData->strFormulario = "cadastro";  
						    //Nome do campo que deve ser criado
						    $objData->strNome = "edtDataIni";
						    //Valor a constar dentro do campo (p/ altera��o)
						    $objData->strValor = "";
						    //Define o tamanho do campo 
						    //$objData->intTamanho = 15;
						    //Define o n�mero maximo de caracteres
						    //$objData->intMaximoCaracter = 20;
						    //define o tamanho da tela do calendario
						    //$objData->intTamanhoCalendario = 200;
						    //Cria o componente com seu calendario para escolha da data
						    $objData->CriarData();
						?>
          </td>
          <td width="61" class="dataLabel">T&eacute;rmino:</td>
          <td width="100" class="tabDetailViewDF">
						<?php
						    //Define a data do formul�rio
						    $objData->strFormulario = "cadastro";  
						    //Nome do campo que deve ser criado
						    $objData->strNome = "edtDataFim";
						    //Valor a constar dentro do campo (p/ altera��o)
						    $objData->strValor = "";
						    //Define o tamanho do campo 
						    //$objData->intTamanho = 15;
						    //Define o n�mero maximo de caracteres
						    //$objData->intMaximoCaracter = 20;
						    //define o tamanho da tela do calendario
						    //$objData->intTamanhoCalendario = 200;
						    //Cria o componente com seu calendario para escolha da data
						    $objData->CriarData();
						?>
          </td>                
	  		</tr>
				<tr>
  				<td class="dataLabel">
  				Situa��o
  				</td>
  				<td colspan="3" class="tabDetailViewDF">
						<table width="450" border="0" cellspacing="0" cellpadding="0">
			    	  <tr>
			    	  	<td width="110">
			    	  		<input type="radio" name="edtSituacao" value="0" checked="checked" /> Todas
								</td>
								<td width="110">
			    	  		<input type="radio" name="edtSituacao" value="1" /> A Vencer
								</td>
			    	  	<td width="110">
			    	  		<input type="radio" name="edtSituacao" value="2" /> Recebidas
								</td>
			    	  	<td>
			    	  		<input type="radio" name="edtSituacao" value="3" /> Vencidas
								</td>						
			    	  </tr>
			    	</table>		  				
  				</td>
  			</tr>               		
      </table>
		</td>
  </tr>
</table>