<?php
require '../Library/db.php';
session_start();
if (isset($_POST['page'])&&isset($_SESSION['id'])) {
    $Access = sqlUserAccess($_SESSION['id'])->fetch()['Access'];
    switch ($_POST['page']) {
        case "advertise" :
            $response = modalAdvertise($Access);
            break;
        case "slides":
            $response = modalSlide($Access);
            break;
        case "newOperator":
            if ($Access == "admin")
                $response = newOperator();
            break;
        case "editAdvertise":
            $response = editAdvertise();
            break;
        default:
        $response = "در بازیابی اطلاعاتی مشکلی وجود دارد!!";
        break;
    }
}
else{
    $response = "در بازیابی اطلاعاتی مشکلی وجود دارد!!";
}
echo json_encode(['text' => $response]);

/**********************************************/
function modalAdvertise($Access){
    $bookId = $_POST['BookId'];
    $stm = ($Access == "user")? (sqlCPUserModalBook($bookId,$_SESSION['id'])):(sqlCPAdminModalBook($bookId));
    $row = $stm->fetch();
    $B_Details = $row['B_Details'];
    $B_Writer = $row['B_Writer'];
    $B_Price = tr_num($row['B_Price'],'fa');
    $B_Date = tr_num(dateCompare($row['B_Date'],true),'fa');
    $B_View = tr_num($row['B_View'],'fa');
    $Status_Note = $row['Status_Note'];
    if($row['B_Status']=="new")
        $B_Status = "درحال بررسی";
    elseif ($row['B_Status']=="accept")
        $B_Status = "تایید شده";
    else
        $B_Status = "تایید نشده";
    $B_Img = ($row['B_Img']!="")? "../Image/".$row['B_Img']: "../Icon/no_img.png";
    $S_Name = $row['S_Name'];
    $html = "
            <div id='modalBook'>
                <div id='modalBookImg' Style=' background-Image: url($B_Img);'></div>
                <div id='modalBookBox'>";
                    $html .= ($B_Details != "")?"<div id='modalBookDetails' >$B_Details</div>":"";
                    $html .= "<div id='modalBookWriter' >نویسنده: $B_Writer</div>
                    <div id='modalBookPrice' >قیمت: $B_Price</div>
                    <div id='modalBookDate' >تاریخ ثبت آگهی: $B_Date</div>
                    <div id='modalBookSubject'>دسته بندی: $S_Name</div>
                    <div id='modalBookView'>تعداد بازدید: $B_View</div>
                    <div id='modalBookStatus'>وضعیت آگهی: $B_Status</div>";
                    if ($Access != "user") {
                        $U_Phone = tr_num($row['U_Phone'], 'fa');
                        $U_Name = $row['U_Name'];
                        $Email = $row['Email'];
                        $B_ActiveBtn = ($row['B_Visible']) ? "غیرفعال سازی" : "فعال سازی";
                        $B_Active = ($row['B_Visible']) ? "فعال" : "غیرفعال";
                        $html .= "<div id='modalBookUPhone' >نام فروشنده: $U_Name</div>
                        <div id='modalBookUPhone' >آدرس ایمیل فروشنده: $Email</div>
                        <div id='modalBookUPhone' >تلفن تماس فروشنده: $U_Phone</div>
                        <div id='modalBookActive'>فعالیت: $B_Active</div>
                        <textarea id='statusDetails' placeholder='توضیحات اپراتور'>$Status_Note</textarea>
                        <div id='modalBtnS'>";
                        if($row['B_Status'] == "nonaccept"||$row['B_Status'] == "new")
                            $html .= "<div data-id='$bookId' data-action = 'accept' class='Btn modalBtn'>تایید آگهی</div>";
                        if($row['B_Status'] == "accept"||$row['B_Status'] == "new")
                            $html .= "<div data-id='$bookId' data-action = 'refuse' class='Btn modalBtn'>رد آگهی</div>";
                        $html .= "<div data-id='$bookId' data-action = 'deActive' class='Btn modalBtn'>$B_ActiveBtn</div>";
                    }
                    else {
                        $html .= "<div id='modalBtnS'><div data-id='$bookId' id='editAdvertise' class='Btn'>ویرایش آگهی</div>";
                        if ($row['B_Status']=="accept") {
                            $html .= "<div data-id='$bookId' data-action = 'slideRequest' class='Btn modalBtn'>درخواست اسلایدر</div>";
                        }
                    }
                    $html .= "<div data-id='$bookId' data-action = 'remove' class='Btn modalBtn'>حذف آگهی</div>
                    </div>
                </div>
            </div>";
    return $html;
}
function modalSlide($Access){
    $stm = sqlQuery("sliderBooks");
    $inSlider = [];
    $counter = 0;
    while( $row = $stm->fetch() ) {
        $inSlider[$counter]= $row['B_Id'];
        $counter+=1;
    }
    $bookId = $_POST['BookId'];
    $stm = ($Access == "user")? (sqlCPUserModalSlide($bookId,$_SESSION['id'])):(sqlCPAdminModalSlide($bookId));
    $row = $stm->fetch();
    $B_Details = $row['B_Details'];
    $B_Writer = $row['B_Writer'];
    $B_Price = tr_num($row['B_Price'],'fa');
    $Slide_Date = tr_num(dateCompare($row['Slide_Date'],true),'fa');
    $B_Status = "درانتظار";
    for ($i = 0;$i < 5;$i++)
        if ($bookId == $inSlider[$i])
            $B_Status = "درحال نمایش";
    $B_Img = ($row['B_Img']!="")? "../Image/".$row['B_Img']: "../Icon/no_img.png";
    $S_Name = $row['S_Name'];
    $html = "
            <div id='modalBook'>
                <div id='modalBookImg' Style=' background-Image: url($B_Img);'></div>
                <div id='modalBookBox'>";
                    $html .= ($B_Details != "")?"<div id='modalBookDetails' >$B_Details</div>":"";
                    $html .= "<div id='modalBookWriter' >نویسنده: $B_Writer</div>
                    <div id='modalBookPrice' >قیمت: $B_Price</div>
                    <div id='modalBookDate' >تاریخ درخواست اسلاید: $Slide_Date</div>
                    <div id='modalBookSubject'>دسته بندی: $S_Name</div>
                    <div id='modalBookStatus'>وضعیت آگهی: $B_Status</div>";
                    if($Access != "user") {
                        $U_Phone = tr_num($row['U_Phone'], 'fa');
                        $U_Name = tr_num($row['U_Name'], 'fa');
                        $Email = tr_num($row['Email'], 'fa');
                        $html .= "<div id='modalBookUPhone' >نام فروشنده: $U_Name</div>
                        <div id='modalBookUEmail' >آدرس ایمیل فروشنده: $Email</div>
                        <div id='modalBookUPhone'>تلفن تماس فروشنده: $U_Phone</div>";
                    }
                    $html .="<div data-id='$bookId' data-action = 'remove' class='Btn modalBtn'>حذف از اسلایدر</div>";
                $html .="</div>
            </div>";
    return $html;
}
function newOperator(){
    return <<<h
        <div id="modalUserManageBox">
            <input id="SignUpName" type="text" maxlength="50" placeholder="نام اپراتور"/>
            <input id="SignUpEmail" type="email" placeholder="ایمیل"/>
            <input id="SignUpPhone" type="text" maxlength="11" placeholder="تلفن"/>
            <input id="SignUpPass" type="password" maxlength="8" placeholder="رمز ورود"/>
            <input id="SignUpRPass" type="password" placeholder="تکرار رمز ورود"/>
            <div id="SignUpOperatorBtn" class="Btn" >ثبت نام</div>
        </div>
        <div id="requestNote"></div> 
h;
}

