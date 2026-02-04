<?php

$createFile = file_get_contents('resources/views/materials/create.blade.php');

// Replace title and header
$editFile = str_replace('Create New Material', 'Edit Material', $createFile);

// Add extends and sections at the top
$editFile = '@extends(\'layouts.dashboard\')

@section(\'title\', \'Edit Material\')
@section(\'user-role\', \'Teacher • Edit Material\')

@section(\'navigation\')
    @include(\'partials.teacher-nav\')
@endsection

' . $editFile;

// Replace form opening tag - need to find and replace the action
$editFile = preg_replace(
    '/action="{{ route\(\'materials\.store\'\) }}" method="POST"/',
    'action="{{ route(\'materials.update\', $material->material_id) }}" method="POST"',
    $editFile
);

// Add PUT method after CSRF
$editFile = str_replace(
    '@csrf',
    '@csrf
            @method(\'PUT\')',
    $editFile
);

// Replace submit button text
$editFile = str_replace(
    '<i class="fas fa-save"></i> Create Material',
    '<i class="fas fa-save"></i> Update Material',
    $editFile
);

// Update back button  
$editFile = preg_replace(
    '/href="{{ route\(\'materials\.index\'\) }}"/U',
    'href="{{ route(\'materials.show\', $material->material_id) }}"',
    $editFile,
    1 // Only first occurrence
);

$editFile = str_replace(
    '<i class="fas fa-times"></i> Cancel',
    '<i class="fas fa-times"></i> Back to Material',
    $editFile
);

file_put_contents('resources/views/materials/edit.blade.php', $editFile);
echo "Edit page template created!\n";
echo "File size: " . strlen($editFile) . " bytes\n";
