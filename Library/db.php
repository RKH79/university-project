<?php
require 'jdf.php';
$host		= 'localhost';
$db 		= 'bookstore';
$username 	= 'root';
$password 	= '';
$dsn		= "mysql:host=$host;dbname=$db;charset=utf8";
try{
    $db = new PDO ($dsn , $username , $password);
}
catch (PDOException $e){
    die( $e->getMessage() );
}
/* for Homepage *************************/
function onLoadHomepage($query,$id){
    switch ($query) {
        case 'autoAdvertiseRemove':
            $expireDate = date("Y-m-d H:i:s",strtotime("-60 day"));
            $sql = "DELETE FROM books WHERE B_Date < '$expireDate'";
            break;
        case 'autoSlideRemove':
            $sql = "DELETE FROM slider WHERE B_Id = $id;DELETE FROM listslider WHERE B_Id = $id";
            break;
        case 'SelectAutoSlideRemove':
            $expireDate = date("Y-m-d H:i:s",strtotime("-7 day"));
            $sql = "SELECT B_Id FROM slider WHERE slider.Slide_Date < '$expireDate' ";
            break;
        case 'countSliderAutoAdd':
            $sql = "SELECT COUNT(*) from slider";
            break;
        case 'selectListSliderAutoAdd':
            $sql = "SELECT B_Id FROM listslider WHERE B_Id NOT IN(SELECT B_Id FROM slider)";
            break;
        case 'insertToSliderAutoAdd':
            $sql = "INSERT INTO slider(B_Id) VALUES ('$id')";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlQuery($query)//for first load
{
    switch ($query) {
        case 'subject':
             $sql = "SELECT S_Id,S_Name FROM subjects WHERE S_Active = 1";
            break;
        case 'menu':
             $sql = "SELECT M_Id,M_Name FROM menus WHERE M_Visible = 1 ";
            break;
        case 'sliderBooks':
             $sql = "SELECT books.B_Id , B_Date , B_Name , B_Price , B_Img , B_Writer FROM books,slider WHERE books.B_Id = slider.B_Id AND B_Status ='accept' AND B_Visible = 1 ";
            break;
        default:
             $sql ="";
    }
    return execute($sql);
}
function sqlQueryLoadBook($query,$cun,$num,$cat)
{
    $catWhere = "";
    if($cat!="all")
        $catWhere = "AND books.S_Id =".$cat;
    switch ($query) {
        case 'loadBook':
            $sql = "SELECT B_Id , B_Date , B_Name , B_Price , B_Img FROM books,subjects WHERE B_Status ='accept' AND books.S_Id = subjects.S_Id AND subjects.S_Active = 1 AND B_Visible = 1 $catWhere ORDER BY B_Id DESC LIMIT $cun , $num";
            break;
        case 'count':
			$sql = "SELECT count(*) from books WHERE B_Status ='accept' AND B_Visible = 1 $catWhere";
			break;
        default:
            $sql ="";
	}
	return execute($sql);
}
function sqlSearch($string){
    $sql = "SELECT B_Id , B_Date , B_Name , B_Price , B_Img FROM books WHERE B_Status ='accept' AND B_Visible = 1 AND B_Name LIKE '%$string%'";
    return execute($sql);
}
function sqlModalLink($Mid){
    $sql = "SELECT M_Text FROM menus WHERE M_Visible = 1 AND M_Id = $Mid";
    return execute($sql);
}
function sqlModalBook($Bid){
    $sql = "SELECT B_Name,B_Details,B_Writer,B_Price,B_Date,B_Img,U_Phone FROM books,users WHERE books.U_Id = users.U_Id  AND B_Status ='accept' AND B_Visible = 1 AND B_Id = $Bid ; UPDATE `books` SET `B_View`= B_View+1 WHERE B_Id = $Bid";
    return execute($sql);
}
function sqlUserManage($query,$email,$phone,$pass,$name){
    switch ($query) {
        case 'userCheck':
            $sql = "SELECT U_Id FROM users WHERE Email='$email' OR U_Phone='$phone'";
            break;
        case 'signUp':
            $sql = "INSERT INTO users ( Email, Password, U_Phone,U_Name) VALUES ('$email', '$pass', '$phone','$name')";
            break;
        case 'logIn':
            $sql = "SELECT U_Id FROM users WHERE Email = '$email' AND Password = '$pass' AND U_Active = 1";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlUserActionLog($Uid,$Aid,$details){
    $sql = "INSERT INTO useraction(U_Id, A_Id, Action_Details) VALUES ('$Uid','$Aid','$details')";
    return execute($sql);
}
function sqlInsertNewAdvertise($name,$prise,$details,$subject,$image,$writer,$UId){
        $sql = "INSERT INTO books ( B_Name , B_Writer , B_Price , B_Details , B_Img , U_Id , S_Id ) VALUES ('$name','$writer','$prise','$details','$image','$UId','$subject')";
    return execute($sql);
}
function sqlUserAccess($Uid){
    $sql = "SELECT Access FROM users WHERE U_Id = $Uid ";
    return execute($sql);
}

/* for control panel ******************************************/
function sqlUserName($Uid){
    $sql = "SELECT U_Name FROM users WHERE U_Id = $Uid ";
    return execute($sql);
}
function sqlUserDetails($Uid){
    $sql = "SELECT U_Name,Email,U_Phone FROM users WHERE U_Id = $Uid ";
    return execute($sql);
}
function sqlUserProfileEdit($query){
    $sql = "UPDATE users SET $query ";
    return execute($sql);
}
/* for admin and operator ************/
function sqlCPCounter($query){
    switch ($query) {
        case 'activeAdvertise':
            $sql = "SELECT count(*) from books WHERE B_Status ='accept' AND B_Visible = 1";
            break;
        case 'newAdvertise':
            $sql = "SELECT count(*) from books WHERE B_Status ='new' AND B_Visible = 1";
            break;
        case 'sliderList':
            $sql = "SELECT count(*) FROM listslider WHERE B_Id NOT IN(SELECT B_Id FROM slider) ";
            break;
        case 'subjects':
            $sql = "SELECT count(*) FROM subjects WHERE S_Active = 1";
            break;
        case 'operators':
            $sql = "SELECT count(*) FROM users WHERE Access = 'operator' AND U_Active = 1";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlCPDateCounter($query,$date){
    switch ($query) {
        case 'userSignUp':
            $sql = "SELECT count(*) FROM useraction,actionlist WHERE useraction.A_Id = actionlist.A_Id AND A_Name = 'signUp' AND Action_date LIKE '%$date%'";
            break;
        case 'advertiseChart':
            $sql = "SELECT count(*) FROM books WHERE B_Date LIKE '%$date%'";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlCPAdmin($query){
    switch ($query) {
        case 'allAdvertise':
            $sql = "SELECT B_Id,B_Name,B_Price,B_Status,B_Date,B_View,B_Visible,S_Name FROM books,subjects WHERE books.S_Id = subjects.S_Id ORDER BY books.B_Status ASC";
            break;
        case 'allSlide':
            $sql = "SELECT books.B_Id,B_Name,B_Price,S_Name,Slide_Date FROM books,listslider,subjects WHERE books.B_Id = listslider.B_Id AND books.S_Id = subjects.S_Id AND B_Status ='accept' AND B_Visible = 1";
            break;
        case 'allOperators':
            $sql = "SELECT U_Id,U_Name,Email,U_Phone,U_Active FROM users WHERE Access = 'operator'";
            break;
        case 'allSubjects':
            $sql = "SELECT S_Id,S_Name,S_Active FROM subjects";
            break;
        case 'allUsers':
            $sql = "SELECT U_Id,U_Name,Email,U_Phone,U_Active FROM users WHERE Access = 'user'";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlCPCountById($query,$id){
    switch ($query) {
        case 'subjectCountBook':
            $sql = "SELECT count(*) FROM subjects,books WHERE subjects.S_Id = books.S_Id AND subjects.S_Id = $id";
            break;
        case 'userCountBook':
            $sql = "SELECT count(*) FROM users,books WHERE users.U_Id = books.U_Id AND users.U_Id = $id";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlAdminEdit($query,$id){
    switch ($query) {
        case 'deActiveSubject':
            $sql = "UPDATE subjects SET S_Active = !S_Active WHERE S_Id = $id";
            break;
        case 'removeSubject':
            $sql = "DELETE FROM subjects WHERE S_Id = $id;DELETE FROM books WHERE S_Id = $id";
            break;
        case 'deActiveUser':
            $sql = "UPDATE users SET U_Active = !U_Active WHERE U_Id = $id";
            break;
        case 'removeUser':
            $sql = "DELETE FROM users WHERE U_Id = $id;DELETE FROM books WHERE U_Id = $id";
            break;
        case 'submitAdvertiseStatus':
            $sql = "SELECT 	B_Status FROM books WHERE B_Id = $id";
            break;
        case 'removeAdvertise':
            $sql = "DELETE FROM books WHERE B_Id = $id;DELETE FROM slider WHERE B_Id = $id;DELETE FROM listslider WHERE B_Id = $id";
            break;
        case 'deActiveAdvertise':
            $sql = "UPDATE books SET B_Visible = !B_Visible WHERE B_Id = $id";
            break;
        case 'removeSlid':
            $sql = "DELETE FROM listslider WHERE B_Id = $id;DELETE FROM slider WHERE B_Id = $id";
            break;
        case 'AddSlid':
            $sql = "INSERT INTO listslider(B_Id) VALUES ('$id')";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlCPAdminSubmitAdvertise($Bid,$B_Status,$StatusNote){
    $sql = "UPDATE books SET B_Status = '$B_Status' , Status_Note = '$StatusNote' WHERE B_Id = $Bid";
    return execute($sql);
}
function sqlCPAdminModalBook($Bid){
    $sql = "SELECT B_Details,B_Writer,B_Price,B_Date,B_Img,B_View,B_Status,B_Visible,U_Phone,U_Name,Email,S_Name,Status_Note FROM books,users,subjects WHERE books.U_Id = users.U_Id AND books.S_Id = subjects.S_Id AND B_Id = $Bid";
    return execute($sql);
}
function sqlCPAdminModalSlide($Bid){
    $sql = "SELECT B_Details,B_Writer,B_Price,B_Img,U_Phone,U_Name,Email,S_Name,Slide_Date FROM books,users,subjects,listslider WHERE books.U_Id = users.U_Id AND books.S_Id = subjects.S_Id AND books.B_Id = listslider.B_Id AND B_Visible = 1 AND books.B_Id = $Bid";
    return execute($sql);
}
function sqlNewSubject($name){
    $sql = "INSERT INTO subjects(S_Name) VALUES ('$name')";
    return execute($sql);
}
function sqlNewOperator($email,$phone,$pass,$name){
    $sql = "INSERT INTO users ( Email, Password, U_Phone,U_Name,Access) VALUES ('$email', '$pass', '$phone','$name','operator')";
    return execute($sql);
}
/* fore user*********************/
function sqlCPUser($query,$Uid){
    switch ($query) {
        case 'userAllAdvertise':
            $sql = "SELECT B_Id,B_Img,B_Name,B_Price,B_Status,B_Date,B_View,subjects.S_Name FROM books,subjects WHERE books.S_Id = subjects.S_Id AND U_Id ='$Uid'";
            break;
        case 'userAllSlide':
            $sql = "SELECT books.B_Id,B_Img,B_Name,B_Price,subjects.S_Name,Slide_Date FROM books,subjects,listslider WHERE books.S_Id = subjects.S_Id AND books.B_Id = listslider.B_Id AND B_Status ='accept'AND U_Id ='$Uid'";
            break;
        default:
            $sql ="";
    }
    return execute($sql);
}
function sqlCPUserModalBook($Bid,$Uid){
    $sql = "SELECT B_Details,B_Writer,B_Price,B_Date,B_Img,B_View,B_Status,S_Name,Status_Note FROM books,users,subjects WHERE books.U_Id = users.U_Id AND users.U_Id = $Uid AND books.S_Id = subjects.S_Id AND B_Visible = 1 AND books.B_Id = $Bid";
    return execute($sql);
}
function sqlCPUserModalSlide($Bid,$Uid){
    $sql = "SELECT B_Details,B_Writer,B_Price,B_Img,S_Name,Slide_Date FROM books,users,subjects,listslider WHERE books.U_Id = users.U_Id AND users.U_Id = $Uid AND books.S_Id = subjects.S_Id AND books.B_Id = listslider.B_Id AND B_Visible = 1 AND books.B_Id = $Bid";
    return execute($sql);
}
function sqlCPUserModalBookEdit($Bid,$Uid){
    $sql = "SELECT B_Name,B_Details,B_Writer,B_Price,B_Img,books.S_Id FROM books,users,subjects WHERE books.U_Id = users.U_Id AND users.U_Id = $Uid AND books.S_Id = subjects.S_Id AND B_Visible = 1 AND books.B_Id = $Bid";
    return execute($sql);
}
function sqlCPUserBookEdit($name,$prise,$details,$subject,$image,$writer,$Uid,$Bid){
    $sql = "UPDATE books SET B_Name ='$name' , B_Writer='$writer' , B_Price='$prise' , B_Details='$details' , B_Img='$image' , S_Id='$subject' ,B_Status = 'new' WHERE U_Id = '$Uid' AND B_id = $Bid";
    return execute($sql);
}
/* execute all query**************************/
function execute($sql){
	global $db;
	$stm  = $db->prepare($sql);
	$stm->execute();
	return $stm;
}
/* convert date *********************************/
function dateCompare($date,$clockShow)
{
        $date = str_replace('-','',$date);
        $date = str_replace(' ','',$date);
        $year = substr($date ,0,4);//sal
        $mons = substr($date ,4,2);//mah
        $day = substr($date ,6,2);//roz
        $clock = substr($date ,8,5);//clock
        $ret = gregorian_to_jalali($year, $mons, $day, '/');
        if ($clockShow)
            $ret .=" " . $clock;
    return ($ret);
}