<?php
function gps2Num($coordPart1, $coordPart2) {
    $coordPart1 = explode('/', $coordPart1);
    $coordPart2 = explode('/', $coordPart2);
  
    $coord = $coordPart1[0] / $coordPart1[1];
    $coord += $coordPart2[0] / $coordPart2[1] / 60;
  
    return $coord;
  }
  
  
  
  
  
  
  





?>