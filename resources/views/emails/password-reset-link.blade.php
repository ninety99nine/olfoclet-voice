@component('mail::message')

<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ asset('images/logo-swirl.png') }}" alt="Telcoflo Logo" style="max-width: 150px; height: auto;" />
</div>

# Reset Your Password

We received a request to reset your password. Click the button below to set a new password.

**Email:** {{ $email }}

@component('mail::button', ['url' => $resetUrl, 'color' => 'primary'])
Reset Password
@endcomponent

If you did not request a password reset, please ignore this email.

Thanks,
<br>
Telcoflo Team

@endcomponent
