// Blood Pressure Validation
document.getElementById('bloodPressure').addEventListener('input', function () {
    const bloodPressureInput = this.value;
    const feedback = document.getElementById('bloodPressureFeedback');
    const asterisk = document.getElementById('bloodPressureAsterisk'); // Asterisk element

    // Regex to match blood pressure format like "120/80"
    const bpRegex = /^(\d{2,3})\/(\d{2,3})$/;
    const match = bloodPressureInput.match(bpRegex);

    if (match) {
        const systolic = parseInt(match[1]);
        const diastolic = parseInt(match[2]);

        // Define blood pressure categories and provide feedback
        if (systolic < 90 && diastolic < 60) {
            feedback.textContent = "Low Blood Pressure";
            feedback.style.backgroundColor = "#2E77C8";
            feedback.style.color = "white";
            feedback.style.fontWeight = "bold";
        } else if (systolic >= 90 && systolic < 120 && diastolic >= 60 && diastolic < 80) {
            feedback.textContent = "Normal Blood Pressure";
            feedback.style.backgroundColor = "green";
            feedback.style.color = "white";
            feedback.style.fontWeight = "bold";
        } else if (systolic >= 120 && systolic <= 139 || diastolic >= 80 && diastolic <= 89) {
            feedback.textContent = "Prehypertension";
            feedback.style.color = "#555454";
            feedback.style.backgroundColor = "yellow";
            feedback.style.fontWeight = "bold";
        } else if (systolic >= 140 && systolic <= 159 || diastolic >= 90 && diastolic <= 99) {
            feedback.textContent = "Hypertension (Stage 1)";
            feedback.style.backgroundColor = "orange";
            feedback.style.color = "white";
            feedback.style.fontWeight = "bold";
        } else if (systolic >= 160 && systolic <= 179 || diastolic >= 100 && diastolic <= 109) {
            feedback.textContent = "Hypertension (Stage 2)";
            feedback.style.backgroundColor = "red";
            feedback.style.color = "white";
            feedback.style.fontWeight = "bold";
        } else if (systolic >= 180 || diastolic >= 110) {
            feedback.textContent = "Hypertensive Crisis. Seek emergency care!";
            feedback.style.backgroundColor = "darkred";
            feedback.style.color = "white";
            feedback.style.fontWeight = "bold";
        }
        asterisk.style.display = 'none'; // Hide the asterisk for valid input
    } else if (bloodPressureInput) {
        feedback.textContent = "Please enter blood pressure in the format: Systolic/Diastolic (e.g., 120/80)";
        feedback.style.backgroundColor = "red";
        feedback.style.color = "white";
        feedback.style.fontWeight = "normal";
        asterisk.style.display = 'none';
    } else {
        feedback.textContent = "";
        asterisk.style.display = 'inline'; // Show the asterisk when input is empty
    }
});

// Body Temperature Validation
document.getElementById('bodyTemperature').addEventListener('input', function () {
    const bodyTempInput = this.value;
    const feedback = document.getElementById('bodyTempFeedback');
    const asterisk = document.getElementById('bodyTemperatureAsterisk');

    // Validate body temperature input
    if (bodyTempInput) {
        const bodyTemp = parseFloat(bodyTempInput);
        if (bodyTemp < 35.0 || bodyTemp > 42.0) {
            feedback.textContent = "Body temperature must be between 35.0°C and 42.0°C";
            feedback.style.backgroundColor = "red";
            feedback.style.color = "white";
        } else if (bodyTemp < 36.1) {
            feedback.textContent = "Body temperature is below normal";
            feedback.style.backgroundColor = "orange";
            feedback.style.color = "white";
        } else if (bodyTemp > 37.5) {
            feedback.style.backgroundColor = "orange";
            feedback.style.color = "white";
        } else {
            feedback.textContent = "Body temperature is within the normal range";
            feedback.style.backgroundColor = "green";
            feedback.style.color = "white";
        }
        asterisk.style.display = 'none';
    } else {
        feedback.textContent = "";
        asterisk.style.display = 'inline'; // Show the asterisk when input is empty
    }
});


document.getElementById('height').addEventListener('input', function () {
    const heightInput = this.value;
    const feedback = document.getElementById('heightFeedback');
    const asterisk = document.getElementById('heightAsterisk');
    const age = parseInt(document.getElementById('ageView').textContent.trim(), 10);

    if (heightInput) {
        const height = parseFloat(heightInput);
        if (height <= 0) {
            feedback.textContent = "Height must be a positive number";
            feedback.style.backgroundColor = "red";
            feedback.style.color = "white";
        } else if (age < 18) { // Validation for children
            if (height < 120) {
                feedback.textContent = "Height is below average for your age.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else if (height > 180) {
                feedback.textContent = "Height is above average for your age.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else {
                feedback.textContent = "Height is within the normal range for your age.";
                feedback.style.backgroundColor = "green";
                feedback.style.color = "white";
            }
        } else { // Validation for adults
            if (height < 150) {
                feedback.textContent = "Height is below average.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else if (height > 200) {
                feedback.textContent = "Height is above average.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else {
                feedback.textContent = "Height is within the normal range.";
                feedback.style.backgroundColor = "green";
                feedback.style.color = "white";
            }
        }
        asterisk.style.display = 'none';
    } else {
        feedback.textContent = "";
        asterisk.style.display = 'inline';
    }
});

document.getElementById('weight').addEventListener('input', function () {
    const weightInput = this.value;
    const feedback = document.getElementById('weightFeedback');
    const asterisk = document.getElementById('weightAsterisk');
    const age = parseInt(document.getElementById('ageView').textContent.trim(), 10);

    if (weightInput) {
        const weight = parseFloat(weightInput);
        if (weight <= 0) {
            feedback.textContent = "Weight must be a positive number";
            feedback.style.backgroundColor = "red";
            feedback.style.color = "white";
        } else if (age < 18) { // Validation for children
            if (weight < 30) {
                feedback.textContent = "Weight is below average for your age.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else if (weight > 70) {
                feedback.textContent = "Weight is above average for your age.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else {
                feedback.textContent = "Weight is within the normal range for your age.";
                feedback.style.backgroundColor = "green";
                feedback.style.color = "white";
            }
        } else { // Validation for adults
            if (weight < 50) {
                feedback.textContent = "Weight is below average.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else if (weight > 100) {
                feedback.textContent = "Weight is above average.";
                feedback.style.backgroundColor = "orange";
                feedback.style.color = "white";
            } else {
                feedback.textContent = "Weight is within the normal range.";
                feedback.style.backgroundColor = "green";
                feedback.style.color = "white";
            }
        }
        asterisk.style.display = 'none';
    } else {
        feedback.textContent = "";
        asterisk.style.display = 'inline';
    }
});

