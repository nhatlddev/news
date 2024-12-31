$(document).ready(function () {
    $('.site-slider').owlCarousel({
      loop: true,
      dots: true,
      autoplay: true,
      autoplayTimeout: 30000,
      autoplayHoverPause: true,
      items: 1,
      nav: false,
      onTranslated: function () {
        var $slideHeading = $('.site-slider .owl-item.active .slider-text h3');
        var $slidePara = $('.site-slider .owl-item.active .slider-text p');
  
        $slideHeading
          .addClass('animate-in-fast')
          .on('animationend', function () {
            $slideHeading.removeClass('animate-in-fast').addClass('opacFull');
          });
  
        $slidePara
          .addClass('animate-in-slow')
          .on('animationend', function () {
            $slidePara.removeClass('animate-in-slow').addClass('opacFull');
          });
      },
      onChange: function () {
        var $slideHeading = $('.site-slider .owl-item.active .slider-text h3');
        var $slidePara = $('.site-slider .owl-item.active .slider-text p');
  
        $slideHeading.removeClass('opacFull');
        $slidePara.removeClass('opacFull');
      },
    });
  
    $(window).on('load', function () {
      var $slideHeading = $('.site-slider .owl-item.active .slider-text h3');
      var $slidePara = $('.site-slider .owl-item.active .slider-text p');
  
      $slideHeading
        .addClass('animate-in-fast')
        .on('animationend', function () {
          $slideHeading.removeClass('animate-in-fast').addClass('opacFull');
        });
  
      $slidePara
        .addClass('animate-in-slow')
        .on('animationend', function () {
          $slidePara.removeClass('animate-in-slow').addClass('opacFull');
        });
    });
  });

window.onscroll = function () {
	const btn = document.getElementById("backToTop");
	if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
		btn.style.display = "block";
	} else {
		btn.style.display = "none";
	}
};

// Scroll to top when the button is clicked
document.getElementById("backToTop").onclick = function () {
	window.scrollTo({ top: 0, behavior: "smooth" });
};
