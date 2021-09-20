<?php
    session_start();
    include('server.php'); //เชื่อต่อฐานข้อมูล

?>
<!-- เรียกใช้งานในส่วน server และ session  -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" type="text/css" media="all" href="login-style.css?ver=1001" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"
        integrity="sha384-tKLJeE1ALTUwtXlaGjJYM3sejfssWdAaWR2s97axw4xkiAdMzQjtOjgcyw0Y50KU" crossorigin="anonymous" />
</head>

<body>

    <main>
        <div class="container">
            <!-- ***********  header *********** -->
            <div class="header">
                <p>Ugly Duckling</p>
                <p>เข้าสู่ระบบ</p>
            </div>

            <!-- **ทำงานร่วมกับ login_db.php** -->
            <form action="db/login_db.php" method="post">

                <div class="error_show">
                    <?php if (isset($_SESSION['error'])) : ?>
                    <div class="error">
                        <h3>
                            <?php 
                                echo $_SESSION['error'];
                                unset($_SESSION['error']);
                            ?>
                        </h3>
                    </div>
                    <?php endif ?>
                </div><br>

                <!-- ***********  login *********** -->

                <input class="text" type="text" name="username" placeholder="username">

                <input class="text" type="password" name="password" placeholder="password">

                <input type="hidden" name="" id="" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">

                <div class="button">
                    <a href="register.php">สมัครสมาชิก</a>

                    <button type="submit" name="login_user" class="btn">Login</button>
                    <br>

                    <a href="sendMailer/sendMail_ResetPass_page.php">ลืมรหัสผ่าน?</a>
                </div>
            </form>
        </div>
    </main>


</body>
<?php 
session_destroy();
unset($_SESSION['username']);
?>

</html>