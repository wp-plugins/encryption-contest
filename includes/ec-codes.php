<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
class EcCodes {

  public function toMorse ($text) {
      $conversion_table = array( 
                          'a' => '.-|',     'b' => '-...|',  'c' => '-.-.|',  'd' => '-..|',    'e' => '.|',      'f' => '..-.|',   'g' => '--.|',    'h' => '....|',
                          'i' => '..|',     'j' => '.---|',  'k' => '-.-|',   'l' => '.-..|',   'm' => '--|',     'n' => '-.|',     'o' => '---|',    'p' => '.--.|',
                          'q' => '--.-|',   'r' => '.-.|',   's' => '...|',   't' => '-|',      'u' => '..-|',    'v' => '...-|',   'w' => '.--|',    'x' => '-..-|',
                          'y' => '-.--|',   'z' => '--..|',  '0' => '-----|', '1' => '.----|',  '2' => '..---|',  '3' => '...--|',  '4' => '....-|',  '5' => '.....|',  
                          '6' => '-....|',  '7' => '--...|', '8' => '---..|', '9' => '----.|',  '.' => '||',      ' ' => '|');
      $text = strtr($text, $conversion_table);
      $text_count = mb_strlen($text);
      $position1 = $text_count - 1;
      $position2 = $text_count - 2;
      $position3 = $text_count - 3;
    
      if ($text[$position1] == '|' and $text[$position2] == '|' and $text[$position3] != '|'){
          $text .= '|';}
    
      if ($text[$position1] == '|' and $text[$position2] != '|' and $text[$position3] != '|'){
          $text .= '||';}

      if ($text[$position1] == '|' and $text[$position2] != '|' and $text[$position3] == '|'){ // If last character is |.| or |-|
          $text .= '||';}        
    
      $text = '<big><big>' .$text. '</big></big>';    
      return $text;                  
  }
  
  public function toReversedMorse ($text) {
      $classCodes = new EcCodes();
      $text = $classCodes->toMorse($text);
      $h_conversion = array ('-' => '!',);
      $text = strtr($text, $h_conversion);
      $conversion_table = array ('.' => '-',
                                 '!' => '.');
      $text = strtr($text, $conversion_table);
      return $text;                           
                                          
  }

  public function substitution ($text) {
      $conversion_table = array( 
                        'a' => '1 ',  'b' => '2 ',  'c' => '3 ',  'd' => '4 ',  'e' => '5 ',  'f' => '6 ',  'g' => '7 ',  'h' => '8 ',
                        'i' => '9 ',  'j' => '10 ', 'k' => '11 ', 'l' => '12 ', 'm' => '13 ', 'n' => '14 ', 'o' => '15 ', 'p' => '16 ',
                        'q' => '17 ', 'r' => '18 ', 's' => '19 ', 't' => '20 ', 'u' => '21 ', 'v' => '22 ', 'w' => '23 ', 'x' => '24 ',
                        'y' => '25 ', 'z' => '26 ', ' ' => '',  '.' => '');
      $text = strtr($text, $conversion_table);
      $text = '<big>' .$text. '</big>';
      return $text;
  }
  
