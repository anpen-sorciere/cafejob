@echo off
setlocal enabledelayedexpansion

echo ========================================
echo カフェJob デプロイスクリプト
echo ========================================
echo.

REM 設定ファイルを読み込み
set CONFIG_FILE=C:\xampp\htdocs\cafejob\deploy_config.bat
if exist "%CONFIG_FILE%" (
    call "%CONFIG_FILE%"
    echo 設定ファイルを読み込みました。
) else (
    echo エラー: deploy_config.bat が見つかりません。
    echo パス: %CONFIG_FILE%
    pause
    exit /b 1
)

echo.
echo 現在の設定:
echo ソースディレクトリ: %SOURCE_DIR%
echo デプロイディレクトリ: %DEPLOY_DIR%
echo Gitリポジトリ: %GIT_REPO%
echo FTPホスト: %FTP_HOST%
echo.

REM メニュー表示
:menu
echo ========================================
echo デプロイ操作を選択してください:
echo ========================================
echo 1. ファイルコピーのみ
echo 2. ファイルコピー + Git操作
echo 3. ファイルコピー + Git操作 + FTPアップロード
echo 4. Git操作のみ
echo 5. FTPアップロードのみ
echo 6. バックアップ作成
echo 7. 設定確認・編集
echo 8. 終了
echo.
set /p choice="選択 (1-8): "

if "%choice%"=="1" goto copy_files
if "%choice%"=="2" goto copy_and_git
if "%choice%"=="3" goto copy_git_ftp
if "%choice%"=="4" goto git_only
if "%choice%"=="5" goto ftp_only
if "%choice%"=="6" goto backup
if "%choice%"=="7" goto show_config
if "%choice%"=="8" goto end
echo 無効な選択です。1-8の数字を入力してください。
goto menu

:copy_files
echo.
echo ========================================
echo ファイルコピーを実行中...
echo ========================================
call :copy_files_function
echo.
echo ファイルコピーが完了しました。
pause
goto menu

:copy_and_git
echo.
echo ========================================
echo ファイルコピー + Git操作を実行中...
echo ========================================
call :copy_files_function
call :git_operations
echo.
echo ファイルコピー + Git操作が完了しました。
pause
goto menu

:copy_git_ftp
echo.
echo ========================================
echo ファイルコピー + Git操作 + FTPアップロードを実行中...
echo ========================================
call :copy_files_function
call :git_operations
call :ftp_upload
echo.
echo すべての操作が完了しました。
pause
goto menu

:git_only
echo.
echo ========================================
echo Git操作を実行中...
echo ========================================
call :git_operations
echo.
echo Git操作が完了しました。
pause
goto menu

:ftp_only
echo.
echo ========================================
echo FTPアップロードを実行中...
echo ========================================
call :ftp_upload
echo.
echo FTPアップロードが完了しました。
pause
goto menu

:backup
echo.
echo ========================================
echo バックアップを作成中...
echo ========================================
call :create_backup
echo.
echo バックアップが完了しました。
pause
goto menu

:show_config
echo.
echo ========================================
echo 現在の設定
echo ========================================
echo ソースディレクトリ: %SOURCE_DIR%
echo デプロイディレクトリ: %DEPLOY_DIR%
echo Gitリポジトリ: %GIT_REPO%
echo FTPホスト: %FTP_HOST%
echo FTPユーザー: %FTP_USER%
echo FTPディレクトリ: %FTP_DIR%
echo GitリモートURL: %GIT_REMOTE_URL%
echo Gitブランチ: %GIT_BRANCH%
echo バックアップディレクトリ: %BACKUP_DIR%
echo ログファイル: %LOG_FILE%
echo.
echo 設定を変更する場合は、deploy_config.batファイルを編集してください。
pause
goto menu

:copy_files_function
REM ログ開始
echo [%date% %time%] ファイルコピー開始 >> "%LOG_FILE%"

