<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; color: #1a1a1a; background: #f8f9fa; margin: 0; padding: 0; }
    .wrapper { max-width: 600px; margin: 2rem auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
    .header { background: #0a1f1a; padding: 2rem; text-align: center; }
    .header h1 { color: #00ff88; margin: 0; font-size: 1.5rem; }
    .body { padding: 2rem; }
    .field { margin-bottom: 1.25rem; }
    .label { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #888; margin-bottom: 0.25rem; }
    .value { font-size: 1rem; color: #1a1a1a; }
    .message-box { background: #f8f9fa; border-left: 4px solid #00ff88; padding: 1rem; border-radius: 4px; white-space: pre-wrap; }
    .footer { background: #f0f0f0; padding: 1rem 2rem; font-size: 0.8rem; color: #888; text-align: center; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <h1>New Contact Form Submission</h1>
    </div>
    <div class="body">
      <div class="field">
        <div class="label">Name</div>
        <div class="value">{{ $formData['name'] }}</div>
      </div>
      <div class="field">
        <div class="label">Email</div>
        <div class="value">{{ $formData['email'] }}</div>
      </div>
      @if(!empty($formData['phone']))
      <div class="field">
        <div class="label">Phone</div>
        <div class="value">{{ $formData['phone'] }}</div>
      </div>
      @endif
      <div class="field">
        <div class="label">Service</div>
        <div class="value">{{ ucwords(str_replace('-', ' ', $formData['service'])) }}</div>
      </div>
      <div class="field">
        <div class="label">Message</div>
        <div class="value message-box">{{ $formData['message'] }}</div>
      </div>
    </div>
    <div class="footer">Sent from the contact form at techne-fixer.com</div>
  </div>
</body>
</html>