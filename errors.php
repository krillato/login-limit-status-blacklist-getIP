<!-- สร้างตัวแปร ไว้เก็บ error  -->
<?php $errors = array(); ?>

<!--  ถ้าเกิด error ขึ้นมากกว่า0ครั้ง -->
<?php if (count($errors) > 0) : ?>
    <div class="error">
        <?php foreach ($errors as $error) : ?>
            <p><?php echo $error ?></p>  <!--  แสดง error ที่เก็บลงไปในตัวแปรแต่ละจุด -->
        <?php endforeach ?>
    </div>
<?php endif ?>