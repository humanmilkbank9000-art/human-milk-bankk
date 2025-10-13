<form action="{{ route('donation.store') }}" method="POST" id="homeCollectionForm">
    @csrf
    <input type="hidden" name="donation_method" value="home_collection">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <div id="home-collection-fields">
        <div class="alert alert-warning border-0 shadow-sm mb-3">
            <div class="d-flex align-items-start">
                <i class="fas fa-map-marker-alt me-2 mt-1" style="font-size: 1.2rem;"></i>
                <div>
                    <strong><i class="fas fa-exclamation-circle me-1"></i>Location Permission Required</strong>
                    <p class="mb-0 mt-1">Please allow location access when prompted. We need your location to ensure
                        accurate home collection service. If you previously denied access, please enable it in your
                        browser settings.</p>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            <strong>Home Collection Process:</strong> Provide donation details below. The admin will contact you to
            schedule a pickup time.
        </div>
        <div class="mb-3">
            <label for="bags_home" class="form-label">Number of Bags:</label>
            <input type="number" id="bags_home" name="number_of_bags" class="form-control" min="1" max="20"
                oninput="generateBagVolumeFields()">
        </div>
        <div id="bag-volumes-container" style="display: none;">
            <label class="form-label">Volume for each bag (ml):</label>
            <div id="bag-volume-fields">
                <!-- Individual bag volume inputs will be generated here -->
            </div>
        </div>
        <div class="mb-3" id="total-volume-display" style="display: none;">
            <div class="alert alert-success">
                <strong>Total Volume:</strong> <span id="total_home">0.00</span> ml
            </div>
        </div>

        <div class="mt-3 text-end">
            <!-- Submit button expected by page JS: id home-submit-btn -->
            <button type="submit" id="home-submit-btn" class="btn btn-success" disabled>
                Schedule
            </button>
        </div>
    </div>
</form>

<script>
    // Get user's current location when modal is shown
    document.addEventListener('DOMContentLoaded', function () {
        const homeCollectionModal = document.getElementById('homeCollectionModal');

        if (homeCollectionModal) {
            homeCollectionModal.addEventListener('shown.bs.modal', function () {
                if (navigator.geolocation) {
                    // Show requesting location feedback
                    const alertBox = homeCollectionModal.querySelector('.alert-warning');
                    if (alertBox) {
                        alertBox.innerHTML = `
                            <div class="d-flex align-items-start">
                                <i class="fas fa-spinner fa-spin me-2 mt-1" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong>Requesting Location...</strong>
                                    <p class="mb-0 mt-1">Please allow location access when prompted by your browser.</p>
                                </div>
                            </div>
                        `;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            document.getElementById('latitude').value = position.coords.latitude;
                            document.getElementById('longitude').value = position.coords.longitude;
                            console.log('Location captured:', position.coords.latitude, position.coords.longitude);

                            // Update alert to show success
                            if (alertBox) {
                                alertBox.className = 'alert alert-success border-0 shadow-sm mb-3';
                                alertBox.innerHTML = `
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-check-circle me-2 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <strong>Location Captured Successfully!</strong>
                                            <p class="mb-0 mt-1">Your location has been recorded for home collection service.</p>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        function (error) {
                            console.warn('Location access denied or unavailable:', error.message);

                            // Update alert to show error
                            if (alertBox) {
                                alertBox.className = 'alert alert-danger border-0 shadow-sm mb-3';
                                alertBox.innerHTML = `
                                    <div class="d-flex align-items-start">
                                        <i class="fas fa-times-circle me-2 mt-1" style="font-size: 1.2rem;"></i>
                                        <div>
                                            <strong>Location Access Denied</strong>
                                            <p class="mb-0 mt-1">
                                                ${error.code === 1 ? 'You denied location access. Please enable it in your browser settings to proceed with home collection.' :
                                        error.code === 2 ? 'Unable to get your location. Please check your device settings.' :
                                            'Location request timed out. Please try again or check your browser settings.'}
                                            </p>
                                        </div>
                                    </div>
                                `;
                            }
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                } else {
                    // Browser doesn't support geolocation
                    const alertBox = homeCollectionModal.querySelector('.alert-warning');
                    if (alertBox) {
                        alertBox.className = 'alert alert-danger border-0 shadow-sm mb-3';
                        alertBox.innerHTML = `
                            <div class="d-flex align-items-start">
                                <i class="fas fa-times-circle me-2 mt-1" style="font-size: 1.2rem;"></i>
                                <div>
                                    <strong>Geolocation Not Supported</strong>
                                    <p class="mb-0 mt-1">Your browser does not support location services. Please use a modern browser for home collection.</p>
                                </div>
                            </div>
                        `;
                    }
                }
            });
        }
    });
</script>