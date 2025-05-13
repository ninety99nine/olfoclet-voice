@component('mail::message')

<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ asset('images/logo-swirl.png') }}" alt="Telcoflo Logo" style="max-width: 150px; height: auto;" />
</div>

# Set Up Your Account

Your account has been created successfully. @if($organizationName)You have been added to the {{ $organizationName }} organization. @endif Please set up your password to log in.

**Email:** {{ $email }}

@component('mail::button', ['url' => $setupUrl, 'color' => 'primary'])
Set Up Your Password
@endcomponent

If you have any questions, feel free to contact our support team.

Thanks,
<br>
Telcoflo Team

@endcomponent
