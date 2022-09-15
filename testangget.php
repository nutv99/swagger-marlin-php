<?php 

  header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Headers: 'x-requested-with, Content-Type, origin, authorization, accept, client-security-token'");

  header("Access-Control-Max-Age: 99500");

      
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




// ตรวจสอบข้อมูลว่า มี id ซ้ำกันไหม 
   // ตรวจสอบ ค่าจาก ตาราง customer
   $sql = "SELECT customer_id,customer_name,customer_lastname,customer_email,customer_address,'-' as remark  FROM customer where customer_id=?";
  // คำสั่งสำหรับ execute sql 
   try {        
      $params = array(trim($request->customer_id)); 
      $rs = $pdo->prepare($sql);
      $rs->execute($params);
      $doworkSuccess = true ;
   } catch (PDOException $ex) {
      echo  $ex->getMessage();
   
   } catch (Exception $exception) {
           // Output unexpected Exceptions.
           Logging::Log($exception, false);
   } 

   //echo $rs->rowCount() . '*****';

   if ($rs->rowCount() > 0) { 
	 // เตรียม ข้อมูล  json 

	 $data = array();
	 while($row = $rs->fetch( PDO::FETCH_ASSOC )) {
       $data[] =  $row; 
     }

	 $response = json_decode('{}'); 
     $response->resultcode = '1' ;
     $response->result = json_encode($data) ;
	 echo json_decode($response);
	 

        

	  
   }  else {
     //ไม่พบ ข้อมูล ให้ทำการ  
	 $response = json_decode('{}'); 
     $response->resultcode = '0' ;
     $response->result = 'ไม่พบข้อมูล' ;
    
	 echo json_decode($response);
	  
   }

    
  
   
   return;
 
	

  // Validate.

//  echo $request->customer_address ;
  if(trim($request->customer_address) === '' )
  {
    return http_response_code(400);
  } 
  
  
	

      
  
   


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


$sql = "SELECT product_master_id as product_list_id,
product_name as product_list_name,
product_brand_id as product_list_brand, 
product_group_id product_list_group,
product_img as imgSrc 
FROM product_master where product_master_id=?"; 
  
 $st = '{
	 "product_list_id" : "1" ,
	 "product_list_name" : "ชื่อProduct" 
	 }';

 echo json_encode($st);


?>

