<footer class="footer foooter text-center">
    <div class="container">
        <div class="row">
            <!-- Footer Location-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="mb-4">LOCATION</h4>
                <p class="pre-wrap lead mb-0">Tel-Aviv, Israel <br>
                    Phone: 05467874354<br>
                    E-mail: Feedback@chefmate.com
                </p>
            </div>
            <!-- Footer Social Icons-->
            <div class="col-lg-4 mb-5 mb-lg-0">
                <h4 class="mb-4">AROUND THE WEB</h4>
                <a class="btn btn-outline-light btn-social mx-1" href="https://www.facebook.com/"><i class="fab fa-fw fa-facebook-f"></i></a>
                <a class="btn btn-outline-light btn-social mx-1" href="https://www.twitter.com/"><i class="fab fa-fw fa-twitter"></i></a>
                <a class="btn btn-outline-light btn-social mx-1" href="https://www.linkedin.com"><i class="fab fa-fw fa-linkedin-in"></i></a>
                <a class="btn btn-outline-light btn-social mx-1" href="https://www.dribble.com/"><i class="fab fa-fw fa-dribbble"></i></a>
            </div>
            <!-- Footer About Text-->
            <div class="col-lg-4">
                <h4 class="mb-4">OUR VISION</h4>
                <p class="pre-wrap lead mb-0">Unleashing culinary creativity, fostering connections, and building a global community around food.</p>
            </div>
        </div>
    </div>
</footer>
<!-- Copyright Section-->
<section class="copyright py-4 text-center text-white">
    <div class="container"><small class="pre-wrap">Copyright © ChefMate 2024 </small></div>
</section>
<!-- Scroll to Top Button (Only visible on small and extra-small screen sizes)-->
<div class="scroll-to-top d-lg-none position-fixed"><a class="js-scroll-trigger d-block text-center text-white rounded" href="#page-top"><i class="fa fa-chevron-up"></i></a></div>
<!-- Bootstrap core JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
   <!-- Custom JS-->
<script src="assets/js/scripts.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<!-- Third party plugin JS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
<!-- Custom JS-->
<script>
/*!
 * Start Bootstrap - Freelancer v6.0.0 (https://startbootstrap.com/themes/freelancer)
 * Copyright 2013-2020 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap-freelancer/blob/master/LICENSE)
 */
(function($) {
    "use strict"; // Start of use strict
  
    // Smooth scrolling using jQuery easing
    $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function() {
      if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html, body').animate({
            scrollTop: (target.offset().top - 71)
          }, 1000, "easeInOutExpo");
          return false;
        }
      }
    });
  
    // Scroll to top button appear
    $(document).scroll(function() {
      var scrollDistance = $(this).scrollTop();
      if (scrollDistance > 100) {
        $('.scroll-to-top').fadeIn();
      } else {
        $('.scroll-to-top').fadeOut();
      }
    });
  
    // Closes responsive menu when a scroll trigger link is clicked
    $('.js-scroll-trigger').click(function() {
      $('.navbar-collapse').collapse('hide');
    });
  
    // Activate scrollspy to add active class to navbar items on scroll
    $('body').scrollspy({
      target: '#mainNav',
      offset: 80
    });
  
    // Collapse Navbar
    var navbarCollapse = function() {
      if ($("#mainNav").offset().top > 100) {
        $("#mainNav").addClass("navbar-shrink");
      } else {
        $("#mainNav").removeClass("navbar-shrink");
      }
    };
    // Collapse now if page is not at top
    navbarCollapse();
    // Collapse the navbar when page is scrolled
    $(window).scroll(navbarCollapse);
  
    // Floating label headings for the contact form
    $(function() {
      $("body").on("input propertychange", ".floating-label-form-group", function(e) {
        $(this).toggleClass("floating-label-form-group-with-value", !!$(e.target).val());
      }).on("focus", ".floating-label-form-group", function() {
        $(this).addClass("floating-label-form-group-with-focus");
      }).on("blur", ".floating-label-form-group", function() {
        $(this).removeClass("floating-label-form-group-with-focus");
      });
    });
  
})(jQuery); // End of use strict
 
</script>

</body>
</html>
<style>
  .foooter{
       background: #375755;
    text-align: center !important;
    bottom: 0 !important;
    margin-top: 10rem !important;
    padding: 10%;
    color: #fff;

  }   
    .text-white {
    color: #fff !important;
    background: #333 !important;
}
    
</style>
