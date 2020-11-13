<?php
use app\models\Country;
?>
<div style="text-align: center; clear:both">
    <form action="index.php" method="GET">
        <input type="hidden" name="a" value="run/groupstage">
        <input type="submit" value="Запустить групповые матчи">
    </form>
</div>

<?php for($i=0 ; $i<8 ; $i++):?>
<div style="width:25%;height:160px;border:1px solid grey; padding:0px 10px 0px 10px; margin:10px; float:left;">
<p style="line-height: 20px;height:25px;font-size:18px;background-color: #eee;">Группа <?=$i?></p>    
<?php for($j=0 ; $j<4 ; $j++):?>

<p style="margin:0;padding:0;line-height: 16px;height:16px;font-size:16px"><?=$GLOBALS['app']->getModel(Country::class)->findOne([['=','id',$groups[$i*4+$j]->country_id]])->name?></p>

<?php endfor;?>
</div>

<?php endfor;?>
