$(document).ready(function() {
			
			$.ajax({
			    url : '@routes.StaticFilesController.getTemplate("game/index")',
			    dataType : 'script',
			    success : function (data,stato) {
			    	var template = new Hogan.Template(data);
					var data = {
					        text: 'Hello World'
					      };
					
					$("#content").html(template.render(data));
			    },
			    error : function (richiesta,stato,errori) {
			        alert("E' evvenuto un errore. Il stato della chiamata: "+stato +":"+errori);
			    }
			});
	});