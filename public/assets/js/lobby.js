/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if($('.lobby').length > 0 ){
    setInterval(listerJoueurLobby,3000);
    setInterval(rejoindrePartie,1000);
}

function listerJoueurLobby(){
    $('.lobby').load('/lobby');
}

$('.demarrer-partie').on('click',function(){
    
    $.ajax({
        type: "POST",
        url: '/plateauDeJeu',
        data:{} 
    }).done(function(){
        // window.location = "/plateauDeJeu";
    });
});



function rejoindrePartie(){
    $.ajax({
        type: "POST",
        url: '/etatPartie',
        data: {}
    }).done(function(data){
        console.log(data);
        if(data == "DEMARREhE"){
            window.location = "/plateauDeJeu";   
        }        
    });
}