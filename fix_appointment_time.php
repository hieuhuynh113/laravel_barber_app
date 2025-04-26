<?php
$file = 'resources/views/admin/barbers/show.blade.php';
$content = file_get_contents($file);
$new_content = str_replace(
    '<td>{{ $appointment->appointment_time }}</td>',
    '<td>{{ $appointment->time_slot ?? ($appointment->start_time ? \Carbon\Carbon::parse($appointment->start_time)->format(\'H:i\') : \'N/A\') }}</td>',
    $content
);
file_put_contents($file, $new_content);
echo "File updated successfully.\n";
?>
