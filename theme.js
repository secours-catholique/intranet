$(document).ready(function() {

$(".menu-conteneur ul ul ").css({display: "none"}); 

$(".menu-conteneur li").hover(function(){
	$(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn(333);
 },function(){
	$(this).find('ul:first').css({visibility: "hidden"});
   });


$(".menu-conteneur ul ul ").parent("li").addClass('plus'); 

// personnalisation
$(".rubrique_conception .liste.sites .description .introduction").hide();
$(".rubrique_conception .liste.sites .item").hover(
  function () {
    $(this).find(".introduction").slideDown('slow');
  },
  function () {
    $(this).find(".introduction").slideUp('slow');
  }
);
});

