var connect, form, formulario, boton, result, user, pass;
boton 		= document.querySelector('button');
formulario  = document.querySelector('form');

/**
 * 	Capturamos el evento del click en el boton de enviar.
 * 	Capturamos el evento del presionar tecla enter.
 */
boton.addEventListener('click', goLogin);
formulario.addEventListener('keypress', runScriptLogin);

function goLogin() {
	user 	= document.querySelector('#usuario').value;
	pass 	= document.querySelector('#clave').value;
  	sesion 	= document.querySelector('#sesion').checked ? true : false;
  	if (user != '') {
  		if (pass != '') {  			
		  	form = 'user=' + user + '&pass=' + pass + '&sesion=' + sesion;
		  	console.log(form);
		  	connect = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
		  	connect.onreadystatechange = function() {
				if(connect.readyState == 4 && connect.status == 200) {
				    if(connect.responseText == 1) {
				      	result = '<div class="alert alert-dismissible alert-success">';
				      	result += '<strong>Conectado!</strong><br />';
				      	result += 'Estamos redireccionandote <img src="views/app/images/loading1.gif" heigth="60%" alt="..." />';
				      	result += '</div>';
				      	document.getElementById('_AJAX_LOGIN_').innerHTML = result;
				      	location.reload();
				    } else {
						document.getElementById('_AJAX_LOGIN_').innerHTML = connect.responseText;
					}
				} else if(connect.readyState != 4) {
		      		result = '<div class="alert alert-dismissible alert-warning">';
		      		result += '<button type="button" class="close" data-dismiss="alert">x</button>';
		      		result += '<strong>Procesando </strong><img src="views/app/images/loading1.gif" heigth="60%" alt="..." />';
		      		result += '</div>';
		      		document.getElementById('_AJAX_LOGIN_').innerHTML = result;
		    	}
		  	}
			connect.open('POST','ajax.php?mode=login',true);
		  	connect.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		  	connect.send(form);
		} else {
			result = '<div class="alert alert-dismissible alert-danger">';
		    result += '<button type="button" class="close" data-dismiss="alert">x</button>';
			result += '<strong>Error:</strong> ';
			result += 'Debe ingresar una contraseña';
			result += '</div>';
			document.getElementById('_AJAX_LOGIN_').innerHTML = result;
		}
	} else {
		result = '<div class="alert alert-dismissible alert-danger">';
	    result += '<button type="button" class="close" data-dismiss="alert">x</button>';
		result += '<strong>Error:</strong> ';
		result += 'Debe ingresar su nombre de usuario y/o email';
		result += '</div>';
		document.getElementById('_AJAX_LOGIN_').innerHTML = result;
	}
}

function runScriptLogin(e) {
	if(e.keyCode == 13) { //13 corresponde al boton enter o intro del teclado en Ascii
		goLogin();
	}
}