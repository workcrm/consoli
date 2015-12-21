//Menu para habilitar com o clique com o botão direito do mouse...
/*
<!--
var ie  = document.all
var ns6 = document.getElementById&&!document.all
var isMenu  = false ;
var menuSelObj = null ;
var overpopupmenu = false;
function mouseSelect(e)
{
  var obj = ns6 ? e.target.parentNode : event.srcElement.parentElement;
  if( isMenu )
  {
    if( overpopupmenu == false )
    {
      isMenu = false ;
      overpopupmenu = false;
      document.getElementById('menudiv').style.display = "none" ;
      return true ;
    }
    return true ;
  }
  return false;
}
// POP UP MENU
function  ItemSelMenu(e)
{
  var obj = ns6 ? e.target.parentNode : event.srcElement.parentElement; 
      menuSelObj = obj ;
  if (ns6)
  {
    document.getElementById('menudiv').style.left = e.clientX+document.body.scrollLeft;
    document.getElementById('menudiv').style.top = e.clientY+document.body.scrollTop;
  } else
  {
    document.getElementById('menudiv').style.pixelLeft = event.clientX+document.body.scrollLeft;
    document.getElementById('menudiv').style.pixelTop = event.clientY+document.body.scrollTop;
  }
  document.getElementById('menudiv').style.display = "";
  document.getElementById('item1').style.backgroundColor='#FFFFFF';
  document.getElementById('item2').style.backgroundColor='#FFFFFF';
  document.getElementById('item3').style.backgroundColor='#FFFFFF';
  document.getElementById('item4').style.backgroundColor='#FFFFFF';
  isMenu = true;
  return false ;
}
document.onmousedown  = mouseSelect;
document.oncontextmenu  = ItemSelMenu;
//*** Fim da função para menu de botão direito
*/


//FunÁies de controle do slider - Motionpack
var timerlen = 5;
var slideAniLen = 250;

var timerID = new Array();
var startTime = new Array();
var obj = new Array();
var endHeight = new Array();
var moving = new Array();
var dir = new Array();

function slidedown(objname)
{
        if(moving[objname])
                return;

        if(document.getElementById(objname).style.display != "none")
                return; // cannot slide down something that is already visible

        moving[objname] = true;
        dir[objname] = "down";
        startslide(objname);
}

function slideup(objname)
{
        if(moving[objname])
                return;

        if(document.getElementById(objname).style.display == "none")
                return; // cannot slide up something that is already hidden

        moving[objname] = true;
        dir[objname] = "up";
        startslide(objname);
}

function startslide(objname)
{
        obj[objname] = document.getElementById(objname);

        endHeight[objname] = parseInt(obj[objname].style.height);
        startTime[objname] = (new Date()).getTime();

        if(dir[objname] == "down"){
                obj[objname].style.height = "1px";
        }

        obj[objname].style.display = "block";

        timerID[objname] = setInterval('slidetick(\'' + objname + '\');',timerlen);
}

function slidetick(objname)
{
        var elapsed = (new Date()).getTime() - startTime[objname];

        if (elapsed > slideAniLen)
                endSlide(objname)
        else {
                var d =Math.round(elapsed / slideAniLen * endHeight[objname]);
                if(dir[objname] == "up")
                        d = endHeight[objname] - d;

                obj[objname].style.height = d + "px";
        }

        return;
}

function endSlide(objname)
{
        clearInterval(timerID[objname]);

        if(dir[objname] == "up")
                obj[objname].style.display = "none";

        obj[objname].style.height = endHeight[objname] + "px";

        delete(moving[objname]);
        delete(timerID[objname]);
        delete(startTime[objname]);
        delete(endHeight[objname]);
        delete(obj[objname]);
        delete(dir[objname]);

        return;
}

function toggleSlide(objname)
{
  if(document.getElementById(objname).style.display == "none")
  {
    // div is hidden, so let's slide down
    slidedown(objname);
  }
  else
  {
    // div is not hidden, so slide up
    slideup(objname);
  }
}

/*** 
* Descrição.: formata um campo do formulário de acordo com a máscara informada... 
* Parâmetros: - objForm (o Objeto Form) 
* - strField (string contendo o nome do textbox) 
* - sMask (mascara que define o formato que o dado será apresentado, usando o algarismo "9" para 
* definir números e o símbolo "!" para  qualquer caracter... 
* - evtKeyPress (evento) 
* Uso.......: <input type="textbox" name="xxx"  
* onkeypress="return txtBoxFormat(document.rcfDownload, 'str_cep', '99999-999', event);"> 
***/
function FormataCampo(objForm, strField, sMask, evtKeyPress) {
      var i, nCount, sValue, fldLen, mskLen,bolMask, sCod, nTecla;

      if(document.all) { // Internet Explorer
        nTecla = evtKeyPress.keyCode; }
      else if(document.layers) { // Nestcape
        nTecla = evtKeyPress.which;
      }

      sValue = objForm[strField].value;

      // Limpa todos os caracteres de formatação que
      // já estiverem no campo.
      sValue = sValue.toString().replace( "-", "" );
      sValue = sValue.toString().replace( "-", "" );
      sValue = sValue.toString().replace( ".", "" );
      sValue = sValue.toString().replace( ".", "" );
      sValue = sValue.toString().replace( "/", "" );
      sValue = sValue.toString().replace( "/", "" );
      sValue = sValue.toString().replace( "(", "" );
      sValue = sValue.toString().replace( "(", "" );
      sValue = sValue.toString().replace( ")", "" );
      sValue = sValue.toString().replace( ")", "" );
      sValue = sValue.toString().replace( ":", "" );
      sValue = sValue.toString().replace( " ", "" );
      fldLen = sValue.length;
      mskLen = sMask.length;

      i = 0;
      nCount = 0;
      sCod = "";
      mskLen = fldLen;

      while (i <= mskLen) {
        bolMask = ((sMask.charAt(i) == "-") || (sMask.charAt(i) == ".") || (sMask.charAt(i) == "/"))
        bolMask = bolMask || ((sMask.charAt(i) == "(") || (sMask.charAt(i) == ")") || (sMask.charAt(i) == " ") || (sMask.charAt(i) == ":"))

        if (bolMask) {
          sCod += sMask.charAt(i);
          mskLen++; }
        else {
          sCod += sValue.charAt(nCount);
          nCount++;
        }

        i++;
      }

      objForm[strField].value = sCod;

      if (nTecla != 8) { // backspace
        if (sMask.charAt(i-1) == "9") { // apenas números...
          return ((nTecla > 47) && (nTecla < 58)); } // números de 0 a 9
        else { // qualquer caracter...
          return true;
        } }
      else {
        return true;
      }
    }
