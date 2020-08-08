/*************************all public variable**************************************************/
let topPoint = 0;
let Ajax = new XMLHttpRequest();
/************************All public select element**********************************************/
let modalLoader = document.getElementById("modalLoader");
let pageLoader = document.getElementById("pageLoader");
let RightMenu = document.querySelectorAll(".OptionBox");
let modal = document.getElementById('modal');
let page = RightMenu[0].dataset['btn'];
/**************************first load**********************************************/
LoadMenu(page);
RightMenu[0].classList.add("selectOption");
/******************right menu selected item********************/
RightMenu.forEach(function (e) {
    e.onclick = function (event) {
        event.preventDefault();
        RightMenu.forEach(function (s) {
            s.classList.remove("selectOption");
        });
        e.classList.add("selectOption");
        if (e.dataset['btn'] === "goBack")
            window.location = "../?B=true";
        else {
            LoadMenu(e.dataset['btn']);
            page = e.dataset['btn'];
        }
    }
});
function LoadMenu(page) {
    Ajax.open("POST", "../Ajax/CPPages.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    Ajax.send("page="+page );
    if (Ajax.readyState === 1) {
        document.getElementById("main").innerHTML = "";
        pageLoader.style.display = "block";
    }
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            pageLoader.style.display = "none";
            let data = JSON.parse(this.responseText);
            if (data['reload'] === true)
                window.location.assign("../");
            document.getElementById("main").innerHTML = data['html'];
            if (page === "dashboard") {
                setColor();
                chart();
                leftNumber();
                showValue();
            }
            else if (page === "advertise"||page === "slides") {
                tableModal();
                BookBoxModal();
            }
            else if (page === "subjects"||page === "operators"|| page === "users"){
                newSubject();
                newOperator();
                adminTableBtn();
            }
            else if (page === "profileEdit")
                userEditProfile();
        }
    }
}
/*************** show menu *********************/
let sidebar = document.getElementById("sidebar");
let main = document.getElementsByTagName("main")[0];
document.getElementById("showSidebar").onclick = function () {
    if (main.style.marginLeft !== "-200px") {
        sidebar.style.marginRight = "0px";
        main.style.marginLeft = "-200px";
    }
    else{
        sidebar.style.marginRight = "";
        main.style.marginLeft = "";
    }
};
/***************** color of quick show ********************/
function setColor() {
    let backgroundColor = ["#00c0ef", "#00A65A", "#ffca28", "#DD4B39", "#9c27b0", "#ff5722"];
    let iconColor = ["#00A3CB", "#008D4D", "#ffb300", "#BC4031", "#7b1fa2", "#e64a19"];
    let quickShowBox = document.getElementsByClassName("quickShowBox");
    for (let i = 0; i < quickShowBox.length; i++) {
        document.getElementsByClassName("quickShowBox")[i].style.backgroundColor = backgroundColor[i];
        document.getElementsByClassName("quickShowIcon")[i].style.fill = iconColor[i];
    }
}
/*********************** chart ****************************/
function chart() {
    if (document.getElementById("chartShape") != null) {
        let circle = document.getElementsByClassName("circle");
        let chartShapeWidth = Number(window.getComputedStyle(document.getElementById("chartShape")).width.replace("px", ""));
        chartShapeWidth = (chartShapeWidth - 10) / 6;
        let pointHeight = [];
        let different = 1;
        let pointString = "";
        for (let i = 0; i < 7; i++) {
            pointHeight[i] = Number(circle[i].attributes['data-value'].value);
            if (pointHeight[i] > topPoint) {
                topPoint = pointHeight[i];
            }
        }
        if (topPoint !== 0)
            different = 200 / topPoint;
        for (let i = 0; i < 7; i++) {
            circle[i].attributes["cx"].value = (chartShapeWidth * i) + 5;
            circle[i].attributes["cy"].value = (256 - (pointHeight[i] * different));
            pointString += (chartShapeWidth * i) + 5 + "," + (256 - (pointHeight[i] * different)) + " ";
        }
        document.getElementById("chartLine").attributes["points"].value = pointString;
    }
}
function leftNumber(){
    if (document.getElementById("chartLeft") != null) {
        let leftNumberDifferent = topPoint / 4;
        let leftNumber = [topPoint, leftNumberDifferent * 3, leftNumberDifferent * 2, leftNumberDifferent, 0];
        let chartLeft = document.getElementById("chartLeft");
        if (topPoint > 5) {
            for (let i = 0; i < 5; i++)
                chartLeft.innerHTML += "<span>" + Math.round(leftNumber[i]) + "</span>";
        } else {
            for (let i = 0; i < 5; i++)
                chartLeft.innerHTML += "<span>" + leftNumber[i].toFixed(1) + "</span>";
        }
    }
}
/*************************** on window resize *******************************/
window.onresize = function(){
    chart();
    setTimeout(chart,400);
    sidebar.style.marginRight = "";
    main.style.marginLeft = "";
};
/******************** muse move on circles showValue *************************/
function showValue() {
    let showValue = document.getElementById("showValue");
    let circle = document.querySelectorAll(".circle");
    circle.forEach(function (e) {
        e.onmouseenter = function (even) {
            showValue.style.top = even.pageY - 85 + "px";
            showValue.style.left = even.pageX - 25 + "px";
            showValue.innerHTML = e.dataset["value"];
            showValue.style.opacity = "1";
        };
        e.onmouseout = function () {
            showValue.style.opacity = "0";
        };
    });
}
/******************************* modal for tables admin ******************************/

