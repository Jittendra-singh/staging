<?php

namespace App\Exports;

use App\Models\EventOrderTicket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegistrationExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $organising_events = auth()->guard('front')->user()->allControlEvents();
        $childrenSummary = \DB::table('event_order_ticket_childs')
            ->selectRaw("
                event_order_ticket_id,
                COUNT(*) as children_count,
                GROUP_CONCAT(
                    CONCAT(child_name, ' (Age ', child_age, ')')
                    ORDER BY id
                    SEPARATOR ', '
                ) as children_details
            ")
            ->groupBy('event_order_ticket_id');
        
        $registration_builder = EventOrderTicket::
                                    selectRaw(
                                        "events.event_name,
                                        CONCAT(volunteers.first_name, ' ' , volunteers.last_name) AS volunteer_first_name,
                                        volunteers.email_address AS volunteer_email_address,
                                        volunteers.phone,
                                        IF(event_order_tickets.waiver_filled_at IS NOT NULL, 'Signed', 'Unsigned') as waiver_status,
                                        IF(event_order_tickets.checkin = 1, 'Checked In', 'Not Checked In') as checkin_status,
                                        CONCAT(buyers.first_name, ' ' , buyers.last_name) AS buyer_first_name,
                                        buyers.email_address AS buyer_email_address,
                                        COALESCE(children.children_count, 0) as children_count,
                                        COALESCE(children.children_details, '') as children_details,
                                        event_tickets.ticket_name AS ticket_name,
                                        CONCAT('#',event_order_tickets.ticket_number) as ticket_number,
                                        event_order_tickets.created_at,
                                        event_order_tickets.ticket_status,
                                        events.ticket_questions,
                                        event_order_tickets.ticket_answer as attendee_ticket_answer" //event_order_tickets.checkin
                                    )
                                    ->join('events', 'event_order_tickets.event_id', '=', 'events.id')
                                    ->join('event_tickets', 'event_order_tickets.ticket_id', '=', 'event_tickets.id')
                                    ->join('volunteers', 'event_order_tickets.volunteer_id', '=', 'volunteers.id')
                                    ->join('event_orders', 'event_order_tickets.order_id', '=', 'event_orders.id')
                                    ->join('volunteers AS buyers', 'event_orders.creator_id', '=', 'buyers.id')
                                    ->leftJoinSub(
                                        $childrenSummary,
                                        'children',
                                        function ($join) {
                                            $join->on(
                                                'children.event_order_ticket_id',
                                                '=',
                                                'event_order_tickets.id'
                                            );
                                        }
                                    )
                                    ->where(function($query) use($organising_events){
                                        $query->whereIn('event_order_tickets.event_id', $organising_events->pluck('id'))
                                        ->getVolunteerRegistrationLists();
                                    });
        
        if (request()->filter === 'oldest') {
            $registrations = (clone $registration_builder)->get();
        } else {
            $registrations = (clone $registration_builder)->latest()->get();
        }

        return $registrations;
        
    }

    public function headings(): array
    {
        return [
            'Event Name', 
            'Attendee Name', 
            'Attendee Email',	
            'Attendee Phone', 
            'Waiver Status', 
            'Check-in Status',
            'Buyer\'s Name', 
            'Buyer\'s Email', 
            'Accompanying Children Count',
            'Accompanying Children Details',
            'Ticket Name', 
            'Registration id', 
            'Registration date', 
            'Registration status', 
            'Event Question',
            'Attendee Answer', 
            //'Checkin'
        ]; 
    }
}
