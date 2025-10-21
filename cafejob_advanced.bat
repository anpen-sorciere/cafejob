@echo off
setlocal enabledelayedexpansion

echo ========================================
echo カフェJob 完全デプロイスクリプト
echo ========================================
echo.

:: 設定
set SOURCE_DIR=C:\xampp\htdocs\cafejob
set DEPLOY_DIR=C:\develop\cafejob
set GIT_REPO=C:\develop\cafejob
set FTP_HOST=purplelion51.sakura.ne.jp
set FTP_USER=purplelion51
set FTP_PASS=-6r_am73

:: メニュー表示
:menu
echo 選択してください:
echo 1. ファイルコピーのみ
echo 2. ファイルコピー + Git操作
echo 3. ファイルコピー + Git操作 + FTPアップロード
echo 4. Git操作のみ
echo 5. FTPアップロードのみ
echo 6. 終了
echo.
set /p choice="選択 (1-6): "

if "%choice%"=="1" goto copy_files
if "%choice%"=="2" goto copy_and_git
if "%choice%"=="3" goto copy_git_ftp
if "%choice%"=="4" goto git_only
if "%choice%"=="5" goto ftp_only
if "%choice%"=="6" goto end
goto menu

:copy_files
echo.
echo ========================================
echo ファイルコピーを実行中...
echo ========================================
call :copy_files_function
goto menu

:copy_and_git
echo.
echo ========================================
echo ファイルコピー + Git操作を実行中...
echo ========================================
call :copy_files_function
call :git_operations
goto menu

:copy_git_ftp
echo.
echo ========================================
echo ファイルコピー + Git操作 + FTPアップロードを実行中...
echo ========================================
call :copy_files_function
call :git_operations
call :ftp_upload
goto menu

:git_only
echo.
echo ========================================
echo Git操作を実行中...
echo ========================================
call :git_operations
goto menu

:ftp_only
echo.
echo ========================================
echo FTPアップロードを実行中...
echo ========================================
call :ftp_upload
goto menu

:copy_files_function
:: ディレクトリの存在確認
if not exist "%SOURCE_DIR%" (
    echo エラー: ソースディレクトリが見つかりません: %SOURCE_DIR%
    goto :eof
)

:: デプロイディレクトリが存在しない場合は作成
if not exist "%DEPLOY_DIR%" (
    echo デプロイディレクトリを作成中: %DEPLOY_DIR%
    mkdir "%DEPLOY_DIR%"
)

echo ソースディレクトリ: %SOURCE_DIR%
echo デプロイディレクトリ: %DEPLOY_DIR%
echo.

:: コピー対象ファイルの拡張子
set EXTENSIONS=*.php *.html *.css *.js *.sql *.md *.txt *.bat *.json *.xml *.yml *.yaml

:: コピー対象ディレクトリ（除外するディレクトリも指定）
set EXCLUDE_DIRS=logs uploads .git .vscode node_modules

echo ファイルコピーを開始します...
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
            
            :: ファイルが存在しない、または更新日時が新しい場合のみコピー
            if not exist "!deploy_file!" (
                echo   新規: !filename!
                copy "!source_file!" "!deploy_file!" >nul
                set /a copied_files+=1
            ) else (
                :: ファイルの更新日時を比較
                for %%s in ("!source_file!") do set source_time=%%~ts
                for %%d in ("!deploy_file!") do set deploy_time=%%~td
                
                :: 更新日時が新しい場合はコピー
                if "!source_time!" gtr "!deploy_time!" (
                    echo   更新: !filename!
                    copy "!source_file!" "!deploy_file!" >nul
                    set /a copied_files+=1
                ) else (
                    echo   スキップ: !filename!
                    set /a skipped_files+=1
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
                    
                    :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                    if not exist "!deploy_file!" (
                        echo   新規: !dirname!\!filename!
                        copy "!source_file!" "!deploy_file!" >nul
                        set /a copied_files+=1
                    ) else (
                        :: ファイルの更新日時を比較
                        for %%s in ("!source_file!") do set source_time=%%~ts
                        for %%d in ("!deploy_file!") do set deploy_time=%%~td
                        
                        :: 更新日時が新しい場合はコピー
                        if "!source_time!" gtr "!deploy_time!" (
                            echo   更新: !dirname!\!filename!
                            copy "!source_file!" "!deploy_file!" >nul
                            set /a copied_files+=1
                        ) else (
                            echo   スキップ: !dirname!\!filename!
                            set /a skipped_files+=1
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
                        
                        :: ファイルが存在しない、または更新日時が新しい場合のみコピー
                        if not exist "!deploy_file!" (
                            echo   新規: !subdirpath!\!filename!
                            copy "!source_file!" "!deploy_file!" >nul
                            set /a copied_files+=1
                        ) else (
                            :: ファイルの更新日時を比較
                            for %%s in ("!source_file!") do set source_time=%%~ts
                            for %%d in ("!deploy_file!") do set deploy_time=%%~td
                            
                            :: 更新日時が新しい場合はコピー
                            if "!source_time!" gtr "!deploy_time!" (
                                echo   更新: !subdirpath!\!filename!
                                copy "!source_file!" "!deploy_file!" >nul
                                set /a copied_files+=1
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

:: 結果表示
echo.
echo ========================================
echo コピー完了
echo ========================================
echo 総ファイル数: %total_files%
echo コピーしたファイル: %copied_files%
echo スキップしたファイル: %skipped_files%
echo.
goto :eof

:git_operations
echo.
echo ========================================
echo Git操作を実行中...
echo ========================================

:: Gitリポジトリの確認
cd /d "%GIT_REPO%"
if not exist ".git" (
    echo Gitリポジトリを初期化中...
    git init
    git remote add origin https://github.com/yourusername/cafejob.git
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
git push origin main

echo Git操作が完了しました。
echo.
goto :eof

:ftp_upload
echo.
echo ========================================
echo FTPアップロードを実行中...
echo ========================================

:: FTPスクリプトファイルを作成
echo open %FTP_HOST% > ftp_script.txt
echo user %FTP_USER% %FTP_PASS% >> ftp_script.txt
echo binary >> ftp_script.txt
echo cd cafejob >> ftp_script.txt
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
echo.
goto :eof

:end
echo.
echo デプロイスクリプトを終了します。
echo.
pause
