
(function() {
	tinymce.create('tinymce.plugins.BlankPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('blank_command', function() {

                            var $blank = tinyMCE.activeEditor.selection.getContent();
                            if (tinyMCE.activeEditor.selection.getNode().nodeName == 'INPUT' && tinyMCE.activeEditor.selection.getNode().type == 'text')
                            {
                                var $id = tinymce.activeEditor.selection.getNode().id;
                                var $val = tinymce.activeEditor.selection.getNode().value;
                                tinyMCE.activeEditor.selection.setContent($val);
                                $('#delete_hole_'+$id).click();
                            }
                            else
                            {
                                var $nbHole = tinyMCE.activeEditor.dom.select('input').length;
                                var $indexHole = 1;
                                if($nbHole > 0)
                                {
                                    tinymce.each(tinyMCE.activeEditor.dom.select('input'), function(n) {
                                        //alert(n.id);
                                        if($indexHole <= n.id)
                                        {
                                            $indexHole = parseInt(n.id)+1;
                                        }
                                    });

                                }
                                var el = tinyMCE.activeEditor.dom.create('input', {'id' : $indexHole, 'type' : 'text', 'size' : '15', 'value' : $blank, 'class' : 'blank'});
                                tinyMCE.activeEditor.selection.setNode(el);
                                
                                add_hole('add','../../../../bundles/ujmexo/images/ajouter_ligne.jpeg',$indexHole,$blank);
                            }
			});

			// Register buttons
			ed.addButton('blankButton', {title : 'pluginName.stringName', cmd : 'blank_command', image: url + '/img/blank.gif'});
		},

		getInfo : function() {
			return {
				longname : 'Blank',
				author : 'Université de Saint-Etienne, DSI, Equipe de développement du Pôle TICE',
				authorurl : 'http://univ-st-etienne.fr',
				infourl : '',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
                        
	});

	// Register plugin
	tinymce.PluginManager.add('blank', tinymce.plugins.BlankPlugin);
})();