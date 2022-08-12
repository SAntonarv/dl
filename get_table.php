<?php
include "baseconf.inc";
// Подключение к базе
$database_desc = mysqli_connect($server, $user, $pass);
if(!$database_desc) { die('<BR>Ошибка: не удается подключиться к MySQL серверу<BR>'); }
if(!mysqli_select_db($database_desc, $basename)) { die('<BR>Ошибка: не удается подключиться к MySQL серверу<BR>'); }
$query = 'SET NAMES utf8';
if (!mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Кодировка может не поодерживаться.<BR>'.mysqli_error($database_desc).'<BR>'); }

if (isset($_GET['ik']))
	{
		// Запрос данных из таблицы
		$table = mysqli_real_escape_string($database_desc, $_GET['ik']);
		$table = str_replace (" ", "_", $table);
		$query = "SELECT * FROM `$table`";
		if (!$result = mysqli_query($database_desc, $query)) { die('<BR>Ошибка: Не удалось считать данные из базы.<BR>'.mysqli_error($database_desc).'<BR>'); }
		$query1 = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '".$table."' ORDER BY ORDINAL_POSITION;";
		if (!$result1 = mysqli_query($database_desc, $query1)) { die('<BR>Ошибка: Не удалось считать данные из базы.<BR>'.mysqli_error($database_desc).'<BR>'); }
		
		//Возврат таблицы
		echo '<div class="head">', $_GET['ik'], '</div>';
		if ($table != 'Каталог_местонахождений')
		{
			echo '<a href="?act=edittablestruc&name=',$_GET['ik'] ,'">Отредактировать заголовки</a> : ';
			echo '<a href="?act=addcomponent&name=',$_GET['ik'] ,'">Добавить деталь</a>';
		} else { echo '<a href="?act=addcomponent&name=',$_GET['ik'] ,'">Добавить место</a>'; }
		echo '<table class="atable">';
		echo '<tr>';
		while ($row = mysqli_fetch_array($result1))
			{
				$cntarr = sizeof ($row) / 2;
				for ($x = 0; $x < $cntarr; $x++)
					{
						echo '<td class="tdhead">', $row[$x], '</td>';
					}
			}
		echo '</tr>';
		while ($row = mysqli_fetch_array($result))
			{
				echo '<tr>';
				$cntarr = sizeof ($row) / 2;
				for ($x = 0; $x < $cntarr; $x++)
					{
						echo '<td class="atd">', $row[$x], '</td>';
					}
				echo '<td class="atd"><a class="editlink" href="?act=editcomponent&name=',$_GET['ik'] ,'&num='.$row['Номер'].'">edit</a> 
				. <a class="editlink" href="?act=delcomponent&name=',$_GET['ik'] ,'&num='.$row['Номер'].'">del</a></td>';	
				echo '</tr>';
			} 
		echo '</table>';
	}
elseif (isset($_GET['nc']))
	{
		if (!isset($_GET['name']) || ($_GET['name'] == "") || !isset($_GET['num']) || ($_GET['num'] == "")) { exit; }
		// Поместить новую запись в таблицу категорий
		if (($_GET['nc'] == 1) || ($_GET['nc'] == 2))
			{
				if ($_GET['num'] != '-1')
				{
					$query = "SELECT * FROM `dikovin_categories` WHERE `number` = ".$_GET['num'];
					if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось считать данные из базы  -  '.mysqli_error($database_desc)); }
					$row = mysqli_fetch_array ($result);
				}
				if ($_GET['nc'] == 1)
					{
						$new_branch = $row['branch'];
						$new_parent = $row['parent'];
					}
				elseif ($_GET['nc'] == 2)
					{
						if ($_GET['num'] == -1)
						{
							$new_parent = "root";
							$new_branch = 1;
						} else
						{
							$new_parent = $row['category_name'];
							$new_branch = $row['branch']+1;
						}
					}
				$query = "INSERT INTO `dikovin_categories` (`category_name`, `parent`, `branch`) VALUES('".$_GET['name']."', '".$new_parent."', '".$new_branch."')";
				if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось сохранить новую категорию  -  '.mysqli_error($database_desc)); }
				$last_id_num = mysqli_insert_id($database_desc);
				// Создать новую таблицу
				$table = mysqli_real_escape_string($database_desc, $_GET['name']);
				$table = str_replace (" ", "_", $table);
				$query = "CREATE TABLE `".$table."` (Номер INT AUTO_INCREMENT KEY)";
				if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось создать таблицу для новой категории  -  '.mysqli_error($database_desc)); }
				// Ok]
				echo 'OK'.$last_id_num;
			}
		elseif ($_GET['nc'] == 3)
			{
				$query = "SELECT * FROM `dikovin_categories` WHERE `number` = ".$_GET['num'];
				if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось прочитать запись из базы  -  '.mysqli_error($database_desc)); }
				$row = mysqli_fetch_array($result);
				$row['category_name'] = str_replace (" ", "_", $row['category_name']);
				$query = "DROP TABLE `".$row['category_name']."`";
				if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось удалить таблицу  -  '.mysqli_error($database_desc)); }
				$query = "DELETE FROM `dikovin_categories` WHERE `number` = ".$_GET['num'];
				if (!$result = mysqli_query($database_desc, $query)) { die('Ошибка: Не удалось удалить запись из базы  -  '.mysqli_error($database_desc)); }				
				echo 'DELOK';
				
			}
			
	}

?>
