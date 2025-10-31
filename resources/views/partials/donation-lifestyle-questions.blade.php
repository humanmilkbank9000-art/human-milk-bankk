<style>
    .lifestyle-container {
        padding: 1.5rem;
    }
    .lifestyle-section {
        margin-bottom: 2rem;
    }
    .lifestyle-section h5 {
        font-weight: 700;
        color: #333;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #ec4899;
    }
    .lifestyle-question {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    .lifestyle-question:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-color: #ec4899;
    }
    .lifestyle-question-text {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }
    .lifestyle-question-translation {
        font-style: italic;
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }
    .lifestyle-options {
        display: flex;
        gap: 1rem;
    }
    .lifestyle-radio-wrapper {
        display: flex;
        align-items: center;
    }
    .lifestyle-radio-wrapper input[type="radio"] {
        width: 20px;
        height: 20px;
        margin-right: 0.5rem;
        cursor: pointer;
        accent-color: #ec4899;
    }
    .lifestyle-radio-wrapper label {
        cursor: pointer;
        font-weight: 500;
        margin-bottom: 0;
        user-select: none;
    }
    .lifestyle-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e9ecef;
    }
    .btn-lifestyle-back {
        background: #6c757d;
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
    }
    .btn-lifestyle-back:hover {
        background: #5a6268;
        color: #fff;
    }
    .btn-lifestyle-submit {
        background: linear-gradient(135deg, #28a745, #20c046);
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        box-shadow: 0 6px 18px rgba(32, 192, 70, 0.12);
    }
    .btn-lifestyle-submit:hover {
        background: linear-gradient(135deg, #20c046, #1ea03d);
        transform: translateY(-2px);
        color: #fff;
    }
    .btn-lifestyle-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>

<div class="lifestyle-container">
    <div class="lifestyle-section">
        <h5>LIFESTYLE</h5>
        
        <!-- Question 1 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I am in good health</div>
            <div class="lifestyle-question-translation">(Maayo akong kahimtang sa akong kalawasan)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="good_health" id="good_health_yes" value="YES" required>
                    <label for="good_health_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="good_health" id="good_health_no" value="NO" required>
                    <label for="good_health_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 2 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I do not smoke</div>
            <div class="lifestyle-question-translation">(Dili ako gapangarilyo)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_smoking" id="no_smoking_yes" value="YES" required>
                    <label for="no_smoking_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_smoking" id="no_smoking_no" value="NO" required>
                    <label for="no_smoking_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 3 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I am not taking medication or herbal supplements</div>
            <div class="lifestyle-question-translation">(Dili ako gatumar ug mga tambal o supplements)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_medication" id="no_medication_yes" value="YES" required>
                    <label for="no_medication_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_medication" id="no_medication_no" value="NO" required>
                    <label for="no_medication_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 4 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I am not consuming alcohol</div>
            <div class="lifestyle-question-translation">(Dili ako gainom ug alkohol)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_alcohol" id="no_alcohol_yes" value="YES" required>
                    <label for="no_alcohol_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_alcohol" id="no_alcohol_no" value="NO" required>
                    <label for="no_alcohol_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 5 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have not had a fever</div>
            <div class="lifestyle-question-translation">(Wala ako naghilanat)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_fever" id="no_fever_yes" value="YES" required>
                    <label for="no_fever_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_fever" id="no_fever_no" value="NO" required>
                    <label for="no_fever_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 6 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have not had cough or colds</div>
            <div class="lifestyle-question-translation">(Wala ako mag-ubo o sip-on)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_cough_colds" id="no_cough_colds_yes" value="YES" required>
                    <label for="no_cough_colds_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_cough_colds" id="no_cough_colds_no" value="NO" required>
                    <label for="no_cough_colds_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 7 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have no breast infections</div>
            <div class="lifestyle-question-translation">(Wala ako impeksyon sa akong totoy)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_breast_infection" id="no_breast_infection_yes" value="YES" required>
                    <label for="no_breast_infection_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="no_breast_infection" id="no_breast_infection_no" value="NO" required>
                    <label for="no_breast_infection_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 8 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have followed all hygiene instructions</div>
            <div class="lifestyle-question-translation">(Gisunod nako ang tanan mga instruksyon tumong sa kalimpyohanon)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_hygiene" id="followed_hygiene_yes" value="YES" required>
                    <label for="followed_hygiene_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_hygiene" id="followed_hygiene_no" value="NO" required>
                    <label for="followed_hygiene_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 9 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have followed all labeling instructions</div>
            <div class="lifestyle-question-translation">(Gisunod nako ang tanan mga instruksyon tumong sa pagmarka)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_labeling" id="followed_labeling_yes" value="YES" required>
                    <label for="followed_labeling_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_labeling" id="followed_labeling_no" value="NO" required>
                    <label for="followed_labeling_no">NO</label>
                </div>
            </div>
        </div>

        <!-- Question 10 -->
        <div class="lifestyle-question">
            <div class="lifestyle-question-text">I have followed all storage instructions</div>
            <div class="lifestyle-question-translation">(Gisunod nako ang tanan mga instruksyon tumong sa pag-tipig sa gatas)</div>
            <div class="lifestyle-options">
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_storage" id="followed_storage_yes" value="YES" required>
                    <label for="followed_storage_yes">YES</label>
                </div>
                <div class="lifestyle-radio-wrapper">
                    <input type="radio" name="followed_storage" id="followed_storage_no" value="NO" required>
                    <label for="followed_storage_no">NO</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="lifestyle-buttons">
        <button type="button" class="btn btn-lifestyle-back" id="lifestyle-back-btn">← Back</button>
        <button type="button" class="btn btn-lifestyle-submit" id="lifestyle-submit-btn" disabled>Submit Donation</button>
    </div>
</div>

<script>
(function() {
    function checkAllAnswered() {
        const questions = [
            'good_health',
            'no_smoking',
            'no_medication',
            'no_alcohol',
            'no_fever',
            'no_cough_colds',
            'no_breast_infection',
            'followed_hygiene',
            'followed_labeling',
            'followed_storage'
        ];
        
        const allAnswered = questions.every(q => {
            return document.querySelector(`input[name="${q}"]:checked`) !== null;
        });
        
        const submitBtn = document.getElementById('lifestyle-submit-btn');
        if (submitBtn) {
            submitBtn.disabled = !allAnswered;
        }
    }
    
    // Add listeners to all radio buttons
    document.querySelectorAll('.lifestyle-question input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', checkAllAnswered);
    });
    
    // Back button
    document.getElementById('lifestyle-back-btn')?.addEventListener('click', function() {
            if (typeof window.openHomeCollectionModal === 'function') {
                window.openHomeCollectionModal();
            }
        });
    
    // Submit button
    document.getElementById('lifestyle-submit-btn')?.addEventListener('click', function() {
            if (typeof window.submitHomeCollectionDonation === 'function') {
                window.submitHomeCollectionDonation();
            }
        });
})();
</script>
