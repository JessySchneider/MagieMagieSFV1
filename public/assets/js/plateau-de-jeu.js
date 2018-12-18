/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var maximumAngle = 70;
var cartesJoueur = $('.carte-joueur img').length;
var iJoueur = -(Math.floor(cartesJoueur/2));
var angleJoueur = maximumAngle/cartesJoueur;

var cartesAdversaire = $('.carte-adversaire img').length;
var iAdversaire = -(Math.floor(cartesAdversaire/2));
var angleAdversaire = maximumAngle/cartesAdversaire;

$('.carte-joueur img').each(function(){
    $(this).css("transform","rotate("+angleJoueur*iJoueur+"deg)");
    iJoueur++;
});

$('.carte-adversaire img').each(function(){
var angleAdversaire = maximumAngle/cartesAdversaire;
$(this).css("transform","rotate("+angleAdversaire*iAdversaire+"deg)");
    iAdversaire++;
});

// var rad = 16.65;
// $('.carte-joueur img').each(function(){
    
//     // posX = Math.round((Math.cos(rad)*300)+700);
//     // posY = Math.round((Math.sin(rad)*300)+770);
//     $(this).css("transform","rotate("+angle*i+"deg)");
//     // $(this).css("top",posY+"px");
//     // $(this).css("left",posX+"px");
//      i++;
    
//     // if(cartes < 5){
//     //     rad = rad +(1/cartes);
//     // }else{
//     //      rad = rad + (1.5/cartes);
//     // }
   

// });


$('.carte-joueur img').on("click",function(){
        if($(this).hasClass("clicked-card") === false){
            $(this).addClass("clicked-card");
        }else{
            $(this).removeClass("clicked-card");
        }
    
});

