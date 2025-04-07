@php
use Illuminate\Support\Facades\Route;
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>New Account Pending Approval</title>
</head>
<body>
    <h2>New Account Pending Approval</h2>
    
    <p>A new account registration requires your approval.</p>
    
    <p><strong>Name:</strong> {{ $pendingAccount->name }}<br>
    <strong>Email:</strong> {{ $pendingAccount->email }}</p>
    
    <p>
        <a href="{{ route('admin.pending-accounts') }}" style="background-color: #4CAF50; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            Review Account
        </a>
    </p>
    
    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html> 