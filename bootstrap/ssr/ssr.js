import { useSSRContext, computed, ref, onMounted, unref, withCtx, createVNode, createTextVNode, toDisplayString, nextTick, openBlock, createBlock, Fragment, renderList, createCommentVNode, withModifiers, withDirectives, vModelText, mergeProps, withKeys, resolveComponent, vModelCheckbox, createSSRApp, h } from "vue";
import { ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderClass, ssrRenderSlot, ssrRenderStyle, ssrRenderList, ssrRenderAttrs, ssrLooseContain } from "vue/server-renderer";
import { usePage, useForm, Link, Head, router, createInertiaApp } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
const _sfc_main$f = {
  __name: "App",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const settings = computed(() => page.props.settings);
    const storage_path = computed(() => page.props.storage_path);
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    computed(() => page.props.servicesList);
    computed(() => page.props.auth);
    computed(() => page.props.pages);
    const subscribeSuccess = ref(false);
    const subscribeForm = useForm({
      email: ""
    });
    const contactSubmitSuccess = ref(false);
    const contactForm = useForm({
      name: "",
      email: "",
      mobile: "",
      subject: "",
      message: ""
    });
    onMounted(() => {
      if ($(".mobile-nav__toggler").length) {
        $(".mobile-nav__toggler").off("click").on("click", function(e) {
          e.preventDefault();
          $(".mobile-nav__wrapper").toggleClass("expanded");
          $("body").toggleClass("locked");
        });
      }
      if ($(".navSidebar-button").length) {
        $(".navSidebar-button").off("click").on("click", function(e) {
          e.preventDefault();
          e.stopPropagation();
          $(".info-group").addClass("isActive");
        });
      }
      if ($(".close-side-widget").length) {
        $(".close-side-widget").off("click").on("click", function(e) {
          e.preventDefault();
          $(".info-group").removeClass("isActive");
        });
      }
      $("body").off("click.infoGroup").on("click.infoGroup", function(e) {
        $(".info-group").removeClass("isActive");
      });
      $(".xs-sidebar-widget").off("click").on("click", function(e) {
        e.stopPropagation();
      });
      if ($(".searcher-toggler-box").length) {
        $(".searcher-toggler-box").off("click").on("click", function() {
          $("body").addClass("search-active");
        });
        $(".close-search").off("click").on("click", function() {
          $("body").removeClass("search-active");
        });
        $(".search-popup .color-layer").off("click").on("click", function() {
          $("body").removeClass("search-active");
        });
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
        let dropdownAnchor = $(".mobile-nav__container .main-menu__list .dropdown > a");
        dropdownAnchor.each(function() {
          let self = $(this);
          if (self.find('button[aria-label="dropdown toggler"]').length === 0) {
            let toggleBtn = document.createElement("BUTTON");
            toggleBtn.setAttribute("aria-label", "dropdown toggler");
            toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
            self.append(function() {
              return toggleBtn;
            });
          }
          self.find("button").off("click").on("click", function(e) {
            e.preventDefault();
            let self2 = $(this);
            self2.toggleClass("expanded");
            self2.parent().toggleClass("expanded");
            self2.parent().parent().children("ul").slideToggle();
          });
        });
      }
      $(window).off("scroll.appLayout").on("scroll.appLayout", function() {
        if ($(".stricked-menu").length) {
          var headerScrollPos = 300;
          var stricky = $(".stricked-menu");
          if ($(window).scrollTop() > headerScrollPos) {
            stricky.addClass("stricky-fixed");
          } else if ($(this).scrollTop() <= headerScrollPos) {
            stricky.removeClass("stricky-fixed");
          }
        }
        var scrollToTopBtn = ".scroll-to-top";
        if ($(scrollToTopBtn).length) {
          if ($(window).scrollTop() > 500) {
            $(scrollToTopBtn).addClass("show");
          } else {
            $(scrollToTopBtn).removeClass("show");
          }
        }
      });
      const toggle = document.getElementById("themeToggle");
      const root = document.documentElement;
      const savedTheme = localStorage.getItem("theme");
      if (savedTheme) root.setAttribute("data-theme", savedTheme);
      toggle == null ? void 0 : toggle.addEventListener("click", () => {
        const current = root.getAttribute("data-theme");
        const next = current === "light" ? "dark" : "light";
        root.setAttribute("data-theme", next);
        localStorage.setItem("theme", next);
      });
    });
    const isActive = (routeName) => {
      return route().current(routeName);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="xs-sidebar-group info-group info-sidebar"><div class="xs-overlay xs-bg-black"></div><div class="xs-sidebar-widget"><div class="sidebar-widget-container"><div class="widget-heading"><a href="#" class="close-side-widget">X</a></div><div class="sidebar-textwidget"><div class="sidebar-info-contents"><div class="content-inner"><div class="logo">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings.value.site_logo,
                alt: "logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="content-box"><h4>${ssrInterpolate(trans("About Us"))}</h4><p>${ssrInterpolate(seo.value.about_us)}</p></div><div class="form-inner"><h4>${ssrInterpolate(trans("Get a price quote"))}</h4><form class="contact-form-validated" novalidate="novalidate"><div class="form-group"><input type="text" name="name"${ssrRenderAttr("value", unref(contactForm).name)}${ssrRenderAttr("placeholder", trans("Name"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required="">`);
      if (unref(contactForm).errors.name) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(contactForm).errors.name)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="form-group"><input type="email" name="email"${ssrRenderAttr("value", unref(contactForm).email)}${ssrRenderAttr("placeholder", trans("Email"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required="">`);
      if (unref(contactForm).errors.email) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(contactForm).errors.email)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="form-group"><input type="text" name="mobile"${ssrRenderAttr("value", unref(contactForm).mobile)}${ssrRenderAttr("placeholder", trans("Phone"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required="">`);
      if (unref(contactForm).errors.mobile) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(contactForm).errors.mobile)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="form-group"><input type="text" name="subject"${ssrRenderAttr("value", unref(contactForm).subject)}${ssrRenderAttr("placeholder", trans("Subject"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required="">`);
      if (unref(contactForm).errors.subject) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(contactForm).errors.subject)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="form-group"><textarea name="message"${ssrRenderAttr("placeholder", trans("Message"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required="">${ssrInterpolate(unref(contactForm).message)}</textarea>`);
      if (unref(contactForm).errors.message) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(contactForm).errors.message)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="form-group message-btn"><button type="submit" class="thm-btn form-inner__btn"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""}>`);
      if (unref(contactForm).processing) {
        _push(`<span>${ssrInterpolate(trans("Sending..."))}</span>`);
      } else {
        _push(`<span>${ssrInterpolate(trans("Submit Now"))}</span>`);
      }
      _push(`<span class="icon-right-arrow"></span></button></div>`);
      if (contactSubmitSuccess.value) {
        _push(`<div class="alert alert-success mt-2">${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</form></div></div></div></div></div></div></div><div class="page-wrapper"><header class="main-header-two"><div class="main-menu-two__top"><div class="main-menu-two__top-inner"><p class="main-menu-two__top-text">${ssrInterpolate(trans("We Build Technology In Perfect Harmony"))}</p><ul class="list-unstyled main-menu-two__contact-list"><li><div class="icon"><i class="icon-pin"></i></div><div class="text"><p>${ssrInterpolate(settings.value.address)}</p></div></li><li><div class="icon"><i class="icon-search-mail"></i></div><div class="text"><p><a href="mailto:{{settings.email}}">${ssrInterpolate(settings.value.email)}</a></p></div></li><li><div class="icon"><i class="icon-phone-call"></i></div><div class="text"><p><a href="tel:{{settings.phone}}">${ssrInterpolate(settings.value.phone)}</a></p></div></li></ul></div></div><nav class="main-menu main-menu-two"><div class="main-menu-two__wrapper"><div class="main-menu-two__wrapper-inner"><div class="main-menu-two__left"><div class="main-menu-two__logo">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings.value.site_logo,
                alt: "logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="main-menu-two__main-menu-box"><a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a><ul class="main-menu__list"><li class="${ssrRenderClass({ current: isActive("home") })}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Home"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Home")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="${ssrRenderClass({ current: isActive("about-us") })}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("about-us")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("About Us"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("About Us")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="${ssrRenderClass({ current: isActive("services.index") })}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("services.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Our Services"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Our Services")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="${ssrRenderClass({ current: isActive("blogs.index") })}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("blogs.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Blogs"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Blogs")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li class="${ssrRenderClass({ current: isActive("contact-us") })}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("contact-us")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Contact Us"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Contact Us")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li></ul></div><div class="main-menu-two__right d-flex align-items-center"><div class="main-menu-two__search-box me-3"><a href="#" class="main-menu-two__search searcher-toggler-box icon-search-interface-symbol"></a></div><div class="main-menu-two__btn-box me-3">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("contact-us"),
        class: "thm-btn"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Get in Touch"))} <span class="icon-right-arrow"${_scopeId}></span>`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Get in Touch")) + " ", 1),
              createVNode("span", { class: "icon-right-arrow" })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="header-lang-switch d-flex align-items-center me-3"><button type="button" class="${ssrRenderClass([{ active: locale.value === "en" }, "btn btn-sm b tn-light me-2"])}"> EN </button><button type="button" class="${ssrRenderClass([{ active: locale.value === "ar" }, "btn btn-sm btn-outline-light"])}"> ع </button></div><div class="main-menu-two__nav-sidebar-icon"><a class="navSidebar-button" href="#"><span class="icon-dots-menu-one"></span><span class="icon-dots-menu-two"></span><span class="icon-dots-menu-three"></span></a></div></div></div></div></nav></header><div class="stricky-header stricked-menu main-menu main-menu-two"><div class="sticky-header__content"></div></div>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`<section class="newsletter-two"><div class="newsletter-two__shape-1"><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/newsletter-two-shape-1.png")} alt=""></div><div class="newsletter-two__shape-2 float-bob-x"><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/newsletter-two-shape-2.png")} alt=""></div><div class="container"><div class="newsletter-two__inner"><div class="newsletter-two__left"><h2 class="newsletter-two__title">${ssrInterpolate(trans("Subscribe to Our Newsletter"))}</h2><p class="newsletter-two__text">${ssrInterpolate(trans("Engineering insights, product updates, and practical tech lessons—delivered occasionally, not daily"))}</p></div><div class="newsletter-two__right"><form class="newsletter-two__form"><div class="newsletter-two__input"><input type="email" name="email"${ssrRenderAttr("value", unref(subscribeForm).email)}${ssrRenderAttr("placeholder", trans("Enter email address"))}${ssrIncludeBooleanAttr(unref(subscribeForm).processing) ? " disabled" : ""} required="">`);
      if (unref(subscribeForm).errors.email) {
        _push(`<div class="text-danger mt-1 small">${ssrInterpolate(unref(subscribeForm).errors.email)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><button type="submit" class="thm-btn"${ssrIncludeBooleanAttr(unref(subscribeForm).processing) ? " disabled" : ""}>`);
      if (unref(subscribeForm).processing) {
        _push(`<span>${ssrInterpolate(trans("Subscribing..."))}</span>`);
      } else {
        _push(`<span>${ssrInterpolate(trans("Subscribe Now"))}</span>`);
      }
      _push(`<span class="icon-right-arrow"></span></button><div class="checked-box"><input type="checkbox" name="skipper1" id="skipper" checked=""><label for="skipper"><span></span>${ssrInterpolate(trans("By subscribing, you accept our privacy policy"))}</label></div>`);
      if (subscribeSuccess.value) {
        _push(`<div class="alert alert-success mt-2">${ssrInterpolate(trans("Thank you for subscribing to our newsletter!"))}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</form></div></div></div></section><footer class="site-footer-two"><div class="site-footer-two__shape-1"></div><div class="site-footer-two__shape-2"></div><div class="site-footer-two__shape-3"></div><div class="site-footer-two__top"><div class="container"><div class="row"><div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms"><div class="site-footer-two__about"><div class="site-footer-two__logo"><a href="index.html"><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/footer-logo.png")} alt=""></a></div><ul class="list-unstyled site-footer-two__contact-list"><li><div class="site-footer-two__contact-icon"><span class="icon-contact"></span></div><div class="site-footer-two__contact-content"><h5 class="site-footer-two__contact-title">${ssrInterpolate(trans("Contact Info"))}</h5><p class="site-footer-two__contact-info"><a${ssrRenderAttr("href", `mailto:${settings.value.email}`)} class="site-footer-two__contact-mail">${ssrInterpolate(settings.value.email)}</a><a${ssrRenderAttr("href", `tel:${settings.value.phone}`)} class="site-footer-two__contact-phone">${ssrInterpolate(settings.value.phone)}</a></p></div></li><li><div class="site-footer-two__contact-icon"><span class="icon-pin"></span></div><div class="site-footer-two__contact-content"><h5 class="site-footer-two__contact-title">${ssrInterpolate(trans("Location"))}</h5><p class="site-footer-two__contact-info">${ssrInterpolate(settings.value.address)}</p></div></li></ul></div></div><div class="col-xl-2 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms"><div class="footer-widget-two__quick-links"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Pages"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><li><a href="index.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Home"))}</a></li><li><a href="about.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("About Us"))}</a></li><li><a href="pricing.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Pricing"))}</a></li><li><a href="portfolio.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Portfolio"))}</a></li><li><a href="blog.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Blogs"))}</a></li><li><a href="contact.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Careers"))}</a></li></ul></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms"><div class="footer-widget-two__support"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Support"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><li><a href="about.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Terms & Condition"))}</a></li><li><a href="faq.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("FAQs"))}</a></li><li><a href="contact.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Contact Us"))}</a></li><li><a href="404.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("404 Page"))}</a></li><li><a href="contact.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Live Chat"))}</a></li><li><a href="services.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Our Services"))}</a></li></ul></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms"><div class="footer-widget-two__services"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Our Services"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><li><a href="services.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("UI/UX Design"))}</a></li><li><a href="about.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Website Design"))}</a></li><li><a href="pricing.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Web Development"))}</a></li><li><a href="about.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Product Design"))}</a></li><li><a href="blog.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Online Branding"))}</a></li><li><a href="contact.html"><span class="icon-right-arrow-2"></span>${ssrInterpolate(trans("Personal Branding"))}</a></li></ul></div></div></div></div></div><div class="site-footer-two__bottom"><div class="container"><div class="row"><div class="col-xl-12"><div class="site-footer-two__bottom-inner"><div class="site-footer-two__copyright"><p class="site-footer-two__copyright-text">${ssrInterpolate(trans("© Copyright"))} ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} <a href="#">${ssrInterpolate(seo.value.website_name)}</a> ${ssrInterpolate(trans("All rights reserved"))}</p></div><div class="site-footer-two__social-box"><h4 class="site-footer-two__social-title">${ssrInterpolate(trans("Follow Us"))}:</h4><div class="site-footer-two__social-box-inner">`);
      if (settings.value.facebook) {
        _push(`<a${ssrRenderAttr("href", settings.value.facebook)} target="_blank" rel="noopener"><span class="icon-facebook"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings.value.instagram) {
        _push(`<a${ssrRenderAttr("href", settings.value.instagram)} target="_blank" rel="noopener"><span class="icon-dribble"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings.value.linkedin) {
        _push(`<a${ssrRenderAttr("href", settings.value.linkedin)} target="_blank" rel="noopener"><span class="icon-linkedin"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div></div></div></div></footer></div><div class="mobile-nav__wrapper"><div class="mobile-nav__overlay mobile-nav__toggler"></div><div class="mobile-nav__content"><span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span><div class="logo-box"><a href="index.html" aria-label="logo image"><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/logo-2.png")} width="150" alt=""></a></div><div class="mobile-nav__container"></div><ul class="mobile-nav__contact list-unstyled"><li><i class="fa fa-envelope"></i><a${ssrRenderAttr("href", `mailto:${settings.value.email}`)}>${ssrInterpolate(settings.value.email)}</a></li><li><i class="fas fa-phone"></i><a${ssrRenderAttr("href", `tel:${settings.value.phone}`)}>${ssrInterpolate(settings.value.phone)}</a></li></ul><div class="mobile-nav__top"><div class="mobile-nav__social">`);
      if (settings.value.twitter) {
        _push(`<a${ssrRenderAttr("href", settings.value.twitter)} class="fab fa-twitter" target="_blank" rel="noopener"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings.value.facebook) {
        _push(`<a${ssrRenderAttr("href", settings.value.facebook)} class="fab fa-facebook-square" target="_blank" rel="noopener"></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<a href="#" class="fab fa-pinterest-p"></a>`);
      if (settings.value.instagram) {
        _push(`<a${ssrRenderAttr("href", settings.value.instagram)} class="fab fa-instagram" target="_blank" rel="noopener"></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div><div class="search-popup"><div class="color-layer"></div><button class="close-search"><span class="far fa-times fa-fw"></span></button><form method="post" action="blog.html"><div class="form-group"><input type="search" name="search-field" value=""${ssrRenderAttr("placeholder", trans("Search Here"))} required=""><button type="submit"><i class="fas fa-search"></i></button></div></form></div><!--]-->`);
    };
  }
};
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/App.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const __default__$9 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$e = /* @__PURE__ */ Object.assign(__default__$9, {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings = computed(() => page.props.settings);
    const asset_path = computed(() => page.props.asset_path || "");
    computed(() => page.props.storage_path || "");
    const locale = computed(() => page.props.locale);
    const posts = computed(() => page.props.posts || []);
    const services = computed(() => page.props.services || []);
    computed(() => page.props.slides || []);
    const testimonials = computed(() => page.props.testimonials || []);
    const translateField = (value) => {
      if (!value) {
        return "";
      }
      if (typeof value === "string") {
        return value;
      }
      const loc = locale.value;
      if (typeof value === "object" && value !== null) {
        if (value[loc]) {
          return value[loc];
        }
      }
      return "";
    };
    ref(false);
    useForm({
      email: ""
    });
    const contactSubmitSuccess = ref(false);
    const contactForm = useForm({
      name: "",
      email: "",
      mobile: "",
      subject: "",
      message: ""
    });
    const handleContactSubmit = () => {
      if (contactForm.processing) {
        return false;
      }
      if (!contactForm.name || !contactForm.name.trim()) return false;
      if (!contactForm.email || !contactForm.email.trim()) return false;
      if (!contactForm.mobile || !contactForm.mobile.trim()) return false;
      if (!contactForm.subject || !contactForm.subject.trim()) return false;
      if (!contactForm.message || !contactForm.message.trim()) return false;
      let contactUrl = "/contact-us";
      try {
        if (typeof route !== "undefined" && route) {
          contactUrl = route("contact-us.store");
        } else {
          const currentLocale = page.props.locale || "";
          contactUrl = currentLocale ? `/${currentLocale}/contact-us` : "/contact-us";
        }
      } catch (e) {
        const currentLocale = page.props.locale || "";
        contactUrl = currentLocale ? `/${currentLocale}/contact-us` : "/contact-us";
      }
      contactForm.post(contactUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
          contactSubmitSuccess.value = false;
        },
        onSuccess: () => {
          contactSubmitSuccess.value = true;
          contactForm.reset();
          contactForm.clearErrors();
          setTimeout(() => {
            contactSubmitSuccess.value = false;
          }, 5e3);
        },
        onError: () => {
          contactSubmitSuccess.value = false;
        }
      });
      return false;
    };
    const getServiceUrl = (service) => {
      if (!service || !service.slug) {
        return "#";
      }
      try {
        return route("services.show", service.slug);
      } catch (e) {
        return "#";
      }
    };
    const getPostUrl = (post) => {
      if (!post || !post.slug) {
        return "#";
      }
      try {
        return route("blogs.show", post.slug);
      } catch (e) {
        return "#";
      }
    };
    onMounted(() => {
      nextTick(() => {
        try {
          if (typeof window !== "undefined") {
            const counters = document.querySelectorAll(".latest-counter__content .count");
            const animateCount = (el) => {
              if (!el || el.dataset.countInitialized === "1") return;
              const rawText = el.textContent || "";
              const target = parseInt(rawText.replace(/[^\d]/g, ""), 10);
              if (!target || isNaN(target)) return;
              el.dataset.countInitialized = "1";
              const duration = 8e3;
              const start = 0;
              const startTime = performance.now();
              const step = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const value = Math.floor(start + (target - start) * progress);
                el.textContent = value.toString();
                if (progress < 1) {
                  requestAnimationFrame(step);
                } else {
                  el.textContent = target.toString();
                }
              };
              requestAnimationFrame(step);
            };
            if (counters.length) {
              if ("IntersectionObserver" in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                  entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                      animateCount(entry.target);
                      obs.unobserve(entry.target);
                    }
                  });
                }, { threshold: 0.3 });
                counters.forEach((el) => observer.observe(el));
              } else {
                counters.forEach((el) => animateCount(el));
              }
            }
          }
        } catch (e) {
        }
        if (window.Swiper) {
          const brandEl = document.querySelector(".brand__active");
          if (brandEl && !brandEl.swiper) {
            new window.Swiper(".brand__active", {
              slidesPerView: 3,
              spaceBetween: 30,
              loop: true,
              autoplay: {
                delay: 2e3,
                disableOnInteraction: false
              },
              breakpoints: {
                1200: {
                  slidesPerView: 3
                },
                992: {
                  slidesPerView: 3
                },
                768: {
                  slidesPerView: 2
                },
                576: {
                  slidesPerView: 2
                },
                0: {
                  slidesPerView: 1
                }
              }
            });
          }
          const servicesEl = document.querySelector(".services-5__active");
          if (servicesEl && !servicesEl.swiper && services.value.length) {
            const servicesCount = services.value.length;
            new window.Swiper(".services-5__active", {
              slidesPerView: 4,
              spaceBetween: 30,
              loop: servicesCount > 4,
              autoplay: servicesCount > 4 ? {
                delay: 2500,
                disableOnInteraction: false
              } : false,
              navigation: {
                prevEl: ".services-5__button-prev",
                nextEl: ".services-5__button-next"
              },
              breakpoints: {
                1400: {
                  slidesPerView: 4
                },
                1200: {
                  slidesPerView: 3
                },
                992: {
                  slidesPerView: 3
                },
                768: {
                  slidesPerView: 2
                },
                0: {
                  slidesPerView: 1
                }
              }
            });
          }
          const blogEl = document.querySelector(".mySwiper");
          if (blogEl && !blogEl.swiper) {
            const postsCount = posts.value.length;
            new window.Swiper(".mySwiper", {
              slidesPerView: 3,
              spaceBetween: 30,
              loop: postsCount > 3,
              // Only loop if we have more than 3 posts
              autoplay: postsCount > 3 ? {
                delay: 3e3,
                disableOnInteraction: false
              } : false,
              navigation: {
                prevEl: ".blog__slider-button-prev",
                nextEl: ".blog__slider-button-next"
              },
              breakpoints: {
                1200: {
                  slidesPerView: 3
                },
                992: {
                  slidesPerView: 3
                },
                768: {
                  slidesPerView: 2
                },
                576: {
                  slidesPerView: 2
                },
                0: {
                  slidesPerView: 1
                }
              }
            });
          }
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(trans("Home"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("Home")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="banner-one"${_scopeId}><div class="banner-one__bg" style="${ssrRenderStyle({
              backgroundImage: `url(${asset_path.value}images/home/banner-bg.jpg)`
            })}"${_scopeId}></div><div class="banner-one__shape-bg float-bob-y" style="${ssrRenderStyle({
              backgroundImage: `url(${asset_path.value}images/shapes/banner-one-shape-bg.png)`
            })}"${_scopeId}></div><div class="container"${_scopeId}><div class="banner-one__inner"${_scopeId}><h2 class="banner-one__title"${_scopeId}>${ssrInterpolate(trans("Expert IT Solutions to Elevate"))} <br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Your Enterprise"))}</span></h2><div class="banner-one__btn-box"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("contact-us"),
              class: "thm-btn"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Get Started"))} <span class="icon-right-arrow"${_scopeId2}></span>`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Get Started")) + " ", 1),
                    createVNode("span", { class: "icon-right-arrow" })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></section><section class="about-three"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="about-three__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="about-three__img-box"${_scopeId}><div class="about-three__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/about_us.jpg")} alt=""${_scopeId}></div></div></div></div><div class="col-xl-6"${_scopeId}><div class="about-three__right"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("About Us"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Any IT Problem Solutions And"))} <span${_scopeId}>${ssrInterpolate(trans("Grow Your Business"))}</span></h2></div><p class="about-three__text"${_scopeId}>${ssrInterpolate(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth."))}</p><div class="about-three__progress-box"${_scopeId}><div class="progress-box"${_scopeId}><div class="bar-title"${_scopeId}>${ssrInterpolate(trans("Business Problem Solving"))}</div><div class="bar"${_scopeId}><div class="bar-inner count-bar" data-percent="70%"${_scopeId}><div class="count-box"${_scopeId}><span class="count-text" data-stop="70" data-speed="1500"${_scopeId}>0</span>% </div></div></div></div><div class="progress-box"${_scopeId}><div class="bar-title"${_scopeId}>${ssrInterpolate(trans("Camping Launches"))}</div><div class="bar"${_scopeId}><div class="bar-inner count-bar" data-percent="80%"${_scopeId}><div class="count-box"${_scopeId}><span class="count-text" data-stop="80" data-speed="1500"${_scopeId}>0</span>% </div></div></div></div></div><ul class="about-three__points list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="content"${_scopeId}><h3${_scopeId}>${ssrInterpolate(trans("Shaping Tomorrow, Transforming Today"))}</h3></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="content"${_scopeId}><h3${_scopeId}>${ssrInterpolate(trans("Innovating Today, Empowering Tomorrow"))}</h3></div></li></ul><div class="about-three__btn-and-call-box"${_scopeId}><div class="about-three__btn-box"${_scopeId}><a${ssrRenderAttr("href", _ctx.route("about-us"))} class="thm-btn"${_scopeId}>${ssrInterpolate(trans("Get in Touch"))} <span class="icon-right-arrow"${_scopeId}></span></a></div><div class="about-three__call-box"${_scopeId}><div class="icon"${_scopeId}><span class="icon-customer-service-headset"${_scopeId}></span></div><div class="content"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Call Any Time"))}</span><p${_scopeId}><a href="tel:{{settings.phone}}"${_scopeId}>${ssrInterpolate(settings.value.phone)}</a></p></div></div></div></div></div></div></div></section>`);
            if (services.value && services.value.length) {
              _push2(`<section class="services-three"${_scopeId}><div class="services-three__shape-1"${_scopeId}></div><div class="services-three__shape-2"${_scopeId}></div><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Our Services"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Discover What We Offer"))} <br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("One Group, Multiple Specializations"))}</span></h2></div><div class="services-three__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(services.value, (service) => {
                _push2(`<div class="item"${_scopeId}><div class="services-three__single"${_scopeId}><div class="services-three__icon-and-title"${_scopeId}><div class="services-three__icon"${_scopeId}><span class="icon-technical-support"${_scopeId}></span></div><h3 class="services-three__title"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(service)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(translateField(service.title))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(translateField(service.title)), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</h3></div><p class="services-three__text"${_scopeId}>${ssrInterpolate(translateField(service.description))}</p>`);
                if (service.category) {
                  _push2(`<ul class="list-unstyled services-three__list"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="text"${_scopeId}><p${_scopeId}>${ssrInterpolate(translateField(service.category.name))}</p></div></li></ul>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(service),
                  class: "services-three__btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Read More"))} <span class="icon-right-arrow-1"${_scopeId2}></span>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                        createVNode("span", { class: "icon-right-arrow-1" })
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div>`);
              });
              _push2(`<!--]--></div><div class="text-center mt-4"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("services.index"),
                class: "thm-btn"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(trans("View All Services"))} <span class="icon-right-arrow"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(trans("View All Services")) + " ", 1),
                      createVNode("span", { class: "icon-right-arrow" })
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="why-choose-two"${_scopeId}><div class="why-choose-two__shape-1 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/why-choose-two-shape-1.png")} alt=""${_scopeId}></div><div class="why-choose-two__shape-2"${_scopeId}></div><div class="why-choose-two__shape-3"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="why-choose-two__left wow slideInLeft" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="why-choose-two__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/why-choose-two-img-1.png")} alt=""${_scopeId}></div></div></div><div class="col-xl-6"${_scopeId}><div class="why-choose-two__right"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Why Choose Us"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Elevate Growth with Our IT Solutions"))} <span${_scopeId}>${ssrInterpolate(trans("for Success"))}</span></h2></div><p class="why-choose-one__text"${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p><ul class="list-unstyled why-choose-two__points"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-earning"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("Industry Experience"))}</h4><p${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-customer-service-headset"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("24/7 Customer Support"))}</h4><p${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-quality"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("Trust & Reliability"))}</h4><p${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></li></ul></div></div></div></div></section><section class="feature-one"${_scopeId}><div class="feature-one__shape-1"${_scopeId}></div><div class="feature-one__shape-2 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/feature-one-shape-2.png")} alt=""${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="100ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/feature-one-img-1-1.png")} alt=""${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Security Services"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></div><div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="300ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/feature-one-img-1-2.png")} alt=""${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Data Privacy"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></div><div class="col-xl-4 col-lg-4 wow fadeInUp" data-wow-delay="500ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/resources/feature-one-img-1-3.png")} alt=""${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Industry Certified"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering"))}</p></div></div></div></div></section><section class="cta-one"${_scopeId}><div class="cta-one__shape-bg float-bob-y" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}site/images/shapes/cta-one-shape-bg.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="cta-one__inner"${_scopeId}><h3 class="cta-one__title"${_scopeId}>${ssrInterpolate(trans("To make requests for further information, contact us"))}</h3><div class="cta-one__contact-info"${_scopeId}><div class="cta-one__contact-icon"${_scopeId}><span class="icon-customer-service-headset"${_scopeId}></span></div><div class="cta-one__contact-details"${_scopeId}><p${_scopeId}>${ssrInterpolate(trans("Call Us For Any inquiry"))}</p><h4${_scopeId}><a href="tel:9900567780"${_scopeId}>+99 (00) 567 780</a></h4></div></div></div></div></section>`);
            if (testimonials.value && testimonials.value.length) {
              _push2(`<section class="testimonial-two"${_scopeId}><div class="testimonial-two__shape-1"${_scopeId}></div><div class="testimonial-two__shape-2"${_scopeId}></div><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("What Our Clients Say"))}</h2></div><div class="testimonial-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(testimonials.value, (testimonial) => {
                _push2(`<div class="item"${_scopeId}><div class="testimonial-two__single"${_scopeId}><div class="testimonial-two__single-inner"${_scopeId}><div class="testimonial-two__star"${_scopeId}><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span></div><p class="testimonial-two__text"${_scopeId}>${ssrInterpolate(translateField(testimonial.quote))}</p></div><div class="testimonial-two__client-info"${_scopeId}><div class="testimonial-two__client-img"${_scopeId}><img${ssrRenderAttr("src", testimonial.avatar_link)}${ssrRenderAttr("alt", translateField(testimonial.name))}${_scopeId}></div><div class="testimonial-two__client-content"${_scopeId}><h4 class="testimonial-two__client-name"${_scopeId}>${ssrInterpolate(translateField(testimonial.name))}</h4><p class="testimonial-two__sub-title"${_scopeId}>${ssrInterpolate(translateField(testimonial.position))}</p></div></div><div class="testimonial-two__quote"${_scopeId}><span class="icon-right-quote"${_scopeId}></span></div></div></div>`);
              });
              _push2(`<!--]--></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (posts.value && posts.value.length) {
              _push2(`<section class="blog-two blog-three"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-4"${_scopeId}><div class="blog-two__left wow fadeInLeft" data-wow-delay="100ms"${_scopeId}><div class="section-title text-left sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Blog Posts"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Read Latest News & Blogs"))}</h2></div><p class="blog-two-text"${_scopeId}>${ssrInterpolate(trans("We deliver complete solutions in printing and branding. From premium banner printing and durable apparel printing to custom Alucobond facades and detailed laser cutting, our team combines expertise and innovation to bring your ideas to life with exceptional quality."))}</p><div class="blog-two__top-btn-box"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.index"),
                class: "thm-btn"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(trans("View All Blogs"))} <span class="icon-right-arrow"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(trans("View All Blogs")) + " ", 1),
                      createVNode("span", { class: "icon-right-arrow" })
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div></div></div><div class="col-xl-8"${_scopeId}><div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(posts.value, (post) => {
                _push2(`<div class="col-md-6 col-lg-4 mb-4"${_scopeId}><div class="blog-two__single"${_scopeId}><div class="blog-two__img"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getPostUrl(post)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<img${ssrRenderAttr("src", post.image_link)}${ssrRenderAttr("alt", translateField(post.title))}${_scopeId2}>`);
                    } else {
                      return [
                        createVNode("img", {
                          src: post.image_link,
                          alt: translateField(post.title)
                        }, null, 8, ["src", "alt"])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div><div class="blog-two__content"${_scopeId}><ul class="blog-two__meta list-unstyled"${_scopeId}>`);
                if (post.created_at) {
                  _push2(`<li${_scopeId}><span class="far fa-calendar-alt"${_scopeId}></span> ${ssrInterpolate(post.created_at)}</li>`);
                } else {
                  _push2(`<!---->`);
                }
                if (post.category) {
                  _push2(`<li${_scopeId}><span class="fal fa-folder-open"${_scopeId}></span> ${ssrInterpolate(translateField(post.category.name))}</li>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</ul><h3 class="blog-two__title"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getPostUrl(post)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(translateField(post.title))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(translateField(post.title)), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</h3><p class="blog-two__text"${_scopeId}>${ssrInterpolate(translateField(post.description))}</p><div class="blog-two__btn-box"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getPostUrl(post),
                  class: "thm-btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Read More"))} <span class="icon-right-arrow"${_scopeId2}></span>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                        createVNode("span", { class: "icon-right-arrow" })
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div></div></div>`);
              });
              _push2(`<!--]--></div></div></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="contact-two"${_scopeId}><ul class="contact-two__sliding-text-list list-unstyled marquee_mode-2"${_scopeId}><li${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></li><li${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></li><li${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></li></ul><div class="contact-two__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}site/images/backgrounds/contact-two-bg.jpg)` })}"${_scopeId}></div><div class="contact-two__shape-1 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/contact-two-shape-1.png")} alt=""${_scopeId}></div><div class="contact-two__shape-2"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="contact-two__left"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Get In Touch"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</h2></div><p class="contact-two__text"${_scopeId}>${ssrInterpolate(trans("Fill out the form below and we'll get back to you as soon as possible"))}</p><ul class="contact-two__contact-list list-unstyled"${_scopeId}>`);
            if (settings.value.website_email) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-mail"${_scopeId}></span></div><div class="content"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Email"))}</span><p${_scopeId}><a${ssrRenderAttr("href", `mailto:${settings.value.website_email}`)}${_scopeId}>${ssrInterpolate(settings.value.website_email)}</a></p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            if (settings.value.website_phone) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><div class="content"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Phone"))}</span><p${_scopeId}><a${ssrRenderAttr("href", `tel:${settings.value.website_phone}`)}${_scopeId}>${ssrInterpolate(settings.value.website_phone)}</a></p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            if (settings.value.website_address) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-pin"${_scopeId}></span></div><div class="content"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Address"))}</span><p${_scopeId}>${ssrInterpolate(settings.value.website_address)}</p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</ul></div></div><div class="col-xl-6"${_scopeId}><div class="contact-two__right wow slideInRight" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><form class="contact-form-validated contact-one__form"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Name"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-user-1"${_scopeId}></span></div><input type="text" name="name"${ssrRenderAttr("value", unref(contactForm).name)}${ssrRenderAttr("placeholder", trans("Name"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.name })}" required${_scopeId}>`);
            if (unref(contactForm).errors.name) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.name)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Email"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-email"${_scopeId}></span></div><input type="email" name="email"${ssrRenderAttr("value", unref(contactForm).email)}${ssrRenderAttr("placeholder", trans("Email"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.email })}" required${_scopeId}>`);
            if (unref(contactForm).errors.email) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.email)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Phone"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><input type="text" name="mobile"${ssrRenderAttr("value", unref(contactForm).mobile)}${ssrRenderAttr("placeholder", trans("Phone"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.mobile })}" required${_scopeId}>`);
            if (unref(contactForm).errors.mobile) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.mobile)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Subject"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><input type="text" name="subject"${ssrRenderAttr("value", unref(contactForm).subject)}${ssrRenderAttr("placeholder", trans("Subject"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.subject })}" required${_scopeId}>`);
            if (unref(contactForm).errors.subject) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.subject)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div><div class="col-xl-12"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Message"))}</h4><div class="contact-one__input-box text-message-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><textarea name="message"${ssrRenderAttr("placeholder", trans("Message"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.message })}" required${_scopeId}>${ssrInterpolate(unref(contactForm).message)}</textarea>`);
            if (unref(contactForm).errors.message) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.message)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="contact-one__btn-box"${_scopeId}><button type="submit"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": unref(contactForm).processing }, "thm-btn"])}"${_scopeId}>`);
            if (unref(contactForm).processing) {
              _push2(`<span${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2"${_scopeId}></i> ${ssrInterpolate(trans("Sending..."))}</span>`);
            } else {
              _push2(`<span${_scopeId}>${ssrInterpolate(trans("Send Message"))} <i class="icon-right-arrow"${_scopeId}></i></span>`);
            }
            _push2(`</button></div>`);
            if (contactSubmitSuccess.value) {
              _push2(`<div class="mt-3 alert alert-success"${_scopeId}>${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></form><div class="result"${_scopeId}></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "banner-one" }, [
                createVNode("div", {
                  class: "banner-one__bg",
                  style: {
                    backgroundImage: `url(${asset_path.value}images/home/banner-bg.jpg)`
                  }
                }, null, 4),
                createVNode("div", {
                  class: "banner-one__shape-bg float-bob-y",
                  style: {
                    backgroundImage: `url(${asset_path.value}images/shapes/banner-one-shape-bg.png)`
                  }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "banner-one__inner" }, [
                    createVNode("h2", { class: "banner-one__title" }, [
                      createTextVNode(toDisplayString(trans("Expert IT Solutions to Elevate")) + " ", 1),
                      createVNode("br"),
                      createVNode("span", null, toDisplayString(trans("Your Enterprise")), 1)
                    ]),
                    createVNode("div", { class: "banner-one__btn-box" }, [
                      createVNode(unref(Link), {
                        href: _ctx.route("contact-us"),
                        class: "thm-btn"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(trans("Get Started")) + " ", 1),
                          createVNode("span", { class: "icon-right-arrow" })
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "about-three" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: "about-three__left wow slideInLeft",
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "about-three__img-box" }, [
                          createVNode("div", { class: "about-three__img" }, [
                            createVNode("img", {
                              src: asset_path.value + "images/home/about_us.jpg",
                              alt: ""
                            }, null, 8, ["src"])
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "about-three__right" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("About Us")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, [
                            createTextVNode(toDisplayString(trans("Any IT Problem Solutions And")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("Grow Your Business")), 1)
                          ])
                        ]),
                        createVNode("p", { class: "about-three__text" }, toDisplayString(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth.")), 1),
                        createVNode("div", { class: "about-three__progress-box" }, [
                          createVNode("div", { class: "progress-box" }, [
                            createVNode("div", { class: "bar-title" }, toDisplayString(trans("Business Problem Solving")), 1),
                            createVNode("div", { class: "bar" }, [
                              createVNode("div", {
                                class: "bar-inner count-bar",
                                "data-percent": "70%"
                              }, [
                                createVNode("div", { class: "count-box" }, [
                                  createVNode("span", {
                                    class: "count-text",
                                    "data-stop": "70",
                                    "data-speed": "1500"
                                  }, "0"),
                                  createTextVNode("% ")
                                ])
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "progress-box" }, [
                            createVNode("div", { class: "bar-title" }, toDisplayString(trans("Camping Launches")), 1),
                            createVNode("div", { class: "bar" }, [
                              createVNode("div", {
                                class: "bar-inner count-bar",
                                "data-percent": "80%"
                              }, [
                                createVNode("div", { class: "count-box" }, [
                                  createVNode("span", {
                                    class: "count-text",
                                    "data-stop": "80",
                                    "data-speed": "1500"
                                  }, "0"),
                                  createTextVNode("% ")
                                ])
                              ])
                            ])
                          ])
                        ]),
                        createVNode("ul", { class: "about-three__points list-unstyled" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-tick-inside-circle" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", null, toDisplayString(trans("Shaping Tomorrow, Transforming Today")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-tick-inside-circle" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", null, toDisplayString(trans("Innovating Today, Empowering Tomorrow")), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "about-three__btn-and-call-box" }, [
                          createVNode("div", { class: "about-three__btn-box" }, [
                            createVNode("a", {
                              href: _ctx.route("about-us"),
                              class: "thm-btn"
                            }, [
                              createTextVNode(toDisplayString(trans("Get in Touch")) + " ", 1),
                              createVNode("span", { class: "icon-right-arrow" })
                            ], 8, ["href"])
                          ]),
                          createVNode("div", { class: "about-three__call-box" }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-customer-service-headset" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("span", null, toDisplayString(trans("Call Any Time")), 1),
                              createVNode("p", null, [
                                createVNode("a", { href: "tel:{{settings.phone}}" }, toDisplayString(settings.value.phone), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              services.value && services.value.length ? (openBlock(), createBlock("section", {
                key: 0,
                class: "services-three"
              }, [
                createVNode("div", { class: "services-three__shape-1" }),
                createVNode("div", { class: "services-three__shape-2" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Services")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, [
                      createTextVNode(toDisplayString(trans("Discover What We Offer")) + " ", 1),
                      createVNode("br"),
                      createVNode("span", null, toDisplayString(trans("One Group, Multiple Specializations")), 1)
                    ])
                  ]),
                  createVNode("div", { class: "services-three__carousel owl-theme owl-carousel" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(services.value, (service) => {
                      return openBlock(), createBlock("div", {
                        key: service.id,
                        class: "item"
                      }, [
                        createVNode("div", { class: "services-three__single" }, [
                          createVNode("div", { class: "services-three__icon-and-title" }, [
                            createVNode("div", { class: "services-three__icon" }, [
                              createVNode("span", { class: "icon-technical-support" })
                            ]),
                            createVNode("h3", { class: "services-three__title" }, [
                              createVNode(unref(Link), {
                                href: getServiceUrl(service)
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(translateField(service.title)), 1)
                                ]),
                                _: 2
                              }, 1032, ["href"])
                            ])
                          ]),
                          createVNode("p", { class: "services-three__text" }, toDisplayString(translateField(service.description)), 1),
                          service.category ? (openBlock(), createBlock("ul", {
                            key: 0,
                            class: "list-unstyled services-three__list"
                          }, [
                            createVNode("li", null, [
                              createVNode("div", { class: "icon" }, [
                                createVNode("span", { class: "icon-tick-inside-circle" })
                              ]),
                              createVNode("div", { class: "text" }, [
                                createVNode("p", null, toDisplayString(translateField(service.category.name)), 1)
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          createVNode(unref(Link), {
                            href: getServiceUrl(service),
                            class: "services-three__btn"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                              createVNode("span", { class: "icon-right-arrow-1" })
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ]);
                    }), 128))
                  ]),
                  createVNode("div", { class: "text-center mt-4" }, [
                    createVNode(unref(Link), {
                      href: _ctx.route("services.index"),
                      class: "thm-btn"
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(trans("View All Services")) + " ", 1),
                        createVNode("span", { class: "icon-right-arrow" })
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ])
                ])
              ])) : createCommentVNode("", true),
              createVNode("section", { class: "why-choose-two" }, [
                createVNode("div", { class: "why-choose-two__shape-1 float-bob-y" }, [
                  createVNode("img", {
                    src: asset_path.value + "site/images/shapes/why-choose-two-shape-1.png",
                    alt: ""
                  }, null, 8, ["src"])
                ]),
                createVNode("div", { class: "why-choose-two__shape-2" }),
                createVNode("div", { class: "why-choose-two__shape-3" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: "why-choose-two__left wow slideInLeft",
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "why-choose-two__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "site/images/resources/why-choose-two-img-1.png",
                            alt: ""
                          }, null, 8, ["src"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "why-choose-two__right" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Why Choose Us")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, [
                            createTextVNode(toDisplayString(trans("Elevate Growth with Our IT Solutions")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("for Success")), 1)
                          ])
                        ]),
                        createVNode("p", { class: "why-choose-one__text" }, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1),
                        createVNode("ul", { class: "list-unstyled why-choose-two__points" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-earning" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("Industry Experience")), 1),
                              createVNode("p", null, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-customer-service-headset" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("24/7 Customer Support")), 1),
                              createVNode("p", null, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-quality" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("Trust & Reliability")), 1),
                              createVNode("p", null, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "feature-one" }, [
                createVNode("div", { class: "feature-one__shape-1" }),
                createVNode("div", { class: "feature-one__shape-2 float-bob-y" }, [
                  createVNode("img", {
                    src: asset_path.value + "site/images/shapes/feature-one-shape-2.png",
                    alt: ""
                  }, null, 8, ["src"])
                ]),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", {
                      class: "col-xl-4 col-lg-4 wow fadeInUp",
                      "data-wow-delay": "100ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "site/images/resources/feature-one-img-1-1.png",
                            alt: ""
                          }, null, 8, ["src"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Security Services")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-4 col-lg-4 wow fadeInUp",
                      "data-wow-delay": "300ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "site/images/resources/feature-one-img-1-2.png",
                            alt: ""
                          }, null, 8, ["src"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Data Privacy")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-4 col-lg-4 wow fadeInUp",
                      "data-wow-delay": "500ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "site/images/resources/feature-one-img-1-3.png",
                            alt: ""
                          }, null, 8, ["src"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Industry Certified")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Innovating and empowering businesses with tailored solutions for success and growth. Innovating and empowering")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "cta-one" }, [
                createVNode("div", {
                  class: "cta-one__shape-bg float-bob-y",
                  style: { backgroundImage: `url(${asset_path.value}site/images/shapes/cta-one-shape-bg.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "cta-one__inner" }, [
                    createVNode("h3", { class: "cta-one__title" }, toDisplayString(trans("To make requests for further information, contact us")), 1),
                    createVNode("div", { class: "cta-one__contact-info" }, [
                      createVNode("div", { class: "cta-one__contact-icon" }, [
                        createVNode("span", { class: "icon-customer-service-headset" })
                      ]),
                      createVNode("div", { class: "cta-one__contact-details" }, [
                        createVNode("p", null, toDisplayString(trans("Call Us For Any inquiry")), 1),
                        createVNode("h4", null, [
                          createVNode("a", { href: "tel:9900567780" }, "+99 (00) 567 780")
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              testimonials.value && testimonials.value.length ? (openBlock(), createBlock("section", {
                key: 1,
                class: "testimonial-two"
              }, [
                createVNode("div", { class: "testimonial-two__shape-1" }),
                createVNode("div", { class: "testimonial-two__shape-2" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Testimonials")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, toDisplayString(trans("What Our Clients Say")), 1)
                  ]),
                  createVNode("div", { class: "testimonial-two__carousel owl-theme owl-carousel" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(testimonials.value, (testimonial) => {
                      return openBlock(), createBlock("div", {
                        class: "item",
                        key: testimonial.id
                      }, [
                        createVNode("div", { class: "testimonial-two__single" }, [
                          createVNode("div", { class: "testimonial-two__single-inner" }, [
                            createVNode("div", { class: "testimonial-two__star" }, [
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-star" }),
                              createVNode("span", { class: "icon-star" })
                            ]),
                            createVNode("p", { class: "testimonial-two__text" }, toDisplayString(translateField(testimonial.quote)), 1)
                          ]),
                          createVNode("div", { class: "testimonial-two__client-info" }, [
                            createVNode("div", { class: "testimonial-two__client-img" }, [
                              createVNode("img", {
                                src: testimonial.avatar_link,
                                alt: translateField(testimonial.name)
                              }, null, 8, ["src", "alt"])
                            ]),
                            createVNode("div", { class: "testimonial-two__client-content" }, [
                              createVNode("h4", { class: "testimonial-two__client-name" }, toDisplayString(translateField(testimonial.name)), 1),
                              createVNode("p", { class: "testimonial-two__sub-title" }, toDisplayString(translateField(testimonial.position)), 1)
                            ])
                          ]),
                          createVNode("div", { class: "testimonial-two__quote" }, [
                            createVNode("span", { class: "icon-right-quote" })
                          ])
                        ])
                      ]);
                    }), 128))
                  ])
                ])
              ])) : createCommentVNode("", true),
              posts.value && posts.value.length ? (openBlock(), createBlock("section", {
                key: 2,
                class: "blog-two blog-three"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-4" }, [
                      createVNode("div", {
                        class: "blog-two__left wow fadeInLeft",
                        "data-wow-delay": "100ms"
                      }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style1" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Blog Posts")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, toDisplayString(trans("Read Latest News & Blogs")), 1)
                        ]),
                        createVNode("p", { class: "blog-two-text" }, toDisplayString(trans("We deliver complete solutions in printing and branding. From premium banner printing and durable apparel printing to custom Alucobond facades and detailed laser cutting, our team combines expertise and innovation to bring your ideas to life with exceptional quality.")), 1),
                        createVNode("div", { class: "blog-two__top-btn-box" }, [
                          createVNode(unref(Link), {
                            href: _ctx.route("blogs.index"),
                            class: "thm-btn"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(trans("View All Blogs")) + " ", 1),
                              createVNode("span", { class: "icon-right-arrow" })
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-8" }, [
                      createVNode("div", { class: "row" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(posts.value, (post) => {
                          return openBlock(), createBlock("div", {
                            class: "col-md-6 col-lg-4 mb-4",
                            key: post.id
                          }, [
                            createVNode("div", { class: "blog-two__single" }, [
                              createVNode("div", { class: "blog-two__img" }, [
                                createVNode(unref(Link), {
                                  href: getPostUrl(post)
                                }, {
                                  default: withCtx(() => [
                                    createVNode("img", {
                                      src: post.image_link,
                                      alt: translateField(post.title)
                                    }, null, 8, ["src", "alt"])
                                  ]),
                                  _: 2
                                }, 1032, ["href"])
                              ]),
                              createVNode("div", { class: "blog-two__content" }, [
                                createVNode("ul", { class: "blog-two__meta list-unstyled" }, [
                                  post.created_at ? (openBlock(), createBlock("li", { key: 0 }, [
                                    createVNode("span", { class: "far fa-calendar-alt" }),
                                    createTextVNode(" " + toDisplayString(post.created_at), 1)
                                  ])) : createCommentVNode("", true),
                                  post.category ? (openBlock(), createBlock("li", { key: 1 }, [
                                    createVNode("span", { class: "fal fa-folder-open" }),
                                    createTextVNode(" " + toDisplayString(translateField(post.category.name)), 1)
                                  ])) : createCommentVNode("", true)
                                ]),
                                createVNode("h3", { class: "blog-two__title" }, [
                                  createVNode(unref(Link), {
                                    href: getPostUrl(post)
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(translateField(post.title)), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ]),
                                createVNode("p", { class: "blog-two__text" }, toDisplayString(translateField(post.description)), 1),
                                createVNode("div", { class: "blog-two__btn-box" }, [
                                  createVNode(unref(Link), {
                                    href: getPostUrl(post),
                                    class: "thm-btn"
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                                      createVNode("span", { class: "icon-right-arrow" })
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ])
                            ])
                          ]);
                        }), 128))
                      ])
                    ])
                  ])
                ])
              ])) : createCommentVNode("", true),
              createVNode("section", { class: "contact-two" }, [
                createVNode("ul", { class: "contact-two__sliding-text-list list-unstyled marquee_mode-2" }, [
                  createVNode("li", null, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ]),
                  createVNode("li", null, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ]),
                  createVNode("li", null, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ])
                ]),
                createVNode("div", {
                  class: "contact-two__bg",
                  style: { backgroundImage: `url(${asset_path.value}site/images/backgrounds/contact-two-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "contact-two__shape-1 float-bob-y" }, [
                  createVNode("img", {
                    src: asset_path.value + "site/images/shapes/contact-two-shape-1.png",
                    alt: ""
                  }, null, 8, ["src"])
                ]),
                createVNode("div", { class: "contact-two__shape-2" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "contact-two__left" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Get In Touch")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, toDisplayString(trans("Contact Us")), 1)
                        ]),
                        createVNode("p", { class: "contact-two__text" }, toDisplayString(trans("Fill out the form below and we'll get back to you as soon as possible")), 1),
                        createVNode("ul", { class: "contact-two__contact-list list-unstyled" }, [
                          settings.value.website_email ? (openBlock(), createBlock("li", { key: 0 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-mail" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("span", null, toDisplayString(trans("Email")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  href: `mailto:${settings.value.website_email}`
                                }, toDisplayString(settings.value.website_email), 9, ["href"])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          settings.value.website_phone ? (openBlock(), createBlock("li", { key: 1 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-phone-call" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("span", null, toDisplayString(trans("Phone")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  href: `tel:${settings.value.website_phone}`
                                }, toDisplayString(settings.value.website_phone), 9, ["href"])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          settings.value.website_address ? (openBlock(), createBlock("li", { key: 2 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-pin" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("span", null, toDisplayString(trans("Address")), 1),
                              createVNode("p", null, toDisplayString(settings.value.website_address), 1)
                            ])
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: "contact-two__right wow slideInRight",
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("form", {
                          class: "contact-form-validated contact-one__form",
                          onSubmit: withModifiers(handleContactSubmit, ["prevent"])
                        }, [
                          createVNode("div", { class: "row" }, [
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Name")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-user-1" })
                                ]),
                                withDirectives(createVNode("input", {
                                  type: "text",
                                  name: "name",
                                  "onUpdate:modelValue": ($event) => unref(contactForm).name = $event,
                                  placeholder: trans("Name"),
                                  disabled: unref(contactForm).processing,
                                  class: { "error": unref(contactForm).errors.name },
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).name]
                                ]),
                                unref(contactForm).errors.name ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.name), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Email")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-email" })
                                ]),
                                withDirectives(createVNode("input", {
                                  type: "email",
                                  name: "email",
                                  "onUpdate:modelValue": ($event) => unref(contactForm).email = $event,
                                  placeholder: trans("Email"),
                                  disabled: unref(contactForm).processing,
                                  class: { "error": unref(contactForm).errors.email },
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).email]
                                ]),
                                unref(contactForm).errors.email ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.email), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Phone")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-phone-call" })
                                ]),
                                withDirectives(createVNode("input", {
                                  type: "text",
                                  name: "mobile",
                                  "onUpdate:modelValue": ($event) => unref(contactForm).mobile = $event,
                                  placeholder: trans("Phone"),
                                  disabled: unref(contactForm).processing,
                                  class: { "error": unref(contactForm).errors.mobile },
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).mobile]
                                ]),
                                unref(contactForm).errors.mobile ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.mobile), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Subject")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-edit" })
                                ]),
                                withDirectives(createVNode("input", {
                                  type: "text",
                                  name: "subject",
                                  "onUpdate:modelValue": ($event) => unref(contactForm).subject = $event,
                                  placeholder: trans("Subject"),
                                  disabled: unref(contactForm).processing,
                                  class: { "error": unref(contactForm).errors.subject },
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).subject]
                                ]),
                                unref(contactForm).errors.subject ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.subject), 1)) : createCommentVNode("", true)
                              ])
                            ])
                          ]),
                          createVNode("div", { class: "col-xl-12" }, [
                            createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Message")), 1),
                            createVNode("div", { class: "contact-one__input-box text-message-box" }, [
                              createVNode("div", { class: "contact-one__input-icon" }, [
                                createVNode("span", { class: "icon-edit" })
                              ]),
                              withDirectives(createVNode("textarea", {
                                name: "message",
                                "onUpdate:modelValue": ($event) => unref(contactForm).message = $event,
                                placeholder: trans("Message"),
                                disabled: unref(contactForm).processing,
                                class: { "error": unref(contactForm).errors.message },
                                required: ""
                              }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                [vModelText, unref(contactForm).message]
                              ]),
                              unref(contactForm).errors.message ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.message), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "contact-one__btn-box" }, [
                              createVNode("button", {
                                type: "submit",
                                class: ["thm-btn", { "opacity-50": unref(contactForm).processing }],
                                disabled: unref(contactForm).processing
                              }, [
                                unref(contactForm).processing ? (openBlock(), createBlock("span", { key: 0 }, [
                                  createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                                  createTextVNode(" " + toDisplayString(trans("Sending...")), 1)
                                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createTextVNode(toDisplayString(trans("Send Message")) + " ", 1),
                                  createVNode("i", { class: "icon-right-arrow" })
                                ]))
                              ], 10, ["disabled"])
                            ]),
                            contactSubmitSuccess.value ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "mt-3 alert alert-success"
                            }, toDisplayString(trans("Thank you for contacting us! We will get back to you soon.")), 1)) : createCommentVNode("", true)
                          ])
                        ], 32),
                        createVNode("div", { class: "result" })
                      ])
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Base/resources/assets/js/Pages/Index.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$e
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$d = {
  __name: "PartnersBrand",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const asset_path = computed(() => page.props.asset_path || "");
    const partners = [
      {
        url: "https://shamexstore.com",
        src: "images/partners/shamex.png",
        alt: "Shamex",
        delay: ".1s"
      },
      {
        url: "http://sham-media.net",
        src: "images/partners/sham_media.png",
        alt: "Sham Media",
        delay: ".2s"
      },
      {
        url: "https://ali-pasha.com",
        src: "images/partners/ali_pasha.png",
        alt: "Ali Pasa",
        delay: ".3s"
      }
    ];
    onMounted(() => {
      nextTick(() => {
        if (window.Swiper) {
          const brandEl = document.querySelector(".brand__active");
          if (brandEl && !brandEl.swiper) {
            new window.Swiper(".brand__active", {
              slidesPerView: 3,
              spaceBetween: 30,
              loop: true,
              autoplay: {
                delay: 2e3,
                disableOnInteraction: false
              },
              breakpoints: {
                1200: {
                  slidesPerView: 3
                },
                992: {
                  slidesPerView: 3
                },
                768: {
                  slidesPerView: 2
                },
                576: {
                  slidesPerView: 2
                },
                0: {
                  slidesPerView: 1
                }
              }
            });
          }
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<section${ssrRenderAttrs(mergeProps({ class: "main-brand__area section-space" }, _attrs))}><div class="brand__area"><div class="container"><div class="row"><div class="col-12"><div class="blog-top heading-space2 justify-content-center mb-40"><div class="latest-blog__title-wrapper text-center"><h6 class="subtitle wow fadeInLeft animated" data-wow-delay=".2s"><i class="fa fa-handshake mx-1"></i> ${ssrInterpolate(trans("Our Partners"))}</h6><h2 class="title wow fadeInLeft animated" data-wow-delay=".4s">${ssrInterpolate(trans("One Group, Multiple Specializations"))}</h2></div></div></div></div><div class="row"><div class="col-12"><div class="swiper brand__active wow fadeIn" data-wow-delay=".3s"><div class="swiper-wrapper"><!--[-->`);
      ssrRenderList(partners, (partner, index) => {
        _push(`<div class="swiper-slide"><div class="brand__item text-center wow fadeIn animated"${ssrRenderAttr("data-wow-delay", partner.delay)}><div class="brand__thumb"><img class="img-fluid"${ssrRenderAttr("src", asset_path.value + partner.src)}${ssrRenderAttr("alt", partner.alt)}></div></div></div>`);
      });
      _push(`<!--]--></div></div></div></div></div></div></section>`);
    };
  }
};
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/PartnersBrand.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const __default__$8 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$c = /* @__PURE__ */ Object.assign(__default__$8, {
  __name: "AboutUs",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const asset_path = computed(() => page.props.asset_path || "");
    const settings = computed(() => page.props.settings);
    const storage_path = computed(() => page.props.storage_path);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("About Us"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("About Us")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-1bd5c1d5${_scopeId}><div class="container" data-v-1bd5c1d5${_scopeId}><div class="row align-items-center justify-content-center m-auto" data-v-1bd5c1d5${_scopeId}><div class="col-12" data-v-1bd5c1d5${_scopeId}><div class="breadcrumb__content text-center" data-v-1bd5c1d5${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-1bd5c1d5${_scopeId}><h1 class="breadcrumb__title wow fadeIn animated" data-wow-delay=".1s" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("About Us"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-1bd5c1d5${_scopeId}><nav data-v-1bd5c1d5${_scopeId}><ul data-v-1bd5c1d5${_scopeId}><li data-v-1bd5c1d5${_scopeId}><span data-v-1bd5c1d5${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-1bd5c1d5${_scopeId}><span data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("About Us"))}</span></li></ul></nav></div></div></div></div></div></div><section class="latest-about2__area section-space overflow-hidden" data-v-1bd5c1d5${_scopeId}><div class="container p-relative z-index-1" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__all-shape" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__all-shape-bg-shape" data-v-1bd5c1d5${_scopeId}><img class="upDown-bottom"${ssrRenderAttr("src", asset_path.value + "images/about-us/about2-bg-shape.svg")} alt="img not found" data-v-1bd5c1d5${_scopeId}></div><div class="latest-about2__all-shape-circle-shape" data-v-1bd5c1d5${_scopeId}><img class="zooming"${ssrRenderAttr("src", asset_path.value + "images/about-us/logo-thump.svg")} alt="img not found" data-v-1bd5c1d5${_scopeId}></div></div><div class="row" data-v-1bd5c1d5${_scopeId}><div class="col-xl-6 col-lg-6" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__content" data-v-1bd5c1d5${_scopeId}><h6 class="latest-about2__content-subtitle" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("About Us"))}</h6><h2 class="latest-about2__content-title" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("More than 12 years of experience in the field"))}</h2><div class="latest-about2__content-description" data-v-1bd5c1d5${_scopeId}><p data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Sham Vision is one of the leading companies in Syria, with more than 10 years of experience in providing comprehensive and innovative solutions that meet the needs of the advertising and publicity sector. The company has branches in Damascus, Aleppo, and the Idlib region, providing all printing and advertising supplies, including raw materials, inks, and the latest specialized machinery. Thanks to its exclusive agencies from leading Chinese companies, the company guarantees high-quality products at competitive prices. The organization continuously strives to enhance its leadership by expanding its network and focusing on innovation and quality in its services"))}</p></div></div></div><div class="col-lg-6 mt-3" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__media" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__media-img1 mt-10" data-v-1bd5c1d5${_scopeId}><img${ssrRenderAttr("src", storage_path.value + settings.value.about_us_img1)} class="img-fluid" alt="img not found" data-v-1bd5c1d5${_scopeId}></div></div></div><div class="col-lg-6 mt-3" data-v-1bd5c1d5${_scopeId}>`);
            if (settings.value.about_us_video) {
              _push2(`<div data-v-1bd5c1d5${_scopeId}><div class="container w-100 text-center" data-v-1bd5c1d5${_scopeId}><div data-v-1bd5c1d5${_scopeId}>${settings.value.about_us_video ?? ""}</div></div></div>`);
            } else {
              _push2(`<div data-v-1bd5c1d5${_scopeId}><div class="latest-about2__media" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__media-img1 mt-10" data-v-1bd5c1d5${_scopeId}><img${ssrRenderAttr("src", storage_path.value + settings.value.about_us_img2)} class="img-fluid" alt="img not found" data-v-1bd5c1d5${_scopeId}></div></div></div>`);
            }
            _push2(`</div><div class="col-lg-6" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__content-text" data-v-1bd5c1d5${_scopeId}><div class="latest-about2__content-description mb-5" data-v-1bd5c1d5${_scopeId}></div><span data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("How do we work?"))}</span><ul data-v-1bd5c1d5${_scopeId}><li data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-check" data-v-1bd5c1d5${_scopeId}></i>${ssrInterpolate(trans("Support and follow-up"))}</li><li data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-check" data-v-1bd5c1d5${_scopeId}></i>${ssrInterpolate(trans("Comprehensive and fast service"))}</li><li data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-check" data-v-1bd5c1d5${_scopeId}></i>${ssrInterpolate(trans("Providing integrated products"))}</li><li data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-check" data-v-1bd5c1d5${_scopeId}></i>${ssrInterpolate(trans("Analyzing customer needs"))}</li></ul></div><div class="latest-about2__content-btn" data-v-1bd5c1d5${_scopeId}><a${ssrRenderAttr("href", asset_path.value + "files/catalog.pdf")} class="rr-btn" download data-v-1bd5c1d5${_scopeId}><i class="fa fa-file-pdf" data-v-1bd5c1d5${_scopeId}></i> ${ssrInterpolate(trans("Our Catalog"))}</a></div></div></div></div></section><section class="overflow-hidden" data-v-1bd5c1d5${_scopeId}><div class="container" data-v-1bd5c1d5${_scopeId}><div class="row justify-content-center" data-v-1bd5c1d5${_scopeId}><div class="col-xl-10 col-lg-11" data-v-1bd5c1d5${_scopeId}><div class="text-center mb-40" data-v-1bd5c1d5${_scopeId}><h2 class="latest-about2__content-title" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Our Values"))}</h2></div><div class="row gy-4" data-v-1bd5c1d5${_scopeId}><div class="col-md-6" data-v-1bd5c1d5${_scopeId}><div class="d-flex align-items-start gap-3" data-v-1bd5c1d5${_scopeId}><div class="fs-2 text-danger flex-shrink-0" data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-award" data-v-1bd5c1d5${_scopeId}></i></div><div data-v-1bd5c1d5${_scopeId}><h4 class="mb-1" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Quality"))}</h4><p class="mb-0" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Commitment to providing the best products and services with high standards that meet our clients expectations"))}</p></div></div></div><div class="col-md-6" data-v-1bd5c1d5${_scopeId}><div class="d-flex align-items-start gap-3" data-v-1bd5c1d5${_scopeId}><div class="fs-2 text-danger flex-shrink-0" data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-lightbulb" data-v-1bd5c1d5${_scopeId}></i></div><div data-v-1bd5c1d5${_scopeId}><h4 class="mb-1" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Innovation"))}</h4><p class="mb-0" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Developing creative solutions and keeping pace with the latest technologies in the advertising and printing field"))}</p></div></div></div><div class="col-md-6" data-v-1bd5c1d5${_scopeId}><div class="d-flex align-items-start gap-3" data-v-1bd5c1d5${_scopeId}><div class="fs-2 text-danger flex-shrink-0" data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-handshake-angle" data-v-1bd5c1d5${_scopeId}></i></div><div data-v-1bd5c1d5${_scopeId}><h4 class="mb-1" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Transparency"))}</h4><p class="mb-0" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Building strong relationships with our clients based on trust and credibility"))}</p></div></div></div><div class="col-md-6" data-v-1bd5c1d5${_scopeId}><div class="d-flex align-items-start gap-3" data-v-1bd5c1d5${_scopeId}><div class="fs-2 text-danger flex-shrink-0" data-v-1bd5c1d5${_scopeId}><i class="fa-solid fa-list-check" data-v-1bd5c1d5${_scopeId}></i></div><div data-v-1bd5c1d5${_scopeId}><h4 class="mb-1" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Commitment"))}</h4><p class="mb-0" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Constantly striving to achieve customer satisfaction and support their success through our integrated services"))}</p></div></div></div></div></div></div></div></section><section class="section-space overflow-hidden" data-v-1bd5c1d5${_scopeId}><div class="container" data-v-1bd5c1d5${_scopeId}><div class="row g-4 align-items-stretch" data-v-1bd5c1d5${_scopeId}><div class="col-lg-6" data-v-1bd5c1d5${_scopeId}><div class="p-4 h-100 bg-white shadow-sm rounded-3" data-v-1bd5c1d5${_scopeId}><h2 class="latest-about2__content-title mb-3 text-danger" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Our Vision"))}</h2><p class="fs-5 mb-0" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("To be the leading company in the advertising and printing sector in Syria and the Middle East"))}</p></div></div><div class="col-lg-6" data-v-1bd5c1d5${_scopeId}><div class="p-4 h-100 text-white rounded-3" style="${ssrRenderStyle({ "background-color": "#cc3333" })}" data-v-1bd5c1d5${_scopeId}><h2 class="latest-about2__content-title mb-3 text-white" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("Our Mission"))}</h2><p class="fs-5 mb-0 text-white" data-v-1bd5c1d5${_scopeId}>${ssrInterpolate(trans("We aim to provide creative and comprehensive advertising and printing solutions, working to enhance our clients' success by meeting their needs with a focus on continuous innovation and fruitful partnerships"))}</p></div></div></div></div></section>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-center m-auto" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("About Us")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("home")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("About Us")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "latest-about2__area section-space overflow-hidden" }, [
                createVNode("div", { class: "container p-relative z-index-1" }, [
                  createVNode("div", { class: "latest-about2__all-shape" }, [
                    createVNode("div", { class: "latest-about2__all-shape-bg-shape" }, [
                      createVNode("img", {
                        class: "upDown-bottom",
                        src: asset_path.value + "images/about-us/about2-bg-shape.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "latest-about2__all-shape-circle-shape" }, [
                      createVNode("img", {
                        class: "zooming",
                        src: asset_path.value + "images/about-us/logo-thump.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ])
                  ]),
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                      createVNode("div", { class: "latest-about2__content" }, [
                        createVNode("h6", { class: "latest-about2__content-subtitle" }, toDisplayString(trans("About Us")), 1),
                        createVNode("h2", { class: "latest-about2__content-title" }, toDisplayString(trans("More than 12 years of experience in the field")), 1),
                        createVNode("div", { class: "latest-about2__content-description" }, [
                          createVNode("p", null, toDisplayString(trans("Sham Vision is one of the leading companies in Syria, with more than 10 years of experience in providing comprehensive and innovative solutions that meet the needs of the advertising and publicity sector. The company has branches in Damascus, Aleppo, and the Idlib region, providing all printing and advertising supplies, including raw materials, inks, and the latest specialized machinery. Thanks to its exclusive agencies from leading Chinese companies, the company guarantees high-quality products at competitive prices. The organization continuously strives to enhance its leadership by expanding its network and focusing on innovation and quality in its services")), 1)
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-lg-6 mt-3" }, [
                      createVNode("div", { class: "latest-about2__media" }, [
                        createVNode("div", { class: "latest-about2__media-img1 mt-10" }, [
                          createVNode("img", {
                            src: storage_path.value + settings.value.about_us_img1,
                            class: "img-fluid",
                            alt: "img not found"
                          }, null, 8, ["src"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-lg-6 mt-3" }, [
                      settings.value.about_us_video ? (openBlock(), createBlock("div", { key: 0 }, [
                        createVNode("div", { class: "container w-100 text-center" }, [
                          createVNode("div", {
                            innerHTML: settings.value.about_us_video
                          }, null, 8, ["innerHTML"])
                        ])
                      ])) : (openBlock(), createBlock("div", { key: 1 }, [
                        createVNode("div", { class: "latest-about2__media" }, [
                          createVNode("div", { class: "latest-about2__media-img1 mt-10" }, [
                            createVNode("img", {
                              src: storage_path.value + settings.value.about_us_img2,
                              class: "img-fluid",
                              alt: "img not found"
                            }, null, 8, ["src"])
                          ])
                        ])
                      ]))
                    ]),
                    createVNode("div", { class: "col-lg-6" }, [
                      createVNode("div", { class: "latest-about2__content-text" }, [
                        createVNode("div", { class: "latest-about2__content-description mb-5" }),
                        createVNode("span", null, toDisplayString(trans("How do we work?")), 1),
                        createVNode("ul", null, [
                          createVNode("li", null, [
                            createVNode("i", { class: "fa-solid fa-check" }),
                            createTextVNode(toDisplayString(trans("Support and follow-up")), 1)
                          ]),
                          createVNode("li", null, [
                            createVNode("i", { class: "fa-solid fa-check" }),
                            createTextVNode(toDisplayString(trans("Comprehensive and fast service")), 1)
                          ]),
                          createVNode("li", null, [
                            createVNode("i", { class: "fa-solid fa-check" }),
                            createTextVNode(toDisplayString(trans("Providing integrated products")), 1)
                          ]),
                          createVNode("li", null, [
                            createVNode("i", { class: "fa-solid fa-check" }),
                            createTextVNode(toDisplayString(trans("Analyzing customer needs")), 1)
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "latest-about2__content-btn" }, [
                        createVNode("a", {
                          href: asset_path.value + "files/catalog.pdf",
                          class: "rr-btn",
                          download: ""
                        }, [
                          createVNode("i", { class: "fa fa-file-pdf" }),
                          createTextVNode(" " + toDisplayString(trans("Our Catalog")), 1)
                        ], 8, ["href"])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "overflow-hidden" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row justify-content-center" }, [
                    createVNode("div", { class: "col-xl-10 col-lg-11" }, [
                      createVNode("div", { class: "text-center mb-40" }, [
                        createVNode("h2", { class: "latest-about2__content-title" }, toDisplayString(trans("Our Values")), 1)
                      ]),
                      createVNode("div", { class: "row gy-4" }, [
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "d-flex align-items-start gap-3" }, [
                            createVNode("div", { class: "fs-2 text-danger flex-shrink-0" }, [
                              createVNode("i", { class: "fa-solid fa-award" })
                            ]),
                            createVNode("div", null, [
                              createVNode("h4", { class: "mb-1" }, toDisplayString(trans("Quality")), 1),
                              createVNode("p", { class: "mb-0" }, toDisplayString(trans("Commitment to providing the best products and services with high standards that meet our clients expectations")), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "d-flex align-items-start gap-3" }, [
                            createVNode("div", { class: "fs-2 text-danger flex-shrink-0" }, [
                              createVNode("i", { class: "fa-solid fa-lightbulb" })
                            ]),
                            createVNode("div", null, [
                              createVNode("h4", { class: "mb-1" }, toDisplayString(trans("Innovation")), 1),
                              createVNode("p", { class: "mb-0" }, toDisplayString(trans("Developing creative solutions and keeping pace with the latest technologies in the advertising and printing field")), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "d-flex align-items-start gap-3" }, [
                            createVNode("div", { class: "fs-2 text-danger flex-shrink-0" }, [
                              createVNode("i", { class: "fa-solid fa-handshake-angle" })
                            ]),
                            createVNode("div", null, [
                              createVNode("h4", { class: "mb-1" }, toDisplayString(trans("Transparency")), 1),
                              createVNode("p", { class: "mb-0" }, toDisplayString(trans("Building strong relationships with our clients based on trust and credibility")), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "d-flex align-items-start gap-3" }, [
                            createVNode("div", { class: "fs-2 text-danger flex-shrink-0" }, [
                              createVNode("i", { class: "fa-solid fa-list-check" })
                            ]),
                            createVNode("div", null, [
                              createVNode("h4", { class: "mb-1" }, toDisplayString(trans("Commitment")), 1),
                              createVNode("p", { class: "mb-0" }, toDisplayString(trans("Constantly striving to achieve customer satisfaction and support their success through our integrated services")), 1)
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "section-space overflow-hidden" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row g-4 align-items-stretch" }, [
                    createVNode("div", { class: "col-lg-6" }, [
                      createVNode("div", { class: "p-4 h-100 bg-white shadow-sm rounded-3" }, [
                        createVNode("h2", { class: "latest-about2__content-title mb-3 text-danger" }, toDisplayString(trans("Our Vision")), 1),
                        createVNode("p", { class: "fs-5 mb-0" }, toDisplayString(trans("To be the leading company in the advertising and printing sector in Syria and the Middle East")), 1)
                      ])
                    ]),
                    createVNode("div", { class: "col-lg-6" }, [
                      createVNode("div", {
                        class: "p-4 h-100 text-white rounded-3",
                        style: { "background-color": "#cc3333" }
                      }, [
                        createVNode("h2", { class: "latest-about2__content-title mb-3 text-white" }, toDisplayString(trans("Our Mission")), 1),
                        createVNode("p", { class: "fs-5 mb-0 text-white" }, toDisplayString(trans("We aim to provide creative and comprehensive advertising and printing solutions, working to enhance our clients' success by meeting their needs with a focus on continuous innovation and fruitful partnerships")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode(_sfc_main$d)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/AboutUs.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const AboutUs = /* @__PURE__ */ _export_sfc(_sfc_main$c, [["__scopeId", "data-v-1bd5c1d5"]]);
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: AboutUs
}, Symbol.toStringTag, { value: "Module" }));
const __default__$7 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$b = /* @__PURE__ */ Object.assign(__default__$7, {
  __name: "BlogIndex",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const blogs = computed(() => page.props.blogs);
    const arrowIcon = computed(() => {
      return locale.value === "ar" ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(trans("Blogs"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("Blogs")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-4f4f8fdd${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-4f4f8fdd${_scopeId}></div><div class="container" data-v-4f4f8fdd${_scopeId}><div class="row align-items-center justify-content-between" data-v-4f4f8fdd${_scopeId}><div class="col-12" data-v-4f4f8fdd${_scopeId}><div class="breadcrumb__content text-center" data-v-4f4f8fdd${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-4f4f8fdd${_scopeId}><h1 class="breadcrumb__title wow fadeIn animated" data-wow-delay=".1s" data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(trans("Blogs"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-4f4f8fdd${_scopeId}><nav data-v-4f4f8fdd${_scopeId}><ul data-v-4f4f8fdd${_scopeId}><li data-v-4f4f8fdd${_scopeId}><span data-v-4f4f8fdd${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-4f4f8fdd${_scopeId}><span data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(trans("Blogs"))}</span></li></ul></nav></div></div></div></div></div></div><section class="latest-blog__area mt-25 pb-90 overflow-hidden latest-blog-bg" data-v-4f4f8fdd${_scopeId}><div class="container" data-v-4f4f8fdd${_scopeId}><div class="row mb-minus-30" data-v-4f4f8fdd${_scopeId}>`);
            if (blogs.value.data && blogs.value.data.length > 0) {
              _push2(`<!--[-->`);
              ssrRenderList(blogs.value.data, (blog) => {
                _push2(`<div class="col-lg-4 col-md-6 mb-30" data-v-4f4f8fdd${_scopeId}><div class="swiper-slide latest-blog__item-slide" data-v-4f4f8fdd${_scopeId}><div class="latest-blog__item-slide-inner" data-v-4f4f8fdd${_scopeId}><div class="latest-blog__item-media" data-v-4f4f8fdd${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", blog.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<img${ssrRenderAttr("src", blog.image_link)}${ssrRenderAttr("alt", blog.title)} class="img-fluid" data-v-4f4f8fdd${_scopeId2}>`);
                    } else {
                      return [
                        createVNode("img", {
                          src: blog.image_link,
                          alt: blog.title,
                          class: "img-fluid"
                        }, null, 8, ["src", "alt"])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div><div class="latest-blog__item-text news" data-v-4f4f8fdd${_scopeId}><div class="latest-blog__item-text-meta d-flex" data-v-4f4f8fdd${_scopeId}><div class="latest-blog__item-text-meta-calender" data-v-4f4f8fdd${_scopeId}><h4 data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(blog.created_at_day)}</h4><p data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(blog.created_at_month)}</p></div><span data-v-4f4f8fdd${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.index", { category: blog.category.slug })
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<i class="fa-regular fa-tag" data-v-4f4f8fdd${_scopeId2}></i> ${ssrInterpolate(blog.category.name)}`);
                    } else {
                      return [
                        createVNode("i", { class: "fa-regular fa-tag" }),
                        createTextVNode(" " + toDisplayString(blog.category.name), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</span></div><div class="latest-blog__item-text-bottom" data-v-4f4f8fdd${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", blog.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<h4 data-v-4f4f8fdd${_scopeId2}>${ssrInterpolate(blog.title)}</h4>`);
                    } else {
                      return [
                        createVNode("h4", null, toDisplayString(blog.title), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", blog.slug),
                  class: "readmore d-flex align-items-center"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Read More"))} <i class="${ssrRenderClass(arrowIcon.value)}" data-v-4f4f8fdd${_scopeId2}></i>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                        createVNode("i", { class: arrowIcon.value }, null, 2)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div></div></div></div>`);
              });
              _push2(`<!--]-->`);
            } else {
              _push2(`<div class="col-12" data-v-4f4f8fdd${_scopeId}><div class="text-center py-5" data-v-4f4f8fdd${_scopeId}><h3 class="text-muted" data-v-4f4f8fdd${_scopeId}>${ssrInterpolate(trans("No blogs found"))} <i class="fa fa-xmark text-danger" data-v-4f4f8fdd${_scopeId}></i></h3></div></div>`);
            }
            _push2(`</div>`);
            if (blogs.value.last_page > 1) {
              _push2(`<div class="bottom-button d-flex justify-content-center mt-30" data-v-4f4f8fdd${_scopeId}>`);
              if (blogs.value.links && blogs.value.links[0] && blogs.value.links[0].url) {
                _push2(ssrRenderComponent(unref(Link), {
                  href: blogs.value.links[0].url,
                  class: "page-link"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<i class="${ssrRenderClass(locale.value === "ar" ? "fa-solid fa-angles-right" : "fa-solid fa-angles-left")}" data-v-4f4f8fdd${_scopeId2}></i>`);
                    } else {
                      return [
                        createVNode("i", {
                          class: locale.value === "ar" ? "fa-solid fa-angles-right" : "fa-solid fa-angles-left"
                        }, null, 2)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(`<!--[-->`);
              ssrRenderList(blogs.value.links, (link, index) => {
                _push2(`<!--[-->`);
                if (link.url && index > 0 && index < blogs.value.links.length - 1 && parseInt(link.label) <= 3) {
                  _push2(ssrRenderComponent(unref(Link), {
                    href: link.url,
                    class: ["page-link", link.active ? "active" : ""]
                  }, {
                    default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                      if (_push3) {
                        _push3(`<i class="${ssrRenderClass(`fa-solid fa-${link.label}`)}" data-v-4f4f8fdd${_scopeId2}></i>`);
                      } else {
                        return [
                          createVNode("i", {
                            class: `fa-solid fa-${link.label}`
                          }, null, 2)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent2, _scopeId));
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<!--]-->`);
              });
              _push2(`<!--]-->`);
              if (blogs.value.links && blogs.value.links[blogs.value.links.length - 1] && blogs.value.links[blogs.value.links.length - 1].url) {
                _push2(ssrRenderComponent(unref(Link), {
                  href: blogs.value.links[blogs.value.links.length - 1].url,
                  class: "page-link"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<i class="${ssrRenderClass(locale.value === "ar" ? "fa-solid fa-angles-left" : "fa-solid fa-angles-right")}" data-v-4f4f8fdd${_scopeId2}></i>`);
                    } else {
                      return [
                        createVNode("i", {
                          class: locale.value === "ar" ? "fa-solid fa-angles-left" : "fa-solid fa-angles-right"
                        }, null, 2)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></section>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-between" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("Blogs")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("home")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("Blogs")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "latest-blog__area mt-25 pb-90 overflow-hidden latest-blog-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row mb-minus-30" }, [
                    blogs.value.data && blogs.value.data.length > 0 ? (openBlock(true), createBlock(Fragment, { key: 0 }, renderList(blogs.value.data, (blog) => {
                      return openBlock(), createBlock("div", {
                        key: blog.id,
                        class: "col-lg-4 col-md-6 mb-30"
                      }, [
                        createVNode("div", { class: "swiper-slide latest-blog__item-slide" }, [
                          createVNode("div", { class: "latest-blog__item-slide-inner" }, [
                            createVNode("div", { class: "latest-blog__item-media" }, [
                              createVNode(unref(Link), {
                                href: _ctx.route("blogs.show", blog.slug)
                              }, {
                                default: withCtx(() => [
                                  createVNode("img", {
                                    src: blog.image_link,
                                    alt: blog.title,
                                    class: "img-fluid"
                                  }, null, 8, ["src", "alt"])
                                ]),
                                _: 2
                              }, 1032, ["href"])
                            ]),
                            createVNode("div", { class: "latest-blog__item-text news" }, [
                              createVNode("div", { class: "latest-blog__item-text-meta d-flex" }, [
                                createVNode("div", { class: "latest-blog__item-text-meta-calender" }, [
                                  createVNode("h4", null, toDisplayString(blog.created_at_day), 1),
                                  createVNode("p", null, toDisplayString(blog.created_at_month), 1)
                                ]),
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("blogs.index", { category: blog.category.slug })
                                  }, {
                                    default: withCtx(() => [
                                      createVNode("i", { class: "fa-regular fa-tag" }),
                                      createTextVNode(" " + toDisplayString(blog.category.name), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ])
                              ]),
                              createVNode("div", { class: "latest-blog__item-text-bottom" }, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.show", blog.slug)
                                }, {
                                  default: withCtx(() => [
                                    createVNode("h4", null, toDisplayString(blog.title), 1)
                                  ]),
                                  _: 2
                                }, 1032, ["href"]),
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.show", blog.slug),
                                  class: "readmore d-flex align-items-center"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                                    createVNode("i", { class: arrowIcon.value }, null, 2)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ])
                            ])
                          ])
                        ])
                      ]);
                    }), 128)) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "col-12"
                    }, [
                      createVNode("div", { class: "text-center py-5" }, [
                        createVNode("h3", { class: "text-muted" }, [
                          createTextVNode(toDisplayString(trans("No blogs found")) + " ", 1),
                          createVNode("i", { class: "fa fa-xmark text-danger" })
                        ])
                      ])
                    ]))
                  ]),
                  blogs.value.last_page > 1 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "bottom-button d-flex justify-content-center mt-30"
                  }, [
                    blogs.value.links && blogs.value.links[0] && blogs.value.links[0].url ? (openBlock(), createBlock(unref(Link), {
                      key: 0,
                      href: blogs.value.links[0].url,
                      class: "page-link"
                    }, {
                      default: withCtx(() => [
                        createVNode("i", {
                          class: locale.value === "ar" ? "fa-solid fa-angles-right" : "fa-solid fa-angles-left"
                        }, null, 2)
                      ]),
                      _: 1
                    }, 8, ["href"])) : createCommentVNode("", true),
                    (openBlock(true), createBlock(Fragment, null, renderList(blogs.value.links, (link, index) => {
                      return openBlock(), createBlock(Fragment, { key: index }, [
                        link.url && index > 0 && index < blogs.value.links.length - 1 && parseInt(link.label) <= 3 ? (openBlock(), createBlock(unref(Link), {
                          key: 0,
                          href: link.url,
                          class: ["page-link", link.active ? "active" : ""]
                        }, {
                          default: withCtx(() => [
                            createVNode("i", {
                              class: `fa-solid fa-${link.label}`
                            }, null, 2)
                          ]),
                          _: 2
                        }, 1032, ["href", "class"])) : createCommentVNode("", true)
                      ], 64);
                    }), 128)),
                    blogs.value.links && blogs.value.links[blogs.value.links.length - 1] && blogs.value.links[blogs.value.links.length - 1].url ? (openBlock(), createBlock(unref(Link), {
                      key: 1,
                      href: blogs.value.links[blogs.value.links.length - 1].url,
                      class: "page-link"
                    }, {
                      default: withCtx(() => [
                        createVNode("i", {
                          class: locale.value === "ar" ? "fa-solid fa-angles-left" : "fa-solid fa-angles-right"
                        }, null, 2)
                      ]),
                      _: 1
                    }, 8, ["href"])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$d)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/BlogIndex.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const BlogIndex = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["__scopeId", "data-v-4f4f8fdd"]]);
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: BlogIndex
}, Symbol.toStringTag, { value: "Module" }));
const __default__$6 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$a = /* @__PURE__ */ Object.assign(__default__$6, {
  __name: "BlogShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    computed(() => page.props.asset_path || "");
    computed(() => page.props.locale || "en");
    const blog = computed(() => page.props.blog);
    const relatedBlogs = computed(() => page.props.relatedBlogs || []);
    const categories = computed(() => page.props.categories || []);
    const recentPosts = computed(() => page.props.recentPosts || []);
    const previousPost = computed(() => page.props.previousPost);
    const nextPost = computed(() => page.props.nextPost);
    const searchQuery = ref("");
    const getKeywords = (keywords) => {
      if (!keywords) return [];
      if (typeof keywords === "string") {
        return keywords.split(",").slice(0, 3);
      }
      return [];
    };
    const getShareUrl = (platform) => {
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(blog.value.title);
      encodeURIComponent(blog.value.description || blog.value.title);
      switch (platform) {
        case "twitter":
          return `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
        case "facebook":
          return `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        case "linkedin":
          return `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`;
        case "pinterest":
          return `https://pinterest.com/pin/create/button/?url=${url}&description=${title}`;
        default:
          return "#";
      }
    };
    const handleSearch = () => {
      if (searchQuery.value) {
        router.get(route("blogs.index"), { search: searchQuery.value });
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(blog.value.title)} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(blog.value.title) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg"${_scopeId}><div class="banner-home__middel-shape inner-top-shape"${_scopeId}></div><div class="container"${_scopeId}><div class="row align-items-center justify-content-between"${_scopeId}><div class="col-12"${_scopeId}><div class="breadcrumb__content text-center"${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5"${_scopeId}><h1 class="breadcrumb__title wow fadeIn animated d-none d-md-block" data-wow-delay=".1s"${_scopeId}>${ssrInterpolate(blog.value.title)}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s"${_scopeId}><nav${_scopeId}><ul${_scopeId}><li${_scopeId}><span${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li${_scopeId}><span${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("blogs.index")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Blogs"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Blogs")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active"${_scopeId}><span${_scopeId}>${ssrInterpolate(blog.value.title)}</span></li></ul></nav></div></div></div></div></div></div><section class="mt-25"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-8"${_scopeId}><div class="blog__details-content"${_scopeId}><div class="blog__details-thumb mb-30"${_scopeId}><img${ssrRenderAttr("src", blog.value.image_link)}${ssrRenderAttr("alt", blog.value.title)} class="img-fluid"${_scopeId}></div><ul class="blog__details-meta mb-25"${_scopeId}><li${_scopeId}><a href="#"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="18" viewBox="0 0 16 18" fill="none"${_scopeId}><path d="M15.2222 17V15.2222C15.2222 14.2792 14.8476 13.3748 14.1808 12.708C13.514 12.0412 12.6097 11.6666 11.6667 11.6666H4.55556C3.61256 11.6666 2.70819 12.0412 2.0414 12.708C1.3746 13.3748 1 14.2792 1 15.2222V17" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M8.11024 8.11111C10.0739 8.11111 11.6658 6.51923 11.6658 4.55556C11.6658 2.59188 10.0739 1 8.11024 1C6.14656 1 4.55469 2.59188 4.55469 4.55556C4.55469 6.51923 6.14656 8.11111 8.11024 8.11111Z" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg> ${ssrInterpolate(trans("By"))} ${ssrInterpolate(trans("Admin"))}</a></li><li${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none"${_scopeId}><path d="M13 2.50012H2.5C1.67157 2.50012 1 3.17169 1 4.00012V14.5001C1 15.3285 1.67157 16.0001 2.5 16.0001H13C13.8284 16.0001 14.5 15.3285 14.5 14.5001V4.00012C14.5 3.17169 13.8284 2.50012 13 2.50012Z" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M10.752 1V4" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M4.75 1V4" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M1 6.99988H14.5" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg> ${ssrInterpolate(blog.value.created_at_formatted)}</li><li${_scopeId}><a href="#"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"${_scopeId}><path d="M20.59 13.41L12.17 5c-.37-.37-.88-.59-1.41-.59H4c-1.1 0-2 .9-2 2v6.76c0 .53.21 1.04.59 1.41l8.42 8.42c.78.78 2.05.78 2.83 0l6.75-6.75c.78-.78.78-2.05 0-2.83zM6.5 9.5c-.83 0-1.5-.67-1.5-1.5S5.67 6.5 6.5 6.5 8 7.17 8 8s-.67 1.5-1.5 1.5z" stroke="#4A5764" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg> ${ssrInterpolate(blog.value.category.name)}</a></li></ul><div class="blog__details-content-text"${_scopeId}><div${_scopeId}>${blog.value.content ?? ""}</div></div><div class="blog__details-bottom d-flex flex-column flex-md-row justify-content-md-between"${_scopeId}><div class="blog__details-bottom-tags_wapper d-flex align-items-center mb-sm-30 mb-xs-30"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Keywords"))}:</span><div class="blog__details-bottom-tags"${_scopeId}>`);
            if (blog.value.keywords) {
              _push2(`<!--[-->`);
              ssrRenderList(getKeywords(blog.value.keywords), (keyword, index) => {
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.index", { search: keyword.trim() })
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(keyword.trim())}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(keyword.trim()), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]-->`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="share-social-media_wrapper"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Share"))}:</span><div class="share-social-media"${_scopeId}><a${ssrRenderAttr("href", getShareUrl("twitter"))} target="_blank"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="17" height="13" viewBox="0 0 17 13" fill="none"${_scopeId}><path d="M16.8235 0.00728525C16.0912 0.496661 15.2804 0.870955 14.4224 1.11575C13.9618 0.614052 13.3497 0.25846 12.6689 0.0970684C11.9881 -0.064323 11.2714 -0.023726 10.6157 0.213369C9.96004 0.450463 9.39704 0.872616 9.00287 1.42273C8.60869 1.97285 8.40236 2.62438 8.41177 3.28922V4.01371C7.0679 4.04672 5.73627 3.76435 4.53548 3.19174C3.33469 2.61913 2.30201 1.77405 1.52941 0.731774C1.52941 0.731774 -1.52941 7.25217 5.35294 10.1501C3.77805 11.1629 1.90194 11.6708 0 11.5991C6.88235 15.2216 15.2941 11.5991 15.2941 3.26749C15.2934 3.06568 15.2729 2.86438 15.2329 2.66616C16.0134 1.93696 16.5642 1.01629 16.8235 0.00728525Z" fill="white"${_scopeId}></path></svg></a><a${ssrRenderAttr("href", getShareUrl("facebook"))} target="_blank"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="10" height="17" viewBox="0 0 10 17" fill="none"${_scopeId}><path d="M9.35 0H6.8C5.67283 0 4.59183 0.447767 3.7948 1.2448C2.99777 2.04183 2.55 3.12283 2.55 4.25V6.8H0V10.2H2.55V17H5.95V10.2H8.5L9.35 6.8H5.95V4.25C5.95 4.02457 6.03955 3.80837 6.19896 3.64896C6.35837 3.48955 6.57457 3.4 6.8 3.4H9.35V0Z" fill="white"${_scopeId}></path></svg></a><a${ssrRenderAttr("href", getShareUrl("linkedin"))} target="_blank"${_scopeId}><svg width="16" height="15" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg"${_scopeId}><path d="M11.0513 4.73682C12.3075 4.73682 13.5124 5.23587 14.4007 6.1242C15.289 7.01252 15.7881 8.21735 15.7881 9.47363V14.9999H12.6302V9.47363C12.6302 9.05487 12.4638 8.65326 12.1677 8.35715C11.8716 8.06104 11.47 7.89469 11.0513 7.89469C10.6325 7.89469 10.2309 8.06104 9.93479 8.35715C9.63868 8.65326 9.47233 9.05487 9.47233 9.47363V14.9999H6.31445V9.47363C6.31445 8.21735 6.81351 7.01252 7.70183 6.1242C8.59016 5.23587 9.79498 4.73682 11.0513 4.73682Z" fill="white"${_scopeId}></path><path d="M3.15787 5.52612H0V14.9997H3.15787V5.52612Z" fill="white"${_scopeId}></path><path d="M1.57894 3.15787C2.45096 3.15787 3.15787 2.45096 3.15787 1.57894C3.15787 0.706914 2.45096 0 1.57894 0C0.706914 0 0 0.706914 0 1.57894C0 2.45096 0.706914 3.15787 1.57894 3.15787Z" fill="white"${_scopeId}></path></svg></a><a${ssrRenderAttr("href", getShareUrl("pinterest"))} target="_blank"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none"${_scopeId}><path d="M9.00757 0C4.03279 0 0 4.02609 0 8.99262C0 12.8043 2.37242 16.0608 5.7231 17.3707C5.64105 16.6605 5.57519 15.5647 5.75232 14.7878C5.91537 14.0843 6.80489 10.317 6.80489 10.317C6.80489 10.317 6.53832 9.77695 6.53832 8.98488C6.53832 7.73402 7.2648 6.80168 8.16911 6.80168C8.93996 6.80168 9.31112 7.3793 9.31112 8.06766C9.31112 8.83723 8.82199 9.99211 8.56211 11.0654C8.3473 11.9609 9.01426 12.6935 9.89639 12.6935C11.498 12.6935 12.7287 11.006 12.7287 8.57812C12.7287 6.4241 11.1796 4.92188 8.96285 4.92188C6.39816 4.92188 4.89273 6.83895 4.89273 8.82246C4.89273 9.59203 5.18959 10.4214 5.5597 10.8731C5.63471 10.961 5.64245 11.0426 5.61992 11.1312C5.55301 11.4124 5.39771 12.0266 5.36743 12.1528C5.33045 12.3156 5.23361 12.3521 5.06317 12.2713C3.9363 11.7457 3.23165 10.1099 3.23165 8.7852C3.23165 5.9502 5.29242 3.34547 9.1847 3.34547C12.3061 3.34547 14.737 5.56629 14.737 8.54156C14.737 11.5168 12.7801 14.1374 10.0665 14.1374C9.15442 14.1374 8.29412 13.6635 8.00571 13.101C8.00571 13.101 7.55356 14.8177 7.44228 15.2399C7.24261 16.0242 6.69326 17.0016 6.3228 17.601C7.16866 17.859 8.05889 18 8.99278 18C13.9676 18 18.0004 13.9739 18.0004 9.00738C18.0148 4.02609 13.9824 0 9.00757 0Z" fill="white"${_scopeId}></path></svg></a></div></div></div>`);
            if (previousPost.value || nextPost.value) {
              _push2(`<div class="next-prev-post position-relative justify-content-between mt-60 mb-80 d-none d-md-flex"${_scopeId}><div class="line-border"${_scopeId}></div>`);
              if (previousPost.value) {
                _push2(`<div class="prev-post post-wrap"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", previousPost.value.slug),
                  class: "btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("PREV POST"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("PREV POST")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", previousPost.value.slug),
                  class: "post-title d-block"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(previousPost.value.title)}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(previousPost.value.title), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (nextPost.value) {
                _push2(`<div class="next-post post-wrap"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", nextPost.value.slug),
                  class: "btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("NEXT POST"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("NEXT POST")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", nextPost.value.slug),
                  class: "post-title d-block"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(nextPost.value.title)}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(nextPost.value.title), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-4"${_scopeId}><div class="sidebar"${_scopeId}><div class="sidebar__widget"${_scopeId}><h5 class="sidebar__widget-title"${_scopeId}>${ssrInterpolate(trans("Search Here"))}</h5><div class="sidebar__widget-search"${_scopeId}><form${_scopeId}><div class="search__bar"${_scopeId}><button type="submit"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"${_scopeId}><path d="M7.22221 13.4444C10.6586 13.4444 13.4444 10.6586 13.4444 7.22221C13.4444 3.78578 10.6586 1 7.22221 1C3.78578 1 1 3.78578 1 7.22221C1 10.6586 3.78578 13.4444 7.22221 13.4444Z" stroke="#525257" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M15.0005 15L11.6172 11.6167" stroke="#525257" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg></button><input type="text"${ssrRenderAttr("value", searchQuery.value)}${ssrRenderAttr("placeholder", trans("Search"))}${_scopeId}></div></form></div></div><div class="sidebar__widget"${_scopeId}><h5 class="sidebar__widget-title sidebar__widget-title__have-bar"${_scopeId}>${ssrInterpolate(trans("Category"))}</h5><div class="sidebar__widget-category"${_scopeId}><!--[-->`);
            ssrRenderList(categories.value, (category) => {
              _push2(ssrRenderComponent(unref(Link), {
                key: category.id,
                href: _ctx.route("blogs.index", { category: category.slug })
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span${_scopeId2}><svg xmlns="http://www.w3.org/2000/svg" width="5" height="5" viewBox="0 0 5 5" fill="none"${_scopeId2}></svg> ${ssrInterpolate(category.name)}</span><span${_scopeId2}>(${ssrInterpolate(category.blogs_count)})</span>`);
                  } else {
                    return [
                      createVNode("span", null, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          width: "5",
                          height: "5",
                          viewBox: "0 0 5 5",
                          fill: "none"
                        })),
                        createTextVNode(" " + toDisplayString(category.name), 1)
                      ]),
                      createVNode("span", null, "(" + toDisplayString(category.blogs_count) + ")", 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
            });
            _push2(`<!--]--></div></div><div class="sidebar__widget"${_scopeId}><h5 class="sidebar__widget-title"${_scopeId}>${ssrInterpolate(trans("Recent Post"))}</h5><div class="sidebar-post__wrapper"${_scopeId}><!--[-->`);
            ssrRenderList(recentPosts.value, (recentPost) => {
              _push2(`<div class="sidebar-post"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.show", recentPost.slug),
                class: "sidebar-post_thumb"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<img${ssrRenderAttr("src", recentPost.image_link)}${ssrRenderAttr("alt", recentPost.title)}${_scopeId2}>`);
                  } else {
                    return [
                      createVNode("img", {
                        src: recentPost.image_link,
                        alt: recentPost.title
                      }, null, 8, ["src", "alt"])
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`<div class="sidebar-post_content"${_scopeId}><ul class="post-meta"${_scopeId}><li${_scopeId}><svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"${_scopeId}><path d="M15 8C15 11.864 11.864 15 8 15C4.136 15 1 11.864 1 8C1 4.136 4.136 1 8 1C11.864 1 15 4.136 15 8Z" stroke="#FF3D00" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path><path d="M10.5962 10.2259L8.42623 8.93093C8.04823 8.70693 7.74023 8.16793 7.74023 7.72693V4.85693" stroke="#FF3D00" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"${_scopeId}></path></svg> ${ssrInterpolate(recentPost.created_at)}</li></ul>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.show", recentPost.slug)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<h3 class="title rr-fw-medium"${_scopeId2}>${ssrInterpolate(recentPost.title)}</h3>`);
                  } else {
                    return [
                      createVNode("h3", { class: "title rr-fw-medium" }, toDisplayString(recentPost.title), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div></div>`);
            });
            _push2(`<!--]--></div></div>`);
            if (blog.value.keywords) {
              _push2(`<div class="sidebar__widget"${_scopeId}><h5 class="sidebar__widget-title"${_scopeId}>${ssrInterpolate(trans("Keywords"))}</h5><div class="sidebar__widget-tags"${_scopeId}><div class="tags"${_scopeId}><!--[-->`);
              ssrRenderList(getKeywords(blog.value.keywords), (keyword, index) => {
                _push2(ssrRenderComponent(unref(Link), {
                  key: index,
                  href: _ctx.route("blogs.index", { search: keyword.trim() })
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(keyword.trim())}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(keyword.trim()), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
              });
              _push2(`<!--]--></div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-12"${_scopeId}>`);
            if (relatedBlogs.value && relatedBlogs.value.length > 0) {
              _push2(`<div class="related-blogs mt-20 mt-xs-10"${_scopeId}><h4 class="mb-30"${_scopeId}>${ssrInterpolate(trans("Related Blogs"))}:</h4><div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(relatedBlogs.value, (relatedBlog) => {
                _push2(`<div class="col-md-4 mb-30"${_scopeId}><div class="latest-blog__item-slide"${_scopeId}><div class="latest-blog__item-media"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", relatedBlog.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<img${ssrRenderAttr("src", relatedBlog.image_link)}${ssrRenderAttr("alt", relatedBlog.title)} class="img-fluid"${_scopeId2}>`);
                    } else {
                      return [
                        createVNode("img", {
                          src: relatedBlog.image_link,
                          alt: relatedBlog.title,
                          class: "img-fluid"
                        }, null, 8, ["src", "alt"])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div><div class="latest-blog__item-text news"${_scopeId}><div class="latest-blog__item-text-meta d-flex"${_scopeId}><div class="latest-blog__item-text-meta-calender"${_scopeId}><h4${_scopeId}>${ssrInterpolate(relatedBlog.created_at_day)}</h4><p${_scopeId}>${ssrInterpolate(relatedBlog.created_at_month)}</p></div></div><div class="latest-blog__item-text-bottom"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", relatedBlog.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<h4${_scopeId2}>${ssrInterpolate(relatedBlog.title)}</h4>`);
                    } else {
                      return [
                        createVNode("h4", null, toDisplayString(relatedBlog.title), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div></div></div>`);
              });
              _push2(`<!--]--></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-between" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title wow fadeIn animated d-none d-md-block",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(blog.value.title), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("home")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("blogs.index")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Blogs")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(blog.value.title), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "mt-25" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-8" }, [
                      createVNode("div", { class: "blog__details-content" }, [
                        createVNode("div", { class: "blog__details-thumb mb-30" }, [
                          createVNode("img", {
                            src: blog.value.image_link,
                            alt: blog.value.title,
                            class: "img-fluid"
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("ul", { class: "blog__details-meta mb-25" }, [
                          createVNode("li", null, [
                            createVNode("a", { href: "#" }, [
                              (openBlock(), createBlock("svg", {
                                xmlns: "http://www.w3.org/2000/svg",
                                width: "16",
                                height: "18",
                                viewBox: "0 0 16 18",
                                fill: "none"
                              }, [
                                createVNode("path", {
                                  d: "M15.2222 17V15.2222C15.2222 14.2792 14.8476 13.3748 14.1808 12.708C13.514 12.0412 12.6097 11.6666 11.6667 11.6666H4.55556C3.61256 11.6666 2.70819 12.0412 2.0414 12.708C1.3746 13.3748 1 14.2792 1 15.2222V17",
                                  stroke: "#4A5764",
                                  "stroke-width": "1.5",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round"
                                }),
                                createVNode("path", {
                                  d: "M8.11024 8.11111C10.0739 8.11111 11.6658 6.51923 11.6658 4.55556C11.6658 2.59188 10.0739 1 8.11024 1C6.14656 1 4.55469 2.59188 4.55469 4.55556C4.55469 6.51923 6.14656 8.11111 8.11024 8.11111Z",
                                  stroke: "#4A5764",
                                  "stroke-width": "1.5",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(trans("By")) + " " + toDisplayString(trans("Admin")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            (openBlock(), createBlock("svg", {
                              xmlns: "http://www.w3.org/2000/svg",
                              width: "16",
                              height: "17",
                              viewBox: "0 0 16 17",
                              fill: "none"
                            }, [
                              createVNode("path", {
                                d: "M13 2.50012H2.5C1.67157 2.50012 1 3.17169 1 4.00012V14.5001C1 15.3285 1.67157 16.0001 2.5 16.0001H13C13.8284 16.0001 14.5 15.3285 14.5 14.5001V4.00012C14.5 3.17169 13.8284 2.50012 13 2.50012Z",
                                stroke: "#4A5764",
                                "stroke-width": "1.5",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round"
                              }),
                              createVNode("path", {
                                d: "M10.752 1V4",
                                stroke: "#4A5764",
                                "stroke-width": "1.5",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round"
                              }),
                              createVNode("path", {
                                d: "M4.75 1V4",
                                stroke: "#4A5764",
                                "stroke-width": "1.5",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round"
                              }),
                              createVNode("path", {
                                d: "M1 6.99988H14.5",
                                stroke: "#4A5764",
                                "stroke-width": "1.5",
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round"
                              })
                            ])),
                            createTextVNode(" " + toDisplayString(blog.value.created_at_formatted), 1)
                          ]),
                          createVNode("li", null, [
                            createVNode("a", { href: "#" }, [
                              (openBlock(), createBlock("svg", {
                                xmlns: "http://www.w3.org/2000/svg",
                                width: "16",
                                height: "16",
                                viewBox: "0 0 24 24",
                                fill: "none"
                              }, [
                                createVNode("path", {
                                  d: "M20.59 13.41L12.17 5c-.37-.37-.88-.59-1.41-.59H4c-1.1 0-2 .9-2 2v6.76c0 .53.21 1.04.59 1.41l8.42 8.42c.78.78 2.05.78 2.83 0l6.75-6.75c.78-.78.78-2.05 0-2.83zM6.5 9.5c-.83 0-1.5-.67-1.5-1.5S5.67 6.5 6.5 6.5 8 7.17 8 8s-.67 1.5-1.5 1.5z",
                                  stroke: "#4A5764",
                                  "stroke-width": "1.5",
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round"
                                })
                              ])),
                              createTextVNode(" " + toDisplayString(blog.value.category.name), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "blog__details-content-text" }, [
                          createVNode("div", {
                            innerHTML: blog.value.content
                          }, null, 8, ["innerHTML"])
                        ]),
                        createVNode("div", { class: "blog__details-bottom d-flex flex-column flex-md-row justify-content-md-between" }, [
                          createVNode("div", { class: "blog__details-bottom-tags_wapper d-flex align-items-center mb-sm-30 mb-xs-30" }, [
                            createVNode("span", null, toDisplayString(trans("Keywords")) + ":", 1),
                            createVNode("div", { class: "blog__details-bottom-tags" }, [
                              blog.value.keywords ? (openBlock(true), createBlock(Fragment, { key: 0 }, renderList(getKeywords(blog.value.keywords), (keyword, index) => {
                                return openBlock(), createBlock(unref(Link), {
                                  key: index,
                                  href: _ctx.route("blogs.index", { search: keyword.trim() })
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(keyword.trim()), 1)
                                  ]),
                                  _: 2
                                }, 1032, ["href"]);
                              }), 128)) : createCommentVNode("", true)
                            ])
                          ]),
                          createVNode("div", { class: "share-social-media_wrapper" }, [
                            createVNode("span", null, toDisplayString(trans("Share")) + ":", 1),
                            createVNode("div", { class: "share-social-media" }, [
                              createVNode("a", {
                                href: getShareUrl("twitter"),
                                target: "_blank"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  width: "17",
                                  height: "13",
                                  viewBox: "0 0 17 13",
                                  fill: "none"
                                }, [
                                  createVNode("path", {
                                    d: "M16.8235 0.00728525C16.0912 0.496661 15.2804 0.870955 14.4224 1.11575C13.9618 0.614052 13.3497 0.25846 12.6689 0.0970684C11.9881 -0.064323 11.2714 -0.023726 10.6157 0.213369C9.96004 0.450463 9.39704 0.872616 9.00287 1.42273C8.60869 1.97285 8.40236 2.62438 8.41177 3.28922V4.01371C7.0679 4.04672 5.73627 3.76435 4.53548 3.19174C3.33469 2.61913 2.30201 1.77405 1.52941 0.731774C1.52941 0.731774 -1.52941 7.25217 5.35294 10.1501C3.77805 11.1629 1.90194 11.6708 0 11.5991C6.88235 15.2216 15.2941 11.5991 15.2941 3.26749C15.2934 3.06568 15.2729 2.86438 15.2329 2.66616C16.0134 1.93696 16.5642 1.01629 16.8235 0.00728525Z",
                                    fill: "white"
                                  })
                                ]))
                              ], 8, ["href"]),
                              createVNode("a", {
                                href: getShareUrl("facebook"),
                                target: "_blank"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  width: "10",
                                  height: "17",
                                  viewBox: "0 0 10 17",
                                  fill: "none"
                                }, [
                                  createVNode("path", {
                                    d: "M9.35 0H6.8C5.67283 0 4.59183 0.447767 3.7948 1.2448C2.99777 2.04183 2.55 3.12283 2.55 4.25V6.8H0V10.2H2.55V17H5.95V10.2H8.5L9.35 6.8H5.95V4.25C5.95 4.02457 6.03955 3.80837 6.19896 3.64896C6.35837 3.48955 6.57457 3.4 6.8 3.4H9.35V0Z",
                                    fill: "white"
                                  })
                                ]))
                              ], 8, ["href"]),
                              createVNode("a", {
                                href: getShareUrl("linkedin"),
                                target: "_blank"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  width: "16",
                                  height: "15",
                                  viewBox: "0 0 16 15",
                                  fill: "none",
                                  xmlns: "http://www.w3.org/2000/svg"
                                }, [
                                  createVNode("path", {
                                    d: "M11.0513 4.73682C12.3075 4.73682 13.5124 5.23587 14.4007 6.1242C15.289 7.01252 15.7881 8.21735 15.7881 9.47363V14.9999H12.6302V9.47363C12.6302 9.05487 12.4638 8.65326 12.1677 8.35715C11.8716 8.06104 11.47 7.89469 11.0513 7.89469C10.6325 7.89469 10.2309 8.06104 9.93479 8.35715C9.63868 8.65326 9.47233 9.05487 9.47233 9.47363V14.9999H6.31445V9.47363C6.31445 8.21735 6.81351 7.01252 7.70183 6.1242C8.59016 5.23587 9.79498 4.73682 11.0513 4.73682Z",
                                    fill: "white"
                                  }),
                                  createVNode("path", {
                                    d: "M3.15787 5.52612H0V14.9997H3.15787V5.52612Z",
                                    fill: "white"
                                  }),
                                  createVNode("path", {
                                    d: "M1.57894 3.15787C2.45096 3.15787 3.15787 2.45096 3.15787 1.57894C3.15787 0.706914 2.45096 0 1.57894 0C0.706914 0 0 0.706914 0 1.57894C0 2.45096 0.706914 3.15787 1.57894 3.15787Z",
                                    fill: "white"
                                  })
                                ]))
                              ], 8, ["href"]),
                              createVNode("a", {
                                href: getShareUrl("pinterest"),
                                target: "_blank"
                              }, [
                                (openBlock(), createBlock("svg", {
                                  xmlns: "http://www.w3.org/2000/svg",
                                  width: "18",
                                  height: "18",
                                  viewBox: "0 0 18 18",
                                  fill: "none"
                                }, [
                                  createVNode("path", {
                                    d: "M9.00757 0C4.03279 0 0 4.02609 0 8.99262C0 12.8043 2.37242 16.0608 5.7231 17.3707C5.64105 16.6605 5.57519 15.5647 5.75232 14.7878C5.91537 14.0843 6.80489 10.317 6.80489 10.317C6.80489 10.317 6.53832 9.77695 6.53832 8.98488C6.53832 7.73402 7.2648 6.80168 8.16911 6.80168C8.93996 6.80168 9.31112 7.3793 9.31112 8.06766C9.31112 8.83723 8.82199 9.99211 8.56211 11.0654C8.3473 11.9609 9.01426 12.6935 9.89639 12.6935C11.498 12.6935 12.7287 11.006 12.7287 8.57812C12.7287 6.4241 11.1796 4.92188 8.96285 4.92188C6.39816 4.92188 4.89273 6.83895 4.89273 8.82246C4.89273 9.59203 5.18959 10.4214 5.5597 10.8731C5.63471 10.961 5.64245 11.0426 5.61992 11.1312C5.55301 11.4124 5.39771 12.0266 5.36743 12.1528C5.33045 12.3156 5.23361 12.3521 5.06317 12.2713C3.9363 11.7457 3.23165 10.1099 3.23165 8.7852C3.23165 5.9502 5.29242 3.34547 9.1847 3.34547C12.3061 3.34547 14.737 5.56629 14.737 8.54156C14.737 11.5168 12.7801 14.1374 10.0665 14.1374C9.15442 14.1374 8.29412 13.6635 8.00571 13.101C8.00571 13.101 7.55356 14.8177 7.44228 15.2399C7.24261 16.0242 6.69326 17.0016 6.3228 17.601C7.16866 17.859 8.05889 18 8.99278 18C13.9676 18 18.0004 13.9739 18.0004 9.00738C18.0148 4.02609 13.9824 0 9.00757 0Z",
                                    fill: "white"
                                  })
                                ]))
                              ], 8, ["href"])
                            ])
                          ])
                        ]),
                        previousPost.value || nextPost.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "next-prev-post position-relative justify-content-between mt-60 mb-80 d-none d-md-flex"
                        }, [
                          createVNode("div", { class: "line-border" }),
                          previousPost.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "prev-post post-wrap"
                          }, [
                            createVNode(unref(Link), {
                              href: _ctx.route("blogs.show", previousPost.value.slug),
                              class: "btn"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("PREV POST")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"]),
                            createVNode(unref(Link), {
                              href: _ctx.route("blogs.show", previousPost.value.slug),
                              class: "post-title d-block"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(previousPost.value.title), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])) : createCommentVNode("", true),
                          nextPost.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            class: "next-post post-wrap"
                          }, [
                            createVNode(unref(Link), {
                              href: _ctx.route("blogs.show", nextPost.value.slug),
                              class: "btn"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("NEXT POST")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"]),
                            createVNode(unref(Link), {
                              href: _ctx.route("blogs.show", nextPost.value.slug),
                              class: "post-title d-block"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(nextPost.value.title), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])) : createCommentVNode("", true)
                        ])) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-4" }, [
                      createVNode("div", { class: "sidebar" }, [
                        createVNode("div", { class: "sidebar__widget" }, [
                          createVNode("h5", { class: "sidebar__widget-title" }, toDisplayString(trans("Search Here")), 1),
                          createVNode("div", { class: "sidebar__widget-search" }, [
                            createVNode("form", {
                              onSubmit: withModifiers(handleSearch, ["prevent"])
                            }, [
                              createVNode("div", { class: "search__bar" }, [
                                createVNode("button", { type: "submit" }, [
                                  (openBlock(), createBlock("svg", {
                                    xmlns: "http://www.w3.org/2000/svg",
                                    width: "16",
                                    height: "16",
                                    viewBox: "0 0 16 16",
                                    fill: "none"
                                  }, [
                                    createVNode("path", {
                                      d: "M7.22221 13.4444C10.6586 13.4444 13.4444 10.6586 13.4444 7.22221C13.4444 3.78578 10.6586 1 7.22221 1C3.78578 1 1 3.78578 1 7.22221C1 10.6586 3.78578 13.4444 7.22221 13.4444Z",
                                      stroke: "#525257",
                                      "stroke-width": "1.5",
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round"
                                    }),
                                    createVNode("path", {
                                      d: "M15.0005 15L11.6172 11.6167",
                                      stroke: "#525257",
                                      "stroke-width": "1.5",
                                      "stroke-linecap": "round",
                                      "stroke-linejoin": "round"
                                    })
                                  ]))
                                ]),
                                withDirectives(createVNode("input", {
                                  type: "text",
                                  "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                                  placeholder: trans("Search")
                                }, null, 8, ["onUpdate:modelValue", "placeholder"]), [
                                  [vModelText, searchQuery.value]
                                ])
                              ])
                            ], 32)
                          ])
                        ]),
                        createVNode("div", { class: "sidebar__widget" }, [
                          createVNode("h5", { class: "sidebar__widget-title sidebar__widget-title__have-bar" }, toDisplayString(trans("Category")), 1),
                          createVNode("div", { class: "sidebar__widget-category" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(categories.value, (category) => {
                              return openBlock(), createBlock(unref(Link), {
                                key: category.id,
                                href: _ctx.route("blogs.index", { category: category.slug })
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", null, [
                                    (openBlock(), createBlock("svg", {
                                      xmlns: "http://www.w3.org/2000/svg",
                                      width: "5",
                                      height: "5",
                                      viewBox: "0 0 5 5",
                                      fill: "none"
                                    })),
                                    createTextVNode(" " + toDisplayString(category.name), 1)
                                  ]),
                                  createVNode("span", null, "(" + toDisplayString(category.blogs_count) + ")", 1)
                                ]),
                                _: 2
                              }, 1032, ["href"]);
                            }), 128))
                          ])
                        ]),
                        createVNode("div", { class: "sidebar__widget" }, [
                          createVNode("h5", { class: "sidebar__widget-title" }, toDisplayString(trans("Recent Post")), 1),
                          createVNode("div", { class: "sidebar-post__wrapper" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(recentPosts.value, (recentPost) => {
                              return openBlock(), createBlock("div", {
                                key: recentPost.id,
                                class: "sidebar-post"
                              }, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.show", recentPost.slug),
                                  class: "sidebar-post_thumb"
                                }, {
                                  default: withCtx(() => [
                                    createVNode("img", {
                                      src: recentPost.image_link,
                                      alt: recentPost.title
                                    }, null, 8, ["src", "alt"])
                                  ]),
                                  _: 2
                                }, 1032, ["href"]),
                                createVNode("div", { class: "sidebar-post_content" }, [
                                  createVNode("ul", { class: "post-meta" }, [
                                    createVNode("li", null, [
                                      (openBlock(), createBlock("svg", {
                                        width: "16",
                                        height: "16",
                                        viewBox: "0 0 16 16",
                                        fill: "none",
                                        xmlns: "http://www.w3.org/2000/svg"
                                      }, [
                                        createVNode("path", {
                                          d: "M15 8C15 11.864 11.864 15 8 15C4.136 15 1 11.864 1 8C1 4.136 4.136 1 8 1C11.864 1 15 4.136 15 8Z",
                                          stroke: "#FF3D00",
                                          "stroke-width": "1.5",
                                          "stroke-linecap": "round",
                                          "stroke-linejoin": "round"
                                        }),
                                        createVNode("path", {
                                          d: "M10.5962 10.2259L8.42623 8.93093C8.04823 8.70693 7.74023 8.16793 7.74023 7.72693V4.85693",
                                          stroke: "#FF3D00",
                                          "stroke-width": "1.5",
                                          "stroke-linecap": "round",
                                          "stroke-linejoin": "round"
                                        })
                                      ])),
                                      createTextVNode(" " + toDisplayString(recentPost.created_at), 1)
                                    ])
                                  ]),
                                  createVNode(unref(Link), {
                                    href: _ctx.route("blogs.show", recentPost.slug)
                                  }, {
                                    default: withCtx(() => [
                                      createVNode("h3", { class: "title rr-fw-medium" }, toDisplayString(recentPost.title), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ])
                              ]);
                            }), 128))
                          ])
                        ]),
                        blog.value.keywords ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "sidebar__widget"
                        }, [
                          createVNode("h5", { class: "sidebar__widget-title" }, toDisplayString(trans("Keywords")), 1),
                          createVNode("div", { class: "sidebar__widget-tags" }, [
                            createVNode("div", { class: "tags" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(getKeywords(blog.value.keywords), (keyword, index) => {
                                return openBlock(), createBlock(unref(Link), {
                                  key: index,
                                  href: _ctx.route("blogs.index", { search: keyword.trim() })
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(keyword.trim()), 1)
                                  ]),
                                  _: 2
                                }, 1032, ["href"]);
                              }), 128))
                            ])
                          ])
                        ])) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-12" }, [
                      relatedBlogs.value && relatedBlogs.value.length > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "related-blogs mt-20 mt-xs-10"
                      }, [
                        createVNode("h4", { class: "mb-30" }, toDisplayString(trans("Related Blogs")) + ":", 1),
                        createVNode("div", { class: "row" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(relatedBlogs.value, (relatedBlog) => {
                            return openBlock(), createBlock("div", {
                              key: relatedBlog.id,
                              class: "col-md-4 mb-30"
                            }, [
                              createVNode("div", { class: "latest-blog__item-slide" }, [
                                createVNode("div", { class: "latest-blog__item-media" }, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("blogs.show", relatedBlog.slug)
                                  }, {
                                    default: withCtx(() => [
                                      createVNode("img", {
                                        src: relatedBlog.image_link,
                                        alt: relatedBlog.title,
                                        class: "img-fluid"
                                      }, null, 8, ["src", "alt"])
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ]),
                                createVNode("div", { class: "latest-blog__item-text news" }, [
                                  createVNode("div", { class: "latest-blog__item-text-meta d-flex" }, [
                                    createVNode("div", { class: "latest-blog__item-text-meta-calender" }, [
                                      createVNode("h4", null, toDisplayString(relatedBlog.created_at_day), 1),
                                      createVNode("p", null, toDisplayString(relatedBlog.created_at_month), 1)
                                    ])
                                  ]),
                                  createVNode("div", { class: "latest-blog__item-text-bottom" }, [
                                    createVNode(unref(Link), {
                                      href: _ctx.route("blogs.show", relatedBlog.slug)
                                    }, {
                                      default: withCtx(() => [
                                        createVNode("h4", null, toDisplayString(relatedBlog.title), 1)
                                      ]),
                                      _: 2
                                    }, 1032, ["href"])
                                  ])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/BlogShow.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const __vite_glob_0_3 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$a
}, Symbol.toStringTag, { value: "Module" }));
const __default__$5 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$9 = /* @__PURE__ */ Object.assign(__default__$5, {
  __name: "PageShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const custom_page = computed(() => page.props.custom_page);
    computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const banner = computed(() => page.props.banner);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-2096f4b1${_scopeId}>${ssrInterpolate(custom_page.value.title[locale.value])} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(custom_page.value.title[locale.value]) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg page-banner" style="${ssrRenderStyle({ backgroundImage: `url('${banner.value}')` })}" data-v-2096f4b1${_scopeId}><div class="banner-overlay" data-v-2096f4b1${_scopeId}></div><div class="container" data-v-2096f4b1${_scopeId}><div class="row align-items-center justify-content-center m-auto" data-v-2096f4b1${_scopeId}><div class="col-12" data-v-2096f4b1${_scopeId}><div class="breadcrumb__content text-center" data-v-2096f4b1${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-2096f4b1${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-2096f4b1${_scopeId}>${ssrInterpolate(custom_page.value.title[locale.value])}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-2096f4b1${_scopeId}><nav data-v-2096f4b1${_scopeId}><ul data-v-2096f4b1${_scopeId}><li data-v-2096f4b1${_scopeId}><span data-v-2096f4b1${_scopeId}><a href="/" class="color-white" data-v-2096f4b1${_scopeId}>${ssrInterpolate(trans("Home"))}</a></span></li><li class="active" data-v-2096f4b1${_scopeId}><span class="color-white" data-v-2096f4b1${_scopeId}>${ssrInterpolate(custom_page.value.title[locale.value])}</span></li></ul></nav></div></div></div></div></div></div><div class="latest-about2__area mt-25 overflow-hidden mb-10" data-v-2096f4b1${_scopeId}><div class="container" data-v-2096f4b1${_scopeId}><div class="content mb-10" data-v-2096f4b1${_scopeId}><div data-v-2096f4b1${_scopeId}>${custom_page.value.content[locale.value] ?? ""}</div></div></div></div>`);
          } else {
            return [
              createVNode("div", {
                class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg page-banner",
                style: { backgroundImage: `url('${banner.value}')` }
              }, [
                createVNode("div", { class: "banner-overlay" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-center m-auto" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title color-white wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(custom_page.value.title[locale.value]), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode("a", {
                                    href: "/",
                                    class: "color-white"
                                  }, toDisplayString(trans("Home")), 1)
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", { class: "color-white" }, toDisplayString(custom_page.value.title[locale.value]), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ], 4),
              createVNode("div", { class: "latest-about2__area mt-25 overflow-hidden mb-10" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "content mb-10" }, [
                    createVNode("div", {
                      innerHTML: custom_page.value.content[locale.value]
                    }, null, 8, ["innerHTML"])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/PageShow.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const PageShow = /* @__PURE__ */ _export_sfc(_sfc_main$9, [["__scopeId", "data-v-2096f4b1"]]);
const __vite_glob_0_4 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: PageShow
}, Symbol.toStringTag, { value: "Module" }));
const __default__$4 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$8 = /* @__PURE__ */ Object.assign(__default__$4, {
  __name: "ServiceIndex",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const services = computed(() => {
      const source = page.props.services || { data: [], links: [], last_page: 1 };
      const data = Array.isArray(source.data) ? source.data.filter((service) => service && service.id) : [];
      return {
        ...source,
        data
      };
    });
    const getServiceUrl = (service) => {
      if (!service || !service.slug) {
        return "#";
      }
      try {
        return route("services.show", service.slug);
      } catch (e) {
        return "#";
      }
    };
    const getServiceTitle = (service) => {
      if (!service || !service.title) {
        return "";
      }
      if (typeof service.title === "string") {
        return service.title;
      }
      if (typeof service.title === "object") {
        return service.title[locale.value] || service.title["en"] || service.title[Object.keys(service.title)[0]] || "";
      }
      return "";
    };
    const arrowIcon = computed(() => {
      return locale.value === "ar" ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-000caf88${_scopeId}>${ssrInterpolate(trans("Our Services"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("Our Services")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-000caf88${_scopeId}><div class="container" data-v-000caf88${_scopeId}><div class="row align-items-center justify-content-between" data-v-000caf88${_scopeId}><div class="col-12" data-v-000caf88${_scopeId}><div class="breadcrumb__content text-center" data-v-000caf88${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-000caf88${_scopeId}><h1 class="breadcrumb__title wow fadeIn animated" data-wow-delay=".1s" data-v-000caf88${_scopeId}>${ssrInterpolate(trans("Our Services"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-000caf88${_scopeId}><nav data-v-000caf88${_scopeId}><ul data-v-000caf88${_scopeId}><li data-v-000caf88${_scopeId}><span data-v-000caf88${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-000caf88${_scopeId}><span data-v-000caf88${_scopeId}>${ssrInterpolate(trans("Our Services"))}</span></li></ul></nav></div></div></div></div></div></div><section class="latest-blog__area mt-25 pb-90 overflow-hidden latest-blog-bg" data-v-000caf88${_scopeId}><div class="container" data-v-000caf88${_scopeId}><div class="row mb-minus-30" data-v-000caf88${_scopeId}>`);
            if (services.value.data && services.value.data.length > 0) {
              _push2(`<!--[-->`);
              ssrRenderList(services.value.data, (service) => {
                _push2(`<div class="col-lg-4 col-md-6 mb-30" data-v-000caf88${_scopeId}><div class="swiper-slide latest-blog__item-slide" data-v-000caf88${_scopeId}><div class="latest-blog__item-slide-inner" data-v-000caf88${_scopeId}><div class="latest-blog__item-media" data-v-000caf88${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(service)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<img${ssrRenderAttr("src", service.image_link)}${ssrRenderAttr("alt", getServiceTitle(service))} class="img-fluid" data-v-000caf88${_scopeId2}>`);
                    } else {
                      return [
                        createVNode("img", {
                          src: service.image_link,
                          alt: getServiceTitle(service),
                          class: "img-fluid"
                        }, null, 8, ["src", "alt"])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div><div class="latest-blog__item-text" data-v-000caf88${_scopeId}><div class="latest-blog__item-text-meta d-flex" data-v-000caf88${_scopeId}></div><div class="latest-blog__item-text-bottom" data-v-000caf88${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(service)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<h4 data-v-000caf88${_scopeId2}>${ssrInterpolate(getServiceTitle(service))}</h4>`);
                    } else {
                      return [
                        createVNode("h4", null, toDisplayString(getServiceTitle(service)), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(service),
                  class: "readmore d-flex align-items-center"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Read More"))} <i class="${ssrRenderClass(arrowIcon.value)}" data-v-000caf88${_scopeId2}></i>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                        createVNode("i", { class: arrowIcon.value }, null, 2)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div></div></div></div>`);
              });
              _push2(`<!--]-->`);
            } else {
              _push2(`<div class="col-12" data-v-000caf88${_scopeId}><div class="text-center py-5" data-v-000caf88${_scopeId}><h3 class="text-muted" data-v-000caf88${_scopeId}>${ssrInterpolate(trans("No services found"))} <i class="fa fa-xmark text-danger" data-v-000caf88${_scopeId}></i></h3></div></div>`);
            }
            _push2(`</div>`);
            if (services.value.last_page > 1) {
              _push2(`<div class="bottom-button d-flex justify-content-center mt-30" data-v-000caf88${_scopeId}>`);
              if (services.value.links && services.value.links[0] && services.value.links[0].url) {
                _push2(ssrRenderComponent(unref(Link), {
                  href: services.value.links[0].url,
                  class: "page-link"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<i class="fa-solid fa-angles-left" data-v-000caf88${_scopeId2}></i>`);
                    } else {
                      return [
                        createVNode("i", { class: "fa-solid fa-angles-left" })
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(`<!--[-->`);
              ssrRenderList(services.value.links, (link, index) => {
                _push2(`<!--[-->`);
                if (link.url && index > 0 && index < services.value.links.length - 1 && parseInt(link.label) <= 3) {
                  _push2(ssrRenderComponent(unref(Link), {
                    href: link.url,
                    class: ["page-link", link.active ? "active" : ""]
                  }, {
                    default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                      if (_push3) {
                        _push3(`${ssrInterpolate(link.label)}`);
                      } else {
                        return [
                          createTextVNode(toDisplayString(link.label), 1)
                        ];
                      }
                    }),
                    _: 2
                  }, _parent2, _scopeId));
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<!--]-->`);
              });
              _push2(`<!--]-->`);
              if (services.value.links && services.value.links[services.value.links.length - 1] && services.value.links[services.value.links.length - 1].url) {
                _push2(ssrRenderComponent(unref(Link), {
                  href: services.value.links[services.value.links.length - 1].url,
                  class: "page-link"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<i class="fa-solid fa-angles-right" data-v-000caf88${_scopeId2}></i>`);
                    } else {
                      return [
                        createVNode("i", { class: "fa-solid fa-angles-right" })
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></section>`);
            _push2(ssrRenderComponent(_sfc_main$d, null, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-between" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("Our Services")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("home")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("Our Services")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "latest-blog__area mt-25 pb-90 overflow-hidden latest-blog-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row mb-minus-30" }, [
                    services.value.data && services.value.data.length > 0 ? (openBlock(true), createBlock(Fragment, { key: 0 }, renderList(services.value.data, (service) => {
                      return openBlock(), createBlock("div", {
                        key: service.id,
                        class: "col-lg-4 col-md-6 mb-30"
                      }, [
                        createVNode("div", { class: "swiper-slide latest-blog__item-slide" }, [
                          createVNode("div", { class: "latest-blog__item-slide-inner" }, [
                            createVNode("div", { class: "latest-blog__item-media" }, [
                              createVNode(unref(Link), {
                                href: getServiceUrl(service)
                              }, {
                                default: withCtx(() => [
                                  createVNode("img", {
                                    src: service.image_link,
                                    alt: getServiceTitle(service),
                                    class: "img-fluid"
                                  }, null, 8, ["src", "alt"])
                                ]),
                                _: 2
                              }, 1032, ["href"])
                            ]),
                            createVNode("div", { class: "latest-blog__item-text" }, [
                              createVNode("div", { class: "latest-blog__item-text-meta d-flex" }),
                              createVNode("div", { class: "latest-blog__item-text-bottom" }, [
                                createVNode(unref(Link), {
                                  href: getServiceUrl(service)
                                }, {
                                  default: withCtx(() => [
                                    createVNode("h4", null, toDisplayString(getServiceTitle(service)), 1)
                                  ]),
                                  _: 2
                                }, 1032, ["href"]),
                                createVNode(unref(Link), {
                                  href: getServiceUrl(service),
                                  class: "readmore d-flex align-items-center"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                                    createVNode("i", { class: arrowIcon.value }, null, 2)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ])
                            ])
                          ])
                        ])
                      ]);
                    }), 128)) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "col-12"
                    }, [
                      createVNode("div", { class: "text-center py-5" }, [
                        createVNode("h3", { class: "text-muted" }, [
                          createTextVNode(toDisplayString(trans("No services found")) + " ", 1),
                          createVNode("i", { class: "fa fa-xmark text-danger" })
                        ])
                      ])
                    ]))
                  ]),
                  services.value.last_page > 1 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "bottom-button d-flex justify-content-center mt-30"
                  }, [
                    services.value.links && services.value.links[0] && services.value.links[0].url ? (openBlock(), createBlock(unref(Link), {
                      key: 0,
                      href: services.value.links[0].url,
                      class: "page-link"
                    }, {
                      default: withCtx(() => [
                        createVNode("i", { class: "fa-solid fa-angles-left" })
                      ]),
                      _: 1
                    }, 8, ["href"])) : createCommentVNode("", true),
                    (openBlock(true), createBlock(Fragment, null, renderList(services.value.links, (link, index) => {
                      return openBlock(), createBlock(Fragment, { key: index }, [
                        link.url && index > 0 && index < services.value.links.length - 1 && parseInt(link.label) <= 3 ? (openBlock(), createBlock(unref(Link), {
                          key: 0,
                          href: link.url,
                          class: ["page-link", link.active ? "active" : ""]
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(link.label), 1)
                          ]),
                          _: 2
                        }, 1032, ["href", "class"])) : createCommentVNode("", true)
                      ], 64);
                    }), 128)),
                    services.value.links && services.value.links[services.value.links.length - 1] && services.value.links[services.value.links.length - 1].url ? (openBlock(), createBlock(unref(Link), {
                      key: 1,
                      href: services.value.links[services.value.links.length - 1].url,
                      class: "page-link"
                    }, {
                      default: withCtx(() => [
                        createVNode("i", { class: "fa-solid fa-angles-right" })
                      ]),
                      _: 1
                    }, 8, ["href"])) : createCommentVNode("", true)
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$d)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Services/resources/assets/js/Pages/ServiceIndex.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const ServiceIndex = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["__scopeId", "data-v-000caf88"]]);
const __vite_glob_0_5 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ServiceIndex
}, Symbol.toStringTag, { value: "Module" }));
const __default__$3 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$7 = /* @__PURE__ */ Object.assign(__default__$3, {
  __name: "ServiceShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const service = computed(() => page.props.service);
    const relatedServices = computed(() => page.props.relatedServices || []);
    computed(() => page.props.categories || []);
    const previousService = computed(() => page.props.previousService);
    const nextService = computed(() => page.props.nextService);
    const arrowIcon = computed(() => {
      return locale.value === "ar" ? "fa-solid fa-arrow-left" : "fa-solid fa-arrow-right";
    });
    const getServiceUrl = (serviceItem) => {
      if (!serviceItem || !serviceItem.slug) {
        return "#";
      }
      try {
        return route("services.show", serviceItem.slug);
      } catch (e) {
        return "#";
      }
    };
    const getServiceTitle = (serviceItem) => {
      if (!serviceItem || !serviceItem.title) {
        return "";
      }
      if (typeof serviceItem.title === "string") {
        return serviceItem.title;
      }
      if (typeof serviceItem.title === "object") {
        return serviceItem.title[locale.value] || serviceItem.title["en"] || serviceItem.title[Object.keys(serviceItem.title)[0]] || "";
      }
      return "";
    };
    const getServiceDescription = (serviceItem) => {
      if (!serviceItem || !serviceItem.description) {
        return "";
      }
      if (typeof serviceItem.description === "string") {
        return serviceItem.description;
      }
      if (typeof serviceItem.description === "object") {
        return serviceItem.description[locale.value] || serviceItem.description["en"] || serviceItem.description[Object.keys(serviceItem.description)[0]] || "";
      }
      return "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-06411c4f${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(getServiceTitle(service.value)) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-06411c4f${_scopeId}><div class="container" data-v-06411c4f${_scopeId}><div class="row align-items-center justify-content-center m-auto" data-v-06411c4f${_scopeId}><div class="col-12" data-v-06411c4f${_scopeId}><div class="breadcrumb__content text-center" data-v-06411c4f${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-06411c4f${_scopeId}><h1 class="breadcrumb__title wow fadeIn animated" data-wow-delay=".1s" data-v-06411c4f${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-06411c4f${_scopeId}><nav data-v-06411c4f${_scopeId}><ul data-v-06411c4f${_scopeId}><li data-v-06411c4f${_scopeId}><span data-v-06411c4f${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li data-v-06411c4f${_scopeId}><span data-v-06411c4f${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("services.index")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Our Services"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Our Services")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-06411c4f${_scopeId}><span data-v-06411c4f${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</span></li></ul></nav></div></div></div></div></div></div><section class="mt-25" data-v-06411c4f${_scopeId}><div class="container" data-v-06411c4f${_scopeId}><div class="row" data-v-06411c4f${_scopeId}><div class="col-xl-12" data-v-06411c4f${_scopeId}><div class="service-details" data-v-06411c4f${_scopeId}><div class="service-details__card" data-v-06411c4f${_scopeId}><img${ssrRenderAttr("src", service.value.image_link)} alt="" class="img-fluid" style="${ssrRenderStyle({ "height": "350px" })}" data-v-06411c4f${_scopeId}><div class="service-details__body" data-v-06411c4f${_scopeId}><h2 class="service-details__title" data-v-06411c4f${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</h2>`);
            if (service.value.description) {
              _push2(`<p class="service-details__excerpt" data-v-06411c4f${_scopeId}>${ssrInterpolate(getServiceDescription(service.value))}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="service-details__content" data-v-06411c4f${_scopeId}><div data-v-06411c4f${_scopeId}>${service.value.content ?? ""}</div></div></div></div>`);
            if (previousService.value || nextService.value) {
              _push2(`<div class="next-prev-post position-relative justify-content-between mt-60 mb-80 d-none d-md-flex" data-v-06411c4f${_scopeId}><div class="line-border" data-v-06411c4f${_scopeId}></div>`);
              if (previousService.value) {
                _push2(`<div class="prev-post post-wrap" data-v-06411c4f${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(previousService.value),
                  class: "btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("PREV SERVICE"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("PREV SERVICE")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(previousService.value),
                  class: "post-title d-block"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(getServiceTitle(previousService.value))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(getServiceTitle(previousService.value)), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (nextService.value) {
                _push2(`<div class="next-post post-wrap" data-v-06411c4f${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(nextService.value),
                  class: "btn"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("NEXT SERVICE"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("NEXT SERVICE")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(nextService.value),
                  class: "post-title d-block"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(getServiceTitle(nextService.value))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(getServiceTitle(nextService.value)), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-12" data-v-06411c4f${_scopeId}>`);
            if (relatedServices.value && relatedServices.value.length > 0) {
              _push2(`<div class="related-blogs mt-20 mt-xs-10" data-v-06411c4f${_scopeId}><h4 class="mb-30" data-v-06411c4f${_scopeId}>${ssrInterpolate(trans("Related Services"))}:</h4><div class="row" data-v-06411c4f${_scopeId}><!--[-->`);
              ssrRenderList(relatedServices.value, (relatedService) => {
                _push2(`<div class="col-md-4 mb-30" data-v-06411c4f${_scopeId}><div class="latest-blog__item-slide service-card--compact" data-v-06411c4f${_scopeId}><div class="latest-blog__item-media" data-v-06411c4f${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(relatedService)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<img${ssrRenderAttr("src", relatedService.image_link)}${ssrRenderAttr("alt", getServiceTitle(relatedService))} class="img-fluid" data-v-06411c4f${_scopeId2}>`);
                    } else {
                      return [
                        createVNode("img", {
                          src: relatedService.image_link,
                          alt: getServiceTitle(relatedService),
                          class: "img-fluid"
                        }, null, 8, ["src", "alt"])
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div><div class="latest-blog__item-text service-card" data-v-06411c4f${_scopeId}><div class="latest-blog__item-text-meta d-flex justify-content-between align-items-center" data-v-06411c4f${_scopeId}><span class="service-card__date" data-v-06411c4f${_scopeId}>${ssrInterpolate(relatedService.created_at)}</span></div><div class="latest-blog__item-text-bottom" data-v-06411c4f${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(relatedService)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<h4 data-v-06411c4f${_scopeId2}>${ssrInterpolate(getServiceTitle(relatedService))}</h4>`);
                    } else {
                      return [
                        createVNode("h4", null, toDisplayString(getServiceTitle(relatedService)), 1)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(Link), {
                  href: getServiceUrl(relatedService),
                  class: "readmore d-flex align-items-center service-card__readmore"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Read More"))} <i class="${ssrRenderClass(arrowIcon.value)}" data-v-06411c4f${_scopeId2}></i>`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                        createVNode("i", { class: arrowIcon.value }, null, 2)
                      ];
                    }
                  }),
                  _: 2
                }, _parent2, _scopeId));
                _push2(`</div></div></div></div>`);
              });
              _push2(`<!--]--></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-center m-auto" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(getServiceTitle(service.value)), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("home")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("services.index")
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Our Services")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(getServiceTitle(service.value)), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "mt-25" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "service-details" }, [
                        createVNode("div", { class: "service-details__card" }, [
                          createVNode("img", {
                            src: service.value.image_link,
                            alt: "",
                            class: "img-fluid",
                            style: { "height": "350px" }
                          }, null, 8, ["src"]),
                          createVNode("div", { class: "service-details__body" }, [
                            createVNode("h2", { class: "service-details__title" }, toDisplayString(getServiceTitle(service.value)), 1),
                            service.value.description ? (openBlock(), createBlock("p", {
                              key: 0,
                              class: "service-details__excerpt"
                            }, toDisplayString(getServiceDescription(service.value)), 1)) : createCommentVNode("", true),
                            createVNode("div", { class: "service-details__content" }, [
                              createVNode("div", {
                                innerHTML: service.value.content
                              }, null, 8, ["innerHTML"])
                            ])
                          ])
                        ]),
                        previousService.value || nextService.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "next-prev-post position-relative justify-content-between mt-60 mb-80 d-none d-md-flex"
                        }, [
                          createVNode("div", { class: "line-border" }),
                          previousService.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "prev-post post-wrap"
                          }, [
                            createVNode(unref(Link), {
                              href: getServiceUrl(previousService.value),
                              class: "btn"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("PREV SERVICE")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"]),
                            createVNode(unref(Link), {
                              href: getServiceUrl(previousService.value),
                              class: "post-title d-block"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(getServiceTitle(previousService.value)), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])) : createCommentVNode("", true),
                          nextService.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            class: "next-post post-wrap"
                          }, [
                            createVNode(unref(Link), {
                              href: getServiceUrl(nextService.value),
                              class: "btn"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("NEXT SERVICE")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"]),
                            createVNode(unref(Link), {
                              href: getServiceUrl(nextService.value),
                              class: "post-title d-block"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(getServiceTitle(nextService.value)), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])) : createCommentVNode("", true)
                        ])) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-12" }, [
                      relatedServices.value && relatedServices.value.length > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "related-blogs mt-20 mt-xs-10"
                      }, [
                        createVNode("h4", { class: "mb-30" }, toDisplayString(trans("Related Services")) + ":", 1),
                        createVNode("div", { class: "row" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(relatedServices.value, (relatedService) => {
                            return openBlock(), createBlock("div", {
                              key: relatedService.id,
                              class: "col-md-4 mb-30"
                            }, [
                              createVNode("div", { class: "latest-blog__item-slide service-card--compact" }, [
                                createVNode("div", { class: "latest-blog__item-media" }, [
                                  createVNode(unref(Link), {
                                    href: getServiceUrl(relatedService)
                                  }, {
                                    default: withCtx(() => [
                                      createVNode("img", {
                                        src: relatedService.image_link,
                                        alt: getServiceTitle(relatedService),
                                        class: "img-fluid"
                                      }, null, 8, ["src", "alt"])
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ]),
                                createVNode("div", { class: "latest-blog__item-text service-card" }, [
                                  createVNode("div", { class: "latest-blog__item-text-meta d-flex justify-content-between align-items-center" }, [
                                    createVNode("span", { class: "service-card__date" }, toDisplayString(relatedService.created_at), 1)
                                  ]),
                                  createVNode("div", { class: "latest-blog__item-text-bottom" }, [
                                    createVNode(unref(Link), {
                                      href: getServiceUrl(relatedService)
                                    }, {
                                      default: withCtx(() => [
                                        createVNode("h4", null, toDisplayString(getServiceTitle(relatedService)), 1)
                                      ]),
                                      _: 2
                                    }, 1032, ["href"]),
                                    createVNode(unref(Link), {
                                      href: getServiceUrl(relatedService),
                                      class: "readmore d-flex align-items-center service-card__readmore"
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                                        createVNode("i", { class: arrowIcon.value }, null, 2)
                                      ]),
                                      _: 1
                                    }, 8, ["href"])
                                  ])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ])) : createCommentVNode("", true)
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Services/resources/assets/js/Pages/ServiceShow.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const ServiceShow = /* @__PURE__ */ _export_sfc(_sfc_main$7, [["__scopeId", "data-v-06411c4f"]]);
const __vite_glob_0_6 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ServiceShow
}, Symbol.toStringTag, { value: "Module" }));
const __default__$2 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$6 = /* @__PURE__ */ Object.assign(__default__$2, {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const branches = computed(() => page.props.branches || []);
    const openBranchId = ref(null);
    const submitSuccess = ref(false);
    const contactForm = useForm({
      name: "",
      email: "",
      mobile: "",
      subject: "",
      message: ""
    });
    const handleSubmit = () => {
      if (contactForm.processing) {
        return false;
      }
      if (!contactForm.name || !contactForm.name.trim()) {
        return false;
      }
      if (!contactForm.email || !contactForm.email.trim()) {
        return false;
      }
      if (!contactForm.mobile || !contactForm.mobile.trim()) {
        return false;
      }
      if (!contactForm.subject || !contactForm.subject.trim()) {
        return false;
      }
      if (!contactForm.message || !contactForm.message.trim()) {
        return false;
      }
      let contactUrl = "/contact-us";
      try {
        if (typeof route !== "undefined" && route) {
          contactUrl = route("contact-us.store");
        } else {
          const currentLocale = page.props.locale || "";
          contactUrl = currentLocale ? `/${currentLocale}/contact-us` : "/contact-us";
        }
      } catch (e) {
        const currentLocale = page.props.locale || "";
        contactUrl = currentLocale ? `/${currentLocale}/contact-us` : "/contact-us";
      }
      contactForm.post(contactUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
          submitSuccess.value = false;
        },
        onSuccess: () => {
          submitSuccess.value = true;
          contactForm.reset();
          contactForm.clearErrors();
          setTimeout(() => {
            submitSuccess.value = false;
          }, 5e3);
        },
        onError: () => {
          submitSuccess.value = false;
        }
      });
      return false;
    };
    const showComplaintModal = ref(false);
    const selectedBranch = ref(null);
    const complaintSuccess = ref(false);
    const complaintForm = useForm({
      name: "",
      mobile: "",
      branch_id: null,
      description: ""
    });
    const openComplaintModal = (branch) => {
      selectedBranch.value = branch;
      complaintForm.reset();
      complaintForm.clearErrors();
      complaintForm.branch_id = branch.id;
      complaintSuccess.value = false;
      showComplaintModal.value = true;
      document.body.style.overflow = "hidden";
    };
    const closeComplaintModal = () => {
      if (!complaintForm.processing) {
        showComplaintModal.value = false;
        selectedBranch.value = null;
        complaintForm.reset();
        complaintForm.clearErrors();
        complaintSuccess.value = false;
        document.body.style.overflow = "";
      }
    };
    const handleComplaintSubmit = (e) => {
      if (e) {
        e.preventDefault();
        e.stopPropagation();
      }
      if (complaintForm.processing) {
        return false;
      }
      if (!complaintForm.branch_id) {
        alert(trans("Please select a branch"));
        return false;
      }
      if (!complaintForm.name || !complaintForm.name.trim()) {
        return false;
      }
      if (!complaintForm.description || !complaintForm.description.trim()) {
        return false;
      }
      if (!complaintForm.mobile || !complaintForm.mobile.trim()) {
        return false;
      }
      let complaintUrl = "/complaint";
      try {
        if (typeof route !== "undefined" && route) {
          complaintUrl = route("complaint.store");
        } else {
          const currentLocale = page.props.locale || "";
          complaintUrl = currentLocale ? `/${currentLocale}/complaint` : "/complaint";
        }
      } catch (e2) {
        const currentLocale = page.props.locale || "";
        complaintUrl = currentLocale ? `/${currentLocale}/complaint` : "/complaint";
      }
      complaintForm.post(complaintUrl, {
        preserveScroll: true,
        preserveState: true,
        onBefore: () => {
          complaintSuccess.value = false;
        },
        onSuccess: () => {
          complaintSuccess.value = true;
          setTimeout(() => {
            complaintSuccess.value = false;
            closeComplaintModal();
          }, 3e3);
        },
        onError: (errors) => {
          complaintSuccess.value = false;
          console.error("Complaint submission error:", errors);
        },
        onFinish: () => {
        }
      });
      return false;
    };
    const toggleBranch = (id) => {
      openBranchId.value = openBranchId.value === id ? null : id;
    };
    const isBranchOpen = (id) => {
      return openBranchId.value === id;
    };
    onMounted(() => {
      nextTick(() => {
        if (branches.value && branches.value.length > 0) {
          openBranchId.value = branches.value[0].id;
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Contact Us"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("Contact Us")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-cebedf4d${_scopeId}><div class="container" data-v-cebedf4d${_scopeId}><div class="row align-items-center justify-content-center m-auto" data-v-cebedf4d${_scopeId}><div class="col-12" data-v-cebedf4d${_scopeId}><div class="breadcrumb__content text-center" data-v-cebedf4d${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-cebedf4d${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated d-none d-md-block" data-wow-delay=".5s" data-v-cebedf4d${_scopeId}><nav data-v-cebedf4d${_scopeId}><ul data-v-cebedf4d${_scopeId}><li data-v-cebedf4d${_scopeId}><span data-v-cebedf4d${_scopeId}><a href="/" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Home"))}</a></span></li><li class="active" data-v-cebedf4d${_scopeId}><span data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</span></li></ul></nav></div></div></div></div></div></div><section class="contact-us__area mt-25 overflow-hidden" data-v-cebedf4d${_scopeId}><div class="container" data-v-cebedf4d${_scopeId}><div class="row" data-v-cebedf4d${_scopeId}><div class="col-xl-8" data-v-cebedf4d${_scopeId}><div class="contact-us__form-wrapper mb-30 mb-xs-25" data-v-cebedf4d${_scopeId}><h3 class="section__title mb-10 wow fadeInLeft animated" data-wow-delay=".3s" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Send a message"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated" data-wow-delay=".5s" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Fill out the form below and we'll get back to you as soon as possible"))}</p><form class="contact-us__form" data-v-cebedf4d${_scopeId}><div class="row wow fadeInLeft animated" data-wow-delay=".9s" data-v-cebedf4d${_scopeId}><div class="col-sm-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "name")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Name"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(contactForm).name)} name="name" id="name" type="text"${ssrRenderAttr("placeholder", trans("Name"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.name })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
            if (unref(contactForm).errors.name) {
              _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).errors.name)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-sm-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "email")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Email"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(contactForm).email)} name="email" id="email" type="email"${ssrRenderAttr("placeholder", trans("Email"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.email })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
            if (unref(contactForm).errors.email) {
              _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).errors.email)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-sm-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "mobile")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Phone"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(contactForm).mobile)} name="mobile" id="mobile" type="text"${ssrRenderAttr("placeholder", trans("Phone"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.mobile })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
            if (unref(contactForm).errors.mobile) {
              _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).errors.mobile)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-sm-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "subject")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Subject"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(contactForm).subject)} name="subject" id="subject" type="text"${ssrRenderAttr("placeholder", trans("Subject"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.subject })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
            if (unref(contactForm).errors.subject) {
              _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).errors.subject)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-12" data-v-cebedf4d${_scopeId}><div class="contact-us__textarea" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "message")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Message"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><textarea name="message" id="message" cols="30" rows="10"${ssrRenderAttr("placeholder", trans("Message"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.message })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).message)}</textarea>`);
            if (unref(contactForm).errors.message) {
              _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(contactForm).errors.message)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-12" data-v-cebedf4d${_scopeId}><button type="submit"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": unref(contactForm).processing }, "rr-btn mt-30"])}" data-v-cebedf4d${_scopeId}>`);
            if (unref(contactForm).processing) {
              _push2(`<span data-v-cebedf4d${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-cebedf4d${_scopeId}></i>${ssrInterpolate(trans("Sending..."))}</span>`);
            } else {
              _push2(`<span data-v-cebedf4d${_scopeId}><i class="fa-solid fa-paper-plane me-2" data-v-cebedf4d${_scopeId}></i>${ssrInterpolate(trans("Send Message"))}</span>`);
            }
            _push2(`</button></div>`);
            if (submitSuccess.value) {
              _push2(`<div class="col-12 mt-3" data-v-cebedf4d${_scopeId}><div class="alert alert-success" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></form></div></div><div class="col-xl-4" data-v-cebedf4d${_scopeId}><div class="contact-us__widget mb-30 wow fadeInRight animated" data-wow-delay=".3s" data-v-cebedf4d${_scopeId}><h3 class="contact-us__widget-title mb-20" data-v-cebedf4d${_scopeId}><i class="fa-solid fa-map-location-dot me-2" data-v-cebedf4d${_scopeId}></i> ${ssrInterpolate(trans("Our Branches"))}</h3>`);
            if (branches.value && branches.value.length > 0) {
              _push2(`<div class="contact-us__branches-list" data-v-cebedf4d${_scopeId}><div class="accordion contact-us__branches-accordion" id="branchesAccordion" data-v-cebedf4d${_scopeId}><!--[-->`);
              ssrRenderList(branches.value, (branch) => {
                _push2(`<div class="accordion-item contact-us__branch-item" data-v-cebedf4d${_scopeId}><h3 class="accordion-header"${ssrRenderAttr("id", `branch-heading-${branch.id}`)} data-v-cebedf4d${_scopeId}><button type="button" class="${ssrRenderClass([{ collapsed: !isBranchOpen(branch.id) }, "accordion-button d-flex justify-content-between"])}"${ssrRenderAttr("aria-expanded", isBranchOpen(branch.id))}${ssrRenderAttr("aria-controls", `branch-collapse-${branch.id}`)} data-v-cebedf4d${_scopeId}><span class="branch-name" data-v-cebedf4d${_scopeId}>${ssrInterpolate(branch.name[locale.value] || branch.name)}</span></button></h3><div${ssrRenderAttr("id", `branch-collapse-${branch.id}`)} class="${ssrRenderClass([{ show: isBranchOpen(branch.id) }, "accordion-collapse collapse"])}"${ssrRenderAttr("aria-labelledby", `branch-heading-${branch.id}`)} data-bs-parent="#branchesAccordion" data-v-cebedf4d${_scopeId}><div class="accordion-body" data-v-cebedf4d${_scopeId}><div class="branch-info" data-v-cebedf4d${_scopeId}>`);
                if (branch.city) {
                  _push2(`<p class="mb-4 d-flex align-items-center" data-v-cebedf4d${_scopeId}><span class="branch-icon-wrapper me-3" data-v-cebedf4d${_scopeId}><i class="fa-regular fa-city" data-v-cebedf4d${_scopeId}></i></span><span data-v-cebedf4d${_scopeId}><strong data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("City"))}: </strong> ${ssrInterpolate(branch.city[locale.value] || branch.city)}</span></p>`);
                } else {
                  _push2(`<!---->`);
                }
                if (branch.address) {
                  _push2(`<p class="mb-4 d-flex align-items-center" data-v-cebedf4d${_scopeId}><span class="branch-icon-wrapper me-3" data-v-cebedf4d${_scopeId}><i class="fa-regular fa-location-dot" data-v-cebedf4d${_scopeId}></i></span><span data-v-cebedf4d${_scopeId}><strong data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Address"))}: </strong> ${ssrInterpolate(branch.address[locale.value] || branch.address)}</span></p>`);
                } else {
                  _push2(`<!---->`);
                }
                if (branch.phone) {
                  _push2(`<p class="mb-0 d-flex align-items-center" data-v-cebedf4d${_scopeId}><span class="branch-icon-wrapper me-3" data-v-cebedf4d${_scopeId}><i class="fa-regular fa-phone" data-v-cebedf4d${_scopeId}></i></span><span data-v-cebedf4d${_scopeId}><strong data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Phone"))}: </strong><a${ssrRenderAttr("href", "tel:" + (branch.phone[locale.value] || branch.phone))} data-v-cebedf4d${_scopeId}>${ssrInterpolate(branch.phone[locale.value] || branch.phone)}</a></span></p>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div><div class="mt-4" data-v-cebedf4d${_scopeId}><button type="button" class="rr-btn" style="${ssrRenderStyle({ "gap": "0" })}" data-v-cebedf4d${_scopeId}><i class="fa-solid fa-exclamation-triangle me-2" data-v-cebedf4d${_scopeId}></i> ${ssrInterpolate(trans("Submit a complaint on branch"))}</button></div></div></div></div>`);
              });
              _push2(`<!--]--></div></div>`);
            } else {
              _push2(`<div class="text-muted" data-v-cebedf4d${_scopeId}><p data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("No branches available"))}</p></div>`);
            }
            _push2(`</div></div></div></div></section>`);
            if (showComplaintModal.value) {
              _push2(`<div class="modal-overlay" data-v-cebedf4d${_scopeId}><div class="modal-content" data-v-cebedf4d${_scopeId}><div class="modal-header" data-v-cebedf4d${_scopeId}><h5 class="modal-title" data-v-cebedf4d${_scopeId}><i class="fa-solid fa-exclamation-triangle me-2" data-v-cebedf4d${_scopeId}></i> ${ssrInterpolate(trans("Submit a complaint on branch"))}</h5><button type="button" class="btn-close" aria-label="Close" data-v-cebedf4d${_scopeId}></button></div><div class="modal-body" data-v-cebedf4d${_scopeId}><form class="contact-us__form" data-v-cebedf4d${_scopeId}><div class="row" data-v-cebedf4d${_scopeId}><div class="col-12" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "complaint-branch")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Branch"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input type="text"${ssrRenderAttr("value", selectedBranch.value ? selectedBranch.value.name[locale.value] || selectedBranch.value.name : "")} disabled data-v-cebedf4d${_scopeId}><input type="hidden"${ssrRenderAttr("value", unref(complaintForm).branch_id)} data-v-cebedf4d${_scopeId}></div></div><div class="col-md-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "complaint-name")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Name"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(complaintForm).name)} type="text"${ssrRenderAttr("placeholder", trans("Name"))} class="${ssrRenderClass({ "error": unref(complaintForm).errors.name })}"${ssrIncludeBooleanAttr(unref(complaintForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
              if (unref(complaintForm).errors.name) {
                _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(complaintForm).errors.name)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="col-md-6" data-v-cebedf4d${_scopeId}><div class="contact-us__input" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "complaint-mobile")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Phone"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><input${ssrRenderAttr("value", unref(complaintForm).mobile)} type="text"${ssrRenderAttr("placeholder", trans("Phone"))} class="${ssrRenderClass({ "error": unref(complaintForm).errors.mobile })}"${ssrIncludeBooleanAttr(unref(complaintForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>`);
              if (unref(complaintForm).errors.mobile) {
                _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(complaintForm).errors.mobile)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="col-12" data-v-cebedf4d${_scopeId}><div class="contact-us__textarea" data-v-cebedf4d${_scopeId}><label${ssrRenderAttr("for", "complaint-description")} class="form-label" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Description"))} <span class="text-danger" data-v-cebedf4d${_scopeId}>*</span></label><textarea rows="5"${ssrRenderAttr("placeholder", trans("Please describe your complaint"))} class="${ssrRenderClass({ "error": unref(complaintForm).errors.description })}"${ssrIncludeBooleanAttr(unref(complaintForm).processing) ? " disabled" : ""} required data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(complaintForm).description)}</textarea>`);
              if (unref(complaintForm).errors.description) {
                _push2(`<div class="text-danger mt-1 small" data-v-cebedf4d${_scopeId}>${ssrInterpolate(unref(complaintForm).errors.description)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div>`);
              if (complaintSuccess.value) {
                _push2(`<div class="col-12" data-v-cebedf4d${_scopeId}><div class="alert alert-success mb-3" data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Thank you for submitting your complaint! We will review it and get back to you soon."))}</div></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="modal-footer" data-v-cebedf4d${_scopeId}><button type="button" class="rr-btn rr-btn-secondary"${ssrIncludeBooleanAttr(unref(complaintForm).processing) ? " disabled" : ""} data-v-cebedf4d${_scopeId}>${ssrInterpolate(trans("Cancel"))}</button><button type="submit"${ssrIncludeBooleanAttr(unref(complaintForm).processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": unref(complaintForm).processing }, "rr-btn"])}" data-v-cebedf4d${_scopeId}>`);
              if (unref(complaintForm).processing) {
                _push2(`<span data-v-cebedf4d${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-cebedf4d${_scopeId}></i>${ssrInterpolate(trans("Submitting..."))}</span>`);
              } else {
                _push2(`<span data-v-cebedf4d${_scopeId}><i class="fa-solid fa-paper-plane me-2" data-v-cebedf4d${_scopeId}></i>${ssrInterpolate(trans("Submit Complaint"))}</span>`);
              }
              _push2(`</button></div></form></div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row align-items-center justify-content-center m-auto" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title color-white wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("Contact Us")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated d-none d-md-block",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode("a", { href: "/" }, toDisplayString(trans("Home")), 1)
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("Contact Us")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "contact-us__area mt-25 overflow-hidden" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-8" }, [
                      createVNode("div", { class: "contact-us__form-wrapper mb-30 mb-xs-25" }, [
                        createVNode("h3", {
                          class: "section__title mb-10 wow fadeInLeft animated",
                          "data-wow-delay": ".3s"
                        }, toDisplayString(trans("Send a message")), 1),
                        createVNode("p", {
                          class: "mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated",
                          "data-wow-delay": ".5s"
                        }, toDisplayString(trans("Fill out the form below and we'll get back to you as soon as possible")), 1),
                        createVNode("form", {
                          onSubmit: withModifiers(handleSubmit, ["prevent"]),
                          class: "contact-us__form"
                        }, [
                          createVNode("div", {
                            class: "row wow fadeInLeft animated",
                            "data-wow-delay": ".9s"
                          }, [
                            createVNode("div", { class: "col-sm-6" }, [
                              createVNode("div", { class: "contact-us__input" }, [
                                createVNode("label", {
                                  for: "name",
                                  class: "form-label"
                                }, [
                                  createTextVNode(toDisplayString(trans("Name")) + " ", 1),
                                  createVNode("span", { class: "text-danger" }, "*")
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).name = $event,
                                  name: "name",
                                  id: "name",
                                  type: "text",
                                  placeholder: trans("Name"),
                                  class: { "error": unref(contactForm).errors.name },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).name]
                                ]),
                                unref(contactForm).errors.name ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.name), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-sm-6" }, [
                              createVNode("div", { class: "contact-us__input" }, [
                                createVNode("label", {
                                  for: "email",
                                  class: "form-label"
                                }, [
                                  createTextVNode(toDisplayString(trans("Email")) + " ", 1),
                                  createVNode("span", { class: "text-danger" }, "*")
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).email = $event,
                                  name: "email",
                                  id: "email",
                                  type: "email",
                                  placeholder: trans("Email"),
                                  class: { "error": unref(contactForm).errors.email },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).email]
                                ]),
                                unref(contactForm).errors.email ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.email), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-sm-6" }, [
                              createVNode("div", { class: "contact-us__input" }, [
                                createVNode("label", {
                                  for: "mobile",
                                  class: "form-label"
                                }, [
                                  createTextVNode(toDisplayString(trans("Phone")) + " ", 1),
                                  createVNode("span", { class: "text-danger" }, "*")
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).mobile = $event,
                                  name: "mobile",
                                  id: "mobile",
                                  type: "text",
                                  placeholder: trans("Phone"),
                                  class: { "error": unref(contactForm).errors.mobile },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).mobile]
                                ]),
                                unref(contactForm).errors.mobile ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.mobile), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-sm-6" }, [
                              createVNode("div", { class: "contact-us__input" }, [
                                createVNode("label", {
                                  for: "subject",
                                  class: "form-label"
                                }, [
                                  createTextVNode(toDisplayString(trans("Subject")) + " ", 1),
                                  createVNode("span", { class: "text-danger" }, "*")
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).subject = $event,
                                  name: "subject",
                                  id: "subject",
                                  type: "text",
                                  placeholder: trans("Subject"),
                                  class: { "error": unref(contactForm).errors.subject },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).subject]
                                ]),
                                unref(contactForm).errors.subject ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.subject), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-12" }, [
                              createVNode("div", { class: "contact-us__textarea" }, [
                                createVNode("label", {
                                  for: "message",
                                  class: "form-label"
                                }, [
                                  createTextVNode(toDisplayString(trans("Message")) + " ", 1),
                                  createVNode("span", { class: "text-danger" }, "*")
                                ]),
                                withDirectives(createVNode("textarea", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).message = $event,
                                  name: "message",
                                  id: "message",
                                  cols: "30",
                                  rows: "10",
                                  placeholder: trans("Message"),
                                  class: { "error": unref(contactForm).errors.message },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).message]
                                ]),
                                unref(contactForm).errors.message ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-danger mt-1 small"
                                }, toDisplayString(unref(contactForm).errors.message), 1)) : createCommentVNode("", true)
                              ])
                            ]),
                            createVNode("div", { class: "col-12" }, [
                              createVNode("button", {
                                type: "submit",
                                class: ["rr-btn mt-30", { "opacity-50": unref(contactForm).processing }],
                                disabled: unref(contactForm).processing
                              }, [
                                unref(contactForm).processing ? (openBlock(), createBlock("span", { key: 0 }, [
                                  createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                                  createTextVNode(toDisplayString(trans("Sending...")), 1)
                                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createVNode("i", { class: "fa-solid fa-paper-plane me-2" }),
                                  createTextVNode(toDisplayString(trans("Send Message")), 1)
                                ]))
                              ], 10, ["disabled"])
                            ]),
                            submitSuccess.value ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "col-12 mt-3"
                            }, [
                              createVNode("div", { class: "alert alert-success" }, toDisplayString(trans("Thank you for contacting us! We will get back to you soon.")), 1)
                            ])) : createCommentVNode("", true)
                          ])
                        ], 32)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-4" }, [
                      createVNode("div", {
                        class: "contact-us__widget mb-30 wow fadeInRight animated",
                        "data-wow-delay": ".3s"
                      }, [
                        createVNode("h3", { class: "contact-us__widget-title mb-20" }, [
                          createVNode("i", { class: "fa-solid fa-map-location-dot me-2" }),
                          createTextVNode(" " + toDisplayString(trans("Our Branches")), 1)
                        ]),
                        branches.value && branches.value.length > 0 ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "contact-us__branches-list"
                        }, [
                          createVNode("div", {
                            class: "accordion contact-us__branches-accordion",
                            id: "branchesAccordion"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(branches.value, (branch) => {
                              return openBlock(), createBlock("div", {
                                key: branch.id,
                                class: "accordion-item contact-us__branch-item"
                              }, [
                                createVNode("h3", {
                                  class: "accordion-header",
                                  id: `branch-heading-${branch.id}`
                                }, [
                                  createVNode("button", {
                                    class: ["accordion-button d-flex justify-content-between", { collapsed: !isBranchOpen(branch.id) }],
                                    type: "button",
                                    onClick: ($event) => toggleBranch(branch.id),
                                    "aria-expanded": isBranchOpen(branch.id),
                                    "aria-controls": `branch-collapse-${branch.id}`
                                  }, [
                                    createVNode("span", { class: "branch-name" }, toDisplayString(branch.name[locale.value] || branch.name), 1)
                                  ], 10, ["onClick", "aria-expanded", "aria-controls"])
                                ], 8, ["id"]),
                                createVNode("div", {
                                  id: `branch-collapse-${branch.id}`,
                                  class: ["accordion-collapse collapse", { show: isBranchOpen(branch.id) }],
                                  "aria-labelledby": `branch-heading-${branch.id}`,
                                  "data-bs-parent": "#branchesAccordion"
                                }, [
                                  createVNode("div", { class: "accordion-body" }, [
                                    createVNode("div", { class: "branch-info" }, [
                                      branch.city ? (openBlock(), createBlock("p", {
                                        key: 0,
                                        class: "mb-4 d-flex align-items-center"
                                      }, [
                                        createVNode("span", { class: "branch-icon-wrapper me-3" }, [
                                          createVNode("i", { class: "fa-regular fa-city" })
                                        ]),
                                        createVNode("span", null, [
                                          createVNode("strong", null, toDisplayString(trans("City")) + ": ", 1),
                                          createTextVNode(" " + toDisplayString(branch.city[locale.value] || branch.city), 1)
                                        ])
                                      ])) : createCommentVNode("", true),
                                      branch.address ? (openBlock(), createBlock("p", {
                                        key: 1,
                                        class: "mb-4 d-flex align-items-center"
                                      }, [
                                        createVNode("span", { class: "branch-icon-wrapper me-3" }, [
                                          createVNode("i", { class: "fa-regular fa-location-dot" })
                                        ]),
                                        createVNode("span", null, [
                                          createVNode("strong", null, toDisplayString(trans("Address")) + ": ", 1),
                                          createTextVNode(" " + toDisplayString(branch.address[locale.value] || branch.address), 1)
                                        ])
                                      ])) : createCommentVNode("", true),
                                      branch.phone ? (openBlock(), createBlock("p", {
                                        key: 2,
                                        class: "mb-0 d-flex align-items-center"
                                      }, [
                                        createVNode("span", { class: "branch-icon-wrapper me-3" }, [
                                          createVNode("i", { class: "fa-regular fa-phone" })
                                        ]),
                                        createVNode("span", null, [
                                          createVNode("strong", null, toDisplayString(trans("Phone")) + ": ", 1),
                                          createVNode("a", {
                                            href: "tel:" + (branch.phone[locale.value] || branch.phone)
                                          }, toDisplayString(branch.phone[locale.value] || branch.phone), 9, ["href"])
                                        ])
                                      ])) : createCommentVNode("", true)
                                    ]),
                                    createVNode("div", { class: "mt-4" }, [
                                      createVNode("button", {
                                        type: "button",
                                        class: "rr-btn",
                                        style: { "gap": "0" },
                                        onClick: ($event) => openComplaintModal(branch)
                                      }, [
                                        createVNode("i", { class: "fa-solid fa-exclamation-triangle me-2" }),
                                        createTextVNode(" " + toDisplayString(trans("Submit a complaint on branch")), 1)
                                      ], 8, ["onClick"])
                                    ])
                                  ])
                                ], 10, ["id", "aria-labelledby"])
                              ]);
                            }), 128))
                          ])
                        ])) : (openBlock(), createBlock("div", {
                          key: 1,
                          class: "text-muted"
                        }, [
                          createVNode("p", null, toDisplayString(trans("No branches available")), 1)
                        ]))
                      ])
                    ])
                  ])
                ])
              ]),
              showComplaintModal.value ? (openBlock(), createBlock("div", {
                key: 0,
                class: "modal-overlay",
                onClick: withModifiers(closeComplaintModal, ["self"]),
                onKeydown: withKeys(closeComplaintModal, ["esc"])
              }, [
                createVNode("div", {
                  class: "modal-content",
                  onClick: withModifiers(() => {
                  }, ["stop"])
                }, [
                  createVNode("div", { class: "modal-header" }, [
                    createVNode("h5", { class: "modal-title" }, [
                      createVNode("i", { class: "fa-solid fa-exclamation-triangle me-2" }),
                      createTextVNode(" " + toDisplayString(trans("Submit a complaint on branch")), 1)
                    ]),
                    createVNode("button", {
                      type: "button",
                      class: "btn-close",
                      onClick: closeComplaintModal,
                      "aria-label": "Close"
                    })
                  ]),
                  createVNode("div", { class: "modal-body" }, [
                    createVNode("form", {
                      onSubmit: withModifiers(handleComplaintSubmit, ["prevent"]),
                      class: "contact-us__form"
                    }, [
                      createVNode("div", { class: "row" }, [
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "complaint-branch",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString(trans("Branch")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            createVNode("input", {
                              type: "text",
                              value: selectedBranch.value ? selectedBranch.value.name[locale.value] || selectedBranch.value.name : "",
                              disabled: ""
                            }, null, 8, ["value"]),
                            withDirectives(createVNode("input", {
                              type: "hidden",
                              "onUpdate:modelValue": ($event) => unref(complaintForm).branch_id = $event
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelText, unref(complaintForm).branch_id]
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "complaint-name",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString(trans("Name")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              "onUpdate:modelValue": ($event) => unref(complaintForm).name = $event,
                              type: "text",
                              placeholder: trans("Name"),
                              class: { "error": unref(complaintForm).errors.name },
                              disabled: unref(complaintForm).processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, unref(complaintForm).name]
                            ]),
                            unref(complaintForm).errors.name ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString(unref(complaintForm).errors.name), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-md-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "complaint-mobile",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString(trans("Phone")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              "onUpdate:modelValue": ($event) => unref(complaintForm).mobile = $event,
                              type: "text",
                              placeholder: trans("Phone"),
                              class: { "error": unref(complaintForm).errors.mobile },
                              disabled: unref(complaintForm).processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, unref(complaintForm).mobile]
                            ]),
                            unref(complaintForm).errors.mobile ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString(unref(complaintForm).errors.mobile), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__textarea" }, [
                            createVNode("label", {
                              for: "complaint-description",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString(trans("Description")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("textarea", {
                              "onUpdate:modelValue": ($event) => unref(complaintForm).description = $event,
                              rows: "5",
                              placeholder: trans("Please describe your complaint"),
                              class: { "error": unref(complaintForm).errors.description },
                              disabled: unref(complaintForm).processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, unref(complaintForm).description]
                            ]),
                            unref(complaintForm).errors.description ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString(unref(complaintForm).errors.description), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        complaintSuccess.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "col-12"
                        }, [
                          createVNode("div", { class: "alert alert-success mb-3" }, toDisplayString(trans("Thank you for submitting your complaint! We will review it and get back to you soon.")), 1)
                        ])) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "modal-footer" }, [
                        createVNode("button", {
                          type: "button",
                          class: "rr-btn rr-btn-secondary",
                          onClick: closeComplaintModal,
                          disabled: unref(complaintForm).processing
                        }, toDisplayString(trans("Cancel")), 9, ["disabled"]),
                        createVNode("button", {
                          type: "submit",
                          class: ["rr-btn", { "opacity-50": unref(complaintForm).processing }],
                          disabled: unref(complaintForm).processing
                        }, [
                          unref(complaintForm).processing ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                            createTextVNode(toDisplayString(trans("Submitting...")), 1)
                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                            createVNode("i", { class: "fa-solid fa-paper-plane me-2" }),
                            createTextVNode(toDisplayString(trans("Submit Complaint")), 1)
                          ]))
                        ], 10, ["disabled"])
                      ])
                    ], 32)
                  ])
                ], 8, ["onClick"])
              ], 32)) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Support/resources/assets/js/Pages/Index.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main$6, [["__scopeId", "data-v-cebedf4d"]]);
const __vite_glob_0_7 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Index
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$5 = {
  components: {
    AppLayout: _sfc_main$f,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const seo = computed(() => page.props.seo);
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const form = useForm({
      email: ""
    });
    return { form, seo, trans };
  }
};
function _sfc_ssrRender$3(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<title data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Forgot Password"))} | ${ssrInterpolate($setup.seo.website_name)}</title>`);
      } else {
        return [
          createVNode("title", null, toDisplayString($setup.trans("Forgot Password")) + " | " + toDisplayString($setup.seo.website_name), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-43e42370${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-43e42370${_scopeId}></div><div class="container" data-v-43e42370${_scopeId}><div class="banner-all-shape-wrapper" data-v-43e42370${_scopeId}></div><div class="row align-items-center justify-content-between" data-v-43e42370${_scopeId}><div class="col-12" data-v-43e42370${_scopeId}><div class="breadcrumb__content text-center" data-v-43e42370${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-43e42370${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Forgot Password"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-43e42370${_scopeId}><nav data-v-43e42370${_scopeId}><ul data-v-43e42370${_scopeId}><li data-v-43e42370${_scopeId}><span data-v-43e42370${_scopeId}><a href="/" data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Home"))}</a></span></li><li class="active" data-v-43e42370${_scopeId}><span data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Forgot Password"))}</span></li></ul></nav></div></div></div></div></div></div><section class="contact-us__area mt-25 overflow-hidden" data-v-43e42370${_scopeId}><div class="container" data-v-43e42370${_scopeId}><div class="row justify-content-center" data-v-43e42370${_scopeId}><div class="col-xl-8" data-v-43e42370${_scopeId}><div class="contact-us__form-wrapper mb-30 mb-xs-25" data-v-43e42370${_scopeId}><h3 class="section__title mb-10 wow fadeInLeft animated" data-wow-delay=".3s" data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Reset Your Password"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated" data-wow-delay=".5s" data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Enter your email address and we'll send you a link to reset your password"))}</p><form class="contact-us__form" data-v-43e42370${_scopeId}><div class="row wow fadeInLeft animated" data-wow-delay=".7s" data-v-43e42370${_scopeId}><div class="col-12" data-v-43e42370${_scopeId}><div class="contact-us__input" data-v-43e42370${_scopeId}><label${ssrRenderAttr("for", "email")} class="form-label" data-v-43e42370${_scopeId}>${ssrInterpolate($setup.trans("Email"))} <span class="text-danger" data-v-43e42370${_scopeId}>*</span></label><input id="email"${ssrRenderAttr("value", $setup.form.email)} autofocus name="email" type="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-43e42370${_scopeId}>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-43e42370${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-12" data-v-43e42370${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "rr-btn mt-30"])}" data-v-43e42370${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-43e42370${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-43e42370${_scopeId}></i>${ssrInterpolate($setup.trans("Sending..."))}</span>`);
        } else {
          _push2(`<span data-v-43e42370${_scopeId}><i class="fa-solid fa-envelope me-2" data-v-43e42370${_scopeId}></i>${ssrInterpolate($setup.trans("Send Email Verification"))}</span>`);
        }
        _push2(`</button></div><div class="col-12 mt-3 text-center" data-v-43e42370${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login"),
          class: "text-decoration-none"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<i class="fa-solid fa-arrow-left me-2" data-v-43e42370${_scopeId2}></i>${ssrInterpolate($setup.trans("Back to Login"))}`);
            } else {
              return [
                createVNode("i", { class: "fa-solid fa-arrow-left me-2" }),
                createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div></form></div></div></div></div></section>`);
      } else {
        return [
          createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
            createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "banner-all-shape-wrapper" }),
              createVNode("div", { class: "row align-items-center justify-content-between" }, [
                createVNode("div", { class: "col-12" }, [
                  createVNode("div", { class: "breadcrumb__content text-center" }, [
                    createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                      createVNode("h1", {
                        class: "breadcrumb__title color-white wow fadeIn animated",
                        "data-wow-delay": ".1s"
                      }, toDisplayString($setup.trans("Forgot Password")), 1)
                    ]),
                    createVNode("div", {
                      class: "breadcrumb__menu wow fadeIn animated",
                      "data-wow-delay": ".5s"
                    }, [
                      createVNode("nav", null, [
                        createVNode("ul", null, [
                          createVNode("li", null, [
                            createVNode("span", null, [
                              createVNode("a", { href: "/" }, toDisplayString($setup.trans("Home")), 1)
                            ])
                          ]),
                          createVNode("li", { class: "active" }, [
                            createVNode("span", null, toDisplayString($setup.trans("Forgot Password")), 1)
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "contact-us__area mt-25 overflow-hidden" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "row justify-content-center" }, [
                createVNode("div", { class: "col-xl-8" }, [
                  createVNode("div", { class: "contact-us__form-wrapper mb-30 mb-xs-25" }, [
                    createVNode("h3", {
                      class: "section__title mb-10 wow fadeInLeft animated",
                      "data-wow-delay": ".3s"
                    }, toDisplayString($setup.trans("Reset Your Password")), 1),
                    createVNode("p", {
                      class: "mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated",
                      "data-wow-delay": ".5s"
                    }, toDisplayString($setup.trans("Enter your email address and we'll send you a link to reset your password")), 1),
                    createVNode("form", {
                      onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("password.email")), ["prevent"]),
                      class: "contact-us__form"
                    }, [
                      createVNode("div", {
                        class: "row wow fadeInLeft animated",
                        "data-wow-delay": ".7s"
                      }, [
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "email",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Email")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "email",
                              "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                              autofocus: "",
                              name: "email",
                              type: "email",
                              placeholder: $setup.trans("Email"),
                              class: { "error": $props.errors.email },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.email]
                            ]),
                            $props.errors.email ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("button", {
                            type: "submit",
                            class: ["rr-btn mt-30", { "opacity-50": $setup.form.processing }],
                            disabled: $setup.form.processing
                          }, [
                            $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                              createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Sending...")), 1)
                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("i", { class: "fa-solid fa-envelope me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Send Email Verification")), 1)
                            ]))
                          ], 10, ["disabled"])
                        ]),
                        createVNode("div", { class: "col-12 mt-3 text-center" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("login"),
                            class: "text-decoration-none"
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fa-solid fa-arrow-left me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ], 40, ["onSubmit"])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/User/resources/assets/js/Pages/Auth/ForgotPassword.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const ForgotPassword = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender$3], ["__scopeId", "data-v-43e42370"]]);
const __vite_glob_0_8 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ForgotPassword
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$4 = {
  components: {
    AppLayout: _sfc_main$f,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const seo = computed(() => page.props.seo);
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const form = useForm({
      email: "",
      password: "",
      remember: false
    });
    return { form, seo, trans };
  }
};
function _sfc_ssrRender$2(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<title data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Login"))} | ${ssrInterpolate($setup.seo.website_name)}</title>`);
      } else {
        return [
          createVNode("title", null, toDisplayString($setup.trans("Login")) + " | " + toDisplayString($setup.seo.website_name), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-f70e1090${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-f70e1090${_scopeId}></div><div class="container" data-v-f70e1090${_scopeId}><div class="banner-all-shape-wrapper" data-v-f70e1090${_scopeId}></div><div class="row align-items-center justify-content-between" data-v-f70e1090${_scopeId}><div class="col-12" data-v-f70e1090${_scopeId}><div class="breadcrumb__content text-center" data-v-f70e1090${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-f70e1090${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Login"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-f70e1090${_scopeId}><nav data-v-f70e1090${_scopeId}><ul data-v-f70e1090${_scopeId}><li data-v-f70e1090${_scopeId}><span data-v-f70e1090${_scopeId}><a href="/" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Home"))}</a></span></li><li class="active" data-v-f70e1090${_scopeId}><span data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Login"))}</span></li></ul></nav></div></div></div></div></div></div><section class="contact-us__area mt-25 overflow-hidden" data-v-f70e1090${_scopeId}><div class="container" data-v-f70e1090${_scopeId}><div class="row justify-content-center" data-v-f70e1090${_scopeId}><div class="col-xl-8" data-v-f70e1090${_scopeId}><div class="contact-us__form-wrapper mb-30 mb-xs-25" data-v-f70e1090${_scopeId}><h3 class="section__title mb-10 wow fadeInLeft animated" data-wow-delay=".3s" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Sign In to Your Account"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated" data-wow-delay=".5s" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Enter your credentials to access your account"))}</p><form class="contact-us__form" data-v-f70e1090${_scopeId}><div class="row wow fadeInLeft animated" data-wow-delay=".7s" data-v-f70e1090${_scopeId}><div class="col-12" data-v-f70e1090${_scopeId}><div class="contact-us__input" data-v-f70e1090${_scopeId}><label${ssrRenderAttr("for", "email")} class="form-label" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Email"))} <span class="text-danger" data-v-f70e1090${_scopeId}>*</span></label><input id="email"${ssrRenderAttr("value", $setup.form.email)} autofocus name="email" type="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-f70e1090${_scopeId}>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-f70e1090${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-12" data-v-f70e1090${_scopeId}><div class="contact-us__input" data-v-f70e1090${_scopeId}><label${ssrRenderAttr("for", "password")} class="form-label" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Password"))} <span class="text-danger" data-v-f70e1090${_scopeId}>*</span></label><input id="password"${ssrRenderAttr("value", $setup.form.password)} name="password" type="password"${ssrRenderAttr("placeholder", $setup.trans("Password"))} class="${ssrRenderClass({ "error": $props.errors.password })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-f70e1090${_scopeId}>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small" data-v-f70e1090${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-12" data-v-f70e1090${_scopeId}><div class="contact-us__input" data-v-f70e1090${_scopeId}><div class="" data-v-f70e1090${_scopeId}><input id="remember"${ssrIncludeBooleanAttr(Array.isArray($setup.form.remember) ? ssrLooseContain($setup.form.remember, null) : $setup.form.remember) ? " checked" : ""} class="" type="checkbox" data-v-f70e1090${_scopeId}><label class="mx-2" for="remember" data-v-f70e1090${_scopeId}>${ssrInterpolate($setup.trans("Remember Me"))}</label></div></div></div><div class="col-12" data-v-f70e1090${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "rr-btn mt-30"])}" data-v-f70e1090${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-f70e1090${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-f70e1090${_scopeId}></i>${ssrInterpolate($setup.trans("Signing In..."))}</span>`);
        } else {
          _push2(`<span data-v-f70e1090${_scopeId}><i class="fa-solid fa-sign-in-alt me-2" data-v-f70e1090${_scopeId}></i>${ssrInterpolate($setup.trans("Sign In"))}</span>`);
        }
        _push2(`</button></div><div class="col-12 mt-3 text-center" data-v-f70e1090${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("password.request"),
          class: "text-decoration-none"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<i class="fa-solid fa-key me-2" data-v-f70e1090${_scopeId2}></i>${ssrInterpolate($setup.trans("Forgot Password"))}`);
            } else {
              return [
                createVNode("i", { class: "fa-solid fa-key me-2" }),
                createTextVNode(toDisplayString($setup.trans("Forgot Password")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`<span class="mx-2" data-v-f70e1090${_scopeId}>|</span>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("register"),
          class: "text-decoration-none"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<i class="fa-solid fa-user-plus me-2" data-v-f70e1090${_scopeId2}></i>${ssrInterpolate($setup.trans("I Don't Have Account!"))}`);
            } else {
              return [
                createVNode("i", { class: "fa-solid fa-user-plus me-2" }),
                createTextVNode(toDisplayString($setup.trans("I Don't Have Account!")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div></form></div></div></div></div></section>`);
      } else {
        return [
          createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
            createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "banner-all-shape-wrapper" }),
              createVNode("div", { class: "row align-items-center justify-content-between" }, [
                createVNode("div", { class: "col-12" }, [
                  createVNode("div", { class: "breadcrumb__content text-center" }, [
                    createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                      createVNode("h1", {
                        class: "breadcrumb__title color-white wow fadeIn animated",
                        "data-wow-delay": ".1s"
                      }, toDisplayString($setup.trans("Login")), 1)
                    ]),
                    createVNode("div", {
                      class: "breadcrumb__menu wow fadeIn animated",
                      "data-wow-delay": ".5s"
                    }, [
                      createVNode("nav", null, [
                        createVNode("ul", null, [
                          createVNode("li", null, [
                            createVNode("span", null, [
                              createVNode("a", { href: "/" }, toDisplayString($setup.trans("Home")), 1)
                            ])
                          ]),
                          createVNode("li", { class: "active" }, [
                            createVNode("span", null, toDisplayString($setup.trans("Login")), 1)
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "contact-us__area mt-25 overflow-hidden" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "row justify-content-center" }, [
                createVNode("div", { class: "col-xl-8" }, [
                  createVNode("div", { class: "contact-us__form-wrapper mb-30 mb-xs-25" }, [
                    createVNode("h3", {
                      class: "section__title mb-10 wow fadeInLeft animated",
                      "data-wow-delay": ".3s"
                    }, toDisplayString($setup.trans("Sign In to Your Account")), 1),
                    createVNode("p", {
                      class: "mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated",
                      "data-wow-delay": ".5s"
                    }, toDisplayString($setup.trans("Enter your credentials to access your account")), 1),
                    createVNode("form", {
                      onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("login")), ["prevent"]),
                      class: "contact-us__form"
                    }, [
                      createVNode("div", {
                        class: "row wow fadeInLeft animated",
                        "data-wow-delay": ".7s"
                      }, [
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "email",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Email")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "email",
                              "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                              autofocus: "",
                              name: "email",
                              type: "email",
                              placeholder: $setup.trans("Email"),
                              class: { "error": $props.errors.email },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.email]
                            ]),
                            $props.errors.email ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "password",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Password")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "password",
                              "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                              name: "password",
                              type: "password",
                              placeholder: $setup.trans("Password"),
                              class: { "error": $props.errors.password },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.password]
                            ]),
                            $props.errors.password ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("div", { class: "" }, [
                              withDirectives(createVNode("input", {
                                id: "remember",
                                "onUpdate:modelValue": ($event) => $setup.form.remember = $event,
                                class: "",
                                type: "checkbox"
                              }, null, 8, ["onUpdate:modelValue"]), [
                                [vModelCheckbox, $setup.form.remember]
                              ]),
                              createVNode("label", {
                                class: "mx-2",
                                for: "remember"
                              }, toDisplayString($setup.trans("Remember Me")), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("button", {
                            type: "submit",
                            class: ["rr-btn mt-30", { "opacity-50": $setup.form.processing }],
                            disabled: $setup.form.processing
                          }, [
                            $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                              createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Signing In...")), 1)
                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("i", { class: "fa-solid fa-sign-in-alt me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Sign In")), 1)
                            ]))
                          ], 10, ["disabled"])
                        ]),
                        createVNode("div", { class: "col-12 mt-3 text-center" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("password.request"),
                            class: "text-decoration-none"
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fa-solid fa-key me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Forgot Password")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"]),
                          createVNode("span", { class: "mx-2" }, "|"),
                          createVNode(_component_Link, {
                            href: _ctx.route("register"),
                            class: "text-decoration-none"
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fa-solid fa-user-plus me-2" }),
                              createTextVNode(toDisplayString($setup.trans("I Don't Have Account!")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ], 40, ["onSubmit"])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/User/resources/assets/js/Pages/Auth/Login.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const Login = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$2], ["__scopeId", "data-v-f70e1090"]]);
const __vite_glob_0_9 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Login
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$3 = {
  components: {
    AppLayout: _sfc_main$f,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const seo = computed(() => page.props.seo);
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const form = useForm({
      name: "",
      email: "",
      mobile: "",
      password: "",
      password_confirmation: ""
    });
    return { form, seo, trans };
  }
};
function _sfc_ssrRender$1(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<title data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Register"))} | ${ssrInterpolate($setup.seo.website_name)}</title>`);
      } else {
        return [
          createVNode("title", null, toDisplayString($setup.trans("Register")) + " | " + toDisplayString($setup.seo.website_name), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-841463ef${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-841463ef${_scopeId}></div><div class="container" data-v-841463ef${_scopeId}><div class="banner-all-shape-wrapper" data-v-841463ef${_scopeId}></div><div class="row align-items-center justify-content-between" data-v-841463ef${_scopeId}><div class="col-12" data-v-841463ef${_scopeId}><div class="breadcrumb__content text-center" data-v-841463ef${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-841463ef${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Register"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-841463ef${_scopeId}><nav data-v-841463ef${_scopeId}><ul data-v-841463ef${_scopeId}><li data-v-841463ef${_scopeId}><span data-v-841463ef${_scopeId}><a href="/" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Home"))}</a></span></li><li class="active" data-v-841463ef${_scopeId}><span data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Register"))}</span></li></ul></nav></div></div></div></div></div></div><section class="contact-us__area mt-25 overflow-hidden" data-v-841463ef${_scopeId}><div class="container" data-v-841463ef${_scopeId}><div class="row justify-content-center" data-v-841463ef${_scopeId}><div class="col-xl-8" data-v-841463ef${_scopeId}><div class="contact-us__form-wrapper mb-30 mb-xs-25" data-v-841463ef${_scopeId}><h3 class="section__title mb-10 wow fadeInLeft animated" data-wow-delay=".3s" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Create A New Account"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated" data-wow-delay=".5s" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Fill out the form below to create your account"))}</p><form class="contact-us__form" data-v-841463ef${_scopeId}><div class="row wow fadeInLeft animated" data-wow-delay=".7s" data-v-841463ef${_scopeId}><div class="col-sm-6" data-v-841463ef${_scopeId}><div class="contact-us__input" data-v-841463ef${_scopeId}><label${ssrRenderAttr("for", "name")} class="form-label" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Name"))} <span class="text-danger" data-v-841463ef${_scopeId}>*</span></label><input id="name"${ssrRenderAttr("value", $setup.form.name)} autofocus name="name" type="text"${ssrRenderAttr("placeholder", $setup.trans("Name"))} class="${ssrRenderClass({ "error": $props.errors.name })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-841463ef${_scopeId}>`);
        if ($props.errors.name) {
          _push2(`<div class="text-danger mt-1 small" data-v-841463ef${_scopeId}>${ssrInterpolate($props.errors.name)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-841463ef${_scopeId}><div class="contact-us__input" data-v-841463ef${_scopeId}><label${ssrRenderAttr("for", "email")} class="form-label" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Email"))} <span class="text-danger" data-v-841463ef${_scopeId}>*</span></label><input id="email"${ssrRenderAttr("value", $setup.form.email)} name="email" type="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-841463ef${_scopeId}>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-841463ef${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-841463ef${_scopeId}><div class="contact-us__input" data-v-841463ef${_scopeId}><label${ssrRenderAttr("for", "mobile")} class="form-label" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Mobile"))} <span class="text-danger" data-v-841463ef${_scopeId}>*</span></label><input id="mobile"${ssrRenderAttr("value", $setup.form.mobile)} name="mobile" type="text"${ssrRenderAttr("placeholder", $setup.trans("Mobile"))} class="${ssrRenderClass({ "error": $props.errors.mobile })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-841463ef${_scopeId}>`);
        if ($props.errors.mobile) {
          _push2(`<div class="text-danger mt-1 small" data-v-841463ef${_scopeId}>${ssrInterpolate($props.errors.mobile)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-841463ef${_scopeId}><div class="contact-us__input" data-v-841463ef${_scopeId}><label${ssrRenderAttr("for", "password")} class="form-label" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Password"))} <span class="text-danger" data-v-841463ef${_scopeId}>*</span></label><input id="password"${ssrRenderAttr("value", $setup.form.password)} name="password" type="password"${ssrRenderAttr("placeholder", $setup.trans("Password"))} class="${ssrRenderClass({ "error": $props.errors.password })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-841463ef${_scopeId}>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small" data-v-841463ef${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-841463ef${_scopeId}><div class="contact-us__input" data-v-841463ef${_scopeId}><label${ssrRenderAttr("for", "password_confirmation")} class="form-label" data-v-841463ef${_scopeId}>${ssrInterpolate($setup.trans("Confirm Password"))} <span class="text-danger" data-v-841463ef${_scopeId}>*</span></label><input id="password_confirmation"${ssrRenderAttr("value", $setup.form.password_confirmation)} name="password_confirmation" type="password"${ssrRenderAttr("placeholder", $setup.trans("Confirm Password"))} class="${ssrRenderClass({ "error": $props.errors.password_confirmation })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-841463ef${_scopeId}>`);
        if ($props.errors.password_confirmation) {
          _push2(`<div class="text-danger mt-1 small" data-v-841463ef${_scopeId}>${ssrInterpolate($props.errors.password_confirmation)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-12" data-v-841463ef${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "rr-btn mt-30"])}" data-v-841463ef${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-841463ef${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-841463ef${_scopeId}></i>${ssrInterpolate($setup.trans("Registering..."))}</span>`);
        } else {
          _push2(`<span data-v-841463ef${_scopeId}><i class="fa-solid fa-user-plus me-2" data-v-841463ef${_scopeId}></i>${ssrInterpolate($setup.trans("Register"))}</span>`);
        }
        _push2(`</button></div><div class="col-12 mt-3 text-center" data-v-841463ef${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login"),
          class: "text-decoration-none"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<i class="fa-solid fa-sign-in-alt me-2" data-v-841463ef${_scopeId2}></i>${ssrInterpolate($setup.trans("Already Have An Account?"))}`);
            } else {
              return [
                createVNode("i", { class: "fa-solid fa-sign-in-alt me-2" }),
                createTextVNode(toDisplayString($setup.trans("Already Have An Account?")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div></form></div></div></div></div></section>`);
      } else {
        return [
          createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
            createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "banner-all-shape-wrapper" }),
              createVNode("div", { class: "row align-items-center justify-content-between" }, [
                createVNode("div", { class: "col-12" }, [
                  createVNode("div", { class: "breadcrumb__content text-center" }, [
                    createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                      createVNode("h1", {
                        class: "breadcrumb__title color-white wow fadeIn animated",
                        "data-wow-delay": ".1s"
                      }, toDisplayString($setup.trans("Register")), 1)
                    ]),
                    createVNode("div", {
                      class: "breadcrumb__menu wow fadeIn animated",
                      "data-wow-delay": ".5s"
                    }, [
                      createVNode("nav", null, [
                        createVNode("ul", null, [
                          createVNode("li", null, [
                            createVNode("span", null, [
                              createVNode("a", { href: "/" }, toDisplayString($setup.trans("Home")), 1)
                            ])
                          ]),
                          createVNode("li", { class: "active" }, [
                            createVNode("span", null, toDisplayString($setup.trans("Register")), 1)
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "contact-us__area mt-25 overflow-hidden" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "row justify-content-center" }, [
                createVNode("div", { class: "col-xl-8" }, [
                  createVNode("div", { class: "contact-us__form-wrapper mb-30 mb-xs-25" }, [
                    createVNode("h3", {
                      class: "section__title mb-10 wow fadeInLeft animated",
                      "data-wow-delay": ".3s"
                    }, toDisplayString($setup.trans("Create A New Account")), 1),
                    createVNode("p", {
                      class: "mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated",
                      "data-wow-delay": ".5s"
                    }, toDisplayString($setup.trans("Fill out the form below to create your account")), 1),
                    createVNode("form", {
                      onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("register")), ["prevent"]),
                      class: "contact-us__form"
                    }, [
                      createVNode("div", {
                        class: "row wow fadeInLeft animated",
                        "data-wow-delay": ".7s"
                      }, [
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "name",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Name")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "name",
                              "onUpdate:modelValue": ($event) => $setup.form.name = $event,
                              autofocus: "",
                              name: "name",
                              type: "text",
                              placeholder: $setup.trans("Name"),
                              class: { "error": $props.errors.name },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.name]
                            ]),
                            $props.errors.name ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.name), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "email",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Email")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "email",
                              "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                              name: "email",
                              type: "email",
                              placeholder: $setup.trans("Email"),
                              class: { "error": $props.errors.email },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.email]
                            ]),
                            $props.errors.email ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "mobile",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Mobile")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "mobile",
                              "onUpdate:modelValue": ($event) => $setup.form.mobile = $event,
                              name: "mobile",
                              type: "text",
                              placeholder: $setup.trans("Mobile"),
                              class: { "error": $props.errors.mobile },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.mobile]
                            ]),
                            $props.errors.mobile ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.mobile), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "password",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Password")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "password",
                              "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                              name: "password",
                              type: "password",
                              placeholder: $setup.trans("Password"),
                              class: { "error": $props.errors.password },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.password]
                            ]),
                            $props.errors.password ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "password_confirmation",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Confirm Password")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "password_confirmation",
                              "onUpdate:modelValue": ($event) => $setup.form.password_confirmation = $event,
                              name: "password_confirmation",
                              type: "password",
                              placeholder: $setup.trans("Confirm Password"),
                              class: { "error": $props.errors.password_confirmation },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.password_confirmation]
                            ]),
                            $props.errors.password_confirmation ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.password_confirmation), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("button", {
                            type: "submit",
                            class: ["rr-btn mt-30", { "opacity-50": $setup.form.processing }],
                            disabled: $setup.form.processing
                          }, [
                            $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                              createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Registering...")), 1)
                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("i", { class: "fa-solid fa-user-plus me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Register")), 1)
                            ]))
                          ], 10, ["disabled"])
                        ]),
                        createVNode("div", { class: "col-12 mt-3 text-center" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("login"),
                            class: "text-decoration-none"
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fa-solid fa-sign-in-alt me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Already Have An Account?")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ], 40, ["onSubmit"])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/User/resources/assets/js/Pages/Auth/Register.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const Register = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$1], ["__scopeId", "data-v-841463ef"]]);
const __vite_glob_0_10 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Register
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$2 = {
  components: {
    AppLayout: _sfc_main$f,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const seo = computed(() => page.props.seo);
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const params = new URLSearchParams(window.location.search);
    const form = useForm({
      email: "",
      password: "",
      remember: false,
      token: params.get("token") || ""
    });
    return { form, seo, trans };
  }
};
function _sfc_ssrRender(_ctx, _push, _parent, _attrs, $props, $setup, $data, $options) {
  const _component_Head = resolveComponent("Head");
  const _component_app_layout = resolveComponent("app-layout");
  const _component_Link = resolveComponent("Link");
  _push(`<!--[-->`);
  _push(ssrRenderComponent(_component_Head, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<title data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))} | ${ssrInterpolate($setup.seo.website_name)}</title>`);
      } else {
        return [
          createVNode("title", null, toDisplayString($setup.trans("Reset Password")) + " | " + toDisplayString($setup.seo.website_name), 1)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-71c7d17a${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-71c7d17a${_scopeId}></div><div class="container" data-v-71c7d17a${_scopeId}><div class="banner-all-shape-wrapper" data-v-71c7d17a${_scopeId}></div><div class="row align-items-center justify-content-between" data-v-71c7d17a${_scopeId}><div class="col-12" data-v-71c7d17a${_scopeId}><div class="breadcrumb__content text-center" data-v-71c7d17a${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-71c7d17a${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-71c7d17a${_scopeId}><nav data-v-71c7d17a${_scopeId}><ul data-v-71c7d17a${_scopeId}><li data-v-71c7d17a${_scopeId}><span data-v-71c7d17a${_scopeId}><a href="/" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Home"))}</a></span></li><li class="active" data-v-71c7d17a${_scopeId}><span data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))}</span></li></ul></nav></div></div></div></div></div></div><section class="contact-us__area mt-25 overflow-hidden" data-v-71c7d17a${_scopeId}><div class="container" data-v-71c7d17a${_scopeId}><div class="row justify-content-center" data-v-71c7d17a${_scopeId}><div class="col-xl-8" data-v-71c7d17a${_scopeId}><div class="contact-us__form-wrapper mb-30 mb-xs-25" data-v-71c7d17a${_scopeId}><h3 class="section__title mb-10 wow fadeInLeft animated" data-wow-delay=".3s" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Set New Password"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated" data-wow-delay=".5s" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Enter your email and new password to reset your account"))}</p><form class="contact-us__form" data-v-71c7d17a${_scopeId}><input${ssrRenderAttr("value", $setup.form.token)} name="token" type="hidden" data-v-71c7d17a${_scopeId}><div class="row wow fadeInLeft animated" data-wow-delay=".7s" data-v-71c7d17a${_scopeId}><div class="col-12" data-v-71c7d17a${_scopeId}><div class="contact-us__input" data-v-71c7d17a${_scopeId}><label${ssrRenderAttr("for", "email")} class="form-label" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Email"))} <span class="text-danger" data-v-71c7d17a${_scopeId}>*</span></label><input id="email"${ssrRenderAttr("value", $setup.form.email)} autofocus name="email" type="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-71c7d17a${_scopeId}>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-71c7d17a${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-71c7d17a${_scopeId}><div class="contact-us__input" data-v-71c7d17a${_scopeId}><label${ssrRenderAttr("for", "password")} class="form-label" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Password"))} <span class="text-danger" data-v-71c7d17a${_scopeId}>*</span></label><input id="password"${ssrRenderAttr("value", $setup.form.password)} name="password" type="password"${ssrRenderAttr("placeholder", $setup.trans("Password"))} class="${ssrRenderClass({ "error": $props.errors.password })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-71c7d17a${_scopeId}>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small" data-v-71c7d17a${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-sm-6" data-v-71c7d17a${_scopeId}><div class="contact-us__input" data-v-71c7d17a${_scopeId}><label${ssrRenderAttr("for", "password_confirmation")} class="form-label" data-v-71c7d17a${_scopeId}>${ssrInterpolate($setup.trans("Confirm Password"))} <span class="text-danger" data-v-71c7d17a${_scopeId}>*</span></label><input id="password_confirmation"${ssrRenderAttr("value", $setup.form.password_confirmation)} name="password_confirmation" type="password"${ssrRenderAttr("placeholder", $setup.trans("Confirm Password"))} class="${ssrRenderClass({ "error": $props.errors.password_confirmation })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-71c7d17a${_scopeId}>`);
        if ($props.errors.password_confirmation) {
          _push2(`<div class="text-danger mt-1 small" data-v-71c7d17a${_scopeId}>${ssrInterpolate($props.errors.password_confirmation)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-12" data-v-71c7d17a${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "rr-btn mt-30"])}" data-v-71c7d17a${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-71c7d17a${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-71c7d17a${_scopeId}></i>${ssrInterpolate($setup.trans("Resetting..."))}</span>`);
        } else {
          _push2(`<span data-v-71c7d17a${_scopeId}><i class="fa-solid fa-key me-2" data-v-71c7d17a${_scopeId}></i>${ssrInterpolate($setup.trans("Reset Password"))}</span>`);
        }
        _push2(`</button></div><div class="col-12 mt-3 text-center" data-v-71c7d17a${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login"),
          class: "text-decoration-none"
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`<i class="fa-solid fa-arrow-left me-2" data-v-71c7d17a${_scopeId2}></i>${ssrInterpolate($setup.trans("Back to Login"))}`);
            } else {
              return [
                createVNode("i", { class: "fa-solid fa-arrow-left me-2" }),
                createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div></form></div></div></div></div></section>`);
      } else {
        return [
          createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
            createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "banner-all-shape-wrapper" }),
              createVNode("div", { class: "row align-items-center justify-content-between" }, [
                createVNode("div", { class: "col-12" }, [
                  createVNode("div", { class: "breadcrumb__content text-center" }, [
                    createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                      createVNode("h1", {
                        class: "breadcrumb__title color-white wow fadeIn animated",
                        "data-wow-delay": ".1s"
                      }, toDisplayString($setup.trans("Reset Password")), 1)
                    ]),
                    createVNode("div", {
                      class: "breadcrumb__menu wow fadeIn animated",
                      "data-wow-delay": ".5s"
                    }, [
                      createVNode("nav", null, [
                        createVNode("ul", null, [
                          createVNode("li", null, [
                            createVNode("span", null, [
                              createVNode("a", { href: "/" }, toDisplayString($setup.trans("Home")), 1)
                            ])
                          ]),
                          createVNode("li", { class: "active" }, [
                            createVNode("span", null, toDisplayString($setup.trans("Reset Password")), 1)
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "contact-us__area mt-25 overflow-hidden" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "row justify-content-center" }, [
                createVNode("div", { class: "col-xl-8" }, [
                  createVNode("div", { class: "contact-us__form-wrapper mb-30 mb-xs-25" }, [
                    createVNode("h3", {
                      class: "section__title mb-10 wow fadeInLeft animated",
                      "data-wow-delay": ".3s"
                    }, toDisplayString($setup.trans("Set New Password")), 1),
                    createVNode("p", {
                      class: "mb-40 mb-sm-25 mb-xs-20 wow fadeInLeft animated",
                      "data-wow-delay": ".5s"
                    }, toDisplayString($setup.trans("Enter your email and new password to reset your account")), 1),
                    createVNode("form", {
                      onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("password.update")), ["prevent"]),
                      class: "contact-us__form"
                    }, [
                      createVNode("input", {
                        value: $setup.form.token,
                        name: "token",
                        type: "hidden"
                      }, null, 8, ["value"]),
                      createVNode("div", {
                        class: "row wow fadeInLeft animated",
                        "data-wow-delay": ".7s"
                      }, [
                        createVNode("div", { class: "col-12" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "email",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Email")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "email",
                              "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                              autofocus: "",
                              name: "email",
                              type: "email",
                              placeholder: $setup.trans("Email"),
                              class: { "error": $props.errors.email },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.email]
                            ]),
                            $props.errors.email ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "password",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Password")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "password",
                              "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                              name: "password",
                              type: "password",
                              placeholder: $setup.trans("Password"),
                              class: { "error": $props.errors.password },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.password]
                            ]),
                            $props.errors.password ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-sm-6" }, [
                          createVNode("div", { class: "contact-us__input" }, [
                            createVNode("label", {
                              for: "password_confirmation",
                              class: "form-label"
                            }, [
                              createTextVNode(toDisplayString($setup.trans("Confirm Password")) + " ", 1),
                              createVNode("span", { class: "text-danger" }, "*")
                            ]),
                            withDirectives(createVNode("input", {
                              id: "password_confirmation",
                              "onUpdate:modelValue": ($event) => $setup.form.password_confirmation = $event,
                              name: "password_confirmation",
                              type: "password",
                              placeholder: $setup.trans("Confirm Password"),
                              class: { "error": $props.errors.password_confirmation },
                              disabled: $setup.form.processing,
                              required: ""
                            }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                              [vModelText, $setup.form.password_confirmation]
                            ]),
                            $props.errors.password_confirmation ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString($props.errors.password_confirmation), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("div", { class: "col-12" }, [
                          createVNode("button", {
                            type: "submit",
                            class: ["rr-btn mt-30", { "opacity-50": $setup.form.processing }],
                            disabled: $setup.form.processing
                          }, [
                            $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                              createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Resetting...")), 1)
                            ])) : (openBlock(), createBlock("span", { key: 1 }, [
                              createVNode("i", { class: "fa-solid fa-key me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Reset Password")), 1)
                            ]))
                          ], 10, ["disabled"])
                        ]),
                        createVNode("div", { class: "col-12 mt-3 text-center" }, [
                          createVNode(_component_Link, {
                            href: _ctx.route("login"),
                            class: "text-decoration-none"
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fa-solid fa-arrow-left me-2" }),
                              createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ], 40, ["onSubmit"])
                  ])
                ])
              ])
            ])
          ])
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(`<!--]-->`);
}
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/User/resources/assets/js/Pages/Auth/ResetPassword.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const ResetPassword = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender], ["__scopeId", "data-v-71c7d17a"]]);
const __vite_glob_0_11 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ResetPassword
}, Symbol.toStringTag, { value: "Module" }));
const __default__$1 = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main$1 = /* @__PURE__ */ Object.assign(__default__$1, {
  __name: "Error404",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const seo = computed(() => page.props.seo || { website_name: "Sham Vision" });
    const asset_path = computed(() => page.props.asset_path || "/");
    const getHomeUrl = () => {
      try {
        return route("home");
      } catch (e) {
        return "/";
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-4b2b70de${_scopeId}>${ssrInterpolate(trans("404 Error"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("404 Error")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-4b2b70de${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-4b2b70de${_scopeId}></div><div class="container" data-v-4b2b70de${_scopeId}><div class="banner-all-shape-wrapper" data-v-4b2b70de${_scopeId}><div class="banner-home__banner-shape-1 first-shape" data-v-4b2b70de${_scopeId}><img class="upDown-top"${ssrRenderAttr("src", asset_path.value + "site/imgs/banner-1/banner-shape-1.svg")} alt="img not found" data-v-4b2b70de${_scopeId}></div><div class="banner-home__banner-shape-2 second-shape" data-v-4b2b70de${_scopeId}><img class="upDown-bottom"${ssrRenderAttr("src", asset_path.value + "site/imgs/banner-1/banner-shape-2.svg")} alt="img not found" data-v-4b2b70de${_scopeId}></div><div class="right-shape" data-v-4b2b70de${_scopeId}><img class="zooming"${ssrRenderAttr("src", asset_path.value + "site/imgs/inner-img/inner-right-shape.svg")} alt="img not found" data-v-4b2b70de${_scopeId}></div></div><div class="row align-items-center justify-content-between" data-v-4b2b70de${_scopeId}><div class="col-12" data-v-4b2b70de${_scopeId}><div class="breadcrumb__content text-center" data-v-4b2b70de${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-4b2b70de${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-4b2b70de${_scopeId}>${ssrInterpolate(trans("404 Error"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-4b2b70de${_scopeId}><nav data-v-4b2b70de${_scopeId}><ul data-v-4b2b70de${_scopeId}><li data-v-4b2b70de${_scopeId}><span data-v-4b2b70de${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHomeUrl()
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-4b2b70de${_scopeId}><span data-v-4b2b70de${_scopeId}>${ssrInterpolate(trans("404 Error"))}</span></li></ul></nav></div></div></div></div></div></div><section class="error section-space" data-v-4b2b70de${_scopeId}><div class="container" data-v-4b2b70de${_scopeId}><div class="row" data-v-4b2b70de${_scopeId}><div class="col-12" data-v-4b2b70de${_scopeId}><div class="error__content" data-v-4b2b70de${_scopeId}><div class="error__content-media mb-40 mb-sm-35 mb-xs-30" data-v-4b2b70de${_scopeId}><img class="upDown-bottom"${ssrRenderAttr("src", asset_path.value + "images/404.png")} alt="404 error" data-v-4b2b70de${_scopeId}></div><div class="section__title-wrapper text-center" data-v-4b2b70de${_scopeId}><h3 class="section__title mb-15 mb-xs-10 wow fadeIn animated" data-wow-delay=".3s" data-v-4b2b70de${_scopeId}>${ssrInterpolate(trans("Sorry.... This Page Not Found"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated" data-wow-delay=".5s" data-v-4b2b70de${_scopeId}>${ssrInterpolate(trans("The page you are looking for might have been removed, had its name changed, or is temporarily unavailable."))}</p><div class="error-btn-wrap" data-v-4b2b70de${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHomeUrl(),
              class: "error-btn wow fadeIn animated",
              "data-wow-delay": ".7s"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Back To Home Page"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Back To Home Page")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "banner-all-shape-wrapper" }, [
                    createVNode("div", { class: "banner-home__banner-shape-1 first-shape" }, [
                      createVNode("img", {
                        class: "upDown-top",
                        src: asset_path.value + "site/imgs/banner-1/banner-shape-1.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "banner-home__banner-shape-2 second-shape" }, [
                      createVNode("img", {
                        class: "upDown-bottom",
                        src: asset_path.value + "site/imgs/banner-1/banner-shape-2.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "right-shape" }, [
                      createVNode("img", {
                        class: "zooming",
                        src: asset_path.value + "site/imgs/inner-img/inner-right-shape.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ])
                  ]),
                  createVNode("div", { class: "row align-items-center justify-content-between" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title color-white wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("404 Error")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: getHomeUrl()
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("404 Error")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "error section-space" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "error__content" }, [
                        createVNode("div", { class: "error__content-media mb-40 mb-sm-35 mb-xs-30" }, [
                          createVNode("img", {
                            class: "upDown-bottom",
                            src: asset_path.value + "images/404.png",
                            alt: "404 error"
                          }, null, 8, ["src"])
                        ]),
                        createVNode("div", { class: "section__title-wrapper text-center" }, [
                          createVNode("h3", {
                            class: "section__title mb-15 mb-xs-10 wow fadeIn animated",
                            "data-wow-delay": ".3s"
                          }, toDisplayString(trans("Sorry.... This Page Not Found")), 1),
                          createVNode("p", {
                            class: "mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated",
                            "data-wow-delay": ".5s"
                          }, toDisplayString(trans("The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.")), 1),
                          createVNode("div", { class: "error-btn-wrap" }, [
                            createVNode(unref(Link), {
                              href: getHomeUrl(),
                              class: "error-btn wow fadeIn animated",
                              "data-wow-delay": ".7s"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("Back To Home Page")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Error404.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const Error404 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-4b2b70de"]]);
const __vite_glob_1_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Error404
}, Symbol.toStringTag, { value: "Module" }));
const __default__ = {
  components: {
    AppLayout: _sfc_main$f
  }
};
const _sfc_main = /* @__PURE__ */ Object.assign(__default__, {
  __name: "Error500",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const seo = computed(() => page.props.seo || { website_name: "Sham Vision" });
    const asset_path = computed(() => page.props.asset_path || "/");
    const appEnv = computed(() => page.props.app_env || "production");
    const isNonProduction = computed(() => appEnv.value !== "production");
    const getHomeUrl = () => {
      try {
        return route("home");
      } catch (e) {
        return "/";
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title data-v-ac51474d${_scopeId}>${ssrInterpolate(trans("500 Error"))} | ${ssrInterpolate(seo.value.website_name)}</title>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(trans("500 Error")) + " | " + toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$f, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p;
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-ac51474d${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-ac51474d${_scopeId}></div><div class="container" data-v-ac51474d${_scopeId}><div class="banner-all-shape-wrapper" data-v-ac51474d${_scopeId}><div class="banner-home__banner-shape-1 first-shape" data-v-ac51474d${_scopeId}><img class="upDown-top"${ssrRenderAttr("src", asset_path.value + "site/imgs/banner-1/banner-shape-1.svg")} alt="img not found" data-v-ac51474d${_scopeId}></div><div class="banner-home__banner-shape-2 second-shape" data-v-ac51474d${_scopeId}><img class="upDown-bottom"${ssrRenderAttr("src", asset_path.value + "site/imgs/banner-1/banner-shape-2.svg")} alt="img not found" data-v-ac51474d${_scopeId}></div><div class="right-shape" data-v-ac51474d${_scopeId}><img class="zooming"${ssrRenderAttr("src", asset_path.value + "site/imgs/inner-img/inner-right-shape.svg")} alt="img not found" data-v-ac51474d${_scopeId}></div></div><div class="row align-items-center justify-content-between" data-v-ac51474d${_scopeId}><div class="col-12" data-v-ac51474d${_scopeId}><div class="breadcrumb__content text-center" data-v-ac51474d${_scopeId}><div class="breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" data-v-ac51474d${_scopeId}><h1 class="breadcrumb__title color-white wow fadeIn animated" data-wow-delay=".1s" data-v-ac51474d${_scopeId}>${ssrInterpolate(trans("500 Error"))}</h1></div><div class="breadcrumb__menu wow fadeIn animated" data-wow-delay=".5s" data-v-ac51474d${_scopeId}><nav data-v-ac51474d${_scopeId}><ul data-v-ac51474d${_scopeId}><li data-v-ac51474d${_scopeId}><span data-v-ac51474d${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHomeUrl()
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</span></li><li class="active" data-v-ac51474d${_scopeId}><span data-v-ac51474d${_scopeId}>${ssrInterpolate(trans("500 Error"))}</span></li></ul></nav></div></div></div></div></div></div><section class="error section-space" data-v-ac51474d${_scopeId}><div class="container" data-v-ac51474d${_scopeId}><div class="row" data-v-ac51474d${_scopeId}><div class="col-12" data-v-ac51474d${_scopeId}><div class="error__content" data-v-ac51474d${_scopeId}><div class="section__title-wrapper text-center" data-v-ac51474d${_scopeId}><h3 class="section__title mb-15 mb-xs-10 wow fadeIn animated" data-wow-delay=".3s" data-v-ac51474d${_scopeId}>${ssrInterpolate(trans("Internal Server Error"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated" data-wow-delay=".5s" data-v-ac51474d${_scopeId}>${ssrInterpolate(trans("We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists."))}</p>`);
            if (isNonProduction.value && (((_b = (_a = unref(page)) == null ? void 0 : _a.props) == null ? void 0 : _b.error) || ((_d = (_c = unref(page)) == null ? void 0 : _c.props) == null ? void 0 : _d.trace))) {
              _push2(`<div class="alert alert-danger text-start mb-40" style="${ssrRenderStyle({ "white-space": "pre-wrap", "word-break": "break-word" })}" data-v-ac51474d${_scopeId}><strong data-v-ac51474d${_scopeId}>Debug Error:</strong>`);
              if ((_f = (_e = unref(page)) == null ? void 0 : _e.props) == null ? void 0 : _f.error) {
                _push2(`<div data-v-ac51474d${_scopeId}>${ssrInterpolate(unref(page).props.error)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if ((_h = (_g = unref(page)) == null ? void 0 : _g.props) == null ? void 0 : _h.trace) {
                _push2(`<details class="mt-3" data-v-ac51474d${_scopeId}><summary data-v-ac51474d${_scopeId}>Stack trace</summary><pre class="mt-2" data-v-ac51474d${_scopeId}>${ssrInterpolate(unref(page).props.trace)}</pre></details>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="error-btn-wrap" data-v-ac51474d${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHomeUrl(),
              class: "error-btn wow fadeIn animated",
              "data-wow-delay": ".7s"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Back To Home Page"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Back To Home Page")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" }, [
                createVNode("div", { class: "banner-home__middel-shape inner-top-shape" }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "banner-all-shape-wrapper" }, [
                    createVNode("div", { class: "banner-home__banner-shape-1 first-shape" }, [
                      createVNode("img", {
                        class: "upDown-top",
                        src: asset_path.value + "site/imgs/banner-1/banner-shape-1.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "banner-home__banner-shape-2 second-shape" }, [
                      createVNode("img", {
                        class: "upDown-bottom",
                        src: asset_path.value + "site/imgs/banner-1/banner-shape-2.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ]),
                    createVNode("div", { class: "right-shape" }, [
                      createVNode("img", {
                        class: "zooming",
                        src: asset_path.value + "site/imgs/inner-img/inner-right-shape.svg",
                        alt: "img not found"
                      }, null, 8, ["src"])
                    ])
                  ]),
                  createVNode("div", { class: "row align-items-center justify-content-between" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "breadcrumb__content text-center" }, [
                        createVNode("div", { class: "breadcrumb__title-wrapper mb-15 mb-sm-10 mb-xs-5" }, [
                          createVNode("h1", {
                            class: "breadcrumb__title color-white wow fadeIn animated",
                            "data-wow-delay": ".1s"
                          }, toDisplayString(trans("500 Error")), 1)
                        ]),
                        createVNode("div", {
                          class: "breadcrumb__menu wow fadeIn animated",
                          "data-wow-delay": ".5s"
                        }, [
                          createVNode("nav", null, [
                            createVNode("ul", null, [
                              createVNode("li", null, [
                                createVNode("span", null, [
                                  createVNode(unref(Link), {
                                    href: getHomeUrl()
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(trans("Home")), 1)
                                    ]),
                                    _: 1
                                  }, 8, ["href"])
                                ])
                              ]),
                              createVNode("li", { class: "active" }, [
                                createVNode("span", null, toDisplayString(trans("500 Error")), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "error section-space" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-12" }, [
                      createVNode("div", { class: "error__content" }, [
                        createVNode("div", { class: "section__title-wrapper text-center" }, [
                          createVNode("h3", {
                            class: "section__title mb-15 mb-xs-10 wow fadeIn animated",
                            "data-wow-delay": ".3s"
                          }, toDisplayString(trans("Internal Server Error")), 1),
                          createVNode("p", {
                            class: "mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated",
                            "data-wow-delay": ".5s"
                          }, toDisplayString(trans("We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists.")), 1),
                          isNonProduction.value && (((_j = (_i = unref(page)) == null ? void 0 : _i.props) == null ? void 0 : _j.error) || ((_l = (_k = unref(page)) == null ? void 0 : _k.props) == null ? void 0 : _l.trace)) ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "alert alert-danger text-start mb-40",
                            style: { "white-space": "pre-wrap", "word-break": "break-word" }
                          }, [
                            createVNode("strong", null, "Debug Error:"),
                            ((_n = (_m = unref(page)) == null ? void 0 : _m.props) == null ? void 0 : _n.error) ? (openBlock(), createBlock("div", { key: 0 }, toDisplayString(unref(page).props.error), 1)) : createCommentVNode("", true),
                            ((_p = (_o = unref(page)) == null ? void 0 : _o.props) == null ? void 0 : _p.trace) ? (openBlock(), createBlock("details", {
                              key: 1,
                              class: "mt-3"
                            }, [
                              createVNode("summary", null, "Stack trace"),
                              createVNode("pre", { class: "mt-2" }, toDisplayString(unref(page).props.trace), 1)
                            ])) : createCommentVNode("", true)
                          ])) : createCommentVNode("", true),
                          createVNode("div", { class: "error-btn-wrap" }, [
                            createVNode(unref(Link), {
                              href: getHomeUrl(),
                              class: "error-btn wow fadeIn animated",
                              "data-wow-delay": ".7s"
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(trans("Back To Home Page")), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Error500.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Error500 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-ac51474d"]]);
const __vite_glob_1_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Error500
}, Symbol.toStringTag, { value: "Module" }));
async function resolvePageComponent(path, pages) {
  for (const p of Array.isArray(path) ? path : [path]) {
    const page = pages[p];
    if (typeof page === "undefined") {
      continue;
    }
    return typeof page === "function" ? page() : page;
  }
  throw new Error(`Page not found: ${path}`);
}
createServer(
  (page) => createInertiaApp({
    page,
    render: renderToString,
    resolve: (name) => {
      const modules = name.split("::");
      if (modules.length > 1) {
        return resolvePageComponent(
          `../../Modules/${modules[0]}/resources/assets/js/Pages/${modules[1]}.vue`,
          /* @__PURE__ */ Object.assign({
            "../../Modules/Base/resources/assets/js/Pages/Index.vue": __vite_glob_0_0,
            "../../Modules/Cms/resources/assets/js/Pages/AboutUs.vue": __vite_glob_0_1,
            "../../Modules/Cms/resources/assets/js/Pages/BlogIndex.vue": __vite_glob_0_2,
            "../../Modules/Cms/resources/assets/js/Pages/BlogShow.vue": __vite_glob_0_3,
            "../../Modules/Cms/resources/assets/js/Pages/PageShow.vue": __vite_glob_0_4,
            "../../Modules/Services/resources/assets/js/Pages/ServiceIndex.vue": __vite_glob_0_5,
            "../../Modules/Services/resources/assets/js/Pages/ServiceShow.vue": __vite_glob_0_6,
            "../../Modules/Support/resources/assets/js/Pages/Index.vue": __vite_glob_0_7,
            "../../Modules/User/resources/assets/js/Pages/Auth/ForgotPassword.vue": __vite_glob_0_8,
            "../../Modules/User/resources/assets/js/Pages/Auth/Login.vue": __vite_glob_0_9,
            "../../Modules/User/resources/assets/js/Pages/Auth/Register.vue": __vite_glob_0_10,
            "../../Modules/User/resources/assets/js/Pages/Auth/ResetPassword.vue": __vite_glob_0_11
          })
        );
      }
      return resolvePageComponent(
        `./Pages/${name}.vue`,
        /* @__PURE__ */ Object.assign({ "./Pages/Error404.vue": __vite_glob_1_0, "./Pages/Error500.vue": __vite_glob_1_1 })
      );
    },
    setup({ App, props, plugin }) {
      return createSSRApp({
        render: () => h(App, props)
      }).use(plugin);
    }
  })
);
