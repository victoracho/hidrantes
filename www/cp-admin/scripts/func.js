conf = function (str){ return confirm('Realmente deseas :\n'+str);}
$(document).ready(function(){
	
	/*------------------------------------- LOGIN -------------------------------------*/
	/* Efectos de mensajes */
	$("#login #warning").fadeOut(5000);
    
    
    /* Mostrar div de recordatorio de contraseña */
    $("a.link_forgot").toggle(
    	function(){$("#forgot").fadeIn(1000);},
    	function(){
    		$("#forgot").fadeOut(1000);
    		$("p.ok").hide();
    		$('#email').removeAttr("disabled");
    		$('#b_forgot').removeAttr("disabled");
    		$('#email').val('')}
    	)
	
	/* Recordar contraseñaa */
	$(function(){
	    var url=$("#located_url").val();
		$("#b_forgot").click(function(){
				$.post(url,{email: $("#email").val()}, function(j){
				// Ejecuta el php, pero no hace nada mas
				})		
				$('#email').attr("disabled", true);
				$('#b_forgot').attr("disabled", true);
				$("p.ok").fadeIn(1000);
		})
	})    
    

 	/*------------------------------------- TASAS -------------------------------------*/
   	/* Ordenar las tablas */
  	$("#table-list-tasas").tablesorter({ headers: { 0: { sorter: false} } }); 
    
    /* Seleccionar todos los check */
	$("#check_all").click(function()
			{
				var checked_status = this.checked;
				$("input[@class=list_del]").each(function()
					{
					this.checked = checked_status;
					});
			});      
  
    /* Cerrar/abrir capas */
    $("#div_parte").click(function () {
        $("#table-parte").toggle();
      });

    $("#a_tasa").click(function () {
        $("#div_tasa").toggle();
      });  
    
    /* Mostrar ocultar select no facturable */
    
    $("select#status").change(function () {
        	if ($(this).val() == 5) {
        		$("#nofac").show();
        	}
        	else $("#nofac").hide();
        		
      }); 
});
function eliminar(id)
{
if(!confirm(" ¿Esta seguro de eliminar la foto?")) {return false;} 
$("#foto_id").val(id);
$("#frmupload").submit();
}
function eliminarfichero(id)
{
if(!confirm(" ¿Esta seguro de eliminar el fichero?")) {return false;} 
$("#fichero_id").val(id);
$("#frmupload").submit();
}



    
    function initialize1(lat,lon) {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng(lat, lon), 17);
		map.addControl(new GMapTypeControl());
        var latlng = new GLatLng(lat,lon);
        map.addOverlay(new GMarker(latlng));
   	    map.setMapType(G_HYBRID_MAP);
		map.addControl(new GSmallMapControl());


      }
    }

 function initializestreetview(lat,lon) {
      var fenwayPark = new GLatLng(lat,lon);
      panoramaOptions = { latlng:fenwayPark };
      myPano = new GStreetviewPanorama(document.getElementById("pano"), panoramaOptions);
      GEvent.addListener(myPano, "error", handleNoFlash);
    }
    
    function handleNoFlash(errorCode) {
      if (errorCode == FLASH_UNAVAILABLE) {
        alert("Error: Flash doesn't appear to be supported by your browser");
        return;
      }
    }  

 /* Fin document ready */  