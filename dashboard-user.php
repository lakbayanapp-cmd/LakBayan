
<?php require_once 'includes/config.php'; ?>  

<?php


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
/* drag handle bar */
#info-panel .drag-handle {
  width: 60px;
  height: 6px;
  background: #fff;
  border-radius: 3px;
  margin: 8px auto;
  cursor: grab;
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
  transform: translateY(90%); /* default hidden */
  transition: transform 0.2s ease-out;
  max-height: 60vh;
  overflow-y: auto;
  touch-action: none; /* importante para sa mobile drag */
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
        <button id="nearby-btn" style="position:fixed; top:90px; left:50px; z-index:3; background:#DE28A6; color:#fff; border:none; border-radius:8px; padding:10px 18px; font-weight:bold; box-shadow:0 2px 8px rgba(0,0,0,0.15); cursor:pointer;">
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
  <div class="drag-handle"></div>
  <div class="info-header">
    <img src="assets/images/LakBayan Logo Transparent .png" alt="Terminal Icon" class="info-logo">
    <h5 class="m-0">Trip Information</h5>
  </div>
    <script>
const panel = document.getElementById("info-panel");
const handle = panel.querySelector(".drag-handle");

let startY = 0;
let currentY = 0;
let startTranslateY = 0; // store panel position before drag
let isDragging = false;

function getCurrentTranslateY() {
  let matrix = new WebKitCSSMatrix(window.getComputedStyle(panel).transform);
  return matrix.m42; // Y translation value
}

handle.addEventListener("mousedown", startDrag);
handle.addEventListener("touchstart", startDrag, { passive: true });

function startDrag(e) {
  isDragging = true;
  startY = e.touches ? e.touches[0].clientY : e.clientY;
  startTranslateY = getCurrentTranslateY(); // baseline
  panel.style.transition = "none";

  document.addEventListener("mousemove", drag);
  document.addEventListener("mouseup", endDrag);
  document.addEventListener("touchmove", drag, { passive: false });
  document.addEventListener("touchend", endDrag);
}

function drag(e) {
  if (!isDragging) return;
  currentY = e.touches ? e.touches[0].clientY : e.clientY;
  let diffY = currentY - startY;

  // combine start position + diff
  let newTranslateY = startTranslateY + diffY;

  // clamp (0 = open, 90% = closed)
  const maxTranslate = window.innerHeight * 0.9;
  if (newTranslateY < 0) newTranslateY = 0;
  if (newTranslateY > maxTranslate) newTranslateY = maxTranslate;

  panel.style.transform = `translateY(${newTranslateY}px)`;
  e.preventDefault(); // stop scroll habang drag
}

function endDrag() {
  isDragging = false;
  panel.style.transition = "transform 0.25s ease-out";

  let currentTranslateY = getCurrentTranslateY();
  const midpoint = window.innerHeight * 0.45; // decide open/close threshold

  if (currentTranslateY < midpoint) {
    // open
    panel.style.transform = "translateY(0)";
  } else {
    // close
    panel.style.transform = "translateY(90%)";
  }

  document.removeEventListener("mousemove", drag);
  document.removeEventListener("mouseup", endDrag);
  document.removeEventListener("touchmove", drag);
  document.removeEventListener("touchend", endDrag);
}

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

    var selectedTerminal = null;
    var selectedDestination = null;
    var destinationMarker = null;
    var routingControl = null;
    // Fetch terminals from database
    fetch("get-terminals.php")
        .then(response => response.json())
        .then(result => {
            // Fix: Some APIs wrap data inside another 'data' property
            let terminals = Array.isArray(result.data) ? result.data
                : (result.data && Array.isArray(result.data.data) ? result.data.data : []);

            if (result.status === "success" && Array.isArray(terminals)) {
                // Add terminal markers dynamically
                terminals.forEach(function(terminal) {
                    var lat = parseFloat(terminal.latitude);
                    var lng = parseFloat(terminal.longitude);

                    var marker = L.marker([lat, lng], {icon: tricycleIcon})
                        .addTo(map)
                        .on('click', function() {
                            Swal.fire({
                                title: terminal.name,
                                html: `
                                    <img src="${terminal.image}" alt="Terminal Image" style="max-width:300px;display:block;margin:10px auto;">
                                    <button id="view-360-btn" style="background:#4285F4;color:#fff;border:none;border-radius:6px;padding:8px 16px;margin:10px auto;display:block;cursor:pointer;">
                                        <i class="fa fa-street-view"></i> View 360
                                    </button>
                                `,
                                text: "Do you want to select this terminal?",
                                showCancelButton: true,
                                confirmButtonText: 'Select',
                                cancelButtonText: 'Cancel',
                                icon: 'info',
                                didOpen: () => {
                                    document.getElementById('view-360-btn').onclick = function() {
                                        const lat = terminal.latitude;
                                        const lng = terminal.longitude;
                                        const url = `https://www.google.com/maps/@?api=1&map_action=pano&viewpoint=${lat},${lng}`;
                                        window.open(url, '_blank');
                                    };
                                }
                            }).then((swalResult) => {
                                if (swalResult.isConfirmed) {
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
            } else {
                console.error("Failed to load terminals:", result);
            }
        })
        .catch(err => console.error("Error fetching terminals:", err));

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
                L.latLng(selectedTerminal.latitude, selectedTerminal.longitude),
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

        var base = parseFloat(selectedTerminal.base_rate || 11); // default 11 kung walang DB value
        var perKm = parseFloat(selectedTerminal.per_km_rate || 1); // default 1 per km

        var fare = base + (roundedKm * perKm);
        var eta = Math.ceil(route.summary.totalTime / 60);

        document.getElementById('distance').textContent = roundedKm + "";
        document.getElementById('fare').textContent = fare.toFixed(2);
        document.getElementById('eta').textContent = eta + "";
    }

    // Clear button
    document.getElementById('clear-btn').addEventListener('click', function() {
        selectedTerminal = null;
        selectedDestination = null;
        document.getElementById('selected-terminal').textContent = 'None';
        document.getElementById('terminal-image').src = '';
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