<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vehicle Registration Renewal</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background: linear-gradient(to right, #0066ff, #33ccff);
      font-family: 'Arial', sans-serif;
    }
    .container {
      margin-top: 50px;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      background: #fff;
    }
    .btn-primary {
      background-color: #0066ff;
      border: none;
    }
    .btn-primary:hover {
      background-color: #004bb5;
    }
    .alert {
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Vehicle Registration Renewal</h2>
    <div class="card p-4 shadow">
      <form id="renewalForm" method="POST">
        <div class="mb-3">
          <label for="vehicleType" class="form-label">Vehicle Type</label>
          <select class="form-select" id="vehicleType" name="vehicleType" required>
            <option value="">Select Vehicle Type</option>
            <option value="Car">Car</option>
            <option value="Motorcycle">Motorcycle</option>
            <option value="Truck">Truck</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="plateNumber" class="form-label">Plate Number</label>
          <input 
            type="text" 
            class="form-control" 
            id="plateNumber" 
            name="plateNumber" 
            placeholder="Select a vehicle type to see format" 
            required>
          <small class="text-muted">Format depends on vehicle type.</small>
        </div>
        <div class="mb-3">
          <label for="setCertificate" class="form-label">Smoke Emission Test Certificate</label>
          <div class="input-group">
            <span class="input-group-text">SET No.</span>
            <input
              type="text"
              class="form-control"
              id="setCertificate"
              name="setCertificate"
              placeholder="00-123-456"
              maxlength="10" 
              required
            />
          </div>
        </div>
        <div class="mb-3">
          <label for="tplCertificate" class="form-label">Third Party Liability Insurance Certificate</label>
          <div class="input-group">
            <span class="input-group-text">TPL No.</span>
            <input
              type="text"
              class="form-control"
              id="tplCertificate"
              name="tplCertificate"
              placeholder="23-001-2345-67890"
              maxlength="17"
              required
            />
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Check Renewal Status</button>
      </form>
      <div id="output" class="mt-4"></div>
    </div>
  </div>

  <script>
  // Automatically format input with dashes
  function formatWithDashes(input, pattern) {
    const value = input.value.replace(/\D/g, ''); // Remove all non-numeric characters
    let formatted = '';
    let index = 0;

    for (const char of pattern) {
      if (index >= value.length) break;
      if (char === '-') {
        formatted += '-';
      } else {
        formatted += value[index++];
      }
    }
    input.value = formatted;
  }

  // Attach event listeners to SET and TPL fields for formatting
  document.getElementById('setCertificate').addEventListener('input', function () {
    formatWithDashes(this, '00-123-456'); // SET format
  });

  document.getElementById('tplCertificate').addEventListener('input', function () {
    formatWithDashes(this, '23-001-2345-67890'); // TPL format
  });

  // Update placeholder and help text based on vehicle type
  document.getElementById('vehicleType').addEventListener('change', function () {
    const plateField = document.getElementById('plateNumber');
    const vehicleType = this.value;
    let placeholder = '';
    let helpText = '';

    if (vehicleType === 'Car') {
      placeholder = 'e.g., ABC-1234';
      helpText = 'Format: ABC-1234 or ABC 1234 (Philippine car plate format).';
    } else if (vehicleType === 'Motorcycle') {
      placeholder = 'e.g., AB 12345';
      helpText = 'Format: AB 12345 or AB-12345 (Philippine motorcycle plate format).';
    } else if (vehicleType === 'Truck') {
      placeholder = 'e.g., ABC-1234';
      helpText = 'Format: ABC-1234 or ABC 1234 (Philippine truck plate format).';
    }

    plateField.placeholder = placeholder;
    document.querySelector('.text-muted').innerText = helpText;
  });

  // Handle form submission
  document.getElementById('renewalForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Gather inputs
    const vehicleType = document.getElementById('vehicleType').value.trim();
    const plateNumber = document.getElementById('plateNumber').value.trim();
    const setCertificate = document.getElementById('setCertificate').value.trim();
    const tplCertificate = document.getElementById('tplCertificate').value.trim();

    // Validate plate number based on vehicle type
    let plateRegex;
    if (vehicleType === 'Car') {
      plateRegex = /^[A-Z]{3}[-\s]?\d{4}$/; // Example: ABC-1234 or ABC 1234
    } else if (vehicleType === 'Motorcycle') {
      plateRegex = /^[A-Z]{2}[-\s]?\d{5}$/; // Example: AB-12345 or AB 12345
    } else if (vehicleType === 'Truck') {
      plateRegex = /^[A-Z]{3}[-\s]?\d{4}$/; // Example: ABC-1234 or ABC 1234
    } else {
      showOutput('Please select a valid vehicle type.', 'danger');
      return;
    }

    if (!plateRegex.test(plateNumber)) {
      showOutput(`Invalid plate number format for a ${vehicleType}.`, 'danger');
      return;
    }

    // Determine renewal schedule month and week based on plate number
    const lastDigit = parseInt(plateNumber.replace(/\D/g, '').slice(-1), 10); // Extract last digit
    const months = {
      1: 'January', 2: 'February', 3: 'March', 4: 'April', 5: 'May',
      6: 'June', 7: 'July', 8: 'August', 9: 'September', 0: 'October'
    };
    const weeks = {
      1: 'First Week', 2: 'First Week', 3: 'First Week',
      4: 'Second Week', 5: 'Second Week', 6: 'Second Week',
      7: 'Third Week', 8: 'Third Week',
      9: 'Fourth Week', 0: 'Fourth Week'
    };

    const renewalMonth = months[lastDigit];
    const secondToLastDigit = parseInt(plateNumber.replace(/\D/g, '').slice(-2, -1), 10); // Extract second-to-last digit
    const renewalWeek = weeks[secondToLastDigit] || 'Fourth Week';

    // Generate result
    const result = `Your ${vehicleType} with Plate No. ${plateNumber} is QUALIFIED for RENEWAL on ${renewalMonth} 2024 during ${renewalWeek}.`;
    showOutput(result, 'success');
  });

  // Helper function to display messages
  function showOutput(message, type) {
    const output = document.getElementById('output');
    output.innerHTML = `<div class="alert alert-${type}" role="alert">${message}</div>`;
  }
