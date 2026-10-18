<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    /** /proxies/{residential|isp|datacenter|mobile} */
    public function product(string $type)
    {
        $products = config('products');
        abort_unless(isset($products[$type]), 404);

        return view('pages.product', [
            'slug' => $type,
            'p'    => $products[$type],
        ]);
    }

    /** /pricing */
    public function pricing()
    {
        $products = config('products');

        // To show live prices, overwrite $products[$slug]['from'] here
        // (from your DB or currency helper) before passing it to the view.

        return view('pages.pricing', ['products' => $products]);
    }

    /** POST /contact */
    public function contactSend(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'required|string|max:150',
            'message' => 'required|string|min:10|max:3000',
            'website' => 'nullable|string', // honeypot, real people leave it empty
        ]);

        // Bots fill the hidden field. Pretend it worked and send nothing.
        if (!empty($data['website'])) {
            return back()->with('contact_sent', true);
        }

        $to = 'info@proxworld.shop';

        try {
            Mail::raw(
                "From: {$data['name']} <{$data['email']}>\n\n{$data['message']}",
                function ($mail) use ($to, $data) {
                    $mail->to($to)
                        ->replyTo($data['email'], $data['name'])
                        ->subject('[ProxWorld contact] ' . $data['subject']);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('contact_failed', true);
        }

        return back()->with('contact_sent', true);
    }
}
