<?php
date_default_timezone_set('Asia/Karachi');
$today = date('l, F j, Y');
$time  = date('h:i A');
require 'config.php';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $f_name = $_POST['f_name'] ?? null;
    $l_name = $_POST['l_name'] ?? null;
    $dob = $_POST['dob'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $user_name = $_POST['user_name'] ?? null;
    $password = $_POST['password'] ?? null;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    try{
      $sql = "INSERT INTO users (f_name, l_name, dob, gender, email, phone, user_name, password)
       VALUES (:f_name, :l_name, :dob, :gender, :email, :phone, :user_name, :password)";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':f_name' => $f_name, ':l_name' => $l_name, ':dob' => $dob, ':gender' => $gender, 
      ':email' => $email, ':phone' => $phone, ':user_name' => $user_name, ':password' => $hashed_password]);
      echo "Account created successfully.";
    }
    catch(PDOException $e){
      if ($e->getCode() == 23000) {
        echo "Error: The email or username is already in use.";
      } else {
        echo "Error: " . $e->getMessage();
      }
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Style Multi-Step Signup</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #0f1626;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }
        .signup-container {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 40px;
            width: 450px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        h2 {
            font-weight: 500;
            margin-bottom: 10px;
            font-size: 24px;
            color: #fff;
        }
        p.subtitle {
            color: #aaa;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .input-group {
            margin-bottom: 22px;
            position: relative;
        }
        label {
            display: block;
            font-size: 13px;
            color: #3b82f6;
            margin-bottom: 8px;
            font-weight: 500;
        }
        input, select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
        }
        input::placeholder {
            color: #666;
        }
        select option {
            background: #151f32;
            color: #fff;
        }
        .radio-card-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .radio-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 16px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        .radio-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
        }
        .radio-card input[type="radio"] {
            width: auto;
            cursor: pointer;
        }
        .radio-card span {
            font-size: 15px;
            color: #fff;
        }
        .input-group.error input,
        .input-group.error select,
        .radio-card-group.error .radio-card {
            border-color: #ef4444 !important;
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.3);
        }
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        .input-group.error .error-message,
        .radio-card-group.error .error-message {
            display: block;
        }
        .global-error-banner {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: none;
        }
        .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
        }
        button {
            padding: 11px 24px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
        }
        button.btn-next, button.btn-submit {
            background: #3b82f6;
            color: white;
        }
        button.btn-next:hover, button.btn-submit:hover {
            background: #2563eb;
            box-shadow: 0 0 12px rgba(37, 99, 235, 0.5);
        }
        button.btn-back {
            background: transparent;
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        button.btn-back:hover {
            background: rgba(59, 130, 246, 0.08);
        }
    </style>
</head>
<body>
<div class="signup-container">
    <div id="globalError" class="global-error-banner"></div> 
    <form id="multiStepForm">
        <div class="form-step active" id="step1">
            <h2>Create Account</h2>
            <p class="subtitle">Choose who you are managing this account for.</p>
            
            <div class="radio-card-group" id="accountTypeGroup">
                <label class="radio-card">
                    <input type="radio" name="account_target" value="myself">
                    <span>For myself</span>
                </label>
                <label class="radio-card">
                    <input type="radio" name="account_target" value="kid">
                    <span>For my kid</span>
                </label>
                <div class="error-message">Selection required</div>
            </div>
            <div class="button-group">
                <button type="button" class="btn-next" onclick="nextStep(1)">Next</button>
            </div>
        </div>
        <div class="form-step" id="step2">
            <h2>Basic Information</h2>
            <p class="subtitle">Enter your name and date of birth.</p>
            <div class="input-group">
                <label for="f_name">First Name</label>
                <input type="text" id="f_name" placeholder="Enter first name">
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="l_name">Last Name</label>
                <input type="text" id="l_name" placeholder="Enter last name">
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob">
                <div class="error-message">Field required</div>
            </div>
            <div class="button-group">
                <button type="button" class="btn-back" onclick="prevStep(2)">Back</button>
                <button type="button" class="btn-next" onclick="nextStep(2)">Next</button>
            </div>
        </div>
        <div class="form-step" id="step3">
            <h2>Account Details</h2>
            <p class="subtitle">Complete your profile credentials to finalize registration.</p>
            <div class="input-group">
                <label for="gender">Gender</label>
                <select id="gender">
                    <option value="" disabled selected>Select your gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" placeholder="name@example.com">
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" placeholder="Enter phone number">
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="user_name">Username</label>
                <input type="text" id="user_name" placeholder="Choose a unique username">
                <div class="error-message">Field required</div>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Create strong password">
                <div class="error-message">Field required</div>
            </div>
            <div class="button-group">
                <button type="button" class="btn-back" onclick="prevStep(3)">Back</button>
                <button type="submit" class="btn-submit">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
    function prevStep(currentStep) {
        document.getElementById('globalError').style.display = 'none';
        document.getElementById('step' + currentStep).classList.remove('active');
        document.getElementById('step' + (currentStep - 1)).classList.add('active');
    }
    function nextStep(currentStep) {
        let isValid = true;
        document.getElementById('globalError').style.display = 'none';
        if (currentStep === 1) {
            const selection = document.querySelector('input[name="account_target"]:checked');
            const targetGroup = document.getElementById('accountTypeGroup');
            if (!selection) {
                targetGroup.classList.add('error');
                isValid = false;
            } else {
                targetGroup.classList.remove('error');
            }
        } 
        else if (currentStep === 2) {
            const fields = ['f_name', 'l_name', 'dob'];
            fields.forEach(id => {
                const input = document.getElementById(id);
                if (!input.value.trim()) {
                    input.parentElement.classList.add('error');
                    isValid = false;
                } else {
                    input.parentElement.classList.remove('error');
                }
            });
            if (isValid) {
                const dobValue = document.getElementById('dob').value;
                const birthDate = new Date(dobValue);
                const today = new Date();
                
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                const accountTarget = document.querySelector('input[name="account_target"]:checked').value;
                const errorBanner = document.getElementById('globalError');

                if (accountTarget === 'myself' && age < 16) {
                    errorBanner.innerText = "Account registration error: You must be 16 or older to create an account for yourself.";
                    errorBanner.style.display = 'block';
                    isValid = false;
                } else if (accountTarget === 'kid' && age >= 16) {
                    errorBanner.innerText = "Account registration error: A child's profile must be set to a birthday under 16 years old.";
                    errorBanner.style.display = 'block';
                    isValid = false;
                }
            }
        }
        if (isValid) {
            document.getElementById('step' + currentStep).classList.remove('active');
            document.getElementById('step' + (currentStep + 1)).classList.add('active');
        }
    }
    document.getElementById('multiStepForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        const fields = ['gender', 'email', 'phone', 'user_name', 'password'];
        fields.forEach(id => {
            const input = document.getElementById(id);
            if (!input.value.trim()) {
                input.parentElement.classList.add('error');
                isValid = false;
            } else {
                input.parentElement.classList.remove('error');
            }
        });
        if (!isValid) return;
        let formdata = new FormData();
        formdata.append('f_name', document.getElementById('f_name').value);
        formdata.append('l_name', document.getElementById('l_name').value);
        formdata.append('dob', document.getElementById('dob').value);
        formdata.append('gender', document.getElementById('gender').value);
        formdata.append('email', document.getElementById('email').value);
        formdata.append('phone', document.getElementById('phone').value);
        formdata.append('user_name', document.getElementById('user_name').value);
        formdata.append('password', document.getElementById('password').value);
        fetch('login.html.php', {
            method: 'POST',
            body: formdata
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
</body>
</html>