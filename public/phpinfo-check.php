<?php
// Temporary PHP configuration check file
// DELETE THIS FILE AFTER CHECKING

echo "<h2>PHP Upload Configuration</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";

echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . " seconds</td></tr>";
echo "<tr><td>max_input_time</td><td>" . ini_get('max_input_time') . " seconds</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</td></tr>";

echo "</table>";

echo "<h3>Required for 10MB uploads:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize should be at least 10M</li>";
echo "<li>post_max_size should be at least 10M (preferably 11M or more)</li>";
echo "<li>max_execution_time should be at least 60 seconds</li>";
echo "</ul>";
?>
