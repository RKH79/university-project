<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="Style/style.css" rel="stylesheet">
    <title>BOOKSTORE</title>
</head>
<body>

<?php
date_default_timezone_set("Asia/Tehran");
session_start();
require 'Library/db.php';
if (isset($_SESSION['id']) && sqlUserAccess($_SESSION['id'])->fetch()['Access'] != "user" && !isset($_GET['B']))
    header("Location: Pages/ControlPanel.php");
(isset($_GET['p']) && ($_GET['p']=="b"))? $page = "Book.php" : $page = "Home.php";
?>

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
    <nav>
        <div id="profile" title="پنل کاربر" data-login="<?= isset($_SESSION['id'])? "Yes" : "No" ; ?>"><?= file_get_contents('./Icon/user.svg'); ?></div>
        <?= isset($_SESSION['id'])? "<div id=\"userName\">سلام ".sqlUserName($_SESSION['id'])->fetch()['U_Name']."</div>" : "" ; ?>
        <div class="links">
            <a href="index.php" class="linkBtn" >صفحه نخست</a>
            <?php
                $stm = sqlQuery('menu');
                while( $row = $stm->fetch() ):?>
            <a class="linkBtn linkModal" data-id="<?= $row['M_Id']?>"><?= $row['M_Name'] ?></a>
            <?php endwhile;?>
        </div>
        <div class="logo"><?= file_get_contents('./Icon/book.svg'); ?></div>
        <div id="newAdvertise" class="Btn">ثبت آگهی جدید</div>
    </nav>
<?php
    require "Pages/$page";
?>
    <footer>
        &copy; Created By Rohollah Khoshhal &hearts;
    </footer>
        <script src='Script/script.js'></script>
</body>
</html>