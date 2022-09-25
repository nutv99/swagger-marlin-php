# swagger-marlin-php
<h2>ต้นแบบ การทำ swagger บน php </h2>
อ้างอิง 
 <ol>
<li>- https://medium.com/@tatianaensslin/how-to-add-swagger-ui-to-php-server-code-f1610c01dc03
<li>- https://codebeautify.org/yaml-to-json-xml-csv 
<li>- https://github.com/zircote/swagger-php
 </ol>
<h2>ขั้นตอน  </h2>
<ol>
 <li>composer require zircote/swagger-php
 <li>ไปที่ path petstore-3.0 หรือ folder งาน ปรับ Model,Controller ตามต้องการ
 <li>เรียกไฟล์ localhost/genJSONForSwagger.php  มันจะทำการ gen ไฟล์ json ออกมาให้ 
 <li>นำ ชื่อไฟล์ json ไปแก้ที่ index999.html สมมุติชื่อ json ที่มัน gen คือ newfileMarlinWork.json
 <li> เรียก localhost/index999.html มันจะแสดงหน้า swagger api ออกมาให้
  <li>ปรับแก้ค่าใน json ไฟล์  newfileMarlinWork.json--- >"servers": [
        {
            "url": "https://virtserver999.swaggerhub.com/swagger/Petstore/1.0.0",
            "description": "SwaggerHUB API Mocking"
        }
    ],
 </ol>
 
