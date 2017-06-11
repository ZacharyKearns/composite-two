$(function() {
   $("#geocomplete").geocomplete({
      details: "#new-location",
      detailsAttribute: "data-geo"
   })
   .bind("geocode:result", function(event, result) {
      $("#locality, #state, #country").val('');
      var components = result.address_components;
      for(var i = 0; i < components.length; i++) {
         if (components[i]["types"].indexOf("locality") > -1) {
            var locality = components[i]["long_name"];
            $("#locality").val(locality);
         }
         if (components[i]["types"].indexOf("administrative_area_level_1") > -1) {
            var state = components[i]["long_name"];
            $("#state").val(state);
         }
         if (components[i]["types"].indexOf("country") > -1) {
            var country = components[i]["long_name"];
            $("#country").val(country);
         }
      }
   });
});
