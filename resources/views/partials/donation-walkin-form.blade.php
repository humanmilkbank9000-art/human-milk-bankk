<form action="{{ route('donation.store') }}" method="POST" id="walkInForm">
    @csrf
    <input type="hidden" name="donation_method" value="walk_in">
    <div id="walkin-fields">
        <div class="alert alert-info">
            <strong>Walk-in Process:</strong> Click on a highlighted date in the calendar to see available appointment
            times.
        </div>
        <div class="mb-3">
            <label class="form-label">Select Appointment Date:</label>
            <div id="appointment-calendar" class="calendar-container">
                <!-- Calendar will be generated here -->
            </div>
            <input type="hidden" name="appointment_date" id="selected_date">
        </div>
        <div class="mb-3" id="time-slots-container" style="display: none;">
            <label class="form-label">Available Time Slots:</label>
            <div id="available-slots">
                <!-- Time slots will be loaded here -->
            </div>
        </div>

        <div class="mt-3 text-end">
            <!-- Submit button expected by page JS: id walk-in-submit-btn -->
            <button type="submit" id="walk-in-submit-btn" class="btn btn-primary" disabled>
                Confirm
            </button>
        </div>
    </div>
</form>