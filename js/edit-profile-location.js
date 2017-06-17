$(function() {
   // initialize geocomplete
   $("#geocomplete").geocomplete({
      details: "#new-location",
      detailsAttribute: "data-geo"
   }) // geocomplete triggers geocode:result event
   .bind("geocode:result", function(event, result) { // bind event handler for geocode:result event
      // reset form values
      $("#locality, #state, #country").val('');

      var components = result.address_components;

      // loop through array set input values
      // using result data
      for(var i = 0; i < components.length; i++) {
         // set locality input value
         if (components[i]["types"].indexOf("locality") > -1) {
            var locality = components[i]["long_name"];
            $("#locality").val(locality);
         }

         // set state input value
         if (components[i]["types"].indexOf("administrative_area_level_1") > -1) {
            var state = components[i]["long_name"];
            $("#state").val(state);
         }

         // set country input value
         if (components[i]["types"].indexOf("country") > -1) {
            var country = components[i]["long_name"];
            $("#country").val(country);
         }
      }
   });
});
