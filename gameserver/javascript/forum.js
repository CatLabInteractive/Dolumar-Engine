function Purge(Heretic, Hex, En) {
	//alert(Heretic + ' ' + Hex);
	Hexen = 'DelNum' + Hex;
	if (document.getElementById(Hexen).value == 'Preserve') {
		if (confirm ('Really delete ' + En + '?')) {
		document.getElementById(Hex).src = 'static/images/forum/CrossR.png';
		document.getElementById(Hexen).value = 'Pickle';
		}
	} else {
		document.getElementById(Hex).src = 'static/images/forum/Cross.png';
		document.getElementById(Hexen).value = 'Preserve';
		alert (En + ' restored.');
	}
}

function Add() {
	var rer = document.getElementById('List').rows.length;
	var table;
	   if (document.all)
		 table = document.all.uploadtable;
	   else if (document.getElementById)
		 table = document.getElementById('List');
	   if (table && table.rows && table.insertRow) {
	   var newID = table.rows.length;
		 var tr = table.insertRow(newID);
	
	rerToo = 700 + rer;//q&d hack to get around the chance of a board having an ID greater than the current rowcount, thus causing Issues
	tr.innerHTML = '<td style="width:20px;"><img src="static/images/forum/ArrowUp.png" alt="Move up" onclick="Shift(this, -1);" /><img src="static/images/forum/ArrowDown.png" alt="Move down" onclick="Shift(this, 1);" /></td><td style="width:1%;"><span style="display:none;">' + rer + '</span><input type="hidden" name="Position' + rerToo + '" value="' + rer + '" /><input type="hidden" name="Made' + rerToo + '" value="1" /></td><td class="ghostRow"><input type="text" class="nameBox" name="TitleNum' + rerToo + '" value="Title ' + rer + '" /><input type="text" class="descBox" name="DescNum' + rerToo + '" value="Summary ' + rer + '" /></td><td style="width:60px;"><label>Private: <input type="checkbox" name="Priv' + rerToo + '" id="Priv' + rerToo + '" /></label></td><td style="width:1%;"><img id="' + rerToo + '" name="' + rerToo + '" src="static/images/forum/Cross.png" alt="Delete?" onclick="Purge(this, ' + rerToo + ', \'this board\');" /><input type="hidden" name="DelNum' + rerToo + '" id="DelNum' + rerToo + '" value="Preserve" /></td>';
	
	}
}

function AddBan() {
	var rer = document.getElementById('List').rows.length;
	var table;
	   if (document.all)
		 table = document.all.uploadtable;
	   else if (document.getElementById)
		 table = document.getElementById('List');
	   if (table && table.rows && table.insertRow) {
	   var newID = table.rows.length;
		 var tr = table.insertRow(newID);

	
	rerToo = 700 + rer;//q&d hack to get around the chance of a board having an ID greater than the current rowcount, thus causing Issues
	tr.innerHTML = '<td><input type="text" class="nameBox" style="width:15%" onclick="this.value=\'\'; this.onclick=\'\';" id="IDNum' + rerToo + '" name="IDNum' + rerToo + '" value="User ID" /> <input type="text" class="nameBox" style="width:75%" name="ReasonNum' + rerToo + '" value="Reason for ban and rough duration" onclick="this.value=\'\'; this.onclick=\'\';" /></td><td></td><td style="width:1%;"><img id="' + rerToo + '" name="' + rerToo + '" src="static/images/forum/Cross.png" alt="Delete?" onclick="Purge(this, ' + rerToo + ', \'this ban\');" /><input type="hidden" name="Made' + rerToo + '" value="1" /><input type="hidden" name="DelNum' + rerToo + '" id="DelNum' + rerToo + '" value="Preserve" /></td>';
	}
}



function Shift(Ego,Bump) {
//Ego = 'this' - the input item in question
//SEgo = this.parentNode
//Id = input.parentnode.rowindex
   while (Ego.parentNode && 'tr' != Ego.nodeName.toLowerCase()) {
     Ego = Ego.parentNode;
   }
   var SEgo = Ego.parentNode;
   var Id = Ego.rowIndex + Bump;

   if (Id<0) { Id += SEgo.rows.length; } //if off the top, add table length to the offset, effectively wrapping around
   if (Id==SEgo.rows.length) { Id = 0; }
//// prob wrong place, but try to swap class with the class of the row at that position currently
	//alert(Ego.className);
   SEgo.removeChild(Ego);
   var nRow = SEgo.insertRow(Id);
   SEgo.replaceChild(Ego, nRow);
}

function sortEm(tehinput) {
	rowCount = document.getElementById('List').rows.length;
	x=0;
	while ((x < rowCount) && (document.getElementById('List').className == 'board')) {
		if (document.getElementById('List').rows[x].childNodes[1].nodeType == 3) {
		document.getElementById('List').rows[x].childNodes[2].lastChild.value = x;
		} else {
		document.getElementById('List').rows[x].childNodes[1].lastChild.value = x;
		}
		x++;
	}
	document.getElementById('totalRows').value = rowCount;
	return submitForm(tehinput.parentNode);
}
