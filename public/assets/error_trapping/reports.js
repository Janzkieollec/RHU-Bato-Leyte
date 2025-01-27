document.addEventListener('DOMContentLoaded', function () {
    const reportTypeSelect = document.getElementById('reportType');
    const monthlySelect = document.getElementById('selectType');
    const selectYears = document.getElementById('selectYear');
    const selectYearsLabel = document.querySelector('label[for="selectYear"]');

    const monthlyOptions = [
        { value: '1', text: 'January' },
        { value: '2', text: 'February' },
        { value: '3', text: 'March' },
        { value: '4', text: 'April' },
        { value: '5', text: 'May' },
        { value: '6', text: 'June' },
        { value: '7', text: 'July' },
        { value: '8', text: 'August' },
        { value: '9', text: 'September' },
        { value: '10', text: 'October' },
        { value: '11', text: 'November' },
        { value: '12', text: 'December' },
    ];

    const quarterOptions = [
        { value: '1', text: 'Quarter 1' },
        { value: '2', text: 'Quarter 2' },
        { value: '3', text: 'Quarter 3' },
        { value: '4', text: 'Quarter 4' },
    ];

    const annualOptions = [];

    // Get the current year and populate the annualOptions array
    const currentYear = new Date().getFullYear();
    for (let i = 0; i < 5; i++) {
        annualOptions.push({
            value: currentYear - i,
            text: (currentYear - i).toString()
        });
    }

    // Populate selectYears with annualOptions by default
    function populateSelectYears() {
        selectYears.innerHTML = '';
        annualOptions.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.text;
            selectYears.appendChild(opt);
        });
        // Set the current year as the default selected option
        selectYears.value = currentYear.toString();
    }

    // Function to update the dropdown based on report type
    function updateDropdown() {
        const selectedReportType = reportTypeSelect.value;

        if (selectedReportType === 'quarterly') {
            // Show quarters and hide the selectYears dropdown and label
            monthlySelect.innerHTML = '';
            selectYears.style.display = 'block';
            selectYearsLabel.style.display = 'block';

            quarterOptions.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.value;
                opt.textContent = option.text;
                monthlySelect.appendChild(opt);
            });

            document.querySelector('label[for="selectType"]').textContent = 'Select Quarter:';
        } else if (selectedReportType === 'annual') {
            // Show years in the main dropdown and hide the selectYears dropdown and label
            monthlySelect.innerHTML = '';
            selectYears.style.display = 'none';
            selectYearsLabel.style.display = 'none';

            annualOptions.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.value;
                opt.textContent = option.text;
                monthlySelect.appendChild(opt);
            });

            document.querySelector('label[for="selectType"]').textContent = 'Select Year:';
        } else {
            // Show months and display the selectYears dropdown and label
            monthlySelect.innerHTML = '';
            selectYears.style.display = 'block';
            selectYearsLabel.style.display = 'block';

            monthlyOptions.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.value;
                opt.textContent = option.text;
                monthlySelect.appendChild(opt);
            });

            document.querySelector('label[for="selectType"]').textContent = 'Select Month:';
        }
    }

    // Populate selectYears and update the dropdown based on the default selection
    populateSelectYears();
    updateDropdown();

    // Event listener for report type change
    reportTypeSelect.addEventListener('change', updateDropdown);
});
