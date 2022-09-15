<?php 

  header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: 'x-requested-with, Content-Type, origin, authorization, accept, client-security-token'");

//  header("Access-Control-Max-Age: 99500");

      
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);   

// รับค่า ที่ post มาจาก  client 	 
   $postdata = file_get_contents("php://input");  
//แปลงเป็น json ใส่ตัวแปร  $resuest    
   $request = json_decode($postdata);

// การอ้างถึงหรือนำ ตัวแปร ที่ไปใช้ จะเป็น ในรูปแบบ $request->citizenid หรือ   $request->customername


// ติดต่อฐานข้อมูล 
   $dbname = 'mywork' ; $usetrans = true;
   $pdo =getPDO($dbname,$usetrans) ; 


   //$imgFileName = SaveUploadImage($request)  ;

// ตรวจสอบข้อมูลว่า มี id ซ้ำกันไหม 
   // ตรวจสอบ ค่าจาก ตาราง customer
   $sql = "SELECT * FROM customer where customer_id=?";
  // คำสั่งสำหรับ execute sql 
   try {        
      $params = array(trim($request->customer_id)); 
      $rs = $pdo->prepare($sql);
      $rs->execute($params);
      $doworkSuccess = true ;
      //$pdo->commit();
   } catch (PDOException $ex) {
      echo  $ex->getMessage();
   
   } catch (Exception $exception) {
           // Output unexpected Exceptions.
           Logging::Log($exception, false);
   } 

   //echo $rs->rowCount() . '*****';

   if ($rs->rowCount() > 0) { 
	  //มีข้อมูลอยู่แล้ว ให้ทำการ  Update
     $action = ' ปรับปรุงข้อมูล ' ;
	 //echo $action;
	 $sql = "UPDATE customer SET customer_name=?,customer_lastname=?,customer_address=? WHERE customer_id=?";    
	 //สร้าง array ของ parameter ตาม ? ในที่นี้มีจำนวน 4 ตัว 
	 $params=array(
	   $request->customer_name,
	   $request->customer_lastname,
	   $request->customer_address,
       $request->customer_id
	 ) ;
   }  else {
     //ไม่พบ ข้อมูล ให้ทำการ  Insert  
	 $action = 'เพิ่มข้อมูล ' ;
	 //echo $action;
	 $sql = "INSERT INTO customer(customer_id,customer_name,customer_lastname,customer_address) VALUES(?,?,?,?)"; 
	 $params=array(
       $request->customer_id,
	   $request->customer_name,
	   $request->customer_lastname,
	   $request->customer_address       
	 ) ;
   }

   try {        
     
      $rs = $pdo->prepare($sql);
      $rs->execute($params);
      $doworkSuccess = true ;
      
   } catch (PDOException $ex) {
      echo  $ex->getMessage();
	  $response = json_decode('{}'); 
      $response->resultcode = '-1' ;
      $response->result = $ex->getMessage() ;

	 echo json_encode($response);
	 return ;
   
   } catch (Exception $exception) {
     // Output unexpected Exceptions.
     Logging::Log($exception, false);
	 $response = json_decode('{}'); 
     $response->resultcode = '-1' ;
     $response->result = $exception->code ;
    
	 echo json_encode($response);
	 return ;

   } 

   $pdo->commit();

   $response = json_decode('{}'); 
   $response->resultcode = 1;
   $response->result = $action . '- Success' ;
   $response->httpresponse = http_response_code(200);
   $response->imgFileName = $imgFileName;

   echo json_encode($response);

   
   return;
 
	

  // Validate.

//  echo $request->customer_address ;
  if(trim($request->customer_address) === '' )
  {
    return http_response_code(400);
  } 
  
  
	
function SaveUploadImage($request) { 


    $folder = "upload/";
	if (!file_exists($folder)) {
		mkdir($folder,true);  chmod($folder,0777);
	}
    /*$postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
	*/
      
    $img = explode(";base64,", $request->imgSrc);
      
    $img_aux = explode("image/", $img[0]);
      
    $image_type = $img_aux[1];
      
    $img_base64 = base64_decode($img[1]);
      
    $imageFileName = $folder . uniqid() . '.png';
      
    file_put_contents($imageFileName, $img_base64);

	return $imageFileName;




} // end function

      
  
   


function getPDO($dbname,$withtrans) {

  

  $username = 'root' ;
  $passw = '' ;
  
  $stconnect = "mysql:host=localhost;dbname=" . $dbname;
  //echo $username . '-' .$passw . '<hr>';
  try {
    $pdo = new PDO($stconnect, $username, $passw, array(
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false));
   
  } catch (PDOException $ex) {
    echo  $ex->getMessage();
   }
   $pdo->exec("set names utf8") ;
  if ($withtrans != "") {
     $pdo->beginTransaction();
   }
   $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

   return $pdo ;


} // end function


  
/*
ขั้นตอนการทำงานกับ ฟอร์ม 
1. ตรวจเชค Validation กับ ทุกฟิลด์ ที่ห้ามเป็น ค่าว่าง โดย ไปไล่ดูฟิลด์  จาก Table Structure ว่า ฟิลด์ ไหน เป็น not null อันนั้นต้อง ใส่ required
2. ฟิลด์ไหนที่ เป็นค่า PK,FK  ต้องใส่ Required
3. กรณี ที่เป็นการ แก้ไข  (Edit) เมื่อ User ทำการ กรอกข้อมูล รหัสสินค้า และ ค้นข้อมูลเจอ แล้ว ต้อง Disabled ค่า PK ให้ห้ามแก้ไข  ตัวอย่างเช่น User คีย์ ข้อมูล รหัส A1 แล้วดีึงข้อมูลออกมาแก้ไข และ ไปคีย์ กรอกข้อมูล รหัสสินค้า เป็น B1 โดยไม่ได้ ทำการดึงข้อมูล แต่ กดบันทึกไปเลย ระบบ ก็จะไป Update ข้อมูล B1 แทน ซึ่งผิดวัตถุประสงค์ 
4. กรณี ที่เป็น การเพิ่ม เมื่อ User ทำการ ป้อนรหัสสินค้าเสร็จ ต้แงตรวจสอบว่า รหัส สินค้า ตัวนี้ มีอยู่ใน Database หรือยัง ถ้าตรวจพบว่า มี ให้ทำการ แจ้งเตือน User และ หยุดการทำงาน โดยอาจจะให้ User ไปกด สร้างสินค้าใหม่
5. กรณี ลบข้อมูล ก็คล้ายคลึงกับ ข้อ 3
6. เมื่อจะ submit ข้อมูล ต้องส่ง jwt แนบไปด้วย เพื่อทาง ฝั่ง Server จะได้ทำการ ตรวจสอบว่า jwt ที่แนบมา กับ Headers นั้นเป็น jwt ที่ออกจาก Server หรือไม่ และทางฝั่ง  Server ก็จะ ถอดรหัส (Decrypt) jwt ออกมา เพื่อจะได้ทราบว่า เป็น User คนไหนที่ทำรายการ เพิ่มหรือ ลบ ข้อมูลนี้ 
7. 
*/

