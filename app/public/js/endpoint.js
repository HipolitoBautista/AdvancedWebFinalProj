$(document).ready(function(){

  var SelectedMethod = $(".methodSelected").val();

  function updateEndpoint(){

    switch(SelectedMethod){
      case "GET":
          $(".RequestBody").addClass('d-none');
          $(".InputtedID").removeClass('d-none');
          if(endpoint == 'applicants' || endpoint == 'applications'){
            $('.endpointDescription').html("This endpoint can be used as a singleton or collection. The input box can be empty or filled.");
          }else{
            $('.endpointDescription').html("This endpoint is a singleton endpoint, Input box must be filled");
          }
          break;
      case "POST":
          $(".RequestBody").removeClass('d-none');
          $(".InputtedID").addClass('d-none');
          $('.endpointDescription').html("This endpoint is a collection endpoint, It does not accept an ID input from the input box.");
          break;
      case "PUT":
          $(".RequestBody").removeClass('d-none');
          $(".InputtedID").removeClass('d-none');
          $('.endpointDescription').html("This endpoint is a singleton endpoint, Input box must be filled");
          break;
      case "DELETE":
        $(".RequestBody").addClass('d-none');
        $(".InputtedID").removeClass('d-none');
        $('.endpointDescription').html("This endpoint is a singleton endpoint, Input box must be filled"); 
          break;    
       
    }
  }
    
    var urlBroken = $(".urlIdentification").html().split("/");
    var endpoint = urlBroken[urlBroken.length - 1];
    
    var InputtedData = "";
    var InputtedID = 0;

    updateEndpoint();
    
    $(".methodSelected").change(function() {
      SelectedMethod = $(this).val();
      updateEndpoint();
    });

    
     $(".InputtedID").change(function() {
      InputtedID = $(this).val();
    });

  
    $(".btn-TestEndpoint").click(function(e) {
        e.preventDefault();
   
          if($(".inputtedData").val()) {
            InputtedData = JSON.stringify(JSON.parse($(".inputtedData").val()));
          }

          var URL = location.protocol + "//" + location.host + "/endpoints/" + endpoint; 
          if(InputtedID != 0){
            URL = URL + "/" + InputtedID
          }

        $.ajax ({
            url: URL, 
            data: InputtedData,
            type: SelectedMethod,
            processData: false,
            contentType: "application/json",
            success: function(result){
              $('.Response').html(result);
              console.log(result);
            },
            error: function(request, status, error) {
              console.log(error);
            }

        });
    });
}); 