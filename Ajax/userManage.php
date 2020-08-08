<?php
require '../Library/db.php';
$emailPattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
$phonePattern = "/^09[0-9]{9}$/";
$passPattern = "/^[_a-z0-9-]{4,8}$/";
session_start();
switch ($_POST['act']){
    case "signUp" :
        signUp($emailPattern,$phonePattern,$passPattern);
        break;
    case "logIn" :
        logIn($emailPattern,$passPattern);
        break;
    case "imageUpload" :
        imageUpload();
        break;
    case "newAdvertise" :
        newAdvertise();
        break;
    default:
        echo json_encode(['text' => "در بازیابی اطلاعاتی مشکلی وجود دارد!!",'reload'=>false]);
        break;
}

function signUp($emailPattern,$phonePattern,$passPattern){
    $name = $_POST['n'];
    $p = $_POST['p'];
    $email = $_POST['e'];
    $phone = $_POST['ph'];
    if(preg_match($emailPattern , $email)&&preg_match($phonePattern , $phone)&&preg_match($passPattern , $p)&& $name != ""){
        $pass = md5(sha1($p));
        $stm = sqlUserManage('userCheck',$email,$phone,0,0);
        $data = $stm->fetch();
        if ($data['U_Id'] == null) {
            sqlUserManage('signUp', $email, $phone, $pass,$name);
            $stm = sqlUserManage('logIn', $email, 0, $pass,0);
            $data = $stm->fetch();
            $_SESSION['id'] = $data['U_Id'];
            sqlUserActionLog($data['U_Id'], 1, "");
            echo json_encode(['text' => "",'reload' => true]);
        }
        else
            echo json_encode(['text' => "کاربری با این مشخصات قبلا ثبت شده است.",'reload'=>false]);
    }
    else{
        echo json_encode(['text' => "نام کاربری یا رمز ورود مشکل ساختاری دارد!",'reload'=>false]);
    }
}

function logIn($emailPattern,$passPattern){
    $p = $_POST['p'];
    $email = $_POST['e'];
    if(preg_match($emailPattern , $email)&&preg_match($passPattern , $p)){
        $pass = md5(sha1($p));
        $stm = sqlUserManage('logIn',$email,0,$pass,0);
        $data = $stm->fetch();
        if ($data['U_Id'] != null) {
            $_SESSION['id'] = $data['U_Id'];
            echo json_encode(['text' => "", 'reload' => true]);
        }
        else
            echo json_encode(['text' => "نام کاربری یا رمز ورود نادرست است.", 'reload' => false]);
    }
    else{
        echo json_encode(['text' => "نام کاربری یا رمز ورود مشکل ساختاری دارد!",'reload'=>false]);
    }
}

function imageUpload(){
    if(is_array($_FILES)) {
        if ($_POST['Del']!==""){
            $DeleteFile = str_replace('url("../','',$_POST['Del']);
            $DeleteFile = str_replace('")','',$DeleteFile);
            if(is_file("../".$DeleteFile))
                unlink("../".$DeleteFile);
        }
        $file = $_FILES['file-select']['tmp_name'];
        $sourceProperties = getimagesize($file);
        $imageType = $sourceProperties[2];
        $imageWidth = $sourceProperties[0];
        $imageHeight = $sourceProperties[1];
        date_default_timezone_set("Asia/Tehran");
        $fileNewName = date("Y-m-d_H-i-s__") . rand(1,1000);
        $folderPath = "../Image/";
        $ext = pathinfo($_FILES['file-select']['name'], PATHINFO_EXTENSION);
        switch ($imageType) {
            case IMAGETYPE_PNG:
                $imageResourceId = imagecreatefrompng($file);
                $targetLayer = imageResize($imageResourceId,$imageWidth,$imageHeight);
                imagepng($targetLayer,$folderPath. $fileNewName. ".". $ext);
                break;
            case IMAGETYPE_JPEG:
                $imageResourceId = imagecreatefromjpeg($file);
                $targetLayer = imageResize($imageResourceId,$imageWidth,$imageHeight);
                imagejpeg($targetLayer,$folderPath. $fileNewName. ".". $ext);
                break;
            default:
                echo "Invalid Image type.";
                exit;
                break;
        }
        echo "$fileNewName.$ext";
    }
}

function imageResize($imageResourceId,$width,$height) {/* resize image ******/
    $targetWidth =426;
    $targetHeight =600;
    $targetLayer=imagecreatetruecolor($targetWidth,$targetHeight);
    imagecopyresampled($targetLayer,$imageResourceId,0,0,0,0,$targetWidth,$targetHeight, $width,$height);
    return $targetLayer;
}

function newAdvertise(){
    if ($_SESSION['id'] != "") {
        sqlInsertNewAdvertise($_POST['Name'], $_POST['Price'], $_POST['Details'], $_POST['Subject'], $_POST['Image'], $_POST['Writer'], $_SESSION['id']);
        sqlUserActionLog($_SESSION['id'], 3, "");
        echo json_encode(['text' => "",'reload' => true]);
    }
    else
        echo json_encode(['text' => "در ثبت آگهی خطایی رخ داده است!!",'reload'=>false]);
}
