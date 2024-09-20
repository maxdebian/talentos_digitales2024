<?php
    $persona = array("Maximiliano","MARCELO","ana","ELIana");
    $persona2 = ["Maximiliano","Juan","Pedro"];
    $libros = [
            'nombre'        =>  'Programacion',
            'editorial'     =>  'Editorial 1',
            'anio'          =>  '2024'
        ];
    $persona3 = [
        'nombre'    =>  'Max',
        'apellido'  =>  'Tedesco',
        'lenguajes' =>  ['PHP','Laravel','Javascript','VueJS']
    ];

    // FOR
    // FOREACH
    // WHILE
    // REPEAT

   /*  for($index=0; $index<4; $index++){
        echo $persona[$index].'<br>';
    } */
    /* for($index=0; $index<count($persona); $index++){
        echo 'Index: '.$index.' La persona es: '.$persona[$index].'<br>';
    } */

    foreach ($persona as /* $index => */ $nombreDePersona) {
        //echo "<br> index: $index   nombre de persona: $nombreDePersona";        
        echo "<br> nombre de persona: $nombreDePersona";        
    }
    foreach ($libros as $index =>  $libro) {
        if($libro==2024)
            echo "<br> index: $index   valor: $libro";
    
        //echo "<br> nombre de libro: $libro";        
    }
    $numero=1;
    $tabla=2;
    echo "<br>";
    /* while ($numero<=10) {
        echo " $tabla X $numero = ".($tabla*$numero)."<br>";
        $numero++;
    } */
    echo "<br><br><br><br><br>";
    $numero=11;
    do {
        echo " $tabla X $numero = ".($tabla*$numero)."<br>";
        $numero++;
    } while ($numero<=10);
    echo "<br> ".floor(4.8);
    echo "<br> ".ceil(4.8);

    