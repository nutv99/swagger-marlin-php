<?php
ini_set('display_errors', FALSE);
require("vendor/autoload.php");

/*
1.สร้าง Folder งาน ในที่นี้คือ petstore-3.0 
2.ไปใน folder งาน ปรับแก้ Model หรืือ Controller
3.เข้ามา run ไฟล์นี้ localhost/index.php เพื่อให้มันสร้าง  json file
4.เข้าไปปรับไฟล์ swagger ui ที่ index999.html แก้ไขชื่อ ไฟล์ marlinshop.json เป็นชื่อ ่json ที่สร้างจาก ไฟล์นี้  
*/
$openapi = \OpenApi\Generator::scan(['./petstore-3.0']);

//header('Content-Type: application/x-yaml');
//header('Content-Type: application/json');
//echo $openapi->toYaml();
$st = $openapi->toJson() ;

// ช่อไฟล์ json ที่สร้าง และจะให้ swagger-ui นำไปใช้ 
$sFileName  = "newfileMarlin.json" ;
$myfile = fopen($sFileName, "w") or die("Unable to open file!");

fwrite($myfile, $st);
fclose($myfile);
echo 'สร้างไฟล์ ' . $sFileName . ' เรียบร้อย ไป ปรับค่าใน index999.html ให้ชี้มายังไฟล์นี้ ' ;
 
?>