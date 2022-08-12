<?php 
include "baseconf.inc";

{
?>
<html>
<head>
<title>Dikovin Lad</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<style type="text/css" media="all">@import "style.css";</style>
<script type="text/javascript" src="script.js"></script>

</head>

<body bgcolor=#FFFFFF>
<font face="MS Sans Serif" size="2">

<?php
//-----------------------    Подключение к базе
$database_desc = mysqli_connect($server, $user, $pass);
if(!$database_desc) { die('<BR>Ошибка: не удается подключиться к MySQL серверу<BR>'); }
if(!mysqli_select_db($database_desc, $basename)) { die('<BR>Ошибка: не удается подключиться к MySQL серверу<BR>'); }
$query = 'SET NAMES utf8';
if (!mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Кодировка может не поодерживаться.<BR>'.mysqli_error($database_desc).'<BR>'); }


//---------------------------------------------------------------------------------
//-------------------------------------------------    ОБРАБОТКА ПАРАМЕТРОВ _POST
//---------------------------------------------------------------------------------

if (isset($_POST['post_act']))
 { // -------------------------------- Запись изменённых имён полей в таблицу	 
	 if ($_POST['post_act']=='settablestruc')
		 {
		 	 $y = 0;
		 	 $last_col = '';
			 foreach ($_POST as $key => $value)
			 	 {
			 	 	 //echo '<br>', $key, '=> ', $value;
			 	 	 $inp_str = ""; 
					 $inp_num = "";
					 $inp_strlen = strlen ($key);
			 	 	 for ($x=0; $x < $inp_strlen; $x++)
						 {
							 if ($key[$x] >= '0' && $key[$x] <= '9') { $inp_num = $inp_num.$key[$x]; }
							 if ($key[$x] >= 'a' && $key[$x] <= 'z') { $inp_str = $inp_str.$key[$x]; }
						 }
					 //echo " ($inp_num - $inp_str)";
					 if ($inp_str == "inpshidden")
					 	 {
					 	 	 // Удаление столбца
					 	 	 if (!isset($_POST['inps'.$inp_num.'_text']))
							 {
								 if ($_POST['inps'.$inp_num.'_hidden'] == 'Номер') { continue; }
								 $query = "ALTER TABLE `".$_POST['tablename']."` DROP COLUMN `".$_POST['inps'.$inp_num.'_hidden'];
								 if (!mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Невозможно удалить столбец.<BR>'.$query.'<BR>'.mysqli_error($database_desc).'<BR>'); }
								 $y++;
							 }
					 	 	 else //Запись изменений имени/типа столбца
							 {
									$query = "ALTER TABLE `".$_POST['tablename']."` CHANGE COLUMN `".$_POST['inps'.$inp_num.'_hidden']."` `".$_POST['inps'.$inp_num.'_text']."` ".$_POST['inps'.$inp_num.'_radio'];
									if (!mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Невозможно изменить имя столбца.<BR>'.$query.'<BR>'.mysqli_error($database_desc).'<BR>'); }
									$y++;
									$last_col = $_POST['inps'.$inp_num.'_text'];
							 }
					 	 }
					 elseif ($inp_str == "inpsnewhidden") // Добавление нового столбца
					 	 {
					 	 	 if ($last_col == "")
					 	 	 { $query = "ALTER TABLE `".$_POST['tablename']."` ADD COLUMN `".$_POST['inps'.$inp_num.'_new_text']."` ".$_POST['inps'.$inp_num.'_new_radio']; }
					 	 		else
					 	 	 { $query = "ALTER TABLE `".$_POST['tablename']."` ADD COLUMN `".$_POST['inps'.$inp_num.'_new_text']."` ".$_POST['inps'.$inp_num.'_new_radio']." AFTER `".$last_col."`"; }
					 	 	 	 
					 	 	 if (!mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Невозможно добавить столбец.<BR>'.$query.'<BR>'.mysqli_error($database_desc).'<BR>'); }
					 	 	 $y++;
					 	 	 $last_col = $_POST['inps'.$inp_num.'_new_text'];
					 	 }
					 
			 	 }
			$xe = ' значений';
			if (($y % 10 == 1) ) { $xe = ' значение'; }
			if ($y % 100 == 11) { $xe = ' значений'; }
			if (($y % 10 < 5) && ($y % 10 > 1)) { $xe = ' значения'; }
			if (($y % 100 < 15) && ($y % 10 > 11)) { $xe = ' значений'; }
			echo "<BR> end";
			echo '<BR><BR><div class="head">Изменено и/или добавлено ', $y, $xe, '</div>';
			echo ('<meta HTTP-EQUIV="Refresh" Content="2; URL=">');
		}
	 elseif ($_POST['post_act']=='addcomponent' || $_POST['post_act']=='editcomponent')
	 { // -------------------------------- Добавление детали
	 	 $fieldnames = ""; $fieldvalues = ""; $updatedfields = "";
		 foreach ($_POST as $key => $value)
		 {
			 $inp_str = ""; 
			 $inp_num = "";
			 $inp_strlen = strlen ($key);
			 for ($x=0; $x < $inp_strlen; $x++)
			 {
			  if ($key[$x] >= '0' && $key[$x] <= '9') { $inp_num = $inp_num.$key[$x]; }
			  if ($key[$x] >= 'a' && $key[$x] <= 'z') { $inp_str = $inp_str.$key[$x]; }
			 }
			 if ($inp_str == "inpshiddennum") { $num = $value; }
			 if (($inp_str == "inpshidden") && ($value != "Номер"))
			 {
			 	 if ($fieldnames == "")
			 	 {
			 	 	$fieldnames = "`".$value."`";
			 	 	$fieldvalues = "'".$_POST['inps'.$inp_num.'_text']."'";
			 	 	$updatedfields = "`".$value."` = '".$_POST['inps'.$inp_num.'_text']."'"; 
			 	 } 
			 	 else
			 	 {
			 	 	$fieldnames = $fieldnames.", `".$value."`";
			 	 	$fieldvalues = $fieldvalues.", '".$_POST['inps'.$inp_num.'_text']."'";
			 	 	$updatedfields = $updatedfields.", `".$value."` = '".$_POST['inps'.$inp_num.'_text']."'";
			 	 }
			 }			 	 
		 }
		 if ($_POST['post_act']=='addcomponent') { $query = "INSERT INTO `".$_POST['tablename']."` (".$fieldnames.") VALUES(".$fieldvalues.")"; }
		 else { $query = "UPDATE `".$_POST['tablename']."` SET ".$updatedfields." WHERE `Номер` = ".$num; }
		 if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось сохранить вещь -  '.mysqli_error($database_desc).'<BR><BR> query ='.$updatedfields); }
		 echo '<BR><BR><div class="head">Вещь сохранена</div>';
		 echo ('<meta HTTP-EQUIV="Refresh" Content="2; URL=/dl">');
	 }
 }
else
//---------------------------------------------------------------------------------
//--------------------------------------------------    ОБРАБОТКА ПАРАМЕТРОВ _GET
//---------------------------------------------------------------------------------

//------------------------------------   Стартовая страница
if (!isset($_GET['act'])) 
  {  // Вывод главной страницы, дерева
    echo('<DIV align="center"><B><font face="MS Sans Serif" size="3" color=#400000>Список диковинок<BR>Trefoil, 2012</font></B></div>'); 
    $query= "SELECT * FROM ".$table_categories." ORDER BY branch";
    if (!$result = mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Не удалось считать данные из базы.<BR>'.mysqli_error($database_desc).'<BR>'); }
    $i = 0;
    while ($row = mysqli_fetch_array($result))
      {
        $rowarr[$i] = $row;
        $i++;
      } 
    $sizerar=$i;
    for ($i=0; $i<$sizerar; $i++)
   	{
  	 	for ($i1 = $i+1; $i1<$sizerar; $i1++)
   	  {
   	 	  if ($rowarr[$i][1] == $rowarr[$i1][2])
   	   	{
   	     $t = $rowarr[$i1];
   	     for ($t2=$i1; $t2>($i+1); $t2--)
   	     {
   	       $rowarr[$t2] = $rowarr[$t2-1];
   	     }
   	     $rowarr[$i+1] = $t;
   	   	}
   	  }
   	}

    echo '<div onclick="tree_toggle(arguments[0])">
   	      <div><br>Каталог </div><div class="editlink" onclick="add_categorytreeform(this, \'-1\', \'child\')">+child</div> ';
   	for ($i=0; $i<$sizerar; $i++)
   		{
   			$is_last_branch = true;
   			$isset_next_cell = isset($rowarr[$i+1]);
   			for ($i2 = ($i+1); $i2 < $sizerar; $i2++)
   				{
   				  if ($rowarr[$i2]['branch'] < $rowarr[$i]['branch']) { $is_last_branch = true; break; }
   				  if ($rowarr[$i2]['branch'] == $rowarr[$i]['branch']) { $is_last_branch = false; break; }
   				}
   			$li_class = '';
   			if ($rowarr[$i]['branch'] == 1)
   				{
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] < $rowarr[$i]['branch'])) || !$isset_next_cell || $is_last_branch) { $li_class = '<li class="TreeNode TreeIsRoot TreeExpandLeaf TreeIsLast">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] > $rowarr[$i]['branch']))) { $li_class = '<li class="TreeNode TreeIsRoot TreeExpandOpen">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] > $rowarr[$i]['branch'])) && $is_last_branch) { $li_class = '<li class="TreeNode TreeIsRoot TreeExpandOpen TreeIsLast">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] == $rowarr[$i]['branch']))) { $li_class = '<li class="TreeNode TreeIsRoot TreeExpandLeaf">'; }
   				} else
   				{
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] < $rowarr[$i]['branch'])) || !$isset_next_cell || $is_last_branch) { $li_class = '<li class="TreeNode TreeExpandLeaf TreeIsLast">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] >= $rowarr[$i]['branch']))) { $li_class = '<li class="TreeNode TreeExpandOpen">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] >= $rowarr[$i]['branch'])) && $is_last_branch) { $li_class = '<li class="TreeNode TreeExpandOpen TreeIsLast">'; }
   				  if (($isset_next_cell && ($rowarr[$i+1]['branch'] == $rowarr[$i]['branch']))) { $li_class = '<li class="TreeNode TreeExpandLeaf">'; }
   				}
   			$li_class = $li_class.'<div class="TreeExpand"></div>'.PHP_EOL;
   			$li_class = $li_class.'<div class="TreeContent" onclick="show_table(\''.$rowarr[$i]['category_name'].'\')">'.$rowarr[$i]['category_name'];
   			if ($rowarr[$i]['category_name'] != "Каталог местонахождений")
   			{
   				//$li_class = $li_class.' <div class="editlink" onclick="add_categorytreeform(this, \''.$rowarr[$i]['number'].'\', \'neighbour\')">+cat</div> <div class="editlink" onclick="add_categorytreeform(this, \''.$rowarr[$i]['number'].'\', \'child\')">+child</div> <div class="editwarnlink"  onclick="add_category(\'del\', this, \''.$rowarr[$i]['number'].'\')">-del</div>';
   				$li_class = $li_class.' <div class="editlink" onclick="add_categorytreeform(this, \''.$rowarr[$i]['number'].'\', \'child\')">+child</div> <div class="editwarnlink"  onclick="add_category(\'del\', this, \''.$rowarr[$i]['number'].'\')">-del</div>';
   			}
   			$li_class = $li_class.'</div>'.PHP_EOL;
   			if (!isset($rowarr[$i-1]) || $rowarr[$i]['branch'] > $rowarr[$i-1]['branch'])
   				{
   					echo PHP_EOL, '<ul class="TreeContainer">';
   					echo PHP_EOL, $li_class;
   				}
   			if (isset($rowarr[$i-1]) && ($rowarr[$i]['branch'] == $rowarr[$i-1]['branch']))
   				{
   					echo PHP_EOL, $li_class;
   				}
   			if (isset($rowarr[$i-1]) && ($rowarr[$i]['branch'] < $rowarr[$i-1]['branch']))
   				{
   					for ($i2 = ($rowarr[$i-1]['branch'] - $rowarr[$i]['branch']); $i2>0; $i2--)	{ echo PHP_EOL, ' </li>', PHP_EOL, '</ul>'; }
   			  	echo PHP_EOL, $li_class;
   				}
   			if (!$isset_next_cell)
   				{
   					for ($i2 = $rowarr[$i]['branch']; $i2>0; $i2--) { echo PHP_EOL, ' </li>', PHP_EOL, '</ul>'; }
   				}
   		}
    echo '<br><hr><br><div id="dikovin_table" align="center">Выберите любую категорию</div><br><br>';
  }
