(function ($) {
  "use strict";




  /*--------------------------------------------------------------
    RegisterPlugin, ScrollTrigger, SplitText
  --------------------------------------------------------------*/
  gsap.registerPlugin(ScrollTrigger, SplitText);
  gsap.config({
    nullTargetWarn: false,
    trialWarn: false
  });


  // Preloader
  $(window).on('load', function (event) {
    $('.js-preloader').delay(300).fadeOut(300);
  });




  /*--------------------------------------------------------------
    FullHeight
  --------------------------------------------------------------*/
  function fullHeight() {
    $('.full-height').css("height", $(window).height());
  }

  function applyOwlDotAriaLabels($carousel) {
    if (!$carousel || !$carousel.length) {
      return;
    }

    $carousel.find('.owl-dot').each(function (index) {
      $(this).attr('aria-label', `Go to slide ${index + 1}`);
    });
  }
















  //Testimonial One Carousel
  if ($(".testimonial-one__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".testimonial-one__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: true,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 1,
        },
        992: {
          items: 1,
        },
        1200: {
          items: 1,
        },
        1320: {
          items: 1,
        },
      },
    }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
      applyOwlDotAriaLabels($(this));
    });
    applyOwlDotAriaLabels($(".testimonial-one__carousel"));
  }




  //Brand One Carousel
  if ($(".brand-one__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".brand-one__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-left-arrow"></span>',
        '<span class="icon-next"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        540: {
          items: 2,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
        1320: {
          items: 5,
        },
      },
    });
  }


  //Brand Two Carousel
  if ($(".brand-two__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".brand-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-left-arrow"></span>',
        '<span class="icon-next"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        540: {
          items: 2,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
        1320: {
          items: 5,
        },
      },
    });
  }



  //Blog One Carousel
  if ($(".blog-one__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".blog-one__carousel").owlCarousel({
      loop: true,
      margin: 40,
      nav: true,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 2,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    });
  }





  //Team One Carousel
  if ($(".team-one__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".team-one__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
        1320: {
          items: 4,
        },
      },
    });
  }





  //Testimonial Two Carousel
  if ($(".testimonial-two__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".testimonial-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
      applyOwlDotAriaLabels($(this));
    });
    applyOwlDotAriaLabels($(".testimonial-two__carousel"));
  }





  //Portfolio One Carousel
  if ($(".portfolio-one__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".portfolio-one__carousel").owlCarousel({
      loop: true,
      margin: 21,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 4,
        },
      },
    });
  }





  //Portfolio Two Carousel
  if ($(".portfolio-two__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".portfolio-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: true,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 1,
        },
        992: {
          items: 1,
        },
        1200: {
          items: 1,
        },
        1320: {
          items: 1,
        },
      },
    });
  }




  //Services Three Carousel
  if ($(".services-three__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".services-three__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    });
  }





  //Team Two Carousel
  if ($(".team-two__carousel").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".team-two__carousel").owlCarousel({
      loop: true,
      margin: 30,
      nav: true,
      dots: false,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-up"></span>',
        '<span class="icon-right-up"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 2.2,
        },
        1320: {
          items: 2.461,
        },
      },
    });
  }







  //Blog Carousel Page
  if ($(".blog-carousel-style").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".blog-carousel-style").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-arrow-1"></span>',
        '<span class="icon-right-arrow-1"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
      applyOwlDotAriaLabels($(this));
    });
    applyOwlDotAriaLabels($(".blog-carousel-style"));
  }





  //Team Carousel Page
  if ($(".team-carousel-style").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".team-carousel-style").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-arrow-1"></span>',
        '<span class="icon-right-arrow-1"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 4,
        },
        1320: {
          items: 4,
        },
      },
    }).on('initialized.owl.carousel refreshed.owl.carousel', function() {
      applyOwlDotAriaLabels($(this));
    });
    applyOwlDotAriaLabels($(".team-carousel-style"));
  }



  //Services Carousel Page
  if ($(".services-carousel-style").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".services-carousel-style").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-arrow-1"></span>',
        '<span class="icon-right-arrow-1"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    });
  }



  //Services Carousel Page
  if ($(".testimonials-carousel-style").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $(".testimonials-carousel-style").owlCarousel({
      loop: true,
      margin: 30,
      nav: false,
      dots: true,
      smartSpeed: 500,
      autoplay: true,
      autoplayTimeout: 7000,
      rtl: isRTL,
      navText: [
        '<span class="icon-right-arrow-1"></span>',
        '<span class="icon-right-arrow-1"></span>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2,
        },
        992: {
          items: 3,
        },
        1200: {
          items: 3,
        },
        1320: {
          items: 3,
        },
      },
    });
  }




  // Hover Image
  const link = document.querySelectorAll(".hover-item");
  const linkHoverReveal = document.querySelectorAll(".hover-item__box");
  const linkImages = document.querySelectorAll(".hover-item__box-img");
  for (let i = 0; i < link.length; i++) {
    link[i].addEventListener("mousemove", (e) => {
      linkHoverReveal[i].style.opacity = 1;
      linkHoverReveal[
        i
      ].style.transform = `translate(-100%, -50%) rotate(0deg)`;
      linkImages[i].style.transform = "scale(1, 1)";
      linkHoverReveal[i].style.left = e.clientX + "px";
    });
    link[i].addEventListener("mouseleave", (e) => {
      linkHoverReveal[i].style.opacity = 0;
      linkHoverReveal[
        i
      ].style.transform = `translate(-50%, -50%) rotate(0deg)`;
      linkImages[i].style.transform = "scale(0.8, 0.8)";
    });
  }



  if ($(".marquee_mode").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $('.marquee_mode').marquee({
      speed: 30,
      gap: 0,
      delayBeforeStart: 0,
      direction: isRTL ? 'right' : 'left',
      duplicated: true,
      pauseOnHover: true,
      startVisible: true,
    });
  }



  if ($(".marquee_mode-2").length) {
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';
    $('.marquee_mode-2').marquee({
      speed: 30,
      gap: 0,
      delayBeforeStart: 0,
      direction: isRTL ? 'right' : 'left',
      duplicated: true,
      pauseOnHover: true,
      startVisible: true,
    });
  }












  // custom coursor
  if ($(".custom-cursor").length) {

    var cursor = document.querySelector('.custom-cursor__cursor');
    var cursorinner = document.querySelector('.custom-cursor__cursor-two');
    var a = document.querySelectorAll('a');
    var customCursorMaxWidth = 991;

    function toggleCustomCursorVisibility() {
      if (!cursor || !cursorinner) return;
      var isMobile = window.innerWidth <= customCursorMaxWidth;
      var displayValue = isMobile ? 'none' : '';
      cursor.style.display = displayValue;
      cursorinner.style.display = displayValue;
    }

    toggleCustomCursorVisibility();
    window.addEventListener('resize', toggleCustomCursorVisibility);

    document.addEventListener('mousemove', function (e) {
      if (window.innerWidth <= customCursorMaxWidth) return;
      var x = e.clientX;
      var y = e.clientY;
      cursor.style.transform = `translate3d(calc(${e.clientX}px - 50%), calc(${e.clientY}px - 50%), 0)`
    });

    document.addEventListener('mousemove', function (e) {
      if (window.innerWidth <= customCursorMaxWidth) return;
      var x = e.clientX;
      var y = e.clientY;
      cursorinner.style.left = x + 'px';
      cursorinner.style.top = y + 'px';
    });

    document.addEventListener('mousedown', function () {
      if (window.innerWidth <= customCursorMaxWidth) return;
      cursor.classList.add('click');
      cursorinner.classList.add('custom-cursor__innerhover')
    });

    document.addEventListener('mouseup', function () {
      if (window.innerWidth <= customCursorMaxWidth) return;
      cursor.classList.remove('click')
      cursorinner.classList.remove('custom-cursor__innerhover')
    });

    a.forEach(item => {
      item.addEventListener('mouseover', () => {
        if (window.innerWidth <= customCursorMaxWidth) return;
        cursor.classList.add('custom-cursor__hover');
      });
      item.addEventListener('mouseleave', () => {
        if (window.innerWidth <= customCursorMaxWidth) return;
        cursor.classList.remove('custom-cursor__hover');
      });
    })
  }




  // project style1
  if ($(".portfolio-two__box li").length) {
    $(".portfolio-two__box li").each(function () {
      let self = $(this);

      self.on("mouseenter", function () {
        console.log($(this));
        $(".portfolio-two__box li").removeClass("active");
        $(this).addClass("active");
      });
    });
  }




  //Progress Count Bar
  if ($(".count-bar").length) {
    $(".count-bar").appear(
      function () {
        var el = $(this);
        var percent = el.data("percent");
        $(el).css("width", percent).addClass("counted");
      }, {
        accY: -50
      }
    );
  }







  //Fact Counter + Text Count
  if ($(".count-box").length) {
    $(".count-box").appear(
      function () {
        var $t = $(this),
          n = $t.find(".count-text").attr("data-stop"),
          r = parseInt($t.find(".count-text").attr("data-speed"), 10);

        if (!$t.hasClass("counted")) {
          $t.addClass("counted");
          $({
            countNum: $t.find(".count-text").text()
          }).animate({
            countNum: n
          }, {
            duration: r,
            easing: "linear",
            step: function () {
              $t.find(".count-text").text(Math.floor(this.countNum));
            },
            complete: function () {
              $t.find(".count-text").text(this.countNum);
            }
          });
        }
      }, {
        accY: 0
      }
    );
  }





  // Accrodion
  if ($(".accrodion-grp").length) {
    var accrodionGrp = $(".accrodion-grp");
    accrodionGrp.each(function () {
      var accrodionName = $(this).data("grp-name");
      var Self = $(this);
      var accordion = Self.find(".accrodion");
      Self.addClass(accrodionName);
      Self.find(".accrodion .accrodion-content").hide();
      Self.find(".accrodion.active").find(".accrodion-content").show();
      accordion.each(function () {
        $(this)
          .find(".accrodion-title")
          .on("click", function () {
            if ($(this).parent().hasClass("active") === false) {
              $(".accrodion-grp." + accrodionName)
                .find(".accrodion")
                .removeClass("active");
              $(".accrodion-grp." + accrodionName)
                .find(".accrodion")
                .find(".accrodion-content")
                .slideUp();
              $(this).parent().addClass("active");
              $(this).parent().find(".accrodion-content").slideDown();
            }
          });
      });
    });
  }














  function dynamicCurrentMenuClass(selector) {
    let FileName = window.location.href.split("/").reverse()[0];

    selector.find("li").each(function () {
      let anchor = $(this).find("a");
      if ($(anchor).attr("href") == FileName) {
        $(this).addClass("current");
      }
    });
    // if any li has .current elmnt add class
    selector.children("li").each(function () {
      if ($(this).find(".current").length) {
        $(this).addClass("current");
      }
    });
    // if no file name return
    if ("" == FileName) {
      selector.find("li").eq(0).addClass("current");
    }
  }

  if ($(".main-menu__list").length) {
    // dynamic current class
    let mainNavUL = $(".main-menu__list");
    dynamicCurrentMenuClass(mainNavUL);
  }


  if ($(".main-menu__list").length && $(".mobile-nav__container").length) {
    let navContent = document.querySelector(".main-menu__list").outerHTML;
    let mobileNavContainer = document.querySelector(".mobile-nav__container");
    mobileNavContainer.innerHTML = navContent;
  }
  if ($(".sticky-header__content").length) {
    let navContent = document.querySelector(".main-menu").innerHTML;
    let mobileNavContainer = document.querySelector(".sticky-header__content");
    mobileNavContainer.innerHTML = navContent;
  }

  if ($(".mobile-nav__container .main-menu__list").length) {
    let dropdownAnchor = $(
      ".mobile-nav__container .main-menu__list .dropdown > a"
    );
    dropdownAnchor.each(function () {
      let self = $(this);
      let toggleBtn = document.createElement("BUTTON");
      toggleBtn.setAttribute("aria-label", "dropdown toggler");
      toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
      self.append(function () {
        return toggleBtn;
      });
      self.find("button").on("click", function (e) {
        e.preventDefault();
        let self = $(this);
        self.toggleClass("expanded");
        self.parent().toggleClass("expanded");
        self.parent().parent().children("ul").slideToggle();
      });
    });
  }

  if ($(".mobile-nav__toggler").length) {
    $(".mobile-nav__toggler").on("click", function (e) {
      e.preventDefault();
      $(".mobile-nav__wrapper").toggleClass("expanded");
      $("body").toggleClass("locked");
    });
  }

  //Header Search
  if ($('.searcher-toggler-box').length) {
    $('.searcher-toggler-box').on('click', function () {
      $('body').addClass('search-active');
    });
    $('.close-search').on('click', function () {
      $('body').removeClass('search-active');
    });

    $('.search-popup .color-layer').on('click', function () {
      $('body').removeClass('search-active');
    });
  }


  if ($(".odometer").length) {
    var odo = $(".odometer");
    odo.each(function () {
      $(this).appear(function () {
        var countNumber = $(this).attr("data-count");
        $(this).html(countNumber);
      });
    });
  }



  if ($(".wow").length) {
    var wow = new WOW({
      boxClass: "wow", // animated element css class (default is wow)
      animateClass: "animated", // animation css class (default is animated)
      mobile: true, // trigger animations on mobile devices (default is true)
      live: true // act on asynchronously loaded content (default is true)
    });
    wow.init();
  }






  if ($(".tabs-box").length) {
    $(".tabs-box .tab-buttons .tab-btn").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).is(":visible")) {
        return false;
      } else {
        target
          .parents(".tabs-box")
          .find(".tab-buttons")
          .find(".tab-btn")
          .removeClass("active-btn");
        $(this).addClass("active-btn");
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .fadeOut(0);
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .removeClass("active-tab");
        $(target).fadeIn(300);
        $(target).addClass("active-tab");
      }
    });
  }














  /*-- Handle Scrollbar --*/
  function handleScrollbar() {
    const bodyHeight = $("body").height();
    const scrollPos = $(window).innerHeight() + $(window).scrollTop();
    let percentage = (scrollPos / bodyHeight) * 100;
    if (percentage > 100) {
      percentage = 100;
    }
    $(".scroll-to-top .scroll-to-top__inner").css("width", percentage + "%");
  }




  // Animation gsap
  function title_animation() {
    var tg_var = jQuery('.sec-title-animation');
    if (!tg_var.length) {
      return;
    }
    const quotes = document.querySelectorAll(".sec-title-animation .title-animation");
    const isRTL = $('html').attr('dir') === 'rtl' || $('body').attr('dir') === 'rtl';

    quotes.forEach(quote => {

      //Reset if needed
      if (quote.animation) {
        quote.animation.progress(1).kill();
        if (quote.split && typeof quote.split.revert === "function") {
          quote.split.revert();
        }
      }

      // Handle Arabic text - capitalize and set proper styling
      if (isRTL) {
        const text = quote.textContent;
        // Check if text contains Arabic characters
        if (/[\u0600-\u06FF]/.test(text)) {
          quote.style.textTransform = 'none';
          quote.style.fontVariant = 'normal';
          // Don't set fontSize to inherit - let CSS handle it to maintain proper size
          // quote.style.fontSize = 'inherit'; // REMOVED - was causing small font size
        }
      }

      var getclass = quote.closest('.sec-title-animation').className;
      var animation = getclass.split('animation-');
      if (animation[1] == "style4") return

      // Check if text contains Arabic characters
      const text = quote.textContent;
      const hasArabic = /[\u0600-\u06FF]/.test(text);

      // For Arabic text, don't split at all - animate the whole element to preserve connections
      // For non-Arabic, use chars for character-by-character animation
      if (hasArabic) {
        // For Arabic: No splitting, just animate the whole element
        gsap.set(quote, {
          perspective: 400,
          opacity: 0,
          y: isRTL ? -50 : 50
        });

        quote.animation = gsap.to(quote, {
          scrollTrigger: {
            trigger: quote,
            start: "top 90%",
            toggleActions: "play none none none",
            once: false
          },
          y: 0,
          opacity: 1,
          duration: 1,
          ease: Back.easeOut
        });
      } else {
        // For non-Arabic: Use character splitting
        quote.split = new SplitText(quote, {
          type: "lines,words,chars",
          linesClass: "split-line"
        });
        gsap.set(quote, {
          perspective: 400
        });

        const xValue = isRTL ? "-50" : "50";
        const yValue = isRTL ? "-90%" : "90%";

        if (animation[1] == "style1") {
          gsap.set(quote.split.chars, {
            opacity: 0,
            y: yValue,
            rotateX: "-40deg"
          });
        }
        if (animation[1] == "style2") {
          gsap.set(quote.split.chars, {
            opacity: 0,
            x: xValue
          });
        }
        if (animation[1] == "style3") {
          gsap.set(quote.split.chars, {
            opacity: 0,
          });
        }
        quote.animation = gsap.to(quote.split.chars, {
          scrollTrigger: {
            trigger: quote,
            start: "top 90%",
            toggleActions: "play none none none",
            once: false
          },
          x: "0",
          y: "0",
          rotateX: "0",
          opacity: 1,
          duration: 1,
          ease: Back.easeOut,
          stagger: 0.02,
          immediateRender: false
        });
      }

      // If element is already in viewport, trigger animation immediately
      const rect = quote.getBoundingClientRect();
      const isInViewport = rect.top < window.innerHeight && rect.bottom > 0;
      if (isInViewport && quote.animation) {
        // Force the animation to play
        quote.animation.play();
      }
    });
  }
  ScrollTrigger.addEventListener("refresh", title_animation);









  $(".add").on("click", function () {
    if ($(this).prev().val() < 999) {
      $(this)
        .prev()
        .val(+$(this).prev().val() + 1);
    }
  });
  $(".sub").on("click", function () {
    if ($(this).next().val() > 1) {
      if ($(this).next().val() > 1)
        $(this)
        .next()
        .val(+$(this).next().val() - 1);
    }
  });






  // ===Checkout Payment===
  if ($(".checkout__payment__title").length) {
    $(".checkout__payment__item").find(".checkout__payment__content").hide();
    $(".checkout__payment__item--active").find(".checkout__payment__content").show();

    $(".checkout__payment__title").on("click", function (e) {
      e.preventDefault();

      $(this)
        .parents(".checkout__payment")
        .find(".checkout__payment__item")
        .removeClass("checkout__payment__item--active");
      $(this).parents(".checkout__payment").find(".checkout__payment__content").slideUp();

      $(this).parent().addClass("checkout__payment__item--active");
      $(this).parent().find(".checkout__payment__content").slideDown();
    });
  }





  // Product All Tab
  if ($(".product__all-tab").length) {
    $(".product__all-tab .tabs-button-box .tab-btn-item").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).hasClass("actve-tab")) {
        return false;
      } else {
        $(".product__all-tab .tabs-button-box .tab-btn-item").removeClass("active-btn-item");
        $(this).addClass("active-btn-item");
        $(".product__all-tab .tabs-content-box .tab-content-box-item").removeClass(
          "tab-content-box-item-active"
        );
        $(target).addClass("tab-content-box-item-active");
      }
    });
  }

















  // window load event
  $(window).on("load", function () {

    fullHeight();
    title_animation();


















  });

  // window scroll event

  $(window).on("scroll", function () {
    if ($(".stricked-menu").length) {
      var headerScrollPos = 130;
      var stricky = $(".stricked-menu");
      if ($(window).scrollTop() > headerScrollPos) {
        stricky.addClass("stricky-fixed");
      } else if ($(this).scrollTop() <= headerScrollPos) {
        stricky.removeClass("stricky-fixed");
      }
    }
  });

  $(window).on("scroll", function () {
    handleScrollbar();
    if ($(".sticky-header--one-page").length) {
      var headerScrollPos = 130;
      var stricky = $(".sticky-header--one-page");
      if ($(window).scrollTop() > headerScrollPos) {
        stricky.addClass("active");
      } else if ($(this).scrollTop() <= headerScrollPos) {
        stricky.removeClass("active");
      }
    }

    var scrollToTopBtn = ".scroll-to-top";
    if (scrollToTopBtn.length) {
      if ($(window).scrollTop() > 500) {
        $(scrollToTopBtn).addClass("show");
      } else {
        $(scrollToTopBtn).removeClass("show");
      }
    }
  });





  // removed unused audio player and select styling



})(jQuery);
