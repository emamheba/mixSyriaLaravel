<!-- Hero Area S t a r t -->
<div class="sliderArea heroAboutStyle plr"  data-padding-top="{{$padding_top}}" data-padding-bottom="{{$padding_bottom}}" style="background-color: {{$section_bg}}">
    <div class="slider-active">
        <div class="single-slider d-flex align-items-center">
            <div class="container-fluid ">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-5 col-md-12">
                        <div class="heroCaption mb-50">
                            <h1 class="tittle" data-animation="fadeInUp" data-delay="0.0s">
                                {{ $title }}
                            </h1>
                            <p class="pera" data-animation="fadeInUp" data-delay="0.3s">
                                {{ $subtitle }}
                            </p>
                            <div class="btn-wrapper">
                                <a href="{{$button_link_one}}" class="new-cmn-btn signup-btn mr-15 mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_title_one }}</a>
                                <a href="{{$button_link_two}}" class="new-cmn-btn browse-ads mb-10 wow fadeInLeft" data-wow-delay="0.3s">{{ $button_title_two }}<i class="las la-angle-right icon"></i></a>
                            </div>
                        </div>
                        <!--? Count Down S t a r t -->
                        <div class="countDown">
                            <div class="row">
                                @foreach ($repeater_data['counting_number_'] as $key => $counting_number)
                                    <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                                        <div class="single mb-24 wow fadeInLeft" data-wow-delay="0.2s">
                                            <div class="single-counter">
                                                <span class="counter odometer" data-count="{{ $counting_number }}"></span>
                                                <p class="icon">{{ $repeater_data['counting_symbol_'][$key] }}</p>
                                            </div>
                                            <div class="pera-count">
                                                <p class="pera">{{ $repeater_data['counting_title_'][$key] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Count Down E n d -->
                    </div>
                    <div class="col-xxl-6 col-xl-5 col-lg-7 ">
                        <div class="hero-man d-none d-lg-block f-right" >
                            {!! $background_image !!}
                        </div>
                    </div>
                </div>
                <!-- Search Box -->
            </div>
        </div>
    </div>
</div>
<!-- End-of Hero  -->
