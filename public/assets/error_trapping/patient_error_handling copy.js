// Blood Pressure Validation
document.getElementById('bloodPressure').addEventListener('input', function () {
    const bloodPressureInput = this.value;
    const feedback = document.getElementById('bloodPressureFeedback');

    // Regex to match blood pressure format like "120/80"
    const bpRegex = /^(\d{2,3})\/(\d{2,3})$/;
    const match = bloodPressureInput.match(bpRegex);

    if (match) {
        const systolic = parseInt(match[1]);
        const diastolic = parseInt(match[2]);

        // Define blood pressure categories and provide feedback
        if (systolic < 120 && diastolic < 80) {
            feedback.textContent = "Normal Blood Pressure.";
            feedback.style.color = "green"; // Normal
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else if ((systolic >= 120 && systolic <= 139) || (diastolic >= 80 && diastolic <= 89)) {
            feedback.textContent = "Prehypertension.";
            feedback.style.color = "yellow"; // Prehypertension
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else if ((systolic >= 140 && systolic <= 159) || (diastolic >= 90 && diastolic <= 99)) {
            feedback.textContent = "Hypertension (Stage 1).";
            feedback.style.color = "orange"; // Hypertension Stage 1
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else if (systolic >= 160 || diastolic >= 100) {
            feedback.textContent = "Hypertension (Stage 2).";
            feedback.style.color = "red"; // Hypertension Stage 2
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else if (systolic >= 180 || diastolic > 110) {
            feedback.textContent = "Hypertensive Crisis. Seek emergency care!";
            feedback.style.color = "darkred"; // Hypertensive Crisis
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        }
    } else if (bloodPressureInput) {
        feedback.textContent = "Please enter blood pressure in the format: Systolic/Diastolic (e.g., 120/80)";
        feedback.style.color = "red"; // Error feedback
        feedback.style.fontWeight = "normal"; // Normal weight for error message
    } else {
        feedback.textContent = "";
    }


});

// Height Validation
document.getElementById('height').addEventListener('input', function () {
    const heightInput = this.value;
    const feedback = document.getElementById('heightFeedback');

    // Validate height input
    if (heightInput) {
        const height = parseFloat(heightInput);
        if (height <= 0) {
            feedback.textContent = "Height must be a positive number.";
            feedback.style.color = "red";
            feedback.style.fontWeight = "normal"; // Normal weight for error message
        } else if (height < 150) {
            feedback.textContent = "Height is below average.";
            feedback.style.color = "orange";
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else if (height > 200) {
            feedback.textContent = "Height is above average.";
            feedback.style.color = "orange";
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        } else {
            feedback.textContent = "Height is within the normal range.";
            feedback.style.color = "green";
            feedback.style.fontWeight = "bold"; // Add bold for emphasis
        }
    } else {
        feedback.textContent = ""; // Clear feedback if input is empty
    }
});

// Body Temperature Validation
document.getElementById('bodyTemperature').addEventListener('input', function () {
    const bodyTempInput = this.value;
    const feedback = document.getElementById('bodyTempFeedback');

    // Validate body temperature input
    if (bodyTempInput) {
        const bodyTemp = parseFloat(bodyTempInput);
        if (bodyTemp < 35.0 || bodyTemp > 42.0) {
            feedback.textContent = "Body temperature must be between 35.0°C and 42.0°C.";
            feedback.style.color = "red";
        } else if (bodyTemp < 36.1) {
            feedback.textContent = "Body temperature is below normal.";
            feedback.style.color = "orange";
        } else if (bodyTemp > 37.5) {
            feedback.textContent = "Body temperature is above normal.";
            feedback.style.color = "orange";
        } else {
            feedback.textContent = "Body temperature is within the normal range.";
            feedback.style.color = "green";
        }
    } else {
        feedback.textContent = "";
    }
});

// Weight Validation
document.getElementById('weight').addEventListener('input', function () {
    const weightInput = this.value;
    const feedback = document.getElementById('weightFeedback');

    // Validate weight input
    if (weightInput) {
        const weight = parseFloat(weightInput);
        if (weight <= 0) {
            feedback.textContent = "Weight must be a positive number.";
            feedback.style.color = "red";
        } else if (weight < 50) {
            feedback.textContent = "Weight is below average.";
            feedback.style.color = "orange";
        } else if (weight > 100) {
            feedback.textContent = "Weight is above average.";
            feedback.style.color = "orange";
        } else {
            feedback.textContent = "Weight is within the normal range.";
            feedback.style.color = "green";
        }
    } else {
        feedback.textContent = "";
    }
});

// Show or hide the 'Others' fields container based on checkbox
document.getElementById("complaintType13").addEventListener("change", function () {
    document.getElementById("otherComplaintContainer").style.display = this.checked ? "block" : "none";
});


// Function to add new 'Others' fields
function addOtherComplaint() {
    const container = document.getElementById("otherComplaintContainer");
    const newField = document.createElement("div");
    newField.classList.add("d-flex", "align-items-center", "mb-2");

    newField.innerHTML = `
          <input type="text" name="otherComplaint[]" class="form-control ms-3" placeholder="Chief Complaints" style="width: 160px;">
          <input type="number" name="otherComplaintDays[]" class="form-control mx-3" placeholder="Number of days" style="width: 160px;">
          <button type="button" class="btn btn-danger btn-icon rounded-circle custom-rounded-btn" onclick="removeField(this)">x</button>
      `;
    container.appendChild(newField);
}

// Show or hide the 'Others' fields container based on checkbox
document.getElementById("dentalType6").addEventListener("change", function () {
    document.getElementById("otherComplaintContainers").style.display = this.checked ? "block" : "none";
});

// Function to add new 'Others' fields
function addOtherComplaints() {
    const container = document.getElementById("otherComplaintContainers");
    const newField = document.createElement("div");
    newField.classList.add("d-flex", "align-items-center", "mb-2");

    newField.innerHTML = `
          <input type="text" name="otherDental[]" class="form-control ms-3" placeholder="Chief Complaints" style="width: 160px;">
          <input type="number" name="otherDentalDays[]" class="form-control mx-3" placeholder="Number of days" style="width: 160px;">
          <button type="button" class="btn btn-danger btn-icon rounded-circle custom-rounded-btn" onclick="removeField(this)">x</button>
      `;
    container.appendChild(newField);
}

// Function to remove a specific 'Others' field
function removeField(button) {
    button.parentElement.remove();
}