function editAdvertise(){
    $B_Img = "";
    $responseClass = "";
    $bookId = $_POST['BookId'];
    $stm = sqlCPUserModalBookEdit($bookId,$_SESSION['id']);
    $row = $stm->fetch();
    $B_Details = $row['B_Details'];
    $B_Writer = $row['B_Writer'];
    $B_Price = $row['B_Price'];
    if($row['B_Img']!="") {
        $B_Img = $row['B_Img'];
        $responseClass = "returnResponse";
    }
    $S_Id = $row['S_Id'];
    $B_Name = $row['B_Name'];
    $html = <<<h
        <div id="modalUserManageBox">
            <input accept=".png,.jpeg,.jpg" id="newAdvertiseBookImage" type="file"/>
            <div id="response" data-img="$B_Img" class="$responseClass" style="border: none;padding: 0;background-image:url('../Image/$B_Img') "></div>
            <label for="newAdvertiseBookImage" id="imageUploadBtn" class="Btn">آپلود تصویر</label>
            <input id="newAdvertiseBookName" type="text" placeholder="نام کتاب" value="$B_Name"/>
            <input id="newAdvertiseBookWriter" type="text" placeholder="نام نویسنده" value="$B_Writer"/>
            <input id="newAdvertiseBookPrice" type="text" placeholder="قیمت کتاب" value="$B_Price"/>
            <select id="newAdvertiseBookSubject" aria-label="number" style="width: 94%;height: 32px;">
h;
    $stm = sqlQuery('subject');
    while( $row = $stm->fetch() ):
        $select = "";
        if ($row['S_Id'] == $S_Id)
            $select = "selected='selected'";
        $html .= "<option $select value='".$row['S_Id']."'>".$row['S_Name']."</option>";
    endwhile;
    $html .= <<<h
            </select>
            <textarea id="newAdvertiseBookDetails" placeholder="توضیحات">$B_Details</textarea>
            <div data-id="$bookId" id="newAdvertiseSubmitBtn" class="Btn modalBtn">ثبت آگهی</div>
        </div>
        <div id="requestNote"></div>
h;
    return $html;
}
