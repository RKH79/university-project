<?php
require '../Library/db.php';
$html = "";
if (isset($_POST['action'])) {
    if ($_POST['action']=="loadBook")
        $stm = sqlQueryLoadBook($_POST['action'], $_POST['counter'], $_POST['number'], $_POST['category']);
    else if ($_POST['action']=="search")
        $stm = sqlSearch($_POST['searchString']);
    while( $row = $stm->fetch() ):
        $Name = $row['B_Name'] ;
        $Price = tr_num($row['B_Price'],'fa') ;
        $Id = $row['B_Id'] ;
        $Date = tr_num(dateCompare($row['B_Date'],true),'fa');
        if ($row['B_Img']!="")
            $Image = "Image/".$row['B_Img'];
        else 
            $Image = "Icon/no_img.png";
$html .= <<<h
    <a class="book" href="?p=b&id=$Id" data-id="$Id">
        <div class="bookImage" Style="background-Image: url($Image);"></div>
        <h2 class="bookTitle">$Name</h2>
        <div class="bookPrice">$Price</div>
        <div class="bookDate">$Date</div>
    </a>
h;
endwhile;
$stm = sqlQueryLoadBook('count',0,0,$_POST['category']);
$rowL = $stm->fetch();
if($html == ""){
    $html ='<div Style="font-size: 14px;padding:10px">آگهی با این مشخصات وجود ندارد!!</div>';
}

echo json_encode(['html' => $html, 'HasEnded' => $rowL['count(*)']]);
}