</script>


  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicleType = trim($_POST['vehicleType']);
    $plateNumber = trim($_POST['plateNumber']);
    $setCertificate = trim($_POST['setCertificate']);
    $tplCertificate = trim($_POST['tplCertificate']);

    // Validate plate number format
    $isValid = false;
    if ($vehicleType === 'Car' && preg_match('/^[A-Z]{3} \d{4}$/', $plateNumber)) {
      $isValid = true;
    } elseif ($vehicleType === 'Motorcycle' && preg_match('/^\d{4} [A-Z]{2}$/', $plateNumber)) {
      $isValid = true;
    } elseif ($vehicleType === 'Truck' && preg_match('/^[A-Z]{2} \d{4}$/', $plateNumber)) {
      $isValid = true;
    }

    if (!$isValid) {
      echo "Invalid plate number format for a $vehicleType.";
      exit;
    }

    // Determine renewal schedule based on last digit of plate number
    $lastDigit = (int)preg_replace('/\D/', '', substr($plateNumber, -1));
    $months = [
      1 => 'January', 2 => 'February', 3 => 'March',
      4 => 'April', 5 => 'May', 6 => 'June',
      7 => 'July', 8 => 'August', 9 => 'September', 0 => 'October',
    ];
    $weeks = [
      1 => 'First Week', 2 => 'Second Week', 3 => 'Third Week',
      4 => 'Fourth Week',
    ];

    $renewalMonth = $months[$lastDigit];
    $weekNumber = ceil($lastDigit / 2.5); // Better grouping logic for weeks
    $renewalWeek = $weeks[$weekNumber];

    // Response
    echo "Your $vehicleType with Plate No. $plateNumber is QUALIFIED for RENEWAL on $renewalMonth 2024 until $renewalWeek.";
  }
  ?>
</body>
</html>