:: ディレクトリの存在確認
if not exist "%SOURCE_DIR%" (
    echo エラー: ソースディレクトリが見つかりません: %SOURCE_DIR%
    echo [%date% %time%] エラー: ソースディレクトリが見つかりません >> "%LOG_FILE%"
    goto :eof
)

:: デプロイディレクトリが存在しない場合は作成
if not exist "%DEPLOY_DIR%" (
    echo デプロイディレクトリを作成中: %DEPLOY_DIR%
    mkdir "%DEPLOY_DIR%"
    echo [%date% %time%] デプロイディレクトリを作成: %DEPLOY_DIR% >> "%LOG_FILE%"
)

echo ソースディレクトリ: %SOURCE_DIR%
echo デプロイディレクトリ: %DEPLOY_DIR%
echo.

:: カウンター初期化
set /a copied_files=0
set /a skipped_files=0
set /a total_files=0

:: ルートディレクトリのファイルをコピー
echo [ルートディレクトリ]
for %%e in (%EXTENSIONS%) do (
    for %%f in ("%SOURCE_DIR%\%%e") do (
        if exist "%%f" (
            set /a total_files+=1
            set source_file=%%f
            set filename=%%~nxf
            set deploy_file=%DEPLOY_DIR%\!filename!
            
            :: 除外ファイルかチェック
            set skip_file=false
            for %%x in (%EXCLUDE_FILES%) do (
                if "!filename!"=="%%x" set skip_file=true
            )
            
            if not !skip_file!==true (
                :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                if not exist "!deploy_file!" (
                    echo   新規: !filename!
                    copy "!source_file!" "!deploy_file!" >nul
                    set /a copied_files+=1
                    echo [%date% %time%] 新規コピー: !filename! >> "%LOG_FILE%"
                ) else (
                    :: ファイルの更新日時を比較
                    for %%s in ("!source_file!") do set source_time=%%~ts
                    for %%d in ("!deploy_file!") do set deploy_time=%%~td
                    
                    :: 更新日時が新しい場合はコピー
                    if "!source_time!" gtr "!deploy_time!" (
                        echo   更新: !filename!
                        copy "!source_file!" "!deploy_file!" >nul
                        set /a copied_files+=1
                        echo [%date% %time%] 更新コピー: !filename! >> "%LOG_FILE%"
                    ) else (
                        echo   スキップ: !filename!
                        set /a skipped_files+=1
                    )
                )
            )
        )
    )
)

