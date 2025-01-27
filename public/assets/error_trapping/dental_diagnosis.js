let selectedDiagnoses = []; // Array to track selected diagnoses
let selectedMedicines = [];

function addDiagnosisFields() {
    const additionalFieldsContainer = document.createElement('div');
    additionalFieldsContainer.classList.add('row', 'g-2', 'mb-3');
    additionalFieldsContainer.innerHTML = `
        <div class="col-md-6 mb-3">
            <label class="form-label">Diagnosis</label>
            <div class="input-group">
                <input type="text" class="form-control diagnosisInput" placeholder="Select or enter a diagnosis" autocomplete="off" name="diagnosis[]" required>
                <input type="hidden" class="diagnosis_id" name="diagnosis_id[]">
                <div class="diagnosisType diagnosisType-box" style="display: none;"></div>
            </div>
        </div>
        <div class="col-md-5 mb-3">
            <label class="form-label">ICD-10 Code</label>
            <input type="text" class="form-control" placeholder="Enter ICD-10 code" name="icdCode[]" required>
        </div>
        <div class="col-md-1 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-icon rounded-circle custom-rounded-btn" onclick="removeDiagnosisFields(this)">-</button>
        </div>
    `;

    document.getElementById('additionalDiagnoses').appendChild(additionalFieldsContainer);
}

// Function to remove diagnosis fields
function removeDiagnosisFields(button) {
    const diagnosisId = $(button).closest('.input-group').find('.diagnosis_id').val();
    if (diagnosisId) {
        selectedDiagnoses = selectedDiagnoses.filter(id => id !== diagnosisId); // Remove from selected
    }
    button.closest('.row').remove();
}

