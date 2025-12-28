<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Créer un utilisateur de test
$user = \App\Models\Utilisateur::create([
    'id' => \Illuminate\Support\Str::uuid(),
    'user_name' => 'testuser',
    'email' => 'test@example.com',
    'password' => bcrypt('password'),
]);

// Générer un token
$token = $user->createToken('test-token')->plainTextToken;

echo "Token créé avec succès!\n";
echo "User ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";
echo "Token: " . $token . "\n";
