@echo off
echo Deploying account page fixes...

echo 1. Copying functions.php...
copy "functions.php" "I:\Waheed\AI\Warafy\warafy-theme\functions.php"

echo 2. Copying page-my-account.php...
copy "page-my-account.php" "I:\Waheed\AI\Warafy\warafy-theme\page-my-account.php"

echo 3. Files deployed successfully!
echo.
echo Please upload these files to your server:
echo - wp-content/themes/warafy-theme/functions.php
echo - wp-content/themes/warafy-theme/page-my-account.php
echo.
echo After uploading, clear any server cache and test the links:
echo - https://warafy.com/my-account/#account-details
echo - https://warafy.com/my-account/#orders
echo - https://warafy.com/my-account/#addresses
pause
