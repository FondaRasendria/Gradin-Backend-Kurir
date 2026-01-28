<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// KURIR INDEX TESTS
test('[index] can be accessed', function () {
    $response = $this->get('/kurir');

    $response->assertOk();
});

test('[index] returns empty array when no kurir exists', function () {
    $response = $this->getJson('/kurir');

    $response->assertOk()
        ->assertJson(['data' => []]);
});

test('[index] returns 500 when database error occurs', function () {
    DB::shouldReceive('table')
        ->once()
        ->andThrow(new \Exception('Database error'));

    $response = $this->getJson('/kurir');

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

test('[index] kurir index returns data', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 1,
        ]
    ]);

    $response = $this->getJson('/kurir');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['nama_kurir' => 'Kurir Satu']);
});

test('[index] kurir index can search by keyword', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 1,
        ],
        [
            'nama_kurir' => 'Kurir Dua',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 2',
            'status' => 'inactive',
            'level' => 2,
        ]
    ]);

    $response = $this->getJson('/kurir?search=kurir+Satu');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['nama_kurir' => 'Kurir Satu'])
        ->assertJsonMissing(['nama_kurir' => 'Kurir Dua']);
});

test('[index] kurir index can filter by level', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 1,
        ],
        [
            'nama_kurir' => 'Kurir Dua',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 2',
            'status' => 'inactive',
            'level' => 2,
        ],
        [
            'nama_kurir' => 'Kurir Tiga',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 3',
            'status' => 'inactive',
            'level' => 3,
        ]
    ]);

    $response = $this->getJson('/kurir?level=1,2');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['nama_kurir' => 'Kurir Satu'])
        ->assertJsonFragment(['nama_kurir' => 'Kurir Dua'])
        ->assertJsonMissing(['nama_kurir' => 'Kurir Tiga']);
});

test('[index] kurir index pagination works', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 1,
        ],
        [
            'nama_kurir' => 'Kurir Dua',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 2',
            'status' => 'inactive',
            'level' => 2,
        ],
        [
            'nama_kurir' => 'Kurir Tiga',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 3',
            'status' => 'inactive',
            'level' => 3,
        ]
    ]);

    $response = $this->getJson('/kurir?size=2&page=2');

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

test('[index] kurir index can sorting data', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 2,
        ],
        [
            'nama_kurir' => 'Kurir Dua',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 2',
            'status' => 'inactive',
            'level' => 1,
        ],
        [
            'nama_kurir' => 'Kurir Tiga',
            'no_telepon' => '089876543210',
            'alamat' => 'Jl. Contoh Alamat 3',
            'status' => 'inactive',
            'level' => 3,
        ]
    ]);

    $response = $this->getJson('/kurir?order=level&order_mode=asc');

    $names = collect($response->json('data'))
        ->pluck('nama_kurir')
        ->values()
        ->toArray();

    expect($names)->toEqual(['Kurir Dua', 'Kurir Satu', 'Kurir Tiga']);
});

// KURIR SHOW TESTS
test('[show] can be accessed', function () {
    $response = $this->get('/kurir/all');

    $response->assertOk();
});

test('[show] returns empty array when no kurir exists', function () {
    $response = $this->getJson('/kurir/all');

    $response->assertOk()
        ->assertJson([]);
});

test('[show] returns 500 when database error occurs', function () {
    DB::shouldReceive('table')
        ->once()
        ->andThrow(new \Exception('Database error'));

    $response = $this->getJson('/kurir/all');

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

test('[show] kurir show returns data', function () {
    DB::table('kurir')->insert([
        [
            'nama_kurir' => 'Kurir Satu',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Contoh Alamat 1',
            'status' => 'active',
            'level' => 1,
        ]
    ]);

    $response = $this->getJson('/kurir/all');

    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['nama_kurir' => 'Kurir Satu']);
});

// KURIR STORE TESTS
test('[store] can create kurir with valid data', function () {
    $data = [
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ];

    $response = $this->postJson('/kurir', $data);

    $response->assertOk()
        ->assertJson(['message' => 'Kurir created successfully']);

    $this->assertDatabaseHas('kurir', [
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);
});

test('[store] create kurir with invalid data return 500', function () {
    $data = [
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'waiting', // invalid status
        'created_at' => now(),
    ];

    $response = $this->postJson('/kurir', $data);

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

test('[store] returns 500 when database error occurs', function () {
    DB::shouldReceive('table')
        ->once()
        ->andThrow(new \Exception('Database error'));

    $response = $this->postJson('/kurir', [
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

// KURIR UPDATE TESTS
test('[update] can update existing kurir with valid data', function () {
    $kurirId = DB::table('kurir')->insertGetId([
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    $data = [
        'nama_kurir' => 'Kurir Satu Updated',
        'updated_at' => now(),
    ];

    $response = $this->postJson('/kurir/' . $kurirId, $data);

    $response->assertOk()
        ->assertJson(['message' => 'Kurir updated successfully']);

    $this->assertDatabaseHas('kurir', [
        'id' => $kurirId,
        'nama_kurir' => 'Kurir Satu Updated',
    ]);
});

test('[update] updating non-existing kurir returns 404', function () {
    $kurirId = 999; // assuming this ID does not exist
    $data = [
        'nama_kurir' => 'Kurir Non Existing',
        'updated_at' => now(),
    ];

    $response = $this->postJson('/kurir/' . $kurirId, $data);

    $response->assertStatus(404)
        ->assertJson(['error' => 'Kurir not found']);
});

test('[update] updating kurir with invalid data returns 500', function () {
    $kurirId = DB::table('kurir')->insertGetId([
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    $data = [
        'status' => 'waiting', // invalid status
        'updated_at' => now(),
    ];

    $response = $this->postJson('/kurir/' . $kurirId, $data);

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

test('[update] returns 500 when database error occurs', function () {
    $kurirId = DB::table('kurir')->insertGetId([
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    DB::shouldReceive('table')
        ->once()
        ->andThrow(new \Exception('Database error'));

    $response = $this->postJson('/kurir/' . $kurirId, [
        'nama_kurir' => 'Kurir Satu Updated',
        'updated_at' => now(),
    ]);

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});

// KURIR DESTROY TESTS
test('[destroy] can delete existing kurir', function () {
    $kurirId = DB::table('kurir')->insertGetId([
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    $response = $this->deleteJson('/kurir/' . $kurirId);

    $response->assertOk()
        ->assertJson(['message' => 'Kurir deleted successfully']);

    $this->assertDatabaseMissing('kurir', ['id' => $kurirId]);
});

test('[destroy] destroy non-existing kurir returns 404', function () {
    $kurirId = 999; // assuming this ID does not exist

    $response = $this->deleteJson('/kurir/' . $kurirId);

    $response->assertStatus(404)
        ->assertJson(['error' => 'Kurir not found']);
});

test('[destroy] returns 500 when database error occurs', function () {
    $kurirId = DB::table('kurir')->insertGetId([
        'nama_kurir' => 'Kurir Satu',
        'no_telepon' => '081234567890',
        'alamat' => 'Jl. Contoh Alamat 1',
        'level' => 1,
        'status' => 'active',
        'created_at' => now(),
    ]);

    DB::shouldReceive('table')
        ->once()
        ->andThrow(new \Exception('Database error'));

    $response = $this->deleteJson('/kurir/' . $kurirId);

    $response->assertStatus(500)
        ->assertJsonStructure(['error']);
});