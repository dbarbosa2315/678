SET GIT="C:\Program Files\Git\bin\git.exe"

SET SSH="C:\Program Files\Git\usr\bin\ssh.exe"

SET APP_PATH=%~dp0

cd %APP_PATH%

SET GIT_SSH_COMMAND=%SSH% -i "%APP_PATH%pdv_local.pem" -o StrictHostKeyChecking=no

%GIT% pull origin master

C:\xampp\php\php.exe -f C:\xampp\htdocs\pdv_local\index.php tasks/updateDatabase