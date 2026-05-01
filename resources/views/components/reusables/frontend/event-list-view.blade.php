<div class="events-list">
    <!-- Event 4 -->
    @forelse($events as $event)
    @if(!empty($event->slug))
    <div class="event-card">
        <!-- <div class="event-date">
            <span class="day">{{$event->start_datetime->format('d')}}</span>
            <span class="month">{{$event->start_datetime->format('F')}}</span>
        </div> -->

        <div class="event-thumbnail">
            <a href="{{ route('view-event-details', ['slug' => $event->slug]) }}" class="thumbnail-img">
                <img src="{{ $event->formattedBanner() }}" alt="">
            </a>
        </div>

        <div class="event-details">
            <h3 class="event-title">{{$event->event_name}}</h3>
            <div>
                <div class="event-time">
                    <span class="time-slot"> <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" />
                            <path d="M16 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M8 2V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        {{$event->startTimeAndEndTime}}</span>
                </div>
                <div class="event-location">
                    <span class="location"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C8.13401 2 5 5.13401 5 9C5 13.25 12 22 12 22C12 22 19 13.25 19 9C19 5.13401 15.866 2 12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <circle cx="12" cy="9" r="2.5" fill="currentColor" />
                        </svg>
                        {{$event->fullAddress}}</span>
                </div>
            </div>
            <div class="d-flex justify-content-start gap-3">
                <a class="text-white" href="{{route('view-event-details',['slug'=> $event->slug])}}">
                    <div class="event-logo">
                        <div class="mini-badge">View Event</div>
                    </div>
                </a>
                @if(strtotime($event->end_datetime)<=strtotime(date('Y-m-d H:i:s')))
                <a target="_blank" class="text-white" href="{{route('cleanup-highlights',['event'=> encrypt($event->id)])}}">
                    <div class="event-logo">
                        <div class="mini-badge">Impact Report</div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
    @empty
    <div class="event-card text-center d-flex   ">
        <h3 class="event-title w-100">No Event Found</h3>
    </div>
    @endforelse
</div>