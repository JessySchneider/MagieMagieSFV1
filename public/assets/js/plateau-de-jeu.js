/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$( document ).ready(function() {

    var cartesJouees = new Array();
    var carteDonnee;
    var joueurCible = "";
    var aLaMain;

    if($('.lancer-sort').length){
        
        setInterval(aLaMain, 3000);
        setInterval(getCarteJoueurActuel, 3000);

    }

    $('body').on("click",".joueur-adverse img",setJoueurCible);

    function setJoueurCible(){
        
        joueurCible = $(this).attr('value');
        canICastASpell(cartesJouees);
    }

    if($('.passer-tour').length){

        $('.passer-tour').on("click",passerTour);
        function passerTour(){
            $.ajax({
                type: "POST",
                url: '/passerTour',
                data: {}
            }).done(function(data){
          
            });
        }
    }


    function getCarteJoueurActuel(){
        if(aLaMain == false){
            $('.joueur-bottom .container-avatar-joueur-plateau').removeClass('a-la-main');
            $('.passer-tour').css("height","0px").css("opacity","0");
            $('.message').addClass("pas-mon-tour").removeClass("mon-tour");
            $('.message').html("Pas a vous de jouer").css("height","40px").css("opacity","1");
            $('.joueur-bottom').load('/getCarteJoueurActuel');
            
        }else{
            $('.joueur-bottom .container-avatar-joueur-plateau').addClass('a-la-main');
            $('.message').addClass("mon-tour").removeClass("pas-mon-tour");
            $('.message').html("A vous de jouer").css("height","40px").css("opacity","1");
            $('.passer-tour').css("height","40px").css("opacity","1");
            $('.adversaire-top').load('/getCarteJoueursAdverses');
        }
    }


    function aLaMain(){
        
        $.ajax({
            type: "POST",
            url: '/aLaMain',
            data: {}
        }).done(function(data){
            // console.log(data);
            if(data){
                aLaMain = true;
            }else{
                aLaMain = false;
            }
        });
    }

    $('body').on('click', '.carte-joueur img', function(){
        if(aLaMain == true){
            var idCarte = $(this).attr("value");

            if(cartesJouees.length == 2){
                $(this).toggleClass("selected-bis");
                carteDonnee = $(this).attr("value");
            }     

            if($.inArray(idCarte,cartesJouees) == -1){
                if(cartesJouees.length < 2){
                    cartesJouees.push(idCarte);
                    $(this).toggleClass("selected");
                }
            }else{
                var indice = cartesJouees.indexOf(idCarte);
                cartesJouees.splice(indice, 1);
                $(this).toggleClass("selected");
            }
            
                 
            canICastASpell(cartesJouees);
        }
    });

    function canICastASpell(cartes){
        if(cartes.length == 2){
            $.ajax({
                type: "POST",
                url: '/castASpell',
                data: {"cartes" : cartes}
            }).done(function(data){
                console.log(data);
                if(data != "Sort invalide"){
                    if(data == "Hypnose" || data == "Philtre d'amour"){
                        if(joueurCible == ""){
                            $('.message-cible').css("height","40px").css("opacity","1")
                            return false;
                        }else{
                           
                            $('.lancer-sort').html("Lançer sort : "+data).css("height","40px").css("opacity","1");
                            $('.message-cible').css("height","0px").css("opacity","0")
                            return true;
                        }
                      
                    }else{
                        $('.lancer-sort').html("Lançer sort : "+data).css("height","40px").css("opacity","1");
                        return true;
                    }
                }
                return false;   
            });
        }else{
            $('.lancer-sort').html("").css("height","0px").css("opacity","0");
        }
        
    }

    $('.lancer-sort').on("click",castSpell);

    function castSpell(){
        $.ajax({
            type: "POST",
            url: '/lancerSort',
            data: {"cartes" : cartesJouees , "cible" :joueurCible, "carteDonnee":carteDonnee}
        }).done(function(){
            console.log("Sort lançer");
            $('.lancer-sort').css("height","0px").css("opacity","0");
        });
    }

 
    $('.carte-joueur img').on("click",function(){
            if($(this).hasClass("clicked-card") === false){
                $(this).addClass("clicked-card");
            }else{
                $(this).removeClass("clicked-card");
            }
        
    });

});