<?php 
include "baseconf.inc";

{
?>
<html>
<head>
<title>���� ��������</title>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
</head>
<body bgcolor=#D0D0D8>
<font face="MS Sans Serif" size="2">
<table border="0" cellpadding="5">
 <tr>
  <td>
   <a href="?act=clist"><font face="MS Sans Serif" size="2">������ ��������</font></a>
  </td>
  <td>
   <a href="?act=tlist"><font face="MS Sans Serif" size="2">������ �����</font></a>
  </td>
  <td>
   <a href="?act=stat"><font face="MS Sans Serif" size="2">����������</font></a>
  </td>
  <td>
   <a href="?act=instr"><font face="MS Sans Serif" size="2">����������</font></a>
  </td>
 </tr>
</table>
<HR>

<?php
//---------------------------------------------------------------------------------------------
//-----------------------    ����������� � ����, �������� �������������, �������� ���� � ������
//---------------------------------------------------------------------------------------------
$dbds = mysql_connect($server, $user, $pass);
if(!$dbds) { die('<BR>������: �� ������� ������������ � MySQL �������<BR>'); }
if(!mysql_select_db($basename, $dbds))
{
 echo('<BR>��������������: ���� ������ �� �������, ������ �����.<BR>');
 $query = 'CREATE DATABASE '.$basename.' DEFAULT CHARACTER SET cp1251 COLLATE cp1251_general_ci';
 if (!mysql_query($query, $dbds)) {die('<BR>������: ���� ������ �� �������<BR>');}
 if (!mysql_select_db($basename, $dbds)) {die('<BR>�� ������� �������������� � ���� ������<BR>');}
}
$query = 'SET NAMES cp1251';
if (!mysql_query($query, $dbds)) {die('<BR>������: ��������� ����� �� ��������������.<BR>'.mysql_error().'<BR>');}

$query = 'CREATE TABLE IF NOT EXISTS '.$tbl_usr_lst.' (
idt INT(12) NOT NULL auto_increment,
surname TEXT,
name TEXT,
mname TEXT,
address TEXT,
town TEXT,
country TEXT,
postindex TEXT,
femail TEXT,
semail TEXT,
birthday DATE,
reg_date DATE,
deleted BOOL,
PRIMARY KEY (idt))';
if (!mysql_query($query, $dbds)) {die('<BR>������� users_list �� �������<BR>'.mysql_error());}
$query = 'CREATE TABLE IF NOT EXISTS '.$tbl_usr_det.' (
postid INT(12) NOT NULL auto_increment,
idt INT(12),
mess_date DATETIME,
mess_text TEXT,
is_task BOOL,
PRIMARY KEY (postid))';
if (!mysql_query($query, $dbds)) {die('<BR>������� users_data �� �������<BR>'.mysql_error());}
$query = 'CREATE TABLE IF NOT EXISTS '.$tbl_itn_stat.' (
postid INT(12) NOT NULL auto_increment,
lidt INT(12),
mess_date DATETIME,
mess_text LONGTEXT,
allmon INT,
PRIMARY KEY (postid))';
if (!mysql_query($query, $dbds)) {die('<BR>������� statistics �� �������<BR>'.mysql_error());}
$query = 'CREATE TABLE IF NOT EXISTS '.$tbl_itn_instr.' (
postid INT(12),
lidt INT(12),
mess_date DATETIME,
mess_text LONGTEXT,
allmon INT,
PRIMARY KEY (postid))';
if (!mysql_query($query, $dbds)) {die('<BR>������: ������� ITN instructions �� �������<BR>'.mysql_error());}

//---------------------------------------------------------------------------------
//-------------------------------------------------    ��������� ���������� _POST
//---------------------------------------------------------------------------------

