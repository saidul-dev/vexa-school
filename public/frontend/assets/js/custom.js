$("document").ready(function(){
     //Sticky Header 
     $(window).scroll(function(){
        if ($(document).scrollTop () < 1){
            $(".header-area").removeClass("fixed");
            $(".header-area").removeClass("active-bg");
        }
        else if ($(document).scrollTop () > 300){
            $(".header-area").removeClass("fixed");
            $(".header-area").addClass("active-bg");
        }
        else{
            $(".header-area").addClass("fixed");
        }
    });
   // Toggle Menu
   $(".hambargar-bar").click(function(){
    $(".menu-items").slideToggle();
    return false;
   })
  $(".crose-icon").click(function(){
    $(".menu-items").slideToggle();
    return false;
   })  
   // Service Show
   $('.see-btn').click(function(){
    $('.hide-service').toggleClass('active');
   }) 
   
   
});