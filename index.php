<?php
// Variable to hold messages/errors and form visibility flags
$message = '';
$displayForm2 = false;
$displayResult = false;
$student = [];
$grades = [];

// Function to calculate average and determine if passed
function calculateAverage($prelim, $midterm, $final) {
    $average = ($prelim + $midterm + $final) / 3;
    $status = $average >= 75 ? 'Passed' : 'Failed';
    return [number_format($average, 2), $status];
}

// Handle the submission of student information
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'enrollment') {
    // Get form data and validate email format
    $student['firstName'] = htmlspecialchars($_POST['first_name']);
    $student['lastName'] = htmlspecialchars($_POST['last_name']);
    $student['age'] = intval($_POST['age']);
    $student['gender'] = htmlspecialchars($_POST['gender']);
    $student['course'] = htmlspecialchars($_POST['course']);
    $student['email'] = htmlspecialchars($_POST['email']);

    if (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
        $message = 'Invalid email format. Please use a valid email (e.g., user@email.com).';
    } else {
        $displayForm2 = true; // Show the grade entry form
    }
}

// Handle the submission of grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'grades') {
    // Retrieve student data from hidden fields
    $student['firstName'] = htmlspecialchars($_POST['first_name']);
    $student['lastName'] = htmlspecialchars($_POST['last_name']);
    $student['age'] = intval($_POST['age']);
    $student['gender'] = htmlspecialchars($_POST['gender']);
    $student['course'] = htmlspecialchars($_POST['course']);
    $student['email'] = htmlspecialchars($_POST['email']);

    // Get grade data
    $grades['prelim'] = intval($_POST['prelim']);
    $grades['midterm'] = intval($_POST['midterm']);
    $grades['final'] = intval($_POST['final']);

    // Calculate average and determine status
    list($grades['average'], $grades['status']) = calculateAverage($grades['prelim'], $grades['midterm'], $grades['final']);

    $displayResult = true; // Display results below the enrollment form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment and Grade Processing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="text-center">Student Enrollment and Grade Processing System</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>
    
    <?php if (!$displayForm2): ?>
        <!-- Student Enrollment Form -->
        <form method="post">
            <h4>Student Enrollment Form</h4>
            <input type="hidden" name="form_type" value="enrollment">
            <div class="mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" class="form-control" id="age" name="age" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label><br>
                <input type="radio" id="male" name="gender" value="Male" checked required> Male
                <input type="radio" id="female" name="gender" value="Female"> Female
            </div>
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <select class="form-select" id="course" name="course" required>
                    <option value="BSIT">BSIT</option>
                    <option value="BSBA">BSBA</option>
                    <option value="BSCRIM">BSCRIM</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Student Information</button>
        </form>
    <?php endif; ?>
    
    <?php if ($displayForm2): ?>
        <!-- Grade Entry Form -->
        <form method="post" class="mt-4">
            <h4>Enter Grades for <?= htmlspecialchars($student['firstName']) . " " . htmlspecialchars($student['lastName']) ?></h4>
            <input type="hidden" name="form_type" value="grades">
            <!-- Pass student data through hidden fields -->
            <input type="hidden" name="first_name" value="<?= htmlspecialchars($student['firstName']) ?>">
            <input type="hidden" name="last_name" value="<?= htmlspecialchars($student['lastName']) ?>">
            <input type="hidden" name="age" value="<?= htmlspecialchars($student['age']) ?>">
            <input type="hidden" name="gender" value="<?= htmlspecialchars($student['gender']) ?>">
            <input type="hidden" name="course" value="<?= htmlspecialchars($student['course']) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($student['email']) ?>">

            <div class="mb-3">
                <label for="prelim" class="form-label">Prelim</label>
                <input type="number" class="form-control" id="prelim" name="prelim" required>
            </div>
            <div class="mb-3">
                <label for="midterm" class="form-label">Midterm</label>
                <input type="number" class="form-control" id="midterm" name="midterm" required>
            </div>
            <div class="mb-3">
                <label for="final" class="form-label">Final</label>
                <input type="number" class="form-control" id="final" name="final" required>
            </div>
            <button type="submit" class="btn btn-success">Submit Grades</button>
        </form>
    <?php endif; ?>
    
    <?php if ($displayResult): ?>
        <!-- Result Display -->
        <div class="mt-4">
            <h4>Student Details</h4>
            <p><strong>First name:</strong> <?= htmlspecialchars($student['firstName']) ?></p>
            <p><strong>Last name:</strong> <?= htmlspecialchars($student['lastName']) ?></p>
            <p><strong>Age:</strong> <?= htmlspecialchars($student['age']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($student['gender']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($student['email']) ?></p>
            
            <h4>Grades</h4>
            <p><strong>Prelim:</strong> <?= htmlspecialchars($grades['prelim']) ?></p>
            <p><strong>Midterm:</strong> <?= htmlspecialchars($grades['midterm']) ?></p>
            <p><strong>Final:</strong> <?= htmlspecialchars($grades['final']) ?></p>
            
            <h4>Average Grade</h4>
            <p>
                <?= htmlspecialchars($grades['average']) ?> - 
                <strong class="<?= $grades['status'] === 'Passed' ? 'text-success' : 'text-danger' ?>">
                    <?= htmlspecialchars($grades['status']) ?>
                </strong>
            </p>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>