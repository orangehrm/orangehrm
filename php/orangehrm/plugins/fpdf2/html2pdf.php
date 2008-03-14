<?php
//HTML2PDF by Clément Lavoillotte
//ac.lavoillotte@noos.fr
//webmaster@streetpc.tk
//http://www.streetpc.tk

if (!defined('ROOT_PATH'))
	define('ROOT_PATH', $_SESSION['path']);

define('FPDF_FONTPATH', '/var/www/html/test/fpdf/font/');
require ROOT_PATH . '/plugins/fpdf2/fpdf.php';
require ROOT_PATH . '/plugins/fpdf2/htmlparser.inc';

//function hex2dec
//returns an associative array (keys: R,G,B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
	$R = substr($couleur, 1, 2);
	$rouge = hexdec($R);
	$V = substr($couleur, 3, 2);
	$vert = hexdec($V);
	$B = substr($couleur, 5, 2);
	$bleu = hexdec($B);
	$tbl_couleur = array();
	$tbl_couleur['R']=$rouge;
	$tbl_couleur['G']=$vert;
	$tbl_couleur['B']=$bleu;
	return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}
////////////////////////////////////

class PDF extends FPDF {
var $B;
var $I;
var $U;
var $HREF;
var $THEAD;
//variables of html parser

//////////////////////////////////////
//html parser
/*
function WriteHTML($html)
{
	$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><h1><h2><h3><h4><h5><h6>"); //remove all unsupported tags
	$html=str_replace("\n",' ',$html); //replace carriage returns by spaces
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //explodes the string
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,stripslashes(txtentities($e)));
		}
		else
		{
			//Tag
			if($e{0}=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				$this->OpenTag($tag,$attr);
			}
		}
	}
}
*/

function Footer()
{
	$this->SetTextColor(190, 190, 190);
	$this->Text(150, 274, 'Powered by');
	$this->Image('../../themes/beyondT/pictures/orange_new_02.png', 150, 275, 34, 8);
}

function OpenTag($tag,$attr)
{
	//Opening tag
	switch($tag){
		case 'STRONG':
			$this->SetStyle('B',true);
			break;
		case 'EM':
			$this->SetStyle('I',true);
			break;
		case 'B':
		case 'I':
		case 'U':
			$this->SetStyle($tag,true);
			break;
		case 'A':
			$this->HREF=$attr['HREF'];
			break;
		case 'IMG':
			if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
				if(!isset($attr['WIDTH']))
					$attr['WIDTH'] = 0;
				if(!isset($attr['HEIGHT']))
					$attr['HEIGHT'] = 0;
				$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
			}
			break;
		case 'TR':
		case 'BLOCKQUOTE':
		case 'BR':
			$this->Ln(5);
			break;
		case 'P':
			$this->Ln(10);
			break;
		case 'FONT':
			if (isset($attr['COLOR']) and $attr['COLOR']!='') {
				$coul=hex2dec($attr['COLOR']);
				$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
				$this->issetcolor=true;
			}
			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
				$this->SetFont(strtolower($attr['FACE']));
				$this->issetfont=true;
			}
			break;
		case 'HR':
	        	if(isset($attr['WIDTH']) && $attr['WIDTH'] != '')
        	        	$Width = $attr['WIDTH'];
			else
				$Width = $this->w - $this->lMargin-$this->rMargin;

			$x = $this->GetX();
			$y = $this->GetY();
			$this->SetDrawColor(200, 200, 200);
			$this->SetLineWidth(0.5);
			$this->Line($x + 5, $y + 5, $x + $Width, $y + 5);
			$this->SetDrawColor(0, 0, 0);
			$this->Ln(8);
			break;
		case 'H2':
			$this->SetFont('Arial', 'B', 18);
			break;

	}
}

function CloseTag($tag)
{
	//Closing tag
	if($tag=='STRONG')
		$tag='B';
	if($tag=='EM')
		$tag='I';
	if($tag=='B' or $tag=='I' or $tag=='U')
		$this->SetStyle($tag,false);
	if($tag=='A')
		$this->HREF='';
	if($tag=='FONT'){
		if ($this->issetcolor==true) {
			$this->SetTextColor(0);
		}
		if ($this->issetfont) {
			$this->SetFont('arial');
			$this->issetfont=false;
		}
	}
	if($tag=='H2'){
		$this->SetFont('Arial', '', 10);
		$this->Ln(5);
	}
}

