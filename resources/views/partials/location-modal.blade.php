<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>Donor Home Collection Location
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Donor Name:</strong> <span id="modal-donor-name"></span>
                </div>
                <div class="mb-3">
                    <strong>Address:</strong> <span id="modal-donor-address"></span>
                </div>
                <div id="map" style="height: 400px; width: 100%; border-radius: 8px; border: 1px solid #ddd;"></div>
                <div class="mt-3 text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Map powered by OpenStreetMap
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    let map = null;
    let marker = null;

    function showLocationModal(donorName, donorAddress, latitude, longitude) {
        // Update modal content
        document.getElementById('modal-donor-name').textContent = donorName;
        document.getElementById('modal-donor-address').textContent = donorAddress;

        // Show the modal
        const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
        locationModal.show();

        // Initialize or update map after modal is shown
        document.getElementById('locationModal').addEventListener('shown.bs.modal', function () {
            if (!map) {
                // Initialize map
                map = L.map('map').setView([latitude, longitude], 15);

                // Add OpenStreetMap tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 19,
                }).addTo(map);

                // Add marker
                marker = L.marker([latitude, longitude]).addTo(map)
                    .bindPopup('<b>' + donorName + '</b><br>' + donorAddress)
                    .openPopup();
            } else {
                // Update existing map
                map.setView([latitude, longitude], 15);

                if (marker) {
                    marker.setLatLng([latitude, longitude])
                        .bindPopup('<b>' + donorName + '</b><br>' + donorAddress)
                        .openPopup();
                } else {
                    marker = L.marker([latitude, longitude]).addTo(map)
                        .bindPopup('<b>' + donorName + '</b><br>' + donorAddress)
                        .openPopup();
                }
            }

            // Invalidate size to fix display issues
            setTimeout(function () {
                map.invalidateSize();
            }, 100);
        }, { once: true });
    }
</script>

<style>
    /* Ensure Leaflet map displays correctly */
    #map {
        z-index: 1;
    }

    .leaflet-container {
        font-family: var(--body-font, 'Quicksand', sans-serif);
    }

    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }

    .leaflet-popup-content {
        margin: 13px 19px;
        line-height: 1.4;
    }
</style>