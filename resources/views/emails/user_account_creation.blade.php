<!DOCTYPE html>
<html>
<head>
    <title>YPI Account Creation</title>
</head>
<body>
   Dear {{ $details['name'] }},<br> 
 <p>A new user account has been created for you in the YPI System.<br>
You can now access the system using the following details:</p>

    <ul>
        <li>Username: {{ $details['email'] }}</li>
        <li>Password: {{ $details['password'] }}</li>
    </ul>

    <p>To get started, please log in to your account using the link below:</p>
    <p><a href="https://ypi.sc.qa">Log In to Your YPI System Account</a></p>
    <p>If you encounter any issues logging in or need assistance, please contact the YPI Support Team at ypisys@scqa0.onmicrosoft.com.</p>
    <p>Best regards,<br>YPI Support Team</p>
</body>
</html>
