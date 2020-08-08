<?php
require '../Library/db.php';
session_start();
if ($_SESSION['id'] != "") {
    $Access = sqlUserAccess($_SESSION['id'])->fetch()['Access'];
    if ($Access == "admin"){
        $action = $_POST['action'];
        switch ($_POST['page']) {
            case "subjects":
                if($action=="remove")
                    sqlAdminEdit("removeSubject",$_POST['id']);
                elseif ($action=="deActive")
                    sqlAdminEdit("deActiveSubject",$_POST['id']);
                elseif ($action=="newSubject")
                    sqlNewSubject($_POST['subjectName']);
                break;
            case "users":
                if($action=="remove") {
                    sqlAdminEdit("removeUser", $_POST['id']);
                    sqlUserActionLog($_SESSION['id'], 7, "");
                }
                else
                    sqlAdminEdit("deActiveUser",$_POST['id']);
                break;
            case "operators":
                if($action=="remove")
                    sqlAdminEdit("removeUser", $_POST['id']);
                elseif ($action=="deActive")
                    sqlAdminEdit("deActiveUser",$_POST['id']);
                elseif ($action=="newOperator")
                    newOperator();
                break;
        }
    }
    switch ($_POST['page']) {
        case "advertise":
            $action = $_POST['action'];
            if ($action == "accept" && ($Access=="admin" || $Access=="operator"))
                sqlCPAdminSubmitAdvertise($_POST['id'],"accept",$_POST['statusNote']);
            elseif ($action == "refuse" && ($Access=="admin" || $Access=="operator"))
                sqlCPAdminSubmitAdvertise($_POST['id'],"nonaccept",$_POST['statusNote']);
            elseif ($action == "remove") {
                sqlAdminEdit("removeAdvertise", $_POST['id']);
                sqlUserActionLog($_SESSION['id'], 5, "");
            }
            elseif ($action == "deActive")
                sqlAdminEdit("deActiveAdvertise", $_POST['id']);
            elseif ($action == "slideRequest") {
                sqlAdminEdit("AddSlid", $_POST['id']);
                sqlUserActionLog($_SESSION['id'], 6, "");
            }
            break;
        case "slides":
            if ($_POST['action'] == "remove")
                sqlAdminEdit("removeSlid", $_POST['id']);
            break;
        case "editAdvertise":
            sqlCPUserBookEdit($_POST['Name'], $_POST['Price'], $_POST['Details'], $_POST['Subject'], $_POST['Image'], $_POST['Writer'], $_SESSION['id'],$_POST['bookId']);
            sqlUserActionLog($_SESSION['id'], 4, "");
            break;
        case "profileEdit":
            userProfileEdit();
            sqlUserActionLog($_SESSION['id'], 2, "");
            break;
    }
}
function newOperator(){
    $emailPattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
    $phonePattern = "/^09[0-9]{9}$/";
    $passPattern = "/^[_a-z0-9-]{4,8}$/";
    $name = $_POST['n'];
    $p = $_POST['p'];
    $email = $_POST['e'];
    $phone = $_POST['ph'];
    if(preg_match($emailPattern , $email)&&preg_match($phonePattern , $phone)&&preg_match($passPattern , $p)&& $name != "") {
        $pass = md5(sha1($p));
        $stm = sqlUserManage('userCheck', $email, $phone, 0, 0);
        $data = $stm->fetch();
        if ($data['U_Id'] == null) {
            sqlNewOperator($email, $phone, $pass, $name);
        }
    }
}
function userProfileEdit(){
    $emailPattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
    $phonePattern = "/^09[0-9]{9}$/";
    $passPattern = "/^[_a-z0-9-]{4,8}$/";
    $userNewName = $_POST['userNewName'];
    $userNewEmail = $_POST['userNewEmail'];
    $userNewPhone = $_POST['userNewPhone'];
    $userNewPass = $_POST['userNewPass'];
    $userOldPass = $_POST['userOldPass'];
    $userId = $_SESSION['id'];
    if($userNewName != "")
        sqlUserProfileEdit("U_Name= '$userNewName' WHERE U_Id= '$userId'");
    if($userNewEmail != "" && preg_match($emailPattern , $userNewEmail))
        sqlUserProfileEdit("Email= '$userNewEmail' WHERE U_Id= '$userId'");
    if($userNewPhone != "" && preg_match($phonePattern , $userNewPhone))
        sqlUserProfileEdit("U_Phone= '$userNewPhone' WHERE U_Id= '$userId'");
    if($userNewPass != "" && $userOldPass != "" && preg_match($passPattern , $userNewPass) && preg_match($passPattern , $userOldPass)) {
        $hashNewPass = md5(sha1($userNewPass));
        $hashOldPass = md5(sha1($userOldPass));
        sqlUserProfileEdit("Password= '$hashNewPass' WHERE U_Id= '$userId' AND Password= '$hashOldPass'");
    }
}