if (isset($_POST['a_act']))
 {
 if ($_POST['a_act']=='add')
 {
  $idc=$_POST['a_client'];
  if ($idc=="0")
  //--------------------------   ���������� ������ �������
   {
    $query = "INSERT INTO $tbl_usr_lst (idt, surname, name, mname, address, town, country, postindex, femail, semail, birthday, reg_date, deleted)
              VALUES (NULL , '".$_POST['a_surname']."', '".$_POST['a_name']."', '".$_POST['a_mname']."', '".$_POST['a_address']."', '".$_POST['a_town']."',
              '".$_POST['a_country']."', '".$_POST['a_index']."', '".$_POST['a_mail1']."', '".$_POST['a_mail2']."', '".$_POST['a_birthday']."', NOW(), NULL)";
    if (!mysql_query($query, $dbds)) {die ('<BR>������ �� ��������!<BR><BR>'.mysql_error());} else
      {echo ('<BR>������ ��������. �����  ���������������');}
    $urlst = $_POST['a_back'];
    $urlarr = explode('__',$urlst);  $urlst = implode ('=', $urlarr);
    $urlarr = explode('_',$urlst);  $urlst = implode ('&', $urlarr);
    $urlst = '?'.$urlst;
    echo ('<meta HTTP-EQUIV="Refresh" Content="4; URL='.$urlst.'">');
   } else
   {
    //--------------------------   ���������� ������ �������
    $msg = $_POST['a_message'];
    $msg = htmlspecialchars(stripslashes($msg));
    $msg = nl2br($msg);
    if ($_POST['a_istask']=='on') {$it = 1;} else {$it = 0;}
    $query = "INSERT INTO ".$tbl_usr_det." (postid, idt, mess_date, mess_text, is_task)
              VALUES (NULL, '".$_POST['a_client']."', NOW(), '".$msg."','".$it."')";
    if (!mysql_query($query, $dbds)) {die ('<BR>������ �� ��������!<BR><BR>'.mysql_error());} else
      {echo ('<BR>������ ��������. �����.');}
    $urlst = $_POST['a_back'];
    $urlarr = explode('__',$urlst);  $urlst = implode ('=', $urlarr);
    $urlarr = explode('_',$urlst);  $urlst = implode ('&', $urlarr);
    $urlst = '?'.$urlst;
    echo ('<meta HTTP-EQUIV="Refresh" Content="2; URL='.$urlst.'">');
   }
 }
 elseif ($_POST['a_act']=='editprofile')
 {
  //--------------------------   ��������� ������� �������
  $idc=$_POST['a_client'];
  $query = "UPDATE $tbl_usr_lst SET surname='".$_POST['a_surname']."', name='".$_POST['a_name']."', mname='".$_POST['a_mname']."',
                   address='".$_POST['a_address']."', town='".$_POST['a_town']."', country='".$_POST['a_country']."',
                   postindex='".$_POST['a_index']."', femail='".$_POST['a_mail1']."', semail='".$_POST['a_mail2']."',
                   birthday='".$_POST['a_birthday']."', reg_date='".$_POST['a_regdate']."' WHERE idt='".$idc."'";
  if (!mysql_query($query, $dbds)) {die ('<BR>������ �� ��������!<BR><BR>'.mysql_error());} else
      {echo ('<BR>������ ��������. ����� ���������������');}
  $urlst = $_POST['a_back'];
  $urlarr = explode('__',$urlst);  $urlst = implode ('=', $urlarr);
  $urlarr = explode('_',$urlst);  $urlst = implode ('&', $urlarr);
  $urlst = '?'.$urlst;
  echo ('<meta HTTP-EQUIV="Refresh" Content="4; URL='.$urlst.'">');
 }
 //--------------------------   ��������� �����
 elseif ($_POST['a_act']=='editpost')
 {
  switch ($_POST['a_tbl'])
  {
   case '2':
    $msg = $_POST['a_message'];
    $msg = htmlspecialchars(stripslashes($msg));
    $msg = nl2br($msg);
    if ($_POST['a_istask']=='on') {$it = 1;} else {$it = 0;}
    $query="UPDATE $tbl_usr_det SET mess_text='".$msg."', is_task='".$it."' WHERE postid='".$_POST['a_postid']."'";
    if (!mysql_query($query, $dbds)) {die ('<BR>������ �� ��������!<BR><BR>'.mysql_error());} else
     {echo ('<BR>������ ��������. ����� ���������������');}
    echo ('<meta HTTP-EQUIV="Refresh" Content="2; URL=index.php?act=clist">');
   break;
  }
 }

 } else
