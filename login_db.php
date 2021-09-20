<?php 
    session_start();
    include('server.php');
    $errors = array(); //เอาไว้เก็บ error

    if (isset($_POST['login_user'])) { // click btn name login_user

        $time = time() - 30;  //วินาทีที่ต้องรอ
        //echo("time: ".$time);
        $ip_address =getIpAddr(); //เก็บ IP
        //echo("IP: ".$ip_address);

        //get count and ip  ค้นหาฟีล TryTime ที่มากกว่า30วิ และแอดเดรส
        $query_IP = "SELECT count(*) as total_count FROM loginlogs WHERE TryTime > '$time' AND `IpAddress` = '$ip_address' ";
        $query_IP_m = mysqli_query($conn, $query_IP); 
        $check_IP = mysqli_fetch_assoc($query_IP_m);
        $total_count = $check_IP['total_count'];  //จำนวนครั้งที่ล็อคอินภายใน30วินาที
        //echo("จำนวนรอบ: ".$total_count);


        /* ถ้าภายใน30วิ มีการล็อคอินผิด 3 ครั้ง */
        if($total_count == 3){
            array_push($errors, "คุณกรอกรหัสผ่านไม่ถูกต้องหลายครั้ง กรุณาลองใหม่ในอีก 30 วินาที");
            $_SESSION['error'] = "คุณกรอกรหัสผ่านไม่ถูกต้องหลายครั้ง กรุณาลองใหม่ในอีก 30 วินาที!";
            header("location: ../login.php");
        }

        /* ถ้าภายใน30วิ ยังไม่ล็อคอินผิด */
        else{

        $username = mysqli_real_escape_string($conn, addslashes($_POST['username'])); //เก็บusername
        $password = mysqli_real_escape_string($conn, addslashes($_POST['password'])); //เก็บpassword

        /* ตรวจสอบว่าค่าว่างหรือเปล่า ถ้าว่างให้  error เก็บข้อความไว้*/
        if (empty($username)) {
            array_push($errors, "Username is required");
        }

        if (empty($password)) {
            array_push($errors, "Password is required");
        }

        
    
        // ถ้าไม่มี error
        if (count($errors) == 0) {
            /* ตอนสมัคร มีการเก็บรหัสแบบเข้ารหัสไว้ ตอนเข้าระบบก็ต้องถอดรหัส */
            $password = md5($password);

            /* เช็ค username password จากuser */
            $query = "SELECT * FROM user_table WHERE `username` LIKE BINARY '%$username%' AND User_Password = '$password' ";
            $result = mysqli_query($conn, $query); 
            $objRe = mysqli_fetch_array($result);

            
            //ถ้า มีบัญชี ถูกต้อง
            if (mysqli_num_rows($result) == 1) {

                //ถ้าติดแบล็คลิส
                if($objRe["Login_Status"]=="pending"){
             
                    mysqli_close($conn);
                    $_SESSION['error'] = "'".$username."' บัญชีพบข้อผิดพลาดระหว่างใช้งานโปรดติดต่อทีมงาน! ";
                    header("location: ../login.php");
                }
        

              /*  if($objRe["Login_Status"]=="online"){
                    echo "'".$username."' username นี้กำลังใช้งานอยู่! ";
                    echo  $objRe["Login_Status"] ;
                    //echo  $objRe["user_id"] ;
                    
                }*/

                /* ล็อคอินสำเร็จ */
                else{
                    //IP ==>  ลบข้อมูล row ของ IP ที่ทำการล็อคอินสำเร็จให้หมด
                    mysqli_query($conn, "DELETE FROM loginlogs WHERE IpAddress = '$ip_address' ");
                    

                    //Updata status
                $sql = "UPDATE user_table SET Login_Status ='online' WHERE user_id ='".$objRe["user_id"]."'";
                $result = mysqli_query($conn, $sql) ;
                $query2 = mysqli_query($conn,$sql); // con มี nตัวเดียว

                $_SESSION['user_id'] = $objRe["user_id"];
                $_SESSION['username'] = $username;
                $_SESSION['success'] = "Your are now logged in";

                $Status_user = $objRe['User_Status'];

                //เป็นแอดมิน
                if($Status_user == 0){ mysqli_close($conn);
                    header("location: ../admin_page.php"); }
                //เป็นuser
                elseif($Status_user == 1){ mysqli_close($conn);
                    header("location: ../home.php"); }

                mysqli_close($conn);
                
                }
                
            } else { //ถ้าไม่พบบัญชี  แสดงว่า IP นี้อาจจะกรอกผิดหรือพยายามเข้าสู่ระบบอย่างมิชอบ
               
                $total_count++;
                $rem_attm = 3 - $total_count; //โอกาสในการเข้า

                if($rem_attm == 0){
                    array_push($errors, "คุณกรอกรหัสผ่านไม่ถูกต้องหลายครั้ง กรุณาลองใหม่ในอีก 30 วินาที!");
                    $_SESSION['error'] = "คุณกรอกรหัสผ่านไม่ถูกต้องหลายครั้ง กรุณาลองใหม่ในอีก 30 วินาที!";
                }else{
                    array_push($errors, "คุณกรอกรหัสผ่านไม่ถูกต้อง ลองได้อีก $rem_attm ครั้ง !");
                    $_SESSION['error'] = "คุณกรอกรหัสผ่านไม่ถูกต้อง ลองได้อีก $rem_attm ครั้ง !";
                }

                $try_time = time(); //เวลาที่เข้าผิด
                
                //เพิ่มข้อมูลในตารางว่ามีการเข้าที่ผิด
                mysqli_query($conn, "INSERT INTO loginlogs(IpAddress, TryTime) 
                VALUES ('$ip_address', '$try_time') ");

                header("location: ../login.php");
               
            }
        } 
        
        /* else {
            array_push($errors, "Username & Password is required");
            $_SESSION['error'] = "Username & Password is required";
            header("location: ../login.php");
        } */

        }
    }


     //ฟังก์ชัน การเก็บ IP ของผู้ใช้
     function getIpAddr(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ipAddr=$_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
        $ipAddr=$_SERVER['REMOTE_ADDR'];
        }
        return $ipAddr;
        }
?>