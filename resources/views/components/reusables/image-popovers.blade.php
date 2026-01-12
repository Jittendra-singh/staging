<div x-data="usePopper({
    offset: 12,
    placement: 'top',
    modifiers: [
        { name: 'preventOverflow', options: { padding: 10 } }
    ]
})" class="flex img-container" @mouseleave="isShowPopper = false"
    @mouseenter="isShowPopper = true">
    <div x-ref="popperRef" class="avatar h-10 w-10 hover:z-10">

        @if (isset($tag) && $tag == 'video')
            <video class="rounded-full ring ring-white dark:ring-navy-700" controls>
                <source src="{{ $image->path ?? '' }}" type="video/{{$image->extension ?? ''}}">
            </video>
        @else
            <img class="rounded-full ring ring-white dark:ring-navy-700" src="{{ $image->path ?? '' }}"
                alt="{{ $image->original_name ?? ''}}" />
        @endif

    </div>
    
        <div x-ref="popperRoot" class="popper-root" :class="isShowPopper && 'show'">
            <div class="popper-box">
                <div
                    class="w-48 rounded-md border border-slate-150 bg-white px-4 py-5 text-center dark:border-navy-600 dark:bg-navy-700">
                    <div class="avatar h-16 w-16">
                        @if (isset($tag) && $tag == 'video')
                            <video class="rounded-full ring ring-white dark:ring-navy-700" controls>
                                <source src="{{ $image->path ?? ''}}" type="video/{{$image->extension ?? ''}}">
                            </video>
                        @else
                            <img class="rounded-full" src="{{ $image->path ?? '' }}" alt="{{ $image->original_name ?? '' }}" />
                        @endif
                    </div>
                    <p
                        class="font-inter tracking-wide hover:text-error focus:text-error dark:hover:text-accent-light dark:focus:text-accent-light">
                        {{ $image->original_name ?? ''}}
                    </p>

                    @if(!isset($delete) || $delete)
                        <button type="button" data-url="{{ $image->path ?? '' }}" data-base="{{ $id ?? '' }}"
                            data-name="{{ $image->original_name  ?? ''}}" data-type="{{ $type ?? '' }}"
                            class="delete-file btn mt-4 h-8 rounded-full bg-error px-4 text-xs+ font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                            Delete
                        </button>
                    @endif
                </div>
                <div class="h-4 w-4" data-popper-arrow>
                    <svg viewBox="0 0 16 9" xmlns="http://www.w3.org/2000/svg" class="absolute h-4 w-4" fill="currentColor">
                        <path class="text-slate-150 dark:text-navy-600"
                            d="M1.5 8.357s-.48.624 2.754-4.779C5.583 1.35 6.796.01 8 0c1.204-.009 2.417 1.33 3.76 3.578 3.253 5.43 2.74 4.78 2.74 4.78h-13z" />
                        <path class="text-white dark:text-navy-700"
                            d="M0 9s1.796-.017 4.67-4.648C5.853 2.442 6.93 1.293 8 1.286c1.07-.008 2.147 1.14 3.343 3.066C14.233 9.006 15.999 9 15.999 9H0z" />
                    </svg>
                </div>
            </div>
        </div>
    
</div>
