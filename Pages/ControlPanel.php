<?php
require '../Library/db.php';
session_start();
if (!isset($_SESSION['id']))
    header("Location: ../");
$Access = sqlUserAccess($_SESSION['id'])->fetch()['Access'];
?>
<!doctype html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="../Style/CPStyle.css" rel="stylesheet">
</head>
<body>
<div id="modal">
    <div id="modalContent">
        <div id="modalHeader">
            <span id="close">&times;</span>
            <h2 id="modalTitle"></h2>
        </div>
        <div id="modalBody">
            <div id="modalText"></div>
            <div id="loaderBox"><div class="loader" id="modalLoader"></div></div>
        </div>
    </div>
</div>
    <header>
        <div id="date"><?= jdate("l Y/m/d");?></div>
        <div id="headerTitle">کنترل پنل <?php if ($Access == "admin") echo "مدیریت"; else if($Access == "operator") echo "اپراتور";else echo "کاربر"?></div>
        <div id="showSidebar"><?= file_get_contents('./../Icon/menu.svg'); ?></div>
        <div id="user">سلام <?= sqlUserName($_SESSION['id'])->fetch()['U_Name']; ?></div>
    </header>
    <content>
        <div id="sidebar">
            <?php if ($Access == "admin"||$Access =="operator"): ?>
            <div data-btn="dashboard" class="OptionBox"><div class="OptionIcon"><?= file_get_contents('./../Icon/controlPanel.svg'); ?></div><span>داشبرد</span></div>
            <?php endif; ?>
            <div data-btn="advertise" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/advertising.svg'); ?></div><span>آگهی ها</span></div>
            <div data-btn="slides" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/slider.svg'); ?></div><span>اسلایدر</span></div>
            <?php if ($Access == "admin"): ?>
            <div data-btn="operators" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/operator.svg'); ?></div><span>اپراتورها</span></div>
            <div data-btn="subjects" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/subject.svg'); ?></div><span>دسته بندی ها</span></div>
            <div data-btn="users" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/user.svg'); ?></div><span>کاربران</span></div>
            <?php endif; ?>
            <div data-btn="profileEdit" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/edit.svg'); ?></div><span>ویرایش مشخصات کاربری</span></div>
            <div data-btn="goBack" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/back.svg'); ?></div><span>بازگشت به وب سایت</span></div>
            <div data-btn="logOut" class="OptionBox" ><div class="OptionIcon"><?= file_get_contents('./../Icon/logout.svg'); ?></div><span>خروج</span></div>
        </div>
        <main>
            <div id="main"></div>
            <div class="loader" id="pageLoader"></div>
        </main>
    </content>
    <script src='../Script/CPScript.js'></script>
</body>
</html>
