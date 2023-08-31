<?php
require_once('database.php');

// Set up the database connection
$db = $conn; // Update with your database connection

// Initialize error messages and input values
$register = $valid = $fnameErr = $lnameErr = $emailErr = $passErr = $cpassErr = '';
$set_firstName = $set_lastName = $set_email = '';

// Extract POST data
extract($_POST);

if (isset($_POST['submit'])) {
    // Regular expressions for validation
    $validName = "/^[a-zA-Z ]*$/";
    $validEmail = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
    $uppercasePassword = "/(?=.*?[A-Z])/";
    $lowercasePassword = "/(?=.*?[a-z])/";
    $digitPassword = "/(?=.*?[0-9])/";
    $spacesPassword = "/^$|\s+/";
    $symbolPassword = "/(?=.*?[#?!@$%^&*-])/";
    $minEightPassword = "/.{8,}/";

    // First Name Validation
    if (empty($first_name)) {
        $fnameErr = "First Name is Required";
    } elseif (!preg_match($validName, $first_name)) {
        $fnameErr = "Digits are not allowed";
    } else {
        $fnameErr = true;
    }

    // Last Name Validation
    if (empty($last_name)) {
        $lnameErr = "Last Name is required";
    } elseif (!preg_match($validName, $last_name)) {
        $lnameErr = "Digit are not allowed";
    } else {
        $lnameErr = true;
    }

    // Email Address Validation
    if (empty($email)) {
        $emailErr = "Email is Required";
    } elseif (!preg_match($validEmail, $email)) {
        $emailErr = "Invalid Email Address";
    } else {
        $emailErr = true;
    }

    // Password validation
    if (empty($password)) {
        $passErr = "Password is Required";
    } elseif (
        !preg_match($uppercasePassword, $password) ||
        !preg_match($lowercasePassword, $password) ||
        !preg_match($digitPassword, $password) ||
        !preg_match($symbolPassword, $password) ||
        !preg_match($minEightPassword, $password) ||
        preg_match($spacesPassword, $password)
    ) {
        $passErr = "Password must meet certain criteria";
    } else {
        $passErr = true;
    }

    // Confirm password validation
    if ($cpassword != $password) {
        $cpassErr = "Confirm Password does not Match";
    } else {
        $cpassErr = true;
    }

    // Check if all fields are valid
    if ($fnameErr === true && $lnameErr === true && $emailErr === true && $passErr === true && $cpassErr === true) {
        $firstName = legal_input($first_name);
        $lastName = legal_input($last_name);
        $email = legal_input($email);
        $password = legal_input(md5($password));

        // Check unique email
        $checkEmail = unique_email($email);

        if ($checkEmail) {
            $register = $email . " is already exist";
        } else {
            // Insert data
            $register = register($firstName, $lastName, $email, $password);
        }
    } else {
        // Set input values to be displayed if the form is invalid
        $set_firstName = $first_name;
        $set_lastName = $last_name;
        $set_email = $email;
    }
}

// Convert illegal input value to legal value format
function legal_input($value)
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = htmlspecialchars($value);
    return $value;
}

// Check if the email is unique
function unique_email($email)
{
    global $db;
    $sql = "SELECT email FROM users WHERE email='" . $email . "'";
    $check = $db->query($sql);

    if ($check->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

// Insert user data into the database table
function register($firstName, $lastName, $email, $password)
{
    global $db;
    $sql = "INSERT INTO users(first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $query = $db->prepare($sql);
    $query->bind_param('ssss', $firstName, $lastName, $email, $password);
    $exec = $query->execute();

    if ($exec == true) {
        return "You are registered successfully";
    } else {
        return "Error: " . $sql . "<br>" . $db->error;
    }
}
?>
