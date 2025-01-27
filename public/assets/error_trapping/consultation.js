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

// Function to remove a specific 'Others' field
function removeField(button) {
    button.parentElement.remove();
}
