 /**
 * @file style.js
 * @brief Fonctions Javascript pour une meilleure gestion de la page d'affichage
 */

$(document).ready(function(){

$(".operateurText").autocomplete({
//  source: "http://localhost/apiNumerotation/Exemples/recherche-web/autocomplete.php",
source: function(request, response){
  $.ajax({
    url: "http://localhost/apiNumerotation/Exemples/recherche-web/autocomplete.php",
    data: request,
    dataType:"json",
    type:"POST",
    success: function(data){
      response(data);
    }
  });
},
/*   source: [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ],*/
  minLength: 1,
 });

// Si aucun bouton n'est coché, on n'affiche pas le champ d'entrée de texte
$(".specificField").hide();

// On affiche le champ correspondant au bouton radio coché
$(".radioSelect").each(function(){
    showSpecificFields(this);
});

// On affiche le champ correspondant au bouton radio coché
$(".radioSelect").click(function(){
   showSpecificFields(this);
});

 /** 
 * Affiche un objet avec un id particulier en fonction du bouton radio coché
 * @param obj l'objet dont on veut contrôler s'il est coché ou non
 */
function showSpecificFields(obj){
    if($(obj).is(":checked")){
    var radioVal = $(obj).val();
     $(".specificField").each(function(){
         if($(this).attr('id') == radioVal){
             $(this).show();
         } else{
             $(this).hide();                 
         }
     });   
    }
}
});