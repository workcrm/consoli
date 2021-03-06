<?php 
###########
## M�dulo para exibi��o da filtragem do relat�rio das contas a receber - agrupada (centro de custo e conta-caixa)
## Criado: 14/08/2007- Maycon Edinger
## Alterado: 
## Altera��es:
## 
###########


if ($_GET['headers'] == 1) {
	//Seta o header do retorno para efetuar a acentua��o correta usando o AJAX
	header("Content-Type: text/html;  charset=ISO-8859-1",true);
}

//Com a utiliza��o do AJAX, deve-se efetuar nova conex�o e novo processamento de diretivas
//Processa as diretivas de seguran�a 
require("Diretivas.php");

//Estabelece a conex�o com o banco de dados
include "./conexao/ConexaoMySQL.php";

//Adiciona o acesso a entidade de cria��o do componente data
include("CalendarioPopUp.php");  
//Cria um objeto do componente data
$objData = new tipData();
//Define que n�o deve exibir a hora no calendario
$objData->bolExibirHora = false;
//Monta javaScript do calendario uma unica vez para todos os campos do tipo data
$objData->MontarJavaScript(); 

//Efetua o lookup na tabela de grupos
//Monta o SQL de pesquisa
$lista_grupo = "SELECT * FROM grupo_conta WHERE empresa_id = $empresaId AND ativo = '1' ORDER BY nome";

//Executa a query
$dados_grupo = mysql_query($lista_grupo);


//Efetua o lookup na tabela de subgrupos
//Monta o SQL de pesquisa
$lista_subgrupo = "SELECT * FROM subgrupo_conta WHERE empresa_id = $empresaId AND ativo = '1' AND tipo = '1' ORDER BY nome";

//Executa a query
$dados_subgrupo = mysql_query($lista_subgrupo);

?>

<input name="TipoObjeto" type="hidden" value="combobox">

<br />
<span class="TituloModulo"> Filtragem: <font color="#990000">Por Conta-caixa e Centro de Custo</font><br />
</span>

<table width="626" border="0" cellspacing="0" cellpadding="0">
  <tr>
		<td valign="middle"> 

				<TABLE class='tabDetailView' cellSpacing='0' cellPadding='0' width='100%' border='0'>
              <TR>
                <TD class='listViewPaginationTdS1' style='PADDING-RIGHT: 0px; PADDING-LEFT: 0px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; BORDER-BOTTOM: 0px' colSpan='21'>
                  <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
                      <TR>
                        <TD class='tabDetailViewDL' style='TEXT-ALIGN: left'><img src="image/bt_cadastro.gif" width="16" height="15"> Selecione o agrupamento desejado para filtragem das contas:</TD>
                      </TR>
                  </TABLE>
                </TD>
              </TR>
			  			<tr>
			  				<td class='dataLabel'>
			  				Conta-caixa:
			  				</td>
			  				<td colspan="3" class=tabDetailViewDF>
									<select name="cmbSubgrupoId" id="cmbSubgrupoId" style="width: 360px">    
										<?php 
											while ($lookup_subgrupo = mysql_fetch_object($dados_subgrupo)) { 
										?>
						  			<option value="<?php echo $lookup_subgrupo->id ?>"><?php echo $lookup_subgrupo->nome ?>				 </option>
							    	<?php 
											} 
										?>
							    </select>			  				
			  				</td>
			  			</tr>
							<tr>
			  				<td class='dataLabel'>
			  				Centro de Custo:
			  				</td>
			  				<td colspan="3" class=tabDetailViewDF>
									<select name="cmbGrupoId" id="cmbGrupoId" style="width: 360px">    
										<?php 
											while ($lookup_grupo = mysql_fetch_object($dados_grupo)) { 
										?>
						  			<option value="<?php echo $lookup_grupo->id ?>"><?php echo $lookup_grupo->nome ?>				 </option>
							    	<?php 
											} 
										?>
							    </select>			  				
			  				</td>
			  			</tr>			  			
							<TR>
                <TD class='dataLabel' width='105'>
                  <SLOT>In&iacute;cio:</SLOT>
                </TD>
                <TD width="107" class=tabDetailViewDF>
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
                </TD>
                <TD width="61" class=dataLabel>T&eacute;rmino:</TD>
                <TD width="100" class=tabDetailViewDF>
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
		  				<td class='dataLabel'>
		  				Situa��o
		  				</td>
		  				<td colspan="3" class=tabDetailViewDF>
								<table width="530" border="0" cellspacing="0" cellpadding="0">
					    	  <tr>
					    	  	<td width="110">
					    	  		<input type="radio" name="edtSituacao" value="0" checked/> Todas
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
          </TABLE>

		</td>
  </tr>
</table>