  public function snailFromCenter ($text) {
      $conversion_table = array(' ' => '',
                                '.' => '',
                                );
      $text = strtr($text, $conversion_table);
      $text = mb_strtoupper($text);
      $number = $text[1];
      $number = ord(strtolower($number)) - 96;    // it gets second letter and convert it to number
      // Snail code is in JS. Author is David Dobrovolný.
      $html = '<script>
            var text = "'.$text.'";
            var cislo = '.$number.';

            var abc = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            var width = 0;
            var height = 0;

            var x = 0;
            var y = 0;

            var stredX;
            var stredY;

            var smer = true;
            var smerX = true;
            var smerY = true;
            var step = 1;
            var index = 1;

            while(text.length > (width * height))
            {	
	             if(width == height)
	             {
		              height++;
	             }
	             else
	             {
                  		width++;
	             }
            }

            var snek = new Array(width);
            for(var i = 0; i < width; i++)
            {
	             snek[i] = new Array(height);
            }
            x = Math.ceil(width/2) - 1;
            y = Math.ceil(height/2) - 1;

            stredX = (width+1)/2-1;
            stredY = (height+1)/2-1;

            if((cislo % 4) == 0 || (cislo % 4) == 1)
            {
	             x = Math.floor(stredX);
	             smerX = true;
            }
            else
            {
	             x = Math.ceil(stredX);
	             smerX = false;
            }

            if((cislo % 4) == 0 || (cislo % 4) == 2)
            {
	             y = Math.floor(stredY);
	             smerY = true;
            }
            else
            {
	          y = Math.ceil(stredY);
	          smerY = false;
            }

            if(text[index] != null) 
              snek[x][y] = text[index - 1];
            else 
              snek[x][y] = abc[index % 26];

            while(index < (height * width))
            {	
	            if(smer)
	            {

		            for(var j = 0; j < step; j++)
		            {

			             if(smerY) 
                      				y++;
			             else 
                				y--;

			             if(text[index] != null && (y != -1 && y < height)) 
                      snek[x][y] = text[index];
			             else 
                      if(y != -1 && y < height) 
                          snek[x][y] = abc[index % 26];
	
			             index++;
		            }

		            for(var j = 0; j < step; j++)
		            {
			             if(smerX) 
                      				x++;
			             else 
                     				x--;
                      
			             if(text[index] != null && (x != -1 && x < width)) 
                      snek[x][y] = text[index];
			             else 
                      if(x != -1 && x < width) 
                          snek[x][y] = abc[index % 26];

			             index++;		
		            }
	             }
	             else
	             {
		              for(var j = 0; j < step; j++)
		              {
			               if(smer) 
                        			x++;
			               else 
                        			x--;

			               if(text[index] != null && (x != -1 && x < width)) 
                        snek[x][y] = text[index];
			               else if(x != -1 && x < width) 
                        snek[x][y] = abc[index % 26];

			               index++;		
		              }

		              for(var j = 0; j < step; j++)
		              {
			               if(smer) 
                        			y++;
			               else 
                        			y--;

			               if(text[index] != null && (y != -1 && y < height)) 
                        snek[x][y] = text[index];
			               else 
                        if(y != -1 && y < height) 
                            snek[x][y] = abc[index % 26];
	
			               index++;
		              }
	               }
	               smerX = !smerX;
	               smerY = !smerY;
	               step++;
             }

             document.write("<table>");

             for(var i = 0; i < height; i++)
             {
	               document.write("<tr>");
	               for(var j = 0; j < width; j++)
	               {
		                document.write("<td>" + snek[j][i] + "</td>");
	               }
	               document.write("</tr>");
            }
            
            document.write("</table>");
            </script>';
   
    return $html;
  }
  
  public function polandCross ($text) {
      $conversion_table = array( 
                        'a' => '<img src="'.plugins_url( 'poland-cross/a.jpg', __FILE__ ).'" > ',
                        'b' => '<img src="'.plugins_url( 'poland-cross/b.jpg', __FILE__ ).'" > ',
                        'c' => '<img src="'.plugins_url( 'poland-cross/c.jpg', __FILE__ ).'" > ',
                        'd' => '<img src="'.plugins_url( 'poland-cross/d.jpg', __FILE__ ).'" > ',
                        'e' => '<img src="'.plugins_url( 'poland-cross/e.jpg', __FILE__ ).'" > ',
                        'f' => '<img src="'.plugins_url( 'poland-cross/f.jpg', __FILE__ ).'" > ',
                        'g' => '<img src="'.plugins_url( 'poland-cross/g.jpg', __FILE__ ).'" > ',
                        'h' => '<img src="'.plugins_url( 'poland-cross/h.jpg', __FILE__ ).'" > ',
                        'i' => '<img src="'.plugins_url( 'poland-cross/i.jpg', __FILE__ ).'" > ',
                        'j' => '<img src="'.plugins_url( 'poland-cross/j.jpg', __FILE__ ).'" > ',
                        'k' => '<img src="'.plugins_url( 'poland-cross/k.jpg', __FILE__ ).'" > ',
                        'l' => '<img src="'.plugins_url( 'poland-cross/l.jpg', __FILE__ ).'" > ',
                        'm' => '<img src="'.plugins_url( 'poland-cross/m.jpg', __FILE__ ).'" > ',
                        'n' => '<img src="'.plugins_url( 'poland-cross/n.jpg', __FILE__ ).'" > ',
                        'o' => '<img src="'.plugins_url( 'poland-cross/o.jpg', __FILE__ ).'" > ',
                        'p' => '<img src="'.plugins_url( 'poland-cross/p.jpg', __FILE__ ).'" > ',
                        'q' => '<img src="'.plugins_url( 'poland-cross/q.jpg', __FILE__ ).'" > ',
                        'r' => '<img src="'.plugins_url( 'poland-cross/r.jpg', __FILE__ ).'" > ',
                        's' => '<img src="'.plugins_url( 'poland-cross/s.jpg', __FILE__ ).'" > ',
                        't' => '<img src="'.plugins_url( 'poland-cross/t.jpg', __FILE__ ).'" > ',
                        'u' => '<img src="'.plugins_url( 'poland-cross/u.jpg', __FILE__ ).'" > ',
                        'v' => '<img src="'.plugins_url( 'poland-cross/v.jpg', __FILE__ ).'" > ',
                        'w' => '<img src="'.plugins_url( 'poland-cross/w.jpg', __FILE__ ).'" > ',
                        'x' => '<img src="'.plugins_url( 'poland-cross/x.jpg', __FILE__ ).'" > ',
                        'y' => '<img src="'.plugins_url( 'poland-cross/y.jpg', __FILE__ ).'" > ',
                        'z' => '<img src="'.plugins_url( 'poland-cross/z.jpg', __FILE__ ).'" > ',
                        ' ' => '<br><br>',
                        '.' => '',
                        );
      $text = strtr($text, $conversion_table);
      return $text;     
  }
  