//-----------------------------------------
//------------------------------------------   Изменение структуры таблицы, добавление компонента
//-----------------------------------------
elseif ($_GET['act']=='edittablestruc' || $_GET['act']=='editcomponent' || $_GET['act']=='addcomponent')
 {
 	 $addcomp = $_GET['act']=='addcomponent';
 	 $editcomp = $_GET['act']=='editcomponent';
 	 $editstruc = $_GET['act']=='edittablestruc'; 	 
 	 // Шапка
 	 echo '<DIV align="center"><B><a href="/dl"><font face="MS Sans Serif" size="3" color=#400000>Список диковинок<BR>Trefoil, 2012</font></a></B></div><br><hr><br>';
 	 echo '<div class="head">', $_GET['name'], '</div><br>';
 	 // Чтение полей таблицы
 	 $table = str_replace (" ", "_", $_GET['name']);
 	 $query = "SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '".$table."' ORDER BY ORDINAL_POSITION;";
   if (!$result = mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Не удалось считать структуру таблицы.<BR>'.mysqli_error($database_desc).'<BR>'); }
   if ($editcomp)
 	 {
 	 	 $query1 = "SELECT * FROM `".$table."` WHERE `Номер` = ".$_GET['num'];
 	 	 if (!$result1 = mysqli_query($database_desc, $query1)) { die('<BR>Ошибка: Не удалось считать данные детали.<BR>'.mysqli_error($database_desc).'<BR>'); }
 	 	 $comp_row = mysqli_fetch_array($result1);
 	 }
 	 if ($addcomp)
 	 {
 	 	 $query1 = "SELECT * FROM `".$table."` ORDER BY Номер DESC LIMIT 1";
 	 	 if (!$result1 = mysqli_query($database_desc, $query1)) { die('<BR>Ошибка: Не удалось считать данные детали.<BR>'.mysqli_error($database_desc).'<BR>'); }
 	 	 $comp_row = mysqli_fetch_array($result1);
 	 }
 	 // Вывод полей редактирования структуры таблицы
 	 echo '<form name="edittablestruc" method="post" action="index.php">';
 	 $x = 0;
 	 echo '<input type="hidden" name="tablename" value="',  $table, '" />';
 	 echo '<table>';
 	 while ($row = mysqli_fetch_array($result))
		 {
		 	 $elem_name = "inps".$x;
			 $x++;
			 if ($editcomp || $addcomp) { $editbarcontent = $comp_row[$row[0]]; } else { $editbarcontent = $row[0]; }  
		 	 echo '
		 	 <tr id="', $elem_name, '">
		 	 <td><label name="', $elem_name, '_label" id="', $elem_name, '_label">', $row[0], '</label><input type="hidden" name="'.$elem_name.'_hidden"  value="', $row[0], '" />';
		 	 if ($row[0] == "Номер")
		 	 {
		 	 	echo '<input type="hidden" name="'.$elem_name.'_hiddennum"  value="', $editbarcontent, '" />
		 	 	</td><td>'.$editbarcontent.'</td></tr>';
		 	 	continue;
		 	 }
		 	 if (($row[0] == "Местонахождение") && ($addcomp || $editcomp))
		 	 {
		 	 	 $query2 = "SELECT * FROM `Каталог_местонахождений`";
		 	 	 if ($result2 = mysqli_query($database_desc, $query2))
		 	 	 {
		 	 	 	 echo '</td><td><select name="', $elem_name, '_text">';
		 	 	 	 while ($places_row = mysqli_fetch_array($result2))
		 	 	 	 {
		 	 	 	 	 if ($editbarcontent == $places_row['Наименование']) { $insert_selected = ' selected=selected'; } else {  $insert_selected = ''; }
		 	 	 	 	 echo '<option'.$insert_selected.' value = "'.$places_row['Наименование'].'">'.$places_row['Наименование'].'</option>';
		 	 	 	 }
		 	 	 	 echo "</select></td></tr>";
		 	 	 	 continue;
		 	 	 }
		 	 	 echo "<div> Error in catalog of places: ".mysqli_error($database_desc)." </div>";
		 	 }
		 	 echo '</td>
		 	 <td><input type="text" name="', $elem_name, '_text" id="',$elem_name , '_text" value="', $editbarcontent, '" /></td>';
		 	 if ($editstruc)
		 	 {
		 	 	 echo'<td><input type="radio" name="', $elem_name, '_radio" id="',$elem_name , '_radioint" value="float" ';
		 	 	 if ($row[1] == 'float') { echo 'checked="checked"'; }			
		 	 	 echo	' /> Число  <input type="radio" name="',$elem_name , '_radio" id="',$elem_name , '_radiotext" value="text" ';
				 if ($row[1] == 'text') { echo 'checked="checked"'; }			
				 echo	' /> Текст</td>
				 <td><a style="color:red;" onclick="return deleteField(', $elem_name, ')" href="#"> [x]</a></td>
				 <td><a style="color:green;" onclick="return addField(', $elem_name, ')" href="#"> [+]</a></td>';
			 }
			 echo '</tr>';
		 }
		 echo '</table>';
		 if ($x == 1 && $editstruc)	{ echo '<a style="color:green;" onclick="return addField(', $elem_name, ')" href="#"> [+]</a>';	}
		 echo '<br><br><input value="OK" type="submit" id="input_go" />';
		 if ($addcomp) { echo '<input type="hidden" name="post_act" value="addcomponent" />'; } 
		 if ($editstruc) { echo '<input type="hidden" name="post_act" value="settablestruc" />'; }
		 if ($editcomp) { echo '<input type="hidden" name="post_act" value="editcomponent" />'; }		 
	echo '</form>';
 }
//------------------------------------------   Удаление компонента, 
elseif ($_GET['act']=='delcomponent')
{
	$table = str_replace (" ", "_", $_GET['name']);
 	$query = "DELETE FROM $table WHERE `Номер` = '".$_GET['num']."'";
 	if (!$result = mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Не удалось удалить запись.<BR>'.mysqli_error($database_desc).'<BR>'); }
	echo '<BR><BR><div class="head">Вещь удалена</div>';
	echo ('<meta HTTP-EQUIV="Refresh" Content="2; URL=/dl">'); 	
}
else
//--------------------------   Страница вывода ошибки
 {
  echo('<font face="MS Sans Serif" size="2" color=#FF0000>Неправильное использование страницы</font>');
  echo ('<meta HTTP-EQUIV="Refresh" Content="1; URL=/dl">');
 }
?>

<div align="center"><font face="Arial" color="#F0F0F8" size="2">Copyleft Trefoil, 2012<BR>admin@trefoil.org.ua</font></div>
</font>
</body>
</font>
</html>

<?php
 }
?>