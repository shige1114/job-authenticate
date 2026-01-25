<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Email Verification</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; margin: 0; }
        .container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
        h1 { color: #333; margin-bottom: 1.5rem; }
        p { color: #555; margin-bottom: 1rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        input[type="email"] { padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        button { background-color: #007bff; color: white; padding: 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; transition: background-color 0.2s ease; }
        button:hover { background-color: #0056b3; }
        .message-box { margin-top: 1rem; padding: 0.8rem; border-radius: 4px; }
        .message-box.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .message-box.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p>Please enter your email address to receive a verification link.</p>
        <form id="emailVerificationRequestForm">
            @csrf {{-- Laravel CSRF token --}}
            <input type="email" name="email" placeholder="Your Email Address" required>
            <button type="submit">Send Verification Link</button>
        </form>
        <div id="message" class="message-box" style="display: none;"></div>
    </div>

    <script>
        document.getElementById('emailVerificationRequestForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const email = formData.get('email');
            const csrfToken = formData.get('_token');
            const messageBox = document.getElementById('message');

            messageBox.style.display = 'none';
            messageBox.className = 'message-box';
            messageBox.textContent = '';

            try {
                const response = await fetch('/api/v1/request-email-verification', { // Corrected API endpoint
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers for API
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();

                if (response.ok) {
                    messageBox.classList.add('success');
                    messageBox.textContent = data.message || 'Verification email sent. Redirecting...';
                    messageBox.style.display = 'block';
                    // Redirect to the verify email page after a short delay
                    setTimeout(() => {
                        let redirectUrl = '/email/verify';
                        if (data.token) { // Check if the token is present in the response
                            redirectUrl = `/email/verify?token=${data.token}`;
                        }
                        window.location.href = redirectUrl;
                    }, 2000);
                } else {
                    messageBox.classList.add('error');
                    messageBox.textContent = data.message || 'An error occurred.';
                    messageBox.style.display = 'block';
                    console.error('API Error:', data);
                }
            } catch (error) {
                messageBox.classList.add('error');
                messageBox.textContent = 'Network error or unexpected issue.';
                messageBox.style.display = 'block';
                console.error('Fetch Error:', error);
            }
        });
    </script>
</body>
</html>
