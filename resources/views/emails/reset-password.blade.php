@include('components.g-header')
<main style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px 20px; min-height: 100vh;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-top: 40px;">
            
            <!-- Header -->
            <div style="background: #3730a3; padding: 30px; text-align: center;">
                <img src="{{ asset('assets/images/B.png') }}" alt="Logo" style="height: 50px; width: auto;">
            </div>
            <!-- Body -->
            <div style="padding: 40px 30px;">
                <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px 0; color: #1a1a2e;">Reset Password</h2>
                <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 24px 0; color: #444;">You requested a password reset</h4>
                <p style="margin: 0 0 16px 0; color: #333; font-size: 14px;">Hello,</p>
                <p style="margin: 0 0 24px 0; color: #333; font-size: 14px;">You are receiving this email because we received a password reset request for your account.</p>
                <!-- Button -->
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ $url }}" style="display: inline-block; background-color: #3730a3; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; width: 100%; box-sizing: border-box; text-align: center;">
                        Reset Password
                    </a>
                </div>
                <!-- Expiry Notice -->
                <div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin: 24px 0;">
                    <p style="font-size: 13px; margin: 0; color: #555;">
                        <strong>This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.</strong>
                    </p>
                </div>
                <!-- Footer Note -->
                <div style="margin-top: 24px;">
                    <p style="font-size: 13px; color: #888; margin: 0 0 10px 0;">If you did not request a password reset, no further action is required.</p>
                    <p style="font-size: 12px; color: #aaa; margin: 0 0 6px 0;">If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                    <p style="font-size: 12px; word-break: break-all; margin: 0;">
                        <a href="{{ $url }}" style="color: #3730a3;">{{ $url }}</a>
                    </p>
                </div>
            </div>
            <!-- Footer -->
            <div style="background: #f4f4f4; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                <p style="font-size: 12px; color: #aaa; margin: 0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</main>