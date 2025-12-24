<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$email = 'admin@dwello.com';
$password = 'password';

$user = User::where('email', $email)->first();

if ($user) {
    echo "Admin User Exists.\n";
    echo "Email: $email\n";
    // We can't see the password, but we assume it's 'password' if we created it. 
    // If not, we might want to reset it or just notify the user.
    // Let's reset it to be sure for testing.
    $user->password = Hash::make($password);
    $user->save();
    echo "Password reset to: $password\n";
} else {
    echo "Creating Admin User...\n";
    $user = User::create([
        'name' => 'Admin User',
        'email' => $email,
        'password' => Hash::make($password),
        'role' => 'landlord', // Role doesn't matter for isAdmin() check, but need valid enum
        'email_verified_at' => now(),
    ]);
    echo "Admin User Created.\n";
    echo "Email: $email\n";
    echo "Password: $password\n";
}
