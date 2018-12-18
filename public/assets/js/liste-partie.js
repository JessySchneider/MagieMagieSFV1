/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$( document ).ready(function() {
    $('.creer-une-partie').on("click",createGame);

    $('body').on('click', '.rejoindre-partie', joinGame);


    if($('.creer-une-partie').length > 0 ){
        setInterval(listerPartie,2000);
    }
        
    function listerPartie(){
        $('.container-listes-parties').load('/ajaxListePartie');
    }

 

    function joinGame(){
        var idPartie = $(this).val();
        $.ajax({
            type: "POST",
            url: '/rejoindrePartie',
            data: { idPartie: idPartie }
        }).done(function(){
            window.location = "/lobbyVide";
        });
    }

    function createGame(event){
        event.preventDefault();
        var nomPartie = $("input[name=nomPartie]").val();      
        if(nomPartie.length > 0 ){
            $.ajax({
                type: "POST",
                url: '/creerPartie',
                data: { nom: nomPartie }
            }).done(function() {
                    $("#message").html("<div class='message-succes'>Votre partie : "+nomPartie+" a bien été créée <i class='fas fa-times-circle icone-message'></i></div>");
                    $("#message").css("opacity","1").css('height','40px');
            });
        }else{
            $("#message").html("<div class='message-error'>Veuillez renseigner un nom de partie <i class='fas fa-times-circle icone-message'></i></div>");
            $("#message").css("opacity",'1').css('height','40px');
        }
    }

    $(document).on("click",".icone-message",removeMessageFromDOM);
    function removeMessageFromDOM(){
        $('#message').css('opacity','0').css("height",'0px'); 
    }
});


   