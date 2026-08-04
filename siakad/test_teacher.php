<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $user = \App\Models\User::create([
        'name' => 'Test Teacher ' . time(),
        'email' => 'test' . time() . '@test.com',
        'password' => bcrypt('password'),
        'role' => 'guru',
    ]);
    echo "User created: ID={$user->id}, Name={$user->name}\n";

    $teacher = \App\Models\Teacher::create([
        'user_id' => $user->id,
        'nip' => 'NIP' . time(),
    ]);
    echo "Teacher created: ID={$teacher->id}, NIP={$teacher->nip}\n";

    echo "SUCCESS: Teacher creation works!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Clean up test data
\App\Models\Teacher::where('nip', 'like', 'NIP%')->delete();
\App\Models\User::where('email', 'like', 'test%@test.com')->delete();
echo "Cleanup done.\n";
