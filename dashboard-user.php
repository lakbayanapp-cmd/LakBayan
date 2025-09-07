
<?php require_once 'includes/config.php'; ?>  

<?php

$id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0);
$progress = $db->select('progress', '*', ['user_id' => $id]);
?>
<!DOCTYPE html>
    <html lang="en">

        <?php require_once 'includes/head.php'; ?>  
    
 <head>
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
  z-index: 2000;
  padding: 16px;

  box-shadow: 0 -3px 10px rgba(0,0,0,0.3);
  border-radius: 16px 16px 0 0;
  font-family: "Segoe UI", sans-serif;
  transform: translateY(90%); /* Hidden by default */
  transition: transform 0.3s ease-in-out;
  max-height: 60vh; /* limit height */
  overflow-y: auto;
}

#info-panel.open {
  transform: translateY(0); /* Show panel */
}

#info-panel .toggle-btn {
  width: 50px;
  height: 6px;
  background: #fff;
  border-radius: 3px;
  margin: 8px auto;
  cursor: pointer;
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

    <!-- Other meta & css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="loading" data-layout-color="light" data-leftbar-theme="light" data-layout-mode="fluid" data-rightbar-onstart="true">
    <!-- Begin page -->
    <div class="wrapper">
        <?php require_once 'includes/sidebar.php'; ?>  
        <?php require_once 'includes/topbar.php'; ?>  
       <button id="nearby-btn" style="position:fixed; top:90px; left:50px; z-index:999; background:#DE28A6; color:#fff; border:none; border-radius:8px; padding:10px 18px; font-weight:bold; box-shadow:0 2px 8px rgba(0,0,0,0.15); cursor:pointer;">
            <i class="fa fa-location-arrow"></i> Nearby Terminals
        </button>
        <div class="content-page" style="margin:0; padding:0;">
            <div class="content" style="margin:0; padding:0;">
            <div class="container-fluid" style="margin:0; padding:0;">
            <div class="row" style="margin:0; padding:0;"> 
            <div class="col-12" style="margin:0; padding:0;"> 
                <div id="map" class="mt-5" style="height:100vh; width:100vw; position:fixed; top:0; left:0; z-index:1;"></div>
                <!-- Bottom panel for terminal/destination info -->
       <!-- Bottom panel for terminal/destination info -->
<div id="info-panel">
  <!-- Toggle Handle -->
  <div class="toggle-btn"></div>

  <div class="info-header">
    <img src="assets/images/LakBayan Logo Transparent .png" alt="Terminal Icon" class="info-logo">
    <h5 class="m-0">Trip Information</h5>
  </div>
  <script>
    document.querySelector("#info-panel .toggle-btn").addEventListener("click", function() {
    document.getElementById("info-panel").classList.toggle("open");
});

</script>
  
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
  <strong>Fare (₱11 base + ₱1/km, rounded up)</strong><br>
  ₱<span id="fare">-</span>
</div>
  <div class="info-card">
    <strong>ETA</strong>
    <span id="eta">-</span> mins
  </div>

</div>


  <button id="clear-btn" class="btn-clear">Clear</button>
</div>


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
var userCircle = null; // Declare userCircle

document.addEventListener("DOMContentLoaded", function() {
    var map = L.map('map').setView([13, 122], 6); // Default center (Philippines)

    // Nearby button
    document.getElementById('nearby-btn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                map.setView([lat, lng], 15);

                if (userCircle) {
                    map.removeLayer(userCircle);
                }
                userCircle = L.circle([lat, lng], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.2,
                    radius: 1000
                }).addTo(map);
            }, function() {
                Swal.fire('Error', 'Unable to get your location.', 'error');
            });
        } else {
            Swal.fire('Error', 'Geolocation not supported.', 'error');
        }
    });

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

    // Terminals
    var terminals = [
        {
            "name": "SIGNAL Terminal (SUBVTODA)",
            "lat": 14.508849,
            "lng": 121.065419,
            "image": "assets/images/signal.jpg"
        },
        {
            "name": "HAGONOY Terminal (BBEVTODA)",
            "lat": 14.530245230558448,
            "lng": 121.05693677750484,
            "image": "assets/images/hagonoy.jpg"
        },
        {
            "name": "LOWER BICUTAN Terminal (People's Market)",
            "lat": 14.487963563965842,
            "lng": 121.06062852616068,
            "image": "assets/images/lowerbicutan.jpg"
        },
        {
            "name": "USUSAN Terminal",
            "lat": 14.5323,
            "lng": 121.0695,
            "image": "assets/images/usuan.jpg"
        }
    ];

    var selectedTerminal = null;
    var selectedDestination = null;
    var destinationMarker = null;
    var routingControl = null;

    // Add terminal markers
    terminals.forEach(function(terminal) {
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
        map.once('click', async function(e) {
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

            // Reverse geocode
            try {
                const url = `reverse-geocode.php?lat=${selectedDestination.lat}&lon=${selectedDestination.lng}`;
                const response = await fetch(url);
                const data = await response.json();
                let address = '';
                if (data && data.address) {
                    address = [
                        data.address.suburb || data.address.village || data.address.hamlet || data.address.neighbourhood || '',
                        data.address.road || '',
                        data.address.city || data.address.town || data.address.municipality || '',
                        data.address.state || '',
                        data.address.country || ''
                    ].filter(Boolean).join(', ');
                }
                if (!address) {
                    address = data.display_name || '';
                }
                document.getElementById('selected-destination').textContent = address ? address : (selectedDestination.lat.toFixed(5) + ", " + selectedDestination.lng.toFixed(5));
            } catch (err) {
                document.getElementById('selected-destination').textContent = selectedDestination.lat.toFixed(5) + ", " + selectedDestination.lng.toFixed(5);
            }

            routeAndCalculate();
        });
    }

    // Routing and calculation
    function routeAndCalculate() {
        if (!selectedTerminal || !selectedDestination) return;
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
                profile: 'driving',
                alternatives: true
            })
        }).addTo(map);

        routingControl.on('routesfound', function(e) {
            if (window.altPolylines) {
                window.altPolylines.forEach(pl => map.removeLayer(pl));
            }
            window.altPolylines = [];
            window.activeRoute = null;

            e.routes.forEach((route, idx) => {
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
                    window.altPolylines.forEach(pl => pl.setStyle({ color: '#888', opacity: 0.7 }));
                    polyline.setStyle({ color: 'blue', opacity: 1 });
                    window.activeRoute = polyline;
                    updateInfo(route);
                });
            });
        });
    }

    // Update Info Panel (Fare, Distance, ETA)
    function updateInfo(route) {
        var distanceKm = route.summary.totalDistance / 1000;
        var roundedKm = Math.ceil(distanceKm);
        var fare = 11 + roundedKm; // Base 11 + rounded km
        var eta = Math.ceil(route.summary.totalTime / 60);

        document.getElementById('distance').textContent = roundedKm + " km";
        document.getElementById('fare').textContent = fare;
        document.getElementById('eta').textContent = eta + " min";
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

    // User Location Tracker
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            if (!userMarker) {
                userMarker = L.marker([lat, lng], {
                    icon: L.icon({
                        iconUrl: "https://cdn-icons-png.flaticon.com/512/64/64113.png",
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    })
                })
                .addTo(map)
                .bindTooltip("You are here", {
                    permanent: true,
                    direction: "top",
                    offset: [0, -10]
                })
                .openTooltip();
            } else {
                userMarker.setLatLng([lat, lng]);
            }

            if (firstUpdate) {
                map.setView([lat, lng], 15);
                firstUpdate = false;
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