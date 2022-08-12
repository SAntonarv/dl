var countOfFields = 1; // Текущее число полей
var curFieldNameId = 1; // Уникальное значение для атрибута name
var maxFieldLimit = 25; // Максимальное число возможных полей
var newcat_count = 0;

function insert_after(elem, refElem) {
   var parent = refElem.parentNode;
   var next = refElem.nextSibling;
   if (next) { return parent.insertBefore(elem, next); } else { return parent.appendChild(elem); }
	}

function deleteField(a) {
	var pre_node = a.parentNode;
	if ( a.id.indexOf("_") == -1 )
		{
			document.getElementById(a.id + "_text").disabled = "disabled";
			document.getElementById(a.id + "_radioint").disabled = "disabled";
			document.getElementById(a.id + "_radiotext").disabled = "disabled";
			document.getElementById(a.id + "_label").innerHTML = "Будет удалено";
			a.id += "_deleted";
		}
	else
		{
			if ( a.id.indexOf("_deleted") == -1 )
				{
					pre_node.removeChild(a);
					countOfFields--;
				}
		}
  return false;
}

function addField(b) {
 if (countOfFields >= maxFieldLimit) {
 	 alert("Число полей достигло своего максимума - " + maxFieldLimit);
 	 return false;
 }
 countOfFields++;
 curFieldNameId++;
 var ntr = document.createElement("tr");
 ntr.id = "inps" + curFieldNameId + "_new";
 ntr.innerHTML = "<td><label name=\"" + ntr.id + "_label\" id=\"" + ntr.id + "_label\">Новое поле</label><input type=\"hidden\" name=\"" + ntr.id + "_hidden\"  value=\"Новое поле\" /></td>";
 ntr.innerHTML += "<td><input type=\"text\" name=\"" + ntr.id + "_text\" id=\"" + ntr.id + "_text\" value=\"Новое поле\" /></td>";
 ntr.innerHTML += "<td><input type=\"radio\" name=\"" + ntr.id + "_radio\" id=\"" + ntr.id + "_radioint\" value=\"float\" /> Число <input type=\"radio\" name=\"" + ntr.id + "_radio\" id=\"" + ntr.id + "_radiotext\" value=\"text\" checked=\"checked\" /> Текст</td>";
 ntr.innerHTML += "<td><a style=\"color:red;\" onclick=\"return deleteField(" + ntr.id + ")\" href=\"#\"> [x]</a></td>";
 ntr.innerHTML += "<td><a style=\"color:green;\" onclick=\"return addField(" + ntr.id + ")\" href=\"#\"> [+]</a></td>";
 insert_after(ntr, b);

 return false;
}

function tree_toggle(event)
 {
  event = event || window.event
  var clickedElem = event.target || event.srcElement
  if (!hasClass(clickedElem, 'TreeExpand'))
   {
    return
   }
  var node = clickedElem.parentNode
  if (hasClass(node, 'TreeExpandLeaf'))
   {
    return
   }
  var newClass = hasClass(node, 'TreeExpandOpen') ? 'TreeExpandClosed' : 'TreeExpandOpen'
  var re =  /(^|\s)(TreeExpandOpen|TreeExpandClosed)(\s|$)/
  node.className = node.className.replace(re, '$1'+newClass+'$3')
 }

function hasClass(elem, className)
 {
  return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className)
 }
 
function show_table(reqtable)
  {
		var xmlhttp;
		if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
			}
		else
			{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
		xmlhttp.onreadystatechange=function()
			{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
				document.getElementById("dikovin_table").innerHTML=xmlhttp.responseText;
				}
			}
		xmlhttp.open("GET","get_table.php?ik=" + reqtable,true);
		xmlhttp.send();
	}
 
