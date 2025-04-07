<!DOCTYPE html>
<html>
<head>
    <title>Account Approved</title>
</head>
<body>
    <h2>Your Account Has Been Approved</h2>
    
    <p>Congratulations! Your account has been approved and is now active.</p>
    
    <p><strong>Name:</strong> {{ $user->name }}<br>
    <strong>Email:</strong> {{ $user->email }}</p>
    
    <p>
        <a href="{{ route('login') }}" style="background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            Login Now
        </a>
    </p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html> 