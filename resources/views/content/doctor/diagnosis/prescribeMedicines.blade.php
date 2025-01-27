@extends('layouts/contentNavbarLayout')

@section('title', 'Diagnosis Records')

@section('page-script')
<script src="{{ asset('assets/js/patient_consultation_diagnosis.js') }}"></script>
<script>
function printPrescription() {
    var printContent = document.getElementById('prescriptionContent').innerHTML;
    var printWindow = window.open('', '', 'height=800, width=1000');

    // Add the content and include print styles
    printWindow.document.write('<html><head><title>Print Prescription</title>');
    printWindow.document.write('<style>');
    printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
    printWindow.document.write('.prescription { max-width: 600px; margin: auto; }');
    printWindow.document.write(
        '.header { display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }');
    printWindow.document.write('.logo-img { width: 120px; height: 120px; margin-right: 20px; }');
    printWindow.document.write('.header-text { text-align: center; }');
    printWindow.document.write('.details { margin-bottom: 20px; }');
    printWindow.document.write(
        '.rx { font-size: 60px; font-weight: bold; margin-bottom: 20px; text-align: left; font-family: "Lucida Calligraphy", cursive; font-style: italic; }'
    );
    printWindow.document.write('.medicines { margin-top: 20px; }');
    printWindow.document.write('.date { text-align: right; }');
    printWindow.document.write('.footer { text-align: right; margin-top: 350px; }');
    printWindow.document.write('@media print { .button { display: none; } }');
    printWindow.document.write('</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContent);
    printWindow.document.write('</body></html>');

    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection

@section('content')
<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

.prescription {
    max-width: 600px;
    margin: auto;
}

.header {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}

.logo-img {
    width: 120px;
    height: 120px;
    margin-right: 20px;
}

.header-text {
    text-align: center;
}

.details {
    margin-bottom: 20px;
}

.rx {
    font-size: 60px;
    font-weight: bold;
    margin-bottom: 20px;
    text-align: left;
    font-family: 'Lucida Calligraphy', cursive;
    font-style: italic;
}

.medicines {
    margin-top: 20px;
}

.date {
    text-align: right;
}

.button {
    text-align: left;
}

.footer {
    text-align: right;
    margin-top: 100px;
}

/* Styles for print view */
/* Styles for print view */
@media print {
    .button {
        display: none;
        /* Hide buttons in print view */
    }

    .footer {
        display: block;
        margin-top: 100px;

    }
}
</style>
<div class="card">
    <div class="prescription mt-5" id="prescriptionContent">
        <div class="header">
            <img src="/assets/img/favicon/login-rhu.png" alt="RHU Logo" class="logo-img" />
            <div class="header-text">
                <h1>MUNICIPAL HEALTH OFFICE</h1>
                <h2>BATO, LEYTE</h2>
            </div>
        </div>

        <!-- Date Section -->
        <div class="date">
            @if($medicines->isNotEmpty())
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($medicines->first()->created_at)->format('F j, Y') }}</p>
            @else
            <p><strong>Date:</strong> No medicines prescribed today.</p>
            @endif
        </div>

        <div class="details">
            <p><strong>Name:</strong> {{ $patient->first_name }} {{ $patient->middle_name }} {{ $patient->last_name }}
            </p>
            <p><strong>Age:</strong> {{ $patient->age }}</p>
            <p><strong>Barangay:</strong> {{ $patientWithAddress->barangay_name }}
                {{ $patientWithAddress->municipality_name }} {{ $patientWithAddress->province_name }}</p>
        </div>

        <div class="rx">Rx</div>

        <div class="medicines">
            <h3>Prescribed Medicines:</h3>
            <table cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th class="fw-bold">Medicine Name</th>
                        <th class="fw-bold">Medication Type</th>
                        <th class="fw-bold">Dosage</th>
                        <th class="fw-bold">Quantity</th>
                        <th class="fw-bold">Frequency</th>
                        <th class="fw-bold">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->medicines_name }}</td>
                        <td>{{ $medicine->medication_type }}</td>
                        <td>{{ $medicine->dosage }}</td>
                        <td>{{ $medicine->quantity }} Pcs</td>
                        <td>{{ $medicine->frequency }}</td>
                        <td>{{ $medicine->duration }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer mb-5">
            <p>PROBO P. QUIJANO, MD, MPH</p>
            <p>Lic. No. 0090266</p>
        </div>

        <div class="button mb-5">
            <a href="/doctor-patients" class="btn btn-primary"> <span class="bx bxs-chevron-left me-1"></span> Back</a>
            <button onclick="printPrescription()" class="btn btn-primary"> <span class="bx bxs-printer me-1"></span>
                Print</button>
        </div>


    </div>
</div>
@endsection