// Event delegation for dynamically added fields
$(document).on('click', '.diagnosisInput', function () {
    const diagnosisType = $(this).siblings('.diagnosisType');

    // Check if suggestions are already displayed
    if (diagnosisType.is(':visible')) {
        diagnosisType.hide();
    } else {
        // Fetch Diagnosis suggestions
        $.ajax({
            url: '/fetch-diagnosis',
            method: 'GET',
            success: function (response) {
                diagnosisType.empty();
                diagnosisType.append('<div class="diagnosisType-item" style="pointer-events: none; color: gray;">Select a diagnosis</div>');

                response.forEach(function (diagnosis) {
                    if (diagnosis.diagnosis_type === 2 && !selectedDiagnoses.includes(diagnosis.encrypted_id)) { // Check if already selected
                        diagnosisType.append(
                            '<div class="diagnosisType-item" data-diagnosis-id="' + diagnosis.encrypted_id + '" data-diagnosis-code="' + diagnosis.diagnosis_code + '">' +
                            diagnosis.diagnosis_name + '</div>'
                        );
                    }
                });

                // Show suggestions if there are any
                if (diagnosisType.children().length > 1) { // Only show if there are valid items
                    diagnosisType.show();
                } else {
                    diagnosisType.hide();
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }
});

// Event delegation for selecting a diagnosis item
$(document).on('click', '.diagnosisType-item', function () {
    const diagnosisCode = $(this).data('diagnosis-code'); // Get the diagnosis code from the selected item
    $('#icdCode').val(diagnosisCode); // Set the diagnosis code to the input field with id="icdCode"

    // Optionally, you can also hide the diagnosis suggestions after selecting
    $(this).parent().hide();
});

// Handle suggestion click
$(document).on('click', '.diagnosisType-item', function () {
    const diagnosisName = $(this).text();
    const diagnosisId = $(this).data('diagnosis-id');

    const diagnosisInput = $(this).closest('.input-group').find('.diagnosisInput');
    const hiddenDiagnosisId = $(this).closest('.input-group').find('.diagnosis_id');

    diagnosisInput.val(diagnosisName);
    hiddenDiagnosisId.val(diagnosisId);

    // Add the selected diagnosis ID to the list
    selectedDiagnoses.push(diagnosisId);

    $(this).closest('.diagnosisType').hide();
});

// Hide suggestions when clicking outside
$(document).click(function (event) {
    if (!$(event.target).closest('.diagnosisInput, .diagnosisType').length) {
        $('.diagnosisType').hide();
    }
});

function addMedicineFields() {
    const additionalFieldsContainer = document.createElement('div');
    additionalFieldsContainer.classList.add('row', 'g-2', 'mb-3');
    additionalFieldsContainer.innerHTML = `
        <div class="col-md-6 mb-3">
            <div style="width: 100%; position: relative;">
                <label class="form-label">Prescribe Medicines</label>
                <input type="text" class="form-control medicinesInput" placeholder="Select or enter a medicine" autocomplete="off" name="medicines[]" required>
                <input type="hidden" class="medicine_id" name="medicine_id[]">
                <div class="medicinesType-box" style="display: none;"></div>
            </div>
        </div>

        <div class="col-md-5 mb-3">
            <div style="width: 100%; position: relative;">
                <label class="form-label">Type of Medicine</label>
                <input type="text" class="form-control medicineTypeInput" placeholder="Enter type of medicine (e.g., capsule, tablet, liquid)" autocomplete="off" name="medicineType[]" required>
                <input type="hidden" class="medicineType_id" name="medicineType_id[]">
                <div class="medicineType-box" style="display: none;"></div>
            </div>
        </div>
        
        <div class="col-md-1 mb-3 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-icon rounded-circle custom-rounded-btn" onclick="removeMedicineFields(this)">-</button>
        </div>

        <div class="col-md-6 mb-3">
            <label for="dosageInput" class="form-label">Dosage</label>
                <div style="width: 100%; position: relative;">
                    <input type="text" class="form-control dosageInput"
                        placeholder="Enter type of dosage (e.g., mg)" id="dosageInput" autocomplete="off"
                        name="dosage[]" required>
                    </div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="quantityInput" class="form-label">Quantity</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control quantityInput" placeholder="Enter quantity"
                                    id="quantityInput" autocomplete="off" name="quantity[]" required>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="frequencyInput" class="form-label">Frequency</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control frequencyInput"
                                    placeholder="Enter frequency (e.g., twice a day, once a day)" id="frequencyInput"
                                    autocomplete="off" name="frequency[]" required>
                            </div>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label for="durationInput" class="form-label">Duration</label>
                            <div style="width: 100%; position: relative;">
                                <input type="text" class="form-control durationInput"
                                    placeholder="Enter duration (e.g., weekly, monthly)" id="durationInput"
                                    autocomplete="off" name="duration[]" required>
                            </div>
                        </div>
    `;

    document.getElementById('additionalMedicines').appendChild(additionalFieldsContainer);
}

function removeMedicineFields(button) {
    const medicineId = $(button).closest('.row').find('.medicine_id').val(); // Corrected selector to .row
    if (medicineId) {
        selectedMedicines = selectedMedicines.filter(id => id !== medicineId); // Remove from selected
    }
    button.closest('.row').remove();
}

// Event delegation for dynamically added fields
$(document).on('click', '.medicinesInput', function () {
    const medicinesType = $(this).siblings('.medicinesType-box');

    // Check if suggestions are already displayed
    if (medicinesType.is(':visible')) {
        medicinesType.hide();
    } else {
        // Fetch Medicines suggestions
        $.ajax({
            url: '/fetch-medicines', // Fetch medicines only
            method: 'GET',
            success: function (response) {
                medicinesType.empty();
                medicinesType.append('<div class="medicinesType-item" style="pointer-events: none; color: gray;">Select a medicine</div>');

                response.forEach(function (medicine) {
                    if (medicine.medicine_type === 2 && !selectedMedicines.includes(medicine.encrypted_id)) { // Check if already selected
                        medicinesType.append(
                            '<div class="medicinesType-item" data-medicine-id="' + medicine.encrypted_id + '">' +
                            '<span>' + medicine.medicines_name + '</span>' +
                            '</div>'
                        );
                    }
                });

                // Show suggestions if there are any
                if (medicinesType.children().length > 1) { // Only show if there are valid items
                    medicinesType.show();
                } else {
                    medicinesType.hide();
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }
});

// Handle medicine suggestion click
$(document).on('click', '.medicinesType-item', function () {
    const medicineName = $(this).text();
    const medicineId = $(this).data('medicine-id');

    const medicinesInput = $(this).closest('.row').find('.medicinesInput'); // Corrected selector to .row
    const hiddenMedicineId = $(this).closest('.row').find('.medicine_id'); // Corrected selector to .row

    medicinesInput.val(medicineName);
    hiddenMedicineId.val(medicineId);

    // Add the selected medicine ID to the list
    selectedMedicines.push(medicineId);

    $(this).closest('.medicinesType-box').hide();
});

// Hide suggestions when clicking outside
$(document).click(function (event) {
    if (!$(event.target).closest('.medicinesInput, .medicinesType-box').length) {
        $('.medicinesType-box').hide();
    }
});
