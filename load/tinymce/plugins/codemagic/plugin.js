/**
 * @author Josh Lobe
 * http://ultimatetinymcepro.com
 *
 * changed by KIMB-technologies
 * http://kimb-technologies.eu/
 */
 
jQuery(document).ready(function($) {


	tinymce.PluginManager.add('codemagic', function(editor, url) {
		
		
		editor.addButton('codemagic', {
			
			icon: 'code',
			tooltip: 'Quelltext',
			onclick: open_codemagic
		});
		
		function open_codemagic() {
			
			editor.windowManager.open({
					
				title: 'Quelltext',
				width: 900,
				height: 700,
				url: url+'/codemagic.htm'
			})
		}
		
	});
});
