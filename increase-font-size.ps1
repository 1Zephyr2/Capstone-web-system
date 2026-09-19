# Run this from inside your project folder:
#   C:\laragon\www\Capstone-web-system> powershell -ExecutionPolicy Bypass -File .\increase-font-size.ps1
#
# What it does: finds every .blade.php page that uses the Tailwind CDN script,
# and inserts one small CSS rule right after it that scales the whole page
# (text, spacing, everything using Tailwind's rem-based sizing) up by 12% —
# noticeably bigger without looking oversized. Safe to run more than once;
# it skips any file that already has the rule.

$root   = "resources\views"
$marker = "html { font-size: 112%; }"
$anchor = '<script src="https://cdn.tailwindcss.com"></script>'
$insertAfter = "$anchor`n    <style>$marker</style>"

$files = Get-ChildItem -Path $root -Recurse -Filter *.blade.php
$updated = 0

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    if ($content.Contains($anchor) -and -not $content.Contains($marker)) {
        $newContent = $content.Replace($anchor, $insertAfter)
        Set-Content -Path $file.FullName -Value $newContent -NoNewline
        Write-Host "Updated: $($file.FullName)"
        $updated++
    }
}

Write-Host ""
Write-Host "Done. Updated $updated file(s)."
Write-Host "Now run: php artisan view:clear, restart your dev server, and hard-refresh your browser."