  public function everySecond ($text) {
      $conversion_table = array( 
                          ' ' => '',
                          '.' => '',
                          );
      $text = strtr($text, $conversion_table);
      $text = mb_strtoupper($text);
      $text_count = mb_strlen($text);
    
      $classCodes = new EcCodes(); 
      $shift = $classCodes->shift($text, '5'); //Generate spam text based on result text
      $shift = mb_strtoupper($shift);
    
      for ($i=0; $i < $text_count; $i++){
          $html .= $text[$i];
          $html .= $shift[$i];    
      }
      $html = '<big>' .$html. '</big>';      
      return $html;   
  }
  
  public function shift ($string, $distance) {
      $distance = $distance % 26;
      $string = strtolower($string);
      $result = array();
      $characters = str_split($string);

      if ($distance < 0) {
          $distance += 26;
      }

      foreach ($characters as $idx => $char) {
          $result[$idx] = chr(97 + (ord($char) - 97 + $distance) % 26);
      }   
      return implode("", $result);
  }  

  public function moveLetter ($text) {
      $text = str_replace('.', '', $text);  
      $classCodes = new EcCodes();
      $text = $classCodes->shift($text, '1');
      $text = str_replace('U', ' ', $text);
    
      $text = '<big>' .$text. '</big>';    
      return $text;  
  } 
  
  public function textBackwards ($text) {
      $text = str_replace('.', '', $text);
      $text = str_replace(' ', '', $text);    
      $text = strrev($text); //Reverse string
      $text = chunk_split($text,"5"," ");
    
      $text = '<big>' .$text. '</big>';
      return $text;
  } 
  
  public function ownTextCode($text) {
      return '<big>'.$text.'</big>';
  } 
  
  public function ownPicture ($text) {
      $start_position = mb_strpos($text, "http");
      $end_possition1 = mb_strpos($text, ".jpg");
      $end_possition2 = mb_strpos($text, ".gif");
      $end_possition3 = mb_strpos($text, ".png");
      $end_possition4 = mb_strpos($text, ".bmp");
    
      if ($end_possition1 != '')
          $end_possition = $end_possition1;
      if ($end_possition2 != '')
          $end_possition = $end_possition2;
      if ($end_possition3 != '')
          $end_possition = $end_possition3;
      if ($end_possition4 != '')
          $end_possition = $end_possition4;            
    
      $end_position = $end_possition + 4; //Because .jpg is plus 4 positions
      $lenght_calc = $end_position - $start_position;
      $link = mb_substr($text, $start_position, $lenght_calc); 
      $content = '<img src="'.$link.'">';

      return $content;  
  } 
}