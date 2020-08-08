<?php
require '../Library/db.php';
if (isset($_POST['act'])) {
    switch ($_POST['act']){
        case "modalLink":
            $html = modalLink();
            break;
        case "modalBook":
            $html = modalBook();
            break;
        case "modalSignUp":
            $html = modalSignUp();
            break;
        case "modalLogIn":
            $html = modalLogIn();
            break;
        case "newAdvertise":
            $html = newAdvertise();
            break;
        default:
            $response = "در بازیابی اطلاعاتی مشکلی وجود دارد!!";
            break;
    }
}
else{
    $response = "در بازیابی اطلاعاتی مشکلی وجود دارد!!";
}
echo json_encode(['text' => $html]);

/******************************/
function modalLink(){
    $stm = sqlModalLink($_POST['val']);
    $row = $stm->fetch();
    $text = $row['M_Text'];
    $html = "<p id='modalNote'>$text</p>";
    return $html;
}

function modalBook(){
    $stm = sqlModalBook($_POST['val']);
    $row = $stm->fetch();
    $B_Details = $row['B_Details'];
    $B_Writer = $row['B_Writer'];
    $B_Price = tr_num($row['B_Price'],'fa');
    $B_Date = tr_num(dateCompare($row['B_Date'],true),'fa');
    $U_Phone = tr_num($row['U_Phone'],'fa');
    if ($row['B_Img']!="")
        $B_Img = "Image/".$row['B_Img'];
    else
        $B_Img = "Icon/no_img.png";
    return <<<h
            <div id="modalBook">
                <div id="modalBookImg" Style=" background-Image: url($B_Img);"></div>
                <div id="modalBookBox">
                    <div id="modalBookDetails" >$B_Details</div>
                    <div id="modalBookWriter" >نویسنده: $B_Writer</div>
                    <div id="modalBookPrice" >قیمت: $B_Price</div>
                    <div id="modalBookDate" >تاریخ ثبت آگهی: $B_Date</div>
                    <div id="modalBookUPhone" >تلفن تماس فروشنده: $U_Phone</div>
                </div>
            </div>
h;
}

function modalSignUp(){
    return <<<h
        <div id="modalUserManageBox">
            <input id="SignUpName" type="text" maxlength="50" placeholder="نام کاربری"/>
            <input id="SignUpEmail" type="email" placeholder="ایمیل"/>
            <input id="SignUpPhone" type="text" maxlength="11" placeholder="تلفن"/>
            <input id="SignUpPass" type="password" maxlength="8" placeholder="رمز ورود"/>
            <input id="SignUpRPass" type="password" placeholder="تکرار رمز ورود"/>
            <div id="SignUpBtn" class="modalBtn">ثبت نام</div>
            <div id="FormLogInBtn" class="FormChangeBtn">ورود</div>
        </div>
        <div id="requestNote"></div>
h;
}

function modalLogIn(){
    return <<<h
        <div id="modalUserManageBox">
             <input id="LogInEmail" type="email" placeholder="ایمیل"/>
             <input id="LogInPass" type="password" placeholder="رمز ورود"/>
             <div id="LogInBtn" class="modalBtn">ورود</div>
             <div id="FormSignUpBtn" class="FormChangeBtn">ثبت نام</div>
        </div>
        <div id="requestNote"></div>
h;
}

function newAdvertise(){
    $html = <<<h
        <div id="modalUserManageBox">
            <input accept=".png,.jpeg,.jpg" id="newAdvertiseBookImage" type="file"/>
            <div id="response"></div>
            <label for="newAdvertiseBookImage" id="imageUploadBtn" class="modalBtn">آپلود تصویر</label>
            <input id="newAdvertiseBookName" type="text" placeholder="نام کتاب"/>
            <input id="newAdvertiseBookWriter" type="text" placeholder="نام نویسنده"/>
            <input id="newAdvertiseBookPrice" type="text" placeholder="قیمت کتاب"/>
            <select id="newAdvertiseBookSubject" aria-label="number" style="width: 94%;height: 32px;">
h;
    $stm = sqlQuery('subject');
    while( $row = $stm->fetch() ):
        $html .= "<option value='".$row['S_Id']."'>".$row['S_Name']."</option>";
    endwhile;
    $html .= <<<h
            </select>
            <textarea id="newAdvertiseBookDetails" placeholder="توضیحات"></textarea>
            <div id="newAdvertiseSubmitBtn" class="modalBtn">ثبت آگهی</div>
        </div>
        <div id="requestNote"></div>
h;
    return $html;
}

