<div class="breadcrumb-div">
    <a href="{{route('front-dashboard')}}" class="back-link fw-bold">{{ __('messages.dashboard') }}</a>
    @if(isset($breadcrumbs) && is_array($breadcrumbs))
        @foreach ($breadcrumbs as $breadcrumb)
            <span class="fw-bold px-2">/</span>
            <b><a href="{{$breadcrumb['url'] ? : '#'}}" class="back-link text-secondary fw-bold">{{$breadcrumb['name']}}</a></b>
        @endforeach
    @endif
</div>
