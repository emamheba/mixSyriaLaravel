@extends('layouts/layoutMaster')

@section('title', 'Listing Details')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/swiper/swiper.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/swiper/swiper.js'
])
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        // Initialize gallery swiper
        const swiperGallery = new Swiper('.gallery-swiper', {
            slidesPerView: 1,
            spaceBetween: 10,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    });
</script>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">{{ __('Listing Details') }}</h4>
    <a href="{{ route('admin.user.all.listings') }}" class="btn btn-primary">
        <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Listings') }}
    </a>
</div>

<div class="row">
  <!-- Listing Media Gallery -->
<div class="col-xl-6 col-lg-7 col-md-12">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">{{ __('Media Gallery') }}</h5>
        </div>
        <div class="card-body">
            <div class="swiper gallery-swiper">
                <div class="swiper-wrapper">
                    @php
                        // Fix: Access the image_url from the array returned by the accessor
                        $mainImage = $listing->image['image_url'] ?? asset('storage/uploads/no-image.png');
                    @endphp
                    <div class="swiper-slide">
                        <img src="{{ $mainImage }}" class="img-fluid rounded" alt="{{ $listing->title }}">
                    </div>
                    
                    @if($listing->gallery_images)
                        @php 
                            $gallery = json_decode($listing->gallery_images) ?? []; 
                        @endphp
                        @foreach($gallery as $image)
                            <div class="swiper-slide">
                                <img src="{{ asset('storage/listings/'.$image) }}"
                                     class="img-fluid rounded" alt="{{ $listing->title }}">
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
            
            @if($listing->video_url)
            <div class="mt-4">
                <h6>{{ __('Video') }}</h6>
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $listing->video_url }}" allowfullscreen></iframe>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

    <!-- Listing Details -->
    <div class="col-xl-6 col-lg-5 col-md-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Basic Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Title') }}:</div>
                    <div class="col-md-8">{{ $listing->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Price') }}:</div>
                    <div class="col-md-8">
                        {{ number_format($listing->price, 2) }}
                        @if($listing->negotiable)
                            <span class="badge bg-info ms-1">{{ __('Negotiable') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Category') }}:</div>
                    <div class="col-md-8">
                        {{ $listing->category->name ?? 'N/A' }}
                        @if($listing->subcategory)
                            > {{ $listing->subcategory->name }}
                        @endif
                        @if($listing->childcategory)
                            > {{ $listing->childcategory->name }}
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Brand') }}:</div>
                    <div class="col-md-8">{{ $listing->brand->name ?? 'N/A' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Condition') }}:</div>
                    <div class="col-md-8">{{ $listing->condition }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Status') }}:</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $listing->status == 1 ? 'success' : 'warning' }}">
                            {{ $listing->status == 1 ? __('Approved') : __('Pending') }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Published') }}:</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $listing->is_published == 1 ? 'primary' : 'secondary' }}">
                            {{ $listing->is_published == 1 ? __('Published') : __('Unpublished') }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Featured') }}:</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $listing->is_featured == 1 ? 'info' : 'secondary' }}">
                            {{ $listing->is_featured == 1 ? __('Yes') : __('No') }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Views') }}:</div>
                    <div class="col-md-8">{{ $listing->view }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Published At') }}:</div>
                    <div class="col-md-8">
                        {{ $listing->published_at ? date('d M Y H:i', strtotime($listing->published_at)) : 'Not published yet' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Created At') }}:</div>
                    <div class="col-md-8">{{ date('d M Y H:i', strtotime($listing->created_at)) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Contact Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Contact Name') }}:</div>
                    <div class="col-md-8">{{ $listing->contact_name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Email') }}:</div>
                    <div class="col-md-8">{{ $listing->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">{{ __('Phone') }}:</div>
                    <div class="col-md-8">
                        {{ $listing->phone }}
                        @if($listing->phone_hidden)
                            <span class="badge bg-warning ms-1">{{ __('Hidden') }}</span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 fw-bold">{{ __('Address') }}:</div>
                    <div class="col-md-8">
                        {{ $listing->address }}
                        <div class="text-muted">
                            @if($listing->city)
                                {{ $listing->city->name }},
                            @endif
                            @if($listing->state)
                                {{ $listing->state->name }},
                            @endif
                            @if($listing->country)
                                {{ $listing->country->name }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Information -->
        @if($listing->user)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('User Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-md me-2">
                        <img src="{{ asset('storage/uploads/profile/'.($listing->user->image ?? 'no-image.jpg')) }}" alt="{{ $listing->user->name }}">
                    </div>
                    <div>
                        <h6 class="mb-0">{{ $listing->user->name }}</h6>
                        <span class="text-muted">{{ $listing->user->email }}</span>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 fw-bold">{{ __('Phone') }}:</div>
                    <div class="col-md-8">{{ $listing->user->phone ?? 'N/A' }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 fw-bold">{{ __('Member Since') }}:</div>
                    <div class="col-md-8">{{ date('d M Y', strtotime($listing->user->created_at)) }}</div>
                </div>
            </div>
        </div>
        @elseif($listing->guestListing)
        <!-- Guest Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Guest Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4 fw-bold">{{ __('Name') }}:</div>
                    <div class="col-md-8">{{ $listing->guestListing->name }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4 fw-bold">{{ __('Email') }}:</div>
                    <div class="col-md-8">{{ $listing->guestListing->email }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 fw-bold">{{ __('Phone') }}:</div>
                    <div class="col-md-8">{{ $listing->guestListing->phone ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Listing Description -->
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Description') }}</h5>
            </div>
            <div class="card-body">
                {!! $listing->description !!}
            </div>
        </div>
    </div>
</div>

<!-- Listing Attributes -->
@if($listing->listing_attributes && count($listing->listing_attributes) > 0)
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Attributes') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($listing->listing_attributes as $attribute)
                    <div class="col-md-4 mb-3">
                        <div class="fw-bold">{{ $attribute->attribute_name }}:</div>
                        <div>{{ $attribute->attribute_value }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Tags -->
@if($listing->tags && count($listing->tags) > 0)
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">{{ __('Tags') }}</h5>
            </div>
            <div class="card-body">
                @foreach($listing->tags as $tag)
                <span class="badge bg-primary me-1 mb-1">{{ $tag->name }}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <form action="{{ route('admin.listings.status.change', $listing->id) }}" method="POST" class="d-inline-block me-2">
                            @csrf
                            <button type="submit" class="btn {{ $listing->status == 1 ? 'btn-outline-success' : 'btn-success' }}">
                                {{ $listing->status == 1 ? __('Mark as Pending') : __('Approve Listing') }}
                            </button>
                        </form>
                        <form action="{{ route('admin.listings.published.status.change', $listing->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            <button type="submit" class="btn {{ $listing->is_published == 1 ? 'btn-outline-primary' : 'btn-primary' }}">
                                {{ $listing->is_published == 1 ? __('Unpublish') : __('Publish') }}
                            </button>
                        </form>
                    </div>
                    <form id="delete-form" action="{{ route('admin.listings.delete', $listing->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('{{ __('Are you sure you want to delete this listing?') }}')">
                            <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection