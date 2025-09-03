
<?php require_once 'includes/config.php'; ?>  

<?php

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0);
$progress = $db->select('progress', '*', ['user_id' => $id]);
?>
<!DOCTYPE html>
    <html lang="en">

        <?php require_once 'includes/head.php'; ?>  
    
 <head>
    <!-- Other meta & css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="loading" data-layout-color="light" data-leftbar-theme="light" data-layout-mode="fluid" data-rightbar-onstart="true">
    <!-- Begin page -->
    <div class="wrapper">
        <?php require_once 'includes/sidebar.php'; ?>  
        <?php require_once 'includes/topbar.php'; ?>  

        <div class="content-page" style="margin:0; padding:0;">
            <div class="content" style="margin:0; padding:0;">
            <div class="container-fluid" style="margin:0; padding:0;">
            <div class="row" style="margin:0; padding:0;"> 
            <div class="col-12" style="margin:0; padding:0;"> 
                <div id="map" class="mt-5" style="height:100vh; width:100vw; position:fixed; top:0; left:0; z-index:1;"></div>
                <!-- Bottom panel for terminal/destination info -->
       <!-- Bottom panel for terminal/destination info -->
<div id="info-panel">
  <div class="info-header">
    <img src="assets/images/LakBayan Logo Transparent .png" alt="Terminal Icon" class="info-logo">
    <h5 class="m-0">Trip Information</h5>
  </div>
  
  <div class="info-fields">
  <div class="info-card">
    <strong>Terminal</strong>
    <img id="terminal-image" src="assets/images/LakBayan Logo Transparent .png" alt="Terminal" style="max-width:60px; display:block; margin:5px auto;">
    <span id="selected-terminal">None</span>
  </div>
  <div class="info-card">
    <strong>Destination</strong>
    <span id="selected-destination">None</span>
  </div>
  <div class="info-card">
    <strong>Distance</strong>
    <span id="distance">-</span> km
  </div>
  <div class="info-card">
    <strong>Fare (P50/km)</strong>
    ₱<span id="fare">-</span>
  </div>
  <div class="info-card">
    <strong>ETA</strong>
    <span id="eta">-</span> mins
  </div>
  <div class="info-card">
    <strong>Speed</strong>
    <span id="speed">0</span> KPH
  </div>
</div>


  <button id="clear-btn" class="btn-clear">Clear</button>
</div>

<style>

.topbar, 
.navbar-custom {
    z-index: 200001 !important;
    position: relative; /* ensure stacking context */
}

.profile-dropdown,
.profile-dropdown .dropdown-menu {
    z-index: 200000 !important; /* way higher than Leaflet */
    position: absolute !important;
}

#info-panel {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100vw;
  background: #DE28A6;
  color: #fff;
  z-index: 1;
  box-shadow: 0 -3px 10px rgba(0,0,0,0.3);
  padding: 16px;
  border-radius: 16px 16px 0 0;
  font-family: "Segoe UI", sans-serif;
}

#info-panel .info-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

#info-panel .info-logo {
  width: 32px;
  height: 32px;
}

#info-panel h5 {
  font-size: 18px;
  font-weight: 600;
}

#info-panel .info-fields {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 8px;
  margin-bottom: 12px;
}

#info-panel .info-card {
  background: #9C2377;
  padding: 8px 12px;
  border-radius: 8px;
  text-align: center;
  font-size: 14px;
}

#info-panel .info-card strong {
  display: block;
  font-size: 12px;
  color: #f8dfff;
}

#info-panel .info-card span {
  font-size: 15px;
  font-weight: bold;
  color: #fff;
}

#info-panel .btn-clear {
  display: block;
  width: 100%;
  background: white;
  color: #DE28A6;
  font-weight: bold;
  border: none;
  padding: 10px;
  border-radius: 10px;
  transition: 0.2s ease;
  cursor: pointer;
}

#info-panel .btn-clear:hover {
  background: #f7f7f7;
}
</style>

            </div>
            </div>
            </div>
            <!-- Leaflet CSS & JS -->
            <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
            <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
            <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                var userMarker = null;
var firstUpdate = true;
            document.addEventListener("DOMContentLoaded", function() {
                var map = L.map('map').setView([13, 122], 6); // Default center (Philippines)

                // Base layers
                var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    maxZoom: 19,
                    attribution: '© Esri'
                });

                var baseMaps = {
                    "OpenStreetMap": osm,
                    "Satellite": satellite
                };
                L.control.layers(baseMaps).addTo(map);

                // Tricycle icon
                var tricycleIcon = L.icon({
                    iconUrl: 'assets/images/TRICYCLE ICON .png',
                    iconSize: [32, 32],
                    iconAnchor: [16, 32],
                    popupAnchor: [0, -32]
                });

                // Example JSON coordinates for terminals
             var terminals = [
    {
        "name": "Terminal 1",
        "lat": 14.5995,
        "lng": 120.9842,
        "image": "assets/images/terminal1.png"
    },
    {
        "name": "Terminal 2",
        "lat": 13.6218,
        "lng": 123.1947,
        "image": "assets/images/terminal2.png"
    },
    {
        "name": "Terminal 3",
        "lat": 10.3157,
        "lng": 123.8854,
        "image": "assets/images/terminal3.png"
    }
];


                var selectedTerminal = null;
                var selectedDestination = null;
                var destinationMarker = null;
                var routingControl = null;

                // Add terminal markers
                terminals.forEach(function(terminal, idx) {
                    var marker = L.marker([terminal.lat, terminal.lng], {icon: tricycleIcon})
                        .addTo(map)
                        .on('click', function() {
                            Swal.fire({
                                title: terminal.name,
                                html: '<img src="' + terminal.image + '" alt="Terminal Image" style="max-width:300px;display:block;margin:10px auto;">',
                                text: "Do you want to select this terminal?",
                                showCancelButton: true,
                                confirmButtonText: 'Select',
                                cancelButtonText: 'Cancel',
                                icon: 'info'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    selectedTerminal = terminal;
                                    document.getElementById('selected-terminal').textContent = terminal.name;
                                    document.getElementById('terminal-image').src = terminal.image;
                                    // Prompt user to click on map for destination
                                    Swal.fire({
                                        title: 'Select Destination',
                                        text: 'Click anywhere on the map to set your destination.',
                                        icon: 'info'
                                    });
                                    enableDestinationSelection();
                                }
                            });
                        });
                });

                function enableDestinationSelection() {
                    map.once('click', function(e) {
                        if (destinationMarker) {
                            map.removeLayer(destinationMarker);
                        }
                        selectedDestination = {
                            lat: e.latlng.lat,
                            lng: e.latlng.lng
                        };
                        destinationMarker = L.marker([selectedDestination.lat, selectedDestination.lng])
                            .addTo(map)
                            .bindPopup("Destination")
                            .openPopup();
                        document.getElementById('selected-destination').textContent = 
                            selectedDestination.lat.toFixed(5) + ", " + selectedDestination.lng.toFixed(5);
                        routeAndCalculate();
                    });
                }

                // Routing and calculation
                function routeAndCalculate() {
                    if (!selectedTerminal || !selectedDestination) return;
                    // Remove previous route
                    if (routingControl) {
                        map.removeControl(routingControl);
                    }
               routingControl = L.Routing.control({
    waypoints: [
        L.latLng(selectedTerminal.lat, selectedTerminal.lng),
        L.latLng(selectedDestination.lat, selectedDestination.lng)
    ],
    routeWhileDragging: false,
    show: false,
    addWaypoints: false,
    draggableWaypoints: false,
    fitSelectedRoutes: true,
    lineOptions: { styles: [{ color: 'blue', weight: 5 }] },
    router: new L.Routing.OSRMv1({
        serviceUrl: 'https://router.project-osrm.org/route/v1',
        profile: 'driving',     // pwede rin 'cycling' or 'walking'
        alternatives: true      // 👉 enables multiple routes
    })
}).addTo(map);

 routingControl.on('routesfound', function(e) {
    // Remove old polylines
    if (window.altPolylines) {
        window.altPolylines.forEach(pl => map.removeLayer(pl));
    }
    window.altPolylines = [];
    window.activeRoute = null;

    e.routes.forEach((route, idx) => {
        // First route = active (blue), others = gray
        var color = idx === 0 ? 'blue' : '#888';
        var polyline = L.polyline(route.coordinates, {
            color: color,
            weight: 5,
            opacity: idx === 0 ? 1 : 0.7
        }).addTo(map);

        window.altPolylines.push(polyline);

        if (idx === 0) {
            window.activeRoute = polyline;
            updateInfo(route);
        }

        polyline.on('click', function() {
            // reset lahat ng polylines → gray
            window.altPolylines.forEach(pl => pl.setStyle({ color: '#888', opacity: 0.7 }));

            // highlight yung napili → blue
            polyline.setStyle({ color: 'blue', opacity: 1 });
            window.activeRoute = polyline;

            // update info panel
            updateInfo(route);
        });
    });
});

// Function para update info panel
function updateInfo(route) {
    var distanceKm = (route.summary.totalDistance / 1000).toFixed(2);
    var fare = Math.ceil(distanceKm * 50);
    var eta = Math.ceil(route.summary.totalTime / 60);

    document.getElementById('distance').textContent = distanceKm;
    document.getElementById('fare').textContent = fare;
    document.getElementById('eta').textContent = eta;
}

                }

                // Clear button
     document.getElementById('clear-btn').addEventListener('click', function() {
    selectedTerminal = null;
    selectedDestination = null;
    document.getElementById('selected-terminal').textContent = 'None';
    document.getElementById('selected-destination').textContent = 'None';
    document.getElementById('distance').textContent = '-';
    document.getElementById('fare').textContent = '-';
    document.getElementById('eta').textContent = '-';

    if (routingControl) {
        map.removeControl(routingControl);
        routingControl = null;
    }

    if (window.altPolylines) {
        window.altPolylines.forEach(pl => map.removeLayer(pl));
        window.altPolylines = [];
    }
    window.activeRoute = null;

    if (destinationMarker) {
        map.removeLayer(destinationMarker);
        destinationMarker = null;
    }
});
 
var userMarker = null;
var firstUpdate = true;

if (navigator.geolocation) {
    navigator.geolocation.watchPosition(function(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        var speed = position.coords.speed ? (position.coords.speed * 3.6).toFixed(1) : 0; // m/s → km/h

        // Kung wala pa marker, create once
     if (!userMarker) {
    userMarker = L.marker([lat, lng], {
        icon: L.icon({
            iconUrl: "https://cdn-icons-png.flaticon.com/512/64/64113.png", // blue dot icon
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        })
    })
    .addTo(map)
    .bindTooltip("You are here", {
        permanent: true,   // always show
        direction: "top",  // position above marker
        offset: [0, -10]   // small offset upward
    })
    .openTooltip();
} else {
    userMarker.setLatLng([lat, lng]);
}


        // Sa first update lang mag setView
        if (firstUpdate) {
            map.setView([lat, lng], 15);
            firstUpdate = false;
        }

        // Update Speed in panel
        document.getElementById('speed').textContent = speed;

        // Kung may destination at terminal → recalc ETA & Distance
        if (selectedTerminal && selectedDestination) {
            var userLatLng = L.latLng(lat, lng);
            var destLatLng = L.latLng(selectedDestination.lat, selectedDestination.lng);
            var distanceMeters = userLatLng.distanceTo(destLatLng);
            var distanceKm = (distanceMeters / 1000).toFixed(2);

            document.getElementById('distance').textContent = distanceKm;
            document.getElementById('fare').textContent = Math.ceil(distanceKm * 50);

            // ETA (kung may speed)
            if (speed > 0) {
                var etaMinutes = Math.ceil((distanceKm / speed) * 60);
                document.getElementById('eta').textContent = etaMinutes;
            } else {
                document.getElementById('eta').textContent = "-";
            }
        }
    });
}

            });
            </script>
            </div>
        </div>
        <!-- End Page content -->
    </div>

    <!-- bundle -->
    <script src="assets/js/vendor.min.js"></script>
    <script src="assets/js/app.min.js"></script>

    <!-- third party js -->
    <script src="assets/js/vendor/apexcharts.min.js"></script>
    <script src="assets/js/vendor/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="assets/js/vendor/jquery-jvectormap-world-mill-en.js"></script>
    <!-- third party js ends -->

    <!-- end demo js-->
</body>


<!-- /hyper/saas/index.html [XR&CO'2014], Fri, 29 Jul 2022 10:20:07 GMT -->
</html>