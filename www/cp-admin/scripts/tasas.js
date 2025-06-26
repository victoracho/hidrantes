conf = function (str){ return confirm('Realmente deseas :\n'+str);}

/*------------------------------------- INICIO DOCUMENT READY -------------------------------------*/
$(document).ready(function(){
		 
	 $(".check").click(function () {
		 if ($(this).val()=="si")
		 {
			 $(".div_tasas").show();
		 }
		 else 
		 {
			 $(".div_tasas").hide();
		 }
	    });
	 
	 
	 
	  	/* Aplicar foco al input de stock */
	  	$(".tTasas input").focus(function () {
	 		$(".tTasas input").each(function()
				{
				 $(this).removeClass();
				});
	         $(this).addClass("focus");                                          
	    });
	    
	    $(".tTasas input").keyup(function(){    		    	   	
	    	
	    	var datos = this.id;
	    	datos = datos.split("_");
	    	var cat = datos[0];
	    	var mul = datos[1];
	    	var id = datos[2];
	    	
	    	/*Validaciones */
	    	if ((cat == "p") || (cat == "v")){
	    		
		    	if (isInteger($(this).val())==false) {
		    		if ($(this).val()!="")
		    		{
			    		alert('No se permiten letras');
			    		$(this).val('');
			    		return false;	  	    			
		    		}  		
		    	}		    		
	    	}
	    	
	    	if (cat == "m") {
	    		if (isDecimal($(this).val())==false) {
		    		if ($(this).val()!="")
		    		{
			    		alert('Sólo se permiten decimales');
			    		$(this).val('');
			    		return false;	  	    			
		    		}  				    			
	    		}
	    	}
	    	if ($("#" + cat + "_m_" + id +"").val()>59)
	    	{
	    		alert('Los minutos no pueden ser más de 59');
	    		$("#" + cat + "_m_" + id +"").val('');
	    		return false;
	    	}
	    	
	    	/* Fin validaciones */
	    	var total_horas = parseInt($("#" + cat + "_h_" + id +"").val()) + parseFloat($("#" + cat + "_m_" + id +"").val()/60);
	    	
	    	var total = parseInt($("#" + cat + "_c_" + id +"").val()) * parseFloat(total_horas);		    			   
	    	
	    	/* Tasas de personal */
	    	if (cat == "p")
	    	{		    		
		    	if (total >=0)
		    	{
		    		// Le tengo que sumar el coste de la tasa.
		    		var ctasa = $("td#ctasa_" +  id).html();
		    		
		    		// Le quiteo el euro
			    	ctasa = ctasa.split(" ");			    		
			    		
			    	// Me quedo con los numeros
			    	ctasa = parseFloat (ctasa[0]);
			    	
		    		total = total * ctasa;			    		
		    		total=redondear (total,3);
		    		
		    		$("#" + cat + "_t_" + id +"").html(total + " &euro;");
		    	}
		    	else
		    	{
		    		$("#" + cat + "_t_" + id +"").html("0" + " &euro;");
		    	}	
		    	
		    	var total_personal = 0;
		    	// Sumo todos los id para dar el total.
		 		$("td.subtotal_personal").each(function()
		 				{			 				 
		 				 total_personal = total_personal + parseFloat($(this).html());
		 				});
		 		
		 		// Le asigno al total
		 		total_personal = redondear (total_personal,3);
		 		
		 		$("#total_personal").html (total_personal + " &euro;");
	    	}
	    	
	    	
	    	/* Tasas de vehiculos */
	    	if (cat == "v")
	    	{
		    	if (total >=0)
		    	{
		    		// Le tengo que sumar el coste de la tasa.
		    		var ctasa = $("td#ctasa_" +  id).html();
		    		
		    		// Le quiteo el euro
			    	ctasa = ctasa.split(" ");			    		
			    		
			    	// Me quedo con los numeros
			    	ctasa = parseFloat (ctasa[0]);
			    	
		    		total = total * ctasa;			    		
		    		total = redondear (total,3);
		    		
		    		$("#" + cat + "_t_" + id +"").html(total + " &euro;");
		    		
		    		/* Kms */
		    		var kms = parseFloat($("#" + cat + "_k_" + id +"").val());
		    		
		    		if (kms >=0 ) kms = kms;
		    		else kms = 0; // Si es NaN			    		
		    		
		    		var item_recorrido = $(".item_recorrido").html();
		    		
		    		// Le quito el euro
		    		item_recorrido = item_recorrido.split (" ");
		    		
		    		
		    		total_kms = kms * parseFloat(item_recorrido[0]);
		    		
		    		total_kms = redondear (total_kms,3);
		    		$("#" + cat + "_kmtotal_" + id +"").html(total_kms + " &euro;");
		    		
		    		/* El subtotal */
		    		var subtotal = 0;
		    		if ( total > 0)
		    		subtotal = total + total_kms;
		    		$("#" + cat + "_sub_" + id +"").html(subtotal + " &euro;");
		    		
		    		
		    	}
		    	else
		    	{
		    		$("#" + cat + "_t_" + id +"").html("0" + " &euro;");
		    	}
		    	
		    	// Total vehiculos.
		    	var total_vehiculos = 0;
		    	// Sumo todos los id para dar el total.
		 		$("td.subtotal_vehiculos").each(function()
		 				{			 				 
		 					total_vehiculos = total_vehiculos + parseFloat($(this).html());
		 				});
		 		
		 		// Le asigno al total
		 		total_vehiculos = redondear (total_vehiculos,3);
		 		
		 		$("#total_vehiculos").html (total_vehiculos + " &euro;");		    	
		    	
	    	}
	    	
	    	/* Materiales */
	    	if (cat == "m")
	    	{
	    		var total = parseFloat($("#" + cat + "_c_" + id +"").val());
	    		
		    	if (total >=0)
		    	{
		    		// Le tengo que sumar el coste de la tasa.
		    		var ctasa = $("td#ctasa_" +  id).html();
		    		
		    		// Le quiteo el euro
			    	ctasa = ctasa.split(" ");			    		
			    		
			    	// Me quedo con los numeros
			    	ctasa = parseFloat (ctasa[0]);
			    	if (id==13) {total = (total / 1000) * ctasa;}
			    	else {
			    		total = total * ctasa;
			    	}
		    		total=redondear (total,3);
		    		
		    		$("#" + cat + "_sub_" + id +"").html(total + " &euro;");
		    	}
		    	else
		    	{
		    		$("#" + cat + "_sub_" + id +"").html("0" + " &euro;");
		    	}	
		    	
		    	var total_materiales = 0;
		    	// Sumo todos los id para dar el total.
		 		$("td.subtotal_materiales").each(function()
		 				{			 				 
		 					total_materiales = total_materiales + parseFloat($(this).html());
		 				});
		 		
		 		// Le asigno al total
		 		total_materiales = redondear (total_materiales,3);
		 		
		 		$("#total_materiales").html (total_materiales + " &euro;");	    		
	    	}
	    	
	    	var total_tasa = parseFloat($("#total_personal").html()) + parseFloat($("#total_vehiculos").html()) + parseFloat($("#total_materiales").html());
	    	$("#total_txt").html( total_tasa + " &euro;");
	    	$("#total_input").val(total_tasa);   		    	
	    	 	
	    });	 
	 
	    
	 /* Envio del formulario de tasas */
	    $("#send").click(function () {	    	
	    	var str = $("#frm_tasas").serialize();
	 		var url=$("#tasa_url").val();  		
	       
			$.post(url,{str: str}, function(j){	  
					$("#init_tasas").html(j);
					$("#send").attr("disabled", true); 
				}
			)
	         	
	    	return false;
	    }
		
		$(".borrafoto").click(function(){
			
			var str = $("#frmupload").serialize();
	 		var url=$("#frm_url").val();  		
	       alert(str + ' ' + url);
			/*$.post(url,{str: str}, function(j){	  
					$("#init_tasas").html(j);
				
				}*/

		
		});
	 )
 })
 /*------------------------------------- FIN DOCUMENT READY -------------------------------------*/
 
 // Trunca el numero 'num' a 'ndec' decimales. 
function trunc(num, ndec) { 
  var fact = Math.pow(10, ndec); // 10 elevado a ndec 

  /* Se desplaza el punto decimal ndec posiciones, 
    se trunca el numero y se vuelve a colocar 
    el punto decimal en su sitio. */ 
  return parseInt(num * fact) / fact; 
}

 // Rendondea los decimales
function redondear(cantidad, decimales) {
	var cantidad = parseFloat(cantidad);
	var decimales = parseFloat(decimales);
	decimales = (!decimales ? 2 : decimales);
	return Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);0
	}

function IsNumeric(expression)  
{  
    return (String(expression).search(/^\d+$/) != -1);  
}  

var isInteger_re     = /^\s*(\+|-)?\d+\s*$/;
function isInteger (s) {
   return String(s).search (isInteger_re) != -1
}

var isDecimal_re     = /^\s*(\+|-)?((\d+(\.\d+)?)|(\.\d+))\s*$/;
function isDecimal (s) {
   return String(s).search (isDecimal_re) != -1
}
function eliminar(id)
{
$("#foto_id").val(id);
$("#frmupload").submit();
}