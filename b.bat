echo ========================================
echo Verificando arquivos grandes (maiores que 5MB) no controle do Git...
echo ========================================

set FLAG_LARGE_FILE=0

:: Verifica apenas arquivos monitorados ou não rastreados pelo Git
for /F "delims=" %%f in ('git ls-files -m -o --exclude-standard') do (
    if exist "%%f" (
        for %%A in ("%%f") do (
            set "filesize=%%~zA"
            if "!filesize!"=="" set "filesize=0"

            if !filesize! GTR 5000000 (
                echo Arquivo grande detectado: %%f (!filesize! bytes)
                set FLAG_LARGE_FILE=1
            )
        )
    )
)

if "!FLAG_LARGE_FILE!"=="1" (
    echo ========================================
    echo Arquivos grandes detectados! Push cancelado.
    echo Verifique e corrija antes de continuar.
    echo ========================================
    pause
    exit /b
)

echo Nenhum arquivo grande encontrado. Prosseguindo com o push...
echo ========================================
git add .
git commit -m "Atualizacao"
git push origin main
echo ========================================
echo Push finalizado com sucesso!
pause