<section class="aboutArea" data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color:{{$section_bg}}">
    <div class="container-1440">
        <div class="aboutAreaWraper" {!! $section_bg_image !!}>
            <div class="row justify-content-between flex-lg-row flex-column-reverse gap-lg-0 gap-4">
                <div class="col-lg-6">
                    <div class="about-caption">
                        <!-- Section Tittle -->
                        <div class="section-tittle section-tittle2 mb-80">
                            <h2 class="head2 wow fadeInUp" data-wow-delay="0.1s">{{ $title }}</h2>
                            <p  class="wow fadeInUp mt-3" data-wow-delay="0.2s">{{ $subtitle }}</p>
                        </div>
                        <div class="btn-wrapper">
                            <a href="{{$button_one_link}}" class="cmn-btn2 mr-15 mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_one_title }}</a>
                            <a href="{{$button_two_link}}" class="cmn-btn2 transparent-btn mb-10 wow fadeInRight" data-wow-delay="0.3s">{{ $button_two_title }}</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- about-img -->
                    <div class="aboutImg tilt-effect wow fadeInRight ps-lg-5" data-wow-delay="0.1s">
                       {!! $banner_image_one !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
