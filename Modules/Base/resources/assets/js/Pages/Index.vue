<template>
    <Head>
        <title>{{metaTitle}}</title>
        <meta name="description" :content="metaDescription">
        <meta name="keywords" :content="metaKeywords">
        <meta name="robots" :content="metaRobots">
        <link v-if="metaCanonical" rel="canonical" :href="metaCanonical">
        <meta property="og:title" :content="metaTitle">
        <meta property="og:description" :content="metaDescription">
        <meta v-if="metaImage" property="og:image" :content="metaImage">
        <meta v-if="metaCanonical" property="og:url" :content="metaCanonical">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" :content="metaTitle">
        <meta name="twitter:description" :content="metaDescription">
        <meta v-if="metaImage" name="twitter:image" :content="metaImage">
    </Head>
    <app-layout>
        <!--Banner One Start -->
        <section class="banner-one">
            <div class="banner-one__bg" aria-hidden="true">
                <img :src="asset_path + 'images/home/banner-bg.webp'"
                     alt=""
                     width="1920"
                     height="1080"
                     fetchpriority="high"
                     decoding="async"
                     class="banner-one__bg-img">
            </div>

            <div
                class="banner-one__shape-bg float-bob-y"
                :style="{
            backgroundImage: `url(${asset_path}images/shapes/banner-one-shape-bg.png)`
        }"
            ></div>

            <div class="container">
                <div class="banner-one__inner">
                    <h1 class="banner-one__title px-4">
                        {{ trans('Transform complex technical ideas into intelligent systems') }} <br/>
                        <span>{{
                                trans('Help companies build practical technology solutions in Web, AI, automation, and cloud computing — designed for growth and sustainability')
                            }}</span>
                    </h1>

                    <div class="banner-one__btn-box mb-5">
                        <Link :href="route('contact-us')" class="thm-btn contact-btn mx-2">
                            <span :class="locale === 'ar' ? 'icon-message ms-2' : 'icon-message me-2'"></span>
                            {{ trans('Book your free consultation') }}
                        </Link>
                        <Link :href="route('services.index')" class="thm-btn  ">
                            <span :class="locale === 'ar' ? 'icon-search ms-2' : 'icon-search me-2'"></span>
                            {{ trans('Explore Our Services') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>


        <!--About Three Start -->
        <section class="about-three">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div :class="`about-three__left wow slideIn${locale !== 'ar' ? 'Left' : 'Right'}`"
                             data-wow-delay="100ms"
                             data-wow-duration="2500ms">
                            <div class="about-three__img-box">
                                <div class="about-three__img">
                                    <picture>
                                        <source :srcset="asset_path + 'images/home/about_us-640.webp'" type="image/webp">
                                        <img :src="asset_path + 'images/home/about_us.jpg'"
                                             :alt="trans('About us')"
                                             width="640"
                                             height="640"
                                             loading="lazy"
                                             decoding="async">
                                    </picture>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="about-three__right">
                            <div class="section-title  sec-title-animation animation-style1">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Our Tech Solutions') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Why Choose Symfonix for Web, AI, and Cloud') }}
                                </h2>
                            </div>

                            <p class="about-three__text">
                                {{
                                    trans("Transform your business with our innovative IT solutions, tailored to address your unique challenges and drive growth in today's digital landscape.")
                                }}
                            </p>

                            <ul class="about-three__points list-unstyled">
                                            <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <div class="content">
                                        <h2 class="h3">{{ trans('Developing Secure & Scalable Systems') }}</h2>
                                        <!--                                        <p>{{ trans('Support & Consulting') }}</p>-->
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <div class="content">
                                        <h2 class="h3">{{ trans('Innovative IT Solutions Expert') }}</h2>
                                        <!--                                        <p>{{ trans('Support & Consulting') }}</p>-->
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <div class="content">
                                        <h2 class="h3">{{ trans('Cloud Solutions for Modern') }}</h2>
                                        <!--                                        <p>{{ trans('Enterprises') }}</p>-->
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-tick-inside-circle"></span>
                                    </div>
                                    <div class="content">
                                        <h2 class="h3">{{ trans('AI-Driven Business Automation') }}</h2>
                                        <!--                                        <p>{{ trans('AI-Driven Business Automation') }}</p>-->
                                    </div>
                                </li>
                            </ul>
                            <div class="about-three__btn-and-call-box">
                                <div class="about-three__btn-box">
                                    <a :href="route('about-us')" class="thm-btn">
                                        {{ trans('Get in Touch') }}
                                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                    </a>
                                </div>
                                <div class="about-three__call-box">
                                    <div class="icon">
                                        <span class="icon-customer-service-headset"></span>
                                    </div>
                                    <div class="content">
                                        <span>{{ trans('Call Any Time') }}</span>
                                        <p><a dir="ltr" :href="`tel:${settings.phone}`">{{ settings.phone }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--About Three End -->

        <!--Services Three Start -->
        <section class="services-three" v-if="servicesCategories && servicesCategories.length">

            <div class="container">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Our Services') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation core-services-title">
                        {{ trans('What We Do') }}! <span>{{ trans('Core Services') }}</span>
                    </h2>
                </div>
                <div class="services-three__carousel owl-theme owl-carousel">
                    <div
                        v-for="servicesCategory in servicesCategories"
                        :key="servicesCategory.id"
                        class="item"
                    >
                        <ServiceCardThree
                            :title="translateField(servicesCategory.title)"
                            :short-desc="translateField(servicesCategory.description)"
                            :description="translateField(servicesCategory.description)"
                            :highlights="getCategoryHighlights(servicesCategory)"
                            :link="route('services.index', { category: servicesCategory.slug })"
                            :image="servicesCategory.image_link"
                            :is-rtl="locale === 'ar'"
                        />
                    </div>
                </div>
                <div class="text-center mt-4">
                    <Link :href="route('services.index')" class="thm-btn">
                        {{ trans('View All Services') }}
                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                    </Link>
                </div>
            </div>
        </section>
        <!--Services Three End -->

        <!-- Products Start -->
        <section class="home-products products-page" v-if="products && products.length">
            <div class="products-page__bg" aria-hidden="true">
                <div class="products-page__orb products-page__orb--one"></div>
                <div class="products-page__orb products-page__orb--two"></div>
                <div class="products-page__orb products-page__orb--three"></div>
            </div>
            <div class="container position-relative">
                <div class="section-title text-center sec-title-animation animation-style1 home-products__head">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Our Products') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('B2B Solutions Built for Scale') }}
                    </h2>
                    <p class="home-products__intro">
                        {{ trans('Discover enterprise-ready platforms and services designed to grow with your business.') }}
                    </p>
                </div>

                <div class="home-products__grid">
                    <div class="row g-4">
                        <div
                            v-for="(product, index) in products"
                            :key="product.id"
                            class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                            :data-wow-delay="`${(index % 3 + 1) * 100}ms`"
                        >
                            <ProductCard :item="product" :locale="locale" />
                        </div>
                    </div>
                </div>

                <div class="text-center home-products__cta">
                    <Link :href="route('product.index')" class="thm-btn">
                        {{ trans('View All Products') }}
                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                    </Link>
                </div>
            </div>
        </section>
        <!-- Products End -->

        <!-- Case Studies Start -->
        <section class="home-case-studies use-cases-page" v-if="useCases && useCases.length">
            <div class="use-cases-page__bg" aria-hidden="true">
                <div class="use-cases-page__orb use-cases-page__orb--one"></div>
                <div class="use-cases-page__orb use-cases-page__orb--two"></div>
            </div>
            <div class="container position-relative">
                <div class="section-title text-center sec-title-animation animation-style1">
                    <div class="section-title__tagline-box">
                        <div class="section-title__tagline-shape-1"></div>
                        <span class="section-title__tagline">{{ trans('Case Studies') }}</span>
                        <div class="section-title__tagline-shape-2"></div>
                    </div>
                    <h2 class="section-title__title title-animation">
                        {{ trans('How We\'ve Empowered Businesses with Innovative Tech Solutions') }}
                    </h2>
                    <p class="home-case-studies__intro">
                        {{ trans('Explore our success stories and real-world solutions we\'ve delivered for businesses.') }}
                    </p>
                </div>

                <div class="use-cases-page__grid">
                    <div class="row g-4">
                        <div
                            v-for="item in useCases"
                            :key="item.id"
                            class="col-xl-4 col-lg-6 col-md-6 wow fadeInUp"
                            data-wow-delay="100ms"
                        >
                            <UseCaseCard :item="item" :locale="locale" />
                        </div>
                    </div>
                </div>

                <div class="text-center home-case-studies__cta">
                    <Link :href="route('use-cases.index')" class="thm-btn">
                        {{ trans('View All Case Studies') }}
                        <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                    </Link>
                </div>
            </div>
        </section>
        <!-- Case Studies End -->

        <!--Why Choose Two Start -->
        <section class="why-choose-two">
            <div class="why-choose-two__shape-1 float-bob-y">
                <img :src="asset_path + 'images/shapes/why-choose-two-shape-1.png'"
                     :alt="trans('Decorative shape')"
                     width="120"
                     height="120"
                     loading="lazy"
                     decoding="async"
                     aria-hidden="true">
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div :class="`why-choose-two__left wow ${locale !== 'ar' ? 'Left' : 'Right'}`"
                             data-wow-delay="100ms"
                             data-wow-duration="2500ms">
                            <div class="why-choose-two__img">
                                <picture>
                                    <source :srcset="asset_path + 'images/home/why_choose_us-640.webp'" type="image/webp">
                                    <img :src="asset_path + 'images/home/why_choose_us.jpg'"
                                         :alt="trans('Why choose us')"
                                         width="640"
                                         height="640"
                                         loading="lazy"
                                         decoding="async">
                                </picture>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="why-choose-two__right">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Why Choose Us') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('How do we deliver reliable, future-ready IT solutions?') }}
                                </h2>
                            </div>
                            <p class="why-choose-one__text">
                                {{
                                    trans('We deliver exceptional products and services that consistently exceed expectations. Backed by years of experience and a proven track record, we are your reliable partner for success.')
                                }}
                            </p>
                            <ul class="list-unstyled why-choose-two__points">
                                <li>
                                    <div class="icon">
                                        <span class="icon-quality"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('Unmatched Quality') }}</h3>
                                        <p>
                                            {{
                                                trans('We deliver exceptional products and services that exceed expectations every time.')
                                            }}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-team"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('Trusted Expertise') }}</h3>
                                        <p>
                                            {{
                                                trans('Backed by years of experience and a proven track record, we are your reliable partner for success.')
                                            }}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="icon">
                                        <span class="icon-customer-centricity"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('User-Centric Approach') }}</h3>
                                        <p>
                                            {{
                                                trans('Your satisfaction is our priority, and we tailor solutions to meet your unique needs. Your happiness comes first.')
                                            }}
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Why Choose Two End -->

        <!-- Team Two Start -->
        <section class="team-two d-none" v-if="teams && teams.length > 0">
            <div class="team-two__bg-shape float-bob-y"
                 :style="{ backgroundImage: `url(${asset_path}images/shapes/team-two-bg-shape.png)` }">
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-xl-5">
                        <div class="team-two__left">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Our Members') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Meet Our Team.') }} <span>{{ trans('Get to') }}</span><br>
                                    <span>{{ trans('Know the Talented') }}</span>
                                    {{ trans('Minds Behind Our Team') }}
                                </h2>
                            </div>
                            <p class="team-two__text">
                                {{
                                    trans('Our dedicated team combines expertise, creativity, and passion to deliver exceptional results and ensure your satisfaction every step of the way.')
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="col-xl-7">
                        <div class="team-two__right">
                            <div class="team-two__carousel owl-theme owl-carousel">
                                <!--Team Two Single Start-->
                                <div class="item" v-for="team in teams" :key="team.id">
                                    <div class="team-two__single">
                                        <div class="team-two__img-box">
                                            <div class="team-two__img">
                                                <img :src="team.avatar_link"
                                                     :alt="translateField(team.name)"
                                                     width="200"
                                                     height="200"
                                                     loading="lazy"
                                                     decoding="async">
                                            </div>
                                            <div class="team-two__social">
                                                <a v-if="team.facebook" :href="team.facebook" target="_blank" rel="noopener noreferrer" :aria-label="`${translateField(team.name)} Facebook`">
                                                    <span class="icon-facebook"></span>
                                                </a>
                                                <a v-if="team.behance" :href="team.behance" target="_blank" rel="noopener noreferrer" :aria-label="`${translateField(team.name)} Behance`">
                                                    <span class="icon-dribble"></span>
                                                </a>
                                                <a v-if="team.linked_in" :href="team.linked_in" target="_blank" rel="noopener noreferrer" :aria-label="`${translateField(team.name)} LinkedIn`">
                                                    <span class="icon-linkedin"></span>
                                                </a>
                                                <a v-if="team.github" :href="team.github" target="_blank" rel="noopener noreferrer" :aria-label="`${translateField(team.name)} GitHub`">
                                                    <span class="icon-github"></span>
                                                </a>
                                            </div>
                                            <div class="team-two__title-box">
                                                <h3><span>{{ translateField(team.name) }}</span></h3>
                                                <p>{{ translateField(team.position) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Team Two Single End-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Team Two End -->

        <!--Feature One Start -->
        <section class="feature-one">

            <div class="feature-one__shape-2 float-bob-y">
                <img :src="asset_path + 'images/shapes/feature-one-shape-2.png'"
                     :alt="trans('Decorative shape')"
                     width="120"
                     height="120"
                     loading="lazy"
                     decoding="async"
                     aria-hidden="true">
            </div>
            <div class="container">
                <div class="row">
                    <!--Feature One Single Start -->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="100ms">
                        <div class="feature-one__single">
                            <div class="feature-one__img">
                                <picture>
                                    <source :srcset="asset_path + 'images/home/website.webp'" type="image/webp">
                                    <img :src="asset_path + 'images/home/website.png'"
                                         :alt="trans('Web Development')"
                                         width="80"
                                         height="80"
                                         loading="lazy"
                                         decoding="async">
                                </picture>
                            </div>
                            <h3 class="feature-one__title">
                                <a :href="route('services.index')">
                                    {{ trans('Web Development') }}
                                </a>
                            </h3>
                            <p class="feature-one__text">
                                {{
                                    trans('Custom web solutions built with cutting-edge technology to drive your business forward.')
                                }}
                            </p>
                        </div>
                    </div>
                    <!--Feature One Single End -->
                    <!--Feature One Single Start -->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="300ms">
                        <div class="feature-one__single">
                            <div class="feature-one__img">
                                <picture>
                                    <source :srcset="asset_path + 'images/home/app-development.webp'" type="image/webp">
                                    <img :src="asset_path + 'images/home/app-development.png'"
                                         :alt="trans('Mobile Development')"
                                         width="80"
                                         height="80"
                                         loading="lazy"
                                         decoding="async">
                                </picture>
                            </div>
                            <h3 class="feature-one__title">
                                <a :href="route('services.index')">
                                    {{ trans('Mobile Development') }}
                                </a>
                            </h3>
                            <p class="feature-one__text">
                                {{
                                    trans('Native and cross-platform mobile applications that deliver exceptional user experiences.')
                                }}
                            </p>
                        </div>
                    </div>
                    <!--Feature One Single End -->
                    <!--Feature One Single Start -->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="500ms">
                        <div class="feature-one__single">
                            <div class="feature-one__img">
                                <picture>
                                    <source :srcset="asset_path + 'images/home/microchip.webp'" type="image/webp">
                                    <img :src="asset_path + 'images/home/microchip.png'"
                                         :alt="trans('AI Agents & Automation')"
                                         width="80"
                                         height="80"
                                         loading="lazy"
                                         decoding="async">
                                </picture>
                            </div>
                            <h3 class="feature-one__title">
                                <a :href="route('services.index')">
                                    {{ trans('AI Agents & Automation') }}
                                </a>
                            </h3>
                            <p class="feature-one__text">
                                {{
                                    trans('Intelligent automation solutions powered by AI to streamline your business processes.')
                                }}
                            </p>
                        </div>
                    </div>
                    <!--Feature One Single End -->
                    <!--Feature One Single Start -->
                    <div class="col-xl-3 col-lg-6 col-md-6 wow fadeInUp" data-wow-delay="700ms">
                        <div class="feature-one__single">
                            <div class="feature-one__img">
                                <picture>
                                    <source :srcset="asset_path + 'images/home/cloud.webp'" type="image/webp">
                                    <img :src="asset_path + 'images/home/cloud.png'"
                                         :alt="trans('Cloud & Infrastructure')"
                                         width="80"
                                         height="80"
                                         loading="lazy"
                                         decoding="async">
                                </picture>
                            </div>
                            <h3 class="feature-one__title">
                                <a :href="route('services.index')">
                                    {{ trans('Cloud & Infrastructure') }}
                                </a>
                            </h3>
                            <p class="feature-one__text">
                                {{
                                    trans('Secure, scalable, and efficient cloud services to power your growth and digital transformation.')
                                }}
                            </p>
                        </div>
                    </div>
                    <!--Feature One Single End -->
                </div>
            </div>
        </section>
        <!--Feature One End -->
        <!--CTA One Start -->
        <section class="cta-one">
            <div class="cta-one__shape-bg float-bob-y"
                 :style="{ backgroundImage: `url(${asset_path}images/shapes/cta-one-shape-bg.png)` }">
            </div>
            <div class="container">
                <div class="cta-one__inner">
                    <h3 class="cta-one__title">
                        {{ trans('Let the Symfonix experts review your technical idea') }}
                    </h3>
                    <div class="cta-one__contact-info">
                        <div class="cta-one__contact-icon">
                            <span class="icon-customer-service-headset"></span>
                        </div>
                        <div class="cta-one__contact-details">
                            <p>{{ trans('Book your free consultation') }}</p>
                            <h4><a dir="ltr" :href="`tel:${settings.phone}`">{{ settings.phone }}</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--CTA One End -->

        <!-- Testimonial One Start -->
        <section class="testimonial-one pb-5" v-if="testimonials && testimonials.length">

            <div class="testimonial-one__shape-2 float-bob-y">
                <img :src="asset_path + 'images/shapes/testimonial-one-shape-2.png'"
                     :alt="trans('Decorative shape')"
                     width="120"
                     height="120"
                     loading="lazy"
                     decoding="async"
                     aria-hidden="true">
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-3"></div>
                    <div class="col-xl-9">
                        <div class="testimonial-one__content-box">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Testimonials') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('What Our Clients Say') }}
                                </h2>
                            </div>
                            <div class="testimonial-one__carousel owl-theme owl-carousel">
                                <!-- Testimonial One Single Start -->
                                <div class="item" v-for="testimonial in testimonials" :key="testimonial.id">
                                    <div class="testimonial-one__single">
                                        <div class="testimonial-one__img-box">
                                            <div class="testimonial-one__img">
                                                <img :src="testimonial.avatar_link"
                                                     :alt="translateField(testimonial.name)"
                                                     width="180"
                                                     height="180"
                                                     loading="lazy"
                                                     decoding="async">
                                            </div>
                                        </div>
                                        <div class="testimonial-one__content">

                                            <p class="testimonial-one__text">
                                                “{{ translateField(testimonial.quote) }}”
                                            </p>
                                            <div class="testimonial-one__bottom">
                                                <div class="testimonial-one__quote-and-client-info">
                                                    <div class="testimonial-one__quote">
                                                        <span class="icon-left"></span>
                                                    </div>
                                                    <div class="testimonial-one__client-info">
                                                        <p class="testimonial-one__client-sub-title">
                                                            {{ translateField(testimonial.position) }}
                                                        </p>
                                                        <h3 class="testimonial-one__client-name">
                                                            {{ translateField(testimonial.name) }}
                                                        </h3>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Testimonial One Single End -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Testimonial One End -->

        <!-- Blog Two Start -->
        <section class="blog-two blog-three" v-if="posts && posts.length">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div :class="`blog-two__left wow fadeIn${locale !== 'ar' ? 'Left' : 'Right'}`"
                             data-wow-delay="100ms">
                            <div class="section-title text-left sec-title-animation animation-style1">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">{{ trans('Our Blogs') }}</span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Explore Our Latest Blogs for Expert Insights') }}
                                </h2>
                            </div>
                            <p class="blog-two-text">
                                {{
                                    trans('Dive into our collection of blogs where we share expert insights, helpful tips, and the latest trends in the industry')
                                }}
                            </p>
                            <div class="blog-two__top-btn-box">
                                <Link :href="route('blogs.index')" class="thm-btn">
                                    {{ trans('View All Blogs') }}
                                    <span :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow `"></span>
                                </Link>
                            </div>

                            <!-- Featured blog (left) -->
                            <div class="blog-two__left-content-box d-none d-md-block" v-if="featuredPost">
                                <HomeBlogCard
                                    :post="featuredPost"
                                    variant="featured"
                                    :locale="locale"
                                    :asset-path="asset_path"
                                    :image-fallback-index="1"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="blog-two__right">
                            <!-- Right-side compact posts -->
                            <HomeBlogCard
                                v-for="(post, idx) in sidePosts"
                                :key="post.id || idx"
                                :post="post"
                                variant="compact"
                                :locale="locale"
                                :asset-path="asset_path"
                                :image-fallback-index="idx + 2"
                                :animation-class="idx % 2 === 0 ? 'fadeInLeft' : 'fadeInRight'"
                                :animation-delay="`${(idx + 1) * 100}ms`"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Two End -->

        <!-- Contact Two Start -->
        <section class="contact-two">
            <div class="contact-two__sliding-text-list marquee_mode-2">
                <div class="contact-two__sliding-text-item">
                    <h2 data-hover="Branding" class="contact-two__sliding-text-title">
                        {{ trans('GET IN TOUCH *') }}
                    </h2>
                </div>
                <div class="contact-two__sliding-text-item">
                    <h2 data-hover="Branding" class="contact-two__sliding-text-title">
                        {{ trans('GET IN TOUCH *') }}
                    </h2>
                </div>
                <div class="contact-two__sliding-text-item">
                    <h2 data-hover="Branding" class="contact-two__sliding-text-title">
                        {{ trans('GET IN TOUCH *') }}
                    </h2>
                </div>
            </div>

            <div class="contact-two__shape-1 float-bob-y">
                <img :src="asset_path + 'images/shapes/contact-two-shape-1.png'"
                     :alt="trans('Decorative shape')"
                     width="120"
                     height="120"
                     loading="lazy"
                     decoding="async"
                     aria-hidden="true">
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="contact-two__left">
                            <div class="section-title text-left sec-title-animation animation-style2">
                                <div class="section-title__tagline-box">
                                    <div class="section-title__tagline-shape-1"></div>
                                    <span class="section-title__tagline">
                                        {{ trans('Get In Touch') }}
                                    </span>
                                    <div class="section-title__tagline-shape-2"></div>
                                </div>
                                <h2 class="section-title__title title-animation">
                                    {{ trans('Contact Us') }}
                                </h2>
                            </div>
                            <p class="contact-two__text">
                                {{ trans("Fill out the form below and we'll get back to you as soon as possible") }}
                            </p>
                            <ul class="contact-two__contact-list list-unstyled">
                                <li v-if="settings.email">
                                    <div class="icon">
                                        <span class="icon-mail"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('Email') }}</h3>
                                        <p>
                                            <a dir="ltr" :href="`mailto:${settings.email}`">
                                                {{ settings.email }}
                                            </a>
                                        </p>
                                    </div>
                                </li>
                                <li v-if="settings.phone">
                                    <div class="icon">
                                        <span class="icon-phone-call"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('Phone') }}</h3>
                                        <p>
                                            <a dir="ltr" :href="`tel:${settings.phone}`">
                                                {{ settings.phone }}
                                            </a>
                                        </p>
                                    </div>
                                </li>
                                <li v-if="settings.address">
                                    <div class="icon">
                                        <span class="icon-pin"></span>
                                    </div>
                                    <div class="content">
                                        <h3 class="h4">{{ trans('Our Location') }}</h3>
                                        <p>{{ settings.address }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div :class="`contact-two__right wow slideIn${locale === 'ar' ? 'Left' : 'Right'}`"
                             data-wow-delay="100ms"
                             data-wow-duration="2500ms">
                            <form class="contact-form-validated contact-one__form"
                                  @submit.prevent="handleContactSubmit">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6">
                                        <h3 class="h4 contact-one__input-title">{{ trans("Full Name") }}</h3>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-user-1"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.name"
                                                type="text"
                                                name="name"
                                                :placeholder="trans('Full Name')"
                                                :class="{ 'error': contactForm.errors.name }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.name" class="text-danger mt-1 small">
                                            {{ contactForm.errors.name }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h3 class="h4 contact-one__input-title">
                                            {{ trans('Email') }}
                                        </h3>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-email"></span>
                                            </div>
                                            <input
                                                type="email"
                                                name="email"
                                                v-model="contactForm.email"
                                                :placeholder="trans('Email')"
                                                :disabled="contactForm.processing"
                                                :class="{ 'error': contactForm.errors.email }"
                                                required
                                            >
                                            <div v-if="contactForm.errors.email" class="text-danger mt-1 small">
                                                {{ contactForm.errors.email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h3 class="h4 contact-one__input-title">{{ trans("Phone Number") }}</h3>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-phone-call"></span>
                                            </div>
                                            <input
                                                v-model="contactForm.mobile"
                                                type="text"
                                                name="mobile"
                                                :placeholder="trans('Phone Number')"
                                                :class="{ 'error': contactForm.errors.mobile }"
                                                :disabled="contactForm.processing"
                                                required>
                                        </div>
                                        <div v-if="contactForm.errors.mobile" class="text-danger mt-1 small">
                                            {{ contactForm.errors.mobile }}
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6">
                                        <h3 class="h4 contact-one__input-title">
                                            {{ trans('Subject') }}
                                        </h3>
                                        <div class="contact-one__input-box">
                                            <div class="contact-one__input-icon">
                                                <span class="icon-edit"></span>
                                            </div>
                                            <input
                                                type="text"
                                                name="subject"
                                                v-model="contactForm.subject"
                                                :placeholder="trans('Subject')"
                                                :disabled="contactForm.processing"
                                                :class="{ 'error': contactForm.errors.subject }"
                                                required
                                            >
                                            <div v-if="contactForm.errors.subject" class="text-danger mt-1 small">
                                                {{ contactForm.errors.subject }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12">
                                    <h3 class="h4 contact-one__input-title">{{ trans("Message") }}</h3>
                                    <div class="contact-one__input-box text-message-box">
                                        <div class="contact-one__input-icon">
                                            <span class="icon-edit"></span>
                                        </div>
                                        <textarea
                                            v-model="contactForm.message"
                                            name="message"
                                            :placeholder="trans('Write your message')"
                                            :class="{ 'error': contactForm.errors.message }"
                                            :disabled="contactForm.processing"
                                            required></textarea>
                                    </div>
                                    <div v-if="contactForm.errors.message" class="text-danger mt-1 small">
                                        {{ contactForm.errors.message }}
                                    </div>
                                    <div class="contact-one__btn-box">
                                        <button
                                            type="submit"
                                            class="thm-btn"
                                            :disabled="contactForm.processing"
                                            :class="{ 'opacity-50': contactForm.processing }">
                                            <span v-if="contactForm.processing">
                                                <i class="fa-solid fa-spinner fa-spin me-2"></i>{{
                                                    trans("Sending...")
                                                }}
                                            </span>
                                            <span v-else>
                                                <span>{{ trans("Submit") }}</span> <i
                                                :class="`icon-${locale === 'ar' ? 'left' : 'right'}-arrow mx-1`"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <div v-if="contactSubmitSuccess" class="col-12 mt-3">
                                    <div class="alert alert-success">
                                        {{ trans("Thank you for contacting us! We will get back to you soon.") }}
                                    </div>
                                </div>
                            </form>
                            <div class="result"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact Two End -->

        <ClientsSection :clients="clients" />
    </app-layout>
</template>

<script setup>
import {computed, onMounted, nextTick, ref} from 'vue'
import {usePage, useForm, Link, Head} from '@inertiajs/vue3'
import HomeBlogCard from '@/Components/HomeBlogCard.vue'
import ServiceCardThree from '@/Components/Services/ServiceCardThree.vue'
import ClientsSection from '@/Components/ClientsSection.vue'

const page = usePage()
const trans = (key) => page.props.translations[key] || key;
const seo = computed(() => page.props.seo)
const settings = computed(() => page.props.settings || {})
const asset_path = computed(() => page.props.asset_path || '')
const locale = computed(() => page.props.locale)
const posts = computed(() => page.props.posts || [])
const servicesCategories = computed(() => page.props.servicesCategories || [])
const testimonials = computed(() => page.props.testimonials || [])
const teams = computed(() => page.props.teams || [])
const useCases = computed(() => page.props.useCases || [])
const products = computed(() => page.props.products || [])
const clients = computed(() => page.props.clients || [])
const meta = computed(() => page.props.meta || {})

const metaTitle = computed(() => {
    return meta.value.title || `${trans("Home")} | ${seo.value.website_name || ''}`.trim()
})
const metaDescription = computed(() => {
    return meta.value.description
        || trans('Empowering businesses with modern web, mobile, AI, and cloud solutions.')
        || seo.value.website_desc
        || ''
})
const metaKeywords = computed(() => {
    return meta.value.keywords
        || trans('IT solutions, web development, mobile apps, AI automation, cloud services')
        || seo.value.website_keywords
        || ''
})
const metaImage = computed(() => {
    return meta.value?.og?.image || meta.value?.twitter?.image || settings.value?.meta_img || ''
})
const metaCanonical = computed(() => meta.value.canonical || '')
const metaRobots = computed(() => meta.value.robots || 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1')

const featuredPost = computed(() => posts.value?.[0] || null)
const sidePosts = computed(() => (posts.value || []).slice(1, 5))


const translateField = (value) => {
    if (!value) {
        return '';
    }

    // If it's already a string, just return it.
    if (typeof value === 'string') {
        return value;
    }

    // If it's an object/array of translations, pick the current locale if available.
    const loc = locale.value;
    if (typeof value === 'object' && value !== null) {
        if (value[loc]) {
            return value[loc];
        }
    }

    return '';
};

const getCategoryHighlights = (category) => {
    if (!category || !Array.isArray(category.services)) {
        return [];
    }
    return category.services
        .map((service) => translateField(service.title))
        .filter(Boolean);
};


// Contact form (shared with Support::ContactUsController)
const contactSubmitSuccess = ref(false)

const contactForm = useForm({
    name: '',
    email: '',
    mobile: '',
    subject: '',
    message: '',
})

const handleContactSubmit = () => {
    if (contactForm.processing) {
        return false;
    }

    if (!contactForm.name || !contactForm.name.trim()) return false;
    if (!contactForm.email || !contactForm.email.trim()) return false;
    if (!contactForm.mobile || !contactForm.mobile.trim()) return false;
    if (!contactForm.subject || !contactForm.subject.trim()) return false;
    if (!contactForm.message || !contactForm.message.trim()) return false;

    let contactUrl = route('contact-us.store');
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
            }, 5000);
        },
        onError: () => {
            contactSubmitSuccess.value = false;
        },
    });

    return false;
};

onMounted(() => {
    nextTick(() => {
        const isRTL = page.props.locale === 'ar';
        const applyOwlDotAriaLabels = ($carousel) => {
            if (!$carousel || !$carousel.length) {
                return;
            }

            $carousel.find('.owl-dot').each(function (index) {
                $(this).attr('aria-label', `${trans('Go to slide')} ${index + 1}`);
            });
        };

        const waitForTheme = (predicate, tries = 40) => new Promise((resolve) => {
            const tick = () => {
                if (predicate()) {
                    resolve(true);
                    return;
                }
                if (tries-- <= 0) {
                    resolve(false);
                    return;
                }
                setTimeout(tick, 150);
            };
            tick();
        });

        const initHomeWidgets = () => {
        // Initialize Services Owl Carousel
        if (typeof $ !== 'undefined' && typeof $.fn?.owlCarousel === 'function' && $('.services-three__carousel').length) {
            const $servicesCarousel = $('.services-three__carousel');
            if (!$servicesCarousel.hasClass('owl-loaded')) {
            $servicesCarousel.owlCarousel({
                loop: servicesCategories.value.length > 3,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: isRTL,
                responsive: {
                    0: {items: 1},
                    768: {items: 2},
                    992: {items: 3},
                    1200: {items: 3}
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function () {
                applyOwlDotAriaLabels($(this));
            });
            applyOwlDotAriaLabels($servicesCarousel);
            }
        }

        // Initialize Team Owl Carousel
        if (typeof $ !== 'undefined' && typeof $.fn?.owlCarousel === 'function' && $('.team-two__carousel').length && teams.value.length > 0) {
            const $teamCarousel = $('.team-two__carousel');
            if (!$teamCarousel.hasClass('owl-loaded')) {
            $teamCarousel.owlCarousel({
                loop: teams.value.length > 3,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: isRTL,
                responsive: {
                    0: {items: 1},
                    768: {items: 2},
                    992: {items: 2},
                    1200: {items: 3}
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function () {
                applyOwlDotAriaLabels($(this));
            });
            applyOwlDotAriaLabels($teamCarousel);
            }
        }

        // Initialize Testimonials Owl Carousel
        if (typeof $ !== 'undefined' && typeof $.fn?.owlCarousel === 'function' && $('.testimonial-one__carousel').length) {
            const $testimonialCarousel = $('.testimonial-one__carousel');
            if (!$testimonialCarousel.hasClass('owl-loaded')) {
            $testimonialCarousel.owlCarousel({
                loop: testimonials.value.length > 1,
                margin: 30,
                nav: false,
                dots: true,
                smartSpeed: 500,
                autoplay: true,
                autoplayTimeout: 7000,
                rtl: isRTL,
                responsive: {
                    0: {items: 1},
                    768: {items: 1},
                    992: {items: 1},
                    1200: {items: 1}
                }
            }).on('initialized.owl.carousel refreshed.owl.carousel', function () {
                applyOwlDotAriaLabels($(this));
            });
            applyOwlDotAriaLabels($testimonialCarousel);
            }
        }

        // Initialize WOW animations
        if (typeof WOW !== 'undefined') {
            new WOW().init();
        }

        // Initialize GSAP title animations
        // Use the same comprehensive implementation as script.js but handle Arabic properly
        if (typeof gsap !== 'undefined' && typeof SplitText !== 'undefined') {
            const titleAnimations = document.querySelectorAll(".sec-title-animation .title-animation");
            if (titleAnimations.length) {
                titleAnimations.forEach(quote => {
                    // Reset if needed
                    if (quote.animation) {
                        quote.animation.progress(1).kill();
                        if (quote.split) quote.split.revert();
                    }

                    // Handle Arabic text - capitalize and set proper styling
                    if (isRTL) {
                        const text = quote.textContent;
                        if (/[\u0600-\u06FF]/.test(text)) {
                            quote.style.textTransform = 'none';
                            quote.style.fontVariant = 'normal';
                            // Don't set fontSize to inherit - let CSS handle it
                            // quote.style.fontSize = 'inherit'; // REMOVED - was causing small font size
                        }
                    }

                    var getclass = quote.closest('.sec-title-animation').className;
                    var animation = getclass.split('animation-');
                    if (animation[1] == "style4") return;

                    // Check if text contains Arabic characters
                    const text = quote.textContent;
                    const hasArabic = /[\u0600-\u06FF]/.test(text);

                    // Skip SplitText char splitting on RTL pages (and for Arabic text).
                    // SplitText reverses char/word nodes under direction:rtl, which garbles
                    // Latin fallback strings that lack an Arabic translation.
                    if (isRTL || hasArabic) {
                        // No character splitting — animate the whole element
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

        // Refresh ScrollTrigger after a short delay
        setTimeout(() => {
            if (typeof ScrollTrigger !== 'undefined') {
                ScrollTrigger.refresh();
            }
        }, 300);

        // Initialize Marquee
        if (typeof $ !== 'undefined' && $('.marquee_mode-2').length && $.fn.marquee) {
            $('.marquee_mode-2').marquee({
                speed: 50,
                gap: 50,
                delayBeforeStart: 0,
                direction: isRTL ? 'right' : 'left',
                duplicated: true,
                pauseOnHover: true,
                startVisible: true
            });
        }

        // Initialize simple count-up animation for latest-counter__content numbers
        try {
            if (typeof window !== 'undefined') {
                const counters = document.querySelectorAll('.latest-counter__content .count');

                const animateCount = (el) => {
                    if (!el || el.dataset.countInitialized === '1') return;

                    const rawText = el.textContent || '';
                    const target = parseInt(rawText.replace(/[^\d]/g, ''), 10);
                    if (!target || isNaN(target)) return;

                    el.dataset.countInitialized = '1';

                    // Increase duration for a slower, smoother count-up
                    const duration = 8000; // ms
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
                    if ('IntersectionObserver' in window) {
                        const observer = new IntersectionObserver((entries, obs) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting) {
                                    animateCount(entry.target);
                                    obs.unobserve(entry.target);
                                }
                            });
                        }, {threshold: 0.3});

                        counters.forEach((el) => observer.observe(el));
                    } else {
                        counters.forEach((el) => animateCount(el));
                    }
                }
            }
        } catch (e) {
            // Fail silently if the browser doesn't support required APIs
        }
        };

        waitForTheme(() => typeof window.$ !== 'undefined' && typeof window.$.fn?.owlCarousel === 'function')
            .then((ready) => {
                if (ready) {
                    initHomeWidgets();
                }
            });
    });
});

</script>

<script>


import AppLayout from '@/Layouts/App.vue';
import UseCaseCard from '@/Components/UseCaseCard.vue';
import ProductCard from '@/Components/ProductCard.vue';

export default {
    components: {
        AppLayout,
        UseCaseCard,
        ProductCard,
    }

};
</script>

<style scoped>
.home-products {
    position: relative;
    display: block;
    padding: 140px 0 140px;
    overflow: hidden;
    z-index: 1;
}

.home-products__head {
    margin-bottom: 58px;
}

.home-products__head :deep(.section-title__title) {
    color: #fff;
}

.home-products__head :deep(.section-title__tagline) {
    color: rgba(255, 255, 255, 0.85);
}

.home-products__intro {
    max-width: 720px;
    margin: 18px auto 0;
    font-size: 1.05rem;
    line-height: 1.7;
    color: rgba(255, 255, 255, 0.78);
}

.home-products__grid {
    position: relative;
    z-index: 1;
}

.home-products :deep(.product-card) {
    margin-bottom: 30px;
}

.home-products__cta {
    position: relative;
    z-index: 1;
    margin-top: 40px;
}

.home-products .products-page__bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        145deg,
        #0b192c 0%,
        #0f2844 35%,
        #155a8a 65%,
        rgba(33, 137, 202, 0.55) 85%,
        rgba(127, 196, 87, 0.2) 100%
    );
    pointer-events: none;
}

.home-products .products-page__orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.45;
}

.home-products .products-page__orb--one {
    width: 420px;
    height: 420px;
    top: -120px;
    left: -80px;
    background: #2189ca;
}

.home-products .products-page__orb--two {
    width: 360px;
    height: 360px;
    top: 40%;
    right: -100px;
    background: #7fc457;
}

.home-products .products-page__orb--three {
    width: 280px;
    height: 280px;
    bottom: -60px;
    left: 35%;
    background: #1a5f8a;
}

.home-case-studies {
    position: relative;
    display: block;
    padding: 140px 0 140px;
    overflow: hidden;
    z-index: 1;
}

.home-case-studies .section-title {
    margin-bottom: 58px;
}

.home-case-studies__intro {
    max-width: 720px;
    margin: 18px auto 0;
    color: rgba(255, 255, 255, 0.78);
    line-height: 1.7;
}

.use-cases-page {
    position: relative;
    overflow: hidden;
}

.use-cases-page__bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        145deg,
        #0b192c 0%,
        #0f2844 35%,
        #155a8a 65%,
        rgba(33, 137, 202, 0.55) 85%,
        rgba(127, 196, 87, 0.2) 100%
    );
    pointer-events: none;
}

.use-cases-page__orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.45;
}

.use-cases-page__orb--one {
    width: 420px;
    height: 420px;
    top: -120px;
    left: -80px;
    background: #2189ca;
}

.use-cases-page__orb--two {
    width: 360px;
    height: 360px;
    top: 40%;
    right: -100px;
    background: #7fc457;
}

.use-cases-page__grid {
    position: relative;
    z-index: 1;
}

.home-case-studies :deep(.use-case-card) {
    margin-bottom: 30px;
}

.home-case-studies__cta {
    position: relative;
    z-index: 1;
    margin-top: 40px;
}
</style>

