{literal}
<style type='text/css'>
    <!--
    .style1 {
	color: #000000;
	font-size: x-large;
    }
    .style2 {font-size: x-large}
    -->
</style>
{/literal}

<p align='center'>
<center>
   <h1>{$language_info1} {$points_cc}).<br />{$language_info2}</h1>
</center>
</p>
<p>&nbsp;</p>
<table width='474' border='1' align='center' cellpadding='2' cellspacing='0'>
<tr>
   <td width='26'>{$language_option}</td>
   <td width='319'>{$language_about}</td>
   <td width='41'>{$language_points}</td>
   <td width='62'>{$language_exchange}</td>
</tr>

{foreach item=seedbonus from=$show_seedbonus}
<form action='seedbonus_exchange.php?id={$seedbonus.id}' method='post'>
<tr>
   <td><h1><center>{$seedbonus.name}</center></h1></td>
   <td><b>{$seedbonus.gb} GB Upload</b><br />{$language_desc}</td>
   <td>{$seedbonus.points}</td>
   <td><input type='submit' name='submit' value='{$language_exchange}!' {if $c < $seedbonus.points} disabled {/if}></td>
</tr>
</form>
{/foreach}

</table>
<p class='style2'><center><h1>{$language_info3}</h1></center></p>
