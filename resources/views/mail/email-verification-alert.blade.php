<h3>Dear <b>{{ ucfirst($user->first_name) }}</b>,</h3>

<h4>Great news! Your email has been successfully verified and account is now awaiting admin approval. 🎉</h4>

<h4>What Happens Next?</h4>
<ul>
    <li>🔹 <b>Your account is now under review for admin approval.</b></li>
    <li>🔹 Once approved, you'll receive another email confirmation.</li>
    <li>🔹 After approval, you can log in and start shopping.</li>
</ul>

<h4>What Can You Do in the Meantime?</h4>
<ul>
    <li>✅ <b>Browse our wide range of premium products.</b></li>
    <li>✅ <b>Access the Help Page</b> to understand how our platform works.</li>
    <li>✅ <b>Watch tutorials</b> and get familiar with the webshop features.</li>
</ul>

<h4>🎁 Exclusive Welcome Offer!</h4>
<p>As a special welcome, we’re giving you an exclusive discount on your first order!</p>
<p>💥 Use promo code <b>{{$promo->code}}</b> to get <b>{{$promo->discount_percentage}}%</b> off once your account is approved.</p>

<p>We'll notify you as soon as your account is fully activated. If you have any questions, feel free to reach out to our support team.</p>

<p><b>Thank you for joining Virgin Farms – we can’t wait to serve you!</b></p>
