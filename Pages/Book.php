<?php
if (!isset($_GET['id']))
    header("Location: ../");
$stm = sqlModalBook($_GET['id']);
$row = $stm->fetch()?>
<div id="bookPageBox">
    <div id="bookPageBookImg" Style=" background-Image: url('<?= ($row['B_Img']!="")? "Image/".$row['B_Img'] : "Icon/no_img.png"; ?>');"></div>
    <div id="bookPageBookBox">
        <div id="bookPageBookName" >نام کتاب : <?= $row['B_Name']; ?></div>
        <div id="bookPageBookDetails" >توضیحات : <?= $row['B_Details']; ?></div>
        <div id="bookPageBookWriter" >نام نویسنده : <?= $row['B_Writer']; ?></div>
        <div id="bookPageBookPrice" >قیمت کتاب : <?= $row['B_Price']; ?></div>
        <div id="bookPageBookDate" >تاریخ ثبت آگهی : <?= dateCompare($row['B_Date'],"mts"); ?></div>
        <div id="bookPageBookUPhone" >شماره تلفن فروشنده : <?= $row['U_Phone']; ?></div>
    </div>
</div>