<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; margin: 0; }
        .container { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
        h1 { color: #333; margin-bottom: 1.5rem; }
        p { color: #555; margin-bottom: 1rem; }
        form { display: flex; flex-direction: column; gap: 1rem; }
        input[type="text"] { padding: 0.8rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        button { background-color: #28a745; color: white; padding: 0.8rem; border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; transition: background-color 0.2s ease; }
        button:hover { background-color: #218838; }
        .message-box { margin-top: 1rem; padding: 0.8rem; border-radius: 4px; }
        .message-box.success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .message-box.error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify Your Email</h1>
        <p>Please enter the verification code you received.</p>
        <form id="verifyEmailForm">
            @csrf {{-- Laravel CSRF token --}}
            <input type="text" name="code" placeholder="Verification Code" required>
            <button type="submit">Verify Email</button>
        </form>
        <div id="message" class="message-box" style="display: none;"></div>
    </div>

    <script>
        // Function to get a cookie value
        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for(let i=0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Function to set a cookie
        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }

        let verificationToken = null;

        // On page load, try to get token from URL or cookie
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tokenFromUrl = urlParams.get('token');

            if (tokenFromUrl) {
                verificationToken = tokenFromUrl;
                setCookie('email_verification_token', tokenFromUrl, 1); // Store for 1 day
                console.log('Token from URL:', verificationToken);
            } else {
                verificationToken = getCookie('email_verification_token');
                if (verificationToken) {
                    console.log('Token from Cookie:', verificationToken);
                } else {
                    console.warn('No verification token found in URL or cookie.');
                    // Optionally, redirect to request verification page or show an error
                }
            }
        });

        document.getElementById('verifyEmailForm').addEventListener('submit', async function(event) {
            event.preventDefault(); // Prevent default form submission

            const form = event.target;
            const formData = new FormData(form);
            const code = formData.get('code');
            const csrfToken = formData.get('_token');
            const messageBox = document.getElementById('message');

            messageBox.style.display = 'none';
            messageBox.className = 'message-box';
            messageBox.textContent = '';

            if (!verificationToken) {
                messageBox.classList.add('error');
                messageBox.textContent = 'Verification token is missing. Please request a new verification email.';
                messageBox.style.display = 'block';
                return;
            }

            try {
                const response = await fetch('/api/v1/email-verification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Email-Verification-Token': verificationToken // Token in custom header
                    },
                    body: JSON.stringify({ code: code })
                });

                const data = await response.json();

                if (response.ok) {
                    messageBox.classList.add('success');
                    messageBox.textContent = data.message || 'Email verified successfully!';
                    messageBox.style.display = 'block';
                    // Clear the token cookie after successful verification
                    setCookie('email_verification_token', '', -1); // Expire the cookie
                } else {
                    messageBox.classList.add('error');
                    messageBox.textContent = data.message || 'Email verification failed.';
                    messageBox.style.display = 'block';
                    console.error('API Error:', data);
                }
            } catch (error) {
                messageBox.classList.add('error');
                messageBox.textContent = 'Network error or unexpected issue during verification.';
                messageBox.style.display = 'block';
                console.error('Fetch Error:', error);
            }
        });
    </script>
</body>
</html>