//---------------------------------------------------------------------------------
//--------------------------------------------------    ��������� ���������� _GET
//---------------------------------------------------------------------------------
//--------------------------   
//------------------------------------   ��������� ��������
if (!isset($_GET['act']))
 {
  echo('<DIV align="center"><B><font face="MS Sans Serif" size="3" color=#400000>���� ��������, �����.<BR>������ 1.0<BR>���������� Stayernik, 2006</font></B></div>');
 }
//------------------------------------------   ����� ���������� �������
elseif ($_GET['act']=='add')
 {
  if (!isset($_GET['client'])) {die('<BR><font face="MS Sans Serif" size="2" color=#FF0000>������������ ������������� ��������</font>');}
  $idc = $_GET['client'];
  if ($idc=='0')
   {
    ?>
    <form action="index.php" method=post>
     <center><font face="Arial" size="3"><B>���������� �������</B></font></center>
     <BR>
     <table>
     <input type="hidden" name="a_act" value="<?php echo('add'); ?>">
     <input type="hidden" name="a_client" value="0">
     <input type="hidden" name="a_back" value="<?php echo($_GET['back']); ?>">
     <tr><td>�������:</td><td><input type="text" name="a_surname" size="20"></td></tr>
     <tr><td>���:</td><td><input type="text" name="a_name" size="20"></td></tr>
     <tr><td>��������:</td><td><input type="text" name="a_mname" size="20"></td></tr>
     <tr><td>�����:</td><td><input type="text" name="a_address" size="20"></td></tr>
     <tr><td>�����:</td><td><input type="text" name="a_town" size="20"></td></tr>
     <tr><td>������:</td><td><input type="text" name="a_country" size="20"></td></tr>
     <tr><td>������:</td><td><input type="text" name="a_index" size="20"></td></tr>
     <tr><td>������ ����:</td><td><input type="text" name="a_mail1" size="20"></td></tr>
     <tr><td>������ ����:</td><td><input type="text" name="a_mail2" size="20"></td></tr>
     <tr><td>���� ��������:</td><td><input type="text" name="a_birthday" size="20"></td></tr>
     </table><BR><BR>
     <input type="submit" value="���������">
    </form>
   <?php
   } else
   //---------------------------------------   ����� ���������� ������ � �������
   {
    ?>
     <table style="width:85%;border:#BBB 2px solid;background-color:#D8D8D8" tabindex="4" align="center">
     <tr><td>
     <form action="index.php" method=post>
     <center><font face="Arial" size="3"><B>���������� ������/��������� �������</B></font></center>
     <input type="hidden" name="a_act" value="<?php echo('add'); ?>">
     <input type="hidden" name="a_client" value="<?php echo($_GET['client']); ?>">
     <input type="hidden" name="a_back" value="<?php echo($_GET['back']); ?>">
     <div align="center"><textarea name="a_message" id="messag" wrap="virtual" cols="75" rows="15" style="width:95%;border:#AAA 2px solid;background-color:#f8f8f8" tabindex="4"></textarea>
     </div>
     <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type="checkbox" name="a_istask"><font size="2">�������� ��� ������.</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <input type="submit" value="���������">
    </form>
    </td></tr>
    </table>
   <?php
   }
 }
