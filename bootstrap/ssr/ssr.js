import { useSSRContext, computed, mergeProps, unref, withCtx, createVNode, createTextVNode, toDisplayString, ref, onMounted, onUnmounted, nextTick, openBlock, createBlock, createCommentVNode, Fragment, renderList, withModifiers, withDirectives, vModelText, resolveComponent, vModelCheckbox, createSSRApp, h } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderClass, ssrRenderList, ssrIncludeBooleanAttr, ssrRenderSlot, ssrRenderStyle, ssrLooseContain } from "vue/server-renderer";
import { usePage, Link, useForm, router, Head, createInertiaApp } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString } from "@vue/server-renderer";
const _sfc_main$l = {
  __name: "HomeBlogCard",
  __ssrInlineRender: true,
  props: {
    post: {
      type: Object,
      required: true
    },
    variant: {
      type: String,
      default: "compact"
    },
    locale: {
      type: String,
      default: "en"
    },
    assetPath: {
      type: String,
      default: ""
    },
    imageFallbackIndex: {
      type: Number,
      default: 1
    },
    showReadingTime: {
      type: Boolean,
      default: true
    },
    animationClass: {
      type: String,
      default: ""
    },
    animationDelay: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const props = __props;
    const page = usePage();
    const trans = (key) => {
      var _a;
      return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
    };
    const postUrl = computed(() => {
      if (!props.post || !props.post.slug) {
        return "#";
      }
      try {
        return route("blogs.show", props.post.slug);
      } catch (e) {
        return "#";
      }
    });
    const imageSrc = computed(() => {
      var _a;
      const link = ((_a = props.post) == null ? void 0 : _a.image_link) || "";
      if (!link || link.includes("/images/blank.png")) {
        return `${props.assetPath}site/images/blog/blog-2-${props.imageFallbackIndex}.jpg`;
      }
      return link;
    });
    const translateField = (value) => {
      if (!value) {
        return "";
      }
      if (typeof value === "string") {
        return value;
      }
      const loc = props.locale;
      if (typeof value === "object" && value !== null) {
        if (value[loc]) {
          return value[loc];
        }
      }
      return "";
    };
    const truncate = (text, length) => {
      if (!text) return "";
      return text.length > length ? text.substring(0, length) + "..." : text;
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.variant === "featured") {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "blog-two__single" }, _attrs))}><div class="blog-two__img">`);
        _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<img${ssrRenderAttr("src", imageSrc.value)}${ssrRenderAttr("alt", translateField(__props.post.title))}${_scopeId}>`);
            } else {
              return [
                createVNode("img", {
                  src: imageSrc.value,
                  alt: translateField(__props.post.title)
                }, null, 8, ["src", "alt"])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`<div class="blog-two__tags"><span>${ssrInterpolate(translateField(__props.post.category.name))}</span></div></div><div class="blog-two__content"><ul class="blog-two__meta list-unstyled">`);
        if (__props.post.created_at) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-calendar-alt"${_scopeId}></span>${ssrInterpolate(__props.post.created_at)}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-calendar-alt" }),
                  createTextVNode(toDisplayString(__props.post.created_at), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.post.comments_count) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-comments"${_scopeId}></span>${ssrInterpolate(__props.post.comments_count)} ${ssrInterpolate(trans("Comments"))}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-comments" }),
                  createTextVNode(toDisplayString(__props.post.comments_count) + " " + toDisplayString(trans("Comments")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else if (__props.showReadingTime && __props.post.reading_time) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-clock"${_scopeId}></span>${ssrInterpolate(__props.post.reading_time)} ${ssrInterpolate(trans("min read"))}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-clock" }),
                  createTextVNode(toDisplayString(__props.post.reading_time) + " " + toDisplayString(trans("min read")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</ul><h3 class="blog-two__title">`);
        _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(truncate(translateField(__props.post.title), 60))}`);
            } else {
              return [
                createTextVNode(toDisplayString(truncate(translateField(__props.post.title), 60)), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</h3><p class="blog-two__text">${ssrInterpolate(truncate(translateField(__props.post.description), 160))}</p><div class="blog-two__btn-box">`);
        _push(ssrRenderComponent(unref(Link), {
          href: postUrl.value,
          class: "thm-btn"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(trans("Read More"))} <span class="${ssrRenderClass(`icon-${__props.locale === "ar" ? "left" : "right"}-arrow `)}"${_scopeId}></span>`);
            } else {
              return [
                createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                createVNode("span", {
                  class: `icon-${__props.locale === "ar" ? "left" : "right"}-arrow `
                }, null, 2)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div></div>`);
      } else {
        _push(`<div${ssrRenderAttrs(mergeProps({
          class: ["blog-two__single-two wow", __props.animationClass],
          "data-wow-delay": __props.animationDelay
        }, _attrs))}><div class="blog-two__img-two">`);
        _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<img${ssrRenderAttr("src", imageSrc.value)}${ssrRenderAttr("alt", translateField(__props.post.title))}${_scopeId}>`);
            } else {
              return [
                createVNode("img", {
                  src: imageSrc.value,
                  alt: translateField(__props.post.title)
                }, null, 8, ["src", "alt"])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div><div class="blog-two__content-two"><div class="blog-two__tags-two"><span>${ssrInterpolate(translateField(__props.post.category.name))}</span></div><h3 class="blog-two__title-two">`);
        _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(truncate(translateField(__props.post.title), 60))}`);
            } else {
              return [
                createTextVNode(toDisplayString(truncate(translateField(__props.post.title), 60)), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</h3><ul class="blog-two__meta-two list-unstyled">`);
        if (__props.post.created_at) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-calendar-alt"${_scopeId}></span>${ssrInterpolate(__props.post.created_at)}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-calendar-alt" }),
                  createTextVNode(toDisplayString(__props.post.created_at), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else {
          _push(`<!---->`);
        }
        if (__props.post.comments_count) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-comments"${_scopeId}></span>${ssrInterpolate(__props.post.comments_count)} ${ssrInterpolate(trans("Comments"))}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-comments" }),
                  createTextVNode(toDisplayString(__props.post.comments_count) + " " + toDisplayString(trans("Comments")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else if (__props.showReadingTime && __props.post.reading_time) {
          _push(`<li>`);
          _push(ssrRenderComponent(unref(Link), { href: postUrl.value }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<span class="far fa-clock"${_scopeId}></span>${ssrInterpolate(__props.post.reading_time)} ${ssrInterpolate(trans("min read"))}`);
              } else {
                return [
                  createVNode("span", { class: "far fa-clock" }),
                  createTextVNode(toDisplayString(__props.post.reading_time) + " " + toDisplayString(trans("min read")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push(`</li>`);
        } else {
          _push(`<!---->`);
        }
        _push(`</ul><div class="blog-two__btn-box-two">`);
        _push(ssrRenderComponent(unref(Link), {
          href: postUrl.value,
          class: "thm-btn"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(trans("Read More"))} <span class="${ssrRenderClass(`icon-${__props.locale === "ar" ? "left" : "right"}-arrow `)}"${_scopeId}></span>`);
            } else {
              return [
                createTextVNode(toDisplayString(trans("Read More")) + " ", 1),
                createVNode("span", {
                  class: `icon-${__props.locale === "ar" ? "left" : "right"}-arrow `
                }, null, 2)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div></div></div>`);
      }
    };
  }
};
const _sfc_setup$l = _sfc_main$l.setup;
_sfc_main$l.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/HomeBlogCard.vue");
  return _sfc_setup$l ? _sfc_setup$l(props, ctx) : void 0;
};
const _export_sfc = (sfc, props) => {
  const target = sfc.__vccOpts || sfc;
  for (const [key, val] of props) {
    target[key] = val;
  }
  return target;
};
const _sfc_main$k = {
  __name: "ServiceCardThree",
  __ssrInlineRender: true,
  props: {
    title: { type: String, required: true },
    description: { type: String, default: "" },
    highlights: { type: Array, default: () => [] },
    link: { type: String, required: true },
    image: { type: String, default: "" },
    buttonLabel: { type: String, default: "Read More" },
    isRtl: { type: Boolean, default: false },
    readingTime: { type: [Number, String], default: 0 },
    readingTimeLabel: { type: String, default: "min read" }
  },
  setup(__props) {
    const props = __props;
    const parseMaybeJson = (value) => {
      if (typeof value !== "string") {
        return value;
      }
      const trimmed = value.trim();
      if (!trimmed.startsWith("{") && !trimmed.startsWith("[")) {
        return value;
      }
      try {
        return JSON.parse(trimmed);
      } catch (e) {
        try {
          return JSON.parse(trimmed.replace(/'/g, '"'));
        } catch (err) {
          return value;
        }
      }
    };
    const normalizeHighlights = (items) => {
      if (!items) {
        return [];
      }
      const rawItems = Array.isArray(items) ? items : [items];
      return rawItems.map((item) => parseMaybeJson(item)).flatMap((item) => {
        if (Array.isArray(item)) {
          return item;
        }
        return [item];
      }).map((item) => {
        if (typeof item === "string") {
          return item;
        }
        if (item && typeof item === "object") {
          if (item.value) {
            return item.value;
          }
          if (item.label) {
            return item.label;
          }
          return JSON.stringify(item);
        }
        return "";
      }).map((item) => String(item).replace(/^\s+|\s+$/g, "")).filter(Boolean);
    };
    const safeHighlights = computed(() => {
      const normalized = normalizeHighlights(props.highlights);
      return normalized.slice(0, 3);
    });
    const shortDescription = computed(() => {
      if (!props.description) {
        return "";
      }
      const text = String(props.description).replace(/\s+/g, " ").trim();
      if (text.length <= 140) {
        return text;
      }
      return `${text.slice(0, 140)}...`;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "services-three__single" }, _attrs))} data-v-c6652724><div class="services-three__media" data-v-c6652724>`);
      if (__props.image) {
        _push(`<img${ssrRenderAttr("src", __props.image)}${ssrRenderAttr("alt", __props.title)} class="services-three__image" data-v-c6652724>`);
      } else {
        _push(`<div class="services-three__image-placeholder" data-v-c6652724><span class="icon-technical-support" data-v-c6652724></span></div>`);
      }
      _push(`</div><h3 class="services-three__title" data-v-c6652724>`);
      _push(ssrRenderComponent(unref(Link), { href: __props.link }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(__props.title)}`);
          } else {
            return [
              createTextVNode(toDisplayString(__props.title), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</h3>`);
      if (shortDescription.value) {
        _push(`<p class="services-three__text" data-v-c6652724>${ssrInterpolate(shortDescription.value)}</p>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.readingTime) {
        _push(`<p class="services-three__meta" data-v-c6652724><span class="far fa-clock mx-1" data-v-c6652724></span>${ssrInterpolate(__props.readingTime)} ${ssrInterpolate(__props.readingTimeLabel)}</p>`);
      } else {
        _push(`<!---->`);
      }
      if (safeHighlights.value.length) {
        _push(`<ul class="list-unstyled services-three__list" data-v-c6652724><!--[-->`);
        ssrRenderList(safeHighlights.value, (item, index) => {
          _push(`<li data-v-c6652724><div class="icon" data-v-c6652724><span class="icon-tick-inside-circle" data-v-c6652724></span></div><div class="text" data-v-c6652724><p data-v-c6652724>${ssrInterpolate(item)}</p></div></li>`);
        });
        _push(`<!--]--></ul>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(unref(Link), {
        href: __props.link,
        class: "services-three__btn"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(__props.buttonLabel)} <span class="${ssrRenderClass(`icon-${__props.isRtl ? "left" : "right"}-arrow-1`)}" data-v-c6652724${_scopeId}></span>`);
          } else {
            return [
              createTextVNode(toDisplayString(__props.buttonLabel) + " ", 1),
              createVNode("span", {
                class: `icon-${__props.isRtl ? "left" : "right"}-arrow-1`
              }, null, 2)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$k = _sfc_main$k.setup;
_sfc_main$k.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Services/ServiceCardThree.vue");
  return _sfc_setup$k ? _sfc_setup$k(props, ctx) : void 0;
};
const ServiceCardThree = /* @__PURE__ */ _export_sfc(_sfc_main$k, [["__scopeId", "data-v-c6652724"]]);
const _sfc_main$j = {
  __name: "MainMenuList",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const locale = computed(() => page.props.locale);
    const headerPages = computed(() => page.props.headerPages || []);
    const auth = computed(() => page.props.auth);
    const asset_path = computed(() => page.props.asset_path || "");
    const normalizePath = (path) => {
      if (!path) return "";
      const withoutQuery = path.split("?")[0];
      if (withoutQuery === "/") return "/";
      return withoutQuery.replace(/\/+$/, "");
    };
    const getPathFromUrl = (url) => {
      if (!url) return "";
      try {
        return new URL(url, window.location.origin).pathname;
      } catch (e) {
        return url;
      }
    };
    const expandPrefixes = (prefixes = []) => {
      const localePrefix = locale.value ? `/${locale.value}` : "";
      return prefixes.flatMap((prefix) => {
        const normalized = prefix.startsWith("/") ? prefix : `/${prefix}`;
        if (!localePrefix) {
          return [normalized];
        }
        return [normalized, `${localePrefix}${normalized}`];
      });
    };
    const isActive = (routeName, options = {}) => {
      const routeNames = Array.isArray(routeName) ? routeName : [routeName];
      const prefixes = expandPrefixes(options.prefixes || []);
      try {
        if (routeNames.some((name) => route().current(name))) {
          return true;
        }
      } catch (e) {
      }
      if (!prefixes.length) {
        return false;
      }
      const currentPath = normalizePath(page.url);
      return prefixes.some((prefix) => {
        const normalized = normalizePath(prefix);
        return currentPath === normalized || currentPath.startsWith(`${normalized}/`);
      });
    };
    const isCurrentUrl = (targetUrl) => {
      const targetPath = normalizePath(getPathFromUrl(targetUrl));
      const currentPath = normalizePath(page.url);
      return currentPath === targetPath;
    };
    const isPageActive = (pageItem) => {
      if (!pageItem || !pageItem.slug) return false;
      try {
        return isCurrentUrl(route("page.view", pageItem.slug));
      } catch (e) {
        return false;
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a;
      _push(`<ul${ssrRenderAttrs(mergeProps({ class: "main-menu__list" }, _attrs))}><li class="${ssrRenderClass({ current: isActive("home") })}">`);
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
      _push(`</li><li class="${ssrRenderClass({ current: isActive(["services.index", "services.show"], { prefixes: ["/services", "/service"] }) })}">`);
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
      _push(`</li><li class="${ssrRenderClass({ current: isActive(["blogs.index", "blogs.show"], { prefixes: ["/blogs", "/blog"] }) })}">`);
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
      _push(`</li><li class="${ssrRenderClass([[
        headerPages.value.length === 0 ? "d-none" : "",
        { current: isActive("page.view", { prefixes: ["/p"] }) }
      ], "dropdown"])}">`);
      _push(ssrRenderComponent(unref(Link), {
        href: headerPages.value.length ? _ctx.route("page.view", headerPages.value[0].slug) : "#"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("Pages"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("Pages")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<ul class="shadow-box"><!--[-->`);
      ssrRenderList(headerPages.value, (page2) => {
        _push(`<li class="${ssrRenderClass({ current: isPageActive(page2) })}">`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("page.view", page2.slug)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`${ssrInterpolate(page2.title[locale.value])}`);
            } else {
              return [
                createTextVNode(toDisplayString(page2.title[locale.value]), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></li><li class="${ssrRenderClass({ current: isActive("contact-us") })}">`);
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
      _push(`</li>`);
      if (((_a = auth.value) == null ? void 0 : _a.type) === "admin") {
        _push(`<li class="${ssrRenderClass({ active: isActive("admin.dashboard.index") })}"><a${ssrRenderAttr("href", _ctx.route("admin.dashboard.index"))}>${ssrInterpolate(trans("Dashboard"))}</a></li>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<li class="dropdown"><a href="#" aria-label="Change language"><img${ssrRenderAttr("src", asset_path.value + `images/langs/${locale.value}.png`)} width="20"${ssrRenderAttr("alt", trans("Current language"))}></a><ul class="shadow-box"><li><a href="#" class="${ssrRenderClass({ active: locale.value === "ar" })}"><img class="me-1"${ssrRenderAttr("src", asset_path.value + "images/langs/ar.png")} width="20"${ssrRenderAttr("alt", trans("Arabic"))}> ${ssrInterpolate(trans("Arabic"))}</a></li><li><a href="#" class="${ssrRenderClass({ active: locale.value === "en" })}"><img class="me-1"${ssrRenderAttr("src", asset_path.value + "images/langs/en.png")} width="20"${ssrRenderAttr("alt", trans("English"))}> ${ssrInterpolate(trans("English"))}</a></li><li><a href="#" class="${ssrRenderClass({ active: locale.value === "tr" })}"><img class="me-1"${ssrRenderAttr("src", asset_path.value + "images/langs/tr.png")} width="20"${ssrRenderAttr("alt", trans("Turkish"))}> ${ssrInterpolate(trans("Turkish"))}</a></li></ul></li></ul>`);
    };
  }
};
const _sfc_setup$j = _sfc_main$j.setup;
_sfc_main$j.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/MainMenuList.vue");
  return _sfc_setup$j ? _sfc_setup$j(props, ctx) : void 0;
};
const _sfc_main$i = {
  __name: "MainMenuNav",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const settings2 = computed(() => page.props.settings);
    const storage_path = computed(() => page.props.storage_path);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<nav${ssrRenderAttrs(mergeProps({ class: "main-menu main-menu-two" }, _attrs))}><div class="main-menu-two__wrapper"><div class="main-menu-two__wrapper-inner"><div class="main-menu-two__left"><div class="main-menu-two__logo">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings2.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings2.value.site_logo,
                alt: "logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="main-menu-two__main-menu-box"><a href="#" class="mobile-nav__toggler" aria-label="Open mobile menu"><i class="fa fa-bars"></i></a>`);
      _push(ssrRenderComponent(_sfc_main$j, null, null, _parent));
      _push(`</div><div class="main-menu-two__right d-none d-md-flex align-items-center"><div class="main-menu-two__search-box me-3"><a href="#" class="main-menu-two__search searcher-toggler-box icon-search-interface-symbol" aria-label="Open search"></a></div><div class="main-menu-two__nav-sidebar-icon"><a class="navSidebar-button" href="#" aria-label="Open sidebar"><span class="icon-dots-menu-one"></span><span class="icon-dots-menu-two"></span><span class="icon-dots-menu-three"></span></a></div></div></div></div></nav>`);
    };
  }
};
const _sfc_setup$i = _sfc_main$i.setup;
_sfc_main$i.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/MainMenuNav.vue");
  return _sfc_setup$i ? _sfc_setup$i(props, ctx) : void 0;
};
const _sfc_main$h = {
  __name: "App",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const settings2 = computed(() => page.props.settings);
    const storage_path = computed(() => page.props.storage_path);
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    const servicesList = computed(() => page.props.servicesList);
    const footerPages = computed(() => page.props.footerPages || []);
    const subscribeSuccess = ref(false);
    const subscribeForm = useForm({
      email: ""
    });
    const searchForm = useForm({
      search: ""
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
      const unregisterNavigate = router.on("navigate", (event) => {
        $(".mobile-nav__wrapper").removeClass("expanded");
        $("body").removeClass("locked");
        $("body").removeClass("search-active");
        $(".info-group").removeClass("isActive");
      });
      onUnmounted(() => {
        unregisterNavigate();
      });
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
    const getTranslatableTitle = (item) => {
      if (!item || !item.title) {
        return "";
      }
      if (typeof item.title === "string") {
        return item.title;
      }
      if (typeof item.title === "object") {
        return item.title[locale.value] || item.title["en"] || item.title[Object.keys(item.title)[0]] || "";
      }
      return "";
    };
    const getPageUrl = (pageItem) => {
      if (!pageItem || !pageItem.slug) {
        return "#";
      }
      try {
        return route("page.view", pageItem.slug);
      } catch (e) {
        return `#`;
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="xs-sidebar-group info-group info-sidebar"><div class="xs-overlay xs-bg-black"></div><div class="xs-sidebar-widget"><div class="sidebar-widget-container"><div class="widget-heading"><a href="#" class="close-side-widget">X</a></div><div class="sidebar-textwidget"><div class="sidebar-info-contents"><div class="content-inner"><div class="logo">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings2.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings2.value.site_logo,
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
        _push(`<span>${ssrInterpolate(trans("Submit"))}</span>`);
      }
      _push(`<span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"></span></button></div>`);
      if (contactSubmitSuccess.value) {
        _push(`<div class="alert alert-success mt-2">${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</form></div></div></div></div></div></div></div><div class="page-wrapper"><header class="main-header-two"><div class="main-menu-two__top"><div class="main-menu-two__top-inner"><p class="main-menu-two__top-text">${ssrInterpolate(trans("We Build Technology In Perfect Harmony"))}</p><ul class="list-unstyled main-menu-two__contact-list"><li><div class="icon"><i class="icon-pin"></i></div><div class="text"><p>${ssrInterpolate(settings2.value.address)}</p></div></li><li><div class="icon"><i class="icon-search-mail"></i></div><div class="text"><p><a dir="ltr"${ssrRenderAttr("href", `mailto::${settings2.value.email}`)}>${ssrInterpolate(settings2.value.email)}</a></p></div></li><li><div class="icon"><i class="icon-phone-call"></i></div><div class="text"><p><a dir="ltr"${ssrRenderAttr("href", `tel::${settings2.value.phone}`)}>${ssrInterpolate(settings2.value.phone)}</a></p></div></li></ul></div></div>`);
      _push(ssrRenderComponent(_sfc_main$i, null, null, _parent));
      _push(`</header><div class="stricky-header stricked-menu main-menu main-menu-two"><div class="sticky-header__content">`);
      _push(ssrRenderComponent(_sfc_main$i, null, null, _parent));
      _push(`</div></div>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`<section class="newsletter-two"><div class="newsletter-two__shape-1"><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/newsletter-two-shape-1.png")}${ssrRenderAttr("alt", trans("Newsletter decoration"))}></div><div class="newsletter-two__shape-2 float-bob-x"><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/newsletter-two-shape-2.png")}${ssrRenderAttr("alt", trans("Newsletter decoration"))}></div><div class="container"><div class="newsletter-two__inner"><div class="newsletter-two__left"><h2 class="newsletter-two__title">${ssrInterpolate(trans("Subscribe to Our Newsletter"))}</h2><p class="newsletter-two__text">${ssrInterpolate(trans("Engineering insights, product updates, and practical tech lessons—delivered occasionally, not daily"))}</p></div><div class="newsletter-two__right"><form class="newsletter-two__form"><div class="newsletter-two__input"><input type="email" name="email"${ssrRenderAttr("value", unref(subscribeForm).email)}${ssrRenderAttr("placeholder", trans("Enter your email address"))}${ssrIncludeBooleanAttr(unref(subscribeForm).processing) ? " disabled" : ""} required="">`);
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
      _push(`<span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"></span></button><div class="checked-box"><input type="checkbox" name="skipper1" id="skipper" checked=""><label for="skipper"><span></span>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("privacy-policy")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(trans("By subscribing, you accept our privacy policy"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(trans("By subscribing, you accept our privacy policy")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</label></div>`);
      if (subscribeSuccess.value) {
        _push(`<div class="alert alert-success mt-2">${ssrInterpolate(trans("Thank you for subscribing to our newsletter!"))}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</form></div></div></div></section><footer class="site-footer-two"><div class="site-footer-two__top"><div class="container"><div class="row"><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms"><div class="site-footer-two__about"><div class="site-footer-two__logo">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings2.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings2.value.site_logo,
                alt: "logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><ul class="list-unstyled site-footer-two__contact-list"><li><div class="site-footer-two__contact-icon"><span class="icon-contact"></span></div><div class="site-footer-two__contact-content"><p class="site-footer-two__contact-info"><a${ssrRenderAttr("href", `mailto:${settings2.value.email}`)} class="site-footer-two__contact-mail">${ssrInterpolate(settings2.value.email)}</a><a${ssrRenderAttr("href", `tel:${settings2.value.phone}`)} class="site-footer-two__contact-phone"><span dir="ltr">${ssrInterpolate(settings2.value.phone)}</span></a></p></div></li><li><div class="site-footer-two__contact-icon"><span class="icon-pin"></span></div><div class="site-footer-two__contact-content"><p class="site-footer-two__contact-info">${ssrInterpolate(settings2.value.address)}</p></div></li></ul></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="200ms"><div class="footer-widget-two__quick-links"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Pages"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("testimonials")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("Testimonials"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("Testimonials")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><!--[-->`);
      ssrRenderList(footerPages.value, (pageItem) => {
        _push(`<li>`);
        _push(ssrRenderComponent(unref(Link), {
          href: getPageUrl(pageItem)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(getTranslatableTitle(pageItem))}`);
            } else {
              return [
                createVNode("span", {
                  class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
                }, null, 2),
                createTextVNode(toDisplayString(getTranslatableTitle(pageItem)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]--></ul></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms"><div class="footer-widget-two__support"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Quick Links"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("Home"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("Home")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("about-us")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("About Us"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("About Us")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("services.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("Our Services"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("Our Services")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("blogs.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("Blogs"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("Blogs")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li><li>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("contact-us")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("Contact Us"))}`);
          } else {
            return [
              createVNode("span", {
                class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
              }, null, 2),
              createTextVNode(toDisplayString(trans("Contact Us")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li></ul></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="400ms"><div class="footer-widget-two__services"><h4 class="footer-widget-two__title">${ssrInterpolate(trans("Our Services"))}</h4><ul class="footer-widget-two__quick-links-list list-unstyled"><!--[-->`);
      ssrRenderList((servicesList.value || []).slice(0, 3), (service) => {
        _push(`<li>`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("services.index", { category: service.slug })
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(getTranslatableTitle(service))}`);
            } else {
              return [
                createVNode("span", {
                  class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
                }, null, 2),
                createTextVNode(toDisplayString(getTranslatableTitle(service)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</li>`);
      });
      _push(`<!--]-->`);
      if (!servicesList.value || servicesList.value.length === 0) {
        _push(`<li>`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("services.index")
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="${ssrRenderClass(locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2")}"${_scopeId}></span>${ssrInterpolate(trans("View All Services"))}`);
            } else {
              return [
                createVNode("span", {
                  class: locale.value === "ar" ? "icon-left-arrow-2" : "icon-right-arrow-2"
                }, null, 2),
                createTextVNode(toDisplayString(trans("View All Services")), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</li>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</ul></div></div></div></div></div><div class="site-footer-two__bottom"><div class="container"><div class="row"><div class="col-xl-12"><div class="site-footer-two__bottom-inner"><div class="site-footer-two__copyright"><p class="site-footer-two__copyright-text">${ssrInterpolate(trans("All rights are reserved"))} ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} © `);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(seo.value.website_name)}`);
          } else {
            return [
              createTextVNode(toDisplayString(seo.value.website_name), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</p></div><div class="site-footer-two__social-box"><h3 class="h4 site-footer-two__social-title">${ssrInterpolate(trans("Follow Us"))}:</h3><div class="site-footer-two__social-box-inner">`);
      if (settings2.value.whatsapp) {
        _push(`<a${ssrRenderAttr("href", settings2.value.whatsapp)} target="_blank" rel="noopener" aria-label="Whatsapp"><span class="icon-whatsapp"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.facebook) {
        _push(`<a${ssrRenderAttr("href", settings2.value.facebook)} target="_blank" rel="noopener" aria-label="Facebook"><span class="icon-facebook"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.instagram) {
        _push(`<a${ssrRenderAttr("href", settings2.value.instagram)} target="_blank" rel="noopener" aria-label="Instagram"><span class="fab fa-instagram"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.linkedin) {
        _push(`<a${ssrRenderAttr("href", settings2.value.linkedin)} target="_blank" rel="noopener" aria-label="LinkedIn"><span class="icon-linkedin"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.github) {
        _push(`<a${ssrRenderAttr("href", settings2.value.github)} target="_blank" rel="noopener" aria-label="GitHub"><span class="fab fa-github"></span></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div></div></div></div></footer></div><div class="mobile-nav__wrapper"><div class="mobile-nav__overlay mobile-nav__toggler"></div><div class="mobile-nav__content"><span class="mobile-nav__close mobile-nav__toggler"><i class="fa fa-times"></i></span><div class="logo-box">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("home"),
        "aria-label": "logo image"
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<img${ssrRenderAttr("src", storage_path.value + settings2.value.site_logo)} alt="logo"${_scopeId}>`);
          } else {
            return [
              createVNode("img", {
                src: storage_path.value + settings2.value.site_logo,
                alt: "logo"
              }, null, 8, ["src"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="mobile-nav__container">`);
      _push(ssrRenderComponent(_sfc_main$j, null, null, _parent));
      _push(`</div><ul class="mobile-nav__contact list-unstyled"><li><i class="fa fa-envelope"></i><a${ssrRenderAttr("href", `mailto:${settings2.value.email}`)}>${ssrInterpolate(settings2.value.email)}</a></li><li><i class="fas fa-phone"></i><a${ssrRenderAttr("href", `tel:${settings2.value.phone}`)}>${ssrInterpolate(settings2.value.phone)}</a></li></ul><div class="mobile-nav__top"><div class="mobile-nav__social">`);
      if (settings2.value.twitter) {
        _push(`<a${ssrRenderAttr("href", settings2.value.twitter)} class="fab fa-twitter me-2" target="_blank" rel="noopener" aria-label="Twitter"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.whatsapp) {
        _push(`<a${ssrRenderAttr("href", settings2.value.whatsapp)} class="fab fa-whatsapp me-2" target="_blank" rel="noopener" aria-label="Whatsapp"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.facebook) {
        _push(`<a${ssrRenderAttr("href", settings2.value.facebook)} class="fab fa-facebook me-2" target="_blank" rel="noopener" aria-label="Facebook"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.instagram) {
        _push(`<a${ssrRenderAttr("href", settings2.value.instagram)} class="fab fa-instagram me-2" target="_blank" rel="noopener" aria-label="Instagram"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.linkedin) {
        _push(`<a${ssrRenderAttr("href", settings2.value.linkedin)} class="fab fa-linkedin me-2" target="_blank" rel="noopener" aria-label="Linkedin"></a>`);
      } else {
        _push(`<!---->`);
      }
      if (settings2.value.github) {
        _push(`<a${ssrRenderAttr("href", settings2.value.github)} class="fab fa-github me-2" target="_blank" rel="noopener" aria-label="Github"></a>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></div><div class="search-popup"><div class="color-layer"></div><button class="close-search"><span class="far fa-times fa-fw"></span></button><form><div class="form-group"><input type="search" name="search"${ssrRenderAttr("value", unref(searchForm).search)}${ssrRenderAttr("placeholder", trans("Search Here"))} required=""><button type="submit"><i class="fas fa-search"></i></button></div></form></div><!--]-->`);
    };
  }
};
const _sfc_setup$h = _sfc_main$h.setup;
_sfc_main$h.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/App.vue");
  return _sfc_setup$h ? _sfc_setup$h(props, ctx) : void 0;
};
const __default__$c = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$g = /* @__PURE__ */ Object.assign(__default__$c, {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const posts = computed(() => page.props.posts || []);
    const servicesCategories = computed(() => page.props.servicesCategories || []);
    const testimonials = computed(() => page.props.testimonials || []);
    const teams = computed(() => page.props.teams || []);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return meta.value.title || `${trans("Home")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Empowering businesses with modern web, mobile, AI, and cloud solutions.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("IT solutions, web development, mobile apps, AI automation, cloud services") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
    const featuredPost = computed(() => {
      var _a;
      return ((_a = posts.value) == null ? void 0 : _a[0]) || null;
    });
    const sidePosts = computed(() => (posts.value || []).slice(1, 5));
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
    const getCategoryHighlights = (category) => {
      if (!category || !Array.isArray(category.services)) {
        return [];
      }
      return category.services.map((service) => translateField(service.title)).filter(Boolean);
    };
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
      let contactUrl = route("contact-us.store");
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
    onMounted(() => {
      nextTick(() => {
        const isRTL = page.props.locale === "ar";
        if (typeof $ !== "undefined" && $(".services-three__carousel").length) {
          $(".services-three__carousel").owlCarousel({
            loop: servicesCategories.value.length > 3,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: isRTL,
            responsive: {
              0: { items: 1 },
              768: { items: 2 },
              992: { items: 3 },
              1200: { items: 3 }
            }
          }).on("initialized.owl.carousel refreshed.owl.carousel", function() {
            $(this).find(".owl-dot").each(function(index) {
              $(this).attr("aria-label", `Go to slide ${index + 1}`);
            });
          });
        }
        if (typeof $ !== "undefined" && $(".team-two__carousel").length && teams.value.length > 0) {
          $(".team-two__carousel").owlCarousel({
            loop: teams.value.length > 3,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: isRTL,
            responsive: {
              0: { items: 1 },
              768: { items: 2 },
              992: { items: 2 },
              1200: { items: 3 }
            }
          }).on("initialized.owl.carousel refreshed.owl.carousel", function() {
            $(this).find(".owl-dot").each(function(index) {
              $(this).attr("aria-label", `Go to slide ${index + 1}`);
            });
          });
        }
        if (typeof $ !== "undefined" && $(".testimonial-two__carousel").length) {
          $(".testimonial-two__carousel").owlCarousel({
            loop: testimonials.value.length > 1,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: isRTL,
            responsive: {
              0: { items: 1 },
              768: { items: 1 },
              992: { items: 1 },
              1200: { items: 1 }
            }
          }).on("initialized.owl.carousel refreshed.owl.carousel", function() {
            $(this).find(".owl-dot").each(function(index) {
              $(this).attr("aria-label", `Go to slide ${index + 1}`);
            });
          });
        }
        if (typeof WOW !== "undefined") {
          new WOW().init();
        }
        if (typeof gsap !== "undefined" && typeof SplitText !== "undefined") {
          const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation");
          if (titleAnimations.length) {
            titleAnimations.forEach((quote) => {
              if (quote.animation) {
                quote.animation.progress(1).kill();
                if (quote.split) quote.split.revert();
              }
              if (isRTL) {
                const text2 = quote.textContent;
                if (/[\u0600-\u06FF]/.test(text2)) {
                  quote.style.textTransform = "none";
                  quote.style.fontVariant = "normal";
                }
              }
              var getclass = quote.closest(".sec-title-animation").className;
              var animation = getclass.split("animation-");
              if (animation[1] == "style4") return;
              const text = quote.textContent;
              const hasArabic = /[\u0600-\u06FF]/.test(text);
              if (hasArabic) {
                gsap.set(quote, {
                  perspective: 400,
                  opacity: 0,
                  y: isRTL ? -50 : 50
                });
                quote.animation = gsap.to(quote, {
                  scrollTrigger: {
                    trigger: quote,
                    start: "top 90%",
                    toggleActions: "play none none none"
                  },
                  y: 0,
                  opacity: 1,
                  duration: 1,
                  ease: Back.easeOut
                });
              } else {
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
                    opacity: 0
                  });
                }
                quote.animation = gsap.to(quote.split.chars, {
                  scrollTrigger: {
                    trigger: quote,
                    start: "top 90%",
                    toggleActions: "play none none none"
                  },
                  x: "0",
                  y: "0",
                  rotateX: "0",
                  opacity: 1,
                  duration: 1,
                  ease: Back.easeOut,
                  stagger: 0.02
                });
              }
            });
          }
        }
        setTimeout(() => {
          if (typeof ScrollTrigger !== "undefined") {
            ScrollTrigger.refresh();
          }
        }, 300);
        if (typeof $ !== "undefined" && $(".marquee_mode-2").length && $.fn.marquee) {
          $(".marquee_mode-2").marquee({
            speed: 50,
            gap: 50,
            delayBeforeStart: 0,
            direction: isRTL ? "right" : "left",
            duplicated: true,
            pauseOnHover: true,
            startVisible: true
          });
        }
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
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="banner-one"${_scopeId}><div class="banner-one__bg" style="${ssrRenderStyle({
              backgroundImage: `url(${asset_path.value}images/home/banner-bg.jpg)`
            })}"${_scopeId}></div><div class="banner-one__shape-bg float-bob-y" style="${ssrRenderStyle({
              backgroundImage: `url(${asset_path.value}images/shapes/banner-one-shape-bg.png)`
            })}"${_scopeId}></div><div class="container"${_scopeId}><div class="banner-one__inner"${_scopeId}><h1 class="banner-one__title px-3"${_scopeId}>${ssrInterpolate(trans("Crafting Intelligent Technologies for the Future"))} <br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Balanced, modern, includes web, mobile, AI, and cloud"))}</span></h1><div class="banner-one__btn-box mb-5"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("contact-us"),
              class: "thm-btn"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Get Started"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"${_scopeId2}></span>`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Get Started")) + " ", 1),
                    createVNode("span", {
                      class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                    }, null, 2)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></section><section class="about-three"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="${ssrRenderClass(`about-three__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="about-three__img-box"${_scopeId}><div class="about-three__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/about_us.jpg")}${ssrRenderAttr("alt", trans("About us"))}${_scopeId}></div></div></div></div><div class="col-xl-6"${_scopeId}><div class="about-three__right"${_scopeId}><p class="about-three__text"${_scopeId}>${ssrInterpolate(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape."))}</p><ul class="about-three__points list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="content"${_scopeId}><h2 class="h3"${_scopeId}>${ssrInterpolate(trans("Innovative IT Solutions Expert"))}</h2><p${_scopeId}>${ssrInterpolate(trans("Support & Consulting"))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="content"${_scopeId}><h2 class="h3"${_scopeId}>${ssrInterpolate(trans("Cloud Solutions for Modern"))}</h2><p${_scopeId}>${ssrInterpolate(trans("Enterprises"))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><div class="content"${_scopeId}><h2 class="h3"${_scopeId}>${ssrInterpolate(trans("Seamless Digital Transformation"))}</h2><p${_scopeId}>${ssrInterpolate(trans("AI-Driven Business Automation"))}</p></div></li></ul><div class="about-three__btn-and-call-box"${_scopeId}><div class="about-three__btn-box"${_scopeId}><a${ssrRenderAttr("href", _ctx.route("about-us"))} class="thm-btn"${_scopeId}>${ssrInterpolate(trans("Get in Touch"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"${_scopeId}></span></a></div><div class="about-three__call-box"${_scopeId}><div class="icon"${_scopeId}><span class="icon-customer-service-headset"${_scopeId}></span></div><div class="content"${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Call Any Time"))}</span><p${_scopeId}><a dir="ltr" href="tel:{{settings.phone}}"${_scopeId}>${ssrInterpolate(settings2.value.phone)}</a></p></div></div></div></div></div></div></div></section>`);
            if (servicesCategories.value && servicesCategories.value.length) {
              _push2(`<section class="services-three"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Our Services"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation core-services-title"${_scopeId}>${ssrInterpolate(trans("What We Do"))}! <span${_scopeId}>${ssrInterpolate(trans("Core Services"))}</span></h2></div><div class="services-three__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(servicesCategories.value, (servicesCategory) => {
                _push2(`<div class="item"${_scopeId}>`);
                _push2(ssrRenderComponent(ServiceCardThree, {
                  title: translateField(servicesCategory.title),
                  description: translateField(servicesCategory.description),
                  highlights: getCategoryHighlights(servicesCategory),
                  link: _ctx.route("services.index", { category: servicesCategory.slug }),
                  image: servicesCategory.image_link,
                  "button-label": trans("Read More"),
                  "is-rtl": locale.value === "ar"
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              });
              _push2(`<!--]--></div><div class="text-center mt-4"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("services.index"),
                class: "thm-btn"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(trans("View All Services"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(trans("View All Services")) + " ", 1),
                      createVNode("span", {
                        class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                      }, null, 2)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="why-choose-two"${_scopeId}><div class="why-choose-two__shape-1 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/why-choose-two-shape-1.png")}${ssrRenderAttr("alt", trans("Decorative shape"))}${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="${ssrRenderClass(`why-choose-two__left wow ${locale.value !== "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="why-choose-two__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/why_choose_us.jpg")} alt="why_choose_us"${_scopeId}></div></div></div><div class="col-xl-6"${_scopeId}><div class="why-choose-two__right"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Why Choose Us"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Your Business with"))} `);
            if (locale.value === "ar") {
              _push2(`<span${_scopeId}>${ssrInterpolate(trans("IT Solutions"))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<span${_scopeId}>${ssrInterpolate(trans("Reliable &"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Future-Ready"))}</span><br${_scopeId}>`);
            if (locale.value !== "ar") {
              _push2(`<span${_scopeId}>${ssrInterpolate(trans("IT Solutions"))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</h2></div><p class="why-choose-one__text"${_scopeId}>${ssrInterpolate(trans("We deliver exceptional products and services that consistently exceed expectations. Backed by years of experience and a proven track record, we are your reliable partner for success."))}</p><ul class="list-unstyled why-choose-two__points"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-quality"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("Unmatched Quality"))}</h3><p${_scopeId}>${ssrInterpolate(trans("We deliver exceptional products and services that exceed expectations every time."))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-team"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("Trusted Expertise"))}</h3><p${_scopeId}>${ssrInterpolate(trans("Backed by years of experience and a proven track record, we are your reliable partner for success."))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-customer-centricity"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("User-Centric Approach"))}</h3><p${_scopeId}>${ssrInterpolate(trans("Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first."))}</p></div></li></ul></div></div></div></div></section>`);
            if (teams.value && teams.value.length > 0) {
              _push2(`<section class="team-two d-none"${_scopeId}><div class="team-two__bg-shape float-bob-y" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/shapes/team-two-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-5"${_scopeId}><div class="team-two__left"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Our Members"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Meet Our Team."))} <span${_scopeId}>${ssrInterpolate(trans("Get to"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Know the Talented"))}</span> ${ssrInterpolate(trans("Minds Behind Our Team"))}</h2></div><p class="team-two__text"${_scopeId}>${ssrInterpolate(trans("Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way."))}</p></div></div><div class="col-xl-7"${_scopeId}><div class="team-two__right"${_scopeId}><div class="team-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(teams.value, (team) => {
                _push2(`<div class="item"${_scopeId}><div class="team-two__single"${_scopeId}><div class="team-two__img-box"${_scopeId}><div class="team-two__img"${_scopeId}><img${ssrRenderAttr("src", team.avatar_link)}${ssrRenderAttr("alt", translateField(team.name))}${_scopeId}></div><div class="team-two__social"${_scopeId}>`);
                if (team.facebook) {
                  _push2(`<a${ssrRenderAttr("href", team.facebook)} target="_blank"${_scopeId}><span class="icon-facebook"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.behance) {
                  _push2(`<a${ssrRenderAttr("href", team.behance)} target="_blank"${_scopeId}><span class="icon-dribble"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.linked_in) {
                  _push2(`<a${ssrRenderAttr("href", team.linked_in)} target="_blank"${_scopeId}><span class="icon-linkedin"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.github) {
                  _push2(`<a${ssrRenderAttr("href", team.github)} target="_blank"${_scopeId}><span class="icon-github"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div><div class="team-two__title-box"${_scopeId}><h3${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(team.name))}</a></h3><p${_scopeId}>${ssrInterpolate(translateField(team.position))}</p></div></div></div></div>`);
              });
              _push2(`<!--]--></div></div></div></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="feature-one"${_scopeId}><div class="feature-one__shape-2 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/feature-one-shape-2.png")}${ssrRenderAttr("alt", trans("Decorative shape"))}${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/website.png")}${ssrRenderAttr("alt", trans("Web Development"))}${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Web Development"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Custom web solutions built with cutting-edge technology to drive your business forward."))}</p></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/app-development.png")}${ssrRenderAttr("alt", trans("Mobile Development"))}${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Mobile Development"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Native and cross-platform mobile applications that deliver exceptional user experiences."))}</p></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="500ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/microchip.png")}${ssrRenderAttr("alt", trans("AI Agents & Automation"))}${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("AI Agents & Automation"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Intelligent automation solutions powered by AI to streamline your business processes."))}</p></div></div><div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="700ms"${_scopeId}><div class="feature-one__single"${_scopeId}><div class="feature-one__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/home/cloud.png")}${ssrRenderAttr("alt", trans("Cloud & Infrastructure"))}${_scopeId}></div><h3 class="feature-one__title"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(trans("Cloud & Infrastructure"))}</a></h3><p class="feature-one__text"${_scopeId}>${ssrInterpolate(trans("Secure, scalable, and efficient cloud services to power your growth and digital transformation."))}</p></div></div></div></div></section><section class="cta-one"${_scopeId}><div class="cta-one__shape-bg float-bob-y" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}site/images/shapes/cta-one-shape-bg.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="cta-one__inner"${_scopeId}><h3 class="cta-one__title"${_scopeId}>${ssrInterpolate(trans("To make requests for further information, contact us"))}</h3><div class="cta-one__contact-info"${_scopeId}><div class="cta-one__contact-icon"${_scopeId}><span class="icon-customer-service-headset"${_scopeId}></span></div><div class="cta-one__contact-details"${_scopeId}><p${_scopeId}>${ssrInterpolate(trans("Call Us For Any inquiry"))}</p><h4${_scopeId}><a dir="ltr" href="tel:{{settings.phone}}"${_scopeId}>${ssrInterpolate(settings2.value.phone)}</a></h4></div></div></div></div></section>`);
            if (testimonials.value && testimonials.value.length) {
              _push2(`<section class="testimonial-one pb-5"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Customer Experiences"))} <br${_scopeId}> ${ssrInterpolate(trans("That"))} <span${_scopeId}>${ssrInterpolate(trans("Speak Volumes"))}</span></h2></div><div class="testimonial-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(testimonials.value, (testimonial) => {
                _push2(`<div class="item"${_scopeId}><div class="testimonial-two__single"${_scopeId}><div class="testimonial-two__single-inner"${_scopeId}><div class="testimonial-two__star"${_scopeId}><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span></div><p class="testimonial-two__text"${_scopeId}>${ssrInterpolate(translateField(testimonial.quote))}</p></div><div class="testimonial-two__client-info"${_scopeId}><div class="testimonial-two__client-img"${_scopeId}><img${ssrRenderAttr("src", testimonial.avatar_link)}${ssrRenderAttr("alt", translateField(testimonial.name))}${_scopeId}></div><div class="testimonial-two__client-content"${_scopeId}><h3 class="h4 testimonial-two__client-name"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(testimonial.name))}</a></h3><p class="testimonial-two__sub-title"${_scopeId}>${ssrInterpolate(translateField(testimonial.position))}</p></div></div><div class="testimonial-two__quote"${_scopeId}><span class="icon-right-quote"${_scopeId}></span></div></div></div>`);
              });
              _push2(`<!--]--></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (posts.value && posts.value.length) {
              _push2(`<section class="blog-two blog-three"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="${ssrRenderClass(`blog-two__left wow fadeIn${locale.value !== "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms"${_scopeId}><div class="section-title text-left sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Our Blogs"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Explore Our Latest Blogs for Expert Insights"))}</h2></div><p class="blog-two-text"${_scopeId}>${ssrInterpolate(trans("Dive into our collection of blogs where we share expert insights, helpful tips, and the latest trends in the industry"))}</p><div class="blog-two__top-btn-box"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.index"),
                class: "thm-btn"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(trans("View All Blogs"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(trans("View All Blogs")) + " ", 1),
                      createVNode("span", {
                        class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                      }, null, 2)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
              if (featuredPost.value) {
                _push2(`<div class="blog-two__left-content-box d-none d-md-block"${_scopeId}>`);
                _push2(ssrRenderComponent(_sfc_main$l, {
                  post: featuredPost.value,
                  variant: "featured",
                  locale: locale.value,
                  "asset-path": asset_path.value,
                  "image-fallback-index": 1
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="col-xl-6"${_scopeId}><div class="blog-two__right"${_scopeId}><!--[-->`);
              ssrRenderList(sidePosts.value, (post, idx) => {
                _push2(ssrRenderComponent(_sfc_main$l, {
                  key: post.id || idx,
                  post,
                  variant: "compact",
                  locale: locale.value,
                  "asset-path": asset_path.value,
                  "image-fallback-index": idx + 2,
                  "animation-class": idx % 2 === 0 ? "fadeInLeft" : "fadeInRight",
                  "animation-delay": `${(idx + 1) * 100}ms`
                }, null, _parent2, _scopeId));
              });
              _push2(`<!--]--></div></div></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="contact-two"${_scopeId}><div class="contact-two__sliding-text-list marquee_mode-2"${_scopeId}><div class="contact-two__sliding-text-item"${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></div><div class="contact-two__sliding-text-item"${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></div><div class="contact-two__sliding-text-item"${_scopeId}><h2 data-hover="Branding" class="contact-two__sliding-text-title"${_scopeId}>${ssrInterpolate(trans("GET IN TOUCH *"))}</h2></div></div><div class="contact-two__shape-1 float-bob-y"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "site/images/shapes/contact-two-shape-1.png")}${ssrRenderAttr("alt", trans("Decorative shape"))}${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="contact-two__left"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Get In Touch"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</h2></div><p class="contact-two__text"${_scopeId}>${ssrInterpolate(trans("Fill out the form below and we'll get back to you as soon as possible"))}</p><ul class="contact-two__contact-list list-unstyled"${_scopeId}>`);
            if (settings2.value.email) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-mail"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("Email"))}</h3><p${_scopeId}><a dir="ltr"${ssrRenderAttr("href", `mailto:${settings2.value.email}`)}${_scopeId}>${ssrInterpolate(settings2.value.email)}</a></p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            if (settings2.value.phone) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("Phone"))}</h3><p${_scopeId}><a dir="ltr"${ssrRenderAttr("href", `tel:${settings2.value.phone}`)}${_scopeId}>${ssrInterpolate(settings2.value.phone)}</a></p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            if (settings2.value.address) {
              _push2(`<li${_scopeId}><div class="icon"${_scopeId}><span class="icon-pin"${_scopeId}></span></div><div class="content"${_scopeId}><h3 class="h4"${_scopeId}>${ssrInterpolate(trans("Our Location"))}</h3><p${_scopeId}>${ssrInterpolate(settings2.value.address)}</p></div></li>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</ul></div></div><div class="col-xl-6"${_scopeId}><div class="${ssrRenderClass(`contact-two__right wow slideIn${locale.value === "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><form class="contact-form-validated contact-one__form"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6 col-lg-6"${_scopeId}><h3 class="h4 contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Full Name"))}</h3><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-user-1"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).name)} type="text" name="name"${ssrRenderAttr("placeholder", trans("Full Name"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.name })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.name) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.name)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-6 col-lg-6"${_scopeId}><h3 class="h4 contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Email"))}</h3><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-email"${_scopeId}></span></div><input type="email" name="email"${ssrRenderAttr("value", unref(contactForm).email)}${ssrRenderAttr("placeholder", trans("Email"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.email })}" required${_scopeId}>`);
            if (unref(contactForm).errors.email) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.email)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-6 col-lg-6"${_scopeId}><h3 class="h4 contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Phone Number"))}</h3><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).mobile)} type="text" name="mobile"${ssrRenderAttr("placeholder", trans("Phone Number"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.mobile })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.mobile) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.mobile)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-6 col-lg-6"${_scopeId}><h3 class="h4 contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Subject"))}</h3><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><input type="text" name="subject"${ssrRenderAttr("value", unref(contactForm).subject)}${ssrRenderAttr("placeholder", trans("Subject"))}${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass({ "error": unref(contactForm).errors.subject })}" required${_scopeId}>`);
            if (unref(contactForm).errors.subject) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.subject)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></div><div class="col-xl-12"${_scopeId}><h3 class="h4 contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Message"))}</h3><div class="contact-one__input-box text-message-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><textarea name="message"${ssrRenderAttr("placeholder", trans("Write your message"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.message })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}>${ssrInterpolate(unref(contactForm).message)}</textarea></div>`);
            if (unref(contactForm).errors.message) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.message)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="contact-one__btn-box"${_scopeId}><button type="submit"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": unref(contactForm).processing }, "thm-btn"])}"${_scopeId}>`);
            if (unref(contactForm).processing) {
              _push2(`<span${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2"${_scopeId}></i>${ssrInterpolate(trans("Sending..."))}</span>`);
            } else {
              _push2(`<span${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Submit"))}</span> <i class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow mx-1`)}"${_scopeId}></i></span>`);
            }
            _push2(`</button></div></div>`);
            if (contactSubmitSuccess.value) {
              _push2(`<div class="col-12 mt-3"${_scopeId}><div class="alert alert-success"${_scopeId}>${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</form><div class="result"${_scopeId}></div></div></div></div></div></section>`);
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
                    createVNode("h1", { class: "banner-one__title px-3" }, [
                      createTextVNode(toDisplayString(trans("Crafting Intelligent Technologies for the Future")) + " ", 1),
                      createVNode("br"),
                      createVNode("span", null, toDisplayString(trans("Balanced, modern, includes web, mobile, AI, and cloud")), 1)
                    ]),
                    createVNode("div", { class: "banner-one__btn-box mb-5" }, [
                      createVNode(unref(Link), {
                        href: _ctx.route("contact-us"),
                        class: "thm-btn"
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(trans("Get Started")) + " ", 1),
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                          }, null, 2)
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
                        class: `about-three__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "about-three__img-box" }, [
                          createVNode("div", { class: "about-three__img" }, [
                            createVNode("img", {
                              src: asset_path.value + "images/home/about_us.jpg",
                              alt: trans("About us")
                            }, null, 8, ["src", "alt"])
                          ])
                        ])
                      ], 2)
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "about-three__right" }, [
                        createVNode("p", { class: "about-three__text" }, toDisplayString(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape.")), 1),
                        createVNode("ul", { class: "about-three__points list-unstyled" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-tick-inside-circle" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h2", { class: "h3" }, toDisplayString(trans("Innovative IT Solutions Expert")), 1),
                              createVNode("p", null, toDisplayString(trans("Support & Consulting")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-tick-inside-circle" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h2", { class: "h3" }, toDisplayString(trans("Cloud Solutions for Modern")), 1),
                              createVNode("p", null, toDisplayString(trans("Enterprises")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-tick-inside-circle" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h2", { class: "h3" }, toDisplayString(trans("Seamless Digital Transformation")), 1),
                              createVNode("p", null, toDisplayString(trans("AI-Driven Business Automation")), 1)
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
                              createVNode("span", {
                                class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                              }, null, 2)
                            ], 8, ["href"])
                          ]),
                          createVNode("div", { class: "about-three__call-box" }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-customer-service-headset" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("span", null, toDisplayString(trans("Call Any Time")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: "tel:{{settings.phone}}"
                                }, toDisplayString(settings2.value.phone), 1)
                              ])
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              servicesCategories.value && servicesCategories.value.length ? (openBlock(), createBlock("section", {
                key: 0,
                class: "services-three"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Services")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation core-services-title" }, [
                      createTextVNode(toDisplayString(trans("What We Do")) + "! ", 1),
                      createVNode("span", null, toDisplayString(trans("Core Services")), 1)
                    ])
                  ]),
                  createVNode("div", { class: "services-three__carousel owl-theme owl-carousel" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(servicesCategories.value, (servicesCategory) => {
                      return openBlock(), createBlock("div", {
                        key: servicesCategory.id,
                        class: "item"
                      }, [
                        createVNode(ServiceCardThree, {
                          title: translateField(servicesCategory.title),
                          description: translateField(servicesCategory.description),
                          highlights: getCategoryHighlights(servicesCategory),
                          link: _ctx.route("services.index", { category: servicesCategory.slug }),
                          image: servicesCategory.image_link,
                          "button-label": trans("Read More"),
                          "is-rtl": locale.value === "ar"
                        }, null, 8, ["title", "description", "highlights", "link", "image", "button-label", "is-rtl"])
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
                        createVNode("span", {
                          class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                        }, null, 2)
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
                    alt: trans("Decorative shape")
                  }, null, 8, ["src", "alt"])
                ]),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: `why-choose-two__left wow ${locale.value !== "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "why-choose-two__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "images/home/why_choose_us.jpg",
                            alt: "why_choose_us"
                          }, null, 8, ["src"])
                        ])
                      ], 2)
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
                            createTextVNode(toDisplayString(trans("Your Business with")) + " ", 1),
                            locale.value === "ar" ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(trans("IT Solutions")), 1)) : createCommentVNode("", true),
                            createVNode("span", null, toDisplayString(trans("Reliable &")), 1),
                            createVNode("br"),
                            createVNode("span", null, toDisplayString(trans("Future-Ready")), 1),
                            createVNode("br"),
                            locale.value !== "ar" ? (openBlock(), createBlock("span", { key: 1 }, toDisplayString(trans("IT Solutions")), 1)) : createCommentVNode("", true)
                          ])
                        ]),
                        createVNode("p", { class: "why-choose-one__text" }, toDisplayString(trans("We deliver exceptional products and services that consistently exceed expectations. Backed by years of experience and a proven track record, we are your reliable partner for success.")), 1),
                        createVNode("ul", { class: "list-unstyled why-choose-two__points" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-quality" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("Unmatched Quality")), 1),
                              createVNode("p", null, toDisplayString(trans("We deliver exceptional products and services that exceed expectations every time.")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-team" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("Trusted Expertise")), 1),
                              createVNode("p", null, toDisplayString(trans("Backed by years of experience and a proven track record, we are your reliable partner for success.")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-customer-centricity" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("User-Centric Approach")), 1),
                              createVNode("p", null, toDisplayString(trans("Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first.")), 1)
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              teams.value && teams.value.length > 0 ? (openBlock(), createBlock("section", {
                key: 1,
                class: "team-two d-none"
              }, [
                createVNode("div", {
                  class: "team-two__bg-shape float-bob-y",
                  style: { backgroundImage: `url(${asset_path.value}images/shapes/team-two-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-5" }, [
                      createVNode("div", { class: "team-two__left" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Members")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, [
                            createTextVNode(toDisplayString(trans("Meet Our Team.")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("Get to")), 1),
                            createVNode("br"),
                            createVNode("span", null, toDisplayString(trans("Know the Talented")), 1),
                            createTextVNode(" " + toDisplayString(trans("Minds Behind Our Team")), 1)
                          ])
                        ]),
                        createVNode("p", { class: "team-two__text" }, toDisplayString(trans("Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way.")), 1)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-7" }, [
                      createVNode("div", { class: "team-two__right" }, [
                        createVNode("div", { class: "team-two__carousel owl-theme owl-carousel" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(teams.value, (team) => {
                            return openBlock(), createBlock("div", {
                              class: "item",
                              key: team.id
                            }, [
                              createVNode("div", { class: "team-two__single" }, [
                                createVNode("div", { class: "team-two__img-box" }, [
                                  createVNode("div", { class: "team-two__img" }, [
                                    createVNode("img", {
                                      src: team.avatar_link,
                                      alt: translateField(team.name)
                                    }, null, 8, ["src", "alt"])
                                  ]),
                                  createVNode("div", { class: "team-two__social" }, [
                                    team.facebook ? (openBlock(), createBlock("a", {
                                      key: 0,
                                      href: team.facebook,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-facebook" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.behance ? (openBlock(), createBlock("a", {
                                      key: 1,
                                      href: team.behance,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-dribble" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.linked_in ? (openBlock(), createBlock("a", {
                                      key: 2,
                                      href: team.linked_in,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-linkedin" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.github ? (openBlock(), createBlock("a", {
                                      key: 3,
                                      href: team.github,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-github" })
                                    ], 8, ["href"])) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "team-two__title-box" }, [
                                    createVNode("h3", null, [
                                      createVNode("a", { href: "#" }, toDisplayString(translateField(team.name)), 1)
                                    ]),
                                    createVNode("p", null, toDisplayString(translateField(team.position)), 1)
                                  ])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ])
                    ])
                  ])
                ])
              ])) : createCommentVNode("", true),
              createVNode("section", { class: "feature-one" }, [
                createVNode("div", { class: "feature-one__shape-2 float-bob-y" }, [
                  createVNode("img", {
                    src: asset_path.value + "site/images/shapes/feature-one-shape-2.png",
                    alt: trans("Decorative shape")
                  }, null, 8, ["src", "alt"])
                ]),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", {
                      class: "col-xl-3 col-lg-6 col-md-6 wow fadeInUp",
                      "data-wow-delay": "100ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "images/home/website.png",
                            alt: trans("Web Development")
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Web Development")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Custom web solutions built with cutting-edge technology to drive your business forward.")), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-3 col-lg-6 col-md-6 wow fadeInUp",
                      "data-wow-delay": "300ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "images/home/app-development.png",
                            alt: trans("Mobile Development")
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Mobile Development")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Native and cross-platform mobile applications that deliver exceptional user experiences.")), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-3 col-lg-6 col-md-6 wow fadeInUp",
                      "data-wow-delay": "500ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "images/home/microchip.png",
                            alt: trans("AI Agents & Automation")
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("AI Agents & Automation")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Intelligent automation solutions powered by AI to streamline your business processes.")), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-3 col-lg-6 col-md-6 wow fadeInUp",
                      "data-wow-delay": "700ms"
                    }, [
                      createVNode("div", { class: "feature-one__single" }, [
                        createVNode("div", { class: "feature-one__img" }, [
                          createVNode("img", {
                            src: asset_path.value + "images/home/cloud.png",
                            alt: trans("Cloud & Infrastructure")
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("h3", { class: "feature-one__title" }, [
                          createVNode("a", { href: "#" }, toDisplayString(trans("Cloud & Infrastructure")), 1)
                        ]),
                        createVNode("p", { class: "feature-one__text" }, toDisplayString(trans("Secure, scalable, and efficient cloud services to power your growth and digital transformation.")), 1)
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
                          createVNode("a", {
                            dir: "ltr",
                            href: "tel:{{settings.phone}}"
                          }, toDisplayString(settings2.value.phone), 1)
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              testimonials.value && testimonials.value.length ? (openBlock(), createBlock("section", {
                key: 2,
                class: "testimonial-one pb-5"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Testimonials")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, [
                      createTextVNode(toDisplayString(trans("Customer Experiences")) + " ", 1),
                      createVNode("br"),
                      createTextVNode(" " + toDisplayString(trans("That")) + " ", 1),
                      createVNode("span", null, toDisplayString(trans("Speak Volumes")), 1)
                    ])
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
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" })
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
                              createVNode("h3", { class: "h4 testimonial-two__client-name" }, [
                                createVNode("a", { href: "#" }, toDisplayString(translateField(testimonial.name)), 1)
                              ]),
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
                key: 3,
                class: "blog-two blog-three"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: `blog-two__left wow fadeIn${locale.value !== "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms"
                      }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style1" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Blogs")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, toDisplayString(trans("Explore Our Latest Blogs for Expert Insights")), 1)
                        ]),
                        createVNode("p", { class: "blog-two-text" }, toDisplayString(trans("Dive into our collection of blogs where we share expert insights, helpful tips, and the latest trends in the industry")), 1),
                        createVNode("div", { class: "blog-two__top-btn-box" }, [
                          createVNode(unref(Link), {
                            href: _ctx.route("blogs.index"),
                            class: "thm-btn"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(trans("View All Blogs")) + " ", 1),
                              createVNode("span", {
                                class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                              }, null, 2)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        featuredPost.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "blog-two__left-content-box d-none d-md-block"
                        }, [
                          createVNode(_sfc_main$l, {
                            post: featuredPost.value,
                            variant: "featured",
                            locale: locale.value,
                            "asset-path": asset_path.value,
                            "image-fallback-index": 1
                          }, null, 8, ["post", "locale", "asset-path"])
                        ])) : createCommentVNode("", true)
                      ], 2)
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "blog-two__right" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(sidePosts.value, (post, idx) => {
                          return openBlock(), createBlock(_sfc_main$l, {
                            key: post.id || idx,
                            post,
                            variant: "compact",
                            locale: locale.value,
                            "asset-path": asset_path.value,
                            "image-fallback-index": idx + 2,
                            "animation-class": idx % 2 === 0 ? "fadeInLeft" : "fadeInRight",
                            "animation-delay": `${(idx + 1) * 100}ms`
                          }, null, 8, ["post", "locale", "asset-path", "image-fallback-index", "animation-class", "animation-delay"]);
                        }), 128))
                      ])
                    ])
                  ])
                ])
              ])) : createCommentVNode("", true),
              createVNode("section", { class: "contact-two" }, [
                createVNode("div", { class: "contact-two__sliding-text-list marquee_mode-2" }, [
                  createVNode("div", { class: "contact-two__sliding-text-item" }, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ]),
                  createVNode("div", { class: "contact-two__sliding-text-item" }, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ]),
                  createVNode("div", { class: "contact-two__sliding-text-item" }, [
                    createVNode("h2", {
                      "data-hover": "Branding",
                      class: "contact-two__sliding-text-title"
                    }, toDisplayString(trans("GET IN TOUCH *")), 1)
                  ])
                ]),
                createVNode("div", { class: "contact-two__shape-1 float-bob-y" }, [
                  createVNode("img", {
                    src: asset_path.value + "site/images/shapes/contact-two-shape-1.png",
                    alt: trans("Decorative shape")
                  }, null, 8, ["src", "alt"])
                ]),
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
                          settings2.value.email ? (openBlock(), createBlock("li", { key: 0 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-mail" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("Email")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: `mailto:${settings2.value.email}`
                                }, toDisplayString(settings2.value.email), 9, ["href"])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          settings2.value.phone ? (openBlock(), createBlock("li", { key: 1 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-phone-call" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("Phone")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: `tel:${settings2.value.phone}`
                                }, toDisplayString(settings2.value.phone), 9, ["href"])
                              ])
                            ])
                          ])) : createCommentVNode("", true),
                          settings2.value.address ? (openBlock(), createBlock("li", { key: 2 }, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-pin" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h3", { class: "h4" }, toDisplayString(trans("Our Location")), 1),
                              createVNode("p", null, toDisplayString(settings2.value.address), 1)
                            ])
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: `contact-two__right wow slideIn${locale.value === "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("form", {
                          class: "contact-form-validated contact-one__form",
                          onSubmit: withModifiers(handleContactSubmit, ["prevent"])
                        }, [
                          createVNode("div", { class: "row" }, [
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h3", { class: "h4 contact-one__input-title" }, toDisplayString(trans("Full Name")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-user-1" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).name = $event,
                                  type: "text",
                                  name: "name",
                                  placeholder: trans("Full Name"),
                                  class: { "error": unref(contactForm).errors.name },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).name]
                                ])
                              ]),
                              unref(contactForm).errors.name ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.name), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h3", { class: "h4 contact-one__input-title" }, toDisplayString(trans("Email")), 1),
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
                              createVNode("h3", { class: "h4 contact-one__input-title" }, toDisplayString(trans("Phone Number")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-phone-call" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).mobile = $event,
                                  type: "text",
                                  name: "mobile",
                                  placeholder: trans("Phone Number"),
                                  class: { "error": unref(contactForm).errors.mobile },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).mobile]
                                ])
                              ]),
                              unref(contactForm).errors.mobile ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.mobile), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h3", { class: "h4 contact-one__input-title" }, toDisplayString(trans("Subject")), 1),
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
                            createVNode("h3", { class: "h4 contact-one__input-title" }, toDisplayString(trans("Message")), 1),
                            createVNode("div", { class: "contact-one__input-box text-message-box" }, [
                              createVNode("div", { class: "contact-one__input-icon" }, [
                                createVNode("span", { class: "icon-edit" })
                              ]),
                              withDirectives(createVNode("textarea", {
                                "onUpdate:modelValue": ($event) => unref(contactForm).message = $event,
                                name: "message",
                                placeholder: trans("Write your message"),
                                class: { "error": unref(contactForm).errors.message },
                                disabled: unref(contactForm).processing,
                                required: ""
                              }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                [vModelText, unref(contactForm).message]
                              ])
                            ]),
                            unref(contactForm).errors.message ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString(unref(contactForm).errors.message), 1)) : createCommentVNode("", true),
                            createVNode("div", { class: "contact-one__btn-box" }, [
                              createVNode("button", {
                                type: "submit",
                                class: ["thm-btn", { "opacity-50": unref(contactForm).processing }],
                                disabled: unref(contactForm).processing
                              }, [
                                unref(contactForm).processing ? (openBlock(), createBlock("span", { key: 0 }, [
                                  createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                                  createTextVNode(toDisplayString(trans("Sending...")), 1)
                                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createVNode("span", null, toDisplayString(trans("Submit")), 1),
                                  createTextVNode(),
                                  createVNode("i", {
                                    class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow mx-1`
                                  }, null, 2)
                                ]))
                              ], 10, ["disabled"])
                            ])
                          ]),
                          contactSubmitSuccess.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "col-12 mt-3"
                          }, [
                            createVNode("div", { class: "alert alert-success" }, toDisplayString(trans("Thank you for contacting us! We will get back to you soon.")), 1)
                          ])) : createCommentVNode("", true)
                        ], 32),
                        createVNode("div", { class: "result" })
                      ], 2)
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
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Base/resources/assets/js/Pages/Index.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$g
}, Symbol.toStringTag, { value: "Module" }));
const __default__$b = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$f = /* @__PURE__ */ Object.assign(__default__$b, {
  __name: "AboutUs",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const teams = computed(() => page.props.teams || []);
    const testimonials = computed(() => page.props.testimonials || []);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return meta.value.title || `${trans("About Us")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Learn about our team, mission, and the technology expertise behind our solutions.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("about us, IT consulting, technology experts, digital transformation") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
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
    onMounted(() => {
      nextTick(() => {
        if (typeof $ !== "undefined" && $(".team-two__carousel").length && teams.value.length > 0) {
          $(".team-two__carousel").owlCarousel({
            loop: teams.value.length > 3,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: locale.value === "ar",
            responsive: {
              0: { items: 1 },
              768: { items: 2 },
              992: { items: 2 },
              1200: { items: 3 }
            }
          }).on("initialized.owl.carousel refreshed.owl.carousel", function() {
            $(this).find(".owl-dot").each(function(index) {
              $(this).attr("aria-label", `Go to slide ${index + 1}`);
            });
          });
        }
        if (typeof $ !== "undefined" && $(".testimonial-two__carousel").length && testimonials.value.length > 0) {
          $(".testimonial-two__carousel").owlCarousel({
            loop: testimonials.value.length > 1,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: locale.value === "ar",
            responsive: {
              0: { items: 1 },
              768: { items: 1 },
              992: { items: 1 },
              1200: { items: 1 }
            }
          }).on("initialized.owl.carousel refreshed.owl.carousel", function() {
            $(this).find(".owl-dot").each(function(index) {
              $(this).attr("aria-label", `Go to slide ${index + 1}`);
            });
          });
        }
        if (typeof WOW !== "undefined") {
          new WOW().init();
        }
        if (typeof gsap !== "undefined" && typeof SplitText !== "undefined") {
          const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation");
          if (titleAnimations.length) {
            titleAnimations.forEach((quote) => {
              new SplitText(quote, { type: "lines" });
              let split = new SplitText(quote, { type: "lines" });
              gsap.from(split.lines, {
                duration: 1,
                y: 100,
                opacity: 0,
                stagger: 0.1,
                scrollTrigger: {
                  trigger: quote,
                  start: "top 90%",
                  toggleActions: "play none none none"
                }
              });
            });
          }
        }
        if (typeof $ !== "undefined" && $.fn.odometer) {
          $(".odometer").each(function() {
            const $this = $(this);
            if (!$this.data("initialized")) {
              $this.odometer({
                value: parseInt($this.attr("data-count")) || 0,
                format: "(,ddd)",
                theme: "minimal"
              });
              $this.data("initialized", true);
            }
          });
        } else if (typeof Odometer !== "undefined") {
          const odometerElements = document.querySelectorAll(".odometer");
          odometerElements.forEach((el) => {
            if (!el.dataset.initialized) {
              const count = parseFloat(el.getAttribute("data-count")) || 0;
              el.dataset.initialized = "1";
              const od = new Odometer({
                el,
                value: 0,
                format: "(,ddd)",
                theme: "minimal"
              });
              setTimeout(() => {
                od.update(count);
              }, 100);
            }
          });
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/counter.css")}${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/about.css")}${_scopeId}>`);
            if (locale.value === "ar") {
              _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/rtl.css")}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/counter.css"
              }, null, 8, ["href"]),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/about.css"
              }, null, 8, ["href"]),
              locale.value === "ar" ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "stylesheet",
                href: asset_path.value + "site/css/rtl.css"
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 1,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/about-us-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("About Us"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("home")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                  } else {
                    return [
                      createVNode("i", { class: "fas fa-home" }),
                      createTextVNode(toDisplayString(trans("Home")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}`)}${_scopeId}><i class="fas fa-home"${_scopeId}></i>${ssrInterpolate(trans("Home"))}</a>`);
            }
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("About Us"))}</li></ul></div></div></div></section><section class="about-four"${_scopeId}><div class="about-four__bg-shape" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/shapes/about-four-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6"${_scopeId}><div class="${ssrRenderClass(`about-four__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="about-four__img-box"${_scopeId}><div class="about-four__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/about/about-four-img-1.jpg")}${ssrRenderAttr("alt", trans("About our company"))}${_scopeId}></div><div class="about-four__experience"${_scopeId}><div class="about-four__experience-inner"${_scopeId}><p class="about-four__experience-count-text"${_scopeId}>10 ${ssrInterpolate(trans("Years of"))} <br${_scopeId}> ${ssrInterpolate(trans("Experience"))}</p></div></div></div></div></div><div class="col-xl-6"${_scopeId}><div class="about-four__right"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("About Us"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div>`);
            if (locale.value !== "ar") {
              _push2(`<h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Supercharge"))} <span${_scopeId}>${ssrInterpolate(trans("Your Business"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Growth with Our"))}</span> ${ssrInterpolate(trans("Cutting-Edge IT"))}<br${_scopeId}> ${ssrInterpolate(trans("Solutions"))}</h2>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><p class="about-four__text"${_scopeId}>${ssrInterpolate(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape."))}</p><div class="about-four__points-box"${_scopeId}><ul class="about-four__points-list list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><p${_scopeId}>${ssrInterpolate(trans("Innovative IT Solutions Expert"))}<br${_scopeId}> ${ssrInterpolate(trans("Support & Consulting"))}</p></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><p${_scopeId}>${ssrInterpolate(trans("Cloud Solutions for Modern"))}<br${_scopeId}> ${ssrInterpolate(trans("Enterprises"))}</p></li></ul><ul class="about-four__points-list about-four__points-list-2 list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-tick-inside-circle"${_scopeId}></span></div><p${_scopeId}>${ssrInterpolate(trans("Seamless Digital"))}<br${_scopeId}> ${ssrInterpolate(trans("Transformation AI-Driven"))} <br${_scopeId}>${ssrInterpolate(trans("Business Automation"))}</p></li></ul></div><ul class="about-four__points-2 list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-technical-support"${_scopeId}></span></div><div class="content"${_scopeId}><h5${_scopeId}>${ssrInterpolate(trans("Innovative IT Solutions"))}</h5><p${_scopeId}>${ssrInterpolate(trans("Stay ahead with cutting-edge technology tailored to your business needs."))}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-real-estate-agency"${_scopeId}></span></div><div class="content"${_scopeId}><h5${_scopeId}>${ssrInterpolate(trans("Cloud Solutions"))}</h5><p${_scopeId}>${ssrInterpolate(trans("Secure, scalable, and efficient cloud services to power your growth."))}</p></div></li></ul></div></div></div></div></section><section class="why-choose-three"${_scopeId}><div class="why-choose-three__bg-shape float-bob-x" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/shapes/why-choose-three-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Why Choose Us"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div>`);
            if (locale.value !== "ar") {
              _push2(`<h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Your Business with"))} <span${_scopeId}>${ssrInterpolate(trans("Reliable &"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Future-Ready"))}</span> ${ssrInterpolate(trans("IT Solutions"))}</h2>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="row"${_scopeId}><div class="col-xl-3 wow fadeInLeft" data-wow-delay="100ms"${_scopeId}><div class="why-choose-three__single-left"${_scopeId}><div class="why-choose-three__single"${_scopeId}><div class="why-choose-three__icon"${_scopeId}><span class="icon-quality"${_scopeId}></span></div><h3 class="why-choose-three__title"${_scopeId}>${ssrInterpolate(trans("Unmatched Quality"))}</h3><div class="why-choose-three__bdr"${_scopeId}></div><p class="why-choose-three__text"${_scopeId}>${ssrInterpolate(trans("We deliver exceptional products and services that exceed expectations every time."))}</p></div><div class="why-choose-three__single"${_scopeId}><div class="why-choose-three__icon"${_scopeId}><span class="icon-team"${_scopeId}></span></div><h3 class="why-choose-three__title"${_scopeId}>${ssrInterpolate(trans("Trusted Expertise"))}</h3><div class="why-choose-three__bdr"${_scopeId}></div><p class="why-choose-three__text"${_scopeId}>${ssrInterpolate(trans("Backed by years of experience and a proven track record, we are your reliable partner for success."))}</p></div></div></div><div class="col-xl-6 wow fadeInUp" data-wow-delay="200ms"${_scopeId}><div class="why-choose-three__img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/about/tech-concept.jpg")}${ssrRenderAttr("alt", trans("Technology concept"))}${_scopeId}></div></div><div class="col-xl-3 wow fadeInRight" data-wow-delay="100ms"${_scopeId}><div class="why-choose-three__single-left"${_scopeId}><div class="why-choose-three__single"${_scopeId}><div class="why-choose-three__icon"${_scopeId}><span class="icon-customer-centricity"${_scopeId}></span></div><h3 class="why-choose-three__title"${_scopeId}>${ssrInterpolate(trans("User-Centric Approach"))}</h3><div class="why-choose-three__bdr"${_scopeId}></div><p class="why-choose-three__text"${_scopeId}>${ssrInterpolate(trans("Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first."))}</p></div><div class="why-choose-three__single"${_scopeId}><div class="why-choose-three__icon"${_scopeId}><span class="icon-support"${_scopeId}></span></div><h3 class="why-choose-three__title"${_scopeId}>${ssrInterpolate(trans("Trusted by Many"))}</h3><div class="why-choose-three__bdr"${_scopeId}></div><p class="why-choose-three__text"${_scopeId}>${ssrInterpolate(trans("We have built a strong reputation over the years by consistently delivering excellent results."))}</p></div></div></div></div></div></section>`);
            if (teams.value && teams.value.length > 0) {
              _push2(`<section class="team-two"${_scopeId}><div class="team-two__bg-shape float-bob-y" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/shapes/team-two-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-5"${_scopeId}><div class="team-two__left"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Our Members"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Meet Our Team."))} <span${_scopeId}>${ssrInterpolate(trans("Get to"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Know the Talented"))}</span> ${ssrInterpolate(trans("Minds Behind Our Team"))}</h2></div><p class="team-two__text"${_scopeId}>${ssrInterpolate(trans("Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way."))}</p></div></div><div class="col-xl-7"${_scopeId}><div class="team-two__right"${_scopeId}><div class="team-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(teams.value, (team) => {
                _push2(`<div class="item"${_scopeId}><div class="team-two__single"${_scopeId}><div class="team-two__img-box"${_scopeId}><div class="team-two__img"${_scopeId}><img${ssrRenderAttr("src", team.avatar_link)}${ssrRenderAttr("alt", translateField(team.name))}${_scopeId}></div><div class="team-two__social"${_scopeId}>`);
                if (team.facebook) {
                  _push2(`<a${ssrRenderAttr("href", team.facebook)} target="_blank"${_scopeId}><span class="icon-facebook"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.behance) {
                  _push2(`<a${ssrRenderAttr("href", team.behance)} target="_blank"${_scopeId}><span class="icon-dribble"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.linked_in) {
                  _push2(`<a${ssrRenderAttr("href", team.linked_in)} target="_blank"${_scopeId}><span class="icon-linkedin"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.github) {
                  _push2(`<a${ssrRenderAttr("href", team.github)} target="_blank"${_scopeId}><span class="icon-github"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div><div class="team-two__title-box"${_scopeId}><h3${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(team.name))}</a></h3><p${_scopeId}>${ssrInterpolate(translateField(team.position))}</p></div></div></div></div>`);
              });
              _push2(`<!--]--></div></div></div></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<section class="process-one"${_scopeId}><div class="process-one__shape-1"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/shapes/process-one-shape-1.png")}${ssrRenderAttr("alt", trans("Process illustration"))}${_scopeId}></div><div class="process-one__bg-shape" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/shapes/process-one-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-5"${_scopeId}><div class="process-one__left"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Working Process"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("How We've"))} <span${_scopeId}>${ssrInterpolate(trans("Empowered"))}</span><br${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Businesses with Innovative"))}</span><br${_scopeId}> ${ssrInterpolate(trans("Tech Solutions"))}</h2></div><p class="process-one__text"${_scopeId}>${ssrInterpolate(trans("From personalized solutions to expert execution, we prioritize quality, reliability, and customer satisfaction"))}</p><div class="process-one__btn-box"${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("contact-us"),
                class: "thm-btn"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(trans("Get in Touch"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow`)}"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(trans("Get in Touch")) + " ", 1),
                      createVNode("span", {
                        class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow`
                      }, null, 2)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}/contact-us`)} class="thm-btn"${_scopeId}>${ssrInterpolate(trans("Get in Touch"))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow`)}"${_scopeId}></span></a>`);
            }
            _push2(`</div></div></div><div class="col-xl-7"${_scopeId}><div class="process-one__right"${_scopeId}><ul class="process-one__process-list list-unstyled"${_scopeId}><li${_scopeId}><div class="process-one__process-count"${_scopeId}></div><div class="process-one__process-content"${_scopeId}><h3 class="process-one__process-title"${_scopeId}>${ssrInterpolate(trans("Discovery & Strategy"))}</h3><p class="process-one__process-text"${_scopeId}>${ssrInterpolate(trans("We analyze your business needs, identify challenges, and craft a strategic roadmap for the best IT solutions."))}</p></div></li><li${_scopeId}><div class="process-one__process-content"${_scopeId}><h3 class="process-one__process-title"${_scopeId}>${ssrInterpolate(trans("Development"))}</h3><p class="process-one__process-text"${_scopeId}>${ssrInterpolate(trans("Our expert team designs, develops, and integrates cutting-edge technology tailored to your goals."))}</p></div><div class="process-one__process-count"${_scopeId}></div></li><li${_scopeId}><div class="process-one__process-count"${_scopeId}></div><div class="process-one__process-content"${_scopeId}><h3 class="process-one__process-title"${_scopeId}>${ssrInterpolate(trans("Optimization & Support"))}</h3><p class="process-one__process-text"${_scopeId}>${ssrInterpolate(trans("We ensure seamless performance with continuous improvements, maintenance, and dedicated support."))}</p></div></li></ul></div></div></div></div></section>`);
            if (testimonials.value && testimonials.value.length > 0) {
              _push2(`<section class="testimonial-two"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Customer Experiences"))} <br${_scopeId}> ${ssrInterpolate(trans("That"))} <span${_scopeId}>${ssrInterpolate(trans("Speak Volumes"))}</span></h2></div><div class="testimonial-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(testimonials.value, (testimonial) => {
                _push2(`<div class="item"${_scopeId}><div class="testimonial-two__single"${_scopeId}><div class="testimonial-two__single-inner"${_scopeId}><div class="testimonial-two__star"${_scopeId}><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span></div><p class="testimonial-two__text"${_scopeId}>${ssrInterpolate(translateField(testimonial.quote))}</p></div><div class="testimonial-two__client-info"${_scopeId}><div class="testimonial-two__client-img"${_scopeId}><img${ssrRenderAttr("src", testimonial.avatar_link)}${ssrRenderAttr("alt", translateField(testimonial.name))}${_scopeId}></div><div class="testimonial-two__client-content"${_scopeId}><h3 class="h4 testimonial-two__client-name"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(testimonial.name))}</a></h3><p class="testimonial-two__sub-title"${_scopeId}>${ssrInterpolate(translateField(testimonial.position))}</p></div></div><div class="testimonial-two__quote"${_scopeId}><span class="icon-right-quote"${_scopeId}></span></div></div></div>`);
              });
              _push2(`<!--]--></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/about-us-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("About Us")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}`
                          }, [
                            createVNode("i", { class: "fas fa-home" }),
                            createTextVNode(toDisplayString(trans("Home")), 1)
                          ], 8, ["href"]))
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("About Us")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "about-four" }, [
                createVNode("div", {
                  class: "about-four__bg-shape",
                  style: { backgroundImage: `url(${asset_path.value}images/shapes/about-four-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", {
                        class: `about-four__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "about-four__img-box" }, [
                          createVNode("div", { class: "about-four__img" }, [
                            createVNode("img", {
                              src: asset_path.value + "images/about/about-four-img-1.jpg",
                              alt: trans("About our company")
                            }, null, 8, ["src", "alt"])
                          ]),
                          createVNode("div", { class: "about-four__experience" }, [
                            createVNode("div", { class: "about-four__experience-inner" }, [
                              createVNode("p", { class: "about-four__experience-count-text" }, [
                                createTextVNode("10 " + toDisplayString(trans("Years of")) + " ", 1),
                                createVNode("br"),
                                createTextVNode(" " + toDisplayString(trans("Experience")), 1)
                              ])
                            ])
                          ])
                        ])
                      ], 2)
                    ]),
                    createVNode("div", { class: "col-xl-6" }, [
                      createVNode("div", { class: "about-four__right" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("About Us")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          locale.value !== "ar" ? (openBlock(), createBlock("h2", {
                            key: 0,
                            class: "section-title__title title-animation"
                          }, [
                            createTextVNode(toDisplayString(trans("Supercharge")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("Your Business")), 1),
                            createVNode("br"),
                            createVNode("span", null, toDisplayString(trans("Growth with Our")), 1),
                            createTextVNode(" " + toDisplayString(trans("Cutting-Edge IT")), 1),
                            createVNode("br"),
                            createTextVNode(" " + toDisplayString(trans("Solutions")), 1)
                          ])) : createCommentVNode("", true)
                        ]),
                        createVNode("p", { class: "about-four__text" }, toDisplayString(trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape.")), 1),
                        createVNode("div", { class: "about-four__points-box" }, [
                          createVNode("ul", { class: "about-four__points-list list-unstyled" }, [
                            createVNode("li", null, [
                              createVNode("div", { class: "icon" }, [
                                createVNode("span", { class: "icon-tick-inside-circle" })
                              ]),
                              createVNode("p", null, [
                                createTextVNode(toDisplayString(trans("Innovative IT Solutions Expert")), 1),
                                createVNode("br"),
                                createTextVNode(" " + toDisplayString(trans("Support & Consulting")), 1)
                              ])
                            ]),
                            createVNode("li", null, [
                              createVNode("div", { class: "icon" }, [
                                createVNode("span", { class: "icon-tick-inside-circle" })
                              ]),
                              createVNode("p", null, [
                                createTextVNode(toDisplayString(trans("Cloud Solutions for Modern")), 1),
                                createVNode("br"),
                                createTextVNode(" " + toDisplayString(trans("Enterprises")), 1)
                              ])
                            ])
                          ]),
                          createVNode("ul", { class: "about-four__points-list about-four__points-list-2 list-unstyled" }, [
                            createVNode("li", null, [
                              createVNode("div", { class: "icon" }, [
                                createVNode("span", { class: "icon-tick-inside-circle" })
                              ]),
                              createVNode("p", null, [
                                createTextVNode(toDisplayString(trans("Seamless Digital")), 1),
                                createVNode("br"),
                                createTextVNode(" " + toDisplayString(trans("Transformation AI-Driven")) + " ", 1),
                                createVNode("br"),
                                createTextVNode(toDisplayString(trans("Business Automation")), 1)
                              ])
                            ])
                          ])
                        ]),
                        createVNode("ul", { class: "about-four__points-2 list-unstyled" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-technical-support" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h5", null, toDisplayString(trans("Innovative IT Solutions")), 1),
                              createVNode("p", null, toDisplayString(trans("Stay ahead with cutting-edge technology tailored to your business needs.")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-real-estate-agency" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h5", null, toDisplayString(trans("Cloud Solutions")), 1),
                              createVNode("p", null, toDisplayString(trans("Secure, scalable, and efficient cloud services to power your growth.")), 1)
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "why-choose-three" }, [
                createVNode("div", {
                  class: "why-choose-three__bg-shape float-bob-x",
                  style: { backgroundImage: `url(${asset_path.value}images/shapes/why-choose-three-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Why Choose Us")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    locale.value !== "ar" ? (openBlock(), createBlock("h2", {
                      key: 0,
                      class: "section-title__title title-animation"
                    }, [
                      createTextVNode(toDisplayString(trans("Your Business with")) + " ", 1),
                      createVNode("span", null, toDisplayString(trans("Reliable &")), 1),
                      createVNode("br"),
                      createVNode("span", null, toDisplayString(trans("Future-Ready")), 1),
                      createTextVNode(" " + toDisplayString(trans("IT Solutions")), 1)
                    ])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "row" }, [
                    createVNode("div", {
                      class: "col-xl-3 wow fadeInLeft",
                      "data-wow-delay": "100ms"
                    }, [
                      createVNode("div", { class: "why-choose-three__single-left" }, [
                        createVNode("div", { class: "why-choose-three__single" }, [
                          createVNode("div", { class: "why-choose-three__icon" }, [
                            createVNode("span", { class: "icon-quality" })
                          ]),
                          createVNode("h3", { class: "why-choose-three__title" }, toDisplayString(trans("Unmatched Quality")), 1),
                          createVNode("div", { class: "why-choose-three__bdr" }),
                          createVNode("p", { class: "why-choose-three__text" }, toDisplayString(trans("We deliver exceptional products and services that exceed expectations every time.")), 1)
                        ]),
                        createVNode("div", { class: "why-choose-three__single" }, [
                          createVNode("div", { class: "why-choose-three__icon" }, [
                            createVNode("span", { class: "icon-team" })
                          ]),
                          createVNode("h3", { class: "why-choose-three__title" }, toDisplayString(trans("Trusted Expertise")), 1),
                          createVNode("div", { class: "why-choose-three__bdr" }),
                          createVNode("p", { class: "why-choose-three__text" }, toDisplayString(trans("Backed by years of experience and a proven track record, we are your reliable partner for success.")), 1)
                        ])
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-6 wow fadeInUp",
                      "data-wow-delay": "200ms"
                    }, [
                      createVNode("div", { class: "why-choose-three__img" }, [
                        createVNode("img", {
                          src: asset_path.value + "images/about/tech-concept.jpg",
                          alt: trans("Technology concept")
                        }, null, 8, ["src", "alt"])
                      ])
                    ]),
                    createVNode("div", {
                      class: "col-xl-3 wow fadeInRight",
                      "data-wow-delay": "100ms"
                    }, [
                      createVNode("div", { class: "why-choose-three__single-left" }, [
                        createVNode("div", { class: "why-choose-three__single" }, [
                          createVNode("div", { class: "why-choose-three__icon" }, [
                            createVNode("span", { class: "icon-customer-centricity" })
                          ]),
                          createVNode("h3", { class: "why-choose-three__title" }, toDisplayString(trans("User-Centric Approach")), 1),
                          createVNode("div", { class: "why-choose-three__bdr" }),
                          createVNode("p", { class: "why-choose-three__text" }, toDisplayString(trans("Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first.")), 1)
                        ]),
                        createVNode("div", { class: "why-choose-three__single" }, [
                          createVNode("div", { class: "why-choose-three__icon" }, [
                            createVNode("span", { class: "icon-support" })
                          ]),
                          createVNode("h3", { class: "why-choose-three__title" }, toDisplayString(trans("Trusted by Many")), 1),
                          createVNode("div", { class: "why-choose-three__bdr" }),
                          createVNode("p", { class: "why-choose-three__text" }, toDisplayString(trans("We have built a strong reputation over the years by consistently delivering excellent results.")), 1)
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              teams.value && teams.value.length > 0 ? (openBlock(), createBlock("section", {
                key: 0,
                class: "team-two"
              }, [
                createVNode("div", {
                  class: "team-two__bg-shape float-bob-y",
                  style: { backgroundImage: `url(${asset_path.value}images/shapes/team-two-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-5" }, [
                      createVNode("div", { class: "team-two__left" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Members")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, [
                            createTextVNode(toDisplayString(trans("Meet Our Team.")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("Get to")), 1),
                            createVNode("br"),
                            createVNode("span", null, toDisplayString(trans("Know the Talented")), 1),
                            createTextVNode(" " + toDisplayString(trans("Minds Behind Our Team")), 1)
                          ])
                        ]),
                        createVNode("p", { class: "team-two__text" }, toDisplayString(trans("Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way.")), 1)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-7" }, [
                      createVNode("div", { class: "team-two__right" }, [
                        createVNode("div", { class: "team-two__carousel owl-theme owl-carousel" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(teams.value, (team) => {
                            return openBlock(), createBlock("div", {
                              class: "item",
                              key: team.id
                            }, [
                              createVNode("div", { class: "team-two__single" }, [
                                createVNode("div", { class: "team-two__img-box" }, [
                                  createVNode("div", { class: "team-two__img" }, [
                                    createVNode("img", {
                                      src: team.avatar_link,
                                      alt: translateField(team.name)
                                    }, null, 8, ["src", "alt"])
                                  ]),
                                  createVNode("div", { class: "team-two__social" }, [
                                    team.facebook ? (openBlock(), createBlock("a", {
                                      key: 0,
                                      href: team.facebook,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-facebook" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.behance ? (openBlock(), createBlock("a", {
                                      key: 1,
                                      href: team.behance,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-dribble" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.linked_in ? (openBlock(), createBlock("a", {
                                      key: 2,
                                      href: team.linked_in,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-linkedin" })
                                    ], 8, ["href"])) : createCommentVNode("", true),
                                    team.github ? (openBlock(), createBlock("a", {
                                      key: 3,
                                      href: team.github,
                                      target: "_blank"
                                    }, [
                                      createVNode("span", { class: "icon-github" })
                                    ], 8, ["href"])) : createCommentVNode("", true)
                                  ]),
                                  createVNode("div", { class: "team-two__title-box" }, [
                                    createVNode("h3", null, [
                                      createVNode("a", { href: "#" }, toDisplayString(translateField(team.name)), 1)
                                    ]),
                                    createVNode("p", null, toDisplayString(translateField(team.position)), 1)
                                  ])
                                ])
                              ])
                            ]);
                          }), 128))
                        ])
                      ])
                    ])
                  ])
                ])
              ])) : createCommentVNode("", true),
              createVNode("section", { class: "process-one" }, [
                createVNode("div", { class: "process-one__shape-1" }, [
                  createVNode("img", {
                    src: asset_path.value + "images/shapes/process-one-shape-1.png",
                    alt: trans("Process illustration")
                  }, null, 8, ["src", "alt"])
                ]),
                createVNode("div", {
                  class: "process-one__bg-shape",
                  style: { backgroundImage: `url(${asset_path.value}images/shapes/process-one-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-5" }, [
                      createVNode("div", { class: "process-one__left" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Working Process")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", { class: "section-title__title title-animation" }, [
                            createTextVNode(toDisplayString(trans("How We've")) + " ", 1),
                            createVNode("span", null, toDisplayString(trans("Empowered")), 1),
                            createVNode("br"),
                            createVNode("span", null, toDisplayString(trans("Businesses with Innovative")), 1),
                            createVNode("br"),
                            createTextVNode(" " + toDisplayString(trans("Tech Solutions")), 1)
                          ])
                        ]),
                        createVNode("p", { class: "process-one__text" }, toDisplayString(trans("From personalized solutions to expert execution, we prioritize quality, reliability, and customer satisfaction")), 1),
                        createVNode("div", { class: "process-one__btn-box" }, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("contact-us"),
                            class: "thm-btn"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(trans("Get in Touch")) + " ", 1),
                              createVNode("span", {
                                class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow`
                              }, null, 2)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}/contact-us`,
                            class: "thm-btn"
                          }, [
                            createTextVNode(toDisplayString(trans("Get in Touch")) + " ", 1),
                            createVNode("span", {
                              class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow`
                            }, null, 2)
                          ], 8, ["href"]))
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-7" }, [
                      createVNode("div", { class: "process-one__right" }, [
                        createVNode("ul", { class: "process-one__process-list list-unstyled" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "process-one__process-count" }),
                            createVNode("div", { class: "process-one__process-content" }, [
                              createVNode("h3", { class: "process-one__process-title" }, toDisplayString(trans("Discovery & Strategy")), 1),
                              createVNode("p", { class: "process-one__process-text" }, toDisplayString(trans("We analyze your business needs, identify challenges, and craft a strategic roadmap for the best IT solutions.")), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "process-one__process-content" }, [
                              createVNode("h3", { class: "process-one__process-title" }, toDisplayString(trans("Development")), 1),
                              createVNode("p", { class: "process-one__process-text" }, toDisplayString(trans("Our expert team designs, develops, and integrates cutting-edge technology tailored to your goals.")), 1)
                            ]),
                            createVNode("div", { class: "process-one__process-count" })
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "process-one__process-count" }),
                            createVNode("div", { class: "process-one__process-content" }, [
                              createVNode("h3", { class: "process-one__process-title" }, toDisplayString(trans("Optimization & Support")), 1),
                              createVNode("p", { class: "process-one__process-text" }, toDisplayString(trans("We ensure seamless performance with continuous improvements, maintenance, and dedicated support.")), 1)
                            ])
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              testimonials.value && testimonials.value.length > 0 ? (openBlock(), createBlock("section", {
                key: 1,
                class: "testimonial-two"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Testimonials")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, [
                      createTextVNode(toDisplayString(trans("Customer Experiences")) + " ", 1),
                      createVNode("br"),
                      createTextVNode(" " + toDisplayString(trans("That")) + " ", 1),
                      createVNode("span", null, toDisplayString(trans("Speak Volumes")), 1)
                    ])
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
                              createVNode("h3", { class: "h4 testimonial-two__client-name" }, [
                                createVNode("a", { href: "#" }, toDisplayString(translateField(testimonial.name)), 1)
                              ]),
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
              ])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
});
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/AboutUs.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$f
}, Symbol.toStringTag, { value: "Module" }));
const __default__$a = {
  components: {
    AppLayout: _sfc_main$h,
    HomeBlogCard: _sfc_main$l
  }
};
const _sfc_main$e = /* @__PURE__ */ Object.assign(__default__$a, {
  __name: "BlogIndex",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const blogs = computed(() => page.props.blogs);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return `${trans("Blogs")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Explore our latest blogs, insights, and technology updates.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("blogs, news, insights, technology trends") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}><title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/blogs-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("Our Blogs"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("Blogs"))}</li></ul></div></div></div></div><section class="blog-page mt-25 pb-90"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("News & Blog"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${trans("How We've <span>Empowered Businesses</span><br><span> with Innovative</span>Tech Solutions") ?? ""}</h2></div><div class="row"${_scopeId}>`);
            if (blogs.value.data && blogs.value.data.length > 0) {
              _push2(`<!--[-->`);
              ssrRenderList(blogs.value.data, (blog) => {
                _push2(`<div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms"${_scopeId}>`);
                _push2(ssrRenderComponent(_sfc_main$l, {
                  post: blog,
                  variant: "featured",
                  locale: locale.value,
                  "asset-path": asset_path.value,
                  "image-fallback-index": 1
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              });
              _push2(`<!--]-->`);
            } else {
              _push2(`<div class="col-12"${_scopeId}><div class="text-center py-5"${_scopeId}><h3 class="text-muted"${_scopeId}>${ssrInterpolate(trans("No blogs found"))} <i class="fa fa-xmark text-danger"${_scopeId}></i></h3></div></div>`);
            }
            if (blogs.value.last_page > 1) {
              _push2(`<div class="blog-page__pagination"${_scopeId}><ul class="pg-pagination list-unstyled"${_scopeId}>`);
              if (blogs.value.prev_page_url) {
                _push2(`<li class="prev"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: blogs.value.prev_page_url,
                  "aria-label": "prev"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<span class="icon-left-arrow-1"${_scopeId2}></span>`);
                    } else {
                      return [
                        createVNode("span", { class: "icon-left-arrow-1" })
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</li>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<!--[-->`);
              ssrRenderList(blogs.value.links, (link, index) => {
                _push2(`<!--[-->`);
                if (link.url && index > 0 && index < blogs.value.links.length - 1) {
                  _push2(`<li class="${ssrRenderClass(["count", link.active ? "active" : ""])}"${_scopeId}>`);
                  _push2(ssrRenderComponent(unref(Link), {
                    href: link.url
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
                  _push2(`</li>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<!--]-->`);
              });
              _push2(`<!--]-->`);
              if (blogs.value.next_page_url) {
                _push2(`<li class="next"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: blogs.value.next_page_url,
                  "aria-label": "Next"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId2}></span>`);
                    } else {
                      return [
                        createVNode("span", {
                          class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                        }, null, 2)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</li>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</ul></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/blogs-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Our Blogs")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Blogs")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "blog-page mt-25 pb-90" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("News & Blog")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", {
                      class: "section-title__title title-animation",
                      innerHTML: trans("How We've <span>Empowered Businesses</span><br><span> with Innovative</span>Tech Solutions")
                    }, null, 8, ["innerHTML"])
                  ]),
                  createVNode("div", { class: "row" }, [
                    blogs.value.data && blogs.value.data.length > 0 ? (openBlock(true), createBlock(Fragment, { key: 0 }, renderList(blogs.value.data, (blog) => {
                      return openBlock(), createBlock("div", {
                        key: blog.id,
                        class: "col-xl-4 col-lg-6 col-md-6 wow fadeInUp",
                        "data-wow-delay": "100ms"
                      }, [
                        createVNode(_sfc_main$l, {
                          post: blog,
                          variant: "featured",
                          locale: locale.value,
                          "asset-path": asset_path.value,
                          "image-fallback-index": 1
                        }, null, 8, ["post", "locale", "asset-path"])
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
                    ])),
                    blogs.value.last_page > 1 ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "blog-page__pagination"
                    }, [
                      createVNode("ul", { class: "pg-pagination list-unstyled" }, [
                        blogs.value.prev_page_url ? (openBlock(), createBlock("li", {
                          key: 0,
                          class: "prev"
                        }, [
                          createVNode(unref(Link), {
                            href: blogs.value.prev_page_url,
                            "aria-label": "prev"
                          }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "icon-left-arrow-1" })
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])) : createCommentVNode("", true),
                        (openBlock(true), createBlock(Fragment, null, renderList(blogs.value.links, (link, index) => {
                          return openBlock(), createBlock(Fragment, { key: index }, [
                            link.url && index > 0 && index < blogs.value.links.length - 1 ? (openBlock(), createBlock("li", {
                              key: 0,
                              class: ["count", link.active ? "active" : ""]
                            }, [
                              createVNode(unref(Link), {
                                href: link.url
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(link.label), 1)
                                ]),
                                _: 2
                              }, 1032, ["href"])
                            ], 2)) : createCommentVNode("", true)
                          ], 64);
                        }), 128)),
                        blogs.value.next_page_url ? (openBlock(), createBlock("li", {
                          key: 1,
                          class: "next"
                        }, [
                          createVNode(unref(Link), {
                            href: blogs.value.next_page_url,
                            "aria-label": "Next"
                          }, {
                            default: withCtx(() => [
                              createVNode("span", {
                                class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                              }, null, 2)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])) : createCommentVNode("", true)
                      ])
                    ])) : createCommentVNode("", true)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/BlogIndex.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$e
}, Symbol.toStringTag, { value: "Module" }));
const __default__$9 = {
  components: {
    AppLayout: _sfc_main$h,
    HomeBlogCard: _sfc_main$l
  }
};
const _sfc_main$d = /* @__PURE__ */ Object.assign(__default__$9, {
  __name: "BlogShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
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
            _push2(`<title${_scopeId}>${ssrInterpolate(blog.value.title)}</title><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(blog.value.title), 1),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("Blog Details"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("Blog Details"))}</li></ul></div></div></div></section><section class="blog-details"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-8 col-lg-7"${_scopeId}><div class="blog-details__left"${_scopeId}><div class="blog-details__img"${_scopeId}><img${ssrRenderAttr("src", blog.value.image_link)}${ssrRenderAttr("alt", blog.value.title)}${_scopeId}></div><div class="blog-details__single-content"${_scopeId}><ul class="blog-details__meta list-unstyled"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("blogs.show", blog.value.slug)
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="far fa-calendar-alt"${_scopeId2}></span>${ssrInterpolate(blog.value.created_at_formatted)}`);
                } else {
                  return [
                    createVNode("span", { class: "far fa-calendar-alt" }),
                    createTextVNode(toDisplayString(blog.value.created_at_formatted), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="far fa-clock"${_scopeId2}></span>${ssrInterpolate(blog.value.reading_time)} ${ssrInterpolate(trans("min read"))}`);
                } else {
                  return [
                    createVNode("span", { class: "far fa-clock" }),
                    createTextVNode(toDisplayString(blog.value.reading_time) + " " + toDisplayString(trans("min read")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li></ul><h3 class="blog-details__title"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("blogs.show", blog.value.slug)
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(blog.value.title)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(blog.value.title), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</h3><div class="blog-details__text"${_scopeId}>${blog.value.content ?? ""}</div></div>`);
            if (blog.value.keywords) {
              _push2(`<div class="blog-details__tag-and-share"${_scopeId}><div class="blog-details__tag"${_scopeId}><h3 class="blog-details__tag-title"${_scopeId}>${ssrInterpolate(trans("Keywords"))}:</h3><ul class="blog-details__tag-list list-unstyled"${_scopeId}><!--[-->`);
              ssrRenderList(getKeywords(blog.value.keywords), (keyword, index) => {
                _push2(`<li${_scopeId}>`);
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
                _push2(`</li>`);
              });
              _push2(`<!--]--></ul></div><div class="blog-details__share-box"${_scopeId}><h3 class="blog-details__share-title"${_scopeId}>${ssrInterpolate(trans("Share On:"))}</h3><div class="blog-details__share"${_scopeId}><a${ssrRenderAttr("href", getShareUrl("facebook"))} target="_blank"${_scopeId}><span class="icon-facebook"${_scopeId}></span></a><a${ssrRenderAttr("href", getShareUrl("twitter"))} target="_blank"${_scopeId}><span class="fab fa-twitter"${_scopeId}></span></a><a${ssrRenderAttr("href", getShareUrl("linkedin"))} target="_blank"${_scopeId}><span class="icon-linkedin"${_scopeId}></span></a></div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (previousPost.value || nextPost.value) {
              _push2(`<div class="blog-details__prev-and-next"${_scopeId}>`);
              if (previousPost.value) {
                _push2(`<div class="blog-details__prev-box"${_scopeId}><div class="blog-details__prev-img"${_scopeId}><img${ssrRenderAttr("src", previousPost.value.image_link)}${ssrRenderAttr("alt", previousPost.value.title)}${_scopeId}></div><div class="blog-details__prev-content"${_scopeId}><div class="blog-details__prev-arrow"${_scopeId}><span class="icon-left-arrow"${_scopeId}></span>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", previousPost.value.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Prev Blog"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Prev Blog")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</div><h4 class="blog-details__prev-title"${_scopeId}>${ssrInterpolate(previousPost.value.title)}</h4></div></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (nextPost.value) {
                _push2(`<div class="blog-details__next-box"${_scopeId}><div class="blog-details__next-content"${_scopeId}><div class="blog-details__next-arrow"${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: _ctx.route("blogs.show", nextPost.value.slug)
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(trans("Next Blog"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(trans("Next Blog")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`<span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow `)}"${_scopeId}></span></div><h4 class="blog-details__next-title"${_scopeId}>${ssrInterpolate(nextPost.value.title)}</h4></div><div class="blog-details__next-img"${_scopeId}><img${ssrRenderAttr("src", nextPost.value.image_link)}${ssrRenderAttr("alt", nextPost.value.title)}${_scopeId}></div></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-4 col-lg-5"${_scopeId}><div class="sidebar"${_scopeId}><div class="sidebar__single sidebar__search"${_scopeId}><div class="sidebar__title-box"${_scopeId}><div class="sidebar__title-shape"${_scopeId}></div><h3 class="sidebar__title"${_scopeId}>${ssrInterpolate(trans("Search"))}</h3></div><p class="sidebar__search-text"${_scopeId}>${ssrInterpolate(trans("Search blogs to discover a vast world of online content on countless topics."))}</p><form class="sidebar__search-form"${_scopeId}><input type="search"${ssrRenderAttr("value", searchQuery.value)}${ssrRenderAttr("placeholder", trans("Search Blogs"))}${_scopeId}><button type="submit"${_scopeId}><i class="fa fa-search"${_scopeId}></i></button></form></div><div class="sidebar__single sidebar__category"${_scopeId}><div class="sidebar__title-box"${_scopeId}><div class="sidebar__title-shape"${_scopeId}></div><h3 class="sidebar__title"${_scopeId}>${ssrInterpolate(trans("Category"))}</h3></div><ul class="sidebar__category-list list-unstyled"${_scopeId}><!--[-->`);
            ssrRenderList(categories.value, (category) => {
              _push2(`<li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.index", { category: category.slug })
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(category.name)} <span${_scopeId2}>(${ssrInterpolate(category.blogs_count)})</span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(category.name) + " ", 1),
                      createVNode("span", null, "(" + toDisplayString(category.blogs_count) + ")", 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</li>`);
            });
            _push2(`<!--]--></ul></div><div class="sidebar__single sidebar__post"${_scopeId}><div class="sidebar__title-box"${_scopeId}><div class="sidebar__title-shape"${_scopeId}></div><h3 class="sidebar__title"${_scopeId}>${ssrInterpolate(trans("Recent Post"))}</h3></div><ul class="sidebar__post-list list-unstyled"${_scopeId}><!--[-->`);
            ssrRenderList(recentPosts.value, (recentPost) => {
              _push2(`<li${_scopeId}><div class="sidebar__post-image"${_scopeId}><img${ssrRenderAttr("src", recentPost.image_link)}${ssrRenderAttr("alt", recentPost.title)}${_scopeId}></div><div class="sidebar__post-content"${_scopeId}><p class="sidebar__post-date"${_scopeId}><span class="icon-calendar"${_scopeId}></span>${ssrInterpolate(recentPost.created_at)}</p><h3 class="sidebar__post-title"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("blogs.show", recentPost.slug)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(recentPost.title)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(recentPost.title), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</h3></div></li>`);
            });
            _push2(`<!--]--></ul></div></div></div></div></div></section><section class="mt-5"${_scopeId}>`);
            if (relatedBlogs.value && relatedBlogs.value.length > 0) {
              _push2(`<div class="container"${_scopeId}><div class="related-blogs mt-20 mt-xs-10"${_scopeId}><h4 class="mb-5"${_scopeId}>${ssrInterpolate(trans("Related Blogs"))}:</h4><div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(relatedBlogs.value, (relatedBlog) => {
                _push2(`<div class="col-md-4 mb-30"${_scopeId}>`);
                _push2(ssrRenderComponent(_sfc_main$l, {
                  post: relatedBlog,
                  variant: "featured",
                  locale: locale.value,
                  "asset-path": asset_path.value,
                  "image-fallback-index": 1
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              });
              _push2(`<!--]--></div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Blog Details")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Blog Details")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "blog-details" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-8 col-lg-7" }, [
                      createVNode("div", { class: "blog-details__left" }, [
                        createVNode("div", { class: "blog-details__img" }, [
                          createVNode("img", {
                            src: blog.value.image_link,
                            alt: blog.value.title
                          }, null, 8, ["src", "alt"])
                        ]),
                        createVNode("div", { class: "blog-details__single-content" }, [
                          createVNode("ul", { class: "blog-details__meta list-unstyled" }, [
                            createVNode("li", null, [
                              createVNode(unref(Link), {
                                href: _ctx.route("blogs.show", blog.value.slug)
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { class: "far fa-calendar-alt" }),
                                  createTextVNode(toDisplayString(blog.value.created_at_formatted), 1)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ]),
                            createVNode("li", null, [
                              createVNode(unref(Link), null, {
                                default: withCtx(() => [
                                  createVNode("span", { class: "far fa-clock" }),
                                  createTextVNode(toDisplayString(blog.value.reading_time) + " " + toDisplayString(trans("min read")), 1)
                                ]),
                                _: 1
                              })
                            ])
                          ]),
                          createVNode("h3", { class: "blog-details__title" }, [
                            createVNode(unref(Link), {
                              href: _ctx.route("blogs.show", blog.value.slug)
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(blog.value.title), 1)
                              ]),
                              _: 1
                            }, 8, ["href"])
                          ]),
                          createVNode("div", {
                            class: "blog-details__text",
                            innerHTML: blog.value.content
                          }, null, 8, ["innerHTML"])
                        ]),
                        blog.value.keywords ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "blog-details__tag-and-share"
                        }, [
                          createVNode("div", { class: "blog-details__tag" }, [
                            createVNode("h3", { class: "blog-details__tag-title" }, toDisplayString(trans("Keywords")) + ":", 1),
                            createVNode("ul", { class: "blog-details__tag-list list-unstyled" }, [
                              (openBlock(true), createBlock(Fragment, null, renderList(getKeywords(blog.value.keywords), (keyword, index) => {
                                return openBlock(), createBlock("li", { key: index }, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("blogs.index", { search: keyword.trim() })
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(keyword.trim()), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ]);
                              }), 128))
                            ])
                          ]),
                          createVNode("div", { class: "blog-details__share-box" }, [
                            createVNode("h3", { class: "blog-details__share-title" }, toDisplayString(trans("Share On:")), 1),
                            createVNode("div", { class: "blog-details__share" }, [
                              createVNode("a", {
                                href: getShareUrl("facebook"),
                                target: "_blank"
                              }, [
                                createVNode("span", { class: "icon-facebook" })
                              ], 8, ["href"]),
                              createVNode("a", {
                                href: getShareUrl("twitter"),
                                target: "_blank"
                              }, [
                                createVNode("span", { class: "fab fa-twitter" })
                              ], 8, ["href"]),
                              createVNode("a", {
                                href: getShareUrl("linkedin"),
                                target: "_blank"
                              }, [
                                createVNode("span", { class: "icon-linkedin" })
                              ], 8, ["href"])
                            ])
                          ])
                        ])) : createCommentVNode("", true),
                        previousPost.value || nextPost.value ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "blog-details__prev-and-next"
                        }, [
                          previousPost.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "blog-details__prev-box"
                          }, [
                            createVNode("div", { class: "blog-details__prev-img" }, [
                              createVNode("img", {
                                src: previousPost.value.image_link,
                                alt: previousPost.value.title
                              }, null, 8, ["src", "alt"])
                            ]),
                            createVNode("div", { class: "blog-details__prev-content" }, [
                              createVNode("div", { class: "blog-details__prev-arrow" }, [
                                createVNode("span", { class: "icon-left-arrow" }),
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.show", previousPost.value.slug)
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(trans("Prev Blog")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ]),
                              createVNode("h4", { class: "blog-details__prev-title" }, toDisplayString(previousPost.value.title), 1)
                            ])
                          ])) : createCommentVNode("", true),
                          nextPost.value ? (openBlock(), createBlock("div", {
                            key: 1,
                            class: "blog-details__next-box"
                          }, [
                            createVNode("div", { class: "blog-details__next-content" }, [
                              createVNode("div", { class: "blog-details__next-arrow" }, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.show", nextPost.value.slug)
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(trans("Next Blog")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode("span", {
                                  class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow `
                                }, null, 2)
                              ]),
                              createVNode("h4", { class: "blog-details__next-title" }, toDisplayString(nextPost.value.title), 1)
                            ]),
                            createVNode("div", { class: "blog-details__next-img" }, [
                              createVNode("img", {
                                src: nextPost.value.image_link,
                                alt: nextPost.value.title
                              }, null, 8, ["src", "alt"])
                            ])
                          ])) : createCommentVNode("", true)
                        ])) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-4 col-lg-5" }, [
                      createVNode("div", { class: "sidebar" }, [
                        createVNode("div", { class: "sidebar__single sidebar__search" }, [
                          createVNode("div", { class: "sidebar__title-box" }, [
                            createVNode("div", { class: "sidebar__title-shape" }),
                            createVNode("h3", { class: "sidebar__title" }, toDisplayString(trans("Search")), 1)
                          ]),
                          createVNode("p", { class: "sidebar__search-text" }, toDisplayString(trans("Search blogs to discover a vast world of online content on countless topics.")), 1),
                          createVNode("form", {
                            onSubmit: withModifiers(handleSearch, ["prevent"]),
                            class: "sidebar__search-form"
                          }, [
                            withDirectives(createVNode("input", {
                              type: "search",
                              "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                              placeholder: trans("Search Blogs")
                            }, null, 8, ["onUpdate:modelValue", "placeholder"]), [
                              [vModelText, searchQuery.value]
                            ]),
                            createVNode("button", { type: "submit" }, [
                              createVNode("i", { class: "fa fa-search" })
                            ])
                          ], 32)
                        ]),
                        createVNode("div", { class: "sidebar__single sidebar__category" }, [
                          createVNode("div", { class: "sidebar__title-box" }, [
                            createVNode("div", { class: "sidebar__title-shape" }),
                            createVNode("h3", { class: "sidebar__title" }, toDisplayString(trans("Category")), 1)
                          ]),
                          createVNode("ul", { class: "sidebar__category-list list-unstyled" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(categories.value, (category) => {
                              return openBlock(), createBlock("li", {
                                key: category.id
                              }, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("blogs.index", { category: category.slug })
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(category.name) + " ", 1),
                                    createVNode("span", null, "(" + toDisplayString(category.blogs_count) + ")", 1)
                                  ]),
                                  _: 2
                                }, 1032, ["href"])
                              ]);
                            }), 128))
                          ])
                        ]),
                        createVNode("div", { class: "sidebar__single sidebar__post" }, [
                          createVNode("div", { class: "sidebar__title-box" }, [
                            createVNode("div", { class: "sidebar__title-shape" }),
                            createVNode("h3", { class: "sidebar__title" }, toDisplayString(trans("Recent Post")), 1)
                          ]),
                          createVNode("ul", { class: "sidebar__post-list list-unstyled" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(recentPosts.value, (recentPost) => {
                              return openBlock(), createBlock("li", {
                                key: recentPost.id
                              }, [
                                createVNode("div", { class: "sidebar__post-image" }, [
                                  createVNode("img", {
                                    src: recentPost.image_link,
                                    alt: recentPost.title
                                  }, null, 8, ["src", "alt"])
                                ]),
                                createVNode("div", { class: "sidebar__post-content" }, [
                                  createVNode("p", { class: "sidebar__post-date" }, [
                                    createVNode("span", { class: "icon-calendar" }),
                                    createTextVNode(toDisplayString(recentPost.created_at), 1)
                                  ]),
                                  createVNode("h3", { class: "sidebar__post-title" }, [
                                    createVNode(unref(Link), {
                                      href: _ctx.route("blogs.show", recentPost.slug)
                                    }, {
                                      default: withCtx(() => [
                                        createTextVNode(toDisplayString(recentPost.title), 1)
                                      ]),
                                      _: 2
                                    }, 1032, ["href"])
                                  ])
                                ])
                              ]);
                            }), 128))
                          ])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "mt-5" }, [
                relatedBlogs.value && relatedBlogs.value.length > 0 ? (openBlock(), createBlock("div", {
                  key: 0,
                  class: "container"
                }, [
                  createVNode("div", { class: "related-blogs mt-20 mt-xs-10" }, [
                    createVNode("h4", { class: "mb-5" }, toDisplayString(trans("Related Blogs")) + ":", 1),
                    createVNode("div", { class: "row" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(relatedBlogs.value, (relatedBlog) => {
                        return openBlock(), createBlock("div", {
                          key: relatedBlog.id,
                          class: "col-md-4 mb-30"
                        }, [
                          createVNode(_sfc_main$l, {
                            post: relatedBlog,
                            variant: "featured",
                            locale: locale.value,
                            "asset-path": asset_path.value,
                            "image-fallback-index": 1
                          }, null, 8, ["post", "locale", "asset-path"])
                        ]);
                      }), 128))
                    ])
                  ])
                ])) : createCommentVNode("", true)
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
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/BlogShow.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const __vite_glob_0_3 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$d
}, Symbol.toStringTag, { value: "Module" }));
const __default__$8 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$c = /* @__PURE__ */ Object.assign(__default__$8, {
  __name: "PageShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const custom_page = computed(() => page.props.custom_page);
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const banner = computed(() => page.props.banner);
    const metaTitle = computed(() => {
      var _a, _b;
      const pageTitle = ((_b = (_a = custom_page.value) == null ? void 0 : _a.title) == null ? void 0 : _b[locale.value]) || "";
      return `${pageTitle} | ${seo.value.website_name || ""}`.trim();
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${banner.value})` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(custom_page.value.title[locale.value])}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("home")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                  } else {
                    return [
                      createVNode("i", { class: "fas fa-home" }),
                      createTextVNode(toDisplayString(trans("Home")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}`)}${_scopeId}><i class="fas fa-home"${_scopeId}></i>${ssrInterpolate(trans("Home"))}</a>`);
            }
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(custom_page.value.title[locale.value])}</li></ul></div></div></div></section><div class="my-5"${_scopeId}><div class="container"${_scopeId}><div class="content mb-10"${_scopeId}><div${_scopeId}>${custom_page.value.content[locale.value] ?? ""}</div></div></div></div>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${banner.value})` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(custom_page.value.title[locale.value]), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}`
                          }, [
                            createVNode("i", { class: "fas fa-home" }),
                            createTextVNode(toDisplayString(trans("Home")), 1)
                          ], 8, ["href"]))
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(custom_page.value.title[locale.value]), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("div", { class: "my-5" }, [
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
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/PageShow.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const __vite_glob_0_4 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$c
}, Symbol.toStringTag, { value: "Module" }));
const __default__$7 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$b = /* @__PURE__ */ Object.assign(__default__$7, {
  __name: "PrivacyPolicy",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const asset_path = computed(() => page.props.asset_path || "");
    const settings2 = computed(() => page.props.settings || {});
    const locale = computed(() => page.props.locale);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return meta.value.title || `${trans("Privacy Policy")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Review how we collect, use, and protect your personal information.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("privacy policy, data protection, security, compliance") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")} data-v-b99b5a4e${_scopeId}><title data-v-b99b5a4e${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)} data-v-b99b5a4e${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)} data-v-b99b5a4e${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)} data-v-b99b5a4e${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)} data-v-b99b5a4e${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)} data-v-b99b5a4e${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)} data-v-b99b5a4e${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)} data-v-b99b5a4e${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)} data-v-b99b5a4e${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website" data-v-b99b5a4e${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-b99b5a4e${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)} data-v-b99b5a4e${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)} data-v-b99b5a4e${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)} data-v-b99b5a4e${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f;
          if (_push2) {
            _push2(`<section class="page-header" data-v-b99b5a4e${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` })}" data-v-b99b5a4e${_scopeId}></div><div class="container" data-v-b99b5a4e${_scopeId}><div class="page-header__inner" data-v-b99b5a4e${_scopeId}><h2 data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Privacy Policy"))}</h2><div class="thm-breadcrumb__box" data-v-b99b5a4e${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-b99b5a4e${_scopeId}><li data-v-b99b5a4e${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("home")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<i class="fas fa-home" data-v-b99b5a4e${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                  } else {
                    return [
                      createVNode("i", { class: "fas fa-home" }),
                      createTextVNode(toDisplayString(trans("Home")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}`)} data-v-b99b5a4e${_scopeId}><i class="fas fa-home" data-v-b99b5a4e${_scopeId}></i>${ssrInterpolate(trans("Home"))}</a>`);
            }
            _push2(`</li><li data-v-b99b5a4e${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}" data-v-b99b5a4e${_scopeId}></span></li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Privacy Policy"))}</li></ul></div></div></div></section><section class="privacy-policy my-5" data-v-b99b5a4e${_scopeId}><div class="container" data-v-b99b5a4e${_scopeId}><div class="row" data-v-b99b5a4e${_scopeId}><div class="col-xl-12" data-v-b99b5a4e${_scopeId}><div class="privacy-policy__content" data-v-b99b5a4e${_scopeId}><div class="privacy-policy__text" data-v-b99b5a4e${_scopeId}><p class="privacy-policy__last-updated" data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Last Updated:"))}</strong> ${ssrInterpolate((/* @__PURE__ */ new Date()).toLocaleDateString())}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("1. Introduction"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Welcome to our Privacy Policy. This document explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site."))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("2. Information We Collect"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("We may collect information about you in a variety of ways. The information we may collect on the site includes:"))}</p><ul data-v-b99b5a4e${_scopeId}><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Personal Data"))}</strong>: ${ssrInterpolate(trans("Personally identifiable information, such as your name, email address, phone number, and demographic information that you voluntarily give to us when you register with the site or when you choose to participate in various activities related to the site."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Derivative Data"))}</strong>: ${ssrInterpolate(trans("Information our servers automatically collect when you access the site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the site."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Financial Data"))}</strong>: ${ssrInterpolate(trans("Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the site."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Mobile Device Data"))}</strong>: ${ssrInterpolate(trans("Device information, such as your mobile device ID, model, and manufacturer, and information about the location of your device, if you access the site from a mobile device."))}</li></ul><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("3. How We Use Your Information"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the site to:"))}</p><ul data-v-b99b5a4e${_scopeId}><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Create and manage your account"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Process your transactions and send you related information"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Email you regarding your account or order"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Fulfill and manage purchases, orders, payments, and other transactions related to the site"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Generate a personal profile about you to make future visits more personalized"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Increase the efficiency and operation of the site"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Monitor and analyze usage and trends to improve your experience with the site"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Notify you of updates to the site"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Perform other business activities as needed"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Request feedback and contact you about your use of the site"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Resolve disputes and troubleshoot problems"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Respond to product and customer service requests"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Send you a newsletter"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Solicit support for the site"))}</li></ul><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("4. Disclosure of Your Information"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("We may share information we have collected about you in certain situations. Your information may be disclosed as follows:"))}</p><ul data-v-b99b5a4e${_scopeId}><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("By Law or to Protect Rights"))}</strong>: ${ssrInterpolate(trans("If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Third-Party Service Providers"))}</strong>: ${ssrInterpolate(trans("We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Business Transfers"))}</strong>: ${ssrInterpolate(trans("We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Affiliates"))}</strong>: ${ssrInterpolate(trans("We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include our parent company and any subsidiaries, joint venture partners, or other companies that we control or that are under common control with us."))}</li><li data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Business Partners"))}</strong>: ${ssrInterpolate(trans("We may share your information with our business partners to offer you certain products, services, or promotions."))}</li></ul><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("5. Security of Your Information"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse. Any information disclosed online is vulnerable to interception and misuse by unauthorized parties. Therefore, we cannot guarantee complete security if you provide personal information."))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("6. Policy for Children"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("We do not knowingly solicit information from or market to children under the age of 13. If we learn that we have collected personal information from a child under age 13 without verification of parental consent, we will delete that information as quickly as possible. If you become aware of any data we have collected from children under age 13, please contact us."))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("7. Your Rights"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Depending on your location, you may have the following rights regarding your personal information:"))}</p><ul data-v-b99b5a4e${_scopeId}><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to access – You have the right to request copies of your personal data"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to rectification – You have the right to request that we correct any information you believe is inaccurate"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to erasure – You have the right to request that we erase your personal data, under certain conditions"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions"))}</li><li data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions"))}</li></ul><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("8. Cookies and Tracking Technologies"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("We may use cookies, web beacons, tracking pixels, and other tracking technologies on the site to help customize the site and improve your experience. When you access the site, your personal information is not collected through the use of tracking technology. Most browsers are set to accept cookies by default. You can remove or reject cookies, but be aware that such action could affect the availability and functionality of the site."))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("9. Third-Party Websites"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("The site may contain links to third-party websites and applications of interest, including advertisements and external services, that are not affiliated with us. Once you have used these links to leave the site, any information you provide to these third parties is not covered by this Privacy Policy, and we cannot guarantee the safety and privacy of your information. Before visiting and providing any information to any third-party websites, you should inform yourself of the privacy policies and practices (if any) of the third party responsible for that website, and should take those steps necessary to, in your discretion, protect the privacy of your information."))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("10. Changes to This Privacy Policy"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans('We may update this Privacy Policy from time to time in order to reflect, for example, changes to our practices or for other operational, legal, or regulatory reasons. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.'))}</p><h3 class="privacy-policy__heading" data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("11. Contact Us"))}</h3><p data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("If you have questions or comments about this Privacy Policy, please contact us at:"))}</p><p data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Email:"))}</strong> ${ssrInterpolate((_a = settings2.value) == null ? void 0 : _a.email)}<br data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Phone:"))}</strong> ${ssrInterpolate((_b = settings2.value) == null ? void 0 : _b.phone)}<br data-v-b99b5a4e${_scopeId}><strong data-v-b99b5a4e${_scopeId}>${ssrInterpolate(trans("Address:"))}</strong> ${ssrInterpolate((_c = settings2.value) == null ? void 0 : _c.address)}</p></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Privacy Policy")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}`
                          }, [
                            createVNode("i", { class: "fas fa-home" }),
                            createTextVNode(toDisplayString(trans("Home")), 1)
                          ], 8, ["href"]))
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Privacy Policy")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "privacy-policy my-5" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "privacy-policy__content" }, [
                        createVNode("div", { class: "privacy-policy__text" }, [
                          createVNode("p", { class: "privacy-policy__last-updated" }, [
                            createVNode("strong", null, toDisplayString(trans("Last Updated:")), 1),
                            createTextVNode(" " + toDisplayString((/* @__PURE__ */ new Date()).toLocaleDateString()), 1)
                          ]),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("1. Introduction")), 1),
                          createVNode("p", null, toDisplayString(trans("Welcome to our Privacy Policy. This document explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site.")), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("2. Information We Collect")), 1),
                          createVNode("p", null, toDisplayString(trans("We may collect information about you in a variety of ways. The information we may collect on the site includes:")), 1),
                          createVNode("ul", null, [
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Personal Data")), 1),
                              createTextVNode(": " + toDisplayString(trans("Personally identifiable information, such as your name, email address, phone number, and demographic information that you voluntarily give to us when you register with the site or when you choose to participate in various activities related to the site.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Derivative Data")), 1),
                              createTextVNode(": " + toDisplayString(trans("Information our servers automatically collect when you access the site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the site.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Financial Data")), 1),
                              createTextVNode(": " + toDisplayString(trans("Financial information, such as data related to your payment method (e.g., valid credit card number, card brand, expiration date) that we may collect when you purchase, order, return, exchange, or request information about our services from the site.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Mobile Device Data")), 1),
                              createTextVNode(": " + toDisplayString(trans("Device information, such as your mobile device ID, model, and manufacturer, and information about the location of your device, if you access the site from a mobile device.")), 1)
                            ])
                          ]),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("3. How We Use Your Information")), 1),
                          createVNode("p", null, toDisplayString(trans("Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. Specifically, we may use information collected about you via the site to:")), 1),
                          createVNode("ul", null, [
                            createVNode("li", null, toDisplayString(trans("Create and manage your account")), 1),
                            createVNode("li", null, toDisplayString(trans("Process your transactions and send you related information")), 1),
                            createVNode("li", null, toDisplayString(trans("Email you regarding your account or order")), 1),
                            createVNode("li", null, toDisplayString(trans("Fulfill and manage purchases, orders, payments, and other transactions related to the site")), 1),
                            createVNode("li", null, toDisplayString(trans("Generate a personal profile about you to make future visits more personalized")), 1),
                            createVNode("li", null, toDisplayString(trans("Increase the efficiency and operation of the site")), 1),
                            createVNode("li", null, toDisplayString(trans("Monitor and analyze usage and trends to improve your experience with the site")), 1),
                            createVNode("li", null, toDisplayString(trans("Notify you of updates to the site")), 1),
                            createVNode("li", null, toDisplayString(trans("Perform other business activities as needed")), 1),
                            createVNode("li", null, toDisplayString(trans("Request feedback and contact you about your use of the site")), 1),
                            createVNode("li", null, toDisplayString(trans("Resolve disputes and troubleshoot problems")), 1),
                            createVNode("li", null, toDisplayString(trans("Respond to product and customer service requests")), 1),
                            createVNode("li", null, toDisplayString(trans("Send you a newsletter")), 1),
                            createVNode("li", null, toDisplayString(trans("Solicit support for the site")), 1)
                          ]),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("4. Disclosure of Your Information")), 1),
                          createVNode("p", null, toDisplayString(trans("We may share information we have collected about you in certain situations. Your information may be disclosed as follows:")), 1),
                          createVNode("ul", null, [
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("By Law or to Protect Rights")), 1),
                              createTextVNode(": " + toDisplayString(trans("If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Third-Party Service Providers")), 1),
                              createTextVNode(": " + toDisplayString(trans("We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Business Transfers")), 1),
                              createTextVNode(": " + toDisplayString(trans("We may share or transfer your information in connection with, or during negotiations of, any merger, sale of company assets, financing, or acquisition of all or a portion of our business to another company.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Affiliates")), 1),
                              createTextVNode(": " + toDisplayString(trans("We may share your information with our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include our parent company and any subsidiaries, joint venture partners, or other companies that we control or that are under common control with us.")), 1)
                            ]),
                            createVNode("li", null, [
                              createVNode("strong", null, toDisplayString(trans("Business Partners")), 1),
                              createTextVNode(": " + toDisplayString(trans("We may share your information with our business partners to offer you certain products, services, or promotions.")), 1)
                            ])
                          ]),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("5. Security of Your Information")), 1),
                          createVNode("p", null, toDisplayString(trans("We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse. Any information disclosed online is vulnerable to interception and misuse by unauthorized parties. Therefore, we cannot guarantee complete security if you provide personal information.")), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("6. Policy for Children")), 1),
                          createVNode("p", null, toDisplayString(trans("We do not knowingly solicit information from or market to children under the age of 13. If we learn that we have collected personal information from a child under age 13 without verification of parental consent, we will delete that information as quickly as possible. If you become aware of any data we have collected from children under age 13, please contact us.")), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("7. Your Rights")), 1),
                          createVNode("p", null, toDisplayString(trans("Depending on your location, you may have the following rights regarding your personal information:")), 1),
                          createVNode("ul", null, [
                            createVNode("li", null, toDisplayString(trans("The right to access – You have the right to request copies of your personal data")), 1),
                            createVNode("li", null, toDisplayString(trans("The right to rectification – You have the right to request that we correct any information you believe is inaccurate")), 1),
                            createVNode("li", null, toDisplayString(trans("The right to erasure – You have the right to request that we erase your personal data, under certain conditions")), 1),
                            createVNode("li", null, toDisplayString(trans("The right to restrict processing – You have the right to request that we restrict the processing of your personal data, under certain conditions")), 1),
                            createVNode("li", null, toDisplayString(trans("The right to object to processing – You have the right to object to our processing of your personal data, under certain conditions")), 1),
                            createVNode("li", null, toDisplayString(trans("The right to data portability – You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions")), 1)
                          ]),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("8. Cookies and Tracking Technologies")), 1),
                          createVNode("p", null, toDisplayString(trans("We may use cookies, web beacons, tracking pixels, and other tracking technologies on the site to help customize the site and improve your experience. When you access the site, your personal information is not collected through the use of tracking technology. Most browsers are set to accept cookies by default. You can remove or reject cookies, but be aware that such action could affect the availability and functionality of the site.")), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("9. Third-Party Websites")), 1),
                          createVNode("p", null, toDisplayString(trans("The site may contain links to third-party websites and applications of interest, including advertisements and external services, that are not affiliated with us. Once you have used these links to leave the site, any information you provide to these third parties is not covered by this Privacy Policy, and we cannot guarantee the safety and privacy of your information. Before visiting and providing any information to any third-party websites, you should inform yourself of the privacy policies and practices (if any) of the third party responsible for that website, and should take those steps necessary to, in your discretion, protect the privacy of your information.")), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("10. Changes to This Privacy Policy")), 1),
                          createVNode("p", null, toDisplayString(trans('We may update this Privacy Policy from time to time in order to reflect, for example, changes to our practices or for other operational, legal, or regulatory reasons. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes.')), 1),
                          createVNode("h3", { class: "privacy-policy__heading" }, toDisplayString(trans("11. Contact Us")), 1),
                          createVNode("p", null, toDisplayString(trans("If you have questions or comments about this Privacy Policy, please contact us at:")), 1),
                          createVNode("p", null, [
                            createVNode("strong", null, toDisplayString(trans("Email:")), 1),
                            createTextVNode(" " + toDisplayString((_d = settings2.value) == null ? void 0 : _d.email), 1),
                            createVNode("br"),
                            createVNode("strong", null, toDisplayString(trans("Phone:")), 1),
                            createTextVNode(" " + toDisplayString((_e = settings2.value) == null ? void 0 : _e.phone), 1),
                            createVNode("br"),
                            createVNode("strong", null, toDisplayString(trans("Address:")), 1),
                            createTextVNode(" " + toDisplayString((_f = settings2.value) == null ? void 0 : _f.address), 1)
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
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/PrivacyPolicy.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const PrivacyPolicy = /* @__PURE__ */ _export_sfc(_sfc_main$b, [["__scopeId", "data-v-b99b5a4e"]]);
const __vite_glob_0_5 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: PrivacyPolicy
}, Symbol.toStringTag, { value: "Module" }));
const __default__$6 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$a = /* @__PURE__ */ Object.assign(__default__$6, {
  __name: "Team",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const teams = computed(() => page.props.teams || []);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return meta.value.title || `${trans("Our Members")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Meet the professionals behind our technology and consulting services.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("team, experts, leadership, professionals") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
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
    onMounted(() => {
      nextTick(() => {
        if (typeof WOW !== "undefined") {
          new WOW().init();
        }
      });
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}><title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/our-team-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("Our Members"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("home")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                  } else {
                    return [
                      createVNode("i", { class: "fas fa-home" }),
                      createTextVNode(toDisplayString(trans("Home")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}`)}${_scopeId}><i class="fas fa-home"${_scopeId}></i>${ssrInterpolate(trans("Home"))}</a>`);
            }
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("Our Members"))}</li></ul></div></div></div></section><section class="team-page my-5"${_scopeId}><div class="container"${_scopeId}>`);
            if (teams.value && teams.value.length > 0) {
              _push2(`<div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(teams.value, (team, index) => {
                _push2(`<div class="col-xl-3 col-lg-6 col-md-6 wow fadeInLeft"${ssrRenderAttr("data-wow-delay", `${index % 4 * 100}ms`)}${_scopeId}><div class="team-one__single"${_scopeId}><div class="team-one__img-box"${_scopeId}><div class="team-one__img"${_scopeId}><img${ssrRenderAttr("src", team.avatar_link)}${ssrRenderAttr("alt", translateField(team.name))}${_scopeId}></div><div class="team-one__social-box-inner"${_scopeId}><div class="team-one__social-box"${_scopeId}><div class="team-one__social"${_scopeId}>`);
                if (team.facebook) {
                  _push2(`<a${ssrRenderAttr("href", team.facebook)} target="_blank" aria-label="Facebook"${_scopeId}><span class="icon-facebook"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.behance) {
                  _push2(`<a${ssrRenderAttr("href", team.behance)} target="_blank" aria-label="Behance"${_scopeId}><span class="icon-dribble"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div><div class="team-one__social"${_scopeId}>`);
                if (team.linked_in) {
                  _push2(`<a${ssrRenderAttr("href", team.linked_in)} target="_blank" aria-label="LinkedIn"${_scopeId}><span class="icon-linkedin"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                if (team.github) {
                  _push2(`<a${ssrRenderAttr("href", team.github)} target="_blank" aria-label="GitHub"${_scopeId}><span class="icon-github"${_scopeId}></span></a>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div></div></div><div class="team-one__title-box"${_scopeId}><h3${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(team.name))}</a></h3><p${_scopeId}>${ssrInterpolate(translateField(team.position))}</p></div></div></div></div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="text-center py-5"${_scopeId}><p${_scopeId}>${ssrInterpolate(trans("No team members found."))}</p></div>`);
            }
            _push2(`</div></section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/our-team-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Our Members")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}`
                          }, [
                            createVNode("i", { class: "fas fa-home" }),
                            createTextVNode(toDisplayString(trans("Home")), 1)
                          ], 8, ["href"]))
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Our Members")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "team-page my-5" }, [
                createVNode("div", { class: "container" }, [
                  teams.value && teams.value.length > 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "row"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(teams.value, (team, index) => {
                      return openBlock(), createBlock("div", {
                        class: "col-xl-3 col-lg-6 col-md-6 wow fadeInLeft",
                        key: team.id,
                        "data-wow-delay": `${index % 4 * 100}ms`
                      }, [
                        createVNode("div", { class: "team-one__single" }, [
                          createVNode("div", { class: "team-one__img-box" }, [
                            createVNode("div", { class: "team-one__img" }, [
                              createVNode("img", {
                                src: team.avatar_link,
                                alt: translateField(team.name)
                              }, null, 8, ["src", "alt"])
                            ]),
                            createVNode("div", { class: "team-one__social-box-inner" }, [
                              createVNode("div", { class: "team-one__social-box" }, [
                                createVNode("div", { class: "team-one__social" }, [
                                  team.facebook ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    href: team.facebook,
                                    target: "_blank",
                                    "aria-label": "Facebook"
                                  }, [
                                    createVNode("span", { class: "icon-facebook" })
                                  ], 8, ["href"])) : createCommentVNode("", true),
                                  team.behance ? (openBlock(), createBlock("a", {
                                    key: 1,
                                    href: team.behance,
                                    target: "_blank",
                                    "aria-label": "Behance"
                                  }, [
                                    createVNode("span", { class: "icon-dribble" })
                                  ], 8, ["href"])) : createCommentVNode("", true)
                                ]),
                                createVNode("div", { class: "team-one__social" }, [
                                  team.linked_in ? (openBlock(), createBlock("a", {
                                    key: 0,
                                    href: team.linked_in,
                                    target: "_blank",
                                    "aria-label": "LinkedIn"
                                  }, [
                                    createVNode("span", { class: "icon-linkedin" })
                                  ], 8, ["href"])) : createCommentVNode("", true),
                                  team.github ? (openBlock(), createBlock("a", {
                                    key: 1,
                                    href: team.github,
                                    target: "_blank",
                                    "aria-label": "GitHub"
                                  }, [
                                    createVNode("span", { class: "icon-github" })
                                  ], 8, ["href"])) : createCommentVNode("", true)
                                ])
                              ])
                            ]),
                            createVNode("div", { class: "team-one__title-box" }, [
                              createVNode("h3", null, [
                                createVNode("a", { href: "#" }, toDisplayString(translateField(team.name)), 1)
                              ]),
                              createVNode("p", null, toDisplayString(translateField(team.position)), 1)
                            ])
                          ])
                        ])
                      ], 8, ["data-wow-delay"]);
                    }), 128))
                  ])) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "text-center py-5"
                  }, [
                    createVNode("p", null, toDisplayString(trans("No team members found.")), 1)
                  ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/Team.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const __vite_glob_0_6 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$a
}, Symbol.toStringTag, { value: "Module" }));
const __default__$5 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$9 = /* @__PURE__ */ Object.assign(__default__$5, {
  __name: "Testimonials",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale);
    const testimonials = computed(() => page.props.testimonials || []);
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return `${trans("Testimonials")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Read what our clients say about working with our team.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("testimonials, reviews, client feedback, success stories") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/testimonial.css")}${_scopeId}><title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/testimonial.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            if (typeof _ctx.route !== "undefined") {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("home")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                  } else {
                    return [
                      createVNode("i", { class: "fas fa-home" }),
                      createTextVNode(toDisplayString(trans("Home")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<a${ssrRenderAttr("href", `/${locale.value === "ar" ? "ar" : ""}`)}${_scopeId}><i class="fas fa-home"${_scopeId}></i>${ssrInterpolate(trans("Home"))}</a>`);
            }
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</li></ul></div></div></div></section><section class="testimonials-page"${_scopeId}><div class="container"${_scopeId}>`);
            if (testimonials.value && testimonials.value.length > 0) {
              _push2(`<div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(testimonials.value, (testimonial) => {
                _push2(`<div class="col-xl-4 col-lg-6 col-md-6"${_scopeId}><div class="testimonial-two__single"${_scopeId}><div class="testimonial-two__single-inner"${_scopeId}><div class="testimonial-two__star"${_scopeId}><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span></div><p class="testimonial-two__text"${_scopeId}>${ssrInterpolate(translateField(testimonial.quote))}</p></div><div class="testimonial-two__client-info"${_scopeId}><div class="testimonial-two__client-img"${_scopeId}><img${ssrRenderAttr("src", testimonial.avatar_link)}${ssrRenderAttr("alt", translateField(testimonial.name))}${_scopeId}></div><div class="testimonial-two__client-content"${_scopeId}><h4 class="testimonial-two__client-name"${_scopeId}><a href="#"${_scopeId}>${ssrInterpolate(translateField(testimonial.name))}</a></h4><p class="testimonial-two__sub-title"${_scopeId}>${ssrInterpolate(translateField(testimonial.position))}</p></div></div><div class="testimonial-two__quote"${_scopeId}><span class="icon-right-quote"${_scopeId}></span></div></div></div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="text-center py-5"${_scopeId}><p${_scopeId}>${ssrInterpolate(trans("No testimonials found."))}</p></div>`);
            }
            _push2(`</div></section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/contact-header-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Testimonials")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          typeof _ctx.route !== "undefined" ? (openBlock(), createBlock(unref(Link), {
                            key: 0,
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])) : (openBlock(), createBlock("a", {
                            key: 1,
                            href: `/${locale.value === "ar" ? "ar" : ""}`
                          }, [
                            createVNode("i", { class: "fas fa-home" }),
                            createTextVNode(toDisplayString(trans("Home")), 1)
                          ], 8, ["href"]))
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Testimonials")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "testimonials-page" }, [
                createVNode("div", { class: "container" }, [
                  testimonials.value && testimonials.value.length > 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "row"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(testimonials.value, (testimonial) => {
                      return openBlock(), createBlock("div", {
                        class: "col-xl-4 col-lg-6 col-md-6",
                        key: testimonial.id
                      }, [
                        createVNode("div", { class: "testimonial-two__single" }, [
                          createVNode("div", { class: "testimonial-two__single-inner" }, [
                            createVNode("div", { class: "testimonial-two__star" }, [
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" }),
                              createVNode("span", { class: "icon-pointed-star" })
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
                              createVNode("h4", { class: "testimonial-two__client-name" }, [
                                createVNode("a", { href: "#" }, toDisplayString(translateField(testimonial.name)), 1)
                              ]),
                              createVNode("p", { class: "testimonial-two__sub-title" }, toDisplayString(translateField(testimonial.position)), 1)
                            ])
                          ]),
                          createVNode("div", { class: "testimonial-two__quote" }, [
                            createVNode("span", { class: "icon-right-quote" })
                          ])
                        ])
                      ]);
                    }), 128))
                  ])) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "text-center py-5"
                  }, [
                    createVNode("p", null, toDisplayString(trans("No testimonials found.")), 1)
                  ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Cms/resources/assets/js/Pages/Testimonials.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const __vite_glob_0_7 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$9
}, Symbol.toStringTag, { value: "Module" }));
const __default__$4 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$8 = /* @__PURE__ */ Object.assign(__default__$4, {
  __name: "ServiceIndex",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const categories = computed(() => page.props.categories || []);
    const filters = computed(() => page.props.filters || {});
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return `${trans("Our Services")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Discover our IT services designed to scale and modernize your business.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("IT services, web development, mobile apps, AI solutions, cloud services") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
    const services = computed(() => {
      const source = page.props.services || { data: [], links: [], last_page: 1 };
      const data = Array.isArray(source.data) ? source.data.filter((service) => service && service.id) : [];
      return {
        ...source,
        data
      };
    });
    const translateField = (value) => {
      if (!value) return "";
      if (typeof value === "string") return value;
      if (typeof value === "object" && value !== null) {
        return value[locale.value] || value["en"] || value[Object.keys(value)[0]] || "";
      }
      return "";
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
    const getServiceTitle = (service) => {
      return translateField(service == null ? void 0 : service.title);
    };
    const getCategoryName = (category) => {
      return translateField(category == null ? void 0 : category.title);
    };
    const normalizeKeywords = (rawKeywords) => {
      if (!rawKeywords) {
        return [];
      }
      let parsed = rawKeywords;
      if (typeof rawKeywords === "string") {
        try {
          parsed = JSON.parse(rawKeywords);
        } catch (e) {
          parsed = rawKeywords;
        }
      }
      if (Array.isArray(parsed)) {
        return parsed.map((item) => {
          if (typeof item === "string") {
            return item;
          }
          if (item && typeof item === "object") {
            if (item.value) {
              return translateField(item.value);
            }
            return translateField(item);
          }
          return "";
        }).map((item) => item == null ? void 0 : item.toString().trim()).filter(Boolean);
      }
      if (typeof parsed === "object") {
        const value = translateField(parsed);
        return value ? [value] : [];
      }
      return parsed.toString().split(/[,;\n]+/).map((item) => item.trim()).filter(Boolean);
    };
    const getServiceHighlights = (service) => {
      const keywords = normalizeKeywords(service == null ? void 0 : service.keywords);
      if (keywords.length) {
        return keywords;
      }
      return [
        trans("Web Development"),
        trans("App Development"),
        trans("Graphics Design")
      ];
    };
    const chunkHighlights = (items, size = 2, maxItems = 6) => {
      const safeItems = Array.isArray(items) ? items.slice(0, maxItems) : [];
      const chunks = [];
      for (let i = 0; i < safeItems.length; i += size) {
        chunks.push(safeItems.slice(i, i + size));
      }
      return chunks;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")} data-v-98c061f6${_scopeId}><title data-v-98c061f6${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)} data-v-98c061f6${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)} data-v-98c061f6${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)} data-v-98c061f6${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)} data-v-98c061f6${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)} data-v-98c061f6${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)} data-v-98c061f6${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)} data-v-98c061f6${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)} data-v-98c061f6${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website" data-v-98c061f6${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-98c061f6${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)} data-v-98c061f6${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)} data-v-98c061f6${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)} data-v-98c061f6${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="page-header" data-v-98c061f6${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/services-bg.jpg)` })}" data-v-98c061f6${_scopeId}></div><div class="container" data-v-98c061f6${_scopeId}><div class="page-header__inner" data-v-98c061f6${_scopeId}><h2 data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("Our Services"))}</h2><div class="thm-breadcrumb__box" data-v-98c061f6${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-98c061f6${_scopeId}><li data-v-98c061f6${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home" data-v-98c061f6${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li data-v-98c061f6${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}" data-v-98c061f6${_scopeId}></span></li><li data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("Our Services"))}</li></ul></div></div></div></div><section class="services-two" data-v-98c061f6${_scopeId}><div class="container" data-v-98c061f6${_scopeId}><div class="services-two__top" data-v-98c061f6${_scopeId}><div class="section-title text-left sec-title-animation animation-style2" data-v-98c061f6${_scopeId}><div class="section-title__tagline-box" data-v-98c061f6${_scopeId}><div class="section-title__tagline-shape-1" data-v-98c061f6${_scopeId}></div><span class="section-title__tagline" data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("Our Services"))}</span></div><h2 class="section-title__title title-animation" data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("Scale Your Business Smarter with Next-Gen IT Solutions"))} <span data-v-98c061f6${_scopeId}></span></h2></div></div><div class="services-two__bottom" data-v-98c061f6${_scopeId}><div class="row" data-v-98c061f6${_scopeId}><div class="col-xl-8" data-v-98c061f6${_scopeId}><div class="services-two__services-list" data-v-98c061f6${_scopeId}><!--[-->`);
            ssrRenderList(services.value.data, (serviceItem, index) => {
              _push2(`<div class="services-two__services-list-single" data-v-98c061f6${_scopeId}><div class="services-two__count-and-title" data-v-98c061f6${_scopeId}><div class="services-two__count" data-v-98c061f6${_scopeId}></div><h3 class="services-two__title" data-v-98c061f6${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: getServiceUrl(serviceItem)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(getServiceTitle(serviceItem))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(getServiceTitle(serviceItem)), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</h3></div><div class="services-two__service-list-box" data-v-98c061f6${_scopeId}><ul class="services-two__services-list-inner list-unstyled" data-v-98c061f6${_scopeId}><!--[-->`);
              ssrRenderList(chunkHighlights(getServiceHighlights(serviceItem)), (chunk, chunkIndex) => {
                _push2(`<li data-v-98c061f6${_scopeId}><!--[-->`);
                ssrRenderList(chunk, (item, itemIndex) => {
                  _push2(`<p data-v-98c061f6${_scopeId}><span class="icon-plus" data-v-98c061f6${_scopeId}></span>${ssrInterpolate(item)}</p>`);
                });
                _push2(`<!--]--></li>`);
              });
              _push2(`<!--]--></ul></div></div>`);
            });
            _push2(`<!--]-->`);
            if (!services.value.data.length) {
              _push2(`<div class="text-center py-5" data-v-98c061f6${_scopeId}><h3 class="text-muted" data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("No services found"))}</h3></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (services.value.last_page > 1) {
              _push2(`<div class="blog-page__pagination services-pagination" data-v-98c061f6${_scopeId}><ul class="pg-pagination list-unstyled" data-v-98c061f6${_scopeId}>`);
              if (services.value.prev_page_url) {
                _push2(`<li class="prev" data-v-98c061f6${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: services.value.prev_page_url,
                  "aria-label": "prev"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<span class="icon-left-arrow-1" data-v-98c061f6${_scopeId2}></span>`);
                    } else {
                      return [
                        createVNode("span", { class: "icon-left-arrow-1" })
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</li>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<!--[-->`);
              ssrRenderList(services.value.links, (link, linkIndex) => {
                _push2(`<!--[-->`);
                if (link.url && linkIndex > 0 && linkIndex < services.value.links.length - 1) {
                  _push2(`<li class="${ssrRenderClass(["count", link.active ? "active" : ""])}" data-v-98c061f6${_scopeId}>`);
                  _push2(ssrRenderComponent(unref(Link), {
                    href: link.url
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
                  _push2(`</li>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<!--]-->`);
              });
              _push2(`<!--]-->`);
              if (services.value.next_page_url) {
                _push2(`<li class="next" data-v-98c061f6${_scopeId}>`);
                _push2(ssrRenderComponent(unref(Link), {
                  href: services.value.next_page_url,
                  "aria-label": "Next"
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}" data-v-98c061f6${_scopeId2}></span>`);
                    } else {
                      return [
                        createVNode("span", {
                          class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                        }, null, 2)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
                _push2(`</li>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</ul></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-4" data-v-98c061f6${_scopeId}><div class="services-index__sidebar" data-v-98c061f6${_scopeId}><div class="services-details__services-list-box" data-v-98c061f6${_scopeId}><h3 class="services-details__services-list-title" data-v-98c061f6${_scopeId}>${ssrInterpolate(trans("Service Categories"))}</h3><ul class="services-details__services-list list-unstyled" data-v-98c061f6${_scopeId}><li data-v-98c061f6${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("services.index"),
              class: { "active": !filters.value.category }
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("All Services"))} <span class="icon-right-arrow-2" data-v-98c061f6${_scopeId2}></span>`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("All Services")) + " ", 1),
                    createVNode("span", { class: "icon-right-arrow-2" })
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><!--[-->`);
            ssrRenderList(categories.value, (category) => {
              _push2(`<li data-v-98c061f6${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("services.index", { category: category.slug }),
                class: { "active": filters.value.category === category.slug }
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(getCategoryName(category))} <span class="icon-right-arrow-2" data-v-98c061f6${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(getCategoryName(category)) + " ", 1),
                      createVNode("span", { class: "icon-right-arrow-2" })
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</li>`);
            });
            _push2(`<!--]--></ul></div></div></div></div></div></div></section>`);
          } else {
            return [
              createVNode("div", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/services-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Our Services")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Our Services")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "services-two" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "services-two__top" }, [
                    createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                      createVNode("div", { class: "section-title__tagline-box" }, [
                        createVNode("div", { class: "section-title__tagline-shape-1" }),
                        createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Our Services")), 1)
                      ]),
                      createVNode("h2", { class: "section-title__title title-animation" }, [
                        createTextVNode(toDisplayString(trans("Scale Your Business Smarter with Next-Gen IT Solutions")) + " ", 1),
                        createVNode("span")
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "services-two__bottom" }, [
                    createVNode("div", { class: "row" }, [
                      createVNode("div", { class: "col-xl-8" }, [
                        createVNode("div", { class: "services-two__services-list" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(services.value.data, (serviceItem, index) => {
                            return openBlock(), createBlock("div", {
                              key: serviceItem.id,
                              class: "services-two__services-list-single"
                            }, [
                              createVNode("div", { class: "services-two__count-and-title" }, [
                                createVNode("div", { class: "services-two__count" }),
                                createVNode("h3", { class: "services-two__title" }, [
                                  createVNode(unref(Link), {
                                    href: getServiceUrl(serviceItem)
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(getServiceTitle(serviceItem)), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ])
                              ]),
                              createVNode("div", { class: "services-two__service-list-box" }, [
                                createVNode("ul", { class: "services-two__services-list-inner list-unstyled" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList(chunkHighlights(getServiceHighlights(serviceItem)), (chunk, chunkIndex) => {
                                    return openBlock(), createBlock("li", {
                                      key: `${serviceItem.id}-chunk-${chunkIndex}`
                                    }, [
                                      (openBlock(true), createBlock(Fragment, null, renderList(chunk, (item, itemIndex) => {
                                        return openBlock(), createBlock("p", {
                                          key: `${serviceItem.id}-${chunkIndex}-${itemIndex}`
                                        }, [
                                          createVNode("span", { class: "icon-plus" }),
                                          createTextVNode(toDisplayString(item), 1)
                                        ]);
                                      }), 128))
                                    ]);
                                  }), 128))
                                ])
                              ])
                            ]);
                          }), 128)),
                          !services.value.data.length ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "text-center py-5"
                          }, [
                            createVNode("h3", { class: "text-muted" }, toDisplayString(trans("No services found")), 1)
                          ])) : createCommentVNode("", true)
                        ]),
                        services.value.last_page > 1 ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "blog-page__pagination services-pagination"
                        }, [
                          createVNode("ul", { class: "pg-pagination list-unstyled" }, [
                            services.value.prev_page_url ? (openBlock(), createBlock("li", {
                              key: 0,
                              class: "prev"
                            }, [
                              createVNode(unref(Link), {
                                href: services.value.prev_page_url,
                                "aria-label": "prev"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { class: "icon-left-arrow-1" })
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ])) : createCommentVNode("", true),
                            (openBlock(true), createBlock(Fragment, null, renderList(services.value.links, (link, linkIndex) => {
                              return openBlock(), createBlock(Fragment, { key: linkIndex }, [
                                link.url && linkIndex > 0 && linkIndex < services.value.links.length - 1 ? (openBlock(), createBlock("li", {
                                  key: 0,
                                  class: ["count", link.active ? "active" : ""]
                                }, [
                                  createVNode(unref(Link), {
                                    href: link.url
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(link.label), 1)
                                    ]),
                                    _: 2
                                  }, 1032, ["href"])
                                ], 2)) : createCommentVNode("", true)
                              ], 64);
                            }), 128)),
                            services.value.next_page_url ? (openBlock(), createBlock("li", {
                              key: 1,
                              class: "next"
                            }, [
                              createVNode(unref(Link), {
                                href: services.value.next_page_url,
                                "aria-label": "Next"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", {
                                    class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                                  }, null, 2)
                                ]),
                                _: 1
                              }, 8, ["href"])
                            ])) : createCommentVNode("", true)
                          ])
                        ])) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "col-xl-4" }, [
                        createVNode("div", { class: "services-index__sidebar" }, [
                          createVNode("div", { class: "services-details__services-list-box" }, [
                            createVNode("h3", { class: "services-details__services-list-title" }, toDisplayString(trans("Service Categories")), 1),
                            createVNode("ul", { class: "services-details__services-list list-unstyled" }, [
                              createVNode("li", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("services.index"),
                                  class: { "active": !filters.value.category }
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(trans("All Services")) + " ", 1),
                                    createVNode("span", { class: "icon-right-arrow-2" })
                                  ]),
                                  _: 1
                                }, 8, ["href", "class"])
                              ]),
                              (openBlock(true), createBlock(Fragment, null, renderList(categories.value, (category) => {
                                return openBlock(), createBlock("li", {
                                  key: category.id
                                }, [
                                  createVNode(unref(Link), {
                                    href: _ctx.route("services.index", { category: category.slug }),
                                    class: { "active": filters.value.category === category.slug }
                                  }, {
                                    default: withCtx(() => [
                                      createTextVNode(toDisplayString(getCategoryName(category)) + " ", 1),
                                      createVNode("span", { class: "icon-right-arrow-2" })
                                    ]),
                                    _: 2
                                  }, 1032, ["href", "class"])
                                ]);
                              }), 128))
                            ])
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
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Services/resources/assets/js/Pages/ServiceIndex.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const ServiceIndex = /* @__PURE__ */ _export_sfc(_sfc_main$8, [["__scopeId", "data-v-98c061f6"]]);
const __vite_glob_0_8 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ServiceIndex
}, Symbol.toStringTag, { value: "Module" }));
const __default__$3 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$7 = /* @__PURE__ */ Object.assign(__default__$3, {
  __name: "ServiceShow",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const service = computed(() => page.props.service);
    const relatedServices = computed(() => page.props.relatedServices || []);
    const recentServices = computed(() => page.props.recentServices || []);
    const testimonials = computed(() => page.props.testimonials || []);
    const meta = computed(() => page.props.meta || {});
    computed(() => {
      return meta.value.title || `${getServiceTitle(service.value)} | ${seo.value.website_name || ""}`.trim();
    });
    computed(() => {
      return meta.value.description || getServiceDescription(service.value) || seo.value.website_desc || "";
    });
    computed(() => {
      var _a;
      return meta.value.keywords || ((_a = service.value) == null ? void 0 : _a.keywords) || seo.value.website_keywords || "";
    });
    computed(() => {
      var _a, _b, _c, _d, _e, _f;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = service.value) == null ? void 0 : _e.image_link) || ((_f = settings2.value) == null ? void 0 : _f.meta_img) || "";
    });
    computed(() => meta.value.canonical || "");
    computed(() => meta.value.robots || "index, follow");
    const translateField = (value) => {
      if (!value) return "";
      if (typeof value === "string") return value;
      if (typeof value === "object" && value !== null) {
        return value[locale.value] || value["en"] || value[Object.keys(value)[0]] || "";
      }
      return "";
    };
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
      return translateField(serviceItem == null ? void 0 : serviceItem.title);
    };
    const getServiceDescription = (serviceItem) => {
      return translateField(serviceItem == null ? void 0 : serviceItem.description);
    };
    const normalizeKeywords = (rawKeywords) => {
      if (!rawKeywords) {
        return [];
      }
      let parsed = rawKeywords;
      if (typeof rawKeywords === "string") {
        const trimmed = rawKeywords.trim();
        if (trimmed.startsWith("{") || trimmed.startsWith("[")) {
          try {
            parsed = JSON.parse(trimmed);
          } catch (e) {
            try {
              parsed = JSON.parse(trimmed.replace(/'/g, '"'));
            } catch (err) {
              parsed = rawKeywords;
            }
          }
        }
      }
      if (Array.isArray(parsed)) {
        return parsed.map((item) => {
          if (typeof item === "string") {
            return item;
          }
          if (item && typeof item === "object") {
            if (item.value) {
              return translateField(item.value);
            }
            return translateField(item);
          }
          return "";
        }).map((item) => String(item).trim()).filter(Boolean);
      }
      if (typeof parsed === "object") {
        const value = translateField(parsed);
        return value ? [value] : [];
      }
      return parsed.toString().split(/[,;\n]+/).map((item) => item.trim()).filter(Boolean);
    };
    const getServiceHighlights = (serviceItem) => {
      const keywords = normalizeKeywords(translateField(serviceItem == null ? void 0 : serviceItem.keywords));
      if (keywords.length) {
        return keywords;
      }
      return [
        trans("Web Development"),
        trans("App Development"),
        trans("Graphics Design"),
        trans("Performance Audits"),
        trans("Customer Insights"),
        trans("Continuous Improvement")
      ];
    };
    onMounted(() => {
      nextTick(() => {
        if (typeof $ !== "undefined" && $(".testimonial-two__carousel").length && testimonials.value.length) {
          $(".testimonial-two__carousel").owlCarousel({
            loop: testimonials.value.length > 1,
            margin: 30,
            nav: false,
            dots: true,
            smartSpeed: 500,
            autoplay: true,
            autoplayTimeout: 7e3,
            rtl: locale.value === "ar",
            responsive: {
              0: { items: 1 },
              768: { items: 1 },
              992: { items: 1 },
              1200: { items: 1 }
            }
          });
        }
        if (typeof WOW !== "undefined") {
          new WOW().init();
        }
        if (typeof gsap !== "undefined" && typeof SplitText !== "undefined") {
          const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation");
          if (titleAnimations.length) {
            titleAnimations.forEach((quote) => {
              new SplitText(quote, { type: "lines" });
              let split = new SplitText(quote, { type: "lines" });
              gsap.from(split.lines, {
                duration: 1,
                y: 100,
                opacity: 0,
                stagger: 0.1,
                scrollTrigger: {
                  trigger: quote,
                  start: "top 90%",
                  toggleActions: "play none none none"
                }
              });
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
            _push2(`<title${_scopeId}>${ssrInterpolate(service.value.title)}</title><link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, toDisplayString(service.value.title), 1),
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/services-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>`);
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
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</li></ul></div></div></div></div><section class="services-details"${_scopeId}><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-4 col-lg-5"${_scopeId}><div class="services-details__left"${_scopeId}><div class="services-details__services-list-box"${_scopeId}><h3 class="services-details__services-list-title"${_scopeId}>${ssrInterpolate(trans("Service Categories"))}</h3><ul class="services-details__services-list list-unstyled"${_scopeId}><!--[-->`);
            ssrRenderList(recentServices.value.slice(0, 6), (recentService) => {
              _push2(`<li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: getServiceUrl(recentService),
                class: { "active": recentService.id === service.value.id }
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(getServiceTitle(recentService))} <span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-2`)}"${_scopeId2}></span>`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(getServiceTitle(recentService)) + " ", 1),
                      createVNode("span", {
                        class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-2`
                      }, null, 2)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</li>`);
            });
            _push2(`<!--]--></ul></div><div class="services-details__need-help"${_scopeId}><div class="services-details__need-help-img"${_scopeId}><img${ssrRenderAttr("src", asset_path.value + "images/need_help.jpg")}${ssrRenderAttr("alt", trans("Need help"))}${_scopeId}><div class="services-details__need-help-content"${_scopeId}><div class="services-details__need-help-bdr"${_scopeId}></div><h3 class="services-details__need-help-title"${_scopeId}>${ssrInterpolate(trans("Need Help?"))}</h3><p class="services-details__need-help-number"${_scopeId}><a dir="ltr"${ssrRenderAttr("href", `tel:${settings2.value.website_phone || settings2.value.phone}`)}${_scopeId}>${ssrInterpolate(settings2.value.website_phone || settings2.value.phone || "+12 (00) 345 789034")}</a></p></div></div></div></div></div><div class="col-xl-8 col-lg-7"${_scopeId}><div class="services-details__right"${_scopeId}><h3 class="services-details__title-1"${_scopeId}>${ssrInterpolate(getServiceTitle(service.value))}</h3><div class="services-details__bdr"${_scopeId}></div>`);
            if (service.value.reading_time) {
              _push2(`<ul class="blog-details__meta list-unstyled"${_scopeId}><li${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="far fa-clock"${_scopeId2}></span>${ssrInterpolate(service.value.reading_time)} ${ssrInterpolate(trans("min read"))}`);
                  } else {
                    return [
                      createVNode("span", { class: "far fa-clock" }),
                      createTextVNode(toDisplayString(service.value.reading_time) + " " + toDisplayString(trans("min read")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</li></ul>`);
            } else {
              _push2(`<!---->`);
            }
            if (service.value.image_link) {
              _push2(`<div class="services-details__img-1 my-3"${_scopeId}><img${ssrRenderAttr("src", service.value.image_link)}${ssrRenderAttr("alt", getServiceTitle(service.value))}${_scopeId}></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="services-details__text-1" style="${ssrRenderStyle({ "line-height": "27px !important" })}"${_scopeId}><div${_scopeId}>${service.value.content ?? ""}</div></div></div></div></div></div></section>`);
            if (relatedServices.value.length) {
              _push2(`<section class="services-carousel-page services-related mb-5"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Related Services"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Explore More"))} <span${_scopeId}>${ssrInterpolate(trans("Services"))}</span></h2></div><div class="row"${_scopeId}><!--[-->`);
              ssrRenderList(relatedServices.value, (relatedService) => {
                _push2(`<div class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms"${_scopeId}>`);
                _push2(ssrRenderComponent(ServiceCardThree, {
                  title: getServiceTitle(relatedService),
                  description: getServiceDescription(relatedService),
                  highlights: getServiceHighlights(relatedService),
                  link: getServiceUrl(relatedService),
                  image: relatedService.image_link,
                  "button-label": trans("Read More"),
                  "is-rtl": locale.value === "ar",
                  "reading-time": relatedService.reading_time,
                  "reading-time-label": trans("min read")
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              });
              _push2(`<!--]--></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
            if (testimonials.value && testimonials.value.length) {
              _push2(`<section class="testimonial-two"${_scopeId}><div class="container"${_scopeId}><div class="section-title text-center sec-title-animation animation-style1"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Testimonials"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${ssrInterpolate(trans("Customer Experiences"))} <br${_scopeId}> ${ssrInterpolate(trans("That"))} <span${_scopeId}>${ssrInterpolate(trans("Speak Volumes"))}</span></h2></div><div class="testimonial-two__carousel owl-theme owl-carousel"${_scopeId}><!--[-->`);
              ssrRenderList(testimonials.value, (testimonial) => {
                _push2(`<div class="item"${_scopeId}><div class="testimonial-two__single"${_scopeId}><div class="testimonial-two__single-inner"${_scopeId}><div class="testimonial-two__star"${_scopeId}><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-pointed-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span><span class="icon-star"${_scopeId}></span></div><p class="testimonial-two__text"${_scopeId}>${ssrInterpolate(translateField(testimonial.quote))}</p></div><div class="testimonial-two__client-info"${_scopeId}><div class="testimonial-two__client-img"${_scopeId}><img${ssrRenderAttr("src", testimonial.avatar_link)}${ssrRenderAttr("alt", translateField(testimonial.name))}${_scopeId}></div><div class="testimonial-two__client-content"${_scopeId}><h4 class="testimonial-two__client-name"${_scopeId}>${ssrInterpolate(translateField(testimonial.name))}</h4><p class="testimonial-two__sub-title"${_scopeId}>${ssrInterpolate(translateField(testimonial.position))}</p></div></div><div class="testimonial-two__quote"${_scopeId}><span class="icon-right-quote"${_scopeId}></span></div></div></div>`);
              });
              _push2(`<!--]--></div></div></section>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("div", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/services-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(getServiceTitle(service.value)), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("services.index")
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(trans("Our Services")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(getServiceTitle(service.value)), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "services-details" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-4 col-lg-5" }, [
                      createVNode("div", { class: "services-details__left" }, [
                        createVNode("div", { class: "services-details__services-list-box" }, [
                          createVNode("h3", { class: "services-details__services-list-title" }, toDisplayString(trans("Service Categories")), 1),
                          createVNode("ul", { class: "services-details__services-list list-unstyled" }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(recentServices.value.slice(0, 6), (recentService) => {
                              return openBlock(), createBlock("li", {
                                key: recentService.id
                              }, [
                                createVNode(unref(Link), {
                                  href: getServiceUrl(recentService),
                                  class: { "active": recentService.id === service.value.id }
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(getServiceTitle(recentService)) + " ", 1),
                                    createVNode("span", {
                                      class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-2`
                                    }, null, 2)
                                  ]),
                                  _: 2
                                }, 1032, ["href", "class"])
                              ]);
                            }), 128))
                          ])
                        ]),
                        createVNode("div", { class: "services-details__need-help" }, [
                          createVNode("div", { class: "services-details__need-help-img" }, [
                            createVNode("img", {
                              src: asset_path.value + "images/need_help.jpg",
                              alt: trans("Need help")
                            }, null, 8, ["src", "alt"]),
                            createVNode("div", { class: "services-details__need-help-content" }, [
                              createVNode("div", { class: "services-details__need-help-bdr" }),
                              createVNode("h3", { class: "services-details__need-help-title" }, toDisplayString(trans("Need Help?")), 1),
                              createVNode("p", { class: "services-details__need-help-number" }, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: `tel:${settings2.value.website_phone || settings2.value.phone}`
                                }, toDisplayString(settings2.value.website_phone || settings2.value.phone || "+12 (00) 345 789034"), 9, ["href"])
                              ])
                            ])
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-8 col-lg-7" }, [
                      createVNode("div", { class: "services-details__right" }, [
                        createVNode("h3", { class: "services-details__title-1" }, toDisplayString(getServiceTitle(service.value)), 1),
                        createVNode("div", { class: "services-details__bdr" }),
                        service.value.reading_time ? (openBlock(), createBlock("ul", {
                          key: 0,
                          class: "blog-details__meta list-unstyled"
                        }, [
                          createVNode("li", null, [
                            createVNode(unref(Link), null, {
                              default: withCtx(() => [
                                createVNode("span", { class: "far fa-clock" }),
                                createTextVNode(toDisplayString(service.value.reading_time) + " " + toDisplayString(trans("min read")), 1)
                              ]),
                              _: 1
                            })
                          ])
                        ])) : createCommentVNode("", true),
                        service.value.image_link ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "services-details__img-1 my-3"
                        }, [
                          createVNode("img", {
                            src: service.value.image_link,
                            alt: getServiceTitle(service.value)
                          }, null, 8, ["src", "alt"])
                        ])) : createCommentVNode("", true),
                        createVNode("div", {
                          class: "services-details__text-1",
                          style: { "line-height": "27px !important" }
                        }, [
                          createVNode("div", {
                            innerHTML: service.value.content
                          }, null, 8, ["innerHTML"])
                        ])
                      ])
                    ])
                  ])
                ])
              ]),
              relatedServices.value.length ? (openBlock(), createBlock("section", {
                key: 0,
                class: "services-carousel-page services-related mb-5"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Related Services")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, [
                      createTextVNode(toDisplayString(trans("Explore More")) + " ", 1),
                      createVNode("span", null, toDisplayString(trans("Services")), 1)
                    ])
                  ]),
                  createVNode("div", { class: "row" }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(relatedServices.value, (relatedService) => {
                      return openBlock(), createBlock("div", {
                        key: relatedService.id,
                        class: "col-xl-4 col-lg-6 col-md-6 wow fadeInUp",
                        "data-wow-delay": "100ms"
                      }, [
                        createVNode(ServiceCardThree, {
                          title: getServiceTitle(relatedService),
                          description: getServiceDescription(relatedService),
                          highlights: getServiceHighlights(relatedService),
                          link: getServiceUrl(relatedService),
                          image: relatedService.image_link,
                          "button-label": trans("Read More"),
                          "is-rtl": locale.value === "ar",
                          "reading-time": relatedService.reading_time,
                          "reading-time-label": trans("min read")
                        }, null, 8, ["title", "description", "highlights", "link", "image", "button-label", "is-rtl", "reading-time", "reading-time-label"])
                      ]);
                    }), 128))
                  ])
                ])
              ])) : createCommentVNode("", true),
              testimonials.value && testimonials.value.length ? (openBlock(), createBlock("section", {
                key: 1,
                class: "testimonial-two"
              }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "section-title text-center sec-title-animation animation-style1" }, [
                    createVNode("div", { class: "section-title__tagline-box" }, [
                      createVNode("div", { class: "section-title__tagline-shape-1" }),
                      createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Testimonials")), 1),
                      createVNode("div", { class: "section-title__tagline-shape-2" })
                    ]),
                    createVNode("h2", { class: "section-title__title title-animation" }, [
                      createTextVNode(toDisplayString(trans("Customer Experiences")) + " ", 1),
                      createVNode("br"),
                      createTextVNode(" " + toDisplayString(trans("That")) + " ", 1),
                      createVNode("span", null, toDisplayString(trans("Speak Volumes")), 1)
                    ])
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
              ])) : createCommentVNode("", true)
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
const __vite_glob_0_9 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$7
}, Symbol.toStringTag, { value: "Module" }));
const __default__$2 = {
  components: {
    AppLayout: _sfc_main$h
  }
};
const _sfc_main$6 = /* @__PURE__ */ Object.assign(__default__$2, {
  __name: "Index",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const trans = (key) => page.props.translations[key] || key;
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const locale = computed(() => page.props.locale || "en");
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => {
      return `${trans("Contact Us")} | ${seo.value.website_name || ""}`.trim();
    });
    const metaDescription = computed(() => {
      return meta.value.description || trans("Contact our team for support, inquiries, or project discussions.") || seo.value.website_desc || "";
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("contact, support, get in touch, customer service") || seo.value.website_keywords || "";
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "index, follow");
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
      let contactUrl = route("contact-us.store");
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<link rel="stylesheet"${ssrRenderAttr("href", asset_path.value + "site/css/module-css/page-header.css")}${_scopeId}><title${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)}${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)}${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("link", {
                rel: "stylesheet",
                href: asset_path.value + "site/css/module-css/page-header.css"
              }, null, 8, ["href"]),
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}images/backgrounds/contact-us-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("home")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home"${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li${_scopeId}><span class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate(trans("Contact Us"))}</li></ul></div></div></div></section><section class="contact-one"${_scopeId}><div class="contact-one__bg-shape" style="${ssrRenderStyle({ backgroundImage: `url(${asset_path.value}site/images/shapes/contact-one-bg-shape.png)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6 col-lg-6"${_scopeId}><div class="${ssrRenderClass(`contact-one__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`)}" data-wow-delay="100ms" data-wow-duration="2500ms"${_scopeId}><div class="contact-one__left-shape-1"${_scopeId}></div><div class="contact-one__left-shape-2"${_scopeId}></div><h3 class="contact-one__from-title"${_scopeId}>${ssrInterpolate(trans("How Can We Help You?"))}</h3><form class="contact-one__form"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Full Name"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-user-1"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).name)} type="text" name="name"${ssrRenderAttr("placeholder", trans("Full Name"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.name })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.name) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.name)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Email"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-email"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).email)} type="email" name="email"${ssrRenderAttr("placeholder", trans("Email"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.email })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.email) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.email)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Phone Number"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).mobile)} type="text" name="mobile"${ssrRenderAttr("placeholder", trans("Phone Number"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.mobile })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.mobile) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.mobile)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="col-xl-6 col-lg-6"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Subject"))}</h4><div class="contact-one__input-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><input${ssrRenderAttr("value", unref(contactForm).subject)} type="text" name="subject"${ssrRenderAttr("placeholder", trans("Subject"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.subject })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}></div>`);
            if (unref(contactForm).errors.subject) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.subject)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="col-xl-12"${_scopeId}><h4 class="contact-one__input-title"${_scopeId}>${ssrInterpolate(trans("Message"))}</h4><div class="contact-one__input-box text-message-box"${_scopeId}><div class="contact-one__input-icon"${_scopeId}><span class="icon-edit"${_scopeId}></span></div><textarea name="message"${ssrRenderAttr("placeholder", trans("Write your message"))} class="${ssrRenderClass({ "error": unref(contactForm).errors.message })}"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} required${_scopeId}>${ssrInterpolate(unref(contactForm).message)}</textarea></div>`);
            if (unref(contactForm).errors.message) {
              _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate(unref(contactForm).errors.message)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="contact-one__btn-box"${_scopeId}><button type="submit"${ssrIncludeBooleanAttr(unref(contactForm).processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": unref(contactForm).processing }, "thm-btn"])}"${_scopeId}>`);
            if (unref(contactForm).processing) {
              _push2(`<span${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2"${_scopeId}></i>${ssrInterpolate(trans("Sending..."))}</span>`);
            } else {
              _push2(`<span${_scopeId}><span${_scopeId}>${ssrInterpolate(trans("Submit"))}</span> <i class="${ssrRenderClass(`icon-${locale.value === "ar" ? "left" : "right"}-arrow mx-1`)}"${_scopeId}></i></span>`);
            }
            _push2(`</button></div></div>`);
            if (submitSuccess.value) {
              _push2(`<div class="col-12 mt-3"${_scopeId}><div class="alert alert-success"${_scopeId}>${ssrInterpolate(trans("Thank you for contacting us! We will get back to you soon."))}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</form></div></div><div class="col-xl-6 col-lg-6"${_scopeId}><div class="contact-one__right"${_scopeId}><div class="section-title text-left sec-title-animation animation-style2"${_scopeId}><div class="section-title__tagline-box"${_scopeId}><div class="section-title__tagline-shape-1"${_scopeId}></div><span class="section-title__tagline"${_scopeId}>${ssrInterpolate(trans("Get In Touch"))}</span><div class="section-title__tagline-shape-2"${_scopeId}></div></div><h2 class="section-title__title title-animation"${_scopeId}>${trans("Start the Conversation") + "<span>–</span><br><span>" + trans("Reach Out Anytime") + "</span>"}</h2></div><p class="contact-one__text"${_scopeId}>${ssrInterpolate(trans("We're here to listen! Whether you have questions, feedback, or just want to say hello, feel free to reach out"))}</p><ul class="contact-one__list list-unstyled"${_scopeId}><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-pin"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("Our Location"))}</h4><p${_scopeId}>${ssrInterpolate(settings2.value.address)}</p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-mail"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("Email"))}</h4><p${_scopeId}><a dir="ltr" href="mailto:{{settings.email}}"${_scopeId}>${ssrInterpolate(settings2.value.email)}</a></p></div></li><li${_scopeId}><div class="icon"${_scopeId}><span class="icon-phone-call"${_scopeId}></span></div><div class="content"${_scopeId}><h4${_scopeId}>${ssrInterpolate(trans("Phone"))}</h4><p${_scopeId}><a dir="ltr" href="tel:{{settings.phone}}"${_scopeId}>${ssrInterpolate(settings2.value.phone)}</a></p></div></li></ul></div></div></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { backgroundImage: `url(${asset_path.value}images/backgrounds/contact-us-bg.jpg)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("Contact Us")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: _ctx.route("home")
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", {
                            class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow-1`
                          }, null, 2)
                        ]),
                        createVNode("li", null, toDisplayString(trans("Contact Us")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "contact-one" }, [
                createVNode("div", {
                  class: "contact-one__bg-shape",
                  style: { backgroundImage: `url(${asset_path.value}site/images/shapes/contact-one-bg-shape.png)` }
                }, null, 4),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                      createVNode("div", {
                        class: `contact-one__left wow slideIn${locale.value !== "ar" ? "Left" : "Right"}`,
                        "data-wow-delay": "100ms",
                        "data-wow-duration": "2500ms"
                      }, [
                        createVNode("div", { class: "contact-one__left-shape-1" }),
                        createVNode("div", { class: "contact-one__left-shape-2" }),
                        createVNode("h3", { class: "contact-one__from-title" }, toDisplayString(trans("How Can We Help You?")), 1),
                        createVNode("form", {
                          onSubmit: withModifiers(handleSubmit, ["prevent"]),
                          class: "contact-one__form"
                        }, [
                          createVNode("div", { class: "row" }, [
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Full Name")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-user-1" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).name = $event,
                                  type: "text",
                                  name: "name",
                                  placeholder: trans("Full Name"),
                                  class: { "error": unref(contactForm).errors.name },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).name]
                                ])
                              ]),
                              unref(contactForm).errors.name ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.name), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Email")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-email" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).email = $event,
                                  type: "email",
                                  name: "email",
                                  placeholder: trans("Email"),
                                  class: { "error": unref(contactForm).errors.email },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).email]
                                ])
                              ]),
                              unref(contactForm).errors.email ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.email), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Phone Number")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-phone-call" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).mobile = $event,
                                  type: "text",
                                  name: "mobile",
                                  placeholder: trans("Phone Number"),
                                  class: { "error": unref(contactForm).errors.mobile },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).mobile]
                                ])
                              ]),
                              unref(contactForm).errors.mobile ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.mobile), 1)) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                              createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Subject")), 1),
                              createVNode("div", { class: "contact-one__input-box" }, [
                                createVNode("div", { class: "contact-one__input-icon" }, [
                                  createVNode("span", { class: "icon-edit" })
                                ]),
                                withDirectives(createVNode("input", {
                                  "onUpdate:modelValue": ($event) => unref(contactForm).subject = $event,
                                  type: "text",
                                  name: "subject",
                                  placeholder: trans("Subject"),
                                  class: { "error": unref(contactForm).errors.subject },
                                  disabled: unref(contactForm).processing,
                                  required: ""
                                }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                  [vModelText, unref(contactForm).subject]
                                ])
                              ]),
                              unref(contactForm).errors.subject ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "text-danger mt-1 small"
                              }, toDisplayString(unref(contactForm).errors.subject), 1)) : createCommentVNode("", true)
                            ])
                          ]),
                          createVNode("div", { class: "col-xl-12" }, [
                            createVNode("h4", { class: "contact-one__input-title" }, toDisplayString(trans("Message")), 1),
                            createVNode("div", { class: "contact-one__input-box text-message-box" }, [
                              createVNode("div", { class: "contact-one__input-icon" }, [
                                createVNode("span", { class: "icon-edit" })
                              ]),
                              withDirectives(createVNode("textarea", {
                                "onUpdate:modelValue": ($event) => unref(contactForm).message = $event,
                                name: "message",
                                placeholder: trans("Write your message"),
                                class: { "error": unref(contactForm).errors.message },
                                disabled: unref(contactForm).processing,
                                required: ""
                              }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                                [vModelText, unref(contactForm).message]
                              ])
                            ]),
                            unref(contactForm).errors.message ? (openBlock(), createBlock("div", {
                              key: 0,
                              class: "text-danger mt-1 small"
                            }, toDisplayString(unref(contactForm).errors.message), 1)) : createCommentVNode("", true),
                            createVNode("div", { class: "contact-one__btn-box" }, [
                              createVNode("button", {
                                type: "submit",
                                class: ["thm-btn", { "opacity-50": unref(contactForm).processing }],
                                disabled: unref(contactForm).processing
                              }, [
                                unref(contactForm).processing ? (openBlock(), createBlock("span", { key: 0 }, [
                                  createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                                  createTextVNode(toDisplayString(trans("Sending...")), 1)
                                ])) : (openBlock(), createBlock("span", { key: 1 }, [
                                  createVNode("span", null, toDisplayString(trans("Submit")), 1),
                                  createTextVNode(),
                                  createVNode("i", {
                                    class: `icon-${locale.value === "ar" ? "left" : "right"}-arrow mx-1`
                                  }, null, 2)
                                ]))
                              ], 10, ["disabled"])
                            ])
                          ]),
                          submitSuccess.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "col-12 mt-3"
                          }, [
                            createVNode("div", { class: "alert alert-success" }, toDisplayString(trans("Thank you for contacting us! We will get back to you soon.")), 1)
                          ])) : createCommentVNode("", true)
                        ], 32)
                      ], 2)
                    ]),
                    createVNode("div", { class: "col-xl-6 col-lg-6" }, [
                      createVNode("div", { class: "contact-one__right" }, [
                        createVNode("div", { class: "section-title text-left sec-title-animation animation-style2" }, [
                          createVNode("div", { class: "section-title__tagline-box" }, [
                            createVNode("div", { class: "section-title__tagline-shape-1" }),
                            createVNode("span", { class: "section-title__tagline" }, toDisplayString(trans("Get In Touch")), 1),
                            createVNode("div", { class: "section-title__tagline-shape-2" })
                          ]),
                          createVNode("h2", {
                            class: "section-title__title title-animation",
                            innerHTML: trans("Start the Conversation") + "<span>–</span><br><span>" + trans("Reach Out Anytime") + "</span>"
                          }, null, 8, ["innerHTML"])
                        ]),
                        createVNode("p", { class: "contact-one__text" }, toDisplayString(trans("We're here to listen! Whether you have questions, feedback, or just want to say hello, feel free to reach out")), 1),
                        createVNode("ul", { class: "contact-one__list list-unstyled" }, [
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-pin" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("Our Location")), 1),
                              createVNode("p", null, toDisplayString(settings2.value.address), 1)
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-mail" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("Email")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: "mailto:{{settings.email}}"
                                }, toDisplayString(settings2.value.email), 1)
                              ])
                            ])
                          ]),
                          createVNode("li", null, [
                            createVNode("div", { class: "icon" }, [
                              createVNode("span", { class: "icon-phone-call" })
                            ]),
                            createVNode("div", { class: "content" }, [
                              createVNode("h4", null, toDisplayString(trans("Phone")), 1),
                              createVNode("p", null, [
                                createVNode("a", {
                                  dir: "ltr",
                                  href: "tel:{{settings.phone}}"
                                }, toDisplayString(settings2.value.phone), 1)
                              ])
                            ])
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
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("Modules/Support/resources/assets/js/Pages/Index.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const __vite_glob_0_10 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: _sfc_main$6
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$5 = {
  components: {
    AppLayout: _sfc_main$h,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path);
    const meta = computed(() => page.props.meta || {});
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const metaTitle = computed(() => `${trans("Forgot Password")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("Request a password reset link to regain access to your account.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("forgot password, reset password, account recovery");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
    const form = useForm({
      email: ""
    });
    return {
      form,
      seo,
      locale,
      trans,
      asset_path,
      metaTitle,
      metaDescription,
      metaKeywords,
      metaImage,
      metaCanonical,
      metaRobots
    };
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
        _push2(`<link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/page-header.css")} data-v-2bc3780d${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/shop.css")} data-v-2bc3780d${_scopeId}><title data-v-2bc3780d${_scopeId}>${ssrInterpolate($setup.metaTitle)}</title><meta name="description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-2bc3780d${_scopeId}><meta name="keywords"${ssrRenderAttr("content", $setup.metaKeywords)} data-v-2bc3780d${_scopeId}><meta name="robots"${ssrRenderAttr("content", $setup.metaRobots)} data-v-2bc3780d${_scopeId}>`);
        if ($setup.metaCanonical) {
          _push2(`<link rel="canonical"${ssrRenderAttr("href", $setup.metaCanonical)} data-v-2bc3780d${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-2bc3780d${_scopeId}><meta property="og:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-2bc3780d${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta property="og:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-2bc3780d${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        if ($setup.metaCanonical) {
          _push2(`<meta property="og:url"${ssrRenderAttr("content", $setup.metaCanonical)} data-v-2bc3780d${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:type" content="website" data-v-2bc3780d${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-2bc3780d${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-2bc3780d${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-2bc3780d${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta name="twitter:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-2bc3780d${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/page-header.css"
          }, null, 8, ["href"]),
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/shop.css"
          }, null, 8, ["href"]),
          createVNode("title", null, toDisplayString($setup.metaTitle), 1),
          createVNode("meta", {
            name: "description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "keywords",
            content: $setup.metaKeywords
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "robots",
            content: $setup.metaRobots
          }, null, 8, ["content"]),
          $setup.metaCanonical ? (openBlock(), createBlock("link", {
            key: 0,
            rel: "canonical",
            href: $setup.metaCanonical
          }, null, 8, ["href"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            property: "og:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 1,
            property: "og:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true),
          $setup.metaCanonical ? (openBlock(), createBlock("meta", {
            key: 2,
            property: "og:url",
            content: $setup.metaCanonical
          }, null, 8, ["content"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:type",
            content: "website"
          }),
          createVNode("meta", {
            name: "twitter:card",
            content: "summary_large_image"
          }),
          createVNode("meta", {
            name: "twitter:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "twitter:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 3,
            name: "twitter:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<section class="page-header" data-v-2bc3780d${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` })}" data-v-2bc3780d${_scopeId}></div><div class="container" data-v-2bc3780d${_scopeId}><div class="page-header__inner" data-v-2bc3780d${_scopeId}><h2 data-v-2bc3780d${_scopeId}>${ssrInterpolate($setup.trans("Forgot Password"))}</h2><div class="thm-breadcrumb__box" data-v-2bc3780d${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-2bc3780d${_scopeId}><li data-v-2bc3780d${_scopeId}><a href="/" data-v-2bc3780d${_scopeId}><i class="fas fa-home" data-v-2bc3780d${_scopeId}></i> ${ssrInterpolate($setup.trans("Home"))}</a></li><li data-v-2bc3780d${_scopeId}><span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`)}" data-v-2bc3780d${_scopeId}></span></li><li data-v-2bc3780d${_scopeId}>${ssrInterpolate($setup.trans("Forgot Password"))}</li></ul></div></div></div></section><section class="login-one" data-v-2bc3780d${_scopeId}><div class="container" data-v-2bc3780d${_scopeId}><div class="login-one__form" data-v-2bc3780d${_scopeId}><div class="inner-title text-center" data-v-2bc3780d${_scopeId}><h2 data-v-2bc3780d${_scopeId}>${ssrInterpolate($setup.trans("Reset Your Password"))}</h2></div><form id="forgot-password__form" data-v-2bc3780d${_scopeId}><div class="row" data-v-2bc3780d${_scopeId}><div class="col-xl-12" data-v-2bc3780d${_scopeId}><div class="form-group" data-v-2bc3780d${_scopeId}><div class="input-box" data-v-2bc3780d${_scopeId}><input id="email"${ssrRenderAttr("value", $setup.form.email)} type="email" name="email" autocomplete="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-2bc3780d${_scopeId}></div>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-2bc3780d${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-2bc3780d${_scopeId}><div class="form-group" data-v-2bc3780d${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "thm-btn"])}" data-v-2bc3780d${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-2bc3780d${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-2bc3780d${_scopeId}></i>${ssrInterpolate($setup.trans("Sending..."))}</span>`);
        } else {
          _push2(`<span data-v-2bc3780d${_scopeId}>${ssrInterpolate($setup.trans("Send Email Verification"))}<span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `)}" data-v-2bc3780d${_scopeId}></span></span>`);
        }
        _push2(`</button></div></div><div class="create-account text-center" data-v-2bc3780d${_scopeId}><p data-v-2bc3780d${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($setup.trans("Back to Login"))}`);
            } else {
              return [
                createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</p></div></div></form></div></div></section>`);
      } else {
        return [
          createVNode("section", { class: "page-header" }, [
            createVNode("div", {
              class: "page-header__bg",
              style: { backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` }
            }, null, 4),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "page-header__inner" }, [
                createVNode("h2", null, toDisplayString($setup.trans("Forgot Password")), 1),
                createVNode("div", { class: "thm-breadcrumb__box" }, [
                  createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                    createVNode("li", null, [
                      createVNode("a", { href: "/" }, [
                        createVNode("i", { class: "fas fa-home" }),
                        createTextVNode(" " + toDisplayString($setup.trans("Home")), 1)
                      ])
                    ]),
                    createVNode("li", null, [
                      createVNode("span", {
                        class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`
                      }, null, 2)
                    ]),
                    createVNode("li", null, toDisplayString($setup.trans("Forgot Password")), 1)
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "login-one" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "login-one__form" }, [
                createVNode("div", { class: "inner-title text-center" }, [
                  createVNode("h2", null, toDisplayString($setup.trans("Reset Your Password")), 1)
                ]),
                createVNode("form", {
                  id: "forgot-password__form",
                  onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("password.email")), ["prevent"])
                }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "email",
                            "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                            type: "email",
                            name: "email",
                            autocomplete: "email",
                            placeholder: $setup.trans("Email"),
                            class: { "error": $props.errors.email },
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.email]
                          ])
                        ]),
                        $props.errors.email ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("button", {
                          class: ["thm-btn", { "opacity-50": $setup.form.processing }],
                          type: "submit",
                          disabled: $setup.form.processing
                        }, [
                          $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                            createTextVNode(toDisplayString($setup.trans("Sending...")), 1)
                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                            createTextVNode(toDisplayString($setup.trans("Send Email Verification")), 1),
                            createVNode("span", {
                              class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `
                            }, null, 2)
                          ]))
                        ], 10, ["disabled"])
                      ])
                    ]),
                    createVNode("div", { class: "create-account text-center" }, [
                      createVNode("p", null, [
                        createVNode(_component_Link, {
                          href: _ctx.route("login")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
                          ]),
                          _: 1
                        }, 8, ["href"])
                      ])
                    ])
                  ])
                ], 40, ["onSubmit"])
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
const ForgotPassword = /* @__PURE__ */ _export_sfc(_sfc_main$5, [["ssrRender", _sfc_ssrRender$3], ["__scopeId", "data-v-2bc3780d"]]);
const __vite_glob_0_11 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ForgotPassword
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$4 = {
  components: {
    AppLayout: _sfc_main$h,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const meta = computed(() => page.props.meta || {});
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const metaTitle = computed(() => `${trans("Login")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("Log in to manage your account and services.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("login, sign in, account access");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
    const form = useForm({
      email: "",
      password: "",
      remember: false
    });
    return {
      form,
      seo,
      locale,
      trans,
      asset_path,
      metaTitle,
      metaDescription,
      metaKeywords,
      metaImage,
      metaCanonical,
      metaRobots
    };
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
        _push2(`<link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/page-header.css")}${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/shop.css")}${_scopeId}><title${_scopeId}>${ssrInterpolate($setup.metaTitle)}</title><meta name="description"${ssrRenderAttr("content", $setup.metaDescription)}${_scopeId}><meta name="keywords"${ssrRenderAttr("content", $setup.metaKeywords)}${_scopeId}><meta name="robots"${ssrRenderAttr("content", $setup.metaRobots)}${_scopeId}>`);
        if ($setup.metaCanonical) {
          _push2(`<link rel="canonical"${ssrRenderAttr("href", $setup.metaCanonical)}${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:title"${ssrRenderAttr("content", $setup.metaTitle)}${_scopeId}><meta property="og:description"${ssrRenderAttr("content", $setup.metaDescription)}${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta property="og:image"${ssrRenderAttr("content", $setup.metaImage)}${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        if ($setup.metaCanonical) {
          _push2(`<meta property="og:url"${ssrRenderAttr("content", $setup.metaCanonical)}${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:type" content="website"${_scopeId}><meta name="twitter:card" content="summary_large_image"${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", $setup.metaTitle)}${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", $setup.metaDescription)}${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta name="twitter:image"${ssrRenderAttr("content", $setup.metaImage)}${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/page-header.css"
          }, null, 8, ["href"]),
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/shop.css"
          }, null, 8, ["href"]),
          createVNode("title", null, toDisplayString($setup.metaTitle), 1),
          createVNode("meta", {
            name: "description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "keywords",
            content: $setup.metaKeywords
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "robots",
            content: $setup.metaRobots
          }, null, 8, ["content"]),
          $setup.metaCanonical ? (openBlock(), createBlock("link", {
            key: 0,
            rel: "canonical",
            href: $setup.metaCanonical
          }, null, 8, ["href"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            property: "og:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 1,
            property: "og:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true),
          $setup.metaCanonical ? (openBlock(), createBlock("meta", {
            key: 2,
            property: "og:url",
            content: $setup.metaCanonical
          }, null, 8, ["content"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:type",
            content: "website"
          }),
          createVNode("meta", {
            name: "twitter:card",
            content: "summary_large_image"
          }),
          createVNode("meta", {
            name: "twitter:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "twitter:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 3,
            name: "twitter:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<section class="page-header"${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` })}"${_scopeId}></div><div class="container"${_scopeId}><div class="page-header__inner"${_scopeId}><h2${_scopeId}>${ssrInterpolate($setup.trans("Login"))}</h2><div class="thm-breadcrumb__box"${_scopeId}><ul class="thm-breadcrumb list-unstyled"${_scopeId}><li${_scopeId}><a href="/"${_scopeId}><i class="fas fa-home"${_scopeId}></i>${ssrInterpolate($setup.trans("Home"))}</a></li><li${_scopeId}><span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`)}"${_scopeId}></span></li><li${_scopeId}>${ssrInterpolate($setup.trans("Login"))}</li></ul></div></div></div></section><section class="login-one"${_scopeId}><div class="container"${_scopeId}><div class="login-one__form"${_scopeId}><div class="inner-title text-center"${_scopeId}><h2${_scopeId}>${ssrInterpolate($setup.trans("Login"))}</h2></div><form id="login-one__form" name="Login-one_form" action="#" method="post"${_scopeId}><div class="row"${_scopeId}><div class="col-xl-12"${_scopeId}><div class="form-group"${_scopeId}><div class="input-box"${_scopeId}><input id="formEmail"${ssrRenderAttr("value", $setup.form.email)} type="email" name="form_email"${ssrRenderAttr("placeholder", $setup.trans("Email"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required=""${_scopeId}></div>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12"${_scopeId}><div class="form-group"${_scopeId}><div class="input-box"${_scopeId}><input id="formPassword"${ssrRenderAttr("value", $setup.form.password)} type="password" name="form_password"${ssrRenderAttr("placeholder", $setup.trans("Password"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required=""${_scopeId}></div>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small"${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12"${_scopeId}><div class="form-group"${_scopeId}><button type="submit" data-loading-text="Please wait..."${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "thm-btn"])}"${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2"${_scopeId}></i>${ssrInterpolate($setup.trans("Signing In..."))}</span>`);
        } else {
          _push2(`<span${_scopeId}>${ssrInterpolate($setup.trans("Login"))} <span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `)}"${_scopeId}></span></span>`);
        }
        _push2(`</button></div></div><div class="remember-forget"${_scopeId}><div class="checked-box1"${_scopeId}><input id="saveinfo"${ssrIncludeBooleanAttr(Array.isArray($setup.form.remember) ? ssrLooseContain($setup.form.remember, null) : $setup.form.remember) ? " checked" : ""} type="checkbox" name="saveMyInfo" checked=""${_scopeId}><label for="saveinfo"${_scopeId}><span${_scopeId}></span> ${ssrInterpolate($setup.trans("Remember Me"))}</label></div><div class="forget"${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("password.request")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($setup.trans("Forgot Password"))}`);
            } else {
              return [
                createTextVNode(toDisplayString($setup.trans("Forgot Password")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</div></div><div class="create-account text-center"${_scopeId}><p${_scopeId}>${ssrInterpolate($setup.trans("I Don't Have Account!"))} `);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("register")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($setup.trans("Create A New Account"))}`);
            } else {
              return [
                createTextVNode(toDisplayString($setup.trans("Create A New Account")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</p></div></div></form></div></div></section>`);
      } else {
        return [
          createVNode("section", { class: "page-header" }, [
            createVNode("div", {
              class: "page-header__bg",
              style: { backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` }
            }, null, 4),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "page-header__inner" }, [
                createVNode("h2", null, toDisplayString($setup.trans("Login")), 1),
                createVNode("div", { class: "thm-breadcrumb__box" }, [
                  createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                    createVNode("li", null, [
                      createVNode("a", { href: "/" }, [
                        createVNode("i", { class: "fas fa-home" }),
                        createTextVNode(toDisplayString($setup.trans("Home")), 1)
                      ])
                    ]),
                    createVNode("li", null, [
                      createVNode("span", {
                        class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`
                      }, null, 2)
                    ]),
                    createVNode("li", null, toDisplayString($setup.trans("Login")), 1)
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "login-one" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "login-one__form" }, [
                createVNode("div", { class: "inner-title text-center" }, [
                  createVNode("h2", null, toDisplayString($setup.trans("Login")), 1)
                ]),
                createVNode("form", {
                  id: "login-one__form",
                  name: "Login-one_form",
                  action: "#",
                  method: "post",
                  onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("login")), ["prevent"])
                }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formEmail",
                            "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                            type: "email",
                            name: "form_email",
                            placeholder: $setup.trans("Email"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.email]
                          ])
                        ]),
                        $props.errors.email ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formPassword",
                            "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                            type: "password",
                            name: "form_password",
                            placeholder: $setup.trans("Password"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.password]
                          ])
                        ]),
                        $props.errors.password ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("button", {
                          class: ["thm-btn", { "opacity-50": $setup.form.processing }],
                          type: "submit",
                          "data-loading-text": "Please wait...",
                          disabled: $setup.form.processing
                        }, [
                          $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                            createTextVNode(toDisplayString($setup.trans("Signing In...")), 1)
                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                            createTextVNode(toDisplayString($setup.trans("Login")) + " ", 1),
                            createVNode("span", {
                              class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `
                            }, null, 2)
                          ]))
                        ], 10, ["disabled"])
                      ])
                    ]),
                    createVNode("div", { class: "remember-forget" }, [
                      createVNode("div", { class: "checked-box1" }, [
                        withDirectives(createVNode("input", {
                          id: "saveinfo",
                          "onUpdate:modelValue": ($event) => $setup.form.remember = $event,
                          type: "checkbox",
                          name: "saveMyInfo",
                          checked: ""
                        }, null, 8, ["onUpdate:modelValue"]), [
                          [vModelCheckbox, $setup.form.remember]
                        ]),
                        createVNode("label", { for: "saveinfo" }, [
                          createVNode("span"),
                          createTextVNode(" " + toDisplayString($setup.trans("Remember Me")), 1)
                        ])
                      ]),
                      createVNode("div", { class: "forget" }, [
                        createVNode(_component_Link, {
                          href: _ctx.route("password.request")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString($setup.trans("Forgot Password")), 1)
                          ]),
                          _: 1
                        }, 8, ["href"])
                      ])
                    ]),
                    createVNode("div", { class: "create-account text-center" }, [
                      createVNode("p", null, [
                        createTextVNode(toDisplayString($setup.trans("I Don't Have Account!")) + " ", 1),
                        createVNode(_component_Link, {
                          href: _ctx.route("register")
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString($setup.trans("Create A New Account")), 1)
                          ]),
                          _: 1
                        }, 8, ["href"])
                      ])
                    ])
                  ])
                ], 40, ["onSubmit"])
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
const Login = /* @__PURE__ */ _export_sfc(_sfc_main$4, [["ssrRender", _sfc_ssrRender$2]]);
const __vite_glob_0_12 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Login
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$3 = {
  components: {
    AppLayout: _sfc_main$h,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path);
    const meta = computed(() => page.props.meta || {});
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const metaTitle = computed(() => `${trans("Register")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("Create a new account to access our services.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("register, sign up, create account");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
    const form = useForm({
      name: "",
      email: "",
      mobile: "",
      password: "",
      password_confirmation: ""
    });
    return {
      form,
      seo,
      trans,
      locale,
      asset_path,
      metaTitle,
      metaDescription,
      metaKeywords,
      metaImage,
      metaCanonical,
      metaRobots
    };
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
        _push2(`<link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/page-header.css")} data-v-daff4a9e${_scopeId}><link rel="stylesheet"${ssrRenderAttr("href", $setup.asset_path + "site/css/module-css/shop.css")} data-v-daff4a9e${_scopeId}><title data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.metaTitle)}</title><meta name="description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-daff4a9e${_scopeId}><meta name="keywords"${ssrRenderAttr("content", $setup.metaKeywords)} data-v-daff4a9e${_scopeId}><meta name="robots"${ssrRenderAttr("content", $setup.metaRobots)} data-v-daff4a9e${_scopeId}>`);
        if ($setup.metaCanonical) {
          _push2(`<link rel="canonical"${ssrRenderAttr("href", $setup.metaCanonical)} data-v-daff4a9e${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-daff4a9e${_scopeId}><meta property="og:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-daff4a9e${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta property="og:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-daff4a9e${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        if ($setup.metaCanonical) {
          _push2(`<meta property="og:url"${ssrRenderAttr("content", $setup.metaCanonical)} data-v-daff4a9e${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:type" content="website" data-v-daff4a9e${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-daff4a9e${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-daff4a9e${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-daff4a9e${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta name="twitter:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-daff4a9e${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/page-header.css"
          }, null, 8, ["href"]),
          createVNode("link", {
            rel: "stylesheet",
            href: $setup.asset_path + "site/css/module-css/shop.css"
          }, null, 8, ["href"]),
          createVNode("title", null, toDisplayString($setup.metaTitle), 1),
          createVNode("meta", {
            name: "description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "keywords",
            content: $setup.metaKeywords
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "robots",
            content: $setup.metaRobots
          }, null, 8, ["content"]),
          $setup.metaCanonical ? (openBlock(), createBlock("link", {
            key: 0,
            rel: "canonical",
            href: $setup.metaCanonical
          }, null, 8, ["href"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            property: "og:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 1,
            property: "og:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true),
          $setup.metaCanonical ? (openBlock(), createBlock("meta", {
            key: 2,
            property: "og:url",
            content: $setup.metaCanonical
          }, null, 8, ["content"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:type",
            content: "website"
          }),
          createVNode("meta", {
            name: "twitter:card",
            content: "summary_large_image"
          }),
          createVNode("meta", {
            name: "twitter:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "twitter:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 3,
            name: "twitter:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<section class="page-header" data-v-daff4a9e${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` })}" data-v-daff4a9e${_scopeId}></div><div class="container" data-v-daff4a9e${_scopeId}><div class="page-header__inner" data-v-daff4a9e${_scopeId}><h2 data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.trans("Register"))}</h2><div class="thm-breadcrumb__box" data-v-daff4a9e${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-daff4a9e${_scopeId}><li data-v-daff4a9e${_scopeId}><a href="/" data-v-daff4a9e${_scopeId}><i class="fas fa-home" data-v-daff4a9e${_scopeId}></i>${ssrInterpolate($setup.trans("Home"))}</a></li><li data-v-daff4a9e${_scopeId}><span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`)}" data-v-daff4a9e${_scopeId}></span></li><li data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.trans("Register"))}</li></ul></div></div></div></section><section class="sign-up-one" data-v-daff4a9e${_scopeId}><div class="container" data-v-daff4a9e${_scopeId}><div class="sign-up-one__form" data-v-daff4a9e${_scopeId}><div class="inner-title text-center" data-v-daff4a9e${_scopeId}><h2 data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.trans("Register"))}</h2></div><form id="sign-up-one__form" name="sign-up-one_form" action="#" method="post" data-v-daff4a9e${_scopeId}><div class="row" data-v-daff4a9e${_scopeId}><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><div class="input-box" data-v-daff4a9e${_scopeId}><input id="formName"${ssrRenderAttr("value", $setup.form.name)} type="text" name="form_name"${ssrRenderAttr("placeholder", $setup.trans("Name"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required="" data-v-daff4a9e${_scopeId}></div>`);
        if ($props.errors.name) {
          _push2(`<div class="text-danger mt-1 small" data-v-daff4a9e${_scopeId}>${ssrInterpolate($props.errors.name)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><div class="input-box" data-v-daff4a9e${_scopeId}><input id="formEmail"${ssrRenderAttr("value", $setup.form.email)} type="email" name="form_email"${ssrRenderAttr("placeholder", $setup.trans("Email"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required="" data-v-daff4a9e${_scopeId}></div>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-daff4a9e${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><div class="input-box" data-v-daff4a9e${_scopeId}><input id="formPhone"${ssrRenderAttr("value", $setup.form.mobile)} type="text" name="form_phone"${ssrRenderAttr("placeholder", $setup.trans("Phone"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required="" data-v-daff4a9e${_scopeId}></div>`);
        if ($props.errors.mobile) {
          _push2(`<div class="text-danger mt-1 small" data-v-daff4a9e${_scopeId}>${ssrInterpolate($props.errors.mobile)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><div class="input-box" data-v-daff4a9e${_scopeId}><input id="formPassword"${ssrRenderAttr("value", $setup.form.password)} type="password" name="form_password"${ssrRenderAttr("placeholder", $setup.trans("Password"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required="" data-v-daff4a9e${_scopeId}></div>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small" data-v-daff4a9e${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><div class="input-box" data-v-daff4a9e${_scopeId}><input id="formPasswordConfirm"${ssrRenderAttr("value", $setup.form.password_confirmation)} type="password" name="password_confirmation"${ssrRenderAttr("placeholder", $setup.trans("Confirm Password"))}${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required="" data-v-daff4a9e${_scopeId}></div>`);
        if ($props.errors.password_confirmation) {
          _push2(`<div class="text-danger mt-1 small" data-v-daff4a9e${_scopeId}>${ssrInterpolate($props.errors.password_confirmation)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-daff4a9e${_scopeId}><div class="form-group" data-v-daff4a9e${_scopeId}><button type="submit" data-loading-text="Please wait..."${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "thm-btn"])}" data-v-daff4a9e${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-daff4a9e${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-daff4a9e${_scopeId}></i>${ssrInterpolate($setup.trans("Registering..."))}</span>`);
        } else {
          _push2(`<span data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.trans("Register"))} <span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `)}" data-v-daff4a9e${_scopeId}></span></span>`);
        }
        _push2(`</button></div></div></div><div class="create-account text-center" data-v-daff4a9e${_scopeId}><p data-v-daff4a9e${_scopeId}>${ssrInterpolate($setup.trans("Already Have An Account?"))} `);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($setup.trans("Login"))}`);
            } else {
              return [
                createTextVNode(toDisplayString($setup.trans("Login")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</p></div></form></div></div></section>`);
      } else {
        return [
          createVNode("section", { class: "page-header" }, [
            createVNode("div", {
              class: "page-header__bg",
              style: { backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` }
            }, null, 4),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "page-header__inner" }, [
                createVNode("h2", null, toDisplayString($setup.trans("Register")), 1),
                createVNode("div", { class: "thm-breadcrumb__box" }, [
                  createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                    createVNode("li", null, [
                      createVNode("a", { href: "/" }, [
                        createVNode("i", { class: "fas fa-home" }),
                        createTextVNode(toDisplayString($setup.trans("Home")), 1)
                      ])
                    ]),
                    createVNode("li", null, [
                      createVNode("span", {
                        class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`
                      }, null, 2)
                    ]),
                    createVNode("li", null, toDisplayString($setup.trans("Register")), 1)
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "sign-up-one" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "sign-up-one__form" }, [
                createVNode("div", { class: "inner-title text-center" }, [
                  createVNode("h2", null, toDisplayString($setup.trans("Register")), 1)
                ]),
                createVNode("form", {
                  id: "sign-up-one__form",
                  name: "sign-up-one_form",
                  action: "#",
                  method: "post",
                  onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("register")), ["prevent"])
                }, [
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formName",
                            "onUpdate:modelValue": ($event) => $setup.form.name = $event,
                            type: "text",
                            name: "form_name",
                            placeholder: $setup.trans("Name"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.name]
                          ])
                        ]),
                        $props.errors.name ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.name), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formEmail",
                            "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                            type: "email",
                            name: "form_email",
                            placeholder: $setup.trans("Email"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.email]
                          ])
                        ]),
                        $props.errors.email ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formPhone",
                            "onUpdate:modelValue": ($event) => $setup.form.mobile = $event,
                            type: "text",
                            name: "form_phone",
                            placeholder: $setup.trans("Phone"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.mobile]
                          ])
                        ]),
                        $props.errors.mobile ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.mobile), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formPassword",
                            "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                            type: "password",
                            name: "form_password",
                            placeholder: $setup.trans("Password"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.password]
                          ])
                        ]),
                        $props.errors.password ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "formPasswordConfirm",
                            "onUpdate:modelValue": ($event) => $setup.form.password_confirmation = $event,
                            type: "password",
                            name: "password_confirmation",
                            placeholder: $setup.trans("Confirm Password"),
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 8, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.password_confirmation]
                          ])
                        ]),
                        $props.errors.password_confirmation ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.password_confirmation), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("button", {
                          class: ["thm-btn", { "opacity-50": $setup.form.processing }],
                          type: "submit",
                          "data-loading-text": "Please wait...",
                          disabled: $setup.form.processing
                        }, [
                          $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                            createTextVNode(toDisplayString($setup.trans("Registering...")), 1)
                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                            createTextVNode(toDisplayString($setup.trans("Register")) + " ", 1),
                            createVNode("span", {
                              class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `
                            }, null, 2)
                          ]))
                        ], 10, ["disabled"])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "create-account text-center" }, [
                    createVNode("p", null, [
                      createTextVNode(toDisplayString($setup.trans("Already Have An Account?")) + " ", 1),
                      createVNode(_component_Link, {
                        href: _ctx.route("login")
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString($setup.trans("Login")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"])
                    ])
                  ])
                ], 40, ["onSubmit"])
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
const Register = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["ssrRender", _sfc_ssrRender$1], ["__scopeId", "data-v-daff4a9e"]]);
const __vite_glob_0_13 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Register
}, Symbol.toStringTag, { value: "Module" }));
const _sfc_main$2 = {
  components: {
    AppLayout: _sfc_main$h,
    Link,
    Head
  },
  props: {
    errors: Object
  },
  setup() {
    const page = usePage();
    const locale = computed(() => page.props.locale);
    const seo = computed(() => page.props.seo);
    const settings2 = computed(() => page.props.settings || {});
    const asset_path = computed(() => page.props.asset_path || "");
    const meta = computed(() => page.props.meta || {});
    const trans = (key) => {
      var _a;
      try {
        return ((_a = page.props.translations) == null ? void 0 : _a[key]) || key;
      } catch (e) {
        return key;
      }
    };
    const metaTitle = computed(() => `${trans("Reset Password")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("Set a new password to secure your account.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("reset password, account security, set new password");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d, _e;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || ((_e = settings2.value) == null ? void 0 : _e.meta_img) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
    const params = new URLSearchParams(window.location.search);
    const form = useForm({
      email: "",
      password: "",
      remember: false,
      token: params.get("token") || ""
    });
    return {
      form,
      seo,
      locale,
      trans,
      asset_path,
      metaTitle,
      metaDescription,
      metaKeywords,
      metaImage,
      metaCanonical,
      metaRobots
    };
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
        _push2(`<title data-v-18c67617${_scopeId}>${ssrInterpolate($setup.metaTitle)}</title><meta name="description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-18c67617${_scopeId}><meta name="keywords"${ssrRenderAttr("content", $setup.metaKeywords)} data-v-18c67617${_scopeId}><meta name="robots"${ssrRenderAttr("content", $setup.metaRobots)} data-v-18c67617${_scopeId}>`);
        if ($setup.metaCanonical) {
          _push2(`<link rel="canonical"${ssrRenderAttr("href", $setup.metaCanonical)} data-v-18c67617${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-18c67617${_scopeId}><meta property="og:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-18c67617${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta property="og:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-18c67617${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        if ($setup.metaCanonical) {
          _push2(`<meta property="og:url"${ssrRenderAttr("content", $setup.metaCanonical)} data-v-18c67617${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<meta property="og:type" content="website" data-v-18c67617${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-18c67617${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", $setup.metaTitle)} data-v-18c67617${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", $setup.metaDescription)} data-v-18c67617${_scopeId}>`);
        if ($setup.metaImage) {
          _push2(`<meta name="twitter:image"${ssrRenderAttr("content", $setup.metaImage)} data-v-18c67617${_scopeId}>`);
        } else {
          _push2(`<!---->`);
        }
      } else {
        return [
          createVNode("title", null, toDisplayString($setup.metaTitle), 1),
          createVNode("meta", {
            name: "description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "keywords",
            content: $setup.metaKeywords
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "robots",
            content: $setup.metaRobots
          }, null, 8, ["content"]),
          $setup.metaCanonical ? (openBlock(), createBlock("link", {
            key: 0,
            rel: "canonical",
            href: $setup.metaCanonical
          }, null, 8, ["href"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            property: "og:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 1,
            property: "og:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true),
          $setup.metaCanonical ? (openBlock(), createBlock("meta", {
            key: 2,
            property: "og:url",
            content: $setup.metaCanonical
          }, null, 8, ["content"])) : createCommentVNode("", true),
          createVNode("meta", {
            property: "og:type",
            content: "website"
          }),
          createVNode("meta", {
            name: "twitter:card",
            content: "summary_large_image"
          }),
          createVNode("meta", {
            name: "twitter:title",
            content: $setup.metaTitle
          }, null, 8, ["content"]),
          createVNode("meta", {
            name: "twitter:description",
            content: $setup.metaDescription
          }, null, 8, ["content"]),
          $setup.metaImage ? (openBlock(), createBlock("meta", {
            key: 3,
            name: "twitter:image",
            content: $setup.metaImage
          }, null, 8, ["content"])) : createCommentVNode("", true)
        ];
      }
    }),
    _: 1
  }, _parent));
  _push(ssrRenderComponent(_component_app_layout, null, {
    default: withCtx((_, _push2, _parent2, _scopeId) => {
      if (_push2) {
        _push2(`<section class="page-header" data-v-18c67617${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` })}" data-v-18c67617${_scopeId}></div><div class="container" data-v-18c67617${_scopeId}><div class="page-header__inner" data-v-18c67617${_scopeId}><h2 data-v-18c67617${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))}</h2><div class="thm-breadcrumb__box" data-v-18c67617${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-18c67617${_scopeId}><li data-v-18c67617${_scopeId}><a href="/" data-v-18c67617${_scopeId}><i class="fas fa-home" data-v-18c67617${_scopeId}></i> ${ssrInterpolate($setup.trans("Home"))}</a></li><li data-v-18c67617${_scopeId}><span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`)}" data-v-18c67617${_scopeId}></span></li><li data-v-18c67617${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))}</li></ul></div></div></div></section><section class="sign-up-one" data-v-18c67617${_scopeId}><div class="container" data-v-18c67617${_scopeId}><div class="sign-up-one__form" data-v-18c67617${_scopeId}><div class="inner-title text-center" data-v-18c67617${_scopeId}><h2 data-v-18c67617${_scopeId}>${ssrInterpolate($setup.trans("Set New Password"))}</h2></div><form id="reset-password__form" data-v-18c67617${_scopeId}><input${ssrRenderAttr("value", $setup.form.token)} name="token" type="hidden" data-v-18c67617${_scopeId}><div class="row" data-v-18c67617${_scopeId}><div class="col-xl-12" data-v-18c67617${_scopeId}><div class="form-group" data-v-18c67617${_scopeId}><div class="input-box" data-v-18c67617${_scopeId}><input id="email"${ssrRenderAttr("value", $setup.form.email)} type="email" name="email" autocomplete="email"${ssrRenderAttr("placeholder", $setup.trans("Email"))} class="${ssrRenderClass({ "error": $props.errors.email })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-18c67617${_scopeId}></div>`);
        if ($props.errors.email) {
          _push2(`<div class="text-danger mt-1 small" data-v-18c67617${_scopeId}>${ssrInterpolate($props.errors.email)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-18c67617${_scopeId}><div class="form-group" data-v-18c67617${_scopeId}><div class="input-box" data-v-18c67617${_scopeId}><input id="password"${ssrRenderAttr("value", $setup.form.password)} type="password" name="password" autocomplete="new-password"${ssrRenderAttr("placeholder", $setup.trans("Password"))} class="${ssrRenderClass({ "error": $props.errors.password })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-18c67617${_scopeId}></div>`);
        if ($props.errors.password) {
          _push2(`<div class="text-danger mt-1 small" data-v-18c67617${_scopeId}>${ssrInterpolate($props.errors.password)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-18c67617${_scopeId}><div class="form-group" data-v-18c67617${_scopeId}><div class="input-box" data-v-18c67617${_scopeId}><input id="password_confirmation"${ssrRenderAttr("value", $setup.form.password_confirmation)} type="password" name="password_confirmation" autocomplete="new-password"${ssrRenderAttr("placeholder", $setup.trans("Confirm Password"))} class="${ssrRenderClass({ "error": $props.errors.password_confirmation })}"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} required data-v-18c67617${_scopeId}></div>`);
        if ($props.errors.password_confirmation) {
          _push2(`<div class="text-danger mt-1 small" data-v-18c67617${_scopeId}>${ssrInterpolate($props.errors.password_confirmation)}</div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`</div></div><div class="col-xl-12" data-v-18c67617${_scopeId}><div class="form-group" data-v-18c67617${_scopeId}><button type="submit"${ssrIncludeBooleanAttr($setup.form.processing) ? " disabled" : ""} class="${ssrRenderClass([{ "opacity-50": $setup.form.processing }, "thm-btn"])}" data-v-18c67617${_scopeId}>`);
        if ($setup.form.processing) {
          _push2(`<span data-v-18c67617${_scopeId}><i class="fa-solid fa-spinner fa-spin me-2" data-v-18c67617${_scopeId}></i>${ssrInterpolate($setup.trans("Resetting..."))}</span>`);
        } else {
          _push2(`<span data-v-18c67617${_scopeId}>${ssrInterpolate($setup.trans("Reset Password"))}<span class="${ssrRenderClass(`icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `)}" data-v-18c67617${_scopeId}></span></span>`);
        }
        _push2(`</button></div></div><div class="col-xl-12" data-v-18c67617${_scopeId}><div class="create-account text-center" data-v-18c67617${_scopeId}><p data-v-18c67617${_scopeId}>`);
        _push2(ssrRenderComponent(_component_Link, {
          href: _ctx.route("login")
        }, {
          default: withCtx((_2, _push3, _parent3, _scopeId2) => {
            if (_push3) {
              _push3(`${ssrInterpolate($setup.trans("Back to Login"))}`);
            } else {
              return [
                createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
              ];
            }
          }),
          _: 1
        }, _parent2, _scopeId));
        _push2(`</p></div></div></div></form></div></div></section>`);
      } else {
        return [
          createVNode("section", { class: "page-header" }, [
            createVNode("div", {
              class: "page-header__bg",
              style: { backgroundImage: `url(${$setup.asset_path}images/backgrounds/login-bg.jpg)` }
            }, null, 4),
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "page-header__inner" }, [
                createVNode("h2", null, toDisplayString($setup.trans("Reset Password")), 1),
                createVNode("div", { class: "thm-breadcrumb__box" }, [
                  createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                    createVNode("li", null, [
                      createVNode("a", { href: "/" }, [
                        createVNode("i", { class: "fas fa-home" }),
                        createTextVNode(" " + toDisplayString($setup.trans("Home")), 1)
                      ])
                    ]),
                    createVNode("li", null, [
                      createVNode("span", {
                        class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow-1`
                      }, null, 2)
                    ]),
                    createVNode("li", null, toDisplayString($setup.trans("Reset Password")), 1)
                  ])
                ])
              ])
            ])
          ]),
          createVNode("section", { class: "sign-up-one" }, [
            createVNode("div", { class: "container" }, [
              createVNode("div", { class: "sign-up-one__form" }, [
                createVNode("div", { class: "inner-title text-center" }, [
                  createVNode("h2", null, toDisplayString($setup.trans("Set New Password")), 1)
                ]),
                createVNode("form", {
                  id: "reset-password__form",
                  onSubmit: withModifiers(($event) => $setup.form.post(_ctx.route("password.update")), ["prevent"])
                }, [
                  createVNode("input", {
                    value: $setup.form.token,
                    name: "token",
                    type: "hidden"
                  }, null, 8, ["value"]),
                  createVNode("div", { class: "row" }, [
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "email",
                            "onUpdate:modelValue": ($event) => $setup.form.email = $event,
                            type: "email",
                            name: "email",
                            autocomplete: "email",
                            placeholder: $setup.trans("Email"),
                            class: { "error": $props.errors.email },
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.email]
                          ])
                        ]),
                        $props.errors.email ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.email), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "password",
                            "onUpdate:modelValue": ($event) => $setup.form.password = $event,
                            type: "password",
                            name: "password",
                            autocomplete: "new-password",
                            placeholder: $setup.trans("Password"),
                            class: { "error": $props.errors.password },
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.password]
                          ])
                        ]),
                        $props.errors.password ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.password), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("div", { class: "input-box" }, [
                          withDirectives(createVNode("input", {
                            id: "password_confirmation",
                            "onUpdate:modelValue": ($event) => $setup.form.password_confirmation = $event,
                            type: "password",
                            name: "password_confirmation",
                            autocomplete: "new-password",
                            placeholder: $setup.trans("Confirm Password"),
                            class: { "error": $props.errors.password_confirmation },
                            disabled: $setup.form.processing,
                            required: ""
                          }, null, 10, ["onUpdate:modelValue", "placeholder", "disabled"]), [
                            [vModelText, $setup.form.password_confirmation]
                          ])
                        ]),
                        $props.errors.password_confirmation ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-danger mt-1 small"
                        }, toDisplayString($props.errors.password_confirmation), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "form-group" }, [
                        createVNode("button", {
                          class: ["thm-btn", { "opacity-50": $setup.form.processing }],
                          type: "submit",
                          disabled: $setup.form.processing
                        }, [
                          $setup.form.processing ? (openBlock(), createBlock("span", { key: 0 }, [
                            createVNode("i", { class: "fa-solid fa-spinner fa-spin me-2" }),
                            createTextVNode(toDisplayString($setup.trans("Resetting...")), 1)
                          ])) : (openBlock(), createBlock("span", { key: 1 }, [
                            createTextVNode(toDisplayString($setup.trans("Reset Password")), 1),
                            createVNode("span", {
                              class: `icon-${$setup.locale === "ar" ? "left" : "right"}-arrow `
                            }, null, 2)
                          ]))
                        ], 10, ["disabled"])
                      ])
                    ]),
                    createVNode("div", { class: "col-xl-12" }, [
                      createVNode("div", { class: "create-account text-center" }, [
                        createVNode("p", null, [
                          createVNode(_component_Link, {
                            href: _ctx.route("login")
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString($setup.trans("Back to Login")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ])
                      ])
                    ])
                  ])
                ], 40, ["onSubmit"])
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
const ResetPassword = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["ssrRender", _sfc_ssrRender], ["__scopeId", "data-v-18c67617"]]);
const __vite_glob_0_14 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ResetPassword
}, Symbol.toStringTag, { value: "Module" }));
const __default__$1 = {
  components: {
    AppLayout: _sfc_main$h
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
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => `${trans("404 Error")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("The page you are looking for could not be found.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("404 error, page not found, missing page");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
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
            _push2(`<title data-v-417e1791${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)} data-v-417e1791${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)} data-v-417e1791${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)} data-v-417e1791${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)} data-v-417e1791${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)} data-v-417e1791${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)} data-v-417e1791${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)} data-v-417e1791${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)} data-v-417e1791${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website" data-v-417e1791${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-417e1791${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)} data-v-417e1791${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)} data-v-417e1791${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)} data-v-417e1791${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<section class="page-header" data-v-417e1791${_scopeId}><div class="page-header__bg" style="${ssrRenderStyle({ "background-image": "url(assets/images/backgrounds/page-header-bg.jpg)" })}" data-v-417e1791${_scopeId}></div><div class="container" data-v-417e1791${_scopeId}><div class="page-header__inner" data-v-417e1791${_scopeId}><h2 data-v-417e1791${_scopeId}>${ssrInterpolate(trans("404 Error"))}</h2><div class="thm-breadcrumb__box" data-v-417e1791${_scopeId}><ul class="thm-breadcrumb list-unstyled" data-v-417e1791${_scopeId}><li data-v-417e1791${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: getHomeUrl()
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<i class="fas fa-home" data-v-417e1791${_scopeId2}></i>${ssrInterpolate(trans("Home"))}`);
                } else {
                  return [
                    createVNode("i", { class: "fas fa-home" }),
                    createTextVNode(toDisplayString(trans("Home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</li><li data-v-417e1791${_scopeId}><span class="icon-right-arrow-1" data-v-417e1791${_scopeId}></span></li><li data-v-417e1791${_scopeId}>${ssrInterpolate(trans("404 Error"))}</li></ul></div></div></div></section><section class="error-page" data-v-417e1791${_scopeId}><div class="container" data-v-417e1791${_scopeId}><div class="error-page__inner text-center" data-v-417e1791${_scopeId}><div class="error-page__img float-bob-y" data-v-417e1791${_scopeId}></div><div class="error-page__content" data-v-417e1791${_scopeId}><h2 data-v-417e1791${_scopeId}>${ssrInterpolate(trans("Oops! Page Not Found!"))}</h2><p data-v-417e1791${_scopeId}>${ssrInterpolate(trans("The page you are looking for does not exist. It might have been moved or deleted."))}</p><div class="btn-box" data-v-417e1791${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              class: "thm-btn",
              href: getHomeUrl()
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(trans("Back To Home"))} <span class="${ssrRenderClass(`icon-${_ctx.locale === "ar" ? "left" : "right"}-arrow `)}" data-v-417e1791${_scopeId2}></span>`);
                } else {
                  return [
                    createTextVNode(toDisplayString(trans("Back To Home")) + " ", 1),
                    createVNode("span", {
                      class: `icon-${_ctx.locale === "ar" ? "left" : "right"}-arrow `
                    }, null, 2)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div></div></section>`);
          } else {
            return [
              createVNode("section", { class: "page-header" }, [
                createVNode("div", {
                  class: "page-header__bg",
                  style: { "background-image": "url(assets/images/backgrounds/page-header-bg.jpg)" }
                }),
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "page-header__inner" }, [
                    createVNode("h2", null, toDisplayString(trans("404 Error")), 1),
                    createVNode("div", { class: "thm-breadcrumb__box" }, [
                      createVNode("ul", { class: "thm-breadcrumb list-unstyled" }, [
                        createVNode("li", null, [
                          createVNode(unref(Link), {
                            href: getHomeUrl()
                          }, {
                            default: withCtx(() => [
                              createVNode("i", { class: "fas fa-home" }),
                              createTextVNode(toDisplayString(trans("Home")), 1)
                            ]),
                            _: 1
                          }, 8, ["href"])
                        ]),
                        createVNode("li", null, [
                          createVNode("span", { class: "icon-right-arrow-1" })
                        ]),
                        createVNode("li", null, toDisplayString(trans("404 Error")), 1)
                      ])
                    ])
                  ])
                ])
              ]),
              createVNode("section", { class: "error-page" }, [
                createVNode("div", { class: "container" }, [
                  createVNode("div", { class: "error-page__inner text-center" }, [
                    createVNode("div", { class: "error-page__img float-bob-y" }),
                    createVNode("div", { class: "error-page__content" }, [
                      createVNode("h2", null, toDisplayString(trans("Oops! Page Not Found!")), 1),
                      createVNode("p", null, toDisplayString(trans("The page you are looking for does not exist. It might have been moved or deleted.")), 1),
                      createVNode("div", { class: "btn-box" }, [
                        createVNode(unref(Link), {
                          class: "thm-btn",
                          href: getHomeUrl()
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(trans("Back To Home")) + " ", 1),
                            createVNode("span", {
                              class: `icon-${_ctx.locale === "ar" ? "left" : "right"}-arrow `
                            }, null, 2)
                          ]),
                          _: 1
                        }, 8, ["href"])
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
const Error404 = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-417e1791"]]);
const __vite_glob_1_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Error404
}, Symbol.toStringTag, { value: "Module" }));
const __default__ = {
  components: {
    AppLayout: _sfc_main$h
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
    const appEnv = computed(() => page.props.app_env || "production");
    const isNonProduction = computed(() => appEnv.value !== "production");
    const meta = computed(() => page.props.meta || {});
    const metaTitle = computed(() => `${trans("500 Error")} | ${seo.value.website_name || ""}`.trim());
    const metaDescription = computed(() => {
      return meta.value.description || trans("An internal server error occurred. Please try again later.");
    });
    const metaKeywords = computed(() => {
      return meta.value.keywords || trans("500 error, server error, internal error");
    });
    const metaImage = computed(() => {
      var _a, _b, _c, _d;
      return ((_b = (_a = meta.value) == null ? void 0 : _a.og) == null ? void 0 : _b.image) || ((_d = (_c = meta.value) == null ? void 0 : _c.twitter) == null ? void 0 : _d.image) || "";
    });
    const metaCanonical = computed(() => meta.value.canonical || "");
    const metaRobots = computed(() => meta.value.robots || "noindex, nofollow");
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
            _push2(`<title data-v-f2dfa1b1${_scopeId}>${ssrInterpolate(metaTitle.value)}</title><meta name="description"${ssrRenderAttr("content", metaDescription.value)} data-v-f2dfa1b1${_scopeId}><meta name="keywords"${ssrRenderAttr("content", metaKeywords.value)} data-v-f2dfa1b1${_scopeId}><meta name="robots"${ssrRenderAttr("content", metaRobots.value)} data-v-f2dfa1b1${_scopeId}>`);
            if (metaCanonical.value) {
              _push2(`<link rel="canonical"${ssrRenderAttr("href", metaCanonical.value)} data-v-f2dfa1b1${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:title"${ssrRenderAttr("content", metaTitle.value)} data-v-f2dfa1b1${_scopeId}><meta property="og:description"${ssrRenderAttr("content", metaDescription.value)} data-v-f2dfa1b1${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", metaImage.value)} data-v-f2dfa1b1${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (metaCanonical.value) {
              _push2(`<meta property="og:url"${ssrRenderAttr("content", metaCanonical.value)} data-v-f2dfa1b1${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:type" content="website" data-v-f2dfa1b1${_scopeId}><meta name="twitter:card" content="summary_large_image" data-v-f2dfa1b1${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", metaTitle.value)} data-v-f2dfa1b1${_scopeId}><meta name="twitter:description"${ssrRenderAttr("content", metaDescription.value)} data-v-f2dfa1b1${_scopeId}>`);
            if (metaImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", metaImage.value)} data-v-f2dfa1b1${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("title", null, toDisplayString(metaTitle.value), 1),
              createVNode("meta", {
                name: "description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "keywords",
                content: metaKeywords.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "robots",
                content: metaRobots.value
              }, null, 8, ["content"]),
              metaCanonical.value ? (openBlock(), createBlock("link", {
                key: 0,
                rel: "canonical",
                href: metaCanonical.value
              }, null, 8, ["href"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 1,
                property: "og:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              metaCanonical.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:url",
                content: metaCanonical.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                name: "twitter:card",
                content: "summary_large_image"
              }),
              createVNode("meta", {
                name: "twitter:title",
                content: metaTitle.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:description",
                content: metaDescription.value
              }, null, 8, ["content"]),
              metaImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                name: "twitter:image",
                content: metaImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$h, null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p;
          if (_push2) {
            _push2(`<div class="breadcrumb__area breadcrumb-space overflow-hidden banner-home-bg" data-v-f2dfa1b1${_scopeId}><div class="banner-home__middel-shape inner-top-shape" data-v-f2dfa1b1${_scopeId}></div></div><section class="error section-space" data-v-f2dfa1b1${_scopeId}><div class="container" data-v-f2dfa1b1${_scopeId}><div class="row" data-v-f2dfa1b1${_scopeId}><div class="col-12" data-v-f2dfa1b1${_scopeId}><div class="error__content" data-v-f2dfa1b1${_scopeId}><div class="section__title-wrapper text-center" data-v-f2dfa1b1${_scopeId}><h3 class="section__title mb-15 mb-xs-10 wow fadeIn animated" data-wow-delay=".3s" data-v-f2dfa1b1${_scopeId}>${ssrInterpolate(trans("Internal Server Error"))}</h3><p class="mb-40 mb-sm-25 mb-xs-20 wow fadeIn animated" data-wow-delay=".5s" data-v-f2dfa1b1${_scopeId}>${ssrInterpolate(trans("We're sorry, but something went wrong on our end. Please try again later or contact support if the problem persists."))}</p>`);
            if (isNonProduction.value && (((_b = (_a = unref(page)) == null ? void 0 : _a.props) == null ? void 0 : _b.error) || ((_d = (_c = unref(page)) == null ? void 0 : _c.props) == null ? void 0 : _d.trace))) {
              _push2(`<div class="alert alert-danger text-start mb-40" style="${ssrRenderStyle({ "white-space": "pre-wrap", "word-break": "break-word" })}" data-v-f2dfa1b1${_scopeId}><strong data-v-f2dfa1b1${_scopeId}>Debug Error:</strong>`);
              if ((_f = (_e = unref(page)) == null ? void 0 : _e.props) == null ? void 0 : _f.error) {
                _push2(`<div data-v-f2dfa1b1${_scopeId}>${ssrInterpolate(unref(page).props.error)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if ((_h = (_g = unref(page)) == null ? void 0 : _g.props) == null ? void 0 : _h.trace) {
                _push2(`<details class="mt-3" data-v-f2dfa1b1${_scopeId}><summary data-v-f2dfa1b1${_scopeId}>Stack trace</summary><pre class="mt-2" data-v-f2dfa1b1${_scopeId}>${ssrInterpolate(unref(page).props.trace)}</pre></details>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="error-btn-wrap" data-v-f2dfa1b1${_scopeId}>`);
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
                createVNode("div", { class: "banner-home__middel-shape inner-top-shape" })
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
const Error500 = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-f2dfa1b1"]]);
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
            "../../Modules/Cms/resources/assets/js/Pages/PrivacyPolicy.vue": __vite_glob_0_5,
            "../../Modules/Cms/resources/assets/js/Pages/Team.vue": __vite_glob_0_6,
            "../../Modules/Cms/resources/assets/js/Pages/Testimonials.vue": __vite_glob_0_7,
            "../../Modules/Services/resources/assets/js/Pages/ServiceIndex.vue": __vite_glob_0_8,
            "../../Modules/Services/resources/assets/js/Pages/ServiceShow.vue": __vite_glob_0_9,
            "../../Modules/Support/resources/assets/js/Pages/Index.vue": __vite_glob_0_10,
            "../../Modules/User/resources/assets/js/Pages/Auth/ForgotPassword.vue": __vite_glob_0_11,
            "../../Modules/User/resources/assets/js/Pages/Auth/Login.vue": __vite_glob_0_12,
            "../../Modules/User/resources/assets/js/Pages/Auth/Register.vue": __vite_glob_0_13,
            "../../Modules/User/resources/assets/js/Pages/Auth/ResetPassword.vue": __vite_glob_0_14
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
