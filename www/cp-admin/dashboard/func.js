
$(function() {
		
	var thumbLinks = $('#thumb a');
	var thumbCount = thumbLinks.length;
	var thumbImages = $('#thumb img');
	var image = $("#main_img");
	var fadeSpeed = "fast";

	image.animate({opacity:0}, 'fast')
	setTimeout("$('#main_img').animate({opacity:1}, 'fast')", 800);

	thumbLinks.each(function(i) {

		var current = (i+1);
	

			this.onclick = function () {

				var imageUrl = this.getAttribute('href'); 
					
				var imageDescription = $(this).find("img").attr("alt");
				
				image.animate({opacity:0}, 'slow', function() {

					$("#main_img img").attr({ src: imageUrl, alt: imageDescription});

					setTimeout("$('#main_img').animate({opacity:1}, 'fast')", 800);
					
					$("#main_img p.nombre").html(imageDescription);
					
				})

				return false;	
			}
	})
});	


    function initializeMap(lat,lon) {
         var myLatlng = new google.maps.LatLng(lat,lon);
        var myOptions = {
        center: myLatlng,
        zoom: 17,
        mapTypeId: google.maps.MapTypeId.HYBRID
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            myOptions);  
        var marker = new google.maps.Marker({
            position: myLatlng,
            map: map,
            title:"Localización"
        });            
    }

    function initializeStreetView(lat,lon) {
        var myLatlng = new google.maps.LatLng(lat,lon);
        var panoramaOptions = {
        position: myLatlng
        };
        var panorama = new  google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
    }
    
    
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 


function isEmpty(obj) {
    var name;
    for (name in obj) {
        return false;
    }
    return true;
}


    function mailReturn(message) {
        $('#mail_return_content').html(message);
        $('#mail_return_link').trigger('click');        
    }

    function mail() {
        //Recoger datos del formulario
        var form = $('.mail_form:visible :input')
        var values = {};
        form.each(function(){
           values[this.name] = $(this).attr('value');
        });
        $('input[type=hidden]').each(function(){
            values[this.name] = $(this).attr('value');
        });
        //Validar datos
        $("label[for='To']").removeClass('error');
        $("label[for='Subject']").removeClass('error');
        $("label[for='Message']").removeClass('error');        
        var error = {};
        if (!validateEmail(values['To']))
            error['To'] = 'Email incorrecto';
        if (values['Subject'] == '')
            error['Subject'] = 'El asunto no puede ser vacío';
        if (values['Message'] == '')
            error['Message'] = 'El mensaje no puede ser vacío';
        //Si no hay errores proceder a enviar el correo
        if (isEmpty(error)) {
            $(document).trigger('close.facebox');
            $.blockUI({ message: '<h1 class="blockUI_message">Espere...</h1>' });  
            //Enviar datos por ajax
            $.ajax({
                type: "POST",
                url: path+"ajax/mail/",
                dataType: "json",
                traditional: true,
                data: {
                    'To': values['To'],
                    'Subject': values['Subject'],
                    'Message': values['Message'],
                    'tipo' : values['tipo'],
                    'id' : values['id']
                },
                success: function(data){
                    $.unblockUI(); 
                    if (data.error == undefined) {
                        //Se envio el correo correctamente
                        message = '<p>El correo se envió correctamente</p>';
                        setTimeout("mailReturn('"+message+"','success')",400);
                    }
                    else {
                        //Hubo error en el correo
                        message = '<p>Se produjo un error al enviar el correo</p>';
                        setTimeout("mailReturn('"+message+"','error')",400);                        
                    }
                }                
            });
        }
        //Mostrar los errores
        else {
            if (error['To'] != undefined)
                $("label[for='To']").addClass('error');
            if (error['Subject'] != undefined)
                $("label[for='Subject']").addClass('error');
            if (error['Message'] != undefined)
                $("label[for='Message']").addClass('error');            
        }
    }