//-----------------------------------------   ����� ������ ��������
elseif ($_GET['act']=='clist')
 {
  echo('<font face="Arial" size="2" color=#400000>������ ��������. �� ������ ����� ����� ������� c����� ��������� �� ���� � � ����, � ����� ������ �����.</font>');
  echo('<div align="center"><table cellpadding="0" border="0" bgcolor="#E0E0E8" width="100%"><tr><td align="center"><font face="arial" size="2" color="#000000">');
  echo('&nbsp;<a href="?act=add&client=0&back=act__clist">�������� �������</a>&nbsp;&nbsp;&nbsp;');
  echo('</font></td></tr></table></div>');
  $query="SELECT * FROM $tbl_usr_lst";
  $rslt = mysql_query($query, $dbds);
  if ($rslt == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error());}
  ?>
  <TABLE width="100%" border="1" bgcolor="#E0E4E0">
  <tr bgcolor="#707070" align="center"><td><font size="1" color="#FFFFFF">�</font></td><td><font size="1" color="#FFFFFF">�������</font></td><td><font size="1" color="#FFFFFF">���</font></td><td><font size="1" color="#FFFFFF">��������</font></td><td><font size="1" color="#FFFFFF">�����</font></td><td><font size="1" color="#FFFFFF">�����</font></td><td><font size="1" color="#FFFFFF">������</font></td><td><font size="1" color="#FFFFFF">������</font></td><td><font size="1" color="#FFFFFF">e-mail(1)</font></td><td><font size="1" color="#FFFFFF">e-mail(2)</font></td><td><font size="1" color="#FFFFFF">���� ��������</font></td><td><font size="1" color="#FFFFFF">���� �����������</font></td><td><font size="1" color="#FFFFFF">��������</font></td></tr>
  <?php
  while ($dtarr = mysql_fetch_array($rslt))
  {
   $idt = trim($dtarr[0]);
   $surname = trim($dtarr[1]);
   $name = trim($dtarr[2]);
   $mname = trim($dtarr[3]);
   $address = trim($dtarr[4]);
   $town = trim($dtarr[5]);
   $country = trim($dtarr[6]);
   $postindex = trim($dtarr[7]);
   $femail = trim($dtarr[8]);
   $semail = trim($dtarr[9]);
   $birthday = trim($dtarr[10]);
   $reg_date = trim($dtarr[11]);
   $isdel = trim($dtarr[12]);
   if ($isdel=='1')
    {continue; $tblstr='<tr bgcolor="#D8D0D0">';} else
    {$tblstr='<tr bgcolor="#E0E4E0">';};
   $tblstr=$tblstr.'<td align="center"><font size="1">'.$idt.'</font>
            </td><td><font size="1">'.$surname.'</font>
            </td><td><font size="1">'.$name.'</font>
            </td><td><font size="1">'.$mname.'</font>
            </td><td><font size="1">'.$address.'</font>
            </td><td><font size="1">'.$town.'</font>
            </td><td><font size="1">'.$country.'</font>
            </td><td><font size="1">'.$postindex.'</font>
            </td><td><font size="1">'.$femail.'</font>
            </td><td><font size="1">'.$semail.'</font>
            </td><td><font size="1">'.$birthday.'</font>
            </td><td><font size="1">'.$reg_date.'</font>
            </td><td>&nbsp;<a href="?act=tlist&client='.$idt.'" title="�������� ������"><img src="/pix/userdet.gif" border="0"></a>&nbsp;
                     <a href="?act=editprofile&client='.$idt.'&back=act__clist" title="�������� �������"><img src="/pix/userprof.gif" border="0"></a>&nbsp;
                     <a href="?act=delclient&client='.$idt.'" title="�������"><img src="/pix/userdel.gif" border="0"></a>&nbsp;
            </td></tr>';
   echo($tblstr);
  }
  echo('</TABLE>');
  mysql_close($dbds);
 }
//-----------------------------------------   ��������� ������� �������
elseif ($_GET['act']=='editprofile')
 {
  $query = "SELECT * FROM $tbl_usr_lst WHERE idt = ".$_GET['client'];
  $rslt = mysql_query($query, $dbds);
  if ($rslt == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error());}
  $dtarr = mysql_fetch_array($rslt);
 ?>
  <form action="index.php" method=post>
   <center><font face="Arial" size="3"><B>��������� ������� �������</B></font></center>
   <BR>
   <table>
   <input type="hidden" name="a_act" value="editprofile">
   <input type="hidden" name="a_client" value="<?php echo $_GET['client'] ?>">
   <input type="hidden" name="a_back" value="<?php echo($_GET['back']); ?>">
   <tr><td>�������:</td><td><input type="text" name="a_surname" size="20" value="<?php echo $dtarr[1] ?>"></td></tr>
   <tr><td>���:</td><td><input type="text" name="a_name" size="20" value="<?php echo $dtarr[2] ?>"></td></tr>
   <tr><td>��������:</td><td><input type="text" name="a_mname" size="20" value="<?php echo $dtarr[3] ?>"></td></tr>
   <tr><td>�����:</td><td><input type="text" name="a_address" size="20" value="<?php echo $dtarr[4] ?>"></td></tr>
   <tr><td>�����:</td><td><input type="text" name="a_town" size="20" value="<?php echo $dtarr[5] ?>"></td></tr>
   <tr><td>������:</td><td><input type="text" name="a_country" size="20" value="<?php echo $dtarr[6] ?>"></td></tr>
   <tr><td>������:</td><td><input type="text" name="a_index" size="20" value="<?php echo $dtarr[7] ?>"></td></tr>
   <tr><td>������ ����:</td><td><input type="text" name="a_mail1" size="20" value="<?php echo $dtarr[8] ?>"></td></tr>
   <tr><td>������ ����:</td><td><input type="text" name="a_mail2" size="20" value="<?php echo $dtarr[9] ?>"></td></tr>
   <tr><td>���� ��������:</td><td><input type="text" name="a_birthday" size="20" value="<?php echo $dtarr[10] ?>"></td></tr>
   <tr><td>���� �����������:</td><td><input type="text" name="a_regdate" size="20" value="<?php echo $dtarr[11] ?>"></td></tr>
   </table><BR><BR>
   <input type="submit" value="���������">
   </form>
  <?php
 }