:: サブディレクトリを再帰的に処理
echo.
echo [サブディレクトリ]
for /d %%d in ("%SOURCE_DIR%\*") do (
    set dirname=%%~nd
    set skip_dir=false
    
    :: 除外ディレクトリかチェック
    for %%e in (%EXCLUDE_DIRS%) do (
        if "!dirname!"=="%%e" set skip_dir=true
    )
    
    if not !skip_dir!==true (
        echo ディレクトリ: !dirname!
        
        :: デプロイ先ディレクトリを作成
        if not exist "%DEPLOY_DIR%\!dirname!" (
            mkdir "%DEPLOY_DIR%\!dirname!"
        )
        
        :: ディレクトリ内のファイルをコピー
        for %%e in (%EXTENSIONS%) do (
            for %%f in ("%%d\%%e") do (
                if exist "%%f" (
                    set /a total_files+=1
                    set source_file=%%f
                    set filename=%%~nxf
                    set deploy_file=%DEPLOY_DIR%\!dirname!\!filename!
                    
                    :: 除外ファイルかチェック
                    set skip_file=false
                    for %%x in (%EXCLUDE_FILES%) do (
                        if "!filename!"=="%%x" set skip_file=true
                    )
                    
                    if not !skip_file!==true (
                        :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                        if not exist "!deploy_file!" (
                            echo   新規: !dirname!\!filename!
                            copy "!source_file!" "!deploy_file!" >nul
                            set /a copied_files+=1
                            echo [%date% %time%] 新規コピー: !dirname!\!filename! >> "%LOG_FILE%"
                        ) else (
                            :: ファイルの更新日時を比較
                            for %%s in ("!source_file!") do set source_time=%%~ts
                            for %%d in ("!deploy_file!") do set deploy_time=%%~td
                            
                            :: 更新日時が新しい場合はコピー
                            if "!source_time!" gtr "!deploy_time!" (
                                echo   更新: !dirname!\!filename!
                                copy "!source_file!" "!deploy_file!" >nul
                                set /a copied_files+=1
                                echo [%date% %time%] 更新コピー: !dirname!\!filename! >> "%LOG_FILE%"
                            ) else (
                                echo   スキップ: !dirname!\!filename!
                                set /a skipped_files+=1
                            )
                        )
                    )
                )
            )
        )
        
        :: サブディレクトリ内のサブディレクトリも処理
        for /d %%sd in ("%%d\*") do (
            set subdirname=%%~nsd
            set subdirpath=!dirname!\!subdirname!
            
            :: デプロイ先サブディレクトリを作成
            if not exist "%DEPLOY_DIR%\!subdirpath!" (
                mkdir "%DEPLOY_DIR%\!subdirpath!"
            )
            
            echo サブディレクトリ: !subdirpath!
            
            :: サブディレクトリ内のファイルをコピー
            for %%e in (%EXTENSIONS%) do (
                for %%f in ("%%sd\%%e") do (
                    if exist "%%f" (
                        set /a total_files+=1
                        set source_file=%%f
                        set filename=%%~nxf
                        set deploy_file=%DEPLOY_DIR%\!subdirpath!\!filename!
                        
                        :: 除外ファイルかチェック
                        set skip_file=false
                        for %%x in (%EXCLUDE_FILES%) do (
                            if "!filename!"=="%%x" set skip_file=true
                        )
                        
                        if not !skip_file!==true (
                            :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                            if not exist "!deploy_file!" (
                                echo   新規: !subdirpath!\!filename!
                                copy "!source_file!" "!deploy_file!" >nul
                                set /a copied_files+=1
                                echo [%date% %time%] 新規コピー: !subdirpath!\!filename! >> "%LOG_FILE%"
                            ) else (
                                :: ファイルの更新日時を比較
                                for %%s in ("!source_file!") do set source_time=%%~ts
                                for %%d in ("!deploy_file!") do set deploy_time=%%~td
                                
                                :: 更新日時が新しい場合はコピー
                                if "!source_time!" gtr "!deploy_time!" (
                                    echo   更新: !subdirpath!\!filename!
                                    copy "!source_file!" "!deploy_file!" >nul
                                    set /a copied_files+=1
                                    echo [%date% %time%] 更新コピー: !subdirpath!\!filename! >> "%LOG_FILE%"
                                ) else (
                                    echo   スキップ: !subdirpath!\!filename!
                                    set /a skipped_files+=1
                                )
                            )
                        )
                    )
                )
            )
        )
    )
)

:: 結果表示
echo.
echo ========================================
echo コピー完了
echo ========================================
echo 総ファイル数: %total_files%
echo コピーしたファイル: %copied_files%
echo スキップしたファイル: %skipped_files%
echo.

REM ログ終了
echo [%date% %time%] ファイルコピー完了 - 総数:%total_files% コピー:%copied_files% スキップ:%skipped_files% >> "%LOG_FILE%"
goto :eof

:git_operations
echo.
echo ========================================
echo Git操作を実行中...
echo ========================================

REM ログ開始
echo [%date% %time%] Git操作開始 >> "%LOG_FILE%"

:: Gitリポジトリの確認
cd /d "%GIT_REPO%"
if not exist ".git" (
    echo Gitリポジトリを初期化中...
    git init
    git remote add origin %GIT_REMOTE_URL%
    echo [%date% %time%] Gitリポジトリを初期化 >> "%LOG_FILE%"
)

:: 変更をステージング
echo 変更をステージング中...
git add .

:: コミットメッセージの入力
set /p commit_message="コミットメッセージを入力してください: "
if "%commit_message%"=="" set commit_message=Update files

