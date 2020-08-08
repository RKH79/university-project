/*************************all public variable**************************************************/
let cat = "all";
let cun = 0;
let num = 10;
let searchString = "";
let Ajax = new XMLHttpRequest();
/************************All public select element**********************************************/
let catList = document.querySelectorAll(".subject");
let moreBtn = document.getElementById("moreBtn");
let modal = document.getElementById('modal');
let profile = document.getElementById("profile");
let modalLoader = document.getElementById("modalLoader");
let modalText = document.getElementById("modalText");
let modalTitle = document.getElementById("modalTitle");
/**************************first load**********************************************/
if (document.getElementById("main") != null) 
    load("loadBook", "yes", "yes");
/************************Load book for all**************************************/
function load(action,clear,btn) {
    Ajax.open("POST", "Ajax/loadBook.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if (action === "loadBook")
        Ajax.send("counter=" + cun + "&category=" + cat + "&number=" + num + "&action=" + action );
    else if(action === "search") {
        Ajax.send("searchString=" + searchString + "&category=all" + "&action=" + action);
    }
    cun = cun + num ;
    if (Ajax.readyState === 1) {
        document.getElementById("moreLoader").style.display='block';
        moreBtn.style.display='none';
        if(clear === "yes")
            document.getElementById("main").innerHTML = "";
    }
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            document.getElementById("moreLoader").style.display='none';
            let userData = JSON.parse(this.responseText);
            if(btn==="yes")
                moreBtn.style.display='';
            document.getElementById("main").innerHTML += userData['html'];
            modalBook();
            if (userData['HasEnded']<=cun)
                moreBtn.style.display='none';
        }
    };
}
/********************************clear book****************************************/
function clear() {
    cun = 0;
    searchString = "";
}
/*************************load subject book***************************************/
if (catList!==null) {
    catList.forEach(function (c) {
        c.onclick = function (event) {
            event.preventDefault();
            catList.forEach(function (s) {
                s.classList.remove("active");
            });
            c.classList.add("active");
            cat = c.dataset['val'];
            clear();
            load("loadBook", "yes", "yes");
        };
    });
}
/*********************************load more book**************************************************/
if (moreBtn!==null) {
    moreBtn.onclick = function () {
        load("loadBook", "no", "yes");
    };
}
/**********************************number book in page for load****************************************************/
if (document.getElementById("numberInPage")!==null) {
    document.getElementById("numberInPage").onchange = function () {
        num = Number(this.options[this.selectedIndex].value);
        clear();
        load("loadBook", "yes", "yes");
    };
}
/********************************** Slider **********************************************/
if (document.getElementById("slider")!==null && document.getElementsByClassName("slide").length!=0) {
    let slideIndex = 0;
    let slides = document.getElementsByClassName("slide");
    let dots = document.getElementsByClassName("dot");
    for (let i = 0; i < slides.length; i++)
        document.getElementById("slideDot").innerHTML += "<span class=\"dot\" data-id =" + i + "></span> ";
    Slider();
    function Slider() {
        for (let j = 0; j < slides.length; j++) {
            slides[j].style.display = "none";
            dots[j].classList.remove("activeDot");
        }
        slideIndex++;
        if (slideIndex > slides.length)
            slideIndex = 1;
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " activeDot";
        setTimeout(Slider, 5000);
    }
}
/********************************** search for book **************************************/
if (document.getElementById("search")!==null) {
    let search = document.getElementById("search");
    search.onkeypress = function (e) {
        if (e.key === 'Enter') {
            catList.forEach(function (s) {
                s.classList.remove("active");
            });
            catList[0].classList.add("active");//return subject to all
            if (search.value !== "") {
                clear();
                searchString = search.value;

                load("search", "yes", "no");
            } else {
                cat = "all";
                clear();
                load("loadBook", "yes", "yes");
            }
        }
    };
}
/******************load modal for Link ***************************/
document.querySelectorAll(".linkModal").forEach(function (l) {
    l.onclick = function (event) {
        event.preventDefault();
        loadModal(Number(l.dataset['id']), l.valueOf().text, "modalLink");
    }
});
/******************load modal for Book ***************************/
function modalBook() {
    document.querySelectorAll(".book").forEach(function (b) {
        b.addEventListener("click",function (event) {
            event.preventDefault();
            let title = b.children[1].innerHTML;
            loadModal(Number(b.dataset['id']), title , "modalBook");
        });
    });
}
/******************load modal for slider book *******************/
if (document.querySelectorAll(".slideButton")!==null) {
    document.querySelectorAll(".slideButton").forEach(function (b) {
        b.addEventListener("click", function (event) {
            event.preventDefault();
            let title = b.parentElement.children[0].innerHTML ;
            loadModal(Number(b.dataset['id']), title , "modalBook");
        })
    })
}
/******************load modal for sign up form**************************/
document.getElementById("newAdvertise").onclick = function () {
    if (profile.dataset['login']==="Yes")
        loadModal(0, "ثبت آگهی جدید", "newAdvertise");
    else
        loadModal(0, "ثبت نام", "modalSignUp");
};
/******************load modal for LogIn form**************************/
profile.onclick = function () {
    if (profile.dataset['login']==="Yes")
        window.location = "Pages/ControlPanel.php";
    else
        loadModal(0, "ورود", "modalLogIn");
};
/******************LogIn SignUp Switch**************************/
function LogInSignUpSW(){
    if (document.getElementById("FormLogInBtn")!==null) {
        document.getElementById("FormLogInBtn").onclick = function () {
            loadModal(0, "ورود", "modalLogIn");
        };
    }
    else if (document.getElementById("FormSignUpBtn")!==null) {
        document.getElementById("FormSignUpBtn").onclick = function () {
            loadModal(0, "ثبت نام", "modalSignUp");
        };
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
/***********************clear modal**********************************/
function modalClear() {
    modalTitle.innerHTML = "";
    modalText.innerHTML = "";
}
/*******************************load all modal************************************/
function loadModal(val,title,action) {
    Ajax.open("POST", "Ajax/modal.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    Ajax.send("val=" + val + "&act=" +action);
    if (Ajax.readyState === 1) {
        modalClear();
        modalTitle.innerHTML = title;
        modalLoader.style.display='block';
    }
    modal.style.display = "block";
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            modalLoader.style.display='none';
            let modalData = JSON.parse(this.responseText);
            modalText.innerHTML = modalData['text'];
            LogInSignUpSW();
            LogInSignUp();
            reloadNewAdvertise();

        }
    }
}
/***********************************Ajax for all user manage except upload image ****************************************/
function userManageAjax(data) {
    Ajax.open("POST", "Ajax/userManage.php", true);
    Ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    Ajax.send(data);
    if (Ajax.readyState === 1) {
        modalLoader.style.display='block';
    }
    Ajax.onreadystatechange = function() {
        if (Ajax.readyState === 4 && Ajax.status === 200) {
            console.log(this.responseText);
            let SignUpNote = JSON.parse(this.responseText);
            if (SignUpNote['reload'])
                window.location = "";
            modalLoader.style.display='none';
            document.getElementById("requestNote").innerHTML = SignUpNote['text'];
            LogInSignUpSW();
            LogInSignUp();
        }
    }
}
/**********************************LogIn SignUp**************************************/
function LogInSignUp() {
    /*************LogIn*********/
    if (document.getElementById("LogInBtn") !== null) {
        let message = "";
        let check = true;
        let LogInEmail = document.getElementById("LogInEmail");
        let LogInPass = document.getElementById("LogInPass");
        document.getElementById("LogInBtn").onclick = function () {
            LogInEmail.style.borderColor = LogInPass.style.borderColor = "var(--fore3)";
            if(LogInEmail.value===null){//email check
                check = false;
                message +="ایمیل را وارد کنید / ";
                LogInEmail.style.borderColor = "var(--mainColor)";
            }else if(!/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i.test(LogInEmail.value)){
                check = false;
                message +="ایمیل وارد شده معتبر نیست / ";
                LogInEmail.style.borderColor = "var(--mainColor)";
            }
            if (LogInPass.value === ""){//password check
                check = false;
                message += "رمز ورود را وارد کنید / ";
                LogInPass.style.borderColor = "var(--mainColor)";
            }else if (!/^[_a-z0-9-]{4,8}$/.test(LogInPass.value)){
                check = false;
                message +="رمز ورود باید بیشتر از 4 کاراکتر باشد / ";
                LogInPass.style.borderColor = "var(--mainColor)";
            }

            if (check === true)
                userManageAjax("act=logIn&e=" + LogInEmail.value +"&p=" + LogInPass.value);
            else {
                alert(message);
                check = true;
                message = "";
            }
        }
    }
    /**************SignUp*******/
    if(document.getElementById("SignUpBtn") !== null){
        let SignUpName = document.getElementById("SignUpName");
        let SignUpEmail = document.getElementById("SignUpEmail");
        let SignUpPhone = document.getElementById("SignUpPhone");
        let SignUpPass = document.getElementById("SignUpPass");
        let SignUpRPass = document.getElementById("SignUpRPass");
        document.getElementById("SignUpBtn").onclick = function () {
            let check = true;
            let message = "";
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

            if (check === true)
                userManageAjax("act=signUp&e=" + SignUpEmail.value + "&ph=" + SignUpPhone.value + "&p=" + SignUpPass.value + "&n=" + SignUpName.value);
            else
                alert(message);
        }
    }
}
/**************************newAdvertise**************************/
function reloadNewAdvertise() {
    if (document.getElementById('newAdvertiseBookImage')!=null) {
        let imageUrl = "";
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
            Ajax.open('POST', 'Ajax/userManage.php', true);
            Ajax.upload.onprogress = function (e) {
                if (e.lengthComputable) {
                    let percentage = Math.round((e.loaded / e.total) * 100);
                    imageUploadBtn.innerHTML = 'Upload ' + percentage + '%';
                }
            };
            Ajax.onload = function () {
                if (Ajax.status === 200) {
                    imageUploadBtn.innerHTML = 'آپلود تصویر';
                    response.classList.add("returnResponse");
                    response.style.backgroundImage = "url(Image/"+Ajax.response+")";
                    imageUrl = Ajax.response;
                }
            };
            Ajax.send(formData);
        };
        /* insert to database*/
        document.getElementById("newAdvertiseSubmitBtn").onclick = function () {
            let newAdvertiseName = document.getElementById("newAdvertiseBookName").value;
            let newAdvertiseWriter = document.getElementById("newAdvertiseBookWriter").value;
            let newAdvertisePrice = document.getElementById("newAdvertiseBookPrice").value;
            let newAdvertiseDetails = document.getElementById("newAdvertiseBookDetails").value;
            let newAdvertiseSubject = document.getElementById("newAdvertiseBookSubject");
            let newAdvertiseSubjectVal = Number(newAdvertiseSubject.options[newAdvertiseSubject.selectedIndex].value);
            userManageAjax("act=newAdvertise&Name=" + newAdvertiseName + "&Writer=" + newAdvertiseWriter + "&Price=" + newAdvertisePrice + "&Details=" + newAdvertiseDetails + "&Image=" + imageUrl + "&Subject=" + newAdvertiseSubjectVal );
        }
    }
}
