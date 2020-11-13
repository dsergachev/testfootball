<div style="text-align: center; clear:both">
    <form action="index.php" method="get">
        <input type="hidden" name="a" value="run/playoff">
        <input type="hidden" name="p" value="0">
        <input type="submit" value="Запустить 1/8 финала">
    </form>
</div>
<?php for($i=0 ; $i<8 ; $i++):?>
<div style="width:25%;height:210px;border:1px solid grey; padding:0px 10px 0px 10px; margin:10px; float:left;">
<p style="line-height: 20px;height:25px;font-size:18px;background-color: #eee;">Группа <?=$i?></p>    
<table>
    <thead>
        <th>Команда</th>  
        <th>И</th>  
        <th>В</th>  
        <th>Н</th>  
        <th>П</th>  
        <th>Голы</th>  
        <th>+/-</th>  
        <th>О</th>  
    </thead>
<?php for($j=0 ; $j<4 ; $j++):?>
    
    <tr <?=($j==0||$j==1?'style="background-color:green"':'')?>>
        <td><?=$result[$i*4+$j]['teamname']?></td>
        <td>3</td>
        <td><?=$result[$i*4+$j]['totalwin']?></td>
        <td><?=$result[$i*4+$j]['totaldraw']?></td>
        <td><?=$result[$i*4+$j]['totallose']?></td>
        <td><?=$result[$i*4+$j]['totalscorred']?> - <?=$result[$i*4+$j]['totalmissed']?></td>
        <td><?=($result[$i*4+$j]['resultgoals']>0?"+":"").$result[$i*4+$j]['resultgoals']?></td>
        <td><?=$result[$i*4+$j]['resultpoint']?></td>
    </tr>

<?php endfor;?>
</table>   
</div>

<?php endfor;?>