function add_categorytreeform(curr_node, num, relation)
	{

		var tree_new_li = document.createElement("li");
		tree_new_li.id = "newcat_div" + newcat_count;
		tree_new_li.className = curr_node.parentNode.parentNode.className;
		tree_new_li.className = tree_new_li.className.replace('TreeExpandOpen', 'TreeExpandLeaf');
		tree_new_li.className = tree_new_li.className.replace('TreeExpandClosed', 'TreeExpandLeaf');
		if (tree_new_li.className.search('TreeIsLast') != -1 ) 
			{
				curr_node.parentNode.parentNode.className = curr_node.parentNode.parentNode.className.replace('TreeIsLast', '');
				tree_new_li.className += " TreeIsLast"; 
			}		
		var tmp = "";
		tmp = "<div class=\"TreeExpand\"></div><div class=\"TreeContent\"><input type=\"text\" name=\"newcat_edit" + newcat_count + "\" id=\"newcat_edit" + newcat_count + "\" /><input type=\"button\" value=\"Добавить\" onclick=\"add_category(newcat_edit" + newcat_count +", " + tree_new_li.id + ", " + num + ")\" /></div>";
		if (relation == 'child')
			{
				tmp = "<div class=\"TreeExpand\"></div><div class=\"TreeContent\"><input type=\"text\" name=\"newchild_edit" + newcat_count + "\" id=\"newchild_edit" + newcat_count + "\" /><input type=\"button\" value=\"Добавить\" onclick=\"add_category(newchild_edit" + newcat_count +", " + tree_new_li.id + ", " + num + ")\" /></div>";
				tmp = "<ul class=\"TreeContainer\"><li class=\"TreeNode TreeExpandLeaf TreeIsLast\">" + tmp + "</li></ul>";
				curr_node.parentNode.parentNode.className = curr_node.parentNode.parentNode.className.replace('TreeExpandLeaf', 'TreeExpandOpen');
			}
		tree_new_li.innerHTML = tmp;
		newcat_count++;
		curr_node_parent = curr_node.parentNode;
		insert_after(tree_new_li, curr_node_parent.parentNode);
	}
	
function add_category(newcat_name, node_target, num)
	{
		var xmlhttp;
		var new_catalog_name = "";
		new_catalog_name = newcat_name.value;
		var nctmp = 1;
		if (newcat_name == 'del') { var nctmp = 3; } else
			{
				if (newcat_name.id.substr(0, 8) == 'newchild') { var nctmp = 2; }
			}			
		if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
			}
		else
			{// code for IE6, IE5
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
		xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						var result_saving = xmlhttp.responseText;
						if (result_saving.substr(0, 2) == "OK")
							{
								var tmp = "<div class=\"TreeExpand\"></div><div class=\"TreeContent\" onclick=\"show_table('" + new_catalog_name + "')\">" + new_catalog_name + "<div style=\"display:inline; color:#aaa; font-size:11\" onclick=\"add_categorytreeform(this, " + result_saving.substr(2, result_saving.length - 2) + ", 'neighbour')\"> +cat</div> <div style=\"display:inline; color:#aaa; font-size:11\" onclick=\"add_categorytreeform(this, " + result_saving.substr(2, result_saving.length - 2) + ", 'child')\">+child</div> <div style=\"display:inline; color:#faa; font-size:11\">-del</div></div>";
								if (node_target.innerHTML.substr(0, 3) == "<ul")
									{
										tmp = "<ul class=\"TreeContainer\"><li class=\"TreeNode TreeExpandLeaf TreeIsLast\">" + tmp + "</li></ul>";
									}
								node_target.innerHTML = tmp;
							}
						else
							{
								if (result_saving.substr(0, 5) == "DELOK")	{ node_target.parentNode.innerHTML = "Удалено"; }
								else { node_target.innerHTML = "<div>" + result_saving + "</div>"; }
							}
					}
			}
		xmlhttp.open("GET", "get_table.php?nc=" + nctmp + "&name=" + new_catalog_name + "&num=" + num, true);
		xmlhttp.send();
	}
