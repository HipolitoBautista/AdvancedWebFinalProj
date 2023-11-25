$(document).ready(function(){

    $(".btn-login").click(function(e) {
        e.preventDefault();
        
        var loginData = {};
        $.each($("#login-form").serializeArray(), function(index, field) {
            loginData[field.name] = field.value;
        });

        var jsonData = JSON.stringify(loginData);


        console.log(jsonData);
        $.ajax ({
            url: location.protocol + "//" + location.host + "/login",
            data: jsonData,
            type: "POST",
            processData: false,
            contentType: "application/json",
            success: function(result){
                var response = JSON.parse(result);
                console.log(response.rc);
                if(response.rc != 1){
                    console.log("Error" + result);
                    $('#login-error').removeClass("d-none");
                }else{
                    window.location.href = '/home';
                }
            },
            error: function(request, status, error) {
                console.log(request);
            }

        });
    });
}); 

function isJSON(str){
    try{
        return(JSON.parse(str) && !!str);
    } catch (e) {
        return false;
    }
}