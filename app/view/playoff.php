<?php
$stages = ['1/8 финала','1/4 финала','полуфинал','финал'];
?>
<h3><?=$stages[$stage]?></h3>
<?php foreach($results as $result):?>
<p style="width:100%;height:50px;border-bottom: 2px solid grey;padding:10px;"><?=$result['hometeam']?> - <?=$result['visitorteam']?> <?=$result['homescore']?>:<?=$result['visitorsscore']?></p>
<?php endforeach; ?>
<div style="text-align: center; clear:both">
    <?php if($stage<3):?>
    <form action="index.php" method="get">
        <input type="hidden" name="a" value="run/playoff">
        <input type="hidden" name="p" value="<?=($stage+1)?>">
        <input type="submit" value="Запустить <?=$stages[$stage+1]?>">
    </form>
    <?php endif;?>
</div>