function SetStyle($tag,$enable)
{
	//Modify style and select corresponding font
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('B','I','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
	//Put a hyperlink
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}

function PDF($orientation='P',$unit='mm',$format='A4')
{
    //Call parent constructor
    $this->FPDF($orientation,$unit,$format);
    //Initialization
    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
}

function WriteHTML2($html)
{
    //HTML parser
    $html=str_replace("\n", ' ', $html);
    $a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
    foreach($a as $i=>$e)
    {
        if($i%2==0)
        {
            //Text
            if($this->HREF)
                $this->PutLink($this->HREF,$e);
            else
                $this->Write(5,$e);
        }
        else
        {
            //Tag
            if($e{0}=='/')
                $this->CloseTag(strtoupper(substr($e,1)));
            else
            {
                //Extract attributes
                $a2=explode(' ',$e);
                $tag=strtoupper(array_shift($a2));
                $attr=array();
                foreach($a2 as $v)
                    if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
                        $attr[strtoupper($a3[1])]=$a3[2];
                $this->OpenTag($tag,$attr);
            }
        }
    }
}

function WriteTable($data,$w, $bypass = false)
{
    $this->SetLineWidth(.3);
    $this->SetFillColor(255,255,255);
    $this->SetTextColor(0);

    if(! $bypass)
	$this->SetFont(''); 

    $even = false;
    $cnt = 1;

    foreach($data as $row)
    {
        $nb=0;
        for($i=0;$i<count($row);$i++)
            $nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));

        $h=5*$nb;

	$h = $_SESSION['cellHeight'];
	$w = $_SESSION['colunmWidths']; 
	$limit = $_SESSION['recordsPerPage'];

	$this->CheckPageBreak($h);

	if($cnt % $limit == 0){
		$this->AddPage();
		$this->Ln(10);
		$this->SetFont('Arial', 'B', 10);
		$this->WriteHTML($this->THEAD, true);
		$this->SetFont('Arial', '', 10);
		$even = true;
	}
	$cnt++;

	if ($even)
    	    $this->SetFillColor(230,230, 230);
	else
    	    $this->SetFillColor(255, 255, 255);

	$even = !$even;

        for($i=0;$i<count($row);$i++)
        {
            $x=$this->GetX();
            $y=$this->GetY();
	
            $this->Rect($x,$y,$w[$i],$h, 'F');

	    if($i != count($row) - 1) {
		$this->SetFillColor(255, 255, 255);
		$this->Rect(($x + $w[$i]) - 1, $y, 0.5, $h, 'F');

		if (!$even)
    	    	    $this->SetFillColor(230,230, 230);
		else
		    $this->SetFillColor(255, 255, 255);
	    }

	    if(! $bypass)
	        $this->MultiCell($w[$i],6,trim($row[$i]),2,'L');
	    else
	        $this->MultiCell($w[$i],6,trim($row[$i]),2,'C');
            //Put the position to the right of the cell
            $this->SetXY($x+$w[$i],$y);                    
        }
	

        $this->Ln($h);

    }
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function ReplaceHTML($html)
{
    $html = str_replace( '<li>', "\n<br> - " , $html );
    $html = str_replace( '<LI>', "\n - " , $html );
    $html = str_replace( '</ul>', "\n\n" , $html );
    $html = str_replace( '<strong>', "<b>" , $html );
    $html = str_replace( '</strong>', "</b>" , $html );
    $html = str_replace( '&#160;', "\n" , $html );
    $html = str_replace( '&nbsp;', " " , $html );
    $html = str_replace( '&quot;', "\"" , $html );
    $html = str_replace( '&#39;', "'" , $html );
    return $html;
}

function ParseTable($Table)
{
    $_var='';
    $htmlText = $Table;
    $parser = new HtmlParser ($htmlText);
    while ($parser->parse()) {
        if(strtolower($parser->iNodeName)=='table')
        {
            if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
                $_var .='/::';
            else
                $_var .='::';
        }

        if(strtolower($parser->iNodeName)=='tr')
        {
            if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
                $_var .='!-:'; //opening row
            else
                $_var .=':-!'; //closing row
        }
        if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
        {
            $_var .='#,#';
        }
        if(strtolower($parser->iNodeName)=='th' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
        {
            //$_var .='#,#';  
		$this->Ln(0.5);
        }
        if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))
        {
            $_var .= $parser->iNodeValue;
        }
    }
    $elems = split(':-!',str_replace('/','',str_replace('::','',str_replace('!-:','',$_var)))); //opening row
    foreach($elems as $key=>$value)
    {
        if(trim($value)!='')
        {
            $elems2 = split('#,#',$value);
            array_pop($elems2);
            $data[] = $elems2;
        }
    }
    return $data;
}

function WriteHTML($html, $bypass = false)
{
    $this->THEAD = substr($html, strpos($html, '<thead>') + 7, strpos($html, '</thead>') - (strpos($html, '<thead>') + 7));
    $this->THEAD = str_replace('<th>', '<td>', $this->THEAD);
    $this->THEAD = str_replace('</th>', '</td>', $this->THEAD);
    $this->THEAD = '<table>' . trim($this->THEAD) . '</table>';	

    if(!$bypass){
	$this->Ln(20);
	$this->SetFont('Arial', 'B', 10);
	$this->WriteHTML($this->THEAD, true);
	$this->SetFont('Arial', '', 10);
	$this->Ln(-38.3);
    }

    $html = $this->ReplaceHTML($html);
    //Search for a table
    $start = strpos(strtolower($html),'<table');
    $end = strpos(strtolower($html),'</table');
    if($start!==false && $end!==false)
    {
        $this->WriteHTML2(substr($html,0,$start).'<BR>');

        $tableVar = substr($html,$start,$end-$start);
        $tableData = $this->ParseTable($tableVar);
        for($i=1;$i<=count($tableData[0]);$i++)
        {
            if($this->CurOrientation=='L')
                $w[] = abs(120/(count($tableData[0])-1))+24;
            else
                $w[] = abs(120/(count($tableData[0])-1))+5;
        }

	if(! isset($w)){
		$w[] = 0;
		$w[] = 30;
		$w[] = 55;
		$w[] = 18;
		$w[] = 18;
		$w[] = 18;
		$w[] = 18;
		$w[] = 18;
		$w[] = 18;
		$w[] = 0;
		$w[] = 0;
	}

        $this->WriteTable($tableData,$w, $bypass);
        $this->WriteHTML2(substr($html,$end+8,strlen($html)-1).'<BR>');

	if(! $bypass)
		$this->Footer();
    }
    else
    {
        $this->WriteHTML2($html);
    }
}



}//end of class
?>
