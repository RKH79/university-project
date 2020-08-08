<?php
require '../Library/db.php';
session_start();
if ($_SESSION['id'] != ""){
    $Access = sqlUserAccess($_SESSION['id'])->fetch()['Access'];
    $id = $_SESSION['id'];
    $reload = false;
    $Html = "";
    switch ($_POST['page']){
        case "dashboard" :
            if ($Access == "operator"|| $Access == "admin")
                $Html = adminDashboard($Access);
            break;
        case "advertise":
            if ($Access == "user")
                $Html = userAdvertise($id);
            else if ($Access == "operator"|| $Access == "admin")
                $Html = adminAdvertise();
            break;
        case "slides":
            if ($Access == "user")
                $Html = userSlides($id);
            else if ($Access == "operator"|| $Access == "admin")
                $Html = adminSlides();
            break;
        case "operators":
            if ($Access == "admin")
                $Html = adminOperators();
                break;
        case "subjects":
            if ($Access == "admin")
                $Html = adminSubjects();
            break;
        case "users":
            if ($Access == "admin")
                $Html = adminUsers();
            break;
        case "profileEdit":
            $Html = profileEdit($id);
            break;
        case "logOut":
            session_destroy();
            $reload = true;
    }
}
else
    $reload = true;
echo json_encode(['reload'=>$reload , 'html'=>$Html ]);
/* dashboard for admin and operator *****************************/
function adminDashboard($Access){
        $quickShowDetails[0] = sqlCPCounter("sliderList")->fetch()['count(*)'];
        $quickShowDetails[1] = sqlCPCounter("newAdvertise")->fetch()['count(*)'];
        $quickShowDetails[2] = sqlCPCounter("activeAdvertise")->fetch()['count(*)'];
        $quickShowDetails[3] = sqlCPDateCounter("userSignUp", date("Y-m-d"))->fetch()['count(*)'];;
        $quickShowDetails[4] = sqlCPCounter("subjects")->fetch()['count(*)'];
        $quickShowDetails[5] = sqlCPCounter("operators")->fetch()['count(*)'];
        $quickShowIcon = ["slider", "speaker", "advertising", "user", "subject", "operator"];
        $quickShowTitle = ["آگهی های در انتظار اسلایدر", "آگهی های جدید", "آگهی های فعال", "کاربران جدید", "دسته بندی ها", "اپراتور های فعال"];
        $counter = ($Access == "admin") ? 6 : 3;
        $CPHtml = "<div id='quickShow'>";
        for ($i = 0; $i < $counter; $i++) {
            $CPHtml .= "
        <div class='quickShowBox'>
            <div class='quickShowIcon'>" . file_get_contents('./../Icon/' . $quickShowIcon[$i] . '.svg') . "</div>
            <div class='quickShowText'>
                <div class='quickShowTitle'>$quickShowTitle[$i]</div>
                <div class='quickShowDetails'>" . $quickShowDetails[$i] . "</div>
            </div>
        </div> ";
        }
        $CPHtml .= "</div>
    <div id='userChart'>
    <div id='chartTitle'>نمودار تعداد آگهی ها در هفت روز گذشته</div>
    <div id='chartRight'>
        <div id='chartShape'>
            <div id='showValue'></div>
            <svg width='100%' height='100%'>";
        for ($i = 56; $i <= 256; $i = $i + 50) $CPHtml .= "<line x1='0' x2='100%' y1='$i' y2='$i' stroke='var(--fore3)'></line>";
        $CPHtml .= "<polyline id='chartLine' points='' fill='none' stroke-width='3px'></polyline>";
        for ($i = 6; $i >= 0; $i--) $CPHtml .= "<circle class='circle' r='4' cx='0' cy='0' data-value = '" . sqlCPDateCounter("advertiseChart", date("Y-m-d", strtotime("-$i day")))->fetch()['count(*)'] . "'></circle>";
        $CPHtml .= "</svg>
        </div>
        <div id='shapeTitleBox'>";
        for ($i = 0; $i < 7; $i++) $CPHtml .= "<div class='shapeTitle'>" . jdate("m/d", strtotime("-$i day")) . "</div>";
        $CPHtml .= "  </div>
    </div>
    <div id='chartLeft'></div>
    </div>";
    return $CPHtml;
}
/*advertise for admin and operator **************************************/
function adminAdvertise(){
        $stm = sqlCPAdmin('allAdvertise');
        $CPHtml = "<table><thead><tr><th>نام کتاب</th><th>تعداد بازدید</th><th>تاریخ ثبت</th><th>قیمت</th><th>دسته بندی</th><th>وضعیت</th><th>فعالیت</th></tr></thead><tbody>";
        while ($row = $stm->fetch()):
            $bookId = $row['B_Id'];
            $bookName = $row['B_Name'];
            $bookPrice = tr_num($row['B_Price'], 'fa');
            if ($row['B_Status'] == "new") {
                $bookStatus = "درحال بررسی";
                $bookStatusColor = "#0000cc";
            } elseif ($row['B_Status'] == "accept") {
                $bookStatus = "تایید شده";
                $bookStatusColor = "#008800";
            } else {
                $bookStatus = "تایید نشده";
                $bookStatusColor = "#cc0000";
            }
            if ($row['B_Visible']) {
                $B_Visible = "فعال";
                $B_VisibleColor = "#008800";
            } else {
                $B_Visible = "غیرفعال";
                $B_VisibleColor = "#cc0000";
            }
            $bookSubject = $row['S_Name'];
            $bookDate = tr_num(dateCompare($row['B_Date'], false), 'fa');
            $bookView = tr_num($row['B_View'], 'fa');
            $CPHtml .= "<tr class='tableLink' data-id='$bookId' ><td>$bookName</td><td>$bookView</td><td>$bookDate</td><td>$bookPrice</td><td>$bookSubject</td><td style='color:$bookStatusColor ;' >$bookStatus</td><td style='color:$B_VisibleColor ;' >$B_Visible</td></tr>";
        endwhile;
        $CPHtml .= "</tbody></table>";
        return $CPHtml;
}
/* slides for admin and operator***************************************/
function adminSlides(){
    $stm = sqlQuery("sliderBooks");
    $inSlider = [];
    $counter = 0;
    while( $row = $stm->fetch() ) {
        $inSlider[$counter]= $row['B_Id'];
        $counter+=1;
    }
    $stm = sqlCPAdmin('allSlide');
    $CPHtml="<table><thead><tr><th>نام کتاب</th><th>تاریخ درخواست اسلاید</th><th>قیمت</th><th>دسته بندی</th><th>وضعیت</th></tr></thead><tbody>";
    while( $row = $stm->fetch() ):
        $bookId = $row['B_Id'];
        $bookName = $row['B_Name'];
        $bookPrice = tr_num($row['B_Price'],'fa');
        $bookStatus = "درانتظار";
        $bookStatusColor = "#cc0000";
        for ($i = 0;$i < $counter;$i++){
            if ($bookId == $inSlider[$i]){
                $bookStatus = "درحال نمایش";
                $bookStatusColor = "#008800";
            }
        }
        $bookSubject = $row['S_Name'];
        $bookDate = tr_num(dateCompare($row['Slide_Date'],false),'fa');
        $CPHtml .="<tr class='tableLink' data-id='$bookId'><td>$bookName</td><td>$bookDate</td><td>$bookPrice</td><td>$bookSubject</td><td style='color:$bookStatusColor ;' >$bookStatus</td></tr>";
    endwhile;
    $CPHtml .="</tbody></table>";
    return $CPHtml;
}
/****************************** operators for admin ****************************************************/
function adminOperators(){
    $stm = sqlCPAdmin('allOperators');
    $CPHtml="<table><thead><tr><th>نام اپراتور</th><th>آدرس ایمیل</th><th>تلفن همراه</th><th>وضعیت</th><th></th><th></th></tr></thead><tbody>";
    while( $row = $stm->fetch() ):
        $operatorId = $row['U_Id'];
        $operatorName = $row['U_Name'];
        $operatorEmail = $row['Email'];
        $operatorPhone = $row['U_Phone'];
        if ($row['U_Active'] == "1"){
            $operatorActive = "فعال";
            $operatorActiveColor = "#008800";
        }
        else {
            $operatorActive = "غیرفعال";
            $operatorActiveColor = "#cc0000";
        }
        $B_ActiveBtn = ($row['U_Active']) ? "غیرفعال سازی" : "فعال سازی";
        $CPHtml .="<tr><td>$operatorName</td><td>$operatorEmail</td><td>$operatorPhone</td><td style='color:$operatorActiveColor ;' >$operatorActive</td><td style='max-width: 50px'><div data-id='$operatorId' data-action = 'deActive' class='Btn adminTableBtn'>$B_ActiveBtn</div></td><td style='max-width: 50px'><div data-id='$operatorId' data-action = 'remove' class='Btn adminTableBtn'>حذف</div></td></tr>";
    endwhile;
    $CPHtml .="</tbody></table>";
    $CPHtml .="<div id='newOperator' class='Btn pageBtn'>اپراتور جدید</div>";
    return $CPHtml;
}
/******************************** subjects for admin ****************************************************/
function adminSubjects(){
    $stm = sqlCPAdmin('allSubjects');
    $CPHtml="<table><thead><tr><th>نام دسته بندی</th><th>تعداد آگهی های دسته بندی</th><th>وضعیت</th><th></th><th></th></tr></thead><tbody>";
    while( $row = $stm->fetch() ):
        $SubjectId = $row['S_Id'];
        $SubjectName = $row['S_Name'];
        $SubjectCountBook = sqlCPCountById("subjectCountBook",$SubjectId)->fetch()['count(*)'];
        if ($row['S_Active'] == "1"){
            $SubjectActive = "فعال";
            $SubjectActiveColor = "#008800";
        }
        else {
            $SubjectActive = "غیرفعال";
            $SubjectActiveColor = "#cc0000";
        }
        $B_ActiveBtn = ($row['S_Active']) ? "غیرفعال سازی" : "فعال سازی";
        $CPHtml .="<tr><td>$SubjectName</td><td>$SubjectCountBook</td><td style='color:$SubjectActiveColor ;' >$SubjectActive</td><td style='max-width: 50px'><div data-id='$SubjectId' data-action = 'deActive' class='Btn adminTableBtn'>$B_ActiveBtn</div></td><td style='max-width: 50px'><div data-id='$SubjectId' data-action = 'remove' class='Btn adminTableBtn'>حذف</div></td></tr>";
    endwhile;
    $CPHtml .="</tbody></table>";
    $CPHtml .="<div id='newSubjectBox'><input id='newSubject' type='text' maxlength='50' placeholder='نام دسته بندی جدید'/><div id='newSubjectBtn' class='Btn pageBtn'>ثبت دسته بندی جدید</div></div>";
    return $CPHtml;
}
/********************************** users for admin *******************************************/
function adminUsers(){
    $stm = sqlCPAdmin('allUsers');
    $CPHtml="<table><thead><tr><th>نام کاربر</th><th>آدرس ایمیل</th><th>تلفن همراه</th><th>تعداد آگهی ها</th><th>وضعیت</th><th></th><th></th></tr></thead><tbody>";
    while( $row = $stm->fetch() ):
        $userId = $row['U_Id'];
        $userName = $row['U_Name'];
        $userEmail = $row['Email'];
        $userPhone = $row['U_Phone'];
        $userCountBook = sqlCPCountById("userCountBook",$userId)->fetch()['count(*)'];
        if ($row['U_Active'] == "1"){
            $userActive = "فعال";
            $userActiveColor = "#008800";
        }
        else {
            $userActive = "غیرفعال";
            $userActiveColor = "#cc0000";
        }
        $B_ActiveBtn = ($row['U_Active']) ? "غیرفعال سازی" : "فعال سازی";
        $CPHtml .="<tr data-id='$userId'><td>$userName</td><td>$userEmail</td><td>$userPhone</td><td>$userCountBook</td><td style='color:$userActiveColor ;' >$userActive</td><td style='max-width: 50px'><div data-id='$userId' data-action = 'deActive' class='Btn adminTableBtn'>$B_ActiveBtn</div></td><td style='max-width: 50px'><div data-id='$userId' data-action = 'remove' class='Btn adminTableBtn'>حذف</div></td></tr>";
    endwhile;
    $CPHtml .="</tbody></table>";
    return $CPHtml;
}
/* user **************************************************************/
/* advertise for user *****************************************/
function userAdvertise($id){
    $stm = sqlCPUser('userAllAdvertise',$id);
    $CPHtml = "";
    while( $row = $stm->fetch() ):
        $bookId = $row['B_Id'];
        ($row['B_Img']!="")? $bookImg = "../Image/".$row['B_Img']: $bookImg = "../Icon/no_img.png";
        $bookName = $row['B_Name'];
        $bookPrice = tr_num($row['B_Price'],'fa');
        if($row['B_Status']=="new") {
            $bookStatus = "درحال بررسی";
            $bookStatusColor = "#0000cc";
        }
        elseif ($row['B_Status']=="accept") {
            $bookStatus = "تایید شده";
            $bookStatusColor = "#00cc00";
        }
        else {
            $bookStatus = "تایید نشده";
            $bookStatusColor = "#cc0000";
        }
        $bookSubject = $row['S_Name'];
        $bookDate = tr_num(dateCompare($row['B_Date'],false),'fa');
        $bookView = tr_num($row['B_View'],'fa');
        $CPHtml.="  <div class='book' data-id='$bookId'>
                    <div class='bookImage' Style='background-Image: url($bookImg);'></div>
                    <div class='bookDetails'>
                        <h2 class='bookName'>عنوان: $bookName</h2>
                        <div class='bookPrice'>قیمت: $bookPrice</div>
                        <div style='color: $bookStatusColor;' class='bookStatus' >وضعیت: $bookStatus</div>
                        <div class='bookSubject'>دسته بندی: $bookSubject</div>
                        <div class='bookDate'>تاریخ ثبت آگهی: $bookDate</div>
                        <div class='bookView'>تعداد بازدید: $bookView</div>
                    </div>
                </div>";
    endwhile;
    if ($CPHtml=="")
        $CPHtml = "شما هیچ آگهی ثبت نکرده اید!!";
    return "<div id='bookBox'>".$CPHtml."</div>";
}
/* slide for user ***************************************/
function userSlides($id){
    $stm = sqlQuery("sliderBooks");
    $inSlider = [];
    $counter = 0;
    while( $row = $stm->fetch() ) {
        $inSlider[$counter]= $row['B_Id'];
        $counter+=1;
    }
    $stm = sqlCPUser('userAllSlide',$id);
    $CPHtml = "";
    while( $row = $stm->fetch() ):
        $bookId = $row['B_Id'];
        ($row['B_Img']!="")? $bookImg = "../Image/".$row['B_Img']: $bookImg = "../Icon/no_img.png";
        $bookName = $row['B_Name'];
        $bookPrice = tr_num($row['B_Price'],'fa');
        $bookStatus = "درانتظار";
        $bookStatusColor = "#cc0000";
        for ($i = 0;$i < $counter;$i++){
            if ($bookId == $inSlider[$i]){
                $bookStatus = "درحال نمایش";
                $bookStatusColor = "#00cc00";
            }
        }
        $bookSubject = $row['S_Name'];
        $bookDate = tr_num(dateCompare($row['Slide_Date'],false),'fa');
        $CPHtml.="  <div class='book' data-id='$bookId'>
                        <div class='bookImage' Style='background-Image: url($bookImg);'></div>
                        <div class='bookDetails'>
                            <h2 class='bookName'>عنوان: $bookName</h2>
                            <div class='bookPrice'>قیمت: $bookPrice</div>
                            <div style='color: $bookStatusColor;' class='bookStatus' >وضعیت: $bookStatus</div>
                            <div class='bookSubject'>دسته بندی: $bookSubject</div>
                            <div class='bookDate'>تاریخ درخواست اسلاید: $bookDate</div>
                        </div>
                     </div>";
    endwhile;
    if ($CPHtml=="")
        $CPHtml = "شما هیچ آگهی در اسلایدر ندارید!!";
    return "<div id='bookBox'>".$CPHtml."</div>";
}
function profileEdit($id){
    $row = sqlUserDetails($id)->fetch();
    $userName = $row['U_Name'];
    $userEmail = $row['Email'];
    $userPhone = $row['U_Phone'];
    $CPHtml = "<div id='userEdit'>
    <div id='userData'>
        <div class='userInformation'><span>نام : </span><div id='userName'>$userName</div></div>
        <div class='userInformation'><span>ایمیل : </span><div id='userEmail'>$userEmail</div></div>
        <div class='userInformation'><span>شماره تلفن : </span><div id='userPhone'>$userPhone</div></div>
    </div>
    <div id='userNewData'>
        <input id='userNewName' type='text' placeholder='نام جدید'/>
        <input id='userNewEmail' type='email' placeholder='ایمیل جدید'/>
        <input id='userNewPhone' type='text' maxlength='11' placeholder='تلفن جدید'/>
        <input id='userOldPass' type='password' maxlength='8' placeholder='رمز قبلی'/>
        <input id='userNewPass' type='password' maxlength='8' placeholder='رمز جدید'/>
        <input id='userNewRPass' type='password' placeholder='تکرار رمز جدید'/>
        <div id='userEditBtn' class='Btn'>ثبت</div>
    </div>
</div>";
    return $CPHtml;
}