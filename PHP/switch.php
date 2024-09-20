<?php
    $sexo = "m";


   /*  if($numero1>0){
        echo "Es positivo";
    }else if(){
        echo "Es negativo";
    }else{

    } */

    switch ($sexo) {
        case 'h':
            echo "Sos Hombre";
            echo ".";
            break;
        case 'H':
            echo "Sos Hombre";
            echo ".";
            break;
        
        default:
            echo "Sos Mujer";
            # code...
            break;
    }