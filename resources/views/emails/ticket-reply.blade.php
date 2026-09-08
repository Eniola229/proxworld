<main style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px 20px; min-height: 100vh;">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-top: 40px;">
            <!-- Header -->
            <div style="background: #3730a3; padding: 30px; text-align: center;">
                <img src="{{ asset('assets/images/B.png') }}" alt="Logo" style="height: 50px; width: auto;">
            </div>
            <!-- Body -->
            <div style="padding: 40px 30px;">
                <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px 0; color: #1a1a2e;">Support Ticket Reply</h2>
                <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 24px 0; color: #444;">You have a new reply to your support ticket</h4>
                <p style="margin: 0 0 12px 0; color: #333; font-size: 14px;">Hello <strong>{{ $ticket->user->name }}</strong>,</p>
                <p style="margin: 0 0 24px 0; color: #333; font-size: 14px;">Our support team has responded to your ticket.</p>
                <!-- Ticket Info Box -->
                <div style="background: #f8f9fa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin: 0 0 24px 0;">
                    <div style="margin-bottom: 10px; font-size: 14px; color: #333;">
                        <strong>Ticket #:</strong> {{ $ticket->id }}
                    </div>
                    <div style="margin-bottom: 10px; font-size: 14px; color: #333;">
                        <strong>Subject:</strong> {{ $ticket->subject }}
                    </div>
                    <div style="font-size: 14px; color: #333;">
                        <strong>Status:</strong>
                        <span style="display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #fff;
                            background-color: 
                            @if($ticket->status === 'open') #f59e0b
                            @elseif($ticket->status === 'in_progress') #0284c7
                            @elseif($ticket->status === 'closed') #22c55e
                            @else #6b7280
                            @endif;">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                <!-- Admin Reply Box -->
                <div style="background: #e0e0f8; border: 1px solid #a5a5e0; border-radius: 8px; padding: 20px; margin: 0 0 24px 0;">
                    <h5 style="font-size: 14px; font-weight: 700; margin: 0 0 12px 0; color: #1a1a2e;">Support Team Reply:</h5>
                    <p style="font-size: 14px; color: #333; margin: 0; white-space: pre-wrap;">{{ $ticketMessage->message }}</p>
                </div>
                <!-- Button -->
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ route('support.show', $ticket->id) }}" style="display: inline-block; background-color: #3730a3; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600; width: 100%; box-sizing: border-box; text-align: center;">
                        View Full Ticket & Reply
                    </a>
                </div>
                <!-- Footer Note -->
                <p style="font-size: 13px; color: #888; margin: 0;">If you have any questions, please reply to this ticket or contact our support team.</p>
            </div>
            <!-- Footer -->
            <div style="background: #f4f4f4; padding: 20px 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                <p style="font-size: 12px; color: #aaa; margin: 0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</main>