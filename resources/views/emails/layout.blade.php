<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<title>@yield('title', config('app.name'))</title>
<!--[if mso]>
<noscript>
<xml>
<o:OfficeDocumentSettings>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
</noscript>
<style>
    table { border-collapse: collapse; }
</style>
<![endif]-->
<style>
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
    body { margin: 0; padding: 0; width: 100% !important; background-color: #eef1f6; }
    a { color: #2563eb; }
    @media screen and (max-width: 600px) {
        .email-container { width: 100% !important; }
        .email-padding { padding-left: 24px !important; padding-right: 24px !important; }
    }
</style>
</head>
<body style="margin:0; padding:0; background-color:#eef1f6;">

{{-- Preheader: hidden preview text shown next to the subject line in the inbox --}}
<div style="display:none; max-height:0; overflow:hidden; mso-hide:all; font-size:1px; line-height:1px; color:#eef1f6;">
    @yield('preheader', '')
    &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#eef1f6;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            {{-- Card: header + body --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" class="email-container" style="width:600px; max-width:600px;">
                <tr>
                    <td align="center" bgcolor="#2563eb" style="background-color:#2563eb; padding:28px 32px; border-radius:10px 10px 0 0;">
                        <img src="{{ asset('assets/images/LOGO.png') }}" alt="{{ config('app.name') }}" height="36" style="display:block; height:36px; width:auto;">
                    </td>
                </tr>
                <tr>
                    <td class="email-padding" bgcolor="#ffffff" style="background-color:#ffffff; padding:40px 32px; border-radius:0 0 10px 10px; font-family:Arial,Helvetica,sans-serif; color:#1f2937; font-size:15px; line-height:1.6;">
                        @yield('content')
                    </td>
                </tr>
            </table>

            {{-- Footer --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" class="email-container" style="width:600px; max-width:600px;">
                <tr>
                    <td align="center" style="padding:24px 16px 0; font-family:Arial,Helvetica,sans-serif; font-size:12px; color:#9ca3af; line-height:1.6;">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        @hasSection('footer_extra')
                            <br>@yield('footer_extra')
                        @endif
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
