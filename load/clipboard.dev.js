/* KIMB Downloader -- Copyright 2015 by https://www.KIMB-technologies.eu --  https://www.gnu.org/licenses/gpl-3.0 */

function delfile( art , del , path, name ) {
	if( art == "folder" ){
			$( "#filemanagerdel" ).html( "<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Möchten Sie den Ordner &apos;" + name + "&apos; wirklich löschen?</p>" );
	}
	else{
		$( "#filemanagerdel" ).html( "<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Möchten Sie die Datei &apos;" + name + "&apos; wirklich löschen?</p>" );
	}
	$( "#filemanagerdel" ).css( "display", "block" );
	$( "#filemanagerdel" ).dialog({
	resizable: false,
	height:200,
	modal: true,
	buttons: {
		"Löschen": function() {
			$( this ).dialog( "close" );
			window.location = allgsys_siteurl + "/backend.php?todo=explorer&del=" + del + "&art=" + art + "&path=" + path ;
			return true;
		},
		"Abbrechen": function() {
			$( this ).dialog( "close" );
			return false;
		}
	}
	});
}

//Daten für Clipboard aus Storage laden
if( typeof localStorage.getItem( "clipboard" ) === "string" ){
	var clipboard = JSON.parse( localStorage.getItem( "clipboard" ) );
}
else{
	var clipboard = [];
}
//beim Verlassen in Storage speichern
$( window ).unload(function() {
	localStorage.setItem("clipboard", JSON.stringify( clipboard ) );	
});

//Funktionen fürs Clipboard

//Datei hinzuzfügen
function add_file_clipboard( file, path ){
	var add = {};
	var doit = true;
	
	$.each( clipboard, function( id, obj ) {
		if( path == obj.path ){
			doit = false;
		}
	});
	
	if( doit ){
		add.file = file;
		add.path = path;
		
		clipboard.push(add);
	}
	
	show_clipboard();
	
	return true;
}

//anzeigen
function show_clipboard(){

	var html = "<table width='100%'>";
	var done = false;

	$.each( clipboard, function( id, obj ) {
		html += "<tr>";
		html += "<td title='" + decodeURIComponent( obj.path ) + "'>" + obj.file + "</td>";
		html += "<td> <span class='ui-icon ui-icon-trash' title='Datei aus Zwischenablage löschen' style='display:inline-block;' onclick='del_clipboard( id );'></span> </td>";
		html += "<td> <span class='ui-icon ui-icon-pin-s' title='Datei in den aktuellen Ordner setzen (kopieren oder verschieben)' style='display:inline-block;' onclick='rename_copy_clipboard( \"" + obj.path + "\", \"" + obj.file + "\" , " + id + " );'></span></td>";
		html += "</tr>";
		done = true;
	});
	
	if( !done ){
		html += "<i>Keine Elemente</i>";
	}
	
	html += "</table>";

	$( "div#clipboard" ).html( html );

	$( "div#clipboard" ).css( "display", "block" );
	$( "div#clipboard" ).dialog({
	resizable: true,
	modal: true,
	buttons: {
		"Schließen": function() {
			$( this ).dialog( "close" );
		}
	}
	});
}

//Datei löschen
function del_clipboard( id ){

	clipboard.splice( id, 1);

	$( "div#clipboard" ).dialog( "close" );
	show_clipboard();
}

//Datei schon im akuellen Ordner?
//	Warnung anzeigen, dass Datei ersetzt wird!
function new_name_clipboard(){
	//php_pathfiles (Array mit allen Dateien)
	
	var newname = $( "input[name=clipboard_file]" ).val();
	var found = false;
	
	$.each( php_pathfiles, function( id, dat ) {
		if( dat == newname ){
			found = true;
		}
	});
	
	if( found ){
		$( "i#clipboard_replaceatt" ).css( 'display','inline-block' );
	}
	else{
		$( "i#clipboard_replaceatt" ).css( 'display','none' );
	}	
}

//Datei verschieben
//	datei => Pfad zu Datei (relativ zu /files/)
//	name => aktueller Name der Datei
function rename_copy_clipboard( datei, name, id ){
	var nach = php_pathnow;
	
	var html = '<h3>Verschieben &amp; kopieren</h3>';
	html += '<form action="' + allgsys_siteurl + '/backend.php?todo=explorer&amp;path=' + nach + '" method="post" id="clipboard_form">';
	html += '<input type="text" readonly="readonly" name="clipboard_oldpath"> (Dateiquelle)<br />';
	html += '<input type="text" readonly="readonly" name="clipboard_newpath"> (Dateiziel)<br />';
	html += '<input type="text" name="clipboard_file" onkeyup="new_name_clipboard();" onchange="new_name_clipboard();" > (Dateiname)<br />';
	html += '<i id="clipboard_replaceatt" style="display:none; color:red;><span class="ui-icon ui-icon-info"  title="Die Datei wird überschrieben, sofern Sie auf Los klicken!" style="display:inline-block;"></span> Dateiname schon vergeben</i><br />';
	html += '<input type="radio" name="clipboard_art" value="rename" checked="checked">  (verschieben)<br />';
	html += '<input type="radio" name="clipboard_art" value="copy"> (kopieren) ';
	html += '</form>';
	
	$( "div#clipboard" ).html( html );
	
	$( "div#clipboard" ).dialog( "option", "buttons", [
		{
			text: "Los", click: function() {
				clipboard.splice( id, 1);
				$( "form#clipboard_form" ).submit();
			}
		 },
		 {
			text: "Abbrechen", click: function() {
				$( this ).dialog( "close" ); 
			}
		}
	] );
	
	$( "input[name=clipboard_file]" ).val( decodeURIComponent( name ) );
	$( "input[name=clipboard_oldpath]" ).val( decodeURIComponent( datei ) );
	$( "input[name=clipboard_newpath]" ).val( decodeURIComponent( nach ) );
	
	new_name_clipboard();
}