{{-- Reusable CTA button. Usage: @include('emails.components.button', ['url' => $url, 'label' => 'Click me']) --}}
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:28px 0 4px;">
    <tr>
        <td align="center" bgcolor="#2563eb" style="border-radius:6px;">
            <a href="{{ $url }}"
               style="display:inline-block;padding:14px 32px;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1;font-weight:700;color:#ffffff;text-decoration:none;border-radius:6px;">
                {{ $label }}
            </a>
        </td>
    </tr>
</table>
