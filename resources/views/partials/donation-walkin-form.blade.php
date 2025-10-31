<form action="{{ route('donation.store') }}" method="POST" id="walkInForm">
    @csrf
    <input type="hidden" name="donation_method" value="walk_in">
    <input type="hidden" name="availability_id" id="availability_id">
    <input type="hidden" name="appointment_date" id="selected_date">

    <div id="walkin-fields">
        <div class="alert alert-info">
            <strong>Walk-in Process:</strong> Select an available date (highlighted in green) in the calendar below.
        </div>

        <div class="mb-3 d-flex flex-column align-items-center">
            <label class="form-label">Select Appointment Date:</label>
            <div id="appointment-calendar" class="calendar-container">
                <!-- Calendar will be generated here -->
            </div>
            <small id="slot-status" class="mt-2 d-block text-muted">Select a highlighted date to check availability.</small>
        </div>

        <div class="mt-3 text-end">
            <button type="button" id="walk-in-submit-btn" class="btn btn-primary" onclick="submitWalkInDonation()" disabled>
                Confirm
            </button>
        </div>
    </div>
</form>