<div class="grid-event-layout" data-ref="event-filter-content">
    @foreach ($events as $i => $event)
        <div class="h-100 mix arts concert workshops volunteer sports health_Wellness"
            data-ref="mixitup-target" data-aos="fade-up" data-aos-easing="linear" data-aos-duration="{{ 600 + $i * 100 }}">
            <div class="main-card h-100">
                <!-- <div class="event-badge">
                    <span>{{$event->start_datetime->format('d')}} <span class="d-block">{{$event->start_datetime->format('F')}}</span></span>
                </div> -->
                <div class="event-thumbnail">
                    <a href="javascript:void(0);" ajaxurl="{{route('front-add-widget-log',['widget_id'=>encrypt($widget->id),'type'=>2,'ref_id'=>$event->id])}}" url="{{ route('view-event-details', ['slug' => $event->slug,'widget_id'=>encrypt($widget->id)]) }}" class="thumbnail-img widget-event-click">
                        <img src="{{ $event->formattedBanner() }}" alt="">
                    </a>
                </div>
                <div class="event-content">
                    <div class="d-flex align-items-center justify-content-between">
                        <a href="javascript:void(0);" ajaxurl="{{route('front-add-widget-log',['widget_id'=>encrypt($widget->id),'type'=>2,'ref_id'=>$event->id])}}" url="{{ route('view-event-details', ['slug' => $event->slug,'widget_id'=>encrypt($widget->id)]) }}" title="{{$event->translated_event_name}}" class="event-title widget-event-click">
                            {{$event->translated_event_name}}
                        </a>
                        @if(strtotime($event->start_datetime) > strtotime(date('Y-m-d H:i:s')))
                        <span class="badge upcoming">{{ __('messages.status_upcoming') }}</span>
                        @elseif(strtotime($event->start_datetime) <= strtotime(date('Y-m-d H:i:s')) && strtotime($event->end_datetime) >= strtotime(date('Y-m-d H:i:s')))
                        <span class="badge ongoing">{{ __('messages.status_in_progress') }}</span>
                        @else
                        <span class="badge ended">{{ __('messages.status_ended') }}</span>
                        @endif
                    </div>
                    
                    @if(strtotime($event->start_datetime) > strtotime(date('Y-m-d H:i:s')))
                    <div class="publish-date">{{ __('messages.from_label') }} {{$event->eventStartDateTime }}</div>
                    @else
                    <div class="publish-date">{{ __('messages.until_label') }} {{$event->eventEndDateTime}}</div>
                    @endif
                    @php
                    $totalTicketCount = 0;
                    /* if($event->eventTickets->count()>0){
                        foreach($event->eventTickets as $eventTicket){
                            $totalTicketCount += $eventTicket->available_quantity;
                        }
                    } */
                    $totalBookedTicket = 0;
                    if(!empty($event->creator->stripe_email) && !empty($event->creator->stripe_account_id)){
                        if($event->eventActiveTickets->count()>0){
                            foreach($event->eventActiveTickets as $eventTicket){
                                $totalTicketCount += $eventTicket->available_quantity;
                                $totalBookedTicket += $eventTicket->approvedSold->count();
                            }
                        }
                    } else {
                        if($event->eventActiveFreeTickets->count()>0){
                            foreach($event->eventActiveFreeTickets as $eventTicket){
                                $totalTicketCount += $eventTicket->available_quantity;
                                $totalBookedTicket += $eventTicket->approvedSold->count();
                            }
                        }
                    }
                    //$totalBookedTicket = $event->tickets->where('rsvp_confirmation',1)->count();
                    @endphp
                    <div class=" mt-2 publish-date">{{$event->full_address}}</div>
                    <div class="publish-date">{{ __('messages.organizer_label') }}: {{$event->creator->name}}</div>
                    <div class="d-flex align-items-center justify-content-between">
                        @if($totalTicketCount>0)
                        <!-- <span class="fw-bold">{{$event->tickets->count()}} / {{$totalTicketCount}} Volunteers
                            @if($event->tickets->count()>0 && $totalTicketCount>0 && $event->tickets->count()>=$totalTicketCount)
                            <span class="badge badge-danger bg-danger ms-2">Sold Out</span>
                            @endif 
                        </span> -->
                            @if($totalBookedTicket>0 && $totalTicketCount>0 && $totalBookedTicket>=$totalTicketCount)
                            <span class="badge badge-danger bg-danger">{{ __('messages.sold_out') }}</span>
                            @else 
                            <span class="fw-bold">{{ __('messages.tickets_remaining_note', ['remaining' => $totalTicketCount - $totalBookedTicket, 'total' => $totalTicketCount]) }}</span>
                            @endif 
                        @else
                        <span class="fw-bold"></span>
                        @endif
                        <a href="javascript:void(0);" ajaxurl="{{route('front-add-widget-log',['widget_id'=>encrypt($widget->id),'type'=>2,'ref_id'=>$event->id])}}" url="{{ route('view-event-details', ['slug' => $event->slug,'widget_id'=>encrypt($widget->id)]) }}" class="primary-btn py-2 px-3 widget-event-click">
                            @if(!empty($event->ticket_sell_start_time) && $event->ticket_sell_start_time!='0000-00-00' && !empty($event->ticket_sell_end_time) && $event->ticket_sell_end_time!='0000-00-00')
                    
                                @if(strtotime($event->ticket_sell_start_time)<=strtotime(date('Y-m-d')) && strtotime($event->ticket_sell_end_time)>=strtotime(date('Y-m-d')))
                                <!-- Join -->{{ __('messages.details_btn') }}
                                @else 
                                <!-- View -->{{ __('messages.details_btn') }}
                                @endif 
                    
                            @else 
                            <!-- View -->{{ __('messages.details_btn') }}
                            @endif 
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