//------------------------------------   �������������� ������
elseif ($_GET['act']=='editpost')
{
 switch ($_GET['tbl'])
 {
  case '2':
   $query = "SELECT * FROM $tbl_usr_det WHERE postid='".$_GET['postid']."'";
   $rslt = mysql_query($query, $dbds);
   if ($rslt == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error());}
   $dtarr = mysql_fetch_array($rslt);
   $msg=$dtarr['mess_text'];
   $mgarr = explode('<br />',$msg);
   $msg = implode('',$mgarr);
   ?>
    <table style="width:85%;border:#BBB 2px solid;background-color:#D8D8D8" tabindex="4" align="center">
    <tr><td>
    <form action="index.php" method=post>
    <center><font face="Arial" size="3"><B>�������������� ������/��������� �������</B></font></center>
    <input type="hidden" name="a_act" value="editpost">
    <input type="hidden" name="a_tbl" value="2">
    <input type="hidden" name="a_postid" value="<?php echo $_GET['postid'] ?>">
    <div align="center"><textarea name="a_message" wrap="virtual" cols="75" rows="15" style="width:95%;border:#AAA 2px solid;background-color:#f8f8f8" tabindex="4" ><?php echo $msg; ?></textarea>
    </div>
    <BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="checkbox" name="a_istask" <?php if ($dtarr['is_task']=='1') { echo('checked'); } ?>>
    <font size="2">�������� ��� ������.</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" value="���������">
    </form>
    </td></tr>
    </table>
   <?php
   break;
 }
}
//------------------------------------   �������� ������� �� ����

elseif ($_GET['act']=='delclient')
 {
  $query="UPDATE $tbl_usr_lst SET deleted='1' WHERE idt='".$_GET['client']."'";
  if (!mysql_query($query, $dbds)) {die ('<BR>������ �� �����!<BR><BR>'.mysql_error());} else
      {echo ('<BR>������ ������� �������. �����.');}
  echo ('<meta HTTP-EQUIV="Refresh" Content="3; URL=?act=clist">');
 }

//------------------------------------   ����� ������ ���� �����

elseif ($_GET['act']=='tlist')
 {
  if (!isset($_GET['client']))
   {
    echo('<CENTER><B><font face="Arial" size="2" color=#400000>������ ���� �����, ����� ����������. ���������� ��������.</font></B></CENTER><BR>');
    $query="SELECT * FROM $tbl_usr_det WHERE is_task=1 ORDER BY mess_date";
    $rslt = mysql_query($query, $dbds);
    if ($rslt == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error().'<BR>');}
    while ($dtarr = mysql_fetch_array($rslt))
     {
      $query = "SELECT * FROM $tbl_usr_lst WHERE idt='".$dtarr['idt']."'";
      $rslt1 = mysql_query($query, $dbds);
      if ($rslt1 == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error().'<BR>');}
      $dtarr1 = mysql_fetch_array($rslt1)
      ?>
      <DIV align="center">
       <TABLE width="90%" border="1" bgcolor ="<?php if ($dtarr['is_task']=='1') {echo '#F8F8E0';} else {echo '#E0E8E0';} ?>">
        <TR><TD><TABLE border="0" width="100%" bgcolor="#D0D0D8" cellpadding="0" cellspacing="0"><TR><TD align="left" width="200">
         &nbsp;&nbsp;<B><FONT size="3" color="#800000"><?php echo $dtarr["mess_date"]; ?></font></B></TD><TD align="left">
         &nbsp;&nbsp;<FONT size="2" color="#800000"><?php echo($dtarr1[1].' '.$dtarr1[2].', '.$dtarr1[8]); ?></font></TD><TD align="right">
         <a href="?act=editpost&tbl=2&postid=<?php echo $dtarr["postid"]; ?>" title="�������������"><img src="/pix/userprof.gif" border="0"></a>
         <a href="?act=delpost&tbl=2&postid=<?php echo $dtarr["postid"]; ?>" title="�������"><img src="/pix/userdel.gif" border="0"></a>
        &nbsp;&nbsp;</TD></TR></TABLE></TD></TR><TR><TD>
         <TABLE border="0"  width="100%" cellpadding="5"><TR><TD><FONT size="2"> <?php echo $dtarr["mess_text"]; ?></font></TD></TR></TABLE>
        </TD></TR></TABLE><BR>
       </DIV>
      <?php
     }
   } else
   {
    //--------------------------   ����� ������ ��������� � ����� �������

    $idtc = $_GET['client'];
    $query="SELECT * FROM $tbl_usr_det WHERE idt=$idtc ORDER BY mess_date";
    $rslt = mysql_query($query, $dbds);
    if ($rslt == FALSE) {die('<BR>�� ���� ��������� ������ �� ����<BR>'.mysql_error().'<BR>');}
    echo('<CENTER>������/��������� ��� ������� ��� ������� '.$idtc.'</CENTER><BR>');
    while ($dtarr = mysql_fetch_array($rslt))
     {
      ?>
      <DIV align="center">
       <TABLE width="90%" border="1" bgcolor ="<?php if ($dtarr['is_task']=='1') {echo '#F8F8E0';} else {echo '#E0E8E0';} ?>">
        <TR><TD><TABLE border="0" width="100%" bgcolor="#D0D0D8" cellpadding="0" cellspacing="0"><TR><TD align="left">
         <B><FONT size="3" color="#800000">&nbsp;&nbsp;<?php echo $dtarr["mess_date"]; ?></font></B></TD><TD align="right">
         <a href="?act=editpost&tbl=2&postid=<?php echo $dtarr["postid"]; ?>" title="�������������"><img src="/pix/userprof.gif" border="0"></a>
         <a href="?act=delpost&tbl=2&postid=<?php echo $dtarr["postid"]; ?>" title="�������"><img src="/pix/userdel.gif" border="0"></a>
        &nbsp;&nbsp;</TD></TR></TABLE></TD></TR><TR><TD>
         <TABLE border="0"  width="100%" cellpadding="5"><TR><TD><FONT size="2"> <?php echo $dtarr["mess_text"]; ?></font></TD></TR></TABLE>
        </TD></TR></TABLE><BR>
       </DIV>
      <?php
     }
    $addurl = '<DIV align="center"><a href="?act=add&client='.$idtc.'&back=act__tlist_client__'.$idtc.'">�������� ������</a></DIV>';
    echo $addurl;
   }
 }


//--------------------------   ����� ���������� ���

elseif ($_GET['act']=='stat')
 {
  echo('<font face="MS Sans Serif" size="2" color=#400000>����������: ������� �������-����� �����, ����� ������������, ����������� �����, ����� ������ � �������.</font>');
 }

//--------------------------   ����� ���������� ���

elseif ($_GET['act']=='instr')
 {
  echo('<font face="MS Sans Serif" size="2" color=#400000>���������� �� ������ � ITN-Project, � ���� ��������� �����.</font>');
 }
else
//--------------------------   �������� ������ ������
 {
  echo('<font face="MS Sans Serif" size="2" color=#FF0000>������������ ������������� ��������</font>');
  echo ('<meta HTTP-EQUIV="Refresh" Content="1; URL=/">');
 }
?>

<HR>
<div align="center"><font face="Arial" color="#F0F0F8" size="2">Copyright Stayernik, 2006<BR>itn@trefoil.org.ua</font></div>
</font>
</body>
</font>
</html>


<?php
 }
?>