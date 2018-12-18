/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$( document ).ready(function() {

    $("#formConnexion").on("submit",function(event){
        event.preventDefault();
        getConnexionInformation();
    });

    $("img").on("click",function(){

        $("img").each(function(){
            $(this).removeClass("imagetoggle");
        });
      
        $(this).toggleClass("imagetoggle");
    });
        

    function getConnexionInformation(){
        var pseudo = $("#utilisateur").val();
        var avatar = $(".imagetoggle").attr('id');

        if(pseudo !== "" && avatar != "undefined"){
            connexionInit(pseudo,avatar);
        }else{
            var message = "Veuillez renseigner un pseudo et/ou un avatar";
            $('.message').html(message).css("height","40px").css("padding","10px");
            $('.message').append('<i class="fas fa-times-circle icone-message"></i>').css("opacity","1");
        } 
    }

    function connexionInit(pseudo,avatar){
        $.ajax({
            type: "POST",
            url: '/connexionInit',
            data: { pseudo: pseudo, avatar: avatar }
          }).done(function() {
                window.location = "/liste-partie";
          });
    }

});


