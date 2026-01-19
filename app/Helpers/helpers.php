<?php 
if (!function_exists('getiCalData')) {
    function formatAmt($amount){
        return number_format($amount, 2);
    }
    function formatPhoneNumber($phone) {
        // Strip everything to digits
        $digits = preg_replace('/\D+/', '', $phone);

        // Determine the 10-digit national number
        if (strlen($digits) === 11 && $digits[0] === '1') {
            $national = substr($digits, 1);
        } elseif (strlen($digits) === 10) {
            $national = $digits;
        } elseif (preg_match('/^1?(\d{10})$/', $digits, $m)) { // fallback (e.g., 0013055550789)
            $national = $m[1];
        } else {
            // Not enough digits to format; return original
            return $phone;
        }

        // Force XXX-XXX-XXXX
        $area = substr($national, 0, 3);
        $exchange = substr($national, 3, 3);
        $line = substr($national, 6, 4);

        return "+1 {$area}-{$exchange}-{$line}";
    }
    function getiCalData($event) {
        if(!isset($event->id)){
            return '';
        }
        $tzMap = [
            'eastern' => 'America/New_York',
            'central' => 'America/Chicago',
            'mountain' => 'America/Denver',
            'pacific' => 'America/Los_Angeles',
        ];
        $tzid = isset($tzMap[$event->event_timezone]) ? $tzMap[$event->event_timezone] : 'UTC';

        $sDate = $event->start_datetime->format('Ymd\THis');
        $eDate = $event->end_datetime->format('Ymd\THis');
        $add = $event->venue.' '.$event->pincode;
        $url = route('view-event-details',$event->slug);
        $orgName = $event->creator->name;
        $orgEmail = $event->creator->email_address;
        $meetingSpot = !empty($event->meeting_spot_details)?"Meeting Spot: ".strip_tags($event->meeting_spot_details):"";
        $ParkingDetail = !empty($event->parking_info_details)?"Parking Info: ".strip_tags($event->parking_info_details):"";
        $MoreInfo = !empty($url)?"More Info: {$url}":"";
        $whatBring = "";
        if($event->bringTools->count()){
            $whatBringTool = "";
            foreach($event->bringTools as $tool){
                $whatBringTool .= !empty($whatBringTool)?", ".$tool->name:$tool->name;
            }
            $whatBring = "What should volunteers bring?: ".strip_tags($whatBringTool);
        }
        $whatProvided = "";
        if($event->providedTools->count()){
            $whatProvidedTool = "";
            foreach($event->providedTools as $tool){
                $whatProvidedTool .= !empty($whatProvidedTool)?", ".$tool->name:$tool->name;
            }
            $whatProvided = "What will be provided?: ".strip_tags($whatProvidedTool);
        }
        $description = implode(" \\n\\n", array_filter([
            $meetingSpot,
            $ParkingDetail,
            $whatBring,
            $whatProvided,
            $MoreInfo
        ]));
        $vtz = getVTimezoneBlock($tzid);
        // URL:{$url}
        $dataStr = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Community Events//EN
{$vtz}
BEGIN:VEVENT
SUMMARY:{$event->event_name}
DTSTART;TZID={$tzid}:{$sDate}
DTEND;TZID={$tzid}:{$eDate}
LOCATION:{$add}
ORGANIZER:CN={$orgName};EMAIL:{$orgEmail}:MAILTO:{$orgEmail}
DESCRIPTION:{$description}
END:VEVENT
END:VCALENDAR
";
        return $dataStr;
    }
    function getVTimezoneBlock($tzid) {
        // Precomputed VTIMEZONE bodies (typical structure: STANDARD and DAYLIGHT components).
        // These blocks work for many clients (Outlook/Apple/Google). They are static templates.
        $blocks = [
        'America/New_York' => <<<VTZ
BEGIN:VTIMEZONE
TZID:America/New_York
X-LIC-LOCATION:America/New_York
BEGIN:DAYLIGHT
TZOFFSETFROM:-0500
TZOFFSETTO:-0400
TZNAME:EDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0400
TZOFFSETTO:-0500
TZNAME:EST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
VTZ
        ,
        'America/Chicago' => <<<VTZ
BEGIN:VTIMEZONE
TZID:America/Chicago
X-LIC-LOCATION:America/Chicago
BEGIN:DAYLIGHT
TZOFFSETFROM:-0600
TZOFFSETTO:-0500
TZNAME:CDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0500
TZOFFSETTO:-0600
TZNAME:CST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
VTZ
        ,
        'America/Denver' => <<<VTZ
BEGIN:VTIMEZONE
TZID:America/Denver
X-LIC-LOCATION:America/Denver
BEGIN:DAYLIGHT
TZOFFSETFROM:-0700
TZOFFSETTO:-0600
TZNAME:MDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0600
TZOFFSETTO:-0700
TZNAME:MST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
VTZ
        ,
        'America/Los_Angeles' => <<<VTZ
BEGIN:VTIMEZONE
TZID:America/Los_Angeles
X-LIC-LOCATION:America/Los_Angeles
BEGIN:DAYLIGHT
TZOFFSETFROM:-0800
TZOFFSETTO:-0700
TZNAME:PDT
DTSTART:19700308T020000
RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=2SU
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:-0700
TZOFFSETTO:-0800
TZNAME:PST
DTSTART:19701101T020000
RRULE:FREQ=YEARLY;BYMONTH=11;BYDAY=1SU
END:STANDARD
END:VTIMEZONE
VTZ
        ,
    ];

        return isset($blocks[$tzid]) ? $blocks[$tzid] : "BEGIN:VTIMEZONE\r\nTZID:{$tzid}\r\nEND:VTIMEZONE";
    }
}