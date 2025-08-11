@component('mail::message')
# Verify Your Email  

Hello {{ $userName }},  

You recently requested to verify your email address. Please use the following One-Time Password (OTP) to complete the process:  

@component('mail::panel')
<div style="font-size: 28px; font-weight: bold; text-align: center; letter-spacing: 4px; color: #333;">
    {{ $otp }}
</div>
@endcomponent  

This OTP will expire in **10 minutes**.  

If you did not request this, you can safely ignore this email.  

Thanks,  
**The WRI Team**  

@endcomponent