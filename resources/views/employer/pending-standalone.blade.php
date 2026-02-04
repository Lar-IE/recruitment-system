<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Employer Approval Pending</title>
        <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">
        <style>
            body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; }
            .container { max-width: 720px; margin: 80px auto; background: #ffffff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
            .content { padding: 32px; color: #111827; }
            h1 { font-size: 22px; margin: 0 0 12px; }
            p { margin: 0 0 12px; color: #4b5563; font-size: 14px; }
            form { margin-top: 20px; }
            button { background: #ef4444; color: #ffffff; border: 0; padding: 10px 16px; border-radius: 8px; font-size: 14px; cursor: pointer; }
            button:hover { background: #dc2626; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <h1>Employer Approval Pending</h1>
                <p>Thanks for registering! Your employer account is pending approval.</p>
                <p>Need help? Contact support.</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
            </div>
        </div>
    </body>
</html>
