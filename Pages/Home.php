<?php
// onLoadHomepage("autoAdvertiseRemove",0);
// $stm = onLoadHomepage("SelectAutoSlideRemove",0);
// while( $row = $stm->fetch() )
//     onLoadHomepage("autoSlideRemove",$row['B_Id']);
// $sliderCount = onLoadHomepage("countSliderAutoAdd",0)->fetch()['COUNT(*)'];
// $stm = onLoadHomepage("selectListSliderAutoAdd",0);
// while( $row = $stm->fetch() ){
//     if ($sliderCount<5){
//         onLoadHomepage("insertToSliderAutoAdd",$row['B_Id']);
//         $sliderCount++;
//     }
// }
?>
<header>
    <div id="slider" data-time="">
        <?php
        $stm = sqlQuery('sliderBooks');
        while( $row = $stm->fetch() ):
            ?>
            <div class="slide fade">
                <div class="slideOverlay" style="background-image: url('Image/<?= $row['B_Img'] ?>');"></div>
                <div class="slideBox">
                    <div class="slideImage" style="background-image: url('Image/<?= ($row['B_Img'])? $row['B_Img'] : file_get_contents('./Icon/book.svg'); ?>');"></div>
                    <div class="slideTextBox">
                        <h2 class="slideTitle"><?= $row['B_Name'] ?></h2>
                        <div class="slideWriter"><?= $row['B_Writer'] ?></div>
                        <div class="slidePrice"><?= tr_num($row['B_Price'],'fa') ?></div>
                        <div class="slideDate"><?= tr_num(dateCompare($row['B_Date'],true),'fa'); ?></div>
                        <a href="?p=b&id=<?= $row['B_Id'] ?>" class="slideButton Btn" data-id="<?= $row['B_Id'] ?>">نمایش بیشتر</a>
                    </div>
                </div>
            </div>
        <?php endwhile;?>
        <div id="slideDot">
        </div>
    </div>
</header>
<section>
    <div class="search">
        <div class="icon"><?= file_get_contents('./Icon/search.svg'); ?></div>
        <input type="text" id="search" placeholder="جستجو">
    </div>
    <div class="option">
        <select aria-label="number" id="numberInPage" title="تعداد آگهی هایی که هربار نمایش داده میشود!">
            <option value="10" selected="selected">10</option>
            <option value="20">20</option>
            <option value="40">40</option>
            <option value="60">60</option>
            <option value="80">80</option>
        </select>
    </div>
    <div class="subjects">
        <a class="subject active" data-val="all" href="#">همه</a>
        <span class="separator">/</span>
        <?php
        $stm = sqlQuery('subject');
        while( $row = $stm->fetch())
            $rows[$row['S_Id']]=$row['S_Name'];
        foreach($rows as $row => $val) {
            $sub[] = '<a class="subject" data-val="'.$row.'" href="#">' . $val . '</a>';
        }
        echo implode('<span class="separator">/</span>',$sub);
        ?>
    </div>
</section>
<main id="main">
</main>
<div class="loadMore">
    <div class="loader" id="moreLoader"></div>
    <button id="moreBtn" class="Btn">نمایش بیشتر</button>
</div>