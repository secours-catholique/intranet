$(document).ready(function(){
$(".menu p").hide();
$(".menu h3").click(function(){
$(this).next("p").slideToggle("slow")
.siblings("p:visible").slideUp("slow");
$(this).toggleClass("active");
$(this).siblings("h3").removeClass("active");
});
});
