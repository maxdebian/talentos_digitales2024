<?php
    // Array Escalar
    //$persona = array("Maximiliano",22.50,TRUE);
    $persona = array("Maximiliano","MARCELO","ana","ELIana");
    //var_dump($persona);
    //echo $persona[2];
    echo "<pre>";
    print_r($persona);
    echo "</pre>";

    $persona2 = ["Maximiliano","Juan","Pedro"];
    echo "<pre>";
    print_r($persona2);
    echo "</pre>";

    // Array Asociativo
    $libros = [
        'nombre'        =>  'Programacion',
        'editorial'     =>  'Editorial 1',
        'anio'          =>  '2024'
    ];

    echo "<pre>";
    print_r($libros) ;
    echo "</pre>";

    echo $libros['editorial'];

    $persona3 = [
        'nombre'    =>  'Max',
        'apellido'  =>  'Tedesco',
        'lenguajes' =>  ['PHP','Laravel','Javascript','VueJS']
    ];
    echo "<pre>";
    print_r($persona3) ;
    echo "</pre>";
    echo $persona3['lenguajes'][1];

    echo "<br>".count($persona3['lenguajes']).'<br>';

    echo strtolower($persona[0]);
    echo '<br>'.ucfirst($persona[1]);
    $str = "Hello fri3nd, you're
    looking          good today!";

print_r(str_word_count($str, 1));
print_r(str_word_count($str, 2));
print_r(str_word_count($str, 1, 'àáãç3'));

echo str_word_count($str);