function tableModal() {
    let tableLink = document.querySelectorAll(".tableLink");
    tableLink.forEach(function (e) {
        e.onclick = function () {
            let title = e.children[0].innerHTML;
            loadModal("BookId=" + e.dataset['id'] + "&page=" + page,title)
        }
    })
}
/*********************** modal for Book Box user**********************************/
function BookBoxModal() {
    let books = document.querySelectorAll(".book");
    books.forEach(function (e) {
        e.onclick = function () {
            let title = e.children[1].children[0].innerHTML;
            loadModal("BookId=" + e.dataset['id'] + "&page=" + page,title)
        }
    })
}
/***********************clear modal**********************************/
function modalClear() {
    modalTitle.innerHTML = "";
    modalText.innerHTML = "";
}
/*******************************load all modal************************************/
function loadModal(data,title) {
    Ajax.open("POST", "../Ajax/CPModal.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    Ajax.send(data);
    if (Ajax.readyState === 1) {
        modalClear();
        modalTitle.innerHTML = title;
        modalLoader.style.display='block';
    }
    modal.style.display = "block";
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            modalLoader.style.display='none';
            console.log(this.responseText);
            let modalData = JSON.parse(this.responseText);
            modalText.innerHTML = modalData['text'];
            submitNewOperator();
            modalBtn();
            editUserAdvertise();
            editNewAdvertise();
        }
    }
}
/***********************close modal********************************/
document.getElementById("close").onclick = function () {
    modal.style.display = "none";
};
window.onclick = function (event) {
    if (event.target === modal) {
        modal.style.display = "none";
    }
};
/*********************** ajax to cp edit *************************/
function ajaxCPEdit(data) {
    Ajax.open("POST", "../Ajax/CPEdit.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    Ajax.send(data);
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            modal.style.display = "none";
            LoadMenu(page);
        }
    }
}
/*********************** admin table btn *************************/
function adminTableBtn() {
    document.querySelectorAll(".adminTableBtn").forEach(function (e) {
        e.onclick = function () {
            ajaxCPEdit("id=" + e.dataset['id'] + "&action=" + e.dataset['action'] + "&page=" + page);
        }
    });
}
/*********************** modal btn *****************************/
function modalBtn() {
    document.querySelectorAll(".modalBtn").forEach(function (e) {
        e.onclick = function () {
            let statusNote = "";
            if (document.getElementById("statusDetails") !== null)
                statusNote = "&statusNote=" + document.getElementById("statusDetails").value;
            ajaxCPEdit("id=" + e.dataset['id'] + "&action=" + e.dataset['action'] + "&page=" + page + statusNote);
            modal.style.display = "none";
        }
    });
}
/****************** new subject ********************************/
function newSubject() {
    if (document.getElementById("newSubjectBtn")!=null) {
        document.getElementById("newSubjectBtn").onclick = function () {
            if (document.getElementById("newSubject").value !== "")
                ajaxCPEdit("page=subjects&action=newSubject&subjectName=" + document.getElementById("newSubject").value);
        };
    }
}
/****************** new operator *******************************/
function newOperator() {//load modal
    if (document.getElementById("newOperator")!=null) {
        document.getElementById("newOperator").onclick = function () {
            loadModal("page=newOperator","ثبت نام اپراتور جدید");
        };
    }
}
function submitNewOperator() {
    if(document.getElementById("SignUpOperatorBtn") !== null){
        let check = true;
        let message = "";
        let SignUpName = document.getElementById("SignUpName");
        let SignUpEmail = document.getElementById("SignUpEmail");
        let SignUpPhone = document.getElementById("SignUpPhone");
        let SignUpPass = document.getElementById("SignUpPass");
        let SignUpRPass = document.getElementById("SignUpRPass");
        document.getElementById("SignUpOperatorBtn").onclick = function () {
            SignUpEmail.style.borderColor = SignUpPhone.style.borderColor = SignUpPass.style.borderColor = SignUpRPass.style.borderColor = "var(--fore3)";
            if(SignUpName.value===""){//name check
                check = false;
                message +="نام کاربری خودرا وارد کنید / ";
                SignUpName.style.borderColor = "var(--mainColor)";
            }
            if(SignUpEmail.value===""){//email check
                check = false;
                message +="ایمیل را وارد کنید / ";
                SignUpEmail.style.borderColor = "var(--mainColor)";
            }else if(!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i.test(SignUpEmail.value)){
                check = false;
                message +="ایمیل وارد شده معتبر نیست / ";
                SignUpEmail.style.borderColor = "var(--mainColor)";
            }
            if (SignUpPhone.value===""){//phone check
                check = false;
                message +="شماره تلفن همراه را وارد کنید / ";
                SignUpPhone.style.borderColor = "var(--mainColor)";
            }else if (!/^09[0-9]{9}$/.test(SignUpPhone.value)){
                check = false;
                message += "شماره تلفن همراه وارد شده معتبر نیست / ";
                SignUpPhone.style.borderColor = "var(--mainColor)";
            }
            if (SignUpPass.value === ""||SignUpRPass.value === ""){//password check
                check = false;
                message += "رمز ورود را وارد کنید / ";
                SignUpPass.style.borderColor = SignUpRPass.style.borderColor = "var(--mainColor)";
            }else if (!/^[_a-z0-9-]{4,8}$/.test(SignUpPass.value)){
                check = false;
                message +="رمز ورود باید بیشتر از 4 کاراکتر باشد / ";
                SignUpPass.style.borderColor = SignUpRPass.style.borderColor = "var(--mainColor)";
            }else if (SignUpPass.value!== SignUpRPass.value){
                check = false;
                message +="رمز های ورود متفاوت هستند / ";
                SignUpPass.style.borderColor = SignUpRPass.style.borderColor = "var(--mainColor)";
            }
            if (check === true) {
                ajaxCPEdit("page=operators&action=newOperator&e=" + SignUpEmail.value + "&ph=" + SignUpPhone.value + "&p=" + SignUpPass.value + "&n=" + SignUpName.value);
                modal.style.display = "none";
            }
            else {
                alert(message);
                message = "";
                check = true;
            }
        }
    }
}
/**************** edit user advertise *********************/
function editUserAdvertise() {
    if (document.getElementById("editAdvertise")) {
        document.getElementById("editAdvertise").onclick = function() {
            let Bid = document.getElementById("editAdvertise").dataset['id'];
            let data = "BookId=" + Bid + "&page=editAdvertise";
            loadModal(data, "ویرایش آگهی")
        }
    }
}
function editNewAdvertise() {
    if (document.getElementById('newAdvertiseBookImage')!=null) {
        let imageUrl = document.getElementById("response").dataset['img'] ;
        let imageInput = document.getElementById('newAdvertiseBookImage');
        /* upload image */
        imageInput.onchange = function (event) {
            let imageUploadBtn = document.getElementById('imageUploadBtn');
            let response = document.getElementById('response');
            event.preventDefault();
            let file = imageInput.files[0];
            let formData = new FormData();
            formData.append('file-select', file);
            formData.append('act', "imageUpload");
            if(response.style.backgroundImage!==null)
                formData.append('Del', response.style.backgroundImage);
            let imageAjax = new XMLHttpRequest();
            imageAjax.open('POST', '../Ajax/userManage.php', true);
            imageAjax.send(formData);
            imageAjax.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    let percentage = Math.round((e.loaded / e.total) * 100);
                    imageUploadBtn.innerHTML = 'Upload ' + percentage + '%';
                }
            };
            imageAjax.onload = function () {
                if (imageAjax.status === 200) {
                    imageUploadBtn.innerHTML = 'آپلود تصویر';
                    response.classList.add("returnResponse");
                    response.style.backgroundImage = "url(../Image/"+imageAjax.response+")";
                    imageUrl = imageAjax.response;
                }
            };
        };
        /* insert to database*/
        document.getElementById("newAdvertiseSubmitBtn").onclick = function () {
            let bookId = document.getElementById("newAdvertiseSubmitBtn").dataset['id'];
            let newAdvertiseName = document.getElementById("newAdvertiseBookName").value;
            let newAdvertiseWriter = document.getElementById("newAdvertiseBookWriter").value;
            let newAdvertisePrice = document.getElementById("newAdvertiseBookPrice").value;
            let newAdvertiseDetails = document.getElementById("newAdvertiseBookDetails").value;
            let newAdvertiseSubject = document.getElementById("newAdvertiseBookSubject");
            let newAdvertiseSubjectVal = Number(newAdvertiseSubject.options[newAdvertiseSubject.selectedIndex].value);
            ajaxCPEdit("page=editAdvertise&Name=" + newAdvertiseName + "&Writer=" + newAdvertiseWriter + "&Price=" + newAdvertisePrice + "&Details=" + newAdvertiseDetails + "&Image=" + imageUrl + "&Subject=" + newAdvertiseSubjectVal + "&bookId=" + bookId );
        }
    }
}
/**********************user edit profile************************/
function userEditProfile() {
    if (document.getElementById("userEditBtn") != null){
        document.getElementById("userEditBtn").onclick = function () {
            let check = true;
            let message = "";
            let userNewName = document.getElementById("userNewName").value;
            let userNewEmail = document.getElementById("userNewEmail").value;
            let userNewPhone = document.getElementById("userNewPhone").value;
            let userOldPass = document.getElementById("userOldPass").value;
            let userNewPass = document.getElementById("userNewPass").value;
            let userNewRPass = document.getElementById("userNewRPass").value;
            if(!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i.test(userNewEmail) && userNewEmail !== ""){
                check = false;
                message +="ایمیل وارد شده معتبر نیست / ";
            }
            if (!/^09[0-9]{9}$/.test(userNewPhone) && userNewPhone !== ""){
                check = false;
                message += "شماره تلفن همراه وارد شده معتبر نیست / ";
            }
            if (userNewPass !== userNewRPass && userNewPass !== ""){
                check = false;
                message +="رمز های ورود متفاوت هستند / ";
            }
            else if (!/^[_a-z0-9-]{4,8}$/.test(userNewPass) && userNewPass !== ""){
                check = false;
                message +="رمز ورود باید بیشتر از 4 کاراکتر باشد / ";
            }

            if (check === true)
                ajaxCPEdit("page=" + page + "&userNewName=" + userNewName + "&userNewEmail=" + userNewEmail + "&userNewPhone=" + userNewPhone + "&userOldPass=" + userOldPass + "&userNewPass=" + userNewPass ) ;
            else
                alert(message);
        }
    }
}