//Fim da Função Máscaras Gerais



//Função para capturar a posição do elemento do menu
function getPos(el, sProp) {
	var iPos = 0
	while (el!=null) {
		iPos+=el["offset" + sProp]
		el = el.offsetParent
	}
	return iPos
}

//Função para menu
function Menu(el,VMenu) {
   //Se existir um menu
   if (VMenu) {
	 VMenu.style.display= '';		
	 VMenu.style.pixelLeft = getPos(el,"Left") 
	 VMenu.style.pixelTop = getPos(el,"Top") + 17
	}
	
	if ((VMenu!=cm) && (cm)) {
		 cm.style.display='none';
		 SubMenuRelatoriosFinanceiro.style.display = 'none'
     SubMenuRelatoriosEventos.style.display = 'none'
		 SubMenuRelatoriosRh.style.display = 'none'
		 SubMenuRelatoriosCadastros.style.display = 'none'		 
	}
   cm=VMenu;	
}

//Função para Sub menu
function SubMenu(el,VSubMenu) {
   //Se existir um menu
   if (VSubMenu) {
	 VSubMenu.style.display= '';		
	 VSubMenu.style.pixelLeft = getPos(el,"Left") + 80
	 VSubMenu.style.pixelTop = getPos(el,"Top")	
	}
	
	if ((VSubMenu!=cs) && (cs)) {
		 cs.style.display='none';
	}
   cs=VSubMenu;	
}

/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;

//Função que alterna a visibilidade do painel especificado.
function change(id){
  ID = document.getElementById(id);

  if(ID.style.display == "")
    ID.style.display = "none";
  else
  ID.style.display = "";
}

/**
 * Abre uma nova janela para relatórios do sistema
 */
function abreJanela(Url,NomeJanela,width,height,extras) 
{ 
	var largura = 800; 
	var altura = 600; 
	var adicionais= extras; 
	var topo = (screen.height-altura)/2; 
	var esquerda = (screen.width-largura)/2; 
	novaJanela=window.open(''+ Url + '','daaa','width=800,height=600,top=' + topo + ',left=' + esquerda + ',features=toolbar=no, location=no, directories=no, status=no, menubar=no, vscroll=no, resizable=no, copyhistory=no'); 

	novaJanela.focus(); 
} 

/**
 * Abre uma nova janela para relatórios do sistema
 */
function abreJanela2(Url,NomeJanela,width,height,extras) 
{ 
	var largura = 980; 
	var altura = 600; 
	var adicionais= extras; 
	var topo = (screen.height-altura)/2; 
	var esquerda = (screen.width-largura)/2; 
	novaJanela=window.open(''+ Url + '','daaa','width=980,height=600,top=' + topo + ',left=' + esquerda + ',features=toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=1, resizable=yes, copyhistory=no'); 

	novaJanela.focus(); 
} 

/**
 * Abre uma nova janela para visualização e impressão do boleto
 */
function abreJanelaBoleto(Url,NomeJanela,width,height,extras) { 
var largura = 740; 
var altura = 600; 
var adicionais= extras; 
var topo = (screen.height-altura)/2; 
var esquerda = (screen.width-largura)/2; 
novaJanela=window.open(''+ Url + '','daaa','width=740,height=600,top=' + topo + ',left=' + esquerda + ', toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, fullscreen=no'); 

novaJanela.focus(); 
} 

//Função para alternar a cor da linha da tabela conforme o mouse move
function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor) {
    var theCells = null;

    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;

    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    }

    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
        }
    }
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
        }
    }

    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
}

function wdSubmitPesquisa() {
     var Form;

     Form = document.pesquisa;
     if (Form.ChavePesquisa.value.length == 0) {
        alert("É necessário informar o argumento de pesquisa !");
        Form.ChavePesquisa.focus();
        return false;
     }
     
     var urlCadastro;
     urlCadastro = "ModuloPesquisaResultado.php?ChavePesquisa=" + Form.ChavePesquisa.value + "&cmbModulo=0";
     
     wdCarregarFormulario(urlCadastro,'conteudo');
     return true;
}

function wdSubmitPesquisaEnter() {
     var Form;

     Form = document.pesquisa;
     if (Form.ChavePesquisa.value.length == 0) {
        alert("É necessário informar o argumento de pesquisa !");
        Form.ChavePesquisa.focus();
        return false;
     }
     
     var urlCadastro;
     urlCadastro = "ModuloPesquisaResultado.php?ChavePesquisa=" + Form.ChavePesquisa.value + "&cmbModulo=0";
     
     wdCarregarFormulario(urlCadastro,'conteudo');
     return false;
}

