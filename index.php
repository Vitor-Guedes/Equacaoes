<style>
    body{background:rgba(0, 0, 0, 0.87); color: white}
    i{color:rgba(123, 231, 092, 0.9)}
</style>
<?php
    ini_set('display_errors',1);
    ini_set('display_startup_erros',1);
    error_reporting(E_ALL);

    spl_autoload_register(function ($classname){
        include 'Classes/'.$classname.'.php';
    });
    // $eq = new Equacao("4x + 2 = 8 - 2x");
    // var_dump($eq);
    // echo '<hr>';
    // $eq2 = new Equacao("10x - 9 = 21 + 2x + 3x");
    // var_dump($eq2);
    // echo '<hr>';
    // $eq3 = new Equacao("10 - 8x + 2 = 5x - 8x + 2");
    // var_dump($eq3);
    // echo '<hr>';
    // $eq4 = new Equacao("3x – 2x + 10 = 10 + 5x – 40");
    // var_dump($eq4);
    // echo '<hr>';
    // $eq5 = new Equacao("3x – 2x + 10 = 10 + 5x – 40");
    // var_dump($eq5);
    
    $eq = new Equacao("x + 30 = 40");
    var_dump($eq);
    echo '<hr>';
    $eq2 = new Equacao("30 - 20 + 2x = 10");
    var_dump($eq2);
    echo '<hr>';
    $eq3 = new Equacao("3x - 10 + 13 = -2x +28");
    var_dump($eq3);
    echo '<hr>';
    $eq4 = new Equacao("20x-30=40+30-20");
    var_dump($eq4);
    echo '<hr>';
    $eq5 = new Equacao("-5x+45-89=-90+41");
    var_dump($eq5);
    echo '<hr>';
    $eq6 = new Equacao("10x-20=40+50");
    var_dump($eq6);
    echo '<hr>';
    $eq7 = new Equacao("20-80+2x=10");
    var_dump($eq7);
    echo '<hr>';
    $eq8 = new Equacao("19+2x-13=10-20");
    var_dump($eq8);
    echo '<hr>';
    $eq9 = new Equacao("10x-12+10=+18-20");
    var_dump($eq9);
    echo '<hr>';
    $eq10 = new Equacao("13x-23-45=-7x+12");
    var_dump($eq10);
    // echo '<hr>';
    // $eq2 = new Equacao("10x - 9 = 21 + 2x + 3x");
    // var_dump($eq2);
    // echo '<hr>';
    // $eq3 = new Equacao("10 - 8x + 2 = 5x - 8x + 2");
    // var_dump($eq3);
    // echo '<hr>';
    // $eq4 = new Equacao("3x – 2x + 10 = 10 + 5x – 40");
    // var_dump($eq4);
    // echo '<hr>';
?>