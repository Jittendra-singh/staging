@push('script')
        {{-- map & autocompletion --}}
        <script src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_API_KEY')}}&libraries=places&callback=initMap" async></script>
        <script>
            function initMap() {
                @if($locationarray)
                // Array of locations with latitude and longitude
                let locations = @json($locationarray);
                let centeredLatLong = null;
                if(locations && locations[0]){
                    centeredLatLong = locations[0];
                } else {
                    centeredLatLong = {lat:34.052235,lng:-118.243683};
                }
                const map = new google.maps.Map(document.getElementById('map'), {
                    //center: { lat: 28.5383, lng: -81.3792 }, // Centered on Florida
                    center: { lat: centeredLatLong.lat, lng: centeredLatLong.lng }, // Centered on Florida
                    zoom: 6
                });
                
                // Add red markers
                var infowindow = new google.maps.InfoWindow();
                var marker;
                let i = 0;
                locations.forEach(location => {
                    marker = new google.maps.Marker({
                        //position: { lat: location.lat, lng: location.lng },
                        position: new google.maps.LatLng(location.lat, location.lng),
                        map: map,
                        title: location.title,
                    });
                    // Attach content to marker click event using closure
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                        infowindow.setContent(location.title);
                        infowindow.open(map, marker);
                        }
                    })(marker, i));
                    i++;
                });
                @endif

                // Initialize autocomplete
                const autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById('autocomplete'),
                    { types: ['geocode',"establishment"], componentRestrictions: { country: 'us' } }
                );
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.geometry && place.geometry.location) {
                        const lat = place.geometry.location.lat();
                        const lng = place.geometry.location.lng();
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    } else {
                        document.getElementById('results').innerHTML = '<p>No valid place selected.</p>';
                    }
                });
                // End of AI
                /* function loadEvents(params = {}) {
                    fetch(`/events?${new URLSearchParams(params)}`)
                        .then(response => response.json())
                        .then(events => {
                            events.forEach(event => {
                                const marker = new google.maps.Marker({
                                    position: { lat: parseFloat(event.latitude), lng: parseFloat(event.longitude) },
                                    map: map,
                                    title: event.name
                                });
                                marker.addListener('click', () => {
                                    new google.maps.InfoWindow({
                                        content: `<b>${event.name}</b><br>Distance: ${event.distance.toFixed(2)} ${params.unit || 'km'}`
                                    }).open(map, marker);
                                });
                            });
                        });
                } */
            }
        </script>

    @endpush