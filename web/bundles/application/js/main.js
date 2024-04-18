// SUB MENU HEADER -> Ouvre les sous-menu du header au passage de la souris 
// Active le menu dÃƒÂ©roulant
/*
$( "header li" ).hover(
  function() {
    $(this).find("ul").addClass( "is-open" );
  }, function() {
    $(this).find("ul").removeClass( "is-open" );
  }
);*/




/* MODAL_SE_CONNECTER -> affiche le modal de connexion lorsqu'on clic sur SE CONNECTER (ou Connectez-vous dans la page d'inscription) */
$("#se_connecter, #deja_membre").click(function(event) {
    if ($('.modal_connexion_container').hasClass("is-open")) {
        $('.modal_connexion_container').removeClass("is-open");
    } else $('.modal_connexion_container').addClass("is-open");
});
$(document).mouseup(function (e){
    var container = $('.modal_connexion_container, #se_connecter, #deja_membre');
    if (!container.is(e.target) && container.has(e.target).length === 0){
        $('.modal_connexion_container').removeClass("is-open");
    }
});


/* MENU DEPLIABLE HOVER -> Selection du parent et coloration en bleu */
$("#button_menu_filtre").hover(
    function() {
        $('.sidebar_container .top').addClass("menu_depliant_hover");
    },
    function() {
        $('.sidebar_container .top').removeClass("menu_depliant_hover");
    }
);

/* MENU DEPLIABLE - CLIC BOUTON -> contenu de la sidebar en display bloc */
$("#button_menu_filtre").click(function() {
    if ($('.sidebar_content').hasClass("is-open")) {
        $('.sidebar_content').removeClass("is-open")
    } else $('.sidebar_content').addClass("is-open");
});




/* AFFICHAGE DES INPUT CORRESPONDANTE (accueil) -> Rayon d'action pour taxi / destination pour demenagement */
// A chaque changement on verifie
// $('.form_container [type=radio]').change(function() {
//     if (document.getElementById('annoncebundle_annonce_categorie_0').checked || document.getElementById('annoncebundle_annonce_categorie_3').checked) {
//         $('.taxi_search').removeClass("is-open");
//         $('.demenagement_search').addClass("is-open");
//     } else if (document.getElementById('annoncebundle_annonce_categorie_1').checked || document.getElementById('annoncebundle_annonce_categorie_2').checked) {
//         $('.demenagement_search').removeClass("is-open");
//         $('.taxi_search').addClass("is-open");

//     }
// });




/* CREER ANNONCE -> Selectionne trajet regulier ou trajet unique */
$(".trajet_selector_container li").click(function() {
    $(".trajet_selector_container li").removeClass("is-selected");
    $(this).addClass("is-selected");
    
    if($(this).hasClass("trajet_unique")){
        $(".trajet_regulier_form").removeClass("is-open");
        $(".trajet_unique_form").addClass("is-open");
    }
    else{
        $(".trajet_unique_form").removeClass("is-open");
        $(".trajet_regulier_form").addClass("is-open");
    }
});

/* CREER ANNONCE DEMENAGEMENT -> Selectionne CREER ou RECHERCHE  dÃƒÂ©menagement */
$(".demenagement_selector li").click(function() {
    $(".demenagement_selector li").removeClass("is-selected");
    $(this).addClass("is-selected");
    
    if($(this).hasClass("creer_demenagement")){
        $(".recherche_demenagement_container").removeClass("is-open");
        $(".creer_demenagement_container").addClass("is-open");
    }
    else{
        $(".creer_demenagement_container").removeClass("is-open");
        $(".recherche_demenagement_container").addClass("is-open");
    }
});


// ANNULER RESERVATION MODAL

$(document).ready(function(){
    $("#annule-reservation-button").click(function(){
        $("#annule-reservation-modal").modal('show');
    });
});