:: コミット
echo コミット中...
git commit -m "%commit_message%"

:: プッシュ
echo プッシュ中...
git push origin %GIT_BRANCH%

echo Git操作が完了しました。
echo [%date% %time%] Git操作完了 >> "%LOG_FILE%"
echo.
goto :eof

:ftp_upload
echo.
echo ========================================
echo FTPアップロードを実行中...
echo ========================================

REM ログ開始
echo [%date% %time%] FTPアップロード開始 >> "%LOG_FILE%"

:: FTPスクリプトファイルを作成
echo open %FTP_HOST% > ftp_script.txt
echo user %FTP_USER% %FTP_PASS% >> ftp_script.txt
echo binary >> ftp_script.txt
echo cd %FTP_DIR% >> ftp_script.txt
echo lcd "%DEPLOY_DIR%" >> ftp_script.txt

:: ファイルをアップロード
echo mput *.php >> ftp_script.txt
echo mput *.html >> ftp_script.txt
echo mput *.css >> ftp_script.txt
echo mput *.js >> ftp_script.txt
echo mput *.sql >> ftp_script.txt
echo mput *.md >> ftp_script.txt
echo mput *.txt >> ftp_script.txt
echo mput *.json >> ftp_script.txt

:: ディレクトリをアップロード
echo cd admin >> ftp_script.txt
echo lcd admin >> ftp_script.txt
echo mput *.php >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo cd assets >> ftp_script.txt
echo lcd assets >> ftp_script.txt
echo mput *.css >> ftp_script.txt
echo mput *.js >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo cd config >> ftp_script.txt
echo lcd config >> ftp_script.txt
echo mput *.php >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo cd includes >> ftp_script.txt
echo lcd includes >> ftp_script.txt
echo mput *.php >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo cd pages >> ftp_script.txt
echo lcd pages >> ftp_script.txt
echo mput *.php >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo cd api >> ftp_script.txt
echo lcd api >> ftp_script.txt
echo mput *.php >> ftp_script.txt
echo cd .. >> ftp_script.txt
echo lcd .. >> ftp_script.txt

echo quit >> ftp_script.txt

:: FTPコマンドを実行
echo FTPアップロードを開始...
ftp -s:ftp_script.txt

:: 一時ファイルを削除
del ftp_script.txt

echo FTPアップロードが完了しました。
echo [%date% %time%] FTPアップロード完了 >> "%LOG_FILE%"
echo.
goto :eof

:create_backup
echo.
echo ========================================
echo バックアップを作成中...
echo ========================================

REM ログ開始
echo [%date% %time%] バックアップ作成開始 >> "%LOG_FILE%"

:: バックアップディレクトリの作成
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

:: 日時でバックアップディレクトリを作成
for /f "tokens=1-3 delims=/ " %%a in ('date /t') do set mydate=%%c%%a%%b
for /f "tokens=1-2 delims=: " %%a in ('time /t') do set mytime=%%a%%b
set backup_name=backup_%mydate%_%mytime%

:: バックアップディレクトリを作成
mkdir "%BACKUP_DIR%\%backup_name%"

:: デプロイディレクトリをバックアップ
echo バックアップディレクトリ: %BACKUP_DIR%\%backup_name%
xcopy "%DEPLOY_DIR%" "%BACKUP_DIR%\%backup_name%" /E /I /H /Y

echo バックアップが完了しました: %backup_name%
echo [%date% %time%] バックアップ完了: %backup_name% >> "%LOG_FILE%"

:: 古いバックアップを削除
echo 古いバックアップを削除中...
forfiles /p "%BACKUP_DIR%" /m backup_* /d -%BACKUP_DAYS% /c "cmd /c rmdir /s /q @path"

echo.
goto :eof

:end
echo.
echo デプロイスクリプトを終了します。
echo [%date% %time%] デプロイスクリプト終了 >> "%LOG_FILE%"